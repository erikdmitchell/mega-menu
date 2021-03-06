<?php
// The menu id of the current menu being edited
$nav_menu_selected_id = isset( $_REQUEST['menu'] ) ? (int) $_REQUEST['menu'] : 0;

// Nav menu object
$nav_menu_object = wp_get_nav_menu_object($nav_menu_selected_id);
$nav_menu_name = isset($nav_menu_object->name) ? $nav_menu_object->name : '';
                            

?>


<div id="pickle-mega-menu-admin">
    
    <div class="pmm-manage-menus">
        
        <form name="select_menu" id="select-menu" action="" method="post">
            <?php wp_nonce_field('pmm-select-menu', 'pmm_admin'); ?>
            
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
    
    <div class="menu-items-wrap">

        <div class="menu-items-column">
            
            <form>
                
                <div class="menu-items-container">
                    <h3>Items</h3>
                    
                    <div class="pmm-menu-items-list">
                        
                        <?php PickleMegaMenu()->admin->items_accordian(); ?>
    
                    </div>
                    
                </div>
                
            </form>
            
        </div>
        
        <div class="menu-management-column">
            
            <form name="save_menu" id="save-menu" action="" method="post">       
                
                <div class="menu-management">
                    
                    <h3>Menu</h3>
                    
                    <div class="menu-options">
                        <?php wp_nonce_field('pmm_save_menu', 'pmm_admin'); ?>
                        
                        <div class="menu-name">
                            <label for="menu-name">Menu Name</label>
                            <input type="text" name="menu_name" id="menu-name" placeholder="Menu Name" value="<?php echo $nav_menu_name; ?>" />
                        </div>
                        
                        <span class="save-menu-button">
                            <a href="#" class="button button-primary" id="pmm-save-menu">Save Menu</a>
                        </span> 
                    </div>  
                    
                    <div class="pmm-menu-grid-wrap">
                        
                        <div class="description">Drag the primary navigation items. Click the arrow to open and close the submenu.</div> 
                        
                        <div id="pmm-menu-main-navigation" class="pmm-menu-main-navigation"></div> 
                                  
                        <div id="pmm-menu-grid" class="pmm-menu-grid">
                            
                            <div class="menu-columns">
                                <a href="#" id="pmm-add-column" class="button">Add Column</a>
                            </div> 
                            
                        </div>
                    </div>
    
                </div>
                
                <input type="hidden" name="menu_id" id="menu-id" value="<?php echo $nav_menu_selected_id; ?>" />
                
                <div class="pmm-menu-locations"></div>
            
            </form>
            
        </div>
    
    </div>
    
</div>