<?php
    
class PMM_Admin {
    
    public function __construct() {
        add_action('admin_enqueue_scripts', array($this, 'scripts_styles'));
        add_action('admin_menu', array($this, 'menu'));
        add_action('admin_init', array($this, 'save_menu'));
        add_action('admin_init', array($this, 'select_menu'));
    }
    
    public function scripts_styles() {
        wp_enqueue_script('pmm-menu-columns', PMM_URL.'admin/js/menu-columns.js', array('jquery'), '0.1.0', true);
        
        wp_enqueue_style('pmm-admin-page', PMM_URL.'admin/css/pmm-page.css', '', PMM_VERSION);
        wp_enqueue_style('pmm-font-awesome', PMM_URL.'admin/css/font-awesome.min.css', '', '4.7.0');
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

        $this->update_menu($_POST['menu_name'], $_POST['menu_id']);
    }
    
    private function update_menu($menu_name='', $menu_id=0) {
       // Add new menu.
       if (0 == $menu_id) :
            $new_menu_title = trim(esc_html($menu_name));
            
            if ($new_menu_title) :
 
				$_nav_menu_selected_id = wp_update_nav_menu_object( 0, array('menu-name' => $new_menu_title) );

				if ( is_wp_error( $_nav_menu_selected_id ) ) {
					//$messages[] = '<div id="message" class="error notice is-dismissible"><p>' . $_nav_menu_selected_id->get_error_message() . '</p></div>';
					// message about error
				} else {
					$_menu_object = wp_get_nav_menu_object( $_nav_menu_selected_id );
					$nav_menu_selected_id = $_nav_menu_selected_id;
					$nav_menu_selected_title = $_menu_object->name;
					
					// Save menu items.
					//if ( isset( $_REQUEST['menu-item'] ) )
						//wp_save_nav_menu_items( $nav_menu_selected_id, absint( $_REQUEST['menu-item'] ) );
						
/*
					if ( isset( $_REQUEST['zero-menu-state'] ) ) {
						// If there are menu items, add them
						wp_nav_menu_update_menu_items( $nav_menu_selected_id, $nav_menu_selected_title );
						// Auto-save nav_menu_locations
						$locations = get_nav_menu_locations();
						foreach ( $locations as $location => $menu_id ) {
								$locations[ $location ] = $nav_menu_selected_id;
								break; // There should only be 1
						}
						set_theme_mod( 'nav_menu_locations', $locations );
					}
*/
					
/*
					if ( isset( $_REQUEST['use-location'] ) ) {
						$locations = get_registered_nav_menus();
						$menu_locations = get_nav_menu_locations();
						if ( isset( $locations[ $_REQUEST['use-location'] ] ) )
							$menu_locations[ $_REQUEST['use-location'] ] = $nav_menu_selected_id;
						set_theme_mod( 'nav_menu_locations', $menu_locations );
					}
*/

					// $messages[] = '<div id="message" class="updated"><p>' . sprintf( __( '<strong>%s</strong> has been created.' ), $nav_menu_selected_title ) . '</p></div>';
					wp_redirect( admin_url( 'themes.php?page=pickle-mega-menu&menu=' . $_nav_menu_selected_id ) );
					exit();
					
					// REDIRECT ???
				} 
            
            else :
                // message about error
            endif;
       
       // Update existing menu.
       else :
            $_menu_object = wp_get_nav_menu_object( $menu_id );

			$menu_title = trim( esc_html( $_POST['menu-name'] ) );
			
			if ( ! $menu_title ) {
				$messages[] = '<div id="message" class="error notice is-dismissible"><p>' . __( 'Please enter a valid menu name.' ) . '</p></div>';
				$menu_title = $_menu_object->name;
			}

            // Update menut object.
			if ( ! is_wp_error( $_menu_object ) ) {
				$_nav_menu_selected_id = wp_update_nav_menu_object( $nav_menu_selected_id, array( 'menu-name' => $menu_title ) );
				if ( is_wp_error( $_nav_menu_selected_id ) ) {
					$_menu_object = $_nav_menu_selected_id;
					$messages[] = '<div id="message" class="error notice is-dismissible"><p>' . $_nav_menu_selected_id->get_error_message() . '</p></div>';
				} else {
					$_menu_object = wp_get_nav_menu_object( $_nav_menu_selected_id );
					$nav_menu_selected_title = $_menu_object->name;
				}
			}

			// Update menu items.
/*
			if ( ! is_wp_error( $_menu_object ) ) {
				$messages = array_merge( $messages, wp_nav_menu_update_menu_items( $_nav_menu_selected_id, $nav_menu_selected_title ) );

				// If the menu ID changed, redirect to the new URL.
				if ( $nav_menu_selected_id != $_nav_menu_selected_id ) {
					wp_redirect( admin_url( 'nav-menus.php?menu=' . intval( $_nav_menu_selected_id ) ) );
					exit();
				}
			} 
*/      
       endif;
        
    }
 
     public function select_menu() {
        if (!isset($_POST['pmm_admin']) || !wp_verify_nonce($_POST['pmm_admin'], 'pmm-select-menu'))
            return;

        if (isset($_POST['pmm_menu_id'])) :
            wp_redirect( admin_url( 'themes.php?page=pickle-mega-menu&menu=' . $_POST['pmm_menu_id'] ) );
            exit();        
        endif;    
    }
    
}