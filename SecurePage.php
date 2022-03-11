<?php
namespace Flipside\Secure;
require_once('Autoload.php');

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
                $secure_menu = array_merge($secure_menu, $ret);
            }
        }
        $this->addLink('Secure', $this->secure_root, $secure_menu);
    }
}

class SecurePage extends \Flipside\Http\WebPage
{
    use SecureWebPage;

    public $secure_root;
    protected $plugins;
    protected $plugin_count;

    function __construct($title)
    {
        parent::__construct($title, true);
        $this->secure_root = $this->getSecureRoot();
        $this->plugins = $this->loadAndGetPlugins();
        $this->plugin_count = count($this->plugins);
        $this->add_links();
        $this->content['root'] = $this->secure_root;
        $this->addTemplateDir(dirname(__FILE__).'/templates', 'Secure');
        $this->setTemplateName('@Secure/main.html');
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
                $entry_points .= '<li><a href="'.$ret['link'].'">'.$ret['name'].'</a></li>';
            }
        }
        return $entry_points;
    }
}
?>
