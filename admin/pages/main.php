<?php
// The menu id of the current menu being edited
$nav_menu_selected_id = isset( $_REQUEST['menu'] ) ? (int) $_REQUEST['menu'] : 0;

// Nav menu object
$nav_menu_object = wp_get_nav_menu_object($nav_menu_selected_id);

//print_r($nav_menu_object);
    
// Get existing menu locations assignments
$locations = get_registered_nav_menus();
$menu_locations = get_nav_menu_locations();
$num_locations = count( array_keys( $locations ) );
?>


<div id="pickle-mega-menu-admin">
    
    <div class="pmm-manage-menus">
        
        <form>
            
            <label for="select-menu">Select a menu to edit:</label>
            <?php pmm_menu_list_dropdown($nav_menu_selected_id); ?>
            
            <span class="submit-button">
                <input type="submit" class="button" value="Select">
            </span>
            
            <span class="add-new-button">
                <input type="button" class="button button-primary" value="Add New">
            </span>            
        </form>
        
    </div>
    
    <div class="menu-items-column">
        
        <form>
            
            <div class="menu-items-container">
                <h3>Items</h3>
                
                <div class="menu-items-list">
                    
                    <h4>Pages</h4>
                    
                   
                    
                    <form>
                    
                        <ul class="pages-list">
                            
                            <?php foreach (get_pages() as $page) : ?>
                        
                                <li id="page-<?php echo $page->ID; ?>"><label for="page_<?php echo $page->ID; ?>"><input type="checkbox" name="page[]" id="page_<?php echo $page->ID; ?>" value="<?php echo $page->ID; ?>"><?php echo $page->post_title; ?></label></li>
                        
                            <?php endforeach; ?>
                            
                        </ul>
                    
                    </form>
                    
                </div>
                
            </div>
            
        </form>
        
    </div>
    
    <div class="menu-management-column">
        
        <form name="save-menu" id="save-menu" action="" method="post">       
            
            <div class="menu-management">
                
                <h3>Menu</h3>
                
                
                    <?php wp_nonce_field('pmm_save_menu', 'pmm_admin'); ?>
                    
                    <label for="menu-name">Menu Name</label>
                    <input type="text" name="menu_name" id="menu-name" placeholder="Menu Name" value="<?php echo $nav_menu_object->name; ?>" />
                    
                     <span class="save-menu-button">
                        <input type="submit" class="button button-primary" value="Save Menu">
                    </span> 
                    
                
                
                <div id="pmm-menu-grid">
                    gRID
                </div>
                
            </div>
            
            <input type="hidden" name="menu_id" id="menu-id" value="<?php echo $nav_menu_selected_id; ?>" />
            
        </form>
        
    </div>
    
</div>

<?php if ( current_theme_supports( 'menus' ) ) : ?>

	<fieldset class="menu-settings-group menu-theme-locations">
		<legend class="menu-settings-group-name howto"><?php _e( 'Display location' ); ?></legend>
		<?php foreach ( $locations as $location => $description ) : ?>
		<div class="menu-settings-input checkbox-input">
			<input type="checkbox"<?php checked( isset( $menu_locations[ $location ] ) && $menu_locations[ $location ] == 0 ); ?> name="menu-locations[<?php echo esc_attr( $location ); ?>]" id="locations-<?php echo esc_attr( $location ); ?>" value="<?php echo esc_attr( $nav_menu_selected_id ); ?>" />
			<label for="locations-<?php echo esc_attr( $location ); ?>"><?php echo $description; ?></label>
			<?php if ( ! empty( $menu_locations[ $location ] ) && $menu_locations[ $location ] != 0 ) : ?>
				<span class="theme-location-set"><?php
					/* translators: %s: menu name */
					printf( _x( '(Currently set to: %s)', 'menu location' ),
						wp_get_nav_menu_object( $menu_locations[ $location ] )->name
					);
				?></span>
			<?php endif; ?>
		</div>
		<?php endforeach; ?>
	</fieldset>

<?php endif; ?>