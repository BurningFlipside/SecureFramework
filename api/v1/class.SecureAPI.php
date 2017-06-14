<?php
class SecureAPI extends Http\Rest\RestAPI
{
    public function setup($app)
    {
        $app->post('/login[/]', array($this, 'login'));
        $app->post('/logout[/]', array($this, 'logout'));
    }

    public function login($request, $response, $args)
    {
        $params = $request->getParams();
        if(!isset($params['username']) || !isset($params['password']))
        {
            return $response->withStatus(400);
        }
        $auth = AuthProvider::getInstance();
        $res = $auth->login($params['username'], $params['password']);
        if($res === false)
        {
            return $response->withStatus(403);
        }
        else
        {
            return $response->withJson($res);
        }
    }

    public function logout($request, $response, $args)
    {
        FlipSession::end();
        return $response->withJson(true);
    }
}
/* vim: set tabstop=4 shiftwidth=4 expandtab: */
