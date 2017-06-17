<?php header('Content-Type: application/javascript'); ?>
jQuery(document).ready(function($) {
	
	
	$('table.dataTable').dataTable({"searching": false, "paging": false, "language": {"info": ""}});
	
	var copy_sel = jQuery('.the_clipboard');
	// Disables other default handlers on click (avoid issues)
	copy_sel.on('click', function(e) {
		e.preventDefault();
	});
	// Apply clipboard click event
	jQuery('.the_clipboard').clipboard({
		path: '/member_dashboard_assets/swf/jquery.clipboard.swf',
		copy: function() {
			labdevs_show_settings_saved();
			return jQuery(this).parent().find('.spy_link_permalink').val();
		}
	});
	
	//popup update contest
	$(document).on('click', '.update_spy_link_button', function(e){
		
		e.preventDefault();		
		var post_id = $(this).attr('data-id');
				
		$.ajax({
			type: "POST",
			url: ajaxurl,
			data: {action: 'contest_dashboard_update',post_id : post_id}
		}).done(function( response ) {
			console.log(response);
			$('#contest_form_modal').html(response);
			
		});
		
		$('#model_edit_contest').modal('show');	
		
		
	});
	
	$("#url_prefix + .dropdown-menu li a").click(function(e) {
		e.preventDefault();
		$('#url_prefix').html($(this).text()+' <span class="caret"></span>');
		$('#url_prefix').val($(this).attr('href'));
	});
	
	
	$("#url").change(function(e) {
		e.preventDefault();
		$('#url_msg').html('');
		$('#url_checking').show();
		$.post(
			ajaxurl,
			{
				action: 'check_contest_url',
				post_id: $('#spy_link_id').val(),
				post_name: $('#url').val()
			},
			function( data ) {
				$('#url_checking').hide();
				if ( data != '-1' ) {
					if(data == 'Yes') {
						$('<span style="color:green;">URL is available</span>').appendTo('#url_msg');
						$('#thankyou-page').html($('#url_prefix').val() + $('#url').val());
					} else {
						$('<span style="color:red;">URL is not available. Used default</span>').appendTo('#url_msg');
						$('#thankyou-page').html($('#url_prefix').val() + data);
						$('#url').val(data);
					}
				}
			}
		);
	});
});

function labdevs_show_settings_saved() {
   jQuery('.labdevs_small_success').fadeIn(500);
   setTimeout( function(){ jQuery('.labdevs_small_success').fadeOut(500); } , 2000 );
}