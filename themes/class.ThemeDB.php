<?php
require_once("/var/www/secure_settings/class.FlipsideSettings.php");
class ThemeDB
{
    private $dataSet;
    private $tables = array();
    
    function __construct()
    {
        $this->dataSet = \DataSetFactory::getDataSetByName('registration');
    }

    function getCurrentYear()
    {
        if(!isset($this->tables['vars']))
        {
            $this->tables['vars'] = $this->dataSet['vars'];
        }
        $data = $this->tables['vars']->read(new \Data\Filter('name eq year'));
        if(empty($data))
        {
            return false;
        }
        return $data[0]['value'];
    }

    function getAllFromCollection($collection, $year = false, $uid = false, $fields = false)
    {
        if($year === false)
        {
            $year = $this->getCurrentYear();
        }
        $table = $this->dataSet[$collection];
        $filter = false;
        if($year !== '*' && $uid !== false)
        {
            $filter = new \Data\Filter("year eq $year and registrars eq $uid");
        }
        else if($year !== '*')
        {
            $filter = new \Data\Filter("year eq $year");
        }
        else if($uid !== false)
        {
            $filter = new \Data\Filter("registrars eq $uid");
        }
        $data = $table->read($filter, $fields);
        return $data;
    }

    function searchFromCollection($collection, $criteria, $fields = false)
    {
        $col = $this->db->selectCollection($collection);
        foreach($criteria as $key=>$value)
        {
            if($value[0] === '/')
            {
                $criteria[$key] = array('$regex'=>new MongoRegex("$value"));
            }
        }
        $cursor = $col->find($criteria);
        if($fields !== false)
        {
            $cursor->fields($fields);
        }
        $ret    = array();
        foreach($cursor as $doc)
        {
            array_push($ret, $doc);
        }
        return $ret;
    }

    function getObjectFromCollectionByID($collection, $id)
    {
        $table = $this->dataSet[$collection];
        $data = $table->read(new \Data\Filter("_id eq $id"));
        if(empty($data))
        {
            return false;
        }
        return $data[0];
    }

    function addObjectToCollection($collection, $obj)
    {
         unset($obj['_id']);
         $table = $this->dataSet[$collection];
         $res = $table->create($obj);
         if($res !== false)
         {
             return $res;
         }
         return false;
    }

    function updateObjectInCollection($collection, $obj)
    {
        $id = $obj['_id'];
        unset($obj['_id']);
        $table = $this->dataSet[$collection];
        $res = $table->update(new \Data\Filter("_id eq $id"), $obj);
        if($res !== false)
        {
            return true;
        }
        return false;
    }

    function deleteObjectFromCollection($collection, $obj)
    {
        $id = $obj['_id'];
        unset($obj['_id']);
        $table = $this->dataSet[$collection];
        return $table->delete(new \Data\Filter("_id eq $id"));
    }

    function getAllThemes($year = false)
    {
        return $this->getAllFromCollection('themes', $year);
    }

    function getAllThemesForUser($uid, $year = false)
    {
        return $this->getAllFromCollection('themes', $year, $uid);
    }

    function getThemeByID($id)
    {
        return $this->getObjectFromCollectionByID('themes', $id);
    }

    function deleteTheme($theme)
    {
        return $this->db->themes->remove(array('_id'=>new MongoId($theme['_id'])));
    }

    function addTheme($theme)
    {
         unset($theme['_id']);
         $res = $this->db->themes->insert($theme);
         if($res['ok'] === true)
         {
             return $theme['_id'];
         }
         return false;
    }

    function updateTheme($theme)
    {
        $id = new MongoId($theme['_id']);
        unset($theme['_id']);
        $res = $this->db->themes->update(array('_id' => $id), $theme);
        if($res['ok'] === true)
        {
            return true;
        }
        return false;
    } 
}
// vim: set tabstop=4 shiftwidth=4 expandtab:
?>
