<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once('class.ThemePage.php');
require_once('class.ThemeDB.php');
$page = new ThemePage('Burning Flipside - Registration');

$page->addWellKnownJS(JS_DATATABLE, false);
$page->addWellKnownCSS(CSS_DATATABLE);
$page->add_js_from_src('js/view.js');

if(!FlipSession::isLoggedIn())
{
$page->body .= '
    <div id="content">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">You must <a href="https://profiles.burningflipside.com/login.php?return='.$page->current_url().'">log in <span class="glyphicon glyphicon-log-in"></span></a> to access the Burning Flipside Registration system!</h1>
            </div>
        </div>
    </div>
';
}
else
{
$page->body .= '
<div id="content">
        <div class="panel panel-default">
            <div class="panel-heading" role="tab" id="themeHeader">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#tc" aria-expanded="true" aria-controls="tc">Themes</a>
                </h4>
            </div>
            <div id="tc" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="themeHeader">
                <div class="panel-body">
                    <table class="table" id="themeTable">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Presenting</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </idv>
        </div>
</div>';
}

$page->print_page();
?>
