// our mega menu function ajax.
var pmmMegaMenuAJAX = {
            
    loadMenu: function(callback) {           
        var data = {
            'action': 'pmm_load_menu',
            'id': jQuery('#pickle-mega-menu-admin #menu-id').val()
        };
       
        jQuery.post(ajaxurl, data, function(response) {
            callback(response);
        });
    },

    loadSubMenu: function(id, callback) {           
        var data = {
            'action': 'pmm_load_submenu',
            'menu_id': jQuery('#pickle-mega-menu-admin #menu-id').val(),
            'sub_nav_id': id
        };
     
        jQuery.post(ajaxurl, data, function(response) {            
            callback(response);
        });
    },

    saveMenu: function(callback) {           
        var data = {
            'action': 'pmm_save_menu',
            'id': jQuery('#pickle-mega-menu-admin #menu-id').val(),
            'form': jQuery('form#save-menu').serialize()
        };
     
        jQuery.post(ajaxurl, data, function(response) {            
            callback(response);
        });
    },

    saveSubMenu: function(id, callback) {           
        var data = {
            'action': 'pmm_save_submenu',
            'menu_id': jQuery('#pickle-mega-menu-admin #menu-id').val(),
            'sub_nav_id': id,
            'form': jQuery('form#save-menu').serialize()
        };
     
        jQuery.post(ajaxurl, data, function(response) {            
            callback(response);
        });
    },
    
    removeSubMenu: function(id, itemID, callback) {           
        var data = {
            'action': 'pmm_delete_submenu',
            'menu_id': jQuery('#pickle-mega-menu-admin #menu-id').val(),
            'sub_nav_id': id,
            'item_id': itemID
        };
     
        jQuery.post(ajaxurl, data, function(response) {            
            callback(response);
        });
    },
    
    loadMenuLocations: function(callback) {           
        var data = {
            'action': 'pmm_load_menu_locations',
            'menu_id': jQuery('#pickle-mega-menu-admin #menu-id').val(),
        };
     
        jQuery.post(ajaxurl, data, function(response) {            
            callback(response);
        });
    },    
    
};