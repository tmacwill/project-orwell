<?php

App::uses('Controller', 'Controller');

class DocumentsController extends AppController {
    /**
     * Add a new document
     *
     */
    public function add() {
        // form submitted
        if ($this->request->is('post')) {
            // hash document
            $file = file_get_contents($this->request->data['url']);
            $md5 = md5($file);
            $sha1 = sha1($file);

            // check if document exists
            $document = $this->Document->find('first', array(
                'conditions' => array(
                    'md5' => $md5,
                    'sha1' => $sha1
                )
            ));

            // document doesn't exist, so add
            if (!$document) {
                $document = $this->Document->save(array(
                    'md5' => $md5,
                    'sha1' => $sha1
                ));
            }

            // add document to host
            $this->Document->HostDocuments->save(array(
                'document_id' => $document['Document']['id'],
                'host_id' => $this->request->data['host_id'],
                'url' => $this->request->data['url']
            ));

            echo json_encode($document);
            exit;
        }
    }
}
