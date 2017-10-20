<?php
        include __SITE_PATH . '/application/controllers/' . 'secureController.php';
    include __SITE_PATH . '/application/models/' . 'crmTickets.php';
    include __SITE_PATH . '/application/models/' . 'crmContacts.php';
    include __SITE_PATH . '/application/models/' . 'crmLogon.php';
    include __SITE_PATH . '/application/models/' . 'crmDocuments.php';

    class ticketController extends secureController
    {
        private $tid;
        private $ticket = null;

        private $crm_session_id;

        public function __construct($registry)
        {
            parent::__construct($registry);

            $this->crm_session_id    = $_SESSION['crm_session_id'];

            $this->view->login_nr    = $_SESSION['login_number'];
            $this->view->company    = $_SESSION['company'];
            $this->view->under_note    = true;
            $this->view->navi        = true;

            $this->view->title    = $this->translate['title'];
        }

        /**
         * show error message or notice for the user
         *
         */
        public function index()
        {
            $this->view->render('index');
        }

        /**
         * show tickets, basis on type(show all, show current, show open or show not handel tickets) and time period
         *
         */
        public function show()
        {
            $type = isset($this->registry->Params['type']) ? strip_tags($this->registry->Params['type']) : 'current';
            if ($type !== 'current' && $type !== 'all' && $type !== 'closed' && $type !== 'open') {
                $type = 'current';
            }

            // get the customer id from session
            $cid = $_SESSION['cid'];
            if (empty($cid) || !isset($cid)) {
                $this->view->error_msg = $this->translate['err_cid_not_found'];
                $this->index();
                return;
            }

            // sql condition for data base query
            $values = array();
            $values['cid'] = $cid;

            if ($type == 'closed') {
                $values['status'] = 3;
            }
            if ($type == 'open') {
                $values['status'] = 0;
            }
            // -1 marks for all tickets excepting closed tickets.
            if ($type == 'current') {
                $values['status'] = -1;
            }

            // get the month and year to sort elements
            if (isset($_POST['month']) && isset($_POST['year'])) {
                $month       = intval(strip_tags($_POST['month']));
                $year        = intval(strip_tags($_POST['year']));

                if (intval($_POST['year']) !== 0) {
                    $time = $this->calculate_time($month, $year);
                    $values['begin_time'] = $time['begin'];
                    $values['end_time']   = $time['end'];
                }
            }

            // get tickets
            $crmTickets = new crmTickets($this->crm_session_id, $this->registry->config, $this->registry->baseUrl);

            $sections = $this->load_sections($crmTickets);
            $tickets = array();

            if (isset($_POST['section'])) {
                $values['section']                    = intval($_POST['section']);
                $_SESSION['selectedsection']        = intval($_POST['section']);
                $this->view->selectedsection        = intval($_POST['section']);
            } else {
                if (isset($_SESSION['selectedsection'])) {
                    if (!isset($sections[$_SESSION['selectedsection']])) {
                        $sectionIds = array_keys($sections);
                        $_SESSION['selectedsection'] = $sectionIds[0];
                    }

                    $values['section']                = intval($_SESSION['selectedsection']);
                    $this->view->selectedsection    = intval($_SESSION['selectedsection']);
                } else {
                    $section_ids                    = array_keys($sections);
                    $values['section']                = count($section_ids) ? array_shift($section_ids) : 0;
                    $this->view->selectedsection    = $values['section'];
                }
            }

            $tickets = $crmTickets->get_by_condition($values);

            if ($tickets === false) {
                $this->view->error_msg = $this->translate['err_connection'];
                $this->index();
                return;
            }
            if (empty($tickets)) {
                $this->view->month        = isset($month) ? $month : 0;
                $this->view->year        = isset($year)  ? $year  : 0;
                $this->view->min_time    = $_SESSION['min_time'];
                $this->view->max_time    = $_SESSION['max_time'];
                $this->view->data        = null;
                $this->view->sections    = $sections;
                $this->view->type        = $type;
                $this->view->render('show_tickets');
                return;
            }

            /** Get all ticket activities belong to the customer id.   **/
            $ticket_ids = array();
            foreach ($tickets as $ticket) {
                $ticket_ids[] = $ticket->id;
            }

            $activities = array();
            $activities['action'] = 1;

            if (isset($this->registry->config['show_email_activities']) && $this->registry->config['show_email_activities'] == 1) {
                $activities['email'] = 1;
            } else {
                $activities['email'] = 0;
            }

            if (isset($this->registry->config['show_call_activities']) && $this->registry->config['show_call_activities'] == 1) {
                $activities['call'] = 1;
            } else {
                $activities['call'] = 0;
            }

            $ticket_activities = $crmTickets->get_tickets_information(implode(';', $ticket_ids), $activities);

            $data = $this->prepare_view_data($tickets, $ticket_activities);

            $this->view->data                   =  $data['data'];
            $this->view->duration               =  $data['total_duration'];
            $this->view->status_fields          =  $_SESSION["ticketstatus"];
            $this->view->priorities_fields      =  $_SESSION["ticketpriority"];

            $this->view->month                  =  isset($month) ? $month : 0;
            $this->view->year                   =  isset($year)  ? $year  : 0;

            $this->view->min_time               =  $_SESSION['min_time'];
            $this->view->max_time               =  $_SESSION['max_time'];

            $this->view->type                   =  $type;
            $this->view->action                 =  $this->registry->action;

            $this->view->sections               =  $sections;

            $this->view->render('show_tickets');
            return;
        }

        /**
         * show one ticket include documents and ticket actions
         *
         */
        public function show_ticket()
        {
            // get ticket id from GET param
            $ticket_id = isset($this->tid) ? $this->tid : strip_tags($this->registry->Params['tid']);

            if (empty($ticket_id) || !isset($ticket_id)) {
                $this->view->error_msg = $this->translate['err_no_ticket_found'];
                $this->index();
                return;
            }

            $tid = intval(base64_decode($ticket_id));
            // get the customer id from session
            $cid = $_SESSION['cid'];
            if (empty($cid) || !isset($cid)) {
                $this->view->error_msg = $this->translate['err_cid_not_found'];
                $this->index();
                return;
            }

            $crmTickets = new crmTickets($this->crm_session_id, $this->registry->config, $this->registry->baseUrl);
            // get ticket and ticket actions from this ticket id

            if ($this->ticket == null) {
                $ticket   = $crmTickets->get_ticket_by_id($tid);
                if ($ticket === false) {
                    $this->view->error_msg = $this->translate['err_connection'];
                    $this->index();
                    return;
                }
            } else {
                $ticket = $this->ticket;
                $this->ticket = null;
            }

            // if the ticket does not belong to the customer, then show error
            if ($ticket[0]->cid != $_SESSION["cid"]) {
                $this->view->error_msg = $this->translate['err_no_authorization'];
                $this->index();
                return;
            }
            
            $closeTicketEnabled     = (isset($this->registry->config['closebutton']['enabled']) && $this->registry->config['closebutton']['enabled'] ? true : false);
            if ($closeTicketEnabled && isset($this->registry->config['closebutton']['sections']) && count($this->registry->config['closebutton']['sections'])) {
                $close_sections     = $this->registry->config['closebutton']['sections'];
                $closeTicketEnabled = in_array($ticket[0]->section, $close_sections) ? true : false;
            }
            
            if (isset($_POST['close_ticket']) && $closeTicketEnabled) {
                $success = $crmTickets->close_ticket($tid);
                if ($success === false) {
                    $this->view->error_msg = $this->translate['err_connection'];
                    $this->index();
                    return;
                }
                
                $ticket[0]->status = 3;
            }

            // get ticket actions from this ticket id
            $tactions = $crmTickets->get_ticket_action_by_ticket_id(array('tid'=>$tid));
            if ($tactions === false) {
                $this->view->error_msg = $this->translate['err_connection'];
                $this->index();
                return;
            }

            // get total duration
            $total_duration = 0;
            if (!empty($tactions)) {
                foreach ($tactions as $value) {
                    $total_duration = $total_duration + $value->duration;
                }
            }

            $sections = $this->load_sections($crmTickets);
            $ticket[0]->sectionName = isset($sections[$ticket[0]->section]) ? $sections[$ticket[0]->section] : '';

            // get documents for this ticket
            $crmDocs = new crmDocuments($this->crm_session_id, $this->registry->config);

            $doc_tree = $crmDocs->get_documents_tree($this->translate['upload_folder_name'], $tid);
            if ($doc_tree === false) {
                $this->view->error_msg = $this->translate['err_connection'];
                $this->index();
                exit;
            }

            // get only file, no folder
            $docs = array();
            foreach ($doc_tree as $doc) {
                if ($doc->folder ==! 1) {
                    $array = array();
                    $array['name']        = substr($doc->path, strlen($this->translate['upload_folder_name'])+2);
                    $array['type']        = $doc->mimetype;
                    $array['size']        = round($doc->filesize/1024);
                    $array['path']        = $doc->path;
                    $array['createtime']  = $doc->ctime;

                    $docs[] = $array;
                }
            }

            $this->view->status_fields          =  $_SESSION["ticketstatus"];
            $this->view->priorities_fields      =  $_SESSION["ticketpriority"];
            $this->view->ticket                 =  $ticket[0];
            $this->view->total_duration         =  $total_duration;
            $this->view->docs                   =  $docs;
            // pass through any unsaved activity entries
            if (isset($_POST['notes_bak']) && !empty($_POST['notes_bak'])) {
                $this->view->notes_bak  = strip_tags($_POST['notes_bak']);
            }

            if (isset($this->registry->config['show_email_activities']) && $this->registry->config['show_email_activities'] == 1) {
                $this->view->show_emails  =  1;
            }

            if (isset($this->registry->config['show_call_activities']) && $this->registry->config['show_call_activities'] == 1) {
                $this->view->show_calls   =  1;
            }
            
            $this->view->showCloseButton = ($ticket[0]->status < 3 && $closeTicketEnabled ? true : false);

            $this->view->render('show_ticket');
        }

        private function parse_ticket_activities($data)
        {
            $activities = array();
            foreach ($data as $activity) {
                if (!empty($activity->crmTicketActionActivities) && count($activity->crmTicketActionActivities)) {
                    foreach ($activity->crmTicketActionActivities as $value) {
                        $action = array();

                        $action['type']           = 'action';
                        $action['user']       =  isset($_SESSION["crm_users"][$value->userid]['name']) ? $_SESSION["crm_users"][$value->userid]['name'] : '';
                        $action['subject']     =  $value->subject;
                        $action['body']        =  $value->body;
                        $action['createtime']  =  $value->createtime;
                        $action['chgtime']     =  $value->chgtime;
                        $action['duration']    =  $value->duration;

                        $activities[] = $action;
                    }
                }

                if (!empty($activity->crmTicketCallActivities) && count($activity->crmTicketCallActivities)) {
                    foreach ($activity->crmTicketCallActivities as $value) {
                        $call = array();

                        $call['type']            = 'call';
                        $call['user']            =  isset($_SESSION["crm_users"][$value->userid]['name']) ? $_SESSION["crm_users"][$value->userid]['name'] : '';
                        $call['subject']           =  $value->subject;
                        $call['body']            =  $value->body;
                        $call['createtime']    =  $value->createtime;
                        $call['chgtime']         =  $value->chgtime;

                        $activities[] = $call;
                    }
                }

                if (!empty($activity->crmTicketEmailActivities) && count($activity->crmTicketEmailActivities)) {
                    foreach ($activity->crmTicketEmailActivities as $value) {
                        $mail = array();

                        $mail['type']            = 'email';
                        $mail['user']           =  $value->from;
                        $mail['subject']        =  $value->subject;
                        $mail['body']            =  $value->body;
                        $mail['createtime']    =  $value->createtime;
                        $mail['chgtime']         =  $value->chgtime;

                        $activities[] = $mail;
                    }
                }
            }

            //bubble sort
            $size = count($activities);
            for ($i=0; $i<$size; $i++) {
                for ($j=0; $j<$size-1-$i; $j++) {
                    if ($activities[$j+1]['createtime'] < $activities[$j]['createtime']) {
                        $tmp = $activities[$j];
                        $activities[$j] = $activities[$j+1];
                        $activities[$j+1] = $tmp;
                    }
                }
            }

            return $activities;
        }

        public function show_activities()
        {
            // get ticket id from GET param
            $ticket_id = isset($this->tid) ? $this->tid : strip_tags($this->registry->Params['tid']);

            $types = array();
            if (isset($this->registry->Params['action']) && $this->registry->Params['action'] == true) {
                $types['action'] = 'action';
            }

            if (isset($this->registry->Params['email']) && $this->registry->Params['email'] == true &&
                 isset($this->registry->config['show_email_activities']) && $this->registry->config['show_email_activities'] == 1) {
                $types['email'] = 'email';
            }

            if (isset($this->registry->Params['call']) && $this->registry->Params['call'] == true &&
                 isset($this->registry->config['show_call_activities']) && $this->registry->config['show_call_activities'] == 1) {
                $types['call'] = 'call';
            }

            $crmTickets = new crmTickets($this->crm_session_id, $this->registry->config, $this->registry->baseUrl);
            $ticket_activities = $crmTickets->get_ticket_activities_by_ticket_id($ticket_id);

            if ($ticket_activities === false) {
                return false;
            }

            $activities = $this->parse_ticket_activities($ticket_activities);

            $values = array();
            if (isset($types['action']) && isset($types['email']) && isset($types['call'])) {
                $values = $activities;
            } else {
                foreach ($activities as $activity) {
                    if (in_array($activity['type'], $types)) {
                        $values[] = $activity;
                    }
                }
            }

            if (!isset($this->registry->Params['action']) && !isset($this->registry->Params['email']) && !isset($this->registry->Params['call'])) {
                $this->view->not_set = true;
            } else {
                $this->view->not_set = false;
            }

            $this->view->activities = $values;

            $this->view->render_frame('show_activities');
        }

        /**
         * show form for create new ticket
         *
         */
        public function new_ticket()
        {
            // get the customer id from session
            $cid = $_SESSION['cid'];

            if (empty($cid) || !isset($cid)) {
                $this->view->error_msg = $this->translate['err_cid_not_found'];
                $this->index();
                return;
            }

            $crmContacts = new crmContacts($this->crm_session_id, $this->registry->config, $this->registry->baseUrl);
            $contact = $crmContacts->get_contact_by_id($cid);
            if ($contact === false) {
                $this->view->error_msg = $this->translate['err_connection'];
                $this->index();
                return;
            }
            if (empty($contact)) {
                $this->view->error_msg = $this->translate['err_no_contact_found'];
                $this->index();
                return;
            }

            if (isset($_SESSION['selectedsection'])) {
                $this->view->selectedsection = $_SESSION['selectedsection'];
            } else {
                $this->view->selectedsection = 0;
            }

            $email = $contact[0]->email;

            $this->view->categories_fields = $_SESSION["ticketcategories"];
            $this->view->priorities_fields = $_SESSION["ticketpriority"];
            $this->view->email = $email;

            $this->view->action = $this->registry->action;
            $this->view->type   = null;


            $crmTickets = new crmTickets($this->crm_session_id, $this->registry->config, $this->registry->baseUrl);
            $this->view->sections = $this->load_sections($crmTickets);

            $this->view->render('new_ticket');
        }

        /**
         * do the create ticket
         *
         */
        public function add_ticket()
        {
            if (!isset($_POST['name']) ||
                 !isset($_POST['email']) ||
                 !isset($_POST['category']) ||
                 !isset($_POST['subject']) ||
                 !isset($_POST['priority']) ||
                 !isset($_POST['notes']) ||
                 !isset($_POST['section'])) {
                $this->view->error_msg = $this->translate['err_create_ticket'];
                $this->index();
            }

            // get the post params
            $name       = strip_tags($_POST['name']);
            $email      = strip_tags($_POST['email']);
            $cate       = intval(strip_tags($_POST['category']));
            $subject    = trim($_POST['subject']);
            $section    = intval(strip_tags($_POST['section']));
            $priority   = intval(strip_tags($_POST['priority']));
            $notes      = trim($_POST['notes']);

            $notes = str_replace('<', '&lt;', $notes);
            $notes = str_replace('>', '&gt;', $notes);

            $subject = str_replace('<', '&lt;', $subject);
            $subject = str_replace('>', '&gt;', $subject);

            if (empty($name) || !isset($name)) {
                $this->view->error_msg  = $this->translate['err_no_name'];
                $this->view->p_email    = isset($email)     ? $email    : null;
                $this->view->cate       = isset($cate)      ? $cate     : null;
                $this->view->subject    = isset($subject)   ? $subject  : null;
                $this->view->notes      = isset($notes)     ? $notes    : null;
                $this->view->priority   = isset($priority)  ? $priority : null;
                $this->view->section    = isset($section)   ? $section  : null;

                $this->new_ticket();
                return;
            }
            if (empty($email) || !isset($email)) {
                $this->view->error_msg = $this->translate['err_no_email'];
                $this->view->p_name     = isset($name)      ? $name     : null;
                $this->view->cate       = isset($cate)      ? $cate     : null;
                $this->view->subject    = isset($subject)   ? $subject  : null;
                $this->view->notes      = isset($notes)     ? $notes    : null;
                $this->view->priority   = isset($priority)  ? $priority : null;
                $this->view->section    = isset($section)   ? $section  : null;

                $this->new_ticket();
                return;
            }
            if ((strpos($email, '@')) == false) {
                $this->view->error_msg = $this->translate['err_not_supported_email'];
                $this->view->p_name     = isset($name)      ? $name     : null;
                $this->view->cate       = isset($cate)      ? $cate     : null;
                $this->view->subject    = isset($subject)   ? $subject  : null;
                $this->view->notes      = isset($notes)     ? $notes    : null;
                $this->view->priority   = isset($priority)  ? $priority : null;
                $this->view->section    = isset($section)   ? $section  : null;

                $this->new_ticket();
                return;
            }

            if (empty($subject) || !isset($subject)) {
                $this->view->error_msg = $this->translate['err_no_subject'];
                $this->view->p_name     = isset($name)      ? $name     : null;
                $this->view->cate       = isset($cate)      ? $cate     : null;
                $this->view->p_email    = isset($email)     ? $email    : null;
                $this->view->notes      = isset($notes)     ? $notes    : null;
                $this->view->priority   = isset($priority)  ? $priority : null;
                $this->view->section    = isset($section)   ? $section  : null;

                $this->new_ticket();
                return;
            }
            if (empty($notes) || !isset($notes)) {
                $this->view->error_msg = $this->translate['err_no_notes'];
                $this->view->p_name     = isset($name)      ? $name     : null;
                $this->view->cate       = isset($cate)      ? $cate     : null;
                $this->view->p_email    = isset($email)     ? $email    : null;
                $this->view->subject    = isset($subject)   ? $subject  : null;
                $this->view->priority   = isset($priority)  ? $priority : null;
                $this->view->section    = isset($section)   ? $section  : null;

                $this->new_ticket();
                return;
            }

            // get the customer id from session
            $cid = $_SESSION['cid'];
            if (empty($cid)) {
                $this->view->error_msg = $this->translate['err_cid_not_found'];
                $this->index();
                return;
            }
            // get responsible person in crm for Ticket webservice.
            $crmTickets = new crmTickets($this->crm_session_id, $this->registry->config, $this->registry->baseUrl);
            $res_user = $crmTickets->get_crm_responsible_user_for_ws($section);

            if (empty($res_user) || !$res_user) {
                $this->view->error_msg = $this->translate['err_no_responsibler_found'];
                $this->index();
                return;
            }

            $category = $_SESSION["ticketcategories"][$cate];

            // start time is the createtime and stop time = starttime + 1 day
            $start_time = time();
            $stop_time  = $start_time + 3600*24;

            $ticket = array( 'cid'          => intval($cid),
                             'name'         => $name,
                             'email'        => $email,
                             'start'        => $start_time,
                             'stop'         => $stop_time,
                             'affected'     => $res_user,
                             'priority'     => intval($priority),
                             'subject'      => $subject,
                             'notes'        => $notes,
                             'status'       => 0,
                             'category'     => $category,
                             'section'      => intval($section));

            $success = $crmTickets->add_ticket($ticket);

            if (!$success) {
                $this->view->error_msg = $this->translate['err_create_ticket'];
                $this->index();
                return;
            }

            // refresh the time period
            $min_time = $crmTickets->get_by_condition(array('cid'=>$cid, 'min_time'=>1));
            $max_time = $crmTickets->get_by_condition(array('cid'=>$cid, 'max_time'=>1));

            if ($min_time === false || $max_time === false) {
                $this->view->error_msg = $this->translate['err_connection'];
                $this->index();
                return;
            }

            $_SESSION["min_time"]       = date('Y', $min_time[0]->createtime);
            $_SESSION["max_time"]       = date('Y', $max_time[0]->createtime);

            $this->view->error_msg = $this->translate['create_ticket_success'];
            $this->view->msg_type  = 'notice';
            $this->show();
            return;
        }

        /**
         * create new ticket action
         *
         */
        public function post_ticket_action()
        {
            if (!isset($_POST['ticket_id'])) {
                $this->view->error_msg = $this->translate['err_tid_not_found'];
                $this->index();
                exit;
            }

            $ticket_id   = intval(strip_tags($_POST['ticket_id']));

            if (!isset($_POST['notes']) || empty($_POST['notes'])) {
                $this->view->error_msg = $this->translate['err_no_notes'];
                header("Location: ".$this->baseUrl."?co=ticket/show_ticket&tid=".base64_encode($ticket_id));
                exit;
            }

            $notes       = trim($_POST['notes']);

            $notes = str_replace('<', '&lt;', $notes);
            $notes = str_replace('>', '&gt;', $notes);

            // get the customer id from session
            $cid = $_SESSION['cid'];
            if (empty($cid) || !isset($cid)) {
                $this->view->error_msg = $this->translate['err_cid_not_found'];
                $this->index();
                return;
            }

            // get all tickets from this customer and check to ensure that he really has the authorization for this Ticket ID
            $crmTickets = new crmTickets($this->crm_session_id, $this->registry->config, $this->registry->baseUrl);

            $ticket   = $crmTickets->get_ticket_by_id($ticket_id);
            if ($ticket === false) {
                $this->view->error_msg = $this->translate['err_connection'];
                $this->index();
                return;
            }
            // if the ticket does not belong to the customer, then show error
            if ($ticket[0]->cid != $_SESSION["cid"]) {
                $this->view->error_msg = $this->translate['err_no_authorization'];
                $this->index();
                return;
            }

            $this->ticket = $ticket;

            // get the user id of the crm logging user.
            $userid = 0;
            foreach ($_SESSION["crm_users"] as $id => $name) {
                if ($name['login'] == $this->registry->config['username']) {
                    $userid = $id;
                    break;
                }
            }

            // atype = 1 is extern action.
            $values['userid']       = $userid;
            $values['ticket_id']    = intval($ticket_id);
            $values['notes']        = $notes;
            $values['atype']        = 1;

            // first 60 letters for subject.
            if (strlen($notes) > 60) {
                $subject=substr($notes, 0, 60);
                while (strlen($subject) > 0 && ord($subject{strlen($subject)-1}) > 32) {
                    $subject = substr($subject, 0, -1);
                }
                $subject.='...';
            } else {
                $subject = $notes;
            }

            $values['subject']      = $subject;


            $success = $crmTickets->add_ticket_action($values);
            if (!$success) {
                $this->view->error_msg = $this->translate['err_create_ticket_action'];
                $this->index();
                return;
            }

            $this->view->error_msg = $this->translate['create_ticket_action_success'];
            $this->view->msg_type  = 'notice';
            $this->tid = base64_encode($ticket_id);
            $this->show_ticket();
            return;
        }

        /**
         * upload a document
         *
         */
        public function upload_doc()
        {
            if (!isset($_POST['ticket_id'])) {
                $this->view->error_msg = $this->translate['err_tid_not_found'];
                $this->index();
                exit;
            }
            $ticket_id   = intval(strip_tags($_POST['ticket_id']));

            if (!is_uploaded_file($_FILES['file']['tmp_name'])) {
                $this->view->error_msg = $this->translate['err_file_upload'];
                header("Location: ".$this->baseUrl."?co=ticket/show_ticket&tid=".base64_encode($ticket_id));
                exit;
            }

            // get the customer id from session
            $cid = $_SESSION['cid'];
            if (empty($cid) || !isset($cid)) {
                $this->view->error_msg = $this->translate['err_cid_not_found'];
                $this->index();
                return;
            }

            // get all tickets from this customer and check to ensure that he really has the authorization for this Ticket ID
            $crmTickets = new crmTickets($this->crm_session_id, $this->registry->config, $this->registry->baseUrl);

            $ticket   = $crmTickets->get_ticket_by_id($ticket_id);
            if ($ticket === false) {
                $this->view->error_msg = $this->translate['err_connection'];
                $this->index();
                return;
            }
            // if the ticket does not belong to the customer, then show error
            if ($ticket[0]->cid != $_SESSION["cid"]) {
                $this->view->error_msg = $this->translate['err_no_authorization'];
                $this->index();
                return;
            }

            $this->ticket = $ticket;

            // get all documents and check, there is a folder for upload documents or not
            $crmDocs = new crmDocuments($this->crm_session_id, $this->registry->config);

            $doc_tree = $crmDocs->get_documents_tree('', $ticket_id);
            if (!is_array($doc_tree)) {
                $this->view->error_msg = $this->translate['err_db'];
                $this->index();
                return;
            }

            $new_folder = true;
            foreach ($doc_tree as $doc) {
                if (($doc->folder == 1) && (strtolower(substr($doc->path, 1)) == strtolower($this->translate['upload_folder_name']))) {
                    $new_folder = false;
                    $folder = substr($doc->path, 1);
                    break;
                }
            }
            // if there is no doc folder for documents available, then create one!
            if ($new_folder == true) {
                $success = $crmDocs->create_folder($this->translate['upload_folder_name'], $ticket_id);
                if ($success != 1) {
                    $this->view->error_msg = $this->translate['err_create_folder'];
                    $this->index();
                    return;
                }
                $folder = $this->translate['upload_folder_name'];
            }

            $content        = file_get_contents($_FILES['file']['tmp_name']);
            $doc_name       = $folder.'/'.$_FILES['file']['name'];
            $base64_content = base64_encode($content);

            // adding doc to crm system
            $success = $crmDocs->upload_document($doc_name, $base64_content, $ticket_id);
            if ($success != 1) {
                $this->view->error_msg = $this->translate['err_adding_doc'];
                $this->index();
                return;
            }

            $this->view->error_msg = $this->translate['doc_upload_success'];
            $this->view->msg_type  = 'notice';
            $this->tid = base64_encode($ticket_id);
            $this->show_ticket();
        }

        /**
         * download a document
         *
         */
        public function download_doc()
        {
            $doc_name  = $this->registry->Params['name'];
            $tid       = $this->registry->Params['tid'];

            // get the customer id from session
            $cid = $_SESSION['cid'];
            if (empty($cid) || !isset($cid)) {
                $this->view->error_msg = $this->translate['err_cid_not_found'];
                $this->index();
                return;
            }

            // get all tickets from this customer and check to ensure that he really has the authorization for this Ticket ID
            $crmTickets = new crmTickets($this->crm_session_id, $this->registry->config, $this->registry->baseUrl);

            $ticket   = $crmTickets->get_ticket_by_id($tid);
            if ($ticket === false) {
                $this->view->error_msg = $this->translate['err_connection'];
                $this->index();
                return;
            }
            // if the ticket does not belong to the customer, then show error
            if ($ticket[0]->cid != $_SESSION["cid"]) {
                $this->view->error_msg = $this->translate['err_no_authorization'];
                $this->index();
                return;
            }

            $crmDocs = new crmDocuments($this->crm_session_id, $this->registry->config);

            $result =  $crmDocs->get_document($this->translate['upload_folder_name']."/".$doc_name, $tid);
            if ($result === false) {
                $this->view->error_msg = $this->translate['err_db'];
                $this->index();
                return;
            }

            $doc = $result[0];
            if (!is_object($doc)) {
                $this->view->error_msg = $this->translate['err_no_document_found'];
                $this->index();
                return;
            }

            if ($doc->filesize > 0) {
                header("Pragma: public");
                header("Expires: 0");
                header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                header("Cache-Control: public");
                header("Content-Description: File Transfer");
                header("Content-Type: ".$doc->mimetype);
                header('Content-Disposition: attachment; filename="'.substr($doc->path, strlen($this->translate['upload_folder_name']) + 2).'";');
                header("Content-Transfer-Encoding: binary");
                header("Content-Length: ".$doc->filesize);
                echo base64_decode($doc->content);
            }
            exit;
        }


        /**
         * Parse the view
         *
         * @param array $tickets
         * @param array $ticket_actions
         * @return array
         */
        private function prepare_view_data($tickets, $ticket_activities)
        {
            $data = array();
            $total_duration = 0;
            foreach ($tickets as $ticket) {
                $duration = 0;
                $activities_amount = 0;
                $max_createtime = 0;
                $createuser = 0;
                $type = '';
                foreach ($ticket_activities as $ticket_activity) {
                    if ($ticket_activity->ticket_id == $ticket->id) {
                        $duration            = $ticket_activity->action_total_duration;
                        $activities_amount    = $ticket_activity->activities_amount;
                        $max_createtime    = $ticket_activity->max_createtime;
                        $createuser        = $ticket_activity->createuser;
                        $type                = $ticket_activity->type;
                    }
                }

                $ta = array();
                $ta['amount']        = $activities_amount;
                $ta['createtime']    = $max_createtime;
                $ta['type']        = $type;

                if ($type == 'email') {
                    $ta['user'] = $createuser;
                } else {
                    $ta['user'] = isset($_SESSION["crm_users"][$createuser]['name']) ? $_SESSION["crm_users"][$createuser]['name'] : '';
                }

                $array = array();
                $array['id']             = $ticket->id;
                $array['tnumber']        = $ticket->tnumber;
                $array['subject']        = $ticket->subject;
                $array['name']           = $ticket->name;
                $array['status']         = $ticket->status;
                $array['priority']       = $ticket->priority;
                $array['duration']       = $duration;
                $array['category']       = $ticket->category;
                $array['createtime']     = $ticket->createtime;
                $array['ticket_action']  = $ta;

                $total_duration = $total_duration + $duration;

                $data[$ticket->id]       = $array;
            }

            return array('data'=>$data, 'total_duration'=>$total_duration);
        }

        /**
         * get ticket actions from ticket ids
         *
         * @param object $crmTickets
         * @param array $tickets
         * @return array
         */
        private function get_ticket_actions($crmTickets, $tickets)
        {
            $ticket_ids = array();
            foreach ($tickets as $ticket) {
                $ticket_ids[] = $ticket->id;
            }

            $ticket_ids = implode(',', $ticket_ids);
            $params = array('tids'=>$ticket_ids);

            $result = $crmTickets->get_ticket_action_by_ids($params);

            if ($result === false) {
                return false;
            }

            return $result;
        }

        /**
         * Calculating the timestamp base on input month and year
         * return a string with a timestamp period  exp:(946681200,978217200)
         * the begin time is the first day of month and the end time is the last day of the month.
         *
         * if $month is 0, then the begin time is the first day of the year and end time is last day of the year.
         *
         * @param int $month (1, 2, 3 ... 12)
         * @param int $year (2000, 2001, 2003 ....)
         * @return array
         */
        private function calculate_time($month, $year)
        {
            if ($month == 0) {
                $begin = mktime(0, 0, 0, 1, 1, $year);
                $end   = mktime(23, 59, 59, 12, 31, $year);
            } else {
                $begin = mktime(0, 0, 0, $month, 1, $year);

                if ($month == 1 || $month == 3 || $month == 5 || $month == 7 || $month == 8 || $month == 10 || $month == 12) {
                    $end   = mktime(23, 59, 59, $month, 31, $year);
                } elseif ($month == 02) {
                    $end   = mktime(23, 59, 59, $month, $this->leap_year($year), $year);
                } else {
                    $end   = mktime(23, 59, 59, $month, 30, $year);
                }
            }

            return array('begin'=>$begin, 'end'=>$end);
        }

        /**
         * check leap year
         * return the day of february for the input year
         *
         * @param int $year
         * @return int
         */
        private function leap_year($year)
        {
            if ($year % 4 != 0) {
                return 28;
            } else {
                if ($year % 100 != 0) {
                    return 29;
                }    // Leap year
                else {
                    if ($year % 400 != 0) {
                        return 28;
                    } else {
                        return 29;
                    }    // Leap year
                }
            }
        }

        /**
         * fetches sections set in config
         * returns array() with id = 0 for standard section
         * @return array
         */
        private function load_sections($crmTickets)
        {
            $crmSections = $crmTickets->get_ticket_sections();

            $return = array();
            if ($crmSections) {
                if (isset($this->registry->config["ticket_sections"]) && $this->registry->config["ticket_sections"] != '') {
                    if (is_array($this->registry->config["ticket_sections"]) || strpos($this->registry->config["ticket_sections"], ':')) {
                        $confSections = (is_array($this->registry->config["ticket_sections"])
                                      ? $this->registry->config["ticket_sections"]
                                      : explode(':', $this->registry->config["ticket_sections"]));

                        foreach ($crmSections as $crmSection) {
                            if (!in_array($crmSection->section_id, $confSections)) {
                                continue;
                            }

                            foreach ($confSections as $confSection) {
                                if ($confSection == $crmSection->section_id) {
                                    $return[$crmSection->section_id] =  $crmSection->name;
                                }
                            }
                        }
                    } else {
                        foreach ($crmSections as $crmSection) {
                            if ($this->registry->config["ticket_sections"] == $crmSection->section_id) {
                                $return[$crmSection->section_id] = $crmSection->name;
                            }
                        }
                    }
                } else {
                    foreach ($crmSections as $crmSection) {
                        $return[$crmSection->section_id] = $crmSection->name;
                    }
                }
            }

            if (!$crmSections || empty($return)) {
                return array($this->translate['section_standard']);
            }

            return $return;
        }
    }
