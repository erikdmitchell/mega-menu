jQuery(document).ready(function($) {
 
   $('#pickle-mega-menu-admin .columns-list li a').on('click', function(e) {
      e.preventDefault();
      
      var cols = $(this).data('cols');
      
console.log(cols);       
   });
   
   $(document).on('click', '.pmm-block .pmm-item .edit-item', function(e) {
       e.preventDefault();
       
       $(this).parent().find('.options').toggle();
   });

});


$( function() {

    // make column (blocks) sortable.
    $( '.pmm-column' ).sortable({
        connectWith: '.pmm-column',
        placeholder: 'pmm-block-placeholder',
    }).disableSelection();
    
    // make block (items) sortable.
    $( '.pmm-block' ).sortable({
        connectWith: '.pmm-block',
        placeholder: 'item-placeholder',
        receive: function(event, ui) {
            // append edit if need be
            if (!$(ui.helper).hasClass('editable')) {
                $(ui.helper).addClass('editable');            
            }
        }
    }).disableSelection();

} );

$( function() {
    
    // list items are draggable to blocks.
    $( '.pmm-menu-items-list .pmm-item-list .pmm-item' ).draggable({
        connectToSortable: '.pmm-block',
        'helper': 'clone',
        revert: 'invalid',
        start: function(event, ui) {
            //$(ui.helper).css('width', '100%')
        },
        drag: function(event, ui) {
            //$(ui.helper).css('width', 'atuo')
        },
        stop: function(event, ui) {
            $(ui.helper).css('width', $(ui.helper).parent().width()); // on drop, set column width                   
        }        
    });
    
} );
