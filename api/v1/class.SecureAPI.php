<?php
class SecureAPI extends Flipside\Http\Rest\RestAPI
{
    public function setup($app)
    {
        $app->post('/login[/]', array($this, 'login'));
        $app->post('/logout[/]', array($this, 'logout'));
        $app->get('/me[/]', array($this, 'me'));
        $app->get('/groups[/]', array($this, 'getGroups'));
    }

    public function login($request, $response, $args)
    {
        $params = $request->getParams();
        if(!isset($params['username']) || !isset($params['password']))
        {
            return $response->withStatus(400);
        }
        $auth = \Flipside\AuthProvider::getInstance();
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
        \Flipside\FlipSession::end();
        return $response->withJson(true);
    }

    public function me($request, $response, $args)
    {
        $this->validateLoggedIn($request);
        $user = $this->user;
        return $response->withJson($user);
    }

    public function getGroups($request, $response, $args)
    {
        $this->validateLoggedIn($request);
        $user = $this->user;
        $groups = $user->getGroups();
        return $response->withJson($groups);
    }
}
/* vim: set tabstop=4 shiftwidth=4 expandtab: */
