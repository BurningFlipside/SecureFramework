<?php
require_once('class.FlipPage.php');
require_once('class.FlipSession.php');
class SecurePage extends FlipPage
{
    function __construct($title)
    {
        parent::__construct($title, true);
        $this->add_css();
        $this->add_script();
        $this->add_sites();
        $this->add_links();
        $this->add_login_form();
    }

    function add_css()
    {
        $css_tag = $this->create_open_tag('link', array('rel'=>'stylesheet', 'href'=>'/css/secure.css', 'type'=>'text/css'), true);
        $this->add_head_tag($css_tag);

        $css_tag = $this->create_open_tag('link', array('rel'=>'stylesheet', 'href'=>'/css/jquery-ui.css', 'type'=>'text/css'), true);
        $this->add_head_tag($css_tag);

        $css_tag = $this->create_open_tag('link', array('rel'=>'stylesheet', 'href'=>'/css/bootstrap.min.css', 'type'=>'text/css'), true);
        $this->add_head_tag($css_tag);

        $css_tag = $this->create_open_tag('link', array('rel'=>'stylesheet', 'href'=>'/css/bootstrap-theme.min.css', 'type'=>'text/css'), true);
        $this->add_head_tag($css_tag);

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
            $this->add_link('Login', 'http://profiles.burningflipside.com/login.php');
        }
        else
        {
            $secure_menu = array(
                'Ticket Registration'=>'/tickets/index.php',
                'Ticket Transfer'=>'/tickets/transfer.php',
                'Theme Camp Registration'=>'/theme_camp/registration.php',
                'Art Project Registration'=>'/art/registration.php',
                'Event Registration'=>'/event/index.php'
            );
            $this->add_link('Secure', 'https://secure.burningflipside.com/', $secure_menu);
            $this->add_link('Logout', 'http://profiles.burningflipside.com/logout.php');
        }
        $about_menu = array(
            'Burning Flipside'=>'http://www.burningflipside.com/about/event',
            'AAR, LLC'=>'http://www.burningflipside.com/about/aar',
            'Privacy Policy'=>'http://www.burningflipside.com/about/privacy'
        );
        $this->add_link('About', 'http://www.burningflipside.com/about', $about_menu);
    }

    function add_script()
    {
        $script_start_tag = $this->create_open_tag('script', array('src'=>'/js/jquery.validate.js'));
        $script_close_tag = $this->create_close_tag('script');
        $this->add_head_tag($script_start_tag.$script_close_tag);

        $script_start_tag = $this->create_open_tag('script', array('src'=>'/js/bootstrap.min.js'));
        $this->add_head_tag($script_start_tag.$script_close_tag);

        $script_start_tag = $this->create_open_tag('script', array('src'=>'/js/login.js'));
        $this->add_head_tag($script_start_tag.$script_close_tag);
    }

    function current_url()
    {
        return 'http'.(isset($_SERVER['HTTPS'])?'s':'').'://'."{$_SERVER['HTTP_HOST']}/{$_SERVER['REQUEST_URI']}";
    }

    function add_login_form()
    {
        $this->body .= '<div class="modal fade" role="dialog" id="login-dialog" title="Login" aria-hidden="true">
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
