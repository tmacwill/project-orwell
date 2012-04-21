<?php

App::uses('Controller', 'Controller');

class HostsController extends AppController {
    /**
     * Add a host to a document
     *
     */
    public function add() {
        // make sure valid api key is given
        $this->verifyKey();

        // save mapping of host to document
        $this->Host->save(array(
            'document_id' => $this->request->data['document_id'],
            'url' => $this->request->data['url']
        ));

        echo json_encode(array('success' => true));
        exit;
    }
}
