<?php
class ThemePlugin extends SecurePlugin
{
    function get_secure_menu_entries($page, $user)
    {
        $ret = array('Themes'=>$page->secure_root.'themes/index.php');
        return $ret;
    }

    function get_plugin_entry_point()
    {
        return array('name'=>'Theme Registration', 'link'=>'themes/index.php');
    }
}
?>
