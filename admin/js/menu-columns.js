jQuery(document).ready(function($) {
 
   $('#pickle-mega-menu-admin .columns-list li a').on('click', function(e) {
      e.preventDefault();
      
      var cols = $(this).data('cols');
      
console.log(cols);       
   });

});


$( function() {

    // make block sortable.
/*
    $( '#pmm-menu-grid .cloumn' ).sortable({
        connectWith: '#pmm-menu-grid .column',
        placeholder: 'placeholder',
    }).disableSelection();
*/
    
    // make block sortable.
    $( '.pmm-block' ).sortable({
        connectWith: '.pmm-block',
        placeholder: 'placeholder',
    }).disableSelection();

} );

$( function() {
    
    // list items are draggable to blocks.
    $( '.pmm-menu-items-list .item-list .item' ).draggable({
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
            $(ui.helper).css('width', '100%')
        }        
    });
    
} );
