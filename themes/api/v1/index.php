<?php
require_once('class.FlipREST.php');
require_once('class.ThemeDB.php');

if($_SERVER['REQUEST_URI'][0] == '/' && $_SERVER['REQUEST_URI'][1] == '/')
{
    $_SERVER['REQUEST_URI'] = substr($_SERVER['REQUEST_URI'], 1);
}

$app = new FlipREST();
$app->group('/themes', 'themes');

function trim_obj(&$obj)
{
    foreach($obj as $key=>$value)
    {
        if($key == '_id')
        {
            $obj['_id'] = (string)$obj['_id'];
        }
        else if(is_object($value) || is_array($value))
        {
            unset($obj[$key]);
        }
    }
}

function validate_user_is_admin($user)
{
   return $user->isInGroupNamed('ThemeAdmins');
}

function validate_user_has_access($user, $obj)
{
   if($user->isInGroupNamed('ThemeAdmins'))
   {
       return true;
   }
   return in_array($user->getUid(), $obj['registrars']);
}

function list_obj()
{
    global $app;
    if(!$app->user)
    {
        throw new Exception('Must be logged in', ACCESS_DENIED);
    }
    $params = $app->request->params();
    if(isset($params['fmt']))
    {
       unset($params['fmt']);
    }
    if(isset($params['_']))
    {
       unset($params['_']);
    }
    $logo_urls = false;
    $db = new ThemeDB();
    $fields = false;
    if(isset($params['no_logo']))
    {
        $fields = array('logo' => false);
        unset($params['no_logo']);
    }
    if(isset($params['logo_url']))
    {
        $logo_urls = true;
        unset($params['logo_url']);
    }
    if(count($params))
    {
        $objs = $db->searchFromCollection('themes', $params, $fields);
    }
    else
    {
        $objs = $db->getAllFromCollection('themes', false, false, $fields);
    }
    if(!validate_user_is_admin($app->user))
    {
        $count = count($objs);
        for($i = 0; $i < $count; $i++)
        {
            trim_obj($objs[$i]);
        }
    }
    $count = count($objs);
    for($i = 0; $i < $count; $i++)
    {
        if($logo_urls && isset($objs[$i]['logo']))
        {
            $objs[$i]['logo'] = $app->request->getUrl().$app->request->getPath().'/'.$objs[$i]['_id'].'/logo';
        }
    }
    echo json_encode($objs);
}

function obj_list_with_filter($field)
{
    global $app;
    if(!$app->user)
    {
        throw new Exception('Must be logged in', ACCESS_DENIED);
    }
    if(!validate_user_is_admin($app->user, $collection))
    {
        throw new Exception('User not admin', ACCESS_DENIED);
    }
    $db = new ThemeDB();
    $objs = $db->getAllFromCollection('themes');
    $res = array();
    $count = count($objs);
    for($i = 0; $i < $count; $i++)
    {
        if(isset($objs[$i][$field]))
        {
            array_push($res, $objs[$i][$field]);
        }
    }
    echo json_encode($res);
}

function obj_view($id, $field = FALSE)
{
    global $app;
    if(!$app->user)
    {
        throw new Exception('Must be logged in', ACCESS_DENIED);
    }
    $db = new ThemeDB();
    if($id === '*')
    {
        obj_list_with_filter($field);
        return;
    }
    $obj = $db->getObjectFromCollectionByID('themes', $id);
    if($obj === FALSE)
    {
        throw new Exception('Unable to obtain object!', INTERNAL_ERROR);
    }
    else
    {
        if($app->request->params('full') === null)
        {
            if(!validate_user_is_admin($app->user))
            {
               trim_obj($obj);
            }
            else if($field === false)
            {
                trim_obj($obj);
            }
            if($field !== FALSE)
            {
                if(!is_array($obj[$field]) && strncmp($obj[$field], 'data:', 5) === 0)
                {
                    $app->fmt = 'passthru';
                    $str = substr($obj[$field], 5);
                    $type = strtok($str, ';');
                    strtok(',');
                    $str = strtok("\0");
                    print(base64_decode($str));
                    $app->response->headers->set('Content-Type', $type);
                }
                else
                {
                    echo json_encode($obj[$field]);
                }
                return;
            }
        }
        else
        {
            if(validate_user_has_access($app->user, $obj) === FALSE)
            {
                throw new Exception('Cannot edit object that is not yours', ACCESS_DENIED);
            }
        }
        echo json_encode($obj);
    }
}

function obj_add()
{
    global $app;
    if(!$app->user)
    {
        throw new Exception('Must be logged in', ACCESS_DENIED);
    }
    $collection = 'themes';
    $db = new ThemeDB();
    $body = $app->request->getBody();
    $obj  = json_decode($body);
    $obj  = get_object_vars($obj);
    //Ensure minimum fields are set...
    if(!isset($obj['name']))
    {
        throw new Exception('Missing one or more required parameters!', INTERNAL_ERROR);
    }
    $obj['year'] = $db->getCurrentYear();
    if(!isset($obj['registrars']))
    {
        $obj['registrars'] = array();
    }
    array_push($obj['registrars'], $app->user->getUid());
    if(isset($obj['_id']) && strlen($obj['_id']) > 0)
    {
        $app->redirect('themes/'.$obj['_id'], 307);
        return;
    }
    else
    {
        $res = $db->addObjectToCollection('themes', $obj);
    }
    if($res === FALSE)
    {
        throw new Exception('Unable to add theme!', INTERNAL_ERROR);
    }
    else
    {
        echo json_encode(array('_id'=>(string)$res, 'url'=>$app->request->getUrl().$app->request->getPath().'/'.(string)$res));
    }
}

function obj_edit($id)
{
    global $app;
    if(!$app->user)
    {
        throw new Exception('Must be logged in', ACCESS_DENIED);
    }
    $db = new ThemeDB();
    $old_obj = $db->getObjectFromCollectionByID('themes', $id);
    if(validate_user_has_access($app->user, $old_obj) === FALSE)
    {
        throw new Exception('Cannot edit object that is not yours', ACCESS_DENIED);
    }
    $obj = $app->request->params();
    if($obj === null || count($obj) === 0)
    {
        $body = $app->request->getBody();
        $obj  = json_decode($body);
        $obj  = get_object_vars($obj);
    }
    //Ensure minimum fields are set...
    if(!isset($obj['name']))
    {
        throw new Exception('Missing one or more required parameters!', INTERNAL_ERROR);
    }
    $obj['year'] = $db->getCurrentYear();
    if(!isset($obj['registrars']))
    {
        $obj['registrars'] = array();
    }
    $obj['registrars'] = array_merge($obj['registrars'], $old_obj['registrars']);
    if(validate_user_is_admin($app->user) === FALSE)
    {
        $uid = $app->user->getUid();
        if(!in_array($uid, $obj['registrars']))
        {
            array_push($obj['registrars'], $app->user->getUid());
        }
    }
    if(!isset($obj['_id']))
    {
        $obj['_id'] = $id;
    }
    $res = $db->updateObjectInCollection('themes', $obj);
    if($res === FALSE)
    {
        throw new Exception('Unable to update object!', INTERNAL_ERROR);
    }
    else
    {
        echo json_encode(array('update'=>TRUE));
    }
}

function obj_delete($id)
{
    global $app;
    if(!$app->user)
    {
        throw new Exception('Must be logged in', ACCESS_DENIED);
    }
    $db = new ThemeDB();
    $old_obj = $db->getObjectFromCollectionByID('themes', $id);
    if(validate_user_has_access($app->user, $old_obj) === FALSE)
    {
        throw new Exception('Cannot delete object that is not yours', ACCESS_DENIED);
    }
    $res = $db->deleteObjectFromCollection('themes', $old_obj);
    if($res === false)
    {
        throw new Exception('Unable to delete object!', INTERNAL_ERROR);
    }
    else
    {
        echo json_encode(array('delete'=>TRUE));
    }
}

function themes()
{
    global $app;
    $app->get('', 'list_obj');
    $app->get('/:id(/:field)', 'obj_view');
    $app->post('', 'obj_add');
    $app->patch('/:id', 'obj_edit');
    $app->delete('/:id', 'obj_delete');
}

$app->run();
?>
