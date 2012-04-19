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
            $response = $this->request("documents/add?client={$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}", array(
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
     * Download a document from the central server
     *
     * @param $id ID of document to download
     *
     */
    public function download($id) {
        // request file from central server
        $this->request("documents/download/$id?client={$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}");

        // redirect to document management screen
        $this->redirect('/documents/manage');
        exit;
    }

    /**
     * Manage documents
     *
     */
    public function manage() {
        $documents = $this->Document->find('all');
        $this->set(compact('documents'));
    }

    /**
     * View a document hosted on the client
     *
     * @param $id ID of document to view
     *
     */
    public function view($id) {
        // get document to view
        $document = $this->Document->findById($id);
        $file = $document['Document']['path'];
        $contents = file_get_contents($file);

        // make sure file exists, and output to browser if so
        if (file_exists($file)) {
            header('Content-Description: File Transfer');
            header('Content-Type: ' . mime_content_type($file));
            header('Content-Disposition: inline; filename=' . basename($file));
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-Length: ' . strlen($contents));
            ob_clean();
            flush();
            echo $contents;
        }
    }
}
