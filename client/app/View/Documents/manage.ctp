<h1 id="main-header">Documents hosted on your Orwell</h1>
<div id="document-list">
    <div id="document-search">
        <input type="search" class="search input-medium search-query" placeholder="Search" />
        <button class="btn sort" data-sort="document-name">Sort</button>
    </div>
    <ul class="list">
        <?php foreach ($documents as $document): ?>
            <li class="document-row" data-document-id="<?= $document['Document']['id'] ?>">
                <span class="document-name"><?= $document['Document']['name'] ?></span>

                <div class="hover-controls">
                    <a href="#" class="btn-verify btn btn-primary">
                        <i class="icon-ok icon-white"></i> Verify
                    </a>
                    <a href="<?= $this->webroot ?>documents/view/<?= $document['Document']['id'] ?>" 
                            target="_blank" class="btn-view btn">
                        <i class="icon-share-alt"></i> View
                    </a>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
</div>

<div id="add-document-container">
    <?php if ($stats['uploaded'] <= 5 || $stats['hosted'] >= $stats['uploaded']): ?>
        <button id="btn-add-document" class="btn btn-primary btn-large">
            <i class="icon-upload icon-white"></i> Add a new Document
        </button>
        <form id="form-add-document" method="post" action="<?= $this->webroot ?>documents/add" enctype="multipart/form-data">
            <input type="text" name="name" placeholder="Document Name (e.g., Nineteen Eighty-Four)" />
            <input type="file" name="file" /><br />
            <input id="btn-add-document-submit" type="submit" class="btn btn-large btn-primary" value="Add" />
        </form>
    <?php else: ?>
        <h2>You need to host a document before you can upload more documents!</h2>
    <?php endif; ?>
</div>
