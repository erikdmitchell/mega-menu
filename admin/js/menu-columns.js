jQuery(document).ready(function($) {
 
   $('#pickle-mega-menu-admin .columns-list li a').on('click', function(e) {
      e.preventDefault();
      
      var cols = $(this).data('cols');
      
console.log(cols);       
   });
    
});

$( function() {
    
    $( '.sortable-list' ).sortable({
        connectWith: '.sortable-list',
        placeholder: 'placeholder',
        update: function(event, ui) {
            // update
        },
        start: function(event, ui) {           
            if(ui.helper.hasClass('second-level')){
                ui.placeholder.addClass('second-level');
            }
            else{ 
                ui.placeholder.removeClass('second-level');
            }
        },
        sort: function(event, ui) {
            var basePos = $(this).parent().position().left;
            var pos = ui.position.left - basePos;
                        
            if (ui.helper.hasClass('second-level')) {
                pos+=20; 
            }

            if (pos >= 32 && !ui.helper.hasClass('second-level')) {
                ui.placeholder.addClass('second-level');
                ui.helper.addClass('second-level');
            } else if (pos < 25 && ui.helper.hasClass('second-level')) {
                ui.placeholder.removeClass('second-level');
                ui.helper.removeClass('second-level');
            }
        },
        stop: function(event, ui) {
            // first item has to be top level //
            if (ui.item.index() == 0) {
                ui.item.removeClass('second-level');
            }
        }
    }).disableSelection();

} );