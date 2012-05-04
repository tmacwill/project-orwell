<?php

/**
 * @file AppController.php
 * @brief Base controller inherited by other controllers.
 * @author Tommy MacWilliam
 *
 */

App::uses('Controller', 'Controller');

class AppController extends Controller {
    // salt used for api key generation
    const SALT = 'ZiTtRaIn42';

    public function beforeFilter() {
        @session_start();
    }
}
