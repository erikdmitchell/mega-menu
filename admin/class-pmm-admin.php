<?php
    
class PMM_Admin {
    
    public $items='';
    
    public function __construct() {
        add_action('admin_enqueue_scripts', array($this, 'scripts_styles'));
        add_action('admin_menu', array($this, 'menu'));
        add_action('admin_init', array($this, 'save_menu'));
        add_action('admin_init', array($this, 'select_menu'));
        add_action('admin_init', array($this, 'register_items'));
    }
    
    public function scripts_styles() {
        wp_enqueue_script('jquery-ui-draggable');
        wp_enqueue_script('jquery-ui-accordion');
        wp_enqueue_script('pmm-menu-columns', PMM_URL.'admin/js/menu-columns.js', array('jquery-ui-draggable', 'jquery-ui-accordion'), '0.1.0', true);
    
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
            
            $html.=pmm_get_admin_notices();
        
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
    
	public function register_items() {
		$item_classes=array();
		
		foreach (get_declared_classes() as $class) :
			if (is_subclass_of($class, 'PMM_Item'))
				$item_classes[]=$class;
		endforeach;
		
		foreach ($item_classes as $item_class) :
			$ic=new $item_class();
			
			$this->items[$ic->slug]=$ic;
		endforeach;
	}  
	
	public function items_accordian() {
        wp_enqueue_script( 'accordion' );

        $html='';
    
        $html.='<div class="accordion-container">';
            $html.='<ul class="outer-border">';

                foreach (PickleMegaMenu()->admin->items as $item) :

                    $html.='<li class="control-section accordion-section open '.$item->slug.'" id="'.$item->slug.'">';
                        $html.='<h3 class="accordion-section-title hndle" tabindex="0">'.$item->label.'</h3>';
                        $html.='<div class="accordion-section-content">';
                            $html.='<div class="inside">';
                                $html.=$item->display();
                            $html.='</div><!-- .inside -->';
                        $html.='</div><!-- .accordion-section-content -->';
                    $html.='</li><!-- .accordion-section -->';
                    
                endforeach;

            $html.='</ul><!-- .outer-border -->';
        $html.='</div><!-- .accordion-container -->';
    
        echo $html;    	
	}  
    
    public function save_menu() {
        if (!isset($_POST['pmm_admin']) || !wp_verify_nonce($_POST['pmm_admin'], 'pmm_save_menu'))
            return;

        $this->update_menu(esc_html($_POST['menu_name']), $_POST['menu_id']);
    }
    
    private function update_menu($menu_name='', $menu_id=0) {
        //$messages = array();
        
        // Add new menu.
        if (0 == $menu_id) :
            $new_menu_title = trim(esc_html($menu_name));
            
            if ($new_menu_title) :
 
				$_nav_menu_selected_id = wp_update_nav_menu_object( 0, array('menu-name' => $new_menu_title) );

				if ( is_wp_error( $_nav_menu_selected_id ) ) {
					//$messages[] = '<div id="message" class="error notice is-dismissible"><p>' . $_nav_menu_selected_id->get_error_message() . '</p></div>';
					
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

					//$messages[] = '<div id="message" class="updated"><p>' . sprintf( __( '<strong>%s</strong> has been created.' ), $nav_menu_selected_title ) . '</p></div>';
					
                    pmm_add_admin_notice(array(
                       'type' => 'updated',
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
				//$messages[] = '<div id="message" class="error notice is-dismissible"><p>' . __( 'Please enter a valid menu name.' ) . '</p></div>';
                    
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
					//$messages[] = '<div id="message" class="error notice is-dismissible"><p>' . $_nav_menu_selected_id->get_error_message() . '</p></div>';

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
				$this->nav_menu_update_menu_items( $_menu_object->term_id, $nav_menu_selected_title ) );
				
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
        //$messages = array();
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
   
                $menu_item_db_id = wp_update_nav_menu_item( $nav_menu_selected_id, ( !empty($k['db_id']) ? $k['db_id'] : 0 ), $args );
     
                if ( is_wp_error( $menu_item_db_id ) ) :
                    //$messages[] = '<div id="message" class="error"><p>' . $menu_item_db_id->get_error_message() . '</p></div>';
                    
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

/*
        $messages[] = '<div id="message" class="updated notice is-dismissible"><p>' .
            // translators: %s: nav menu title.
            sprintf( __( '%s has been updated.' ),
                '<strong>' . $nav_menu_selected_title . '</strong>'
            ) . '</p></div>';
*/
            
        pmm_add_admin_notice(array(
           'type' => 'updated',
           'message' => sprintf( __( '%s has been updated.' ), '<strong>' . $nav_menu_selected_title . '</strong>' ),
           'dismissible' => true, 
        ));
     
        unset( $menu_items, $unsorted_menu_items );
   
        //return $messages;
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
 
    public function select_menu() {
        if (!isset($_POST['pmm_admin']) || !wp_verify_nonce($_POST['pmm_admin'], 'pmm-select-menu'))
            return;

        if (isset($_POST['pmm_menu_id'])) :
            wp_redirect( admin_url( 'themes.php?page=pickle-mega-menu&menu=' . $_POST['pmm_menu_id'] ) );
            exit();        
        endif;    
    }
    
    public function load_mega_menu($menu_id=0) {
        $menu = new PMM_Admin_Build_Menu($menu_id);

        echo $menu->display();
    }
	   
}