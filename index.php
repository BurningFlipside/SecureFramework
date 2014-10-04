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
        <li><a href="/tickets/index.php">Ticket Registration/Transfer</a></li>
        <li><a href="/theme_camp/registration.php">Theme Camp Registration</a></li>
        <li><a href="/art/registration.php">Art Project Registration</a></li>
        <li><a href="/event/index.php">Event Registration</a></li>
        <li><a href="/fvs/index.php">Flipside Volunteer System</a></li>
    </ul>
</div>';

$page->print_page();
?>
