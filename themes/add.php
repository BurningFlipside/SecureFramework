<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once('class.ThemePage.php');
$page = new ThemePage('Theme');

$page->add_js_from_src('js/add.js');
$page->body .= '
    <div id="content">
        <fieldset id="request_set">
            <legend>Theme Submission</legend>
            <div class="form-group">
                <label for="name" class="col-sm-2 control-label">Theme:</label>
                <div class="col-sm-10">
                    <input class="form-control" id="name" name="name" type="text" required/>
                </div>
            </div>
            <div class="clearfix visible-sm visible-md visible-lg"></div>
            <div class="alert alert-info" role="alert"><b>NOTE:</b> Theme presentations will be limited to 2 minutes!</div>
            <div class="clearfix visible-sm visible-md visible-lg"></div>
            <div class="form-group">
                <label for="presenting" class="col-sm-2 control-label">Presenting:</label>
                <div class="col-sm-10">
                    <input class="form-control" id="presenting" name="presenting" type="checkbox" data-toggle="tooltip" data-placement="top" title="I plan to present this theme at Townhall"/>
                </div>
            </div>
            <button type="submit" name="submit" class="btn btn-primary">Submit Request</button>
        </fieldset>
    </div>
';

$page->printPage();
?>
