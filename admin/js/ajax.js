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
            'id': id
        };
     
        $.post(ajaxurl, data, function(response) {
            callback(response);
        });
    },
    
};