<?php

/*
 * this the basic template for db.php and db_wordpress.php
 */
$dbh = mysql_connect('localhost','username','password');
if (!$dbh) {
  die('Could not connect: ' . mysql_error());
}
mysql_select_db( 'database_name' );

