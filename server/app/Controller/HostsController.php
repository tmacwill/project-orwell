<?php

App::uses('Controller', 'Controller');

class HostsController extends AppController {
    public function beforeFilter() {
        parent::beforeFilter();

        Controller::loadModel('Document');
        Controller::loadModel('Host');
    }

    /**
     * Add a new host
     *
     */
    public function add() {
        // form submitted
        if ($this->request->is('post')) {
            // encrypt password and generate api key
            $this->request->data['password'] = sha1($this->request->data['password']);
            $this->request->data['key'] = sha1(self::SALT . mt_rand() . time());

            // save host
            $host = $this->Host->save($this->request->data);

            // log user in and direct to hosts page
            $_SESSION['host'] = $host['Host'];
            $this->redirect("/hosts/view");
            exit;
        }
    }

    /**
     * Log a user in 
     *
     */
    public function login() {
        // form submitted
        if ($this->request->is('post')) {
            // hash password
            $email = $this->request->data['email'];
            $password = sha1($this->request->data['password']);

            // check for valid credentials
            $host = $this->Host->find('first', array(
                'conditions' => array(
                    'email' => $email,
                    'password' => $password
                )
            ));

            // successful login
            if ($host) {
                // save host info in session
                $_SESSION['host'] = $host['Host'];
                $this->redirect("/hosts/view/{$_SESSION['host']['host_id']}");
                exit;
            }

            // unsuccessful login
            else
                $this->set('error', 'The email or password you entered was not correct');
        }
    }

    /**
     * Log a user out
     *
     */
    public function logout() {
        session_destroy();
        $this->redirect('/');
    }

    /**
     * Notify a host that another host thinks they have the correct copy of the document
     *
     */
    public function notify($document_id) {
        // get given document and hosts
        $document = $this->Document->findById($document_id);
        $client = $this->Host->findByUrl($_GET['client']);
        $compare = $this->Host->findByUrl($_GET['compare']);

        // send email to compare host
        require_once ROOT . DS . APP_DIR . DS . 'Lib' . DS . 'phpmailer' . DS . 'class.phpmailer.php';
        $mail = new PHPMailer();  
        $mail->IsSMTP(); 
        $mail->SMTPDebug = 0;
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = 'ssl';
        $mail->Host = 'smtp.gmail.com';
        $mail->Port = 465; 
        $mail->Username = 'projectorwell@gmail.com';
        $mail->Password = 'ProjectOrwell42';
        $mail->From = 'projectorwell@gmail.com';
        $mail->FromName = 'Project Orwell';
        $mail->Subject = 'Document Integrity Compromised!';
        $mail->Body = "The Orwell host at http://{$client['Host']['url']} (whose admin is CC'd on this email) has reported that your copy of {$document['Document']['name']} does not match theirs. Please head to http://{$compare['Host']['url']}/documents/diff/{$document['Document']['id']}?compare={$client['Host']['url']} to resolve the conflict.";
        $mail->AddAddress($compare['Host']['email']);
        $mail->AddCC($client['Host']['email']);
        $mail->Send();

        echo json_encode(array('success' => true));
        exit;
    }

    /**
     * View the information for the current host
     *
     */
    public function view() {
        // make sure user is logged in
        if (!isset($_SESSION['host'])) {
            $this->redirect('/hosts/login');
            exit;
        }
    }
}
