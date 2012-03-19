<div class="container-fluid">
    <form method="post" action="<?= Router::url('/') ?>hosts/login">
        <input type="email" name="email" placeholder="Email address" /><br />
        <input type="password" name="password" placeholder="Password" /><br />
        <input type="submit" value="Log in" />
    </form>
</div>
