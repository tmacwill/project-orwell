<?php

mysql_connect('localhost', 'root', 'crimson');

mysql_select_db('orwell');
mysql_query('truncate documents');
mysql_query('truncate host_documents');

mysql_select_db('orwell_client');
mysql_query('truncate documents');
mysql_query('truncate hosts');

mysql_select_db('orwell_client2');
mysql_query('truncate documents');
mysql_query('truncate hosts');

shell_exec('rm -rf /var/www/html/server/app/files');
shell_exec('rm -rf /var/www/html/client/app/files');
shell_exec('rm -rf /var/www/html/client2/app/files');

?>
