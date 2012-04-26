<?php

App::uses('Controller', 'Controller');

class HostsController extends AppController {
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
