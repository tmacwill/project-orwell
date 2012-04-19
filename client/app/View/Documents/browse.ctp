<h1 id="main-header">All Documents</h1>
<div id="document-list">
    <div id="document-search">
        <input type="search" class="search input-medium search-query" placeholder="Search" />
        <button class="btn sort" data-sort="document-name">Sort</button>
    </div>

    <ul class="list">
        <?php foreach ($documents as $document): ?>
            <li>
                <span class="document-name"><?= $document['Document']['name'] ?></span>

                <div class="hover-controls">
                    <a href="<?= $this->webroot ?>documents/download/<?= $document['Document']['id'] ?>"
                            class="btn btn-primary">
                        <i class="icon-download icon-white"></i> Host
                    </a>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
