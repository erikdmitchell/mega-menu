<?php
    
class PMM_Admin {
    
    public function __construct() {
        add_action('admin_menu', array($this, 'menu'));
    }
    
    public function menu() {
        add_theme_page('Pickle Mega Menu', 'Mega Menu', 'edit_theme_options', 'pickle-mega-menu', array($this, 'menu_page'));
    }
    
    public function menu_page() {
        $html='';
        
        $html.='<div class="wrap">';
        
            $html.='<h1>Pickle Mega Menu</h1>';
        
        $html.='</div>';
        
        echo $html;
    }
    
}