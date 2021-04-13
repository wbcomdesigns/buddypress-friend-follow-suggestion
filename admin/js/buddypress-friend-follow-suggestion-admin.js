(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */
	 
	$( document ).ready( function(){
		
		var field_count = $( '#bffs-field-content .search_field').length;
		
		$( document ).on('click','#add-bffs-match-field', function(e){		
			e.preventDefault();
			var save_button = $('input[type=submit]');			
			var data = {
				'action': 'bffs_get_profile_field',
				'count': field_count++
			};

			save_button.attr('disabled', 'disabled');

			$.post (ajaxurl, data, function (search_field) {
				$('#bffs-field-content').append(search_field);
				save_button.removeAttr('disabled');
			});

			return false;
		});
		
		$('#bffs-field-content').sortable ({
			items: 'div.search_field',
			tolerance: 'pointer',
			axis: 'y',
			handle: 'span',
			update: function(event, ui) {
				
				var count = 0;
				$('#bffs-field-content .search_field').each(function() {
					
					jQuery(this).find( '.bffs_profile_field_name' ).attr('name', 'bffs_general_setting[bffs_match_data]['+ count +'][field_id]');
					
					jQuery(this).find( '.bffs-match-percentage' ).attr('name', 'bffs_general_setting[bffs_match_data]['+ count +'][percentage]');
					
					jQuery(this).find( '.bffs-match-stop-match' ).attr('name', 'bffs_general_setting[bffs_match_data]['+ count +'][stop_match]');
					console.log(jQuery(this).find( '.bffs_profile_field_name' ).attr('name') );
					
					count++;
				});
				
			}
		});
		
		$( document ).on( 'click', '.delete_bffs_field', function (e){
			e.preventDefault();
			$( this ).parent().parent().remove();
			
		});
	});

})( jQuery );
