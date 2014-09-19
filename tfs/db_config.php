<?php
$db_host = "localhost";
$db_user = "root";
$db_pw = '';
$db_name = 'sandbox';

$db= new PDO("mysql:dbname=$db_name;host=$db_host;charset=utf8", $db_user, $db_pw);

$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// have this here until I set up user
$user_id = 1;
