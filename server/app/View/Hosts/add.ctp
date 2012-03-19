<div class="container-fluid">
    <h1 id="main-header">Join the Revolution.</h1>

    <form id="form-register" method="post" action="<?= Router::url('/') ?>hosts/add">
        <input type="text" name="name" placeholder="What's your organization's name?" /><br />
        <input type="email" name="email" placeholder="And your email address?" /><br />
        <input type="password" name="password" placeholder="Now, pick a super-secure password." /><br />
        <input type="text" name="url" placeholder="Where's your Orwell hosted? (e.g., http://example.com/orwell)" /><br />
        <input type="submit" class="btn btn-primary btn-register" value="Sign Up." />
    </form>
</div>
