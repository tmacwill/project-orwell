<?php

App::uses('Model', 'Model');

class HostDocument extends AppModel {
    public $belongsTo = array(
        'Host' => array(
            'className' => 'Host'
        ),

        'Document' => array(
            'className' => 'Document'
        )
    );
}
