<?php
    
class PMM_Admin_Save_Menu {
    
    private $post_fields = array();
        
    public function __construct() {
        add_action('wp_ajax_pmm_save_menu', array($this, 'ajax_save_menu'));
        add_action('wp_ajax_pmm_save_submenu', array($this, 'ajax_save_submenu'));
        
        $this->post_fields = array(
            'menu-item-db-id', 'menu-item-object-id', 'menu-item-object',
            'menu-item-parent-id', 'menu-item-position', 'menu-item-type',
            'menu-item-title', 'menu-item-url', 'menu-item-description',
            'menu-item-attr-title', 'menu-item-target', 'menu-item-classes', 'menu-item-xfn'
        );
    }

    public function ajax_save_menu() {
        $form_menu_items = array();
       
        parse_str($_POST['form'], $form_data);
        $menu_locations = isset($form_data['menu_locations']) ? $form_data['menu_locations'] : '';

        foreach ($form_data['pmm_menu_items'] as $form_menu_item) :                   
            if ($form_menu_item['nav_type'] === 'primary')
                $form_menu_items[] = $form_menu_item; 
        endforeach; 
        
        $this->update_menu_locations($menu_locations);

        echo $this->update_menu(esc_html($form_data['menu_name']), $form_data['menu_id'], $form_menu_items);

        wp_die();        
    }
    
    public function ajax_save_submenu() {
        parse_str($_POST['form'], $form_data);
     
        if (!empty($form_data['pmm_menu_items'])) :
            $form_submenu_items = array();
            
            foreach ($form_data['pmm_menu_items'] as $form_menu_item) :           
                if ($form_menu_item['nav_type'] == 'subnav' && $form_menu_item['primary_nav'] == $_POST['sub_nav_id'])
                    $form_submenu_items[] = $form_menu_item; 
            endforeach;
    
            echo $this->update_submenu_nav_menu_items($form_data['menu_id'], $form_data['menu_name'], $_POST['sub_nav_id'], $form_submenu_items);
        else :
// no items        
        endif;

        wp_die();        
    }
        
    private function update_menu($menu_name='', $menu_id=0, $menu_items = '') {
        $message = '';
                      
        // Add new menu.
        if (0 == $menu_id) :
            $new_menu_title = trim(esc_html($menu_name));
            
            if ($new_menu_title) :
 
				$_nav_menu_selected_id = wp_update_nav_menu_object( 0, array('menu-name' => $new_menu_title) );

				if ( is_wp_error( $_nav_menu_selected_id ) ) {
                    $message = $this->notices(array(
                       'type' => 'error',
                       'message' => $_nav_menu_selected_id->get_error_message(),
                       'dismissible' => true, 
                    ));					
				} else {
					$_menu_object = wp_get_nav_menu_object( $_nav_menu_selected_id );
					$nav_menu_selected_id = $_nav_menu_selected_id;
					$nav_menu_selected_title = $_menu_object->name;
					
					// Save menu items.
		  			if ( !empty($menu_items) )
                        $this->update_menu_nav_items($nav_menu_selected_id, $nav_menu_selected_title, $menu_items);
					
                    $message = $this->notices(array(
                       'type' => 'success',
                       'message' => sprintf( __( '<strong>%s</strong> has been created.' ), $nav_menu_selected_title ),
                    ));					
				} 
            
                return $message;
            else :
                // message about error
            endif;
       
        // Update existing menu.
        else :
            $_menu_object = wp_get_nav_menu_object( $menu_id );

			$menu_title = trim( $menu_name );
			
			if ( ! $menu_title ) {
                $message = $this->notices(array(
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

                    $message = $this->notices(array(
                       'type' => 'error',
                       'message' => $_nav_menu_selected_id->get_error_message(),
                       'dismissible' => true,
                    ));	
                    
                    return $message;					
				} else {
					$_menu_object = wp_get_nav_menu_object( $_nav_menu_selected_id );
					$nav_menu_selected_title = $_menu_object->name;
				}
			}

			// Update menu items.
			if ( ! is_wp_error( $_menu_object ) ) {
    			$this->update_menu_nav_items($_menu_object->term_id, $nav_menu_selected_title, $menu_items);

				// If the menu ID changed, redirect to the new URL.
				if ( $menu_id != $_nav_menu_selected_id ) {
					wp_redirect( admin_url( 'themes.php?page=pickle-mega-menu&menu=' . intval( $_nav_menu_selected_id ) ) );
					exit();
				}
				
                $message = $this->notices(array(
                   'type' => 'success',
                   'message' => sprintf( __( '%s has been updated.' ), '<strong>' . $nav_menu_selected_title . '</strong>' ),
                   'dismissible' => true,            
                ));				
			} 
     
        endif;
        
        return $message;
    }

    private function update_submenu_nav_menu_items($nav_menu_selected_id, $nav_menu_selected_title, $sub_nav_id = 0, $post_menu_items = '') {
        $unsorted_menu_items = $this->get_submenu_items($nav_menu_selected_id, $sub_nav_id);
        $menu_items = array();
        
        // Index menu items by db ID
        foreach ( $unsorted_menu_items as $_item )
            $menu_items[$_item->db_id] = $_item;

        wp_defer_term_counting( true );

        // Loop through all the menu items' POST variables
        if (!empty($post_menu_items)) : 
            foreach ( (array) $post_menu_items as $_key => $k ) :

                $menu_item_db_id = $this->update_nav_menu_item($k, $nav_menu_selected_id);
                
                if (isset($menu_items[$menu_item_db_id]))
                    unset( $menu_items[ $menu_item_db_id ] );                

            endforeach;       
        endif;
     
        // Remove menu items from the menu that weren't in $_POST - this needs to be modified to handle the specific submenu
        if ( ! empty( $menu_items ) ) {
            foreach ( array_keys( $menu_items ) as $menu_item_id ) {
                if ( is_nav_menu_item( $menu_item_id ) ) {                   
                    wp_delete_post( $menu_item_id );
                }
            }
        }
     
        wp_defer_term_counting( false );
        
        $message = $this->notices(array(
           'type' => 'success',
           'message' => sprintf( __( '%s has been updated.' ), '<strong>' . $nav_menu_selected_title . '</strong>' ),
           'dismissible' => true,            
        ));
     
        unset( $menu_items, $unsorted_menu_items );
   
        return $message;
    }
    
    private function get_submenu_items($nav_menu_selected_id, $submenu_id) {
        $menu_items = wp_get_nav_menu_items( $nav_menu_selected_id, array( 'orderby' => 'ID', 'output' => ARRAY_A, 'output_key' => 'ID', 'post_status' => 'draft, publish' ) );
        $submenu_items = array();
                
        if (empty($menu_items))
            return;
            
        $menu_items = $this->append_nav_item_meta_via_db_id($menu_items);
           
        foreach ($menu_items as $menu_item) :
            if ($menu_item->pmm_menu_primary_nav == $submenu_id)
                $submenu_items[] = $menu_item; 
        endforeach;
        
        return $submenu_items;
    }
    
    private function update_nav_menu_item($menu_item, $nav_menu_selected_id) {
        // Menu item title can't be blank
        if ( ! isset( $menu_item['label'] ) || '' == $menu_item['label'] )
            return;
            
        // convert to wp names for better menu compat and insert.
        foreach ($menu_item as $key => $value) :
            if (array_key_exists($key, $this->pmm_item_args_to_wp())) :
                $menu_item[$this->pmm_item_args_to_wp()[$key]] = $value;
            endif;
        endforeach;

        $args = array();
        foreach ( $this->post_fields as $field )
            $args[$field] = isset( $menu_item[$field] ) ? $menu_item[$field] : '';

        $db_id = $this->get_menu_item_db_id($menu_item['id']);

        $menu_item_db_id = wp_update_nav_menu_item( $nav_menu_selected_id, $db_id, $args );

        if ( is_wp_error( $menu_item_db_id ) ) :                    
            $message = $this->notices(array(
               'type' => 'error',
               'message' => $menu_item_db_id->get_error_message(), 
            ));                    
        else :
            // this is a force publish for now - long term we may need to fix this
            wp_update_post(array(
                'ID' => $menu_item_db_id,
                'post_status' => 'publish',
            ));
            
            $this->update_menu_item_meta($menu_item_db_id, $menu_item);
            
            return $menu_item_db_id;
        endif;
        
        return;       
    }
    
    private function update_menu_nav_items($nav_menu_selected_id, $nav_menu_selected_title, $post_menu_items = '') {              
        $unsorted_menu_items = $this->get_menu_items($nav_menu_selected_id);
        $menu_items = array();
        
        // Index menu items by db ID
        foreach ( $unsorted_menu_items as $_item )
            $menu_items[$_item->db_id] = $_item;

        wp_defer_term_counting( true );        
        
        // Loop through all the menu items' POST variables
        if (!empty($post_menu_items)) : 
            foreach ( (array) $post_menu_items as $_key => $k ) :
     
                $menu_item_db_id = $this->update_nav_menu_item($k, $nav_menu_selected_id);
                
                if (isset($menu_items[$menu_item_db_id]))
                    unset( $menu_items[ $menu_item_db_id ] );

            endforeach;       
        endif;
     
        // Remove menu items from the menu that weren't in $_POST - this needs to be modified to handle the specific submenu       
        if ( ! empty( $menu_items ) ) {
            foreach ( array_keys( $menu_items ) as $menu_item_id ) {
                if ( is_nav_menu_item( $menu_item_id ) ) {                   
                    wp_delete_post( $menu_item_id );
                }
            }
        }
     
        wp_defer_term_counting( false );
     
        unset( $menu_items, $unsorted_menu_items );
   
        return;
    }
 
    private function get_menu_items($nav_menu_selected_id) {
        $menu_items = wp_get_nav_menu_items( $nav_menu_selected_id, array( 'orderby' => 'ID', 'output' => ARRAY_A, 'output_key' => 'ID', 'post_status' => 'draft, publish' ) );
        $primary_menu_items = array();
     
        if (empty($menu_items))
            return $primary_menu_items;

        $menu_items = $this->append_nav_item_meta_via_db_id($menu_items);            

        foreach ($menu_items as $menu_item) :
            if ($menu_item->pmm_nav_type == 'primary')
                $primary_menu_items[] = $menu_item; 
        endforeach;
        
        return $primary_menu_items;
    }
    
    private function get_menu_item_db_id($id = 0) {
        global $wpdb;
        
        $db_id = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE ID = $id AND post_type = 'nav_menu_item'");
        
        if (null === $db_id)
            return 0;
        
        return $db_id;
    }
    
    private function update_menu_locations($locations = '') {  
        $menu_locations = get_nav_menu_locations();
        $new_menu_locations = array_map( 'absint', $locations );
        $menu_locations = array_merge( $menu_locations, $new_menu_locations );
     
        // Set menu locations
        set_theme_mod( 'nav_menu_locations', $menu_locations );
    }
    
    private function append_nav_item_meta_via_db_id($items) {   
        foreach ($items as $item) :
            $item->pmm_column = get_post_meta($item->db_id, '_pmm_menu_item_column', true);
            $item->pmm_row = get_post_meta($item->db_id, '_pmm_menu_item_row', true);
            $item->pmm_order = get_post_meta($item->db_id, '_pmm_menu_item_order', true);
            $item->pmm_item_type = get_post_meta($item->db_id, '_pmm_menu_item_type', true); // MAY BE ABLE TO REMOVE
            $item->pmm_nav_type = get_post_meta($item->db_id, '_pmm_menu_nav_type', true);
            $item->pmm_menu_primary_nav = get_post_meta($item->db_id, '_pmm_menu_primary_nav', true); // MAY BE ABLE TO REMOVE
        endforeach;                       
    
        return $items;
    }
    
    private function notices($args='') {
        $default_args=array(
            'type' => '',
            'message' => '',
            'dismissible' => false,
        );
        $args=wp_parse_args($args, $default_args);
        $type = '';
        $dismissible = '';
        $button = '';
				
        if ( $args['type'] )
            $type = ' notice-' . sanitize_title( $args['type'] );
				
        if ( $args['dismissible'] ) :
            $dismissible = ' is-dismissible';
            $button = '<button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>';
        endif;

        $class = ' class="notice' . $type . $dismissible . '"';
        $message = wp_kses_post( $args['message'] );
        
		return sprintf( '<div%1$s%2$s><p>%3$s</p>%4$s</div>', $id, $class, $message, $button );        
    }
    
    protected function update_menu_item_meta($post_id=0, $item='') {
        update_post_meta($post_id, '_pmm_menu_item_column', $item['column']);
        update_post_meta($post_id, '_pmm_menu_item_row', $item['row']);
        update_post_meta($post_id, '_pmm_menu_item_row_column', $item['row_column']);        
        update_post_meta($post_id, '_pmm_menu_item_order', $item['order']); 
        update_post_meta($post_id, '_pmm_menu_item_type', $item['item_type']);
        update_post_meta($post_id, '_pmm_menu_nav_type', $item['nav_type']);
        update_post_meta($post_id, '_pmm_menu_primary_nav', $item['primary_nav']);
    }
    
    private function pmm_item_args_to_wp() {
        return array(
            'label' => 'menu-item-title',
            'title' => 'menu-item-attr-title',
            'classes' => 'menu-item-classes',
            'object' => 'menu-item-object',
            'item_type' => 'menu-item-type',
            'page_id' => 'menu-item-object-id',
            'db_id' => 'menu-item-db-id',
            'primary_nav' => 'menu-item-parent-id',
        );

        /*
        menu-item-url 
        */
    }
	   
}

new PMM_Admin_Save_Menu();
