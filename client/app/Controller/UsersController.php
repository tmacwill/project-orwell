<?php

App::uses('Controller', 'Controller');

class UsersController extends AppController {
    /**
     * Log the user in
     *
     */
    public function login() {
        // form submitted
        if ($this->request->is('post')) {
            // make sure necessary parameters are set
            if (isset($this->request->data['email'], $this->request->data['password'])) {
                // check if user with given email and password exists
                $user = $this->User->find('first', array(
                    'conditions' => array(
                        'email' => $this->request->data['email'],
                        'password' => sha1($this->request->data['password'])
                    )
                ));

                // incorrect credentials, so redirect back to login page
                if (!$user) {
                    $url = isset($_GET['return']) ? $_GET['return'] : '';
                    $this->redirect("/users/login?return=$url");
                    exit;
                }

                // log user in
                $_SESSION['user'] = $user['User']['id'];

                // redirect the user to where they were trying to go
                if ($_GET['return']) {
                    // redirect without using cake redirect methods because return includes webroot
                    header('Location: ' . $_GET['return']);
                    exit;
                }

                // by default, redirect to document management
                $this->redirect('/documents/manage');
                exit;
            }
        }

        else {
            // check if any users already exist, and if not, show registration form
            $users = $this->User->find('first');
            if (!$users) {
                $this->redirect('/users/register');
                exit;
            }
        }
    }

    /**
     * Log the user out
     *
     */
    public function logout() {
        session_destroy();
        $this->redirect('/');
        exit;
    }

    /**
     * Register a new user
     *
     */
    public function register() {
        // form submitted
        if ($this->request->is('post')) {
            // make sure paramters are set and passwords match
            if (isset($this->request->data['email'], $this->request->data['password'],
                $this->request->data['confirm']) &&
                $this->request->data['password'] == $this->request->data['confirm']) {

                // save user object
                $user = $this->User->save(array(
                    'email' => $this->request->data['email'],
                    'password' => sha1($this->request->data['password'])
                ));

                // log user in
                $_SESSION['user'] = $user['User']['id'];

                // redirect to document management screen
                $this->redirect('/documents/manage');
                exit;
            }
        }
    }
}
