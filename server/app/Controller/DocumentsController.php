<?php

App::uses('Controller', 'Controller');

class DocumentsController extends AppController {
    public function beforeFilter() {
        parent::beforeFilter();

        Controller::loadModel('Host');
    }

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

            // determine requesting client
            preg_match('/^([\w\/]+)\/documents\/add$/', $_GET['client'], $matches);
            $client = $matches[1];

            // add document to uploading host
            $host = $this->Host->findByUrl($client);
            $this->Document->HostDocuments->save(array(
                'document_id' => $document['Document']['id'],
                'host_id' => $host['Host']['id'],
            ));

            echo json_encode($document);
            exit;
        }
    }

    /**
     * Retrieve a list of all documents
     *
     */
    public function all() {
        $this->Document->contain(array('HostDocuments'));
        $documents = $this->Document->find('all');

        echo json_encode(array('documents' => $documents));
        exit;
    }

    /**
     * Download a document to a client
     *
     * @param $id ID of document to download
     *
     */
    public function download($id) {
        // determine client from which request is coming
        preg_match('/^([\w\/]+)\/documents\/download\/\d+$/', $_GET['client'], $matches);
        $client = $matches[1];

        // get document to download and host requesting document
        $document = $this->Document->findById($id);
        $host = $this->Host->findByUrl($client);

        // make sure we have something to send and somewhere to send it
        if ($host && $document) {
            // get all clients hosting this document
            $this->Host->contain();
            $host_ids = array_map(function ($e) { return $e['host_id']; }, $document['HostDocuments']);
            $hosts = $this->Host->findAllById($host_ids);

            // notify each host that a new client has joined the network
            foreach ($hosts as $h) {
                $url = "http://{$h['Host']['url']}/hosts/add";
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, array(
                    'document_id' => $document['Document']['id'],
                    'url' => $host['Host']['url']
                ));
                curl_exec($ch);
                curl_close($ch);
            }

            // save that client is hosting document
            $this->Document->HostDocuments->save(array(
                'document_id' => $document['Document']['id'],
                'host_id' => $host['Host']['id']
            ));

            // send document and relevant metadata to client
            $url = "http://{$host['Host']['url']}/documents/acquire";
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, array(
                'file' => '@' . $document['Document']['path'],
                'id' => $document['Document']['id'],
                'name' => $document['Document']['name']
            ));
            curl_exec($ch);
            curl_close($ch);

            echo json_encode(array('hosts' => $hosts));
        }

        exit;
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
