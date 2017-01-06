<?php
require_once('class.SecurePage.php');
class ThemePage extends SecurePage
{
    public $theme_root;

    function __construct($title)
    {
        parent::__construct($title, true);
        $root = $_SERVER['DOCUMENT_ROOT'];
        $script_dir = dirname(__FILE__);
        $this->theme_root = substr($script_dir, strlen($root));
        $this->add_links();
    }

    function add_links()
    {
        $dir = $this->theme_root;
        if(!FlipSession::isLoggedIn())
        {
            $this->add_link('Login', 'http://profiles.burningflipside.com/login.php?return='.$this->current_url());
        }
        else
        {
            if($this->user->isInGroupNamed('ThemeAdmins'))
            { 
                $this->add_link('Admin', $dir.'/_admin/');
            }
            $secure_menu = array(
                'Tickets'=>'/tickets/index.php',
                'View Registrations'=>'/register/view.php',
                //'Theme Camp Registration'=>$dir.'/tc_reg.php',
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
