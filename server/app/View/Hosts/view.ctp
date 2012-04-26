<h1 id="main-header">Your Orwell Host</h1>
<div class="container" style="margin-top: 40px">
    <h2>Your URL: <a target="_blank" href="http://<?= $_SESSION['host']['url']; ?>">http://<?= $_SESSION['host']['url'] ?></a></h2>
    <h2>Your Key: <?= $_SESSION['host']['key'] ?></h2>

    <div id="installation-steps" class="row">
        <div class="span4">
            <h2>Step 1</h2>
            <a href="/install">Download</a> and run the installer.
        </div>
        <div class="span4">
            <h2>Step 2</h2>
            <a href="http://<?= $_SESSION['host']['url']; ?>">Head to your Orwell</a> and create a new user.
        </div>
        <div class="span4">
            <h2>Step 3</h2>
            <a href="http://<?= $_SESSION['host']['url']; ?>/documents/browse">Browse documents to host</a> or 
            <a href="http://<?= $_SESSION['host']['url']; ?>/documents/manage">upload your own</a>.
        </div>
    </div>
</div>
