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