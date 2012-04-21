<?php

App::uses('Controller', 'Controller');

class AppController extends Controller {
    public $requireUser = array();

    public function beforeFilter() {
        // define URL to central server
        if (getenv('SERVER') == 'DEV')
            define('SERVER_URL', 'http://localhost/server/');

        // make sure we have a session
        @session_start();

        // if action requires login, then redirect user to login page
        if (in_array($this->request->action, $this->requireUser)) {
            if (!isset($_SESSION['user'])) {
                $this->redirect('/users/login?return=' . $this->request->here);
                exit;
            }
        }
    }

    public function request($url, $post = null) {
        // create curl object pointing to given url
        $ch = curl_init(SERVER_URL . $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // attach post data if given
        if ($post) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        }

        // execute request
        $response = json_decode(curl_exec($ch), true);
        curl_close($ch);

        return $response;
    }
}
