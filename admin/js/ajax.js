// our mega menu function ajax.
var pmmMegaMenuAJAX = {        
    loadMenu: function(callback) {           
        var data = {
            'action': 'pmm_load_menu',
            'id': $('#pickle-mega-menu-admin #menu-id').val()
        };
       
        $.post(ajaxurl, data, function(response) {
            // return something as part of callback - returns html string
            callback(response);
        });
    },
    
};