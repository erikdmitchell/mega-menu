<?php
    
class PMM_Admin {
    
    public function __construct() {
        add_action('admin_enqueue_scripts', array($this, 'scripts_styles'));
        add_action('admin_menu', array($this, 'menu'));
        add_action('admin_init', array($this, 'save_menu'));
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
    
    public function save_menu() {
        if (!isset($_POST['pmm_admin']) || !wp_verify_nonce($_POST['pmm_admin'], 'pmm_save_menu'))
            return;
            
/*
        wp_terms
            term_id **
            name ($_POST['menu_name'])
            slug (sanitize_title_with_dashes($_POST['menu_name']))
            term_group
            
        wp_term_taxonomy
            term_taxonomy_id
            term_id **
            taxonomy nav_menu
            description
            parent
            count
            
        generates a custom post type 'nav_menu_item'
*/
    }
    
}