<?php
    
class PMM_Nav_Walker extends Walker_Nav_Menu {
    
    private $current_column = '';
    private $current_row = '';
    private $current_row_column = '';
    
    private $end_item_row = '</ul>';
    private $end_item_col = '</li>';
    private $end_row_column = '</li>';
    private $end_row_column_row = '</ul>';

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
		$classes = array( 'pmm-mega-sub-menu' );

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

		$output .= "$indent{$this->end_row_column_row}{$n}{$this->end_row_column}{$n}{$this->end_item_row}{$n}{$this->end_item_col}{$n}</ul>{$n}";
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
		$column_count = 0;
	
        // setup classes.
		$classes = empty( $item->classes ) ? array() : (array) $item->classes;
		$classes = $this->update_item_classes($classes);
		$classes[] = 'pmm-mega-menu-item-' . $item->ID;
		$classes[] = 'pmm-mega-menu-item-' . $item->post_name;

		// add class to primary nav.
		if (0 === $depth)
    		$classes[] = 'pmm-mega-menu-primary-nav-item';   		

        // buld out our mega menu.
        if (0 !== $depth) :
        
            if ($this->is_new_item_column($item)) :
                if ($item->pmm_column != 0) :
                    $output.=$this->end_row_column_row; // MAY NEED CHECK 
                    $output.=$this->end_row_column;
                    $output.=$this->end_item_row;
                    $output.=$this->end_item_col;            
                endif;
                
                //$output.='<!-- new item column -->';
                $output .= '<li id="pmm-mega-menu-column-'.$this->current_column.'" class="pmm-mega-menu-column pmm-mega-menu-columns-'.$this->get_total_columns($args, $item).' pmm-item-column">';
            endif;
            
            if ($this->is_new_item_row($item)) :
                if ($this->current_row != 0) :
                    $output.=$this->end_row_column_row; // MAY NEED CHECK 
                    $output.=$this->end_row_column;
                    $output.=$this->end_item_row;
                endif;
                
                
                
                //$output.='<!-- new item row -->';
                $output .= '<ul id="pmm-mega-menu-row-'.$this->current_column.'-'.$this->current_row.'" class="pmm-mega-menu-row pmm-item-row">';
            endif;
            
            if ($this->is_new_row_column($item)) :
                if ($this->current_row_column != 0) :
                    $output.=$this->end_row_column_row; // MAY NEED CHECK 
                    $output.=$this->end_row_column;
                endif;
            
                $output.='<li id="pmm-mega-menu-column-'.$this->current_column.'-'.$this->current_row.'-'.$this->current_row_column.'" class="pmm-mega-menu-column pmm-row-column pmm-mega-menu-columns-'.$this->get_total_row_columns($args, $item).'">';
                $output.='<ul id="pmm-mega-menu-row-'.$this->current_column.'-'.$this->current_row.'-'.$this->current_row_column.'-'.$item->pmm_row.'" class="pmm-mega-menu-row pmm-row-column-row">';  // MAY NEED CHECK  
            endif;
        
        endif;

		// Filters the arguments for a single nav menu item.
		$args = apply_filters( 'nav_menu_item_args', $args, $item, $depth );

		// Filters the CSS class(es) applied to a menu item's list item element.
		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) );
		$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

		// Filters the ID applied to a menu item's list item element.
		$id = apply_filters( 'nav_menu_item_id', 'pmm-mega-menu-item-'. $item->ID, $item, $args, $depth );
		$id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

		$output .= $indent . '<li' . $id . $class_names .'>';
		//$output .= '<!-- item -->';

		$atts = array();
		$atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
		$atts['target'] = ! empty( $item->target )     ? $item->target     : '';
		$atts['rel']    = ! empty( $item->xfn )        ? $item->xfn        : '';
		$atts['href']   = ! empty( $item->url )        ? $item->url        : '';
		$atts['class'] = 'pmm-mega-menu-link'; // set class for link.

		// Filters the HTML attributes applied to a menu item's anchor element.
		$atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );
print_r($item);
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
	

/*
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
*/
	
	protected function update_item_classes($classes) {
    	if (empty($classes))
    	    return $classes;
    	    
        return str_replace('menu-item', 'pmm-mega-menu-item', $classes);
	}
	
	protected function is_new_item_column($item) {
        if ($item->pmm_row == 0 && $item->pmm_row_column == 0 && $item->pmm_order == 0) :
            $this->current_column = $item->pmm_column;
            $this->current_row = ''; // reset to force new row.    	
        
            return true;
        else :
            return false;
        endif;
	}

	protected function is_new_item_row($item) {
        if ($this->current_column == $item->pmm_column && $this->current_row != $item->pmm_row) :
            $this->current_row = $item->pmm_row; 
        
            return true;
        else :
            return false;
        endif;   	
	}
	
	protected function is_new_row_column($item) {
        if ($item->pmm_column == $this->current_column && $item->pmm_order == 0) :
            $this->current_row_column = $item->pmm_row_column;
            
            return true;
        else:
            return false;
        endif;    	
	}
		
	protected function get_total_columns($args, $item) {
    	$item_parent_id = $item->menu_item_parent;
        $menu_items = wp_get_nav_menu_items($args->menu->term_id);
        $sub_menu_items = array();
        $columns = array();
 
        // get sub nav items.
        foreach ($menu_items as $menu_item) :       
            if ($menu_item->pmm_nav_type == 'subnav' && $menu_item->menu_item_parent == $item_parent_id) :
                $sub_menu_items[] = $menu_item;
            endif;
        endforeach;
       
        if (empty($sub_menu_items))
            return 0;

        // get columns.
        foreach ($sub_menu_items as $item) :
            $columns[] = $item->pmm_column;
        endforeach;
        
        $columns = array_unique($columns);
       
        return count($columns);   	
	}
	
	protected function get_total_row_columns($args, $item) {
    	$item_parent_id = $item->menu_item_parent;
        $menu_items = wp_get_nav_menu_items($args->menu->term_id);
        $sub_menu_items = array();
        $columns = array();
 
        // get sub nav items.
        foreach ($menu_items as $menu_item) :       
            if ($menu_item->pmm_nav_type == 'subnav' && $menu_item->menu_item_parent == $item_parent_id && $menu_item->pmm_column == $item->pmm_column && $menu_item->pmm_row == $item->pmm_row) :
                $sub_menu_items[] = $menu_item;
            endif;
        endforeach;
       
        if (empty($sub_menu_items))
            return 0;

        // get columns.
        foreach ($sub_menu_items as $item) :
            $columns[] = $item->pmm_row_column;
        endforeach;
        
        $columns = array_unique($columns);
       
        return count($columns);  
	} 
    
}
