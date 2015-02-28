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
        $this->add_css($this->secure_root);
        $this->add_script($this->secure_root);
        $this->add_sites();
        $this->add_links();
        $this->add_login_form();
    }

    function add_css($dir)
    {
        $this->add_css_from_src('//ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/themes/smoothness/jquery-ui.min.css');
        $this->add_css_from_src('//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css');
        $this->add_css_from_src('//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap-theme.min.css');
        $this->add_css_from_src($dir.'/css/secure.css');
        $this->add_css_from_src($dir.'/css/bootstrap-formhelpers.min.css');
        $this->add_css_from_src($dir.'/css/bootstrap-switch.min.css');

        $meta_tag = $this->create_open_tag('meta', array('name'=>'viewport', 'content'=>'width=device-width, initial-scale=1'), true);
        $this->add_head_tag($meta_tag);
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

    function add_script($dir)
    {
        $this->add_js_from_src($dir.'/js/jquery.validate.min.js');
        $this->add_js_from_src($dir.'/js/bootstrap-formhelpers.min.js');
        $this->add_js_from_src($dir.'/js/bootstrap-switch.min.js');
        $this->add_js_from_src($dir.'/js/login.min.js');
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
