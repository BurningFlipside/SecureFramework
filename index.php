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
    <ul>
        <li><a href="'.$page->secure_root.'/tickets/index.php">Ticket Registration/Transfer</a></li>
        <li><a href="'.$page->secure_root.'/register/index.php">Theme Camp, Art Project, Art Car, and Event Registration</a></li>
        <li><a href="'.$page->secure_root.'/register/view.php">View Existing Registrations</a></li>
        <li><a href="'.$page->secure_root.'/fvs/index.php">Flipside Volunteer System</a></li>
    </ul>
</div>';

$page->print_page();
?>
