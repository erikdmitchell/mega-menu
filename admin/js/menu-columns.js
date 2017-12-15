jQuery(document).ready(function($) {
 
    // add a column.
    $('#pickle-mega-menu-admin .columns-list li a').on('click', function(e) {
      e.preventDefault();
      
      // add column       
   });
   
   // toggles the edit details for an item.
   $(document).on('click', '.pmm-block .pmm-item .edit-item', function(e) {
       e.preventDefault();

       $(this).parent().find('.options').slideToggle();
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
        var pattern = /[0-9]/g;
        var matches = string.match(pattern);
        
        if (matches.length == 1) {
            return matches[0];
        }

        return matches;
    };
    
    // allows us to rerun our sortables.
    var refreshSortables = function() {
        // make column (blocks) sortable.
        $( '.pmm-column' ).sortable({
            connectWith: '.pmm-column',
            placeholder: 'pmm-block-placeholder',
            stop: function(event, ui) {
                updateBlockIds();                
            }            
        }).disableSelection();  
        
        // make block (items) sortable.
        $( '.pmm-block' ).sortable({
            connectWith: '.pmm-block',
            placeholder: 'item-placeholder',
            receive: function(event, ui) {
                // append edit if need be.
                if (!$(ui.helper).hasClass('editable')) {
                    $(ui.helper).addClass('editable');            
                }
                
                addItemHiddenFields($(ui.helper));              
            },           
            stop: function(event, ui) {
                // setup our id here.                               
                setItemId(ui);                     
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
                setItemWidth($(ui.helper)); // on drop, set item width.
                $(ui.helper).height('auto'); // sets height to auto to allow for options to toggle properly.                   
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
            pmmMegaMenu.manualAddBlock(0, 0);          
        }
        
    };
    
    // sets the actual item width.
    var setItemWidth = function($el) {
        var fullWidth = $el.parent().width();
        var itemPadding = parseInt($el.css('padding-right')) + parseInt($el.css('padding-left'));

        $el.width(fullWidth - itemPadding);        
    };
    
    // sets the id of our item within a block.
    var setItemId = function(ui) {
        var $el = $(ui.item);       
        var blockId = getID($el.parent().attr('id')).join('-');
        var itemId = 'pmm-item-' + blockId + '-' + ui.item.index();
        
        $el.attr('id', itemId);
        
        // set unique id.
        $el.attr('uID', uniqueID());
        
        // update fields/options.
        updateItemOptions($el);
        
        // update all item ids.
        updateItemIds();
    };
    
    // update all item ids.
    var updateItemIds = function() {
        var pattern = /.*-/g;
        
        $('.pmm-block').each(function(blockIndex) {
            var $block = $(this);
            
            $block.find('.pmm-item').each(function(itemIndex) {
                var itemLocation = getID($(this).attr('id')); // returns array [col, block, pos]
                var uId = $(this).attr('uId');
                var baseId = $(this).attr('id').match(pattern)[0];

                $(this).attr('id', baseId + itemIndex);
                
                // update column, block and order (pos).
                $(this).find('input[name="' + uId + '[column]"]').val(itemLocation[0]);
                $(this).find('input[name="' + uId + '[block]"]').val(itemLocation[1]);
                $(this).find('input[name="' + uId + '[order]"]').val(itemLocation[2]);                
            });           
        });
    };
    
    // generates a unique id.
    var uniqueID = function() {
        return '_' + Math.random().toString(36).substr(2, 9);
    };
    
    // update block ids.
    var updateBlockIds = function() {      
        $('.pmm-column').each(function(colIndex) {
            var $col = $(this); 
            var colIdNum = getID($col.attr('id'));
           
            $col.find('.pmm-block').each(function(blockIndex) {
                $(this).attr('id', 'pmm-block-' + colIdNum + '-' + blockIndex);
            });
        });
    };    
    
    // updates item options with the proper name.
    var updateItemOptions = function($el) {
        var uId = $el.attr('uid');

        $el.find(':input').each(function() {
            var name = $(this).attr('name');
            
            $(this).attr('name', uId + '[' + name + ']');
        });        
    };
    
    // adds hidden fields to item.
    var addItemHiddenFields = function($el) {
        var fields = ['column', 'block', 'order'];
        
        $.each(fields, function(key, value) {
            $('<input>').attr({
                type: 'hidden',
                id: value,
                name: value
            }).appendTo($el);
        });
    };

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
            
            var colId=$('.pmm-column').length;
            
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
            var order = $col.find('.pmm-block').length;

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