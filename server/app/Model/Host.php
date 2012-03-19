<?php

App::uses('Model', 'Model');

class Host extends AppModel {
    public $actsAs = array('Containable');
    public $hasMany = array(
        'HostDocuments' => array(
            'className' => 'HostDocument'
        )
    );
}
