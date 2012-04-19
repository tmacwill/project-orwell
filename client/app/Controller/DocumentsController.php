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
            // send uploaded document to central server
            $response = $this->request('documents/add', array(
                'file' => '@' .$_FILES['file']['tmp_name'],
                'filename' => $_FILES['file']['name'],
                'name' => $this->request->data['name']
            ));

            // host this document, since we uploaded it
            $this->request->data['id'] = $response['Document']['id'];
            $this->acquire();
        }
    }

    /**
     * Acquire a file to host
     *
     */
    public function acquire() {
        // hash document
        $file = file_get_contents($_FILES['file']['tmp_name']);
        $md5 = md5($file);
        $sha1 = sha1($file);

        // check if document already exists on client
        $document = $this->Document->find('first', array(
            'conditions' => array(
                'md5' => $md5,
                'sha1' => $sha1
            )
        ));

        // document doesn't exist, so add
        if (!$document) {
            // add document to database, being sure to use server's ID, not an auto-generated one
            $document = $this->Document->save(array(
                'id' => $this->request->data['id'],
                'filename' => $_FILES['file']['name'],
                'name' => $this->request->data['name'],
                'md5' => $md5,
                'sha1' => $sha1
            ));

            // create new directory for file
            $path = ROOT . DS . APP_DIR . DS . 'files' . DS . $document['Document']['id'];
            @mkdir($path, 0777, true);
            $path .= DS . $_FILES['file']['name'];

            // move uploaded file to directory
            move_uploaded_file($_FILES['file']['tmp_name'], $path);

            // update document with path
            $this->Document->set('path', $path);
            $this->Document->save();
        }

        // redirect to document management page
        $this->redirect('/documents/manage');
        exit;
    }

    /**
     * Browse all documents on the central server
     *
     */
    public function browse() {
        // get list of documents from the central server
        $documents = $this->request('documents/all');

        // send documents to view
        $this->set('documents', $documents['documents']);
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
