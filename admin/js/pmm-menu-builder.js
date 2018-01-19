jQuery(document).ready(function($) {
   
   // toggles the edit details for an item.
   $(document).on('click', '.pmm-row .pmm-item .edit-item', function(e) {
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
        updateSubmenuColumnWidth();
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
        
        // make column (rows) sortable.
        $( '.pmm-column' ).sortable({
            items: '.pmm-row',
            connectWith: '.pmm-column',
            placeholder: 'pmm-row-placeholder',
            stop: function(event, ui) {
                updateRowIds();                
            }            
        }).disableSelection(); 
        
        // make row columns sortable.
        $( '.pmm-row' ).sortable({
            items: '.pmm-row-column',
            connectWith: '.pmm-row',
            placeholder: 'pmm-row-column-placeholder',
            stop: function(event, ui) {
                updateRowColumnIds();
                updateSubmenuColumnWidth()                
            }            
        }).disableSelection();          
        
        // make row (items) sortable.
        $( '.pmm-row-column' ).sortable({
            items: '.pmm-item',
            connectWith: '.pmm-row-column',
            placeholder: 'item-placeholder',
            receive: function(event, ui) {
                var $el = $(ui.helper);
                
                // if item is on grid, helper will be null.
                if ($el.length == 0) {
                    $el = $(ui.item);                   
                }
                             
                // append edit if need be.
                if (!$el.hasClass('editable')) {
                    $el.addClass('editable');            
                }
        
                addItemHiddenFields($el); // adds hidden fields to the item               
                addItemActions($el); // add action icons.               
                setItemID($el, ui.item.index()); // set item id.               
                addItemPrimaryNavID($el); // adds the submenu id.     
                
                adjustItemsWidth();          
            },           
            stop: function(event, ui) {                
                updateItemIds(); // update all item ids.                
                updateItemsHiddenFields(); // update all items hidden.               
            }
        }).disableSelection();              
    };
    
    // allows us to rerun our draggables.
    var refreshDraggable = function() {
        // list items are draggable to rows.
        $( '.pmm-menu-items-list .pmm-item-list .pmm-item' ).draggable({
            connectToSortable: '.pmm-row-column, #pmm-menu-main-navigation',
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
    
    // sets item width to row width.
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
        $('#pmm-menu-grid .pmm-row').each(function() {            

            // we need this sub loop to get proper index.
            $(this).find('.pmm-item').each(function(i) {
                $el = $(this);

                var rowID = getID($el.parent().attr('id')).join('-');
                var itemID = 'pmm-item-' + rowID + '-' + i;

                $el.addClass('editable');
                $el.attr('id', itemID); // update id.
                $el.attr('uid', uniqueID()); // add unique id.
                addItemHiddenFields($el); // adds hidden fields.
                updateItemOptions($el); // update fields/options. 
                addItemActions($el); // add action icons.
                addItemPrimaryNavID($el); // adds the submenu id.
            });
            
            addRowActions($(this).attr('id'));   
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
    
    // sets the id of our item within a row.
    var setItemID = function($el, itemIndex) {
        var rowId = getID($el.parent().attr('id')).join('-');
        var itemID = 'pmm-item-' + rowId + '-' + itemIndex;

        // set id.
        $el.attr('id', itemID);
        
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
            'row': '',
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
        
        $('.pmm-row .pmm-row-column').each(function(rowIndex) {
            var $row = $(this);
            
            $row.find('.pmm-item').each(function(itemIndex) {
                var uId = $(this).attr('uId');
                var baseId = $(this).attr('id').match(pattern)[0];

                $(this).attr('id', baseId + itemIndex);                
            });           
        });
    };
    
    // update column, row and order (pos).
    var updateItemsHiddenFields = function() {
        $('.pmm-row .pmm-item').each(function() {
            var uID = $(this).attr('uId');
            var itemLocation = getID($(this).attr('id')); // returns array [col, row, row column, pos]

            $(this).find('input[name="pmm_menu_items[' + uID + '][column]"]').val(itemLocation[0]);
            $(this).find('input[name="pmm_menu_items[' + uID + '][row]"]').val(itemLocation[1]);
            $(this).find('input[name="pmm_menu_items[' + uID + '][row_column]"]').val(itemLocation[2]);
            $(this).find('input[name="pmm_menu_items[' + uID + '][order]"]').val(itemLocation[3]);          
        });        
    };
    
    // generates a unique id.
    var uniqueID = function() {
        return '_' + Math.random().toString(36).substr(2, 9);
    };
    
    // update row ids.
    var updateRowIds = function() {      
        $('.pmm-column').each(function(colIndex) {
            var $col = $(this); 
            var colIdNum = getID($col.attr('id'));
           
            $col.find('.pmm-row').each(function(rowIndex) {
                $(this).attr('id', 'pmm-row-' + colIdNum + '-' + rowIndex);
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
        var fields = ['column', 'row', 'row_column', 'order', 'primary_nav', 'nav_type'];
        
        $.each(fields, function(key, value) {
            $('<input>').attr({
                type: 'hidden',
                id: value,
                name: value
            }).appendTo($el);
        });
    };
    
    // clears the grid of any existing cols, rows, etc.
    var clearGrid = function() {
        $('.pmm-menu-grid .pmm-column').remove();  
    };
    
    // adds the proper primay nav item id.
    var addItemPrimaryNavID = function($el) {
        var uID = $el.attr('uId');
        var primaryNavuID = $('.pmm-navigation-item.show-submenu').attr('uid');
        var primaryNavID = $('.pmm-navigation-item.show-submenu').find('input[name="pmm_menu_items[' + primaryNavuID + '][id]"]').val();

        $el.find('input[name="pmm_menu_items[' + uID + '][primary_nav]"]').val(primaryNavID); // set primary nav value.
        $el.find('input[name="pmm_menu_items[' + uID + '][nav_type]"]').val('subnav'); // set type as something other than primary (subnav).        
    };
    
    // when a row column is moved, update the ids.
    var updateRowColumnIds = function() {      
        $('.pmm-column .pmm-row').each(function() {
            var $row = $(this);
            var rowIDs = getID($row.attr('id'));

            $row.find('.pmm-row-column').each(function(colIndex) {
                $(this).attr('id', 'pmm-row-column-' + rowIDs[0] + '-' + rowIDs[1] + '-' + colIndex);
            });
        });
    }; 

    var updateSubmenuColumnWidth = function() {        
        $('.pmm-column .pmm-row').each(function() {
            var $row = $(this);
            var $singleCol = $($row.find('.pmm-row-column')[0]);
            var totalCols = $row.find('.pmm-row-column').length;
            var colWidth = $row.width() / totalCols
            var colMarginRight = parseInt($singleCol.css('margin-right'));
            var colExtraSpace = parseInt($singleCol.css('padding-left')) + parseInt($singleCol.css('padding-right')) + colMarginRight;

            $row.find('.pmm-row-column').each(function() { 
                $(this).css('width', (colWidth - colExtraSpace)); 
            });
            
            adjustItemsWidth();
        });
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
        if ($el.find('.remove-item').length) {
            return;    
        }
        
        $('<a/>', {
            href: '',
            class: 'remove-item dashicons dashicons-trash' 
        }).appendTo($el);         
    };
    
    // adds actions to the row. 
    var addRowActions = function(rowID) {
        //$('<div class="pmm-row-actions"><a href="#" class="remove-row dashicons dashicons-trash"></a></div>').appendTo($('#' + rowID));       
    };

    // adds actions to the column. 
    var addColumnActions = function(columnId) {       
        $('<a href="#" class="remove-column dashicons dashicons-trash"></a>').appendTo($('#' + columnId + ' .pmm-column-row-actions'));       
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
            $(document).on('click', '.pmm-column .add-row', this.addRow);
            $(document).on('click', '.pmm-item .remove-item', this.removeItem); 
            $(document).on('click', '.pmm-row .remove-row', this.removeRow);
            $(document).on('click', '.pmm-column .remove-column', this.removeColumn);
            $(document).on('click', '#pickle-mega-menu-admin .notice-dismiss', this.dismissNotice);                                              
            $(document).on('click', '#pmm-save-submenu', this.saveSubmenuButton);
            $(document).on('click', '.pmm-row-columns-selector', this.insertRowColumns);
            $(document).on('input', '.pmm-menu-grid .pmm-item .options .option-field input.label', this.changeNavigationLabel);
			                        
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

                    var primaryNavID = $(this).find('input[name="pmm_menu_items[' + $(this).attr('uid') + '][id]"]').val();                    
                    
                    pmmMegaMenu.closeSubmenu(primaryNavID);
                }    
            });
            
            // open new submenu. check that it's not already open.
            if (closeOnly === true)
                return;
                
            $(this).addClass('show-submenu');                
            pmmMegaMenu.openSubmenu($(this));
        },
        
        openSubmenu: function($el) {           
            pmmMegaMenu.showGrid();
console.log('openSubmenu()');           
            pmmMegaMenu.loadSubmenu($el.find('input[name="pmm_menu_items[' + $el.attr('uid') + '][id]"]').val()); // get the submenu.
        },
        
        closeSubmenu: function(id) {  
console.log('closeSubmenu()');                    
            pmmMegaMenu.saveSubmenu(id);
        },
        
        // fires ajax to save sumbmenu.
        saveSubmenu: function(id, close) {
            if (typeof close === "undefined" || close === null) {
                close = 1;  
            }

            pmmSavingSubmenu = true;
            showAJAXLoader('#wpcontent');

            // ajax to save submenu.            
/*
            pmmMegaMenuAJAX.saveSubMenu(id, function(response) {
                pmmMegaMenu.displayMessage(response);

                if (close) {
                    clearGrid(); // empty grid.
                    pmmMegaMenu.hideGrid();
                }

                pmmSavingSubmenu = false;
                
                hideAJAXLoader();
            });
*/
            
            // use id to work backwards and find nav item base on value, then remove class. 
            if (close) {            
                $('.pmm-navigation-item input[value="' + id + '"]').parents('.pmm-navigation-item').removeClass('show-submenu');            
            }
        },
        
        // when our save submenu button is clicked.
        saveSubmenuButton: function(e) {
            e.preventDefault();
           
            pmmMegaMenu.saveSubmenu($(this).data('submenuId'), 0);  
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
                    
                    pmmMegaMenu.showGrid();
                    
                    updateColumnWidth();
                    
                    hideAJAXLoader();
                }
                
                $('.pmm-menu-grid-wrap #pmm-save-submenu').attr('data-submenu-id', submenuID);
            });           
        },
        
        addColumn: function() {            
            var colNum=$('.pmm-column').length;
            var colID = 'pmm-column-' + colNum;
            
            $('<div id="' + colID +'" class="pmm-column"><div class="pmm-column-row-actions"><div class="add-row-wrap"><a href="#" class="add-row">Add Row</a></div></div></div>').appendTo('#pmm-menu-grid'); 
            
            // add actions.
            addColumnActions(colID);

            // add default row.
            pmmMegaMenu.manualAddRow(getID(colID), 0);           
            
            // update column width
            updateColumnWidth();                     
        },
        
        addRow: function(e) {
            if (typeof e !== 'undefined') {
                e.preventDefault();
            }
          
            var $col = $(this).parents('.pmm-column');
            var colIdNum = getID($col.attr('id'));
            var order = $col.find('.pmm-row').length;
            var rowID = 'pmm-row-' + colIdNum + '-' + order;

            $('<div/>', {
               id: rowID,
               class: 'pmm-row' 
            }).appendTo($col);

            pmmMegaMenu.openRowColumnModal(rowID);    
        },
        
        manualAddRow: function(colIdNum, order) {
            $col=$('#pmm-column-' + colIdNum);
            
            var rowID = 'pmm-row-' + colIdNum + '-' + order;

            $('<div/>', {
               id: rowID,
               class: 'pmm-row' 
            }).appendTo($col);

            pmmMegaMenu.openRowColumnModal(rowID);            
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
        
        removeRow: function(e) {
            e.preventDefault();
           
            $(this).parents('.pmm-row').remove();          
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
        },
        
        showGrid: function() {
            $('.pmm-menu-grid, .pmm-submenu-options').show();
        },

        hideGrid: function() {
            $('.pmm-menu-grid, .pmm-submenu-options').hide();
        },
        
        openRowColumnModal: function(rowID) {
            var minColumns = 1;
            var maxColumns = 4;
            var modalContent = '';
           
            modalContent += '<div class="pmm-row-columns">';
                
                for (var i = minColumns; i <= maxColumns; i++) {
                    modalContent += '<div class="pmm-rc-option total-columns-' + i + '">';
                        modalContent += '<a href="#" class="pmm-row-columns-selector" data-columns="' + i + '" data-row="' + rowID + '">';
                    
                            for (var cols = 1; cols <= i; cols ++) {
                                var content = '';
                                
                                if (i != 1) {
                                    content = '1/' + i; 
                                }
                                
                                modalContent += '<div class="column column-' + cols + '"><div>' + content + '</div></div>';
                            }
                    
                        modalContent += '</a>';
                    
                    modalContent += '</div>';
                }
                    
            modalContent += '</div>';
            
            pmmModal.open({
				content: modalContent,
				class: 'pmm-row-columns-modal'
			});
        },
        
        insertRowColumns: function() {
            var columns = $(this).data('columns');
            var rowID = $(this).data('row');
            var rowIDs = getID(rowID);

            for (var i = 0; i < columns; i++) {            
                $('<div id="pmm-row-column-' + rowIDs[0] + '-' + rowIDs[1] + '-' + i + '" class="pmm-row-column"></div>').appendTo($('#' + rowID));
            }
            
            updateSubmenuColumnWidth();
            
            addRowActions('pmm-row-' + rowIDs[0] + '-' + rowIDs[1]);
            
            refreshSortables();
            refreshDraggable();
            
            pmmModal.close(); // close modal.                    
        },
        
        changeNavigationLabel: function() {
            var $item = $(this).parents('.pmm-item');
            var $itemSpan = $item.find('span');
            
            $itemSpan.text($(this).val()); // live update of item title (label)
        }
        
    };

    pmmMegaMenu.init();
    
});