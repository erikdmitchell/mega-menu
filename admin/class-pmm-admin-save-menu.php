<?php
    
class PMM_Admin_Save_Menu {
    
    public $items='';
    
    public function __construct() {
        add_action('admin_enqueue_scripts', array($this, 'scripts_styles'));
        add_action('admin_init', array($this, 'save_menu'));
    }
    
    public function scripts_styles() {
        //wp_enqueue_script('pmm-menu-columns', PMM_URL.'admin/js/menu-columns.js', array('jquery-ui-draggable', 'jquery-ui-accordion'), '0.1.0', true);      
    }
    
    public function save_menu() {
        if (!isset($_POST['pmm_admin']) || !wp_verify_nonce($_POST['pmm_admin'], 'pmm_save_menu'))
            return;

        $this->update_menu(esc_html($_POST['menu_name']), $_POST['menu_id']);
    }
    
    private function update_menu($menu_name='', $menu_id=0) {
        // Add new menu.
        if (0 == $menu_id) :
            $new_menu_title = trim(esc_html($menu_name));
            
            if ($new_menu_title) :
 
				$_nav_menu_selected_id = wp_update_nav_menu_object( 0, array('menu-name' => $new_menu_title) );

				if ( is_wp_error( $_nav_menu_selected_id ) ) {
                    pmm_add_admin_notice(array(
                       'type' => 'error',
                       'message' => $_nav_menu_selected_id->get_error_message(),
                       'dismissible' => true, 
                    ));					
				} else {
					$_menu_object = wp_get_nav_menu_object( $_nav_menu_selected_id );
					$nav_menu_selected_id = $_nav_menu_selected_id;
					$nav_menu_selected_title = $_menu_object->name;
					
					// Save menu items.
		  			if ( isset( $_REQUEST['pmm_menu_items'] ) )
                        $this->nav_menu_update_menu_items( $nav_menu_selected_id, $nav_menu_selected_title );
					
                    pmm_add_admin_notice(array(
                       'type' => 'success',
                       'message' => sprintf( __( '<strong>%s</strong> has been created.' ), $nav_menu_selected_title ),
                    ));					
					
					wp_redirect( admin_url( 'themes.php?page=pickle-mega-menu&menu=' . intval( $_nav_menu_selected_id ) ) );
					exit();
				} 
            
            else :
                // message about error
            endif;
       
        // Update existing menu.
        else :
            $_menu_object = wp_get_nav_menu_object( $menu_id );

			$menu_title = trim( $menu_name );
			
			if ( ! $menu_title ) {
                pmm_add_admin_notice(array(
                   'type' => 'error',
                   'message' => __( 'Please enter a valid menu name.' ),
                   'dismissible' => true,
                ));	
                    				
				$menu_title = $_menu_object->name;
			}

            // Update menut object.
			if ( ! is_wp_error( $_menu_object ) ) {
				$_nav_menu_selected_id = wp_update_nav_menu_object( $menu_id, array( 'menu-name' => $menu_title ) );
				if ( is_wp_error( $_nav_menu_selected_id ) ) {
					$_menu_object = $_nav_menu_selected_id;

                    pmm_add_admin_notice(array(
                       'type' => 'error',
                       'message' => $_nav_menu_selected_id->get_error_message(),
                       'dismissible' => true,
                    ));						
				} else {
					$_menu_object = wp_get_nav_menu_object( $_nav_menu_selected_id );
					$nav_menu_selected_title = $_menu_object->name;
				}
			}

			// Update menu items.
			if ( ! is_wp_error( $_menu_object ) ) {
				$this->nav_menu_update_menu_items( $_menu_object->term_id, $nav_menu_selected_title );
//exit();				
				// If the menu ID changed, redirect to the new URL.
				if ( $nav_menu_selected_id != $_nav_menu_selected_id ) {
					wp_redirect( admin_url( 'themes.php?page=pickle-mega-menu&menu=' . intval( $_nav_menu_selected_id ) ) );
					exit();
				}
			} 
     
        endif;
        
    }
    
    // https://developer.wordpress.org/reference/functions/wp_nav_menu_update_menu_items/
    private function nav_menu_update_menu_items($nav_menu_selected_id, $nav_menu_selected_title) {
        $unsorted_menu_items = wp_get_nav_menu_items( $nav_menu_selected_id, array( 'orderby' => 'ID', 'output' => ARRAY_A, 'output_key' => 'ID', 'post_status' => 'draft, publish' ) );
        $menu_items = array();
        
        // Index menu items by db ID
        foreach ( $unsorted_menu_items as $_item )
            $menu_items[$_item->db_id] = $_item;
     
        $post_fields = array(
            'menu-item-db-id', 'menu-item-object-id', 'menu-item-object',
            'menu-item-parent-id', 'menu-item-position', 'menu-item-type',
            'menu-item-title', 'menu-item-url', 'menu-item-description',
            'menu-item-attr-title', 'menu-item-target', 'menu-item-classes', 'menu-item-xfn'
        );

        wp_defer_term_counting( true );

        // Loop through all the menu items' POST variables
        if (!empty($_POST['pmm_menu_items'])) :
            foreach ( (array) $_POST['pmm_menu_items'] as $_key => $k ) :
     
                // Menu item title can't be blank
                if ( ! isset( $k['label'] ) || '' == $k['label'] )
                    continue;
                    
                // convert to wp names for better menu compat and insert.
                foreach ($k as $key => $value) :
                    if (array_key_exists($key, $this->pmm_item_args_to_wp())) :
                        $k[$this->pmm_item_args_to_wp()[$key]] = $value;
                    endif;
                endforeach;
     
                $args = array();
                foreach ( $post_fields as $field )
                    $args[$field] = isset( $k[$field] ) ? $k[$field] : '';

                $db_id = $this->get_menu_item_db_id($k['id']);

                $menu_item_db_id = wp_update_nav_menu_item( $nav_menu_selected_id, $db_id, $args );
     
                if ( is_wp_error( $menu_item_db_id ) ) :                    
                    pmm_add_admin_notice(array(
                       'type' => 'error',
                       'message' => $menu_item_db_id->get_error_message(), 
                    ));                    
                else :
                    unset( $menu_items[ $menu_item_db_id ] );
                    
                    // this is a force publish for now - long term we may need to fix this
                    wp_update_post(array(
                        'ID' => $menu_item_db_id,
                        'post_status' => 'publish',
                    ));
                    
                    $this->update_menu_item_meta($menu_item_db_id, $k);
                endif;
            endforeach;       
        endif;
     
        // Remove menu items from the menu that weren't in $_POST
        if ( ! empty( $menu_items ) ) {
            foreach ( array_keys( $menu_items ) as $menu_item_id ) {
                if ( is_nav_menu_item( $menu_item_id ) ) {
                    wp_delete_post( $menu_item_id );
                }
            }
        }
     
        wp_defer_term_counting( false );
            
        pmm_add_admin_notice(array(
           'type' => 'success',
           'message' => sprintf( __( '%s has been updated.' ), '<strong>' . $nav_menu_selected_title . '</strong>' ),
           'dismissible' => true, 
        ));
     
        unset( $menu_items, $unsorted_menu_items );
   
        return true;
    }
    
    private function get_menu_item_db_id($id = 0) {
        global $wpdb;
        
        $db_id = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE ID = $id AND post_type = 'nav_menu_item'");
        
        if (null === $db_id)
            return 0;
        
        return $db_id;
    }
    
    protected function update_menu_item_meta($post_id=0, $item='') {
        update_post_meta($post_id, '_pmm_menu_item_column', $item['column']);
        update_post_meta($post_id, '_pmm_menu_item_block', $item['block']);
        update_post_meta($post_id, '_pmm_menu_item_order', $item['order']); 
        update_post_meta($post_id, '_pmm_menu_item_type', $item['item_type']);                        
    }
    
    private function pmm_item_args_to_wp() {
        return array(
            'label' => 'menu-item-title',
            'title' => 'menu-item-attr-title',
            'classes' => 'menu-item-classes',
            'page_id' => 'menu-item-object-id',
            'db_id' => 'menu-item-db-id',
        );

        /*
        menu-item-object 
        menu-item-type 
        menu-item-url 
        */
    }
	   
}

new PMM_Admin_Save_Menu();