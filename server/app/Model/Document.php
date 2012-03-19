<?php

App::uses('Model', 'Model');

class Document extends AppModel {
    public $hasMany = array(
        'HostDocuments' => array(
            'className' => 'HostDocument'
        )
    );
}
