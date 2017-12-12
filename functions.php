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

/*
function pmm_do_accordion_sections( $screen, $context, $object ) {
    global $wp_meta_boxes;
echo '<pre>';
print_r($wp_meta_boxes);
echo '</pre>'; 
    wp_enqueue_script( 'accordion' );
 
    if ( empty( $screen ) )
        $screen = get_current_screen();
    elseif ( is_string( $screen ) )
        $screen = convert_to_screen( $screen );

    $page = $screen->id;
 
    $hidden = get_hidden_meta_boxes( $screen );
    ?>
    <div id="side-sortables" class="accordion-container">
        <ul class="outer-border">
    <?php
    $i = 0;
    $first_open = false;
 
    if ( isset( $wp_meta_boxes[ $page ][ $context ] ) ) {
        foreach ( array( 'high', 'core', 'default', 'low' ) as $priority ) {
            if ( isset( $wp_meta_boxes[ $page ][ $context ][ $priority ] ) ) {
                foreach ( $wp_meta_boxes[ $page ][ $context ][ $priority ] as $box ) {
                    if ( false == $box || ! $box['title'] )
                        continue;
                    $i++;
                    $hidden_class = in_array( $box['id'], $hidden ) ? 'hide-if-js' : '';
 
                    $open_class = '';
                    if ( ! $first_open && empty( $hidden_class ) ) {
                        $first_open = true;
                        $open_class = 'open';
                    }
                    ?>
                    <li class="control-section accordion-section <?php echo $hidden_class; ?> <?php echo $open_class; ?> <?php echo esc_attr( $box['id'] ); ?>" id="<?php echo esc_attr( $box['id'] ); ?>">
                        <h3 class="accordion-section-title hndle" tabindex="0">
                            <?php echo esc_html( $box['title'] ); ?>
                            <span class="screen-reader-text"><?php _e( 'Press return or enter to open this section' ); ?></span>
                        </h3>
                        <div class="accordion-section-content <?php postbox_classes( $box['id'], $page ); ?>">
                            <div class="inside">
                                <?php call_user_func( $box['callback'], $object, $box ); ?>
                            </div><!-- .inside -->
                        </div><!-- .accordion-section-content -->
                    </li><!-- .accordion-section -->
                    <?php
                }
            }
        }
    }
    ?>
        </ul><!-- .outer-border -->
    </div><!-- .accordion-container -->
    <?php
    return $i;
}
*/