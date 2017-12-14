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
        
        <form name="save_menu" id="save-menu" action="" method="post">       
            
            <div class="menu-management">
                
                <h3>Menu</h3>
                
                <div class="menu-options">
                    <?php wp_nonce_field('pmm_save_menu', 'pmm_admin'); ?>
                    
                    <div class="menu-name">
                        <label for="menu-name">Menu Name</label>
                        <input type="text" name="menu_name" id="menu-name" placeholder="Menu Name" value="<?php echo $nav_menu_object->name; ?>" />
                    </div>
                    
                    <div class="menu-columns">
                        <a href="#" id="pmm-menu-columns-selector"><i class="fa fa-columns" aria-hidden="true"></i></a>
                        
                        <div class="columns-dropdown">
                            <ul class="columns-list">
                                <li><a href="#" data-cols="1">1</a></li>
                                <li><a href="#" data-cols="2">2</a></li>
                                <li><a href="#" data-cols="3">3</a></li>
                            </ul> 
                        </div>
                    </div> 
                    
                    <span class="save-menu-button">
                        <input type="submit" class="button button-primary" value="Save Menu">
                    </span> 
                </div>  
                                
                <div id="pmm-menu-grid" class="">

                    <div id="column-1" class="column">
                        <ul class="sortable-list">
                            <li class="item">Item 1<ol></ol></li>
                            <li class="item">Item 2<ol></ol></li>
                            <li class="item">Item 3<ol></ol></li>                                               
                            <li class="item">Item 4<ol></ol></li>
                        </ul>
                    </div>

                    <div id="column-2" class="column">
                        <ul class="sortable-list">
                            <li class="item">Item 5</li>
                            <li class="item">Item 6</li>
                            <li class="item">Item 7</li>                                                
                        </ul>
                    </div>

                </div>

                    
                

            </div>
            
            <input type="hidden" name="menu_id" id="menu-id" value="<?php echo $nav_menu_selected_id; ?>" />
            
        </form>
        
    </div>
    
</div>