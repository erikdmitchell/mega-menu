<?php
    
class PMM_Admin {
    
    public function __construct() {
        add_action('admin_enqueue_scripts', array($this, 'scripts_styles'));
        add_action('admin_menu', array($this, 'menu'));
    }
    
    public function scripts_styles() {
        wp_enqueue_style('pmm-admin-page', PMM_URL.'admin/css/pmm-page.css', '', PMM_VERSION);
    }
    
    public function menu() {
        add_theme_page('Pickle Mega Menu', 'Mega Menu', 'edit_theme_options', 'pickle-mega-menu', array($this, 'menu_page'));
    }
    
    public function menu_page() {
        $html='';
        
        $html.='<div class="wrap">';
        
            $html.='<h1>Pickle Mega Menu</h1>';
        
            $html.=$this->get_admin_page( 'main' );
        
        $html.='</div>';
        
        echo $html;
    }

    protected function get_admin_page( $template_name = false ) {
        if ( ! $template_name ) {
            return false;
        }

        ob_start();

        include PMM_PATH . 'admin/pages/' . $template_name . '.php';

        $html = ob_get_contents();

        ob_end_clean();

        return $html;
    }
    
}