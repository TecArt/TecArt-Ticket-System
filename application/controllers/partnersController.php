<?php
        include __SITE_PATH . '/application/controllers/secureController.php';
    include __SITE_PATH . '/application/models/crmContacts.php';
    include __SITE_PATH . '/application/models/crmContract.php';
    include __SITE_PATH . '/application/models/crmLogon.php';
    include __SITE_PATH . '/application/models/crmDocuments.php';

    class partnersController extends secureController
    {
        private $pid;
        private $ticket = null;

        private $root_path;
        private $crm_session_id;

        public function __construct($registry)
        {
            parent::__construct($registry);

            $this->view->login_nr    = $_SESSION['login_number'];
            $this->view->company    = $_SESSION['company'];
            $this->view->under_note    = true;
            $this->view->navi        = true;

            $this->view->title        = $this->translate['title'];
            
            $this->root_path        = isset($this->registry->config['partners']['folder_name'])
                                    ? $this->registry->config['partners']['folder_name']
                                    : '';
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
         * Show documents for partners
         */
        public function show()
        {
            // get the customer id from session
            $cid = $_SESSION['cid'];
            if (empty($cid) || !isset($cid)) {
                $this->view->error_msg = $this->translate['err_cid_not_found'];
                $this->index();
                return;
            }
            
            if (!isset($this->registry->config["partners"]["enabled"]) || (isset($this->registry->config["partners"]["enabled"]) && !$this->registry->config["partners"]["enabled"])) {
                $this->view->error_msg = $this->translate['err_partners_not_enabled'];
                $this->index();
                return false;
            }
            
            if (!$this->view->partners_enabled) {
                $this->view->error_msg = $this->translate['err_partners_no_contract'];
                $this->index();
                return false;
            }

            $path    = (isset($this->registry->Params['tree_path']) && $this->registry->Params['tree_path'] != $this->root_path)
                    ? trim(strip_tags(str_rot13(base64_decode(rawurldecode($this->registry->Params['tree_path'])))))
                    : '';
            
            $docs = $this->get_documents($path);
            
            // check if current path to open is equal to root path, which can be a subfolder
            // if equal, the set path empty to hide levelup-buttons
            if ($this->root_path == $path) {
                $path = '';
            }
            
            $this->view->backlink = dirname($path);
            $this->view->current  = $path;
            $this->view->folder      = str_replace('/', ' &raquo; ', ltrim(str_replace($this->root_path, '', $path), '/'));
            $this->view->docs      = $docs;
            $this->view->pid      = $this->registry->config["partners"]["project_id"];
            $this->view->action      = 'partners';

            if (isset($this->registry->Params['ajax'])) {
                $this->view->is_ajax = true;
                $this->view->render_ajax('show_documents');
            } else {
                $this->view->render('show_documents');
            }

            return;
        }
        
        public function wishlist()
        {
            $this->view->action      = 'wishlist';
            $this->view->render('wishlist');
        }
        
        /**
         * Download a document
         */
        public function download_doc()
        {
            $doc_name  = trim(str_rot13(base64_decode(rawurldecode($this->registry->Params['name']))));
            $pid       = (int)$this->registry->Params['pid'];
        
            // get the customer id from session
            $cid = $_SESSION['cid'];
            if (empty($cid) || !isset($cid)) {
                $this->view->error_msg = $this->translate['err_cid_not_found'];
                $this->index();
                return;
            }
        
            $crmDocs = new crmDocuments($this->crm_session_id, $this->registry->config, 'projects');
        
            $doc =  $crmDocs->get_document($doc_name, $pid);
            if ($doc === false) {
                $this->view->error_msg = $this->translate['err_db'];
                $this->index();
                return;
            }

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
                header('Content-Disposition: attachment; filename="'.basename($doc->path).'";');
                header("Content-Transfer-Encoding: binary");
                header("Content-Length: ".$doc->filesize);
                echo $doc->content;
            }
            exit;
        }
        
        /**
         * Get documents from partner project
         * @param string $path
         * @return array
         */
        private function get_documents($path)
        {
            if (!isset($this->registry->config["partners"]["project_id"])) {
                return array();
            }
            
            if (!$path) {
                $path = $this->root_path;
            }
            
            $documents    = new crmDocuments($this->crm_session_id, $this->registry->config, 'projects');
            $doc_tree    = $documents->get_documents_tree($path, $this->registry->config["partners"]["project_id"]);
            
            if ($doc_tree === false) {
                $this->view->error_msg = $this->translate['err_connection'];
                $this->index();
                exit;
            }

            $docs = array();
            foreach ($doc_tree as $doc) {
                // 				if($doc->folder == 1 && $doc->link) {
                // 					continue;
                // 				}

                // basename() entfernt Umlaute, etc. am Anfang des Pfadnamens
                $basename                = explode('/', $doc->path);

                $listDoc                = array();
                $listDoc['name']        = ltrim(array_pop($basename), '\/');
                $listDoc['type']        = $doc->mimetype;
                $listDoc['size']        = round($doc->filesize/1024);
                $listDoc['path']        = $doc->path;
                $listDoc['edittime']    = $doc->change_time;
                $listDoc['isfolder']    = $doc->folder == 1 ? true : false;
                $listDoc['icon']        = $doc->folder == 1 ? 'folder' : pathinfo($doc->path, PATHINFO_EXTENSION);
                
                $docs[] = $listDoc;
            }
            
            return $docs;
        }
    }
