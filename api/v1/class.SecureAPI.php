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
        $params = $request->getQueryParams();
        if(!isset($params['username']) || !isset($params['password']))
        {
            return $request->withStatus(400);
        }
        $auth = AuthProvider::getInstance();
        $res = $auth->login($params['username'], $params['password']);
        if($res === false)
        {
            return $request->withStatus(403);
        }
        else
        {
            return $request->withJson($res);
        }
    }

    public function logout($request, $response, $args)
    {
        FlipSession::end();
        return $request->withJson(true);
    }
}
/* vim: set tabstop=4 shiftwidth=4 expandtab: */
