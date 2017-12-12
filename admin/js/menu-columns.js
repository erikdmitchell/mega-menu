jQuery(document).ready(function($) {
 
   $('#pickle-mega-menu-admin .columns-list li a').on('click', function(e) {
      e.preventDefault();
      
      var cols = $(this).data('cols');
      
console.log(cols);       
   });
    
});

$( function() {
    
    var prevBottom, nextThreshold;
    
    $( '.sortable-list' ).sortable({
        connectWith: '.sortable-list',
        placeholder: 'placeholder',
        update: function(event, ui) {
            // update
        },
        start: function(event, ui) {           
            if(ui.helper.hasClass('level-1')){
                ui.placeholder.addClass('level-1');
            }
            else{ 
                ui.placeholder.removeClass('level-1');
            }
            
            // Now that the element is complete, we can update...
            updateSharedVars(ui);
        },
        change: function(event, ui) {
            updateSharedVars(ui);
        },
        sort: function(event, ui) {
            var basePos = $(this).parent().position().left;
            var pos = ui.position.left - basePos;
            var offset = ui.helper.offset();
            
//console.log(nextThreshold + ' ' + offset.top);                        
        					// If we overlap the next element, manually shift downwards

if( nextThreshold && offset.top > nextThreshold ) {
    console.log('overlap?');
						//next.after( ui.placeholder );
						updateSharedVars( ui );
						$( this ).sortable( 'refreshPositions' );
						ui.placeholder.addClass('level-1');
					}
/*
					if( nextThreshold && offset.top + helperHeight > nextThreshold ) {
						next.after( ui.placeholder );
						updateSharedVars( ui );
						$( this ).sortable( 'refreshPositions' );
					}
*/
					
//console.log(ui.item.index());        
            if (ui.helper.hasClass('level-1')) {
                pos+=20; 
            }
    
            if (pos >= 32 && !ui.helper.hasClass('level-1')) {
                ui.placeholder.addClass('level-1');
                ui.helper.addClass('level-1');
            } else if (pos < 25 && ui.helper.hasClass('level-1')) {
                ui.placeholder.removeClass('level-1');
                ui.helper.removeClass('level-1');
            } 
        
        
        },
        stop: function(event, ui) {
            // first item has to be top level //
            if (ui.item.index() == 0) {
                ui.item.removeClass('level-1');
            }
        }
    }).disableSelection();

	function updateSharedVars(ui) {
		var depth;

		prev = ui.placeholder.prev( '.item' );
		next = ui.placeholder.next( '.item' );
//console.log(prev);
//console.log(next);
		// Make sure we don't select the moving item.
		if( prev[0] == ui.item[0] ) prev = prev.prev( '.item' );
		if( next[0] == ui.item[0] ) next = next.next( '.item' );

		prevBottom = (prev.length) ? prev.offset().top + prev.height() : 0;
		nextThreshold = (next.length) ? next.offset().top + next.height() / 3 : 0;
		//minDepth = (next.length) ? next.menuItemDepth() : 0;
	}

} );