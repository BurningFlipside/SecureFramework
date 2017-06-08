<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require('Autoload.php');
require('class.AreasAPI.php');
require('class.GroupsAPI.php');
require('class.LeadsAPI.php');
require('class.PendingUserAPI.php');
require('class.SessionsAPI.php');
require('class.UsersAPI.php');
require('class.ProfilesAPI.php');

$site = new \Http\WebSite();
$site->registerAPI('/', new SecureAPI());
$site->run();
/* vim: set tabstop=4 shiftwidth=4 expandtab: */
