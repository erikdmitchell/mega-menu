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

jQuery( function($) {

    // sets all columns to equal width.
    var updateColumnWidth = function() {
        var totalCols = $('.pmm-column').length;
        var colWidthPerc = (100 / totalCols) + '%';
        var colExtraSpace = parseInt($('.pmm-column').css('padding-left')) + parseInt($('.pmm-column').css('padding-right')) + parseInt($('.pmm-column').css('margin-right'));

        $('.pmm-column').each(function() {
           $(this).css('width', colWidthPerc).css('width', '-=' + colExtraSpace + 'px'); 
        });
        
        adjustItemsWidth();
    }
    
    // gets id from an id string.
    var getID = function(string) {
        var pattern = /\d/;

        return string.match(pattern)[0];
    };
    
    // allows us to rerun our sortables.
    var refreshSortables = function() {
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
    };
    
    // allows us to rerun our draggables.
    var refreshDraggable = function() {
        // list items are draggable to blocks.
        $( '.pmm-menu-items-list .pmm-item-list .pmm-item' ).draggable({
            connectToSortable: '.pmm-block',
            'helper': 'clone',
            revert: 'invalid',
            start: function(event, ui) {},
            drag: function(event, ui) {},
            stop: function(event, ui) {
                setItemWidth($(ui.helper)); // on drop, set item width                   
            }        
        });        
    };
    
    // sets item width to block width.
    var adjustItemsWidth = function() {
        $('#pmm-menu-grid .pmm-column .pmm-item').each(function() {
           setItemWidth($(this));
        });
    };
    
    // adds a default column and block on empty menu setup.
    var setupDefaults = function() {
        
        if (!$('#pmm-menu-grid .pmm-column').length) {
            pmmMegaMenu.addColumn();
            pmmMegaMenu.manualAddBlock(1, 1);          
        }
        
    };
    
    // sets the actual item width.
    var setItemWidth = function($el) {
        var fullWidth = $el.parent().width();
        var itemPadding = parseInt($el.css('padding-right')) + parseInt($el.css('padding-left'));

        $el.width(fullWidth - itemPadding);        
    }

    // our mega menu function.
    var pmmMegaMenu = {
        init: function() {
            $(document).on('click', '#pmm-add-column', this.addColumn);
            $(document).on('click', '.pmm-column .add-block', this.addBlock);
            
            setupDefaults();
            
            updateColumnWidth();
            refreshSortables(); 
            refreshDraggable();           
        },
        
        addColumn: function(e) {
            if (typeof e !== 'undefined') {
                e.preventDefault();
            }           
            
            var colId=$('.pmm-column').length + 1;
            
            $('<div id="pmm-column-' + colId + '" class="pmm-column"><div class="block-actions"><div class="add-block-wrap"><a href="#" class="add-block">Add Block</a></div></div></div>').appendTo('#pmm-menu-grid'); 
            
            // update column width
            updateColumnWidth();                     
        },
        
        addBlock: function(e) {
            if (typeof e !== 'undefined') {
                e.preventDefault();
            }
          
            var $col = $(this).parents('.pmm-column');
            var colIdNum = getID($col.attr('id'));
            var order = $col.find('.pmm-block').length +1;

            $('<div/>', {
               id: 'pmm-block-' + colIdNum + '-' + order,
               class: 'pmm-block' 
            }).appendTo($col);
            
            refreshSortables();
            refreshDraggable();    
        },
        
        manualAddBlock: function(colIdNum, order) {
            $col=$('#pmm-column-' + colIdNum);

            $('<div/>', {
               id: 'pmm-block-' + colIdNum + '-' + order,
               class: 'pmm-block' 
            }).appendTo($col);
            
            refreshSortables();
            refreshDraggable();            
        }
        
    };

    pmmMegaMenu.init();
    
});