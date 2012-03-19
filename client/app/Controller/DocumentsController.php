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

            // inform central server we have uploaded a document
            $ch = curl_init(SERVER_URL . 'documents/add');
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, "host_id=1&url={$this->request->data['url']}");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_VERBOSE, 1);
            $response = json_decode(curl_exec($ch), true);
            curl_close($ch);

            // check if document already exists
            $document = $this->Document->findById($response['Document']['id']);

            // document doesn't exist, so add
            if (!$document) {
                $document = $this->Document->save(array(
                    'id' => $response['Document']['id'],
                    'name' => $this->request->data['name'],
                    'url' => $this->request->data['url'],
                    'md5' => $md5,
                    'sha1' => $sha1
                ));
            }

            // redirect to host page
            $this->redirect('/documents/manage');
            exit;
        }
    }

    /**
     * Manage documents
     *
     */
    public function manage() {
        $documents = $this->Document->find('all');
        $this->set(compact('documents'));
    }
}
