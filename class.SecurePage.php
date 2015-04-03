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
        $this->add_secure_css($this->secure_root);
        $this->add_secure_script($this->secure_root);
        $this->add_sites();
        $this->add_links();
        $this->add_login_form();
        $this->body_tags='data-login-url="'.$this->secure_root.'/api/v1/login"';
    }

    function add_secure_css($dir)
    {
        $this->add_css_from_src($dir.'/css/secure.css');
        $this->add_css(CSS_BOOTSTRAP_FH);
        $this->add_css(CSS_BOOTSTRAP_SW);
    }

    function add_secure_script($dir)
    {
        $this->add_js(JQUERY_VALIDATE);
        $this->add_js(JS_BOOTSTRAP_FH);
        $this->add_js(JS_BOOTSTRAP_SW);
        $this->add_js(JS_LOGIN);
    }

    function add_sites()
    {
        $this->add_site('Profiles', 'http://profiles.burningflipside.com');
        $this->add_site('WWW', 'http://www.burningflipside.com');
        $this->add_site('Pyropedia', 'http://wiki.burningflipside.com');
        $this->add_site('Secure', 'https://secure.burningflipside.com');
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

    function current_url()
    {
        return 'http'.(isset($_SERVER['HTTPS'])?'s':'').'://'."{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
    }

    function add_login_form()
    {
        $this->body .= '<div class="modal fade" tabindex="-1" role="dialog" id="login-dialog" title="Login" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal">
                                            <span aria-hidden="true">&times;</span>
                                            <span class="sr-only">Close</span>
                                        </button>
                                        <h4 class="modal-title">Login</h4>
                                    </div>
                                    <div class="modal-body">
                                        <form id="login_dialog_form" role="form">
                                            <input class="form-control" type="text" name="username" placeholder="Username or Email" required autofocus/>
                                            <input class="form-control" type="password" name="password" placeholder="Password" required/>
                                            <input type="hidden" name="return" value="'.$this->current_url().'"/>
                                            <button class="btn btn-lg btn-primary btn-block" type="submit">Login</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>';
    }
}
?>
