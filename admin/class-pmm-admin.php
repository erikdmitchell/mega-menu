<?php
    
class PMM_Admin {
    
    public $items='';
    
    public function __construct() {
        add_action('admin_enqueue_scripts', array($this, 'scripts_styles'));
        add_action('admin_menu', array($this, 'menu'));
        add_action('admin_init', array($this, 'select_menu'));
        add_action('admin_init', array($this, 'register_items'));
        add_action('wp_ajax_pmm_load_menu', array($this, 'ajax_load_menu'));
        add_action('wp_ajax_pmm_load_menu_locations', array($this, 'ajax_load_menu_locations'));
        add_action('wp_ajax_pmm_load_submenu', array($this, 'ajax_load_submenu'));
        add_action('wp_ajax_pmm_delete_submenu', array($this, 'ajax_delete_submenu'));
    }
    
    public function scripts_styles() {
        wp_enqueue_script('jquery-ui-sortable');
        wp_enqueue_script('jquery-ui-draggable');
        wp_enqueue_script('jquery-ui-accordion');
        wp_enqueue_script('pmm-menu-builder', PMM_URL.'admin/js/pmm-menu-builder.js', array('jquery-ui-sortable', 'jquery-ui-draggable', 'jquery-ui-accordion'), PMM_VERSION, true);
        wp_enqueue_script('pmm-menu-ajax', PMM_URL.'admin/js/pmm-menu-builder-ajax.js', array('pmm-menu-builder'), PMM_VERSION, true);
        wp_enqueue_script('pmm-modal-script', PMM_URL.'admin/js/pmm-modal.js', array('jquery'), PMM_VERSION, true);
            
        wp_enqueue_style('pmm-admin-page', PMM_URL.'admin/css/pmm-menu-builder.css', '', PMM_VERSION);
        wp_enqueue_style('pmm-modal-style', PMM_URL.'admin/css/pmm-modal.css', '', PMM_VERSION);
    }
    
    public function menu() {
        add_theme_page('Pickle Mega Menu', 'Mega Menu', 'edit_theme_options', 'pickle-mega-menu', array($this, 'menu_page'));
    }
    
    public function menu_page() {
        $html='';
        
        $html.='<div class="wrap">';
        
            $html.='<h1>Pickle Mega Menu</h1>';
        
            $html.=$this->get_admin_page( 'menu-builder' );
        
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

                    $html.='<li class="control-section accordion-section '.$item->slug.'" id="'.$item->slug.'">';
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
     
    public function select_menu() {
        if (!isset($_POST['pmm_admin']) || !wp_verify_nonce($_POST['pmm_admin'], 'pmm-select-menu'))
            return;

        if (isset($_POST['pmm_menu_id'])) :
            wp_redirect( admin_url( 'themes.php?page=pickle-mega-menu&menu=' . $_POST['pmm_menu_id'] ) );
            exit();        
        endif;    
    }

    public function ajax_load_menu() {
        $menu = new PMM_Admin_Build_Menu($_POST['id']);    
        $primary_nav_html = $menu->build_primary_nav();

        // if empty, no menu, else return menu.
        if (empty($primary_nav_html)) :
            wp_send_json_error();
        else :
            wp_send_json_success($primary_nav_html);  
        endif;

        wp_die();        
    }   

    public function ajax_load_menu_locations() {
        wp_send_json($this->get_menu_locations());

        wp_die();        
    }
    
    public function get_menu_locations() {
        $html = '';
        $locations = get_registered_nav_menus();
        $menu_locations = get_nav_menu_locations();

        $html.='<h3>Menu Location</h3>';
                    
        $html.='<fieldset class="menu-theme-locations">';
            foreach ($locations as $location_slug => $location_name):
                $nav_menu_name = '';
                $nav_menu_id = 0;
                 
                if (array_key_exists($location_slug, $menu_locations)) : 
                    $nav_menu = wp_get_nav_menu_object($menu_locations[$location_slug]);
                    $nav_menu_name = $nav_menu->name;
                    $nav_menu_id = $menu_locations[$location_slug];
                endif;
            
                $html.='<div class="menu-settings-input checkbox-input">';
                    $html.='<input type="checkbox" name="menu_locations['.$location_slug.']" id="locations-primary" value="'.$_POST['menu_id'].'" '.checked($nav_menu_id, $_POST['menu_id'], false).'>';
                    $html.='<label for="locations-primary">'.$location_name.'</label>';
                    
                    if (!empty($nav_menu_name)) :
                        $html.='<span class="theme-location-set">(Currently set to: '.$nav_menu_name.')</span>';
                    endif;
		        $html.='</div>';
            endforeach;
		$html.='</fieldset>';
		
		return $html;        
    }

    public function ajax_load_submenu() {
        $menu = new PMM_Admin_Build_Menu($_POST['menu_id']);
        $subnav = $menu->get_subnav($_POST['sub_nav_id']);

        // if empty, no menu, else return menu.
        if (empty($subnav)) :
            wp_send_json_error();
        else :
            wp_send_json_success($subnav);  
        endif;

        wp_die();        
    } 
    
    public function ajax_delete_submenu() {
        $menu_items = wp_get_nav_menu_items( $_POST['menu_id'] );
        $menu_items_to_remove = array($_POST['item_id']);
        
        foreach ($menu_items as $menu_item) :
            if ('subnav' === $menu_item->pmm_nav_type && $_POST['sub_nav_id'] === $menu_item->pmm_menu_primary_nav )
                $menu_items_to_remove[] = $menu_item->ID;
        endforeach;
        
        foreach ($menu_items_to_remove as $post_id) :
            wp_delete_post($post_id);
        endforeach;

        wp_send_json('<div class="notice notice-success is-dismissible"><p>Menu items removed.</p></div>');
        
        wp_die();
    }
	   
}
