jQuery(document).ready(function($) {
   
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
        var colMarginRight = parseInt($('.pmm-column').css('margin-right'));
        var colExtraSpace = parseInt($('.pmm-column').css('padding-left')) + parseInt($('.pmm-column').css('padding-right')) + parseInt($('.pmm-column').css('margin-right'));
        
        colExtraSpace = colExtraSpace - (colMarginRight/totalCols); // last col no margin.

        $('.pmm-column').each(function() {
           $(this).css('width', colWidthPerc).css('width', '-=' + colExtraSpace + 'px'); 
        });
        
        adjustItemsWidth();
    };
    
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
        
        // make primary navigation items sortable.
        $( '#pmm-menu-main-navigation' ).sortable({
            items: '.pmm-item',
            placeholder: 'pmm-main-navigation-item-placeholder',           
            receive: function(event, ui) {
                // append edit if need be.
                if (!$(ui.helper).hasClass('add-submenu')) {
                    $(ui.helper).addClass('add-submenu');            
                }
                setNavigationItemID($(ui.helper), ui.item.index());
                addPrimaryNavItemActions($(ui.helper));             
            },
            stop: function(event, ui) {
                // setup our id here.                               
                updateNavigationItemIDs();                     
            }        
        }).disableSelection(); 
        
        // make column (blocks) sortable.
        $( '.pmm-column' ).sortable({
            items: '.pmm-block',
            connectWith: '.pmm-column',
            placeholder: 'pmm-block-placeholder',
            stop: function(event, ui) {
                updateBlockIds();                
            }            
        }).disableSelection();  
        
        // make block (items) sortable.
        $( '.pmm-block' ).sortable({
            items: '.pmm-item',
            connectWith: '.pmm-block',
            placeholder: 'item-placeholder',
            receive: function(event, ui) {
                // append edit if need be.
                if (!$(ui.helper).hasClass('editable')) {
                    $(ui.helper).addClass('editable');            
                }
         
                addItemHiddenFields($(ui.helper)); // adds hidden fields to the item
                addItemActions($(ui.helper)); // add action icons. 
                setItemId(ui); // set item id.
                addItemPrimaryNavID($(ui.helper)); // adds the submenu id.         
            },           
            stop: function(event, ui) {
                updateItemIds(); // update all item ids.
                updateItemsHiddenFields(); // update all items hidden.
            }
        }).disableSelection();               
    };
    
    // allows us to rerun our draggables.
    var refreshDraggable = function() {
        // list items are draggable to blocks.
        $( '.pmm-menu-items-list .pmm-item-list .pmm-item' ).draggable({
            connectToSortable: '.pmm-block, #pmm-menu-main-navigation',
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
    
    // if there is an existing menu, tweak what has been loaded.
    var loadMenu = function() {
        pmmMegaMenuAJAX.loadMenu(function(response) {            
            if (response.success == true) {
                setupExistingMenu(response.data); // we have a menu.
            } else {
                // setup default menu
console.log('load new menu');
                //pmmMegaMenu.addColumn();
                //pmmMegaMenu.manualAddBlock(0, 0);
            }
        });        
    };

    // load menu locations info.
    var loadMenuLocations = function() {
        pmmMegaMenuAJAX.loadMenuLocations(function(response) {            
            $('.pmm-menu-locations').html(response);
        });        
    };
    
    // setup an existing menu.
    var setupExistingMenu = function(primaryNavHTMl) {
        setupPrimaryNavHTML(primaryNavHTMl);
    };
    
    // loads and setups submenu items.
    var setupExistingSubMenu = function(submenuHTML) {
        // append the submenu.
        $('#pmm-menu-grid').append(submenuHTML); 
        
        // updated col width.
        updateColumnWidth();      
             
        // add column actions.
        $('#pmm-menu-grid .pmm-column').each(function() {
            addColumnActions($(this).attr('id'));
        });
        
        // get all items and loop through to add uid and update options.
        $('#pmm-menu-grid .pmm-block').each(function() {
            
            // we need this sub loop to get proper index.
            $(this).find('.pmm-item').each(function(i) {
                $el = $(this);

                var blockId = getID($el.parent().attr('id')).join('-');
                var itemId = 'pmm-item-' + blockId + '-' + i;
                
                $el.addClass('editable');
                $el.attr('id', itemId); // update id.
                $el.attr('uid', uniqueID()); // add unique id.
                addItemHiddenFields($el); // adds hidden fields.
                updateItemOptions($el); // update fields/options. 
                addItemActions($el); // add action icons.
                addItemPrimaryNavID($el); // adds the submenu id.
            });
            
            addBlockActions($(this).attr('id'));
        }); 
        
        // update all item ids and subsequent hidden fields.
        updateItemIds();       
        updateItemsHiddenFields();
        
        // sets up our sortables, draggables, etc.
        updateColumnWidth();
        refreshSortables(); 
        refreshDraggable(); 
        
        hideAJAXLoader(); // hide ajax loader.
    };
    
    // take our basic html and add our classes and actions to it.
    var setupPrimaryNavHTML = function(html) {
        var $primaryNav = $('#pmm-menu-main-navigation');
        
        // we need to append the html, then update it. we will keep it hidden unti it's gtg?!
        $primaryNav.hide();
        $primaryNav.append(html);
        
        $primaryNav.find('.pmm-item').each(function(i) {
            $(this).addClass('add-submenu');
            
            setNavigationItemID($(this), i);
            addPrimaryNavItemActions($(this));
        });
        
        refreshSortables(); 
        
        $primaryNav.show();     
    };
    
    // sets the actual item width.
    var setItemWidth = function($el) {
        var fullWidth = $el.parent().width();
        var itemPadding = parseInt($el.css('padding-right')) + parseInt($el.css('padding-left'));

        $el.width(fullWidth - itemPadding);        
    };
    
    // sets the id of our item within a block.
    var setItemId = function(ui) {
        var $el = $(ui.helper);
        var blockId = getID($el.parent().attr('id')).join('-');
        var itemId = 'pmm-item-' + blockId + '-' + ui.item.index();

        // set id.
        $el.attr('id', itemId);
        
        // set unique id.
        $el.attr('uID', uniqueID());
        
        // update fields/options.
        updateItemOptions($el);
    };
    
    // set primary navigation item id.
    var setNavigationItemID = function($el, index) {      
        var itemId = 'pmm-navigation-item-' + index;
        var uID = uniqueID();
    
        $el.attr('id', itemId); // set id.
        $el.addClass('pmm-navigation-item'); // also add a class.
        $el.attr('uID', uID); // set unique id.
        
        // add hidden fields.
        addPrimaryNavHiddenFields($el, index);
        
        // update fields/options.
        updateItemOptions($el);       
    };
    
    // adds hidden fields to primary nav.
    var addPrimaryNavHiddenFields = function($el, order) {
        var fields = {
            'nav_type': 'primary',
            'order': order,
            'block': '',
            'column': '',
            'primary_nav': '',
        };

        $.each(fields, function(name, value) {         
            $('<input>').attr({
                type: 'hidden',
                id: name,
                name: name,
                value: value
            }).appendTo($el);
        });        
    };
    
    // update primary nav ids.
    var updateNavigationItemIDs = function() {
        var pattern = /.*-/g;
        
        $('.pmm-navigation-item').each(function(index) {
            var uID = $(this).attr('uid');
            var baseID = $(this).attr('id').match(pattern)[0];
            
            $(this).attr('id', baseID + index); // update id.
            $(this).find('input[name="pmm_menu_items[' + uID + '][order]"]').val(index); // update order value.
        });        
    };
    
    // update all item ids.
    var updateItemIds = function() {
        var pattern = /.*-/g;
        
        $('.pmm-block').each(function(blockIndex) {
            var $block = $(this);
            
            $block.find('.pmm-item').each(function(itemIndex) {
                var uId = $(this).attr('uId');
                var baseId = $(this).attr('id').match(pattern)[0];

                $(this).attr('id', baseId + itemIndex);                
            });           
        });
    };
    
    // update column, block and order (pos).
    var updateItemsHiddenFields = function() {
        $('.pmm-block .pmm-item').each(function() {
            var uID = $(this).attr('uId');
            var itemLocation = getID($(this).attr('id')); // returns array [col, block, pos]
    
            $(this).find('input[name="pmm_menu_items[' + uID + '][column]"]').val(itemLocation[0]);
            $(this).find('input[name="pmm_menu_items[' + uID + '][block]"]').val(itemLocation[1]);
            $(this).find('input[name="pmm_menu_items[' + uID + '][order]"]').val(itemLocation[2]);          
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

    // update column ids.
    var updateColumnIDs = function() {      
        $('.pmm-column').each(function(colIndex) {
            var $col = $(this); 
            var colID = getID($col.attr('id'));
            
            $(this).attr('id', 'pmm-column-' + colID);
        });
    }; 
    
    // updates item options with the proper name.
    var updateItemOptions = function($el) {
        var uId = $el.attr('uid');

        $el.find(':input').each(function() {
            var name = $(this).attr('name');
            
            $(this).attr('name', 'pmm_menu_items' + '[' + uId + ']' + '[' + name + ']');
        });        
    };
    
    // adds hidden fields to item.
    var addItemHiddenFields = function($el) {
        var fields = ['column', 'block', 'order', 'primary_nav', 'nav_type'];
        
        $.each(fields, function(key, value) {
            $('<input>').attr({
                type: 'hidden',
                id: value,
                name: value
            }).appendTo($el);
        });
    };
    
    // clears the grid of any existing cols, blocks, etc.
    var clearGrid = function() {
        $('.pmm-menu-grid .pmm-column').remove();  
    };
    
    // adds the proper primay nav item id.
    var addItemPrimaryNavID = function($el) {
        var uID = $el.attr('uId');
        var primaryNavID = $('.pmm-navigation-item.show-submenu').attr('id');
         
        $el.find('input[name="pmm_menu_items[' + uID + '][primary_nav]"]').val(getID(primaryNavID)); // set primary nav value.
        $el.find('input[name="pmm_menu_items[' + uID + '][nav_type]"]').val('subnav'); // set type as something other than primary (subnav).        
    };

    // adds actions to the primary nav item.    
    var addPrimaryNavItemActions = function($el) {
        $('<a/>', {
            href: '',
            class: 'remove-primary-item dashicons dashicons-trash' 
        }).appendTo($el);         
    };
    
    // adds actions to the item.    
    var addItemActions = function($el) {
        $('<a/>', {
            href: '',
            class: 'remove-item dashicons dashicons-trash' 
        }).appendTo($el);         
    };
    
    // adds actions to the block. 
    var addBlockActions = function(blockId) {
        $('<div class="pmm-block-actions"><a href="#" class="remove-block dashicons dashicons-trash"></a></div>').appendTo($('#' + blockId));       
    };

    // adds actions to the column. 
    var addColumnActions = function(columnId) {       
        $('<a href="#" class="remove-column dashicons dashicons-trash"></a>').appendTo($('#' + columnId + ' .block-actions'));       
    };

    // create/display loader.
    var showAJAXLoader = function(self) {
    	var loaderContainer = jQuery( '<div/>', {
    		'class': 'pmm-admin-ajax-loader-image-container'
    	}).appendTo( self ).show();
    
    	var loader = jQuery( '<img/>', {
    		src: '/wp-admin/images/wpspin_light-2x.gif',
    		'class': 'pmm-admin-ajax-loader-image'
    	}).appendTo( loaderContainer );
    };

    // remove loader.
    var hideAJAXLoader = function() {
    	jQuery('.pmm-admin-ajax-loader-image-container').remove();
    };
    
    // variables.
    var pmmSavingSubmenu = false;

    // our mega menu function.
    var pmmMegaMenu = {
        
        init: function() {
            $(document).on('click', '#pmm-save-menu', this.saveMenu);
            $(document).on('click', '#pmm-menu-main-navigation .pmm-navigation-item', this.toggleSubmenu);
            $(document).on('click', '.pmm-navigation-item .remove-primary-item', this.removePrimaryNavItem);
            $(document).on('click', '#pmm-add-column', this.addColumnBtn);
            $(document).on('click', '.pmm-column .add-block', this.addBlock);
            $(document).on('click', '.pmm-item .remove-item', this.removeItem); 
            $(document).on('click', '.pmm-block .remove-block', this.removeBlock);
            $(document).on('click', '.pmm-column .remove-column', this.removeColumn);
            $(document).on('click', '#pickle-mega-menu-admin .notice-dismiss', this.dismissNotice);                                               
            
            loadMenu();
            loadMenuLocations();
            
            updateColumnWidth();
            refreshSortables(); 
            refreshDraggable();         
        },
        
        saveMenu: function(e) {
            e.preventDefault();
            
            showAJAXLoader('#wpcontent');
            
            // ajax to save submenu.
            pmmMegaMenuAJAX.saveMenu(function(response) {
                pmmMegaMenu.displayMessage(response);
                
                $('.pmm-menu-main-navigation').html(''); // clear.
                loadMenu(); // reload primary nav.
                loadMenuLocations(); // reload menu locations.
                
                hideAJAXLoader();
            });               
        },
        
        toggleSubmenu: function(e) {
            e.preventDefault();
            
            var closeOnly = false;
            
            // mark if currently open, we are only closing it.
            if ($(this).hasClass('show-submenu')) {
                closeOnly = true;
            }
            
            // close any open submenus.
            $('.pmm-menu-main-navigation .pmm-navigation-item').each(function() {
                if ($(this).hasClass('show-submenu')) {
                    $(this).removeClass('show-submenu');
                    pmmMegaMenu.closeSubmenu(getID($(this).attr('id')));
                }    
            });
            
            // open new submenu. check that it's not already open.
            if (closeOnly === true)
                return;
                
            $(this).addClass('show-submenu');                
            pmmMegaMenu.openSubmenu($(this));
        },
        
        openSubmenu: function($el) {           
            $('.pmm-menu-grid').show(); // show grid.
            
            pmmMegaMenu.loadSubmenu(getID($el.attr('id'))); // get the submenu.
        },
        
        closeSubmenu: function(id) {           
            pmmSavingSubmenu = true;
            showAJAXLoader('#wpcontent');
              
            // ajax to save submenu.
            pmmMegaMenuAJAX.saveSubMenu(id, function(response) {
                pmmMegaMenu.displayMessage(response);

                clearGrid(); // empty grid.
                $('.pmm-menu-grid').hide(); // hide grid.

                pmmSavingSubmenu = false;
                
                hideAJAXLoader();
            });       
        },
        
        loadSubmenu: function(submenuID) {
            // make sure we are not currently saving another submenu.
            if (pmmSavingSubmenu) {
                setTimeout(function() {
                    pmmMegaMenu.loadSubmenu(submenuID);
                }, 1000);
                
                return;
            }
            
            showAJAXLoader('#wpcontent');
            
            // ajax to get submenu.
            pmmMegaMenuAJAX.loadSubMenu(submenuID, function(response) {
                if (response.success == true) {                                     
                    setupExistingSubMenu(response.data); // we have a sub menu.
                } else {
                    // setup default menu
                    pmmMegaMenu.addColumn();
                    pmmMegaMenu.manualAddBlock(0, 0);
                    
                    $('.pmm-menu-grid').show(); // show grid.
                    
                    updateColumnWidth();
                    
                    hideAJAXLoader();
                }
            });           
        },
        
        addColumn: function() {            
            var colNum=$('.pmm-column').length;
            var colID = 'pmm-column-' + colNum;
            
            $('<div id="' + colID +'" class="pmm-column"><div class="block-actions"><div class="add-block-wrap"><a href="#" class="add-block">Add Block</a></div></div></div>').appendTo('#pmm-menu-grid'); 
            
            // add actions.
            addColumnActions(colID);
            
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
            
            addBlockActions('pmm-block-' + colIdNum + '-' + order);
            
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
        },

        addColumnBtn: function(e) {
            if (typeof e !== 'undefined') {
                e.preventDefault();
            } 
            
            pmmMegaMenu.addColumn();                    
        },
        
        removeItem: function(e) {
            e.preventDefault();
            
            $(this).parents('.pmm-item').remove();          
        },
        
        removePrimaryNavItem: function(e) {
            showAJAXLoader('#wpcontent');
            
            e.stopPropagation();
            e.preventDefault();
            
            var $item = $(this).parents('.pmm-navigation-item');
            var itemuID = $item.attr('uid');
            var itemDBid = $('input[name="pmm_menu_items[' + itemuID + '][id]"]').val();
            
            $item.remove(); // remove item from screen.
            
            pmmMegaMenuAJAX.removeSubMenu(getID($item.attr('id')), itemDBid, function(response) {            
                pmmMegaMenu.displayMessage(response);
                
                hideAJAXLoader();
            });                    
        },
        
        removeBlock: function(e) {
            e.preventDefault();
           
            $(this).parents('.pmm-block').remove();          
        },        

        removeColumn: function(e) {
            e.preventDefault();
           
            $(this).parents('.pmm-column').remove(); 
            
            updateColumnIDs();
            updateColumnWidth();         
        },
        
        displayMessage: function(message) {
            $('#pickle-mega-menu-admin .menu-items-wrap').prepend(message);
        },
        
        dismissNotice: function() {
            $(this).parent().remove();
        }
        
    };

    pmmMegaMenu.init();
    
});