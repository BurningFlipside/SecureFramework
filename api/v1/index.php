<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once('class.FlipREST.php');
require_once('class.AuthProvider.php');

if($_SERVER['REQUEST_URI'][0] == '/' && $_SERVER['REQUEST_URI'][1] == '/')
{
    $_SERVER['REQUEST_URI'] = substr($_SERVER['REQUEST_URI'], 1);
}

$app = new FlipREST();
$app->post('/login', 'login');

function login()
{
    global $app;
    $auth = AuthProvider::getInstance();
    $res = $auth->login($app->request->params('username'), $app->request->params('password'));
    if($res === false)
    {
        $app->response->setStatus(403);
    }
    else
    {
        echo json_encode($res);
    }
}

$app->run();
/* vim: set tabstop=4 shiftwidth=4 expandtab: */
?>
