<h1 id="main-header">Compare Documents</h1>
<div id="diff">
    <?= $diff ?>
</div>
<div class="container" style="margin-top: 40px">
    <div class="row" style="text-align: center">
        <a href="<?= $this->webroot ?>hosts/notify/<?= $document['Document']['id'] ?>?compare=<?= $compare ?>" 
            class="btn btn-large btn-success">My copy is correct.</a>
        <a href="#" class="btn btn-large btn-danger">The other copy is correct.</a>
    </div>
</div>
