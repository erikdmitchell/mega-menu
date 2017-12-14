jQuery(document).ready(function($) {
 
    // add a column.
    $('#pickle-mega-menu-admin .columns-list li a').on('click', function(e) {
      e.preventDefault();
      
      // add column       
   });
   
   // toggles the edit details for an item.
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



jQuery( function($) {

    var updateColumnWidth = function() {
        var totalCols = $('.pmm-column').length;
        var colWidthPerc = (100 / totalCols) + '%';
        var colExtraSpace = parseInt($('.pmm-column').css('padding-left')) + parseInt($('.pmm-column').css('padding-right')) + parseInt($('.pmm-column').css('margin-right'));

        $('.pmm-column').each(function() {
           $(this).css('width', colWidthPerc).css('width', '-=' + colExtraSpace + 'px'); 
        });
    }

    var pmmMegaMenu = {
        init: function() {
            $(document).on('click', '#pmm-add-column', this.addColumn);
        },
        
        addColumn: function(e) {
            e.preventDefault();
            
            var colId=$('.pmm-column').length + 1;
            
            
            
            $('<div/>', {
                class: 'pmm-column',
                id: 'pmm-column-'.colId
            }).appendTo('#pmm-menu-grid'); 
            
            // update column width
            updateColumnWidth();                     
        },
        
    };

    pmmMegaMenu.init();
    
});