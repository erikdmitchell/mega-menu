<?php
    
function pmm_menu_list_dropdown($selected='', $name='pmm_menu_id', $echo=true) {
    $html='';
    $menus=wp_get_nav_menus();
    
    $html.='<select name="'.$name.'" id="'.$name.'">';
        
        foreach ($menus as $menu) :
        
            $html.='<option value="'.$menu->term_id.'" '.selected($selected, $menu->term_id, 0).'>'.$menu->name.'</option>';
        
        endforeach;
        
    $html.='</select>';
            
    if ($echo) :
        echo $html;
    else :
        return $html;
    endif;
}

function pmm_get_nav_menu_items($items, $menu, $args) {   
    foreach ($items as $item) :
        $item->pmm_column = get_post_meta($item->ID, '_pmm_menu_item_column', true);
        $item->pmm_row = get_post_meta($item->ID, '_pmm_menu_item_row', true);
        $item->pmm_order = get_post_meta($item->ID, '_pmm_menu_item_order', true);
        $item->pmm_item_type = get_post_meta($item->ID, '_pmm_menu_item_type', true);
        $item->pmm_nav_type = get_post_meta($item->ID, '_pmm_menu_nav_type', true);
        $item->pmm_menu_primary_nav = get_post_meta($item->ID, '_pmm_menu_primary_nav', true);                        
    endforeach;
    
    return $items;
}
add_filter('wp_get_nav_menu_items', 'pmm_get_nav_menu_items', 10, 3);

// adds our walker to the class if setup properly
function pmm_nav_walker_override($sorted_menu_items, $args) {
    // verify is megau menu?!
    // $args->menu will have the info
    if ($args->theme_location != 'primary')
        return $sorted_menu_items;
    
    // append our walker and tweak args.
    $args->walker = new PMM_Nav_Walker();
    $args->menu_class = 'pmm-mega-menu';

    return $sorted_menu_items;
}
add_filter('wp_nav_menu_objects', 'pmm_nav_walker_override', 10, 2);
 
/* Similar to wp_parse_args() just a bit extended to work with multidimensional arrays :) */
function pmm_wp_parse_args( &$a, $b ) {
	$a = (array) $a;
	$b = (array) $b;
	$result = $b;
	foreach ( $a as $k => &$v ) {
		if ( is_array( $v ) && isset( $result[ $k ] ) ) {
			$result[ $k ] = pmm_wp_parse_args( $v, $result[ $k ] );
		} else {
			$result[ $k ] = $v;
		}
	}
	return $result;
}
