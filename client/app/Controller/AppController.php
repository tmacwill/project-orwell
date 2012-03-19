<?php

App::uses('Controller', 'Controller');

class AppController extends Controller {
    public function beforeFilter() {
        if (getenv('SERVER') == 'DEV')
            define('SERVER_URL', 'http://localhost/server/');

        @session_start();
    }
}
