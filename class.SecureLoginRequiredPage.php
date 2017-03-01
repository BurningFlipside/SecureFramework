<?php
require_once('class.LoginRequiredPage.php');
require_once('class.SecurePage.php');
class SecureLoginRequiredPage extends LoginRequiredPage
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
}
?>
