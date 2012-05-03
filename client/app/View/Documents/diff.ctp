<h1 id="main-header">Compare Documents</h1>
<?php if ($diff): ?>
    <div id="diff">
        <?= $diff ?>
    </div>
<?php else: ?>
<div class="container" style="margin-top: 40px; text-align: center">
    <a href="<?= $this->webroot ?>documents/view/<?= $document['Document']['id'] ?>" target="_blank" class="btn btn-large">
        My Copy
    </a>
    <a href="http://<?= $compare ?>/documents/view/<?= $document['Document']['id'] ?>"
        target="_blank" class="btn btn-large">
        Their Copy
    </a>
</div>
<?php endif; ?>
<div class="container" style="margin-top: 40px">
    <div class="row" style="text-align: center">
        <a href="<?= $this->webroot ?>hosts/notify/<?= $document['Document']['id'] ?>?compare=<?= $compare ?>" 
            class="btn btn-large btn-success">
            My copy is correct.
        </a>
        <a href="<?= $this->webroot ?>documents/repair/<?= $document['Document']['id']?>?client=<?= $compare ?>"
            class="btn btn-large btn-danger">
            Their copy is correct.
        </a>
    </div>
</div>
