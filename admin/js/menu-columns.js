jQuery(document).ready(function($) {
 
   $('#pickle-mega-menu-admin .columns-list li a').on('click', function(e) {
      e.preventDefault();
      
      var cols = $(this).data('cols');
      
console.log(cols);       
   });
    
});

$( function() {
    
    var maxDepth = 200;
    var currentDepth = 0;

    $( '.sortable-list' ).sortable({
        connectWith: '.sortable-list',
        placeholder: 'placeholder',
        update: function(event, ui) {
            // update
        },
        start: function(event, ui) {
            if(ui.helper.hasClass('second-level')){
                //ui.placeholder.removeClass('placeholder');
                ui.placeholder.addClass('second-level');
            }
            else{ 
                ui.placeholder.removeClass('second-level');
                //ui.placeholder.addClass('placeholder');
            }
        },
        sort: function(event, ui) {
            var pos;
            if(ui.helper.hasClass('second-level')){
                pos = ui.position.left+20; 
                $('#cursor').text(ui.position.left+20);
            }
            else{
                pos = ui.position.left; 
                $('#cursor').text(ui.position.left);    
            }
console.log(pos);
console.log(ui.helper.offset());            
            if(pos >= 32 && !ui.helper.hasClass('second-level')){
                //ui.placeholder.removeClass('placeholder');
                ui.placeholder.addClass('second-level');
                ui.helper.addClass('second-level');
            }
            else if(pos < 25 && ui.helper.hasClass('second-level')){
                ui.placeholder.removeClass('second-level');
                //ui.placeholder.addClass('placeholder');
                ui.helper.removeClass('second-level');
            }
        }
    }).disableSelection();

} );