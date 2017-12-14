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
                       