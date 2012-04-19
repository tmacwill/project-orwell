<h1 id="main-header">My Documents</h1>
<div id="document-list">
    <div id="document-search">
        <input type="search" class="search input-medium search-query" placeholder="Search" />
        <button class="btn sort" data-sort="document-name">Sort</button>
    </div>
    <ul class="list">
        <?php foreach ($documents as $document): ?>
            <li data-document-id="<?= $document['Document']['id'] ?>">
                <span class="document-name"><?= $document['Document']['name'] ?></span>

                <div class="hover-controls">
                    <button class="hover-verify btn btn-primary">
                        <i class="icon-ok icon-white"></i> Verify
                    </button>
                    <button class="hover-view btn">
                        <i class="icon-share-alt"></i> View
                    </button>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
</div>

<div id="add-document-container">
    <button id="btn-add-document" class="btn btn-primary btn-large">
        <i class="icon-upload icon-white"></i> Add a new Document
    </button>
    <form id="form-add-document" method="post" action="<?= $this->webroot ?>documents/add" enctype="multipart/form-data">
        <input type="text" name="name" placeholder="Document Name (e.g., Nineteen Eighty-Four)" />
        <input type="file" name="file" /><br />
        <input id="btn-add-document-submit" type="submit" class="btn btn-large btn-primary" value="Add" />
    </form>
</div>
