<?php

App::uses('Controller', 'Controller');

class DocumentsController extends AppController {
    public $requireUser = array('add', 'browse', 'download', 'manage');

    public function beforeFilter() {
        parent::beforeFilter();
        Controller::loadModel('Host');
        Controller::loadModel('User');
    }

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
            if (isset($response['Document']['id']) && $response['Document']['id']) {
                $this->request->data['id'] = $response['Document']['id'];
                $this->acquireDocument();
            }

            // upload failed, so redirect back to document management
            else {
                $this->redirect('/documents/manage');
                exit;
            }
        }
    }

    /**
     * Wrapper for document acquisition method, checking api key first
     *
     */
    public function acquire() {
        // make sure valid api key is given
        $this->verifyKey();
        
        $this->acquireDocument();
    }

    /**
     * Acquire a file to host 
     *
     */
    private function acquireDocument() {
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
        // get list of all documents hosted by client
        $documents = $this->Document->find('all');
        $document_ids = array_map(function ($e) { return $e['Document']['id']; }, $documents);

        // get list of documents from the central server, filtering those already hosted by client
        $all_documents = $this->request('documents/all');

        // make sure server returned a valid list of documents
        if ($all_documents)
            $all_documents = array_filter($all_documents['documents'], function($e) use ($document_ids) {
                return !in_array($e['Document']['id'], $document_ids);
            });
        else
            $all_documents = array();

        // send documents to view
        $this->set('documents', $all_documents);
    }

    /**
     * Display the differences between two text files
     *
     */
    public function diff($id) {
        require_once ROOT . DS . APP_DIR . DS . 'Lib' . DS . 'Diff.php';
        require_once ROOT . DS . APP_DIR . DS . 'Lib' . DS . 'Diff' . DS . 'Renderer' . DS . 
            'Html' . DS . 'SideBySide.php';
        require_once ROOT . DS . APP_DIR . DS . 'Lib' . DS . 'Diff.php';

        // get document to verify
        $document = $this->Document->findById($id);
        if (!$document)
            exit;

        // read local file
        $document_contents = file_get_contents($document['Document']['path']);

        // read file to compare to
        $compare_url = "http://{$_GET['compare']}/documents/view/$id";
        $compare_contents = file_get_contents($compare_url);

        // make sure neither file is binary
        if (strpos($document_contents, "\x00") !== false || strpos($compare_contents, "\x00") !== false)
            exit;

        // compute and render diff
        $diff = new Diff(explode("\n", $document_contents), explode("\n", $compare_contents));
        $renderer = new Diff_Renderer_Html_SideBySide;
        $diff_contents = $diff->render($renderer);

        // replace library default text
        $diff_contents = preg_replace('/<th colspan="2">Old Version<\/th>/', 
            "<th colspan=\"2\">{$document['Document']['filename']} (you)</th>", $diff_contents);
        $diff_contents = preg_replace('/<th colspan="2">New Version<\/th>/', 
            "<th colspan=\"2\">http://{$_GET['compare']}</th>", $diff_contents);

        $this->set('diff', $diff_contents);
    }

    /**
     * Download a document from the central server
     *
     * @param $id ID of document to download
     *
     */
    public function download($id) {
        // request file from central server
        $response = $this->request("documents/download/$id?client={$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}");

        // save all clients who are already hosting the file we just downloaded
        $data = array();
        foreach ($response['hosts'] as $host) {
            $data[] = array(
                'document_id' => $id,
                'url' => $host['Host']['url']
            );
        }
        $this->Host->saveAll($data);

        // redirect to document management screen
        echo json_encode(array('success' => true));
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
     * Verify the integrity of a single document
     *
     * @param $id ID of document to verify
     *
     */
    public function verify($id = 0) {
        // if no ID given, pick a random document
        if ($id == 0)
            $document = $this->Document->find('first', array('order' => 'rand()'));
        // get specified document
        else 
            $document = $this->Document->findById($id);

        // make sure document exists
        if (!$document) {
            echo json_encode(array('success' => false));
            exit;
        }

        // read our document
        $client_document = file_get_contents($document['Document']['path']);

        // get a random host to compare against
        $host = $this->Host->find('first', array(
            'conditions' => array('document_id' => $document['Document']['id']),
            'order' => 'rand()'
        ));

        // make sure valid host was found
        if (!$host) {
            echo json_encode(array('success' => false));
            exit;
        }
        
        // read file to compare against
        $url = "http://{$host['Host']['url']}/documents/view/{$document['Document']['id']}";
        $compare_document = file_get_contents($url);

        // check if documents are the same
        if (md5($client_document) == md5($compare_document) &&
            sha1($client_document) == sha1($compare_document) &&
            base64_encode($client_document) == base64_encode($compare_document)) {

            echo json_encode(array('success' => true, 'same' => true));
        }

        // documents are not the same
        else {
            // only non-binary files should be diff-able
            $should_compare = true;
            if (strpos($client_document, "\x00") !== false || strpos($compare_document, "\x00") !== false)
                $should_compare = false;

            // retrieve users on the client
            $users = $this->User->find('all');

            // send email
            require_once ROOT . DS . APP_DIR . DS . 'Lib' . DS . 'phpmailer' . DS . 'phpmailer.inc.php';
            $mail = new PHPMailer();  
            $mail->IsSMTP(); 
            $mail->SMTPDebug = 0;
            $mail->SMTPAuth = true;
            $mail->SMTPSecure = 'ssl';
            $mail->Host = 'smtp.gmail.com';
            $mail->Port = 465; 
            $mail->Username = 'projectorwell@gmail.com';
            $mail->Password = 'ProjectOrwell42';
            $mail->SetFrom('projectorwell@gmail.com', 'Project Orwell');
            $mail->Subject = 'Document Integrity Compromised!';
            $mail->Body = "A document hosted on your Orwell, {$document['Document']['name']}, does not match the copy hosted by http://{$host['Host']['url']}.";
            foreach ($users as $user)
                $mail->AddAddress($user['User']['email']);
            $mail->Send();

            echo json_encode(array(
                'success' => true, 
                'same' => false,
                'document' => $document['Document']['id'],
                'compare' => ($should_compare) ? $host['Host']['url'] : false
            ));
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

        exit;
    }
}
