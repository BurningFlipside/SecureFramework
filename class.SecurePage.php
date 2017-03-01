<?php
require_once('class.FlipPage.php');
require_once('class.FlipSession.php');
require_once('class.SecurePlugin.php');

trait SecureWebPage
{
    protected function getSecureRoot()
    {
        $root = $_SERVER['DOCUMENT_ROOT'];
        $script_dir = dirname(__FILE__);
        $ret = substr($script_dir, strlen($root));

        if($ret === false || strlen($ret) === 0)
        {
            return '/';
        }
        else if($ret[strlen($ret)-1] !== '/')
        {
            $ret .= '/';
        }
        return $ret;
    }

    protected function addSecureCss()
    {
        $this->addCSSByURI($this->secure_root.'css/secure.css');
    }

    protected function addSecureScript()
    {
        $this->addWellKnownJS(JS_LOGIN);
    }

    protected function loadAndGetPlugins()
    {
        $script_dir = dirname(__FILE__);
        $plugin_files = glob($script_dir.'/*/plugin.php');
        $count = count($plugin_files);
        for($i = 0; $i < $count; $i++)
        {
            include($plugin_files[$i]);
        }
        $ret = array();
        foreach(get_declared_classes() as $class)
        {
            if(is_subclass_of($class, 'SecurePlugin'))
            {
                $ret[] = new $class();
            }
        }
        return $ret;
    }

    public function addPluginLinks($count, $plugins)
    {
        $secure_menu = array();
        for($i = 0; $i < $count; $i++)
        {
            $ret = $plugins[$i]->get_secure_menu_entries($this, $this->user);
            if($ret !== false)
            {
                $ret["<hr id='hr_$i'/>"] = false;
                $secure_menu = array_merge($secure_menu, $ret);
            }
        }
        array_pop($secure_menu);
        $this->addLink('Secure', $this->secureUrl, $secure_menu);
    }
}

class SecurePage extends FlipPage
{
    use SecureWebPage;

    public $secure_root;
    protected $plugins;
    protected $plugin_count;

    function __construct($title)
    {
        parent::__construct($title, true);
        $this->secure_root = $this->getSecureRoot();
        $this->addSecureCss();
        $this->addSecureScript();
        $this->add_login_form();
        $this->body_tags='data-login-url="'.$this->secure_root.'api/v1/login"';
        $this->plugins = $this->loadAndGetPlugins();
        $this->plugin_count = count($this->plugins);
        $this->add_links();
    }

    function add_links()
    {
        if($this->user !== false)
        {
            $this->addPluginLinks($this->plugin_count, $this->plugins);
        }
    }

    function get_secure_child_entry_points()
    {
        $entry_points = '';
        for($i = 0; $i < $this->plugin_count; $i++)
        {
            $ret = $this->plugins[$i]->get_plugin_entry_point();
            if($ret !== false)
            {
                $entry_points .= '<li>'.$this->createLink($ret['name'],$ret['link']).'</li>';
            }
        }
        return $entry_points;
    }
}
?>
