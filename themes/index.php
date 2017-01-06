<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once('class.ThemePage.php');
require_once('class.ThemeDB.php');
$page = new ThemePage('Burning Flipside - Theme');

$db = new ThemeDB();
$themes = array();

$page->body .= '
<div id="content">
    <h1>Welcome to the Burning Flipside Theme System</h1>
    <p></p>
    <h1>What would you like to do?</h1>
    <ul>
        <li><a href="view.php">View Existing Themes</a></li>
        <li><a href="add.php">Add a new theme</a></li>
    </ul>
</div>';

$page->print_page();
?>
