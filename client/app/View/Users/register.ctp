<h1 id="main-header">Register a user for your Orwell</h1>
<form id="form-register" method="post" action="<?= $this->webroot ?>users/register">
    <input type="email" name="email" placeholder="Email" /><br />
    <input type="password" name="password" placeholder="Password" /><br />
    <input type="password" name="confirm" placeholder="Confirm Password" /><br />
    <input type="submit" class="btn btn-primary" value="Register" />
</form>
