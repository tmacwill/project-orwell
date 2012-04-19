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
            $file = file_get_contents($_FILES['file']['tmp_name']);
            $md5 = md5($file);
            $sha1 = sha1($file);

            // check if document already exists on server (based on hash)
            $document = $this->Document->find('first', array(
                'conditions' => array(
                    'md5' => $md5,
                    'sha1' => $sha1
                )
            ));

            // document doesn't exist, so add
            if (!$document) {
                // add document to database
                $document = $this->Document->save(array(
                    'name' => $this->request->data['name'],
                    'filename' => $this->request->data['filename'],
                    'md5' => $md5,
                    'sha1' => $sha1
                ));

                // create new directory for file
                $path = ROOT . DS . APP_DIR . DS . 'files' . DS . $document['Document']['id'];
                @mkdir($path, 0777, true);
                $path .= DS . $this->request->data['filename'];

                // move uploaded file to directory
                move_uploaded_file($_FILES['file']['tmp_name'], $path);

                // update document with path
                $this->Document->set('path', $path);
                $this->Document->save();
            }

            // add document to uploading host
            /*
            $this->Document->HostDocuments->save(array(
                'document_id' => $document['Document']['id'],
                'host_id' => 1,
                'url' => $this->request->data['url']
            ));
             */

            echo json_encode($document);
            exit;
        }
    }

    /**
     * Retrieve a list of all documents
     *
     */
    public function all() {
        $this->Document->contain();
        $documents = $this->Document->find('all');

        echo json_encode(array('documents' => $documents));
        exit;
    }
}
