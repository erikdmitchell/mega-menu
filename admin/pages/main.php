<?php
// Get existing menu locations assignments
$locations = get_registered_nav_menus();
$menu_locations = get_nav_menu_locations();
$num_locations = count( array_keys( $locations ) );
?>


<div id="pickle-mega-menu-admin">
    
    <div class="pmm-manage-menus">
        
        <form>
            
            <label for="select-menu">Select a menu to edit:</label>
            <select name="menu" id="pmm-to-edit">
                
            </select>
            
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
        
        <form>
            
            <div class="menu-management">
                
                <h3>Menu</h3>
                
                <form>
                    
                    <label for="menu-name">Menu Name</label>
                    <input type="text" name="menu_name" id="menu-name" value="" />
                    
                     <span class="save-menu-button">
                        <input type="button" class="button button-primary" value="Save Menu">
                    </span> 
                    
                </form>
                
                <div id="pmm-menu-grid">
                    gRID
                </div>
                
            </div>
            
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