<?php
require_once('class.FlipPage.php');
require_once('class.FlipSession.php');
require_once('class.SecurePlugin.php');
class SecurePage extends FlipPage
{
    public $secure_root;
    protected $plugins;
    protected $plugin_count;

    function __construct($title)
    {
        parent::__construct($title, true);
        $root = $_SERVER['DOCUMENT_ROOT'];
        $script_dir = dirname(__FILE__);
        $this->secure_root = substr($script_dir, strlen($root));
        $this->add_secure_css();
        $this->add_secure_script();
        $this->add_login_form();
        $this->body_tags='data-login-url="'.$this->secure_root.'/api/v1/login"';
        $plugin_files = glob('*/plugin.php');
        $count = count($plugin_files);
        for($i = 0; $i < $count; $i++)
        {
            include($plugin_files[$i]);
        }
        $this->plugins = array();
        foreach(get_declared_classes() as $class)
        {
            if(is_subclass_of($class, 'SecurePlugin'))
            {
                $this->plugins[] = new $class();
            }
        }
        $this->plugin_count = count($this->plugins);
        $this->add_links();
    }

    function add_secure_css()
    {
        $this->add_css_from_src($this->secure_root.'/css/secure.css');
    }

    function add_secure_script()
    {
        $this->add_js(JS_LOGIN);
    }

    function add_links()
    {
        if(!FlipSession::is_logged_in())
        {
            $this->add_link('Login', 'http://profiles.burningflipside.com/login.php?return='.$this->current_url());
        }
        else
        {
            $user = FlipSession::get_user();
            $secure_menu = array();
            for($i = 0; $i < $this->plugin_count; $i++)
            {
                $ret = $this->plugins[$i]->get_secure_menu_entries($this, $user);
                if($ret !== false)
                {
                    $ret["<hr id='hr_$i'/>"] = false;
                    $secure_menu = array_merge($secure_menu, $ret);
                }
            }
            array_pop($secure_menu);
            $this->add_link('Secure', 'https://secure.burningflipside.com/', $secure_menu);
            $this->add_link('Logout', 'http://profiles.burningflipside.com/logout.php');
        }
        $about_menu = array(
            'Burning Flipside'=>'http://www.burningflipside.com/about/event',
            'AAR, LLC'=>'http://www.burningflipside.com/LLC',
            'Privacy Policy'=>'http://www.burningflipside.com/about/privacy'
        );
        $this->add_link('About', 'http://www.burningflipside.com/about', $about_menu);
    }

    function get_secure_child_entry_points()
    {
        $entry_points = '';
        for($i = 0; $i < $this->plugin_count; $i++)
        {
            $ret = $this->plugins[$i]->get_plugin_entry_point();
            if($ret !== false)
            {
                $entry_points .= '<li>'.$this->create_link($ret['name'],$ret['link']).'</li>';
            }
        }
        return $entry_points;
    }
}
?>
