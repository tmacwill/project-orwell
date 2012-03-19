<h1 id="main-header">Manage Documents</h1>
<div id="document-list">
    <div id="document-search">
        <input type="search" class="search input-medium search-query" placeholder="Search documents" />
        <button class="btn sort" data-sort="document-name">Sort</button>
    </div>
    <ul class="list">
        <?php foreach ($documents as $document): ?>
            <li>
                <span class="document-name"><?= $document['Document']['name'] ?></span>
                <span class="document-url">
                    <a target="_blank" href="<?= $document['Document']['url'] ?>">
                        <?= $document['Document']['url']; ?>
                    </a>
                </span>

                <div class="hover-controls">
                    <a href="#" class="btn btn-primary">Verify now</a>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
<div id="add-document-container">
    <button id="btn-add-document" class="btn btn-primary btn-large">Add a new Document</button>
    <form id="form-add-document" method="post" action="<?= $this->webroot ?>documents/add">
        <input type="text" name="url" placeholder="Document URL (e.g., http://example.com/1984.html)" />
        <input type="text" name="name" placeholder="Document Name (e.g., Nineteen Eighty-Four)" />
        <input type="hidden" name="host_id" value="<?= $_SESSION['host']['id'] ?>" />
        <input id="btn-add-document-submit" type="submit" class="btn" value="Add" />
    </form>
</div>
