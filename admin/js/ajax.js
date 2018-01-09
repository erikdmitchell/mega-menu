// our mega menu function ajax.
var pmmMegaMenuAJAX = {
            
    loadMenu: function(callback) {           
        var data = {
            'action': 'pmm_load_menu',
            'id': $('#pickle-mega-menu-admin #menu-id').val()
        };
       
        $.post(ajaxurl, data, function(response) {
            callback(response);
        });
    },

    loadSubMenu: function(id, callback) {           
        var data = {
            'action': 'pmm_load_submenu',
            'menu_id': $('#pickle-mega-menu-admin #menu-id').val(),
            'sub_nav_id': id
        };
     
        $.post(ajaxurl, data, function(response) {            
            callback(response);
        });
    },

    saveMenu: function(callback) {           
        var data = {
            'action': 'pmm_save_menu',
            'id': $('#pickle-mega-menu-admin #menu-id').val(),
            'form': $('form#save-menu').serialize()
        };
     
        $.post(ajaxurl, data, function(response) {            
            callback(response);
        });
    },

    saveSubMenu: function(id, callback) {           
        var data = {
            'action': 'pmm_save_submenu',
            'menu_id': $('#pickle-mega-menu-admin #menu-id').val(),
            'sub_nav_id': id,
            'form': $('form#save-menu').serialize()
        };
     
        $.post(ajaxurl, data, function(response) {            
            callback(response);
        });
    }
    
};