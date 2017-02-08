<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once('class.SecurePage.php');
$page = new SecurePage('Burning Flipside - Secure Sites');

$page->body .= '
<div id="content">
    <h1>Welcome to the Burning Flipside Secure System</h1>
    <p>This system allows you to register for tickets, transfer tickets, register your theme camp or art project, etc..</p>
    <h1>What would you like to do?</h1>
    <ul>'.$page->get_secure_child_entry_points().'</ul>
</div>';

$page->printPage();
?>
