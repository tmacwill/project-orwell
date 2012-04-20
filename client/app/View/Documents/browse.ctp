<script>
    $(function() {
        $('a[rel="tooltip"]').tooltip();
    });
</script>

<h1 id="main-header">All Documents</h1>
<div id="document-list">
    <div id="document-search">
        <input type="search" class="search input-medium search-query" placeholder="Search" />
        <button class="btn sort" data-sort="document-name">Sort</button>
    </div>

    <ul class="list">
        <?php foreach ($documents as $document): ?>
            <li>
                <span class="document-name">
                    <?php $count = count($document['HostDocuments']); ?>
                    <?= $document['Document']['name'] ?>&nbsp;
                    <a href="#" rel="tooltip" 
                        title="<?= $count ?> <?= ($count != 1) ? 'people are' : 'person is' ?> hosting this document">
                        <span class="badge badge-info"><?= $count ?></span>
                    </a>
                </span>

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
