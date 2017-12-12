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
        sort: function(e, ui) {
			var offset = ui.helper.offset();
// Check and correct if depth is not within range.
					// Also, if the dragged element is dragged upwards over
					// an item, shift the placeholder to a child position.
					
					// update depth.
/*
console.log(offset);


if ( depth > maxDepth || offset.top < ( prevBottom - api.options.targetTolerance ) ) {
						depth = maxDepth;
					} else if ( depth < minDepth ) {
						depth = minDepth;
					}
					
					
console.log(depth);	
*/				

        }
    }).disableSelection();

} );