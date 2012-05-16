Project Orwell
===

## Introduction

Cloud-hosted e-books allow content providers to push updated documents to users' devices unbeknownst to their owners. This issue is representative of the larger problem of document integrity verification: how can content providers ensure their versions of hosted documents match the accepted, standard versions? For example, suppose a website hosts the text of the United States Constitution. If a malicious user gains access to the server, he or she could potentially change (perhaps very slightly) the text of the site's version of the Constitution without notifying the site administrator. In the case of a high-traffic website, this change has the potential to propagate to other sources, as users have reason to believe the hosted copy of the document is correct. Alternatively, consider the case of a software aggregation service like CNET's Download.com: mirrored installers must match those of the original distributors to protect users from malware.

However, content providers cannot simply rely on a version hosted by a single authoritative source, as a compromise of this central store is no different than the aforementioned scenario. Instead, distributing the task of document verification across a network of providers will allow hosts to verify content without the need for a centralized data store; rather than comparing a document to a single correct copy, content providers can compare their copy to those of multiple peers. In the event any node in the network is found to have a document mismatch, the network can be notified so the error can be corrected and the document can be rolled back to its previous state.

Project Orwell seeks to address the problem of document integrity assurance. After registering with a central server and obtaining an API key, Orwell users download a client package that functions as a content management application. Using the client application, users can upload content for other clients to host as well as host content uploaded by other users. Because the content is duplicated across many hosts, documents will be preserved even if a single server is compromised or ceases to host the content. Furthermore, documents' integrity is periodically verified across hosts in order to ensure that all copies of the document are equivalent to the originally-uploaded document. Subsequently, Project Orwell provides a mechanism for users to preserve documents while ensuring readers that the content is at all times faithful to the originally uploaded document.

For more installation, read the Orwell white paper: http://tommymacwilliam.com/orwell.pdf

## Dependencies 

* PHP 5.3+
* MySQL

## Client Installation

After registering an account, simply run the installer, as with:

    chmod +x install && ./install

You will be prompted for your API key and MySQL credentials. The installer will then download and configure all necessary files.

## Automated Verification

Verification of a random document can be perfomed from the command-line simply by executing:
    
    php /path/to/client/app/webroot/cron_dispatcher.php /documents/verify

As a result, adding this line to your crontab will enable automatic document verification.
