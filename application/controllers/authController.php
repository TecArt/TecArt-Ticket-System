<?php


include __SITE_PATH . '/application/models/crmContacts.php';
include __SITE_PATH . '/application/models/crmContract.php';
include __SITE_PATH . '/application/models/crmTickets.php';
include __SITE_PATH . '/application/models/crmLogon.php';
include __SITE_PATH . '/application/controllers/' . 'secureController.php';

class authController extends baseController
{
    private $cid;

    /**
     * @var crmLogon
     */
    private $crmLogon;

    private $crm_contact;

    public function __construct($registry)
    {
        parent::__construct($registry);
    }

    public function index()
    {
        $template = $this->registry->config['login_template'];

        if (!file_exists(__SITE_PATH . '/application/views/auth/'.$template.'.php')) {
            $template = 'login_standard';
        }

        $template_view = $template;
        $this->view->render($template_view);
    }

    /**
     * do the authentification and write information in session
     *
     */
    public function login()
    {
        $username = strip_tags($_POST['number']);
        $pass     = strip_tags($_POST['password']);

        if (empty($username) || empty($pass)) {
            $this->view->error_msg = $this->translate['err_no_username_password'];
            $this->index();
            return;
        }

        // Check username and password
        $this->crm_contact = new crmContacts($this->registry->config);
        $this->cid = $this->crm_contact->do_contact_authenticate($username, $pass);

        if ($this->cid === null) {
            unset($_SESSION['crm_session_id']);
            $this->view->error_msg = $this->translate['err_incorrect_username_pass'];
            $this->index();
            return;
        }

        // Init data for session.
        $this->init_data();

        if (!isset($_SESSION["login"])) {
            $_SESSION["login"]          = true;
        }
        if (!isset($_SESSION["cid"])) {
            $_SESSION["cid"]            = $this->cid;
        }
        if (!isset($_SESSION["login_number"])) {
            $_SESSION["login_number"]   = $username;
        }

        header("Location: ".$this->baseUrl."?co=ticket/show&type=current");
        exit;
    }

    public function ping()
    {
        $redir = trim(base64_decode($_GET['redir']));

        if (!isset($_SESSION['crm_session_id']) || !isset($_GET['redir'])) {
            header('Location: '.$redir);
        }

        $html    = array();
        $html[] = '<form method="post" action="'.$redir.'">';
        $html[] = '<input type="hidden" name="sid" value="'.$_SESSION['crm_session_id'].'">';
        $html[] = '</form>';
        $html[] = '<script>document.forms[0].submit();</script>';

        die(implode(' ', $html));
    }

    /**
     * do the logout and destroy the session
     *
     */
    public function logout()
    {
        session_destroy();

        $redirect = $this->registry->config['logout_redirect'];
        if (empty($redirect) || !isset($redirect)) {
            $redirect = "https://www.tecart.de/ticket-support";
        }

        header("Location: ".$redirect);
        exit;
    }

    public function change_password()
    {
        if (isset($_POST['old_password']) && isset($_POST['new_password']) && isset($_POST['confirm_password'])) {
            $old_pass        = $_POST['old_password'];
            $new_pass        = $_POST['new_password'];
            $confirm_pass    = $_POST['confirm_password'];

            // Check username and password

            if (empty($new_pass) || empty($confirm_pass)) {
                $this->view->error_msg = $this->translate['err_no_new_password'];
                unset($_POST);
                return $this->change_password();
            }

            if ($new_pass != $confirm_pass) {
                $this->view->error_msg = $this->translate['err_password_not_matched'];
                unset($_POST);
                return $this->change_password();
            }

            $crm_contact = new crmContacts($this->registry->config);
            $contact_id = $crm_contact->do_contact_authenticate($_SESSION["login_number"], $old_pass);

            if (null === $contact_id) {
                $this->view->error_msg = $this->translate['err_wrong_password'];
                unset($_POST);
                return $this->change_password();
            }


            try {
                $success = $crm_contact->change_password($_SESSION["cid"], $new_pass);

                if ($success) {
                    $this->view->error_msg = $this->translate['change_pass_success'];
                } else {
                    $this->view->error_msg = $this->translate['change_pass_error'];
                }
            }
            catch(Rest_Exception_CouldNotAuthenticate $e) {
                $this->view->error_msg = $this->translate['change_pass_error'];
                return;
            }

        }

        $this->view->login_nr     = $_SESSION['login_number'];
        $this->view->company      = $_SESSION['company'];
        $this->view->under_note   = true;
        $this->view->navi         = true;

        $this->view->title    = $this->translate['title'];

        $this->view->render('change_password');
        return true;
    }

    /**
     * get data from sqlite database and write it in to the session
     *
     * @return boolean
     */
    private function init_data()
    {
        include __SITE_PATH . '/application/models/' . 'sqLiteAbstract.php';
        if (!function_exists('sqlite_open')) {
            include __SITE_PATH . '/application/models/' . 'sqLite3.php';
        } else {
            include __SITE_PATH . '/application/models/' . 'sqLite.php';
        }

        $sqlite = new sqLite();
        $sqlite->check_tables();

        $timestamp = $sqlite->get_data('timestamp');

        $tms = isset($timestamp[0]['timestamp']) ? $timestamp[0]['timestamp'] : 0;

        $this->init_users($sqlite, $tms);

        $this->init_lists($sqlite, $tms);

        $this->init_contacts($sqlite);

        $insert = ($tms == 0) ? true : false;
        $sqlite->update_timestamp($insert);

        return true;
    }

    private function init_lists($sqlite, $timestamp)
    {
        // Lists
        $ticket_lists = $sqlite->get_data('ticket_lists');
        $crmLogon = new crmLogon();

        if (empty($ticket_lists)) {
            $lists = $crmLogon->get_changed_lists(0);
        } else {
            $lists = $crmLogon->get_changed_lists($timestamp);
        }

        if (count($lists)) {
            $list_arr = array('ticketstatus', 'ticketcategories', 'ticketpriority');

            foreach ($lists as $list) {
                if (in_array($list->name, $list_arr)) {
                    $crm_list = $crmLogon->get_ticket_lists($list->name);
                    if (null === $crm_list) {
                        continue;
                    }

                    $sqlite->insert_list($list->name, $crm_list);
                }
            }

            $ticket_lists = $sqlite->get_data('ticket_lists');
        }

        $ticketstatus        = array();
        $ticketcategories    = array();
        $ticketpriority    = array();
        foreach ($ticket_lists as $list) {
            if ($list['list'] == 'ticketstatus') {
                $ticketstatus[$list['value']] = $list['name'];
            } elseif ($list['list'] == 'ticketcategories') {
                $ticketcategories[$list['value']] = $list['name'];
            } elseif ($list['list'] == 'ticketpriority') {
                $ticketpriority[$list['value']] = $list['name'];
            }
        }

        if (!isset($_SESSION['ticketstatus'])) {
            $_SESSION['ticketstatus']     = $ticketstatus;
        }
        if (!isset($_SESSION['ticketcategories'])) {
            $_SESSION['ticketcategories'] = $ticketcategories;
        }
        if (!isset($_SESSION['ticketpriority'])) {
            $_SESSION['ticketpriority']   = $ticketpriority;
        }

        return true;
    }

    private function init_users($sqlite, $timestamp)
    {
        // Users
        $users = $sqlite->get_data('users');
        $crmLogon = new crmLogon();

        try {
            if (empty($users)) {
                $crm_users = $crmLogon->get_changed_users(0);
            } else {
                $crm_users = $crmLogon->get_changed_users($timestamp);
            }
        }
        catch (Rest_Exception_CouldNotAuthenticate $e) {
            $this->view->error_msg = $this->translate['err_crm_connect'];
            $this->index();
            return;
        }

        if (count($crm_users)) {
            $sqlite->insert_users($crm_users);
            $users = $sqlite->get_data('users');
        }

        $arr_users = array();
        foreach ($users as $user) {
            $arr_users[$user['userid']]['name']  = $user['name'];
            $arr_users[$user['userid']]['login'] = $user['login'];
        }

        if (!isset($_SESSION["crm_users"])) {
            $_SESSION["crm_users"] = $arr_users;
        }

        return true;
    }

    private function init_contacts($sqlite)
    {
        $contact = array();
        // Get Contacts by id
        $contact = $sqlite->get_contact($this->cid);

        $action = '';
        if (empty($contact)) {
            $action = 'insert';
        } elseif (isset($contact['last_update']) && ($contact['last_update'] + 24*3600) < time()) {
            $action = 'update';
        }

        if (!empty($action)) { // do insert or update contact info

            // Get contact
            $crm_contact = $this->crm_contact->get_contact_by_id($this->cid);

            if (!$crm_contact) {
                log_error('Error getting Contact!');
                return false;
            }

            // Get min and max time for the search function
            $crm_tickets = new crmTickets();

            // Get crm version
            $crmLogon = new crmLogon();
            $crm_version = $crmLogon->get_crm_version();

            $values = array();
            $values['cid']                = $this->cid;
            $values['name']               = $crm_contact->company;
            $values['crm_version']        = $crm_version;

            if ($action == 'insert') { // get only one time.
                $min_max = $crm_tickets->get_min_max_createtime($this->cid);

                if (empty($min_max->min)) {
                    //log_error('Empty min max ticket time for contact id: '.$this->cid);

                    $values['min_ticket_year']    = date('Y') - 1 ;
                    $values['max_ticket_year']    = date('Y') + 1;
                } else {
                    $values['min_ticket_year']    = !empty($min_max->min) ? date('Y', $min_max->min) - 1 : date('Y') - 1 ;
                    $values['max_ticket_year']    = date('Y') + 1;
                }
            }

            // Get and save access to partner portal, only if partner portal function is enabled
            if (isset($this->registry->config['partners']['enabled']) && $this->registry->config['partners']['enabled']) {
                $pa_field = $this->registry->config['partners']['partner_access_field'];
                $values['partner_access'] = (isset($crm_contact->$pa_field)) ? $crm_contact->$pa_field : 0;
                $partner = new crmContract($this->registry->config, $this->registry->baseUrl);
                $values['partner_contract'] = (int)$partner->has_partner_contract($this->cid);
            }

            if ($action == 'insert') { // if contact is not exists. then add new contact.
                $success = $sqlite->insert_contact($values);
            } elseif ($action =='update') { // update
                $success = $sqlite->update_contact($values);
            }

            if (!$success) {
                log_error('Error by insert or update contact data!');
            }

            $contact['name']            = $values['name'];
            $contact['crm_version']        = $crm_version;

            if (isset($pa_field)) {
                $contact['partner_access'] = $values['partner_access'];
                $contact['partner_contract'] = $values['partner_contract'];
            }

            if ($action == 'insert') {
                $contact['min_ticket_year'] = $values['min_ticket_year'];
                $contact['max_ticket_year'] = $values['max_ticket_year'];
            }
        }

        if (!isset($_SESSION["company"])) {
            $_SESSION["company"]            = $contact['name'];
        }
        if (!isset($_SESSION["min_time"])) {
            $_SESSION["min_time"]           = $contact['min_ticket_year'];
        }
        if (!isset($_SESSION["max_time"])) {
            $_SESSION["max_time"]           = $contact['max_ticket_year'];
        }
        if (!isset($_SESSION["crm_version"])) {
            $_SESSION["crm_version"]        = $contact['crm_version'];
        }
        if (!isset($_SESSION["partner_access"])) {
            $_SESSION["partner_access"]     = isset($contact['partner_access']) ? $contact['partner_access'] : false;
        }
        if (!isset($_SESSION["partner_contract"])) {
            $_SESSION["partner_contract"]   = isset($contact['partner_contract']) ? $contact['partner_contract'] : false;
        }

        return true;
    }
}
