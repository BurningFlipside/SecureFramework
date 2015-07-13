<?php
require_once('class.FlipPage.php');
require_once('class.FlipSession.php');
class SecurePage extends FlipPage
{
    public $secure_root;

    function __construct($title)
    {
        parent::__construct($title, true);
        $root = $_SERVER['DOCUMENT_ROOT'];
        $script_dir = dirname(__FILE__);
        $this->secure_root = substr($script_dir, strlen($root));
        $this->add_secure_css();
        $this->add_secure_script();
        $this->add_links();
        $this->add_login_form();
        $this->body_tags='data-login-url="'.$this->secure_root.'/api/v1/login"';
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
            $secure_menu = array(
                'Tickets'=>'/tickets/index.php',
                'View Registrations'=>'/register/view.php',
                'Theme Camp Registration'=>'/register/tc_reg.php',
                'Art Project Registration'=>'/register/art_reg.php',
                'Art Car Registration'=>'/register/artCar_reg.php',
                'Event Registration'=>'/register/event_reg.php'
            );
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
}
?>
