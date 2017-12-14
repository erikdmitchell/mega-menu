jQuery(document).ready(function($) {
 
   $('#pickle-mega-menu-admin .columns-list li a').on('click', function(e) {
      e.preventDefault();
      
      var cols = $(this).data('cols');
      
console.log(cols);       
   });

});


/*
$( function() {
    
    var prevBottom, nextThreshold;
    
    $( '.sortable-list' ).sortable({
        connectWith: '.sortable-list',
        placeholder: 'placeholder',
    }).disableSelection();

} );
*/

$( function() {
    
    $( '#pmm-menu-grid .block' ).sortable({
        connectWith: '#pmm-menu-grid .block',
        placeholder: 'placeholder',
    }).disableSelection();

} );

$( function() {
    $( '.pmm-menu-items-list .item-list .item' ).draggable({
        connectToSortable: '.block',
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


//console.log(pmmItemsOptions);
jQuery( function($) {
    
    // geneates a tingle modal.
    // https://robinparisi.github.io/tingle/.
/*
    var itemModal = function() {

        // instanciate new modal
        var modal = new tingle.modal({
            closeMethods: ['overlay', 'button', 'escape'],
            closeLabel: "Close",
            cssClass: ['add-item-modal'],
            beforeClose: function() {
                // here's goes some logic
                // e.g. save content before closing the modal
                return true; // close the modal
            	return false; // nothing happens
            }
        });
        
        // set content.
        modal.setContent(itemsList());

        // open modal.
        modal.open();
        
    };
    
    var itemsList = function() {
        var html='';
        
        html += '<div class="item-list">';
        
            $.each(pmmItemsOptions.items, function(key, value) {
                
                html += '<div class="item col-3" data-slug="' + value.slug + '">' + value.label + '</div>';
           
            });
            
        html += '</div>';
 
        return html;  
    }
*/
    
/*
    var pmmMegaMenu = {
        init: function() {
            $(document).on('click', '#pmm-menu-grid .add-item', this.addItem);
        },
        
        addItem: function(e) {
            e.preventDefault();
           
        }
        
    };

    pmmMegaMenu.init();
*/
    
});

