<?php

class PMM_Nav_Walker extends Walker_Nav_Menu {

        /**
         * @see Walker::start_lvl()
         * @since 3.0.0
         *
         * @param string $output Passed by reference. Used to append additional content.
         * @param int $depth Depth of page. Used for padding.
         */
        public function start_lvl( &$output, $depth = 0, $args = array() ) {
                // adds our code for multi level drop downs //
                if ($depth!=0) :
                	$class='dropdown-submenu';
                else :
                	$class='dropdown-menu';
                endif;

                $indent = str_repeat( "\t", $depth );
                $output .= "\n$indent<ul role=\"menu\" class=\" ".$class."\">\n";
        }

        /**
         * @see Walker::start_el()
         * @since 3.0.0
         *
         * @param string $output Passed by reference. Used to append additional content.
         * @param object $item Menu item data object.
         * @param int $depth Depth of menu item. Used for padding.
         * @param int $current_page Menu item ID.
         * @param object $args
         */
        public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
                $indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

                /**
                 * Dividers, Headers or Disabled
                 * =============================
                 * Determine whether the item is a Divider, Header, Disabled or regular
                 * menu item. To prevent errors we use the strcasecmp() function to so a
                 * comparison that is not case sensitive. The strcasecmp() function returns
                 * a 0 if the strings are equal.
                 */
                if ( strcasecmp( $item->attr_title, 'divider' ) == 0 && $depth === 1 ) {
                        $output .= $indent . '<li role="presentation" class="divider">';
                } else if ( strcasecmp( $item->title, 'divider') == 0 && $depth === 1 ) {
                        $output .= $indent . '<li role="presentation" class="divider">';
                } else if ( strcasecmp( $item->attr_title, 'dropdown-header') == 0 && $depth === 1 ) {
                        $output .= $indent . '<li role="presentation" class="dropdown-header">' . esc_attr( $item->title );
                } else if ( strcasecmp($item->attr_title, 'disabled' ) == 0 ) {
                        $output .= $indent . '<li role="presentation" class="disabled"><a href="#">' . esc_attr( $item->title ) . '</a>';
                } else {

                        $class_names = $value = '';

                        $classes = empty( $item->classes ) ? array() : (array) $item->classes;
                        $classes[] = 'menu-item-' . $item->ID;

                        $class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );

                        if ( $args->has_children )
                                $class_names .= ' dropdown';

												if ($depth!=0)
													$class_names.=' sub-level';

                        if ( in_array( 'current-menu-item', $classes ) )
                                $class_names .= ' active';

                        $class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

                        $id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args );
                        $id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

                        $output .= $indent . '<li' . $id . $value . $class_names .' role="menuitem">';

                        $atts = array();
                        $atts['title']  = ! empty( $item->title )        ? $item->title        : '';
                        $atts['target'] = ! empty( $item->target )        ? $item->target        : '';
                        $atts['rel']    = ! empty( $item->xfn )                ? $item->xfn        : '';

                        // If item has_children add atts to a.
                        if ( $args->has_children && $depth === 0 ) {
                                $atts['href'] = ! empty( $item->url ) ? $item->url : '';
                                $atts['class']                        = 'dropdown-toggle';
                        } else {
                                $atts['href'] = ! empty( $item->url ) ? $item->url : '';
                        }

                        $atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args );

                        $attributes = '';
                        foreach ( $atts as $attr => $value ) {
                                if ( ! empty( $value ) ) {
                                        $value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
                                        $attributes .= ' ' . $attr . '="' . $value . '"';
                                }
                        }

                        $item_output = $args->before;

                        /*
                         * Glyphicons
                         * ===========
                         * Since the the menu item is NOT a Divider or Header we check the see
                         * if there is a value in the attr_title property. If the attr_title
                         * property is NOT null we apply it as the class name for the glyphicon.
                         */
                        if ( ! empty( $item->attr_title ) )
                                $item_output .= '<a'. $attributes .'><span class="glyphicon ' . esc_attr( $item->attr_title ) . '"></span>&nbsp;';
                        else
                                $item_output .= '<a'. $attributes .'>';

                        $item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
                        if ($args->has_children && $depth==0) :
                        	$item_output.='<span class="caret"></span></a>';
                        elseif ($args->has_children && $depth!=0) :
                        	$item_output.='<span class="right-caret"></span></a>';
                        else :
                        	$item_output.='</a>';
                        endif;
                        //$item_output .= ( $args->has_children && 0 === $depth ) ? ' <span class="caret"></span></a>' : '</a>';
                        //$item_output .= ( $args->has_children && $dept!=0 ) ? ' <span class="right-caret"></span></a>' : '</a>';
                        
                        $item_output .= $args->after;

                        $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
                }
        }

        /**
         * Traverse elements to create list from elements.
         *
         * Display one element if the element doesn't have any children otherwise,
         * display the element and its children. Will only traverse up to the max
         * depth and no ignore elements under that depth.
         *
         * This method shouldn't be called directly, use the walk() method instead.
         *
         * @see Walker::start_el()
         * @since 2.5.0
         *
         * @param object $element Data object
         * @param array $children_elements List of elements to continue traversing.
         * @param int $max_depth Max depth to traverse.
         * @param int $depth Depth of current element.
         * @param array $args
         * @param string $output Passed by reference. Used to append additional content.
         * @return null Null on failure with no changes to parameters.
         */
        public function display_element( $element, &$children_elements, $max_depth, $depth, $args, &$output ) {
            if ( ! $element )
                return;
    
            $id_field = $this->db_fields['id'];
    
            // Display this element.
            if ( is_object( $args[0] ) )
               $args[0]->has_children = ! empty( $children_elements[ $element->$id_field ] );
    
            parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
        }

        /**
         * Menu Fallback
         * =============
         * If this function is assigned to the wp_nav_menu's fallback_cb variable
         * and a manu has not been assigned to the theme location in the WordPress
         * menu manager the function with display nothing to a non-logged in user,
         * and will add a link to the WordPress menu manager if logged in as an admin.
         *
         * @param array $args passed from the wp_nav_menu function.
         *
         */
        public static function fallback( $args ) {
                if ( current_user_can( 'manage_options' ) ) {

                        extract( $args );

                        $fb_output = null;

                        if ( $container ) {
                                $fb_output = '<' . $container;

                                if ( $container_id )
                                        $fb_output .= ' id="' . $container_id . '"';

                                if ( $container_class )
                                        $fb_output .= ' class="' . $container_class . '"';

                                $fb_output .= '>';
                        }

                        $fb_output .= '<ul';

                        if ( $menu_id )
                                $fb_output .= ' id="' . $menu_id . '"';

                        if ( $menu_class )
                                $fb_output .= ' class="' . $menu_class . '"';

                        $fb_output .= '>';
                        $fb_output .= '<li><a href="' . admin_url( 'nav-menus.php' ) . '">Add a menu</a></li>';
                        $fb_output .= '</ul>';

                        if ( $container )
                                $fb_output .= '</' . $container . '>';

                        echo $fb_output;
                }
        }
}

    
/*
class PMM_Build_Menu {
    
    public $menu_id = 0;
    
    public $menu_object_id = 0;
        
    public function __construct($menu_id=0) {
        $this->menu_id = $menu_id;
        
        $menu_object = wp_get_nav_menu_object($menu_id);
        
        if (!isset($menu_object->term_id))
            return;
            
        $this->menu_object_id = $menu_object->term_id;
    }
    
    public function display() {
        echo $this->build_menu();
    }

    protected function build_menu() {
        $html='';
        $this->menu_items = wp_get_nav_menu_items($this->menu_object_id);
        $layout = $this->get_layout();
        
        foreach ($layout as $column => $blocks) :
            $html.=$this->add_column($column, $blocks);
        endforeach;

        return $html;
    }
    
    protected function get_layout() {
        $layout = array();
        
        // get column (as key) and array of blocks (as value).
        foreach ($this->menu_items as $item) :
            $layout[$item->pmm_column][] = $item->pmm_block;
        endforeach;
        
        // make blocks unique.
        foreach ($layout as $column => $blocks) :
            $layout[$column] = array_values( array_unique( $blocks ) );
        endforeach;
        
        return $layout;            
    }
    
    protected function add_column($id, $blocks) {
        $html='';
        
        $html.='<div id="pmm-column-'.$id.'" class="pmm-column">';
            $html.='<div class="block-actions">';
                $html.='<div class="add-block-wrap">';
                    $html.='<a href="#" class="add-block">Add Block</a>';
                $html.='</div>';
            $html.='</div>';
            
            foreach ($blocks as $block) :
                $html.=$this->add_block($id, $block);
            endforeach;
        $html.='</div>';
        
        return $html;
    }


    protected function add_block($column_id, $block_id) {
        $html='';
        
        $html.='<div id="pmm-block-'.$column_id.'-'.$block_id.'" class="pmm-block">';
            $html.=$this->add_items($column_id, $block_id);
        $html.='</div>';
        
        return $html;
    }
    
    protected function add_items($column_id, $block_id) {
        $html='';
        $items=array();
        
        // get items in column and block.
        foreach ($this->menu_items as $menu_item) :
            if ($menu_item->pmm_column == $column_id && $menu_item->pmm_block == $block_id) :
                $items[] = $menu_item;
            endif;
        endforeach;
        
        if (empty($items))
            return;
            
        // double check order.
        usort($items, function($a, $b) {
           return $a->pmm_order - $b->pmm_order; 
        });

        foreach ($items as $item) :
            if (isset($item->pmm_item_type) && '' !== $item->pmm_item_type)
                $html.=PickleMegaMenu()->admin->items[$item->pmm_item_type]->load_item($item->ID);
        endforeach;
        
        return $html;
    }    

}
*/