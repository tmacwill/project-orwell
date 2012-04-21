<!doctype html>
<html>
<head>
    <?php echo $this->Html->charset(); ?>
    <title>
        <?php echo $title_for_layout; ?>
    </title>

    <link href='http://fonts.googleapis.com/css?family=Sorts+Mill+Goudy' rel='stylesheet' type='text/css'>

    <?php

        // load app-wide css
        $this->Html->css('bootstrap.min.css', null, array('inline' => false));
        $this->Html->css('global.css', null, array('inline' => false));

        // load app-wide javascript
        $this->Html->script('jquery-1.7.1.min.js', array('inline' => false));
        $this->Html->script('bootstrap.min.js', array('inline' => false));
        $this->Html->script('list.min.js', array('inline' => false));

        // load controller-level assets
        if (is_file(WWW_ROOT . DS . 'css' . DS . $this->params['controller'] . '.css'))
            echo $this->Html->css($this->params['controller'], null, array('inline' => false)); 
        if (is_file(WWW_ROOT . DS . 'js' . DS . $this->params['controller'] . '.js'))
            echo $this->Html->script($this->params['controller'], array('inline' => false)); 

        // load action-level assets
        if (is_file(WWW_ROOT . DS . 'css' . DS . $this->params['controller'] . DS . $this->params['action'] . '.css'))
            echo $this->Html->css($this->params['controller'] . DS . $this->params['action'], null, array('inline' => false)); 
        if (is_file(WWW_ROOT . DS . 'js' . DS . $this->params['controller'] . DS . $this->params['action'] . '.js'))
            echo $this->Html->script($this->params['controller'] . DS . $this->params['action'], array('inline' => false)); 

        echo $this->fetch('meta');
        echo $this->fetch('css');
        echo $this->fetch('script');
    ?>

</head>
<body>
    <div id="nav-container">
        <div id="nav">
            <a id="nav-logo" href="<?= $this->webroot ?>">Project Orwell</a>
            <?php if (isset($_SESSION['user'])): ?>
                <div class="nav-links">
                    <a href="<?= $this->webroot ?>documents/manage">My Documents</a>
                    <a href="<?= $this->webroot ?>documents/browse">Browse Documents</a>
                </div>
                <a id="nav-login" href="<?= $this->webroot ?>users/logout">Logout</a>
            <?php else: ?>
                <a id="nav-login" href="<?= $this->webroot ?>users/login">Login</a>
            <?php endif; ?>
        </div>
    </div>

    <?php echo $this->fetch('content'); ?>
</body>
</html>
