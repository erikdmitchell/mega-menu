<?php
    
class PMM_Admin {
    
    public $items='';
    
    public function __construct() {
        add_action('admin_enqueue_scripts', array($this, 'scripts_styles'));
        add_action('admin_menu', array($this, 'menu'));
        add_action('admin_init', array($this, 'select_menu'));
        add_action('admin_init', array($this, 'register_items'));
        add_action('wp_ajax_pmm_load_menu', array($this, 'ajax_load_menu'));
    }
    
    public function scripts_styles() {
        wp_enqueue_script('jquery-ui-draggable');
        wp_enqueue_script('jquery-ui-accordion');
        wp_enqueue_script('pmm-menu-columns', PMM_URL.'admin/js/menu-columns.js', array('jquery-ui-draggable', 'jquery-ui-accordion'), '0.1.0', true);
        wp_enqueue_script('pmm-menu-ajax', PMM_URL.'admin/js/ajax.js', array('pmm-menu-columns'), '0.1.0', true);
            
        wp_enqueue_style('pmm-admin-page', PMM_URL.'admin/css/pmm-page.css', '', PMM_VERSION);         
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
print_r($primary_nav_html);        
echo 'ajax load menu return output';        

        wp_die();        
    }   
	   
}