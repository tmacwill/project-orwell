<h1 id="main-header">Login</h1>
<form id="form-login" method="post" 
    action='<?= $this->webroot ?>hosts/login?<?php if (isset($_GET["return"])) echo "return={$_GET["return"]}"; ?>'>
    <input type="email" name="email" placeholder="Email" /><br />
    <input type="password" name="password" placeholder="Password" /><br />
    <input type="submit" class="btn btn-primary" value="Login" />
</form>
