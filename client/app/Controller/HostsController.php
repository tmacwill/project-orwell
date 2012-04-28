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

    /**
     * Notify a host that our document is correct
     *
     */
    public function notify($document_id) {
        // extract URLs 
        preg_match('/^([\w\/]+)\/hosts\/notify/', "{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}", $matches);
        $client = $matches[1];
        $compare = $_GET['compare'];
        $key = Configure::read('apiKey');

        // send request to central server to begin email conversation
        $this->request("hosts/notify/$document_id?client=$client&compare=$compare");
        $this->redirect('/documents/manage');
    }
}
