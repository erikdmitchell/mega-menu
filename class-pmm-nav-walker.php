<?php
    
class PMM_Nav_Walker extends Walker_Nav_Menu {
    
    private $current_column = '';
    private $current_block = '';
    
	public function start_lvl( &$output, $depth = 0, $args = array() ) {
		if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
			$t = '';
			$n = '';
		} else {
			$t = "\t";
			$n = "\n";
		}
		$indent = str_repeat( $t, $depth );

		// Default class.
		$classes = array( 'pmm-mega-sub-menu', 'DEPTH'.$depth );

		// Filters the CSS class(es) applied to a menu list element.
		$class_names = join( ' ', apply_filters( 'nav_menu_submenu_css_class', $classes, $args, $depth ) );
		$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

		$output .= "{$n}{$indent}<ul$class_names>{$n}";
	}
	
	public function end_lvl( &$output, $depth = 0, $args = array() ) {
		if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
			$t = '';
			$n = '';
		} else {
			$t = "\t";
			$n = "\n";
		}
		$indent = str_repeat( $t, $depth );
		
		$output .= "$indent</ul>{$n}";
	}
	
	public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
			$t = '';
			$n = '';
		} else {
			$t = "\t";
			$n = "\n";
		}
		$indent = ( $depth ) ? str_repeat( $t, $depth ) : '';

		$classes = empty( $item->classes ) ? array() : (array) $item->classes;
		$classes = $this->update_item_classes($classes);
		$classes[] = 'pmm-mega-menu-item-' . $item->ID;
		$classes[] = 'pmm-mega-menu-item-' . $item->post_name;
		$classes[] = 'DEPTH' . $depth;
		
		// add class to primary nav.
		if (0 === $depth)
    		$classes[] = 'pmm-mega-menu-primary-nav-item';
    		
        // check for column and row //
        if ($this->is_new_column($item))
            $classes[] = 'pmm-NEW-COLUMN';
            //$classes[] = 'pmm-NEW-ROW';
            // $html.='<li id="pmm-mega-menu-column-'.$id.'" class="pmm-mega-menu-column">';
            
        if ($this->is_new_row($item))
            $classes[] = 'pmm-NEW-ROW';
            // $html.='<ul id="pmm-mega-menu-row-'.$column_id.'-'.$block_id.'" class="pmm-mega-menu-row">';

        

		// Filters the arguments for a single nav menu item.
		$args = apply_filters( 'nav_menu_item_args', $args, $item, $depth );

		// Filters the CSS class(es) applied to a menu item's list item element.
		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) );
		$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

		// Filters the ID applied to a menu item's list item element.
		$id = apply_filters( 'nav_menu_item_id', 'pmm-mega-menu-item-'. $item->ID, $item, $args, $depth );
		$id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

		$output .= $indent . '<li' . $id . $class_names .'>';

		$atts = array();
		$atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
		$atts['target'] = ! empty( $item->target )     ? $item->target     : '';
		$atts['rel']    = ! empty( $item->xfn )        ? $item->xfn        : '';
		$atts['href']   = ! empty( $item->url )        ? $item->url        : '';
		$atts['class'] = 'pmm-mega-menu-link'; // set class for link.

		// Filters the HTML attributes applied to a menu item's anchor element.
		$atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );

		$attributes = '';
		foreach ( $atts as $attr => $value ) {
			if ( ! empty( $value ) ) {
				$value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
				$attributes .= ' ' . $attr . '="' . $value . '"';
			}
		}

		// This filter is documented in wp-includes/post-template.php.
		$title = apply_filters( 'the_title', $item->title, $item->ID );

		// Filters a menu item's title.
		$title = apply_filters( 'nav_menu_item_title', $title, $item, $args, $depth );

		$item_output = $args->before;
		$item_output .= '<a'. $attributes .'>';
		$item_output .= $args->link_before . $title . $args->link_after;
		$item_output .= '</a>';
		$item_output .= $args->after;

		// Filters a menu item's starting output.

		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}
	
	public function end_el( &$output, $item, $depth = 0, $args = array() ) {
		if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
			$t = '';
			$n = '';
		} else {
			$t = "\t";
			$n = "\n";
		}
		
		$output .= "</li>{$n}";
	}
	
	protected function update_item_classes($classes) {
    	if (empty($classes))
    	    return $classes;
    	    
        return str_replace('menu-item', 'pmm-mega-menu-item', $classes);
	}
	
	protected function is_new_column($item) {   	
    	if (0 == $item->pmm_block && 0 == $item->pmm_order) :
    	    $this->current_column = $item->pmm_column;
    	    
    	    return true;
        endif;
    	    
        return false;
	}
	
	protected function is_end_column() {
    	
	}
	
	protected function is_new_row($item) {
        if ($this->current_column == $item->pmm_column && $this->current_block != $item->pmm_block) :
            $this->current_block = $item->pmm_block;
            
            return true;
        endif;
    	
    	return false;
	}
	
/*
    protected function has_subnav($sub_nav_id = 0, $menu_id = 0) {
        $menu_items = wp_get_nav_menu_items($menu_id);
        
        foreach ($menu_items as $menu_item) :       
            if ($menu_item->pmm_nav_type == 'subnav' && $menu_item->pmm_menu_primary_nav === $sub_nav_id) :
                return true;
            endif;
        endforeach;
        
        return false;       
    }	
*/   
    
}

                  

                
                   
                        //$html.=$this->get_subnav($primary_nav_item->pmm_order);