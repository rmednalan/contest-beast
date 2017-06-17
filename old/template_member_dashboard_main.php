<?php
/*
Template Name: Member Dashboard
*/
include('member_dashboard_files/header.php');

global $current_user;
get_currentuserinfo();
?>

<link rel="stylesheet" href="//cdn.datatables.net/plug-ins/3cfcc339e89/integration/bootstrap/3/dataTables.bootstrap.css" />
<script type="text/javascript" language="javascript" src="//cdn.datatables.net/1.10.4/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" language="javascript" src="//cdn.datatables.net/plug-ins/3cfcc339e89/integration/bootstrap/3/dataTables.bootstrap.js"></script>
<script type="text/javascript" charset="utf-8">
jQuery(document).ready(function($) {
	$('table.dataTable').dataTable({"searching": false, "paging": false, "language": {"info": ""}});} );
</script>
<script>
function labdevs_show_settings_saved() {
   jQuery('.labdevs_small_success').fadeIn(500);
   setTimeout( function(){ jQuery('.labdevs_small_success').fadeOut(500); } , 2000 );
}
</script>
<div class="labdevs_small_success" style="position: fixed; top: 40px; left: 0px; z-index: 100000; width: 100%; padding: 10px; text-align: center; height: auto; display: none;">
	<span style="color: white;background-color: #80C751;padding: 10px 30px;border-radius: 9px;font-family: monospace;line-height: 1em;">Copied To Clipboard!</span>
</div>
<script>
jQuery(document).ready(function($) {
	var copy_sel = jQuery('.the_clipboard');
	// Disables other default handlers on click (avoid issues)
	copy_sel.on('click', function(e) {
		e.preventDefault();
	});
	// Apply clipboard click event
	jQuery('.the_clipboard').clipboard({
		path: '<?php echo get_template_directory_uri(); ?>/member_dashboard_assets/swf/jquery.clipboard.swf',
		copy: function() {
			labdevs_show_settings_saved();
			return jQuery(this).parent().find('.spy_link_permalink').val();
		}
	});
});
</script>
<style type="text/css">
#url_checking {height: 10px;margin-top: 8px;}
#custom_mailing {display:none;}
.ui-widget{z-index:9999 !important;}
.contest-beast-winners-box img{display:none;}
</style>
	<h1 class="page-header"><span class="glyphicon glyphicon-stats" style="color:#000;"></span> Your Contests</h1>
	<div id="updating_table" class="alert alert-info" style="display: none;padding: 10px;">
		<strong>Updating Table!</strong> Please wait.
			<div class="progress progress-striped active" style="margin-bottom: 0;margin-top: 9px;height: 10px;">
				<div class="progress-bar" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
				<span class="sr-only">Fetching</span>
				</div>
			</div>
		</div>  <!-- #updating_table -->
			<div class="table-responsive">
			<table class="table table-striped table table-striped table-bordered dataTable no-footer" style="text-align:center;">
				<thead>
				<tr>
					<th style=" vertical-align:middle;">Titlex</th>
					<th style="text-align:center; vertical-align:middle;">Date Published</th>
					<th style="text-align:center; vertical-align:middle;">Start Date</th>
					<th style="text-align:center; vertical-align:middle;">End Date</th>
					<th style="text-align:center; vertical-align:middle;">Submissions</th>
					<th style="text-align:center; vertical-align:middle;">Entries Per Referral</th>
					<th style="text-align:center; vertical-align:middle;"># Of Winners</th>
					<th style="text-align:center; vertical-align:middle;">Winners</th>
					<th style="text-align:center; vertical-align:middle;">Actions</th>
				</tr>
				</thead>
				<style>
				.spy_link_permalink {
					background-color: #FFF;
					padding: 5px 7px;
					width: 265px;
					display: inline-block;
					border-radius: 4px;
				}
				</style>
				<tbody  id="spy_link_row">
					<?php
				/* ----------------------------------------------------- */
				/* WP_QUERY - THE LOOP */ /* SPY LINK LOOP */
				/* ----------------------------------------------------- */
				/* arguments */
				$args = array (
					'pagination'			 => true,
					'posts_per_page'		 => '-1',
					'author' 				 => labdevs_get_user_current_id(),
					'post_type' 			 => 'contest',
				);
				/* query */
				$home_query = new WP_Query( $args );
				while ( $home_query->have_posts() ) {
					$home_query->the_post();
				/*
				 * DISPLAYING FUNCTIONS
				 * ====================
				 * Title => the_title();
				 * Permalink => the_permalink();
				 * Content => the_content();
				 *
				 */
				 $tmp = get_permalink();
				 $tmp = split('/r/' , $tmp);
				 $permalink = $tmp[1];
				 $post_id = $post->ID;
				 $contest_id = $post_id;
				 ?>
					 <tr>
						<td style="width:130px; vertical-align:middle; text-align:left;"><?php the_title(); ?></td>
						<td style="width:100px; vertical-align:middle; text-align:center;"><?php echo get_the_time('Y-m-d', $post->ID); ?></td>
						<td style="width:130px; vertical-align:middle;">
							<?php contest_beast_the_contest_start_date(); ?>
						</td>
						<td style="width:130px; vertical-align:middle;"><?php contest_beast_the_contest_end_date(); ?>
						</td>
						<td style="width:80px; vertical-align:middle;"><?php contest_beast_the_contest_number_submissions($post_id); ?> <?php $nonce_url = add_query_arg(array('export-contest-beast-entries-nonce' => wp_create_nonce("export-contest-beast-entries-{$post_id}"), 'ID' => $post_id), admin_url('admin.php'));
					printf('<br /><a href="%s">%s</a>', $nonce_url, __('Export Entrants')); ?></td>
						<td style="width:80px; vertical-align:middle;"><?php contest_beast_the_contest_number_entries($post_id); ?></td>
						<td style="width:60px; vertical-align:middle;"><?php contest_beast_the_contest_number_winners($post_id); ?></td>
						<td style="width:120px; vertical-align:middle;"><?php
							$winners = contest_beast_get_contest_winners($post_id);
							if (!contest_beast_has_ended($post_id)) {
								_e('N/A');
							} else {
								Contest_Beast::display_contest_winners_markup($post_id);
							}
							?>
							<script type="text/javascript">
							jQuery('.contest-beast-choose-winners').on('click', function(event) {
								event.preventDefault();
								var $button = jQuery(this)
								, $container = $button.parents('.contest-beast-winners-box')
								, $ajax_feedback = $container.find('.contest-beast-winner-action-ajax');
								$ajax_feedback.insertAfter($button).css({ visibility: 'visible', top: '-4px', 'margin-left': '5px', position: 'relative' }).show();
								jQuery.post(
									ajaxurl,
									{
										action: 'contest_beast_choose_winners',
										'contest-id': $button.attr('data-contest-id'),
										method: $container.find('.contest-beast-winners-method').val()
									},
									function(data, status) {
										$container.replaceWith(data);
									}
								);
							});
							jQuery('.contest-beast-disqualify-submission').on('click', function(event) {
								event.preventDefault();
								var $link = jQuery(this)
								, $container = $link.parents('.contest-beast-winners-box')
								, $ajax_feedback = $container.find('.contest-beast-winner-action-ajax');
								$ajax_feedback.insertAfter($link).css({ visibility: 'visible', top: '-3px', 'margin-left': '5px', position: 'relative' }).show();
								jQuery.post(
									ajaxurl,
									{ action: 'contest_beast_replace_winner', 'contest-id': $link.attr('data-contest-id'), 'submission-id': $link.attr('data-submission-id') },
									function(data, status) {
										$container.replaceWith(data);
									}
								);
							});
						</script>
						</td>
						<td style=" vertical-align:middle; width:150px;">
						<input type="hidden" value="<?php the_permalink(); ?>" class="spy_link_permalink" />
						<button class="the_clipboard btn btn-default btn-sm"><span class="glyphicon glyphicon-paperclip"></span></button>
						 <a href="<?php the_permalink(); ?>" role="button" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-eye-open"></span></a>
						 <?php $items = get_post_meta($post->ID, '_contest_beast_post_meta', true); ?>
						 <?php // print_r($items); ?>
						 <button data-url="<?php echo esc_attr(get_the_permalink());?>"
							data-id="<?php the_ID(); ?>"
							data-title="<?php the_title(); ?>"
							data-slug="<?php echo esc_attr($post->post_name); ?>"
							data-prefix="<?php echo esc_attr($items["url_prefix"]); ?>"
							data-content="<?php echo esc_attr($post->post_content); ?>"
							data-start="<?php echo esc_attr($items["start-date"]); ?>"
							data-hour="<?php echo esc_attr($items["start-time-hour"]); ?>"
							data-minute="<?php echo esc_attr($items["start-time-minute"]); ?>"
							data-meridian="<?php echo esc_attr($items["start-time-meridian"]);  ?>"
							data-end="<?php echo esc_attr($items["end-date"]);  ?>"
							data-endhour="<?php echo esc_attr($items["end-time-hour"]);  ?>"
							data-endminute="<?php echo esc_attr($items["end-time-minute"]);  ?>"
							data-endmeridian="<?php echo esc_attr($items["end-time-meridian"]);  ?>"
							data-referral="<?php echo esc_attr($items["number-entries"]);  ?>"
							data-winners="<?php echo esc_attr($items["number-winners"]);  ?>"
							data-disclaimer="<?php echo esc_attr($items["contest-disclaimer"]);  ?>"
							data-termsofservice="<?php echo esc_attr($items["contest-termsofservice"]);  ?>"
							data-privacypolicybox="<?php echo esc_attr($items["contest-privacypolicybox"]);  ?>"
							data-contest_rules="<?php echo esc_attr($items["contest-rules-custom"]);  ?>"
							data-rules="<?php echo esc_attr($items["contest-rules"]);  ?>"
							data-custom_scripts="<?php echo esc_attr($items["tracking-scripts-custom"]);  ?>"
							data-scripts="<?php echo esc_attr($items["tracking-scripts"]);  ?>"
							data-custom_mailing="<?php echo esc_attr($items["add-to-mailing-list"]);  ?>"
							data-mailing="<?php echo esc_attr($items["mailing-list-list"]);  ?>"
							data-logo="<?php echo esc_attr($items["branding-logo-url"]);  ?>"
							data-mc="<?php echo esc_attr($items["mc-template"]);  ?>"
							
							data-body_background_color="<?php echo esc_attr($items["body_background_color"]);  ?>"
							data-body_background_image="<?php echo esc_attr($items["body_background_image"]);  ?>"
							data-contest_background_color="<?php echo esc_attr($items["contest_background_color"]);  ?>"
							data-contest_background_image="<?php echo esc_attr($items["contest_background_image"]);  ?>"
							data-button_tpl="<?php echo esc_attr($items["button_tpl"]);  ?>"
							data-button_img="<?php echo esc_attr($items["button_img"]);  ?>"
							
							class="update_spy_link_button btn btn-default btn-sm">
								<span class="glyphicon glyphicon-edit"></span>
			 					</button>
							<button
								data-id="<?php the_ID(); ?>"
								class="delete_spy_link_button btn btn-default btn-sm">
								<span class="glyphicon glyphicon-trash"></span>
							</button></td>
					</tr>
				 <?php
				} // while while posts
				/* restore the global $post variable of the main query loop after a secondary query loop using new WP_Query */
				wp_reset_postdata();
				?>
				</tbody>
			</table>
			</div>
			<script>
		jQuery(document).ready(function($){
			$(document).on('click', '.delete_spy_link_button', function() {
				var id = $(this).attr('data-id');
				$('#delete_spy_link_id').val( id );
				$('#myModal_2').modal('show');
			});
			$(document).on('click', '#delete_spy_link_button', function() {
				$('#myModal_2').modal('hide');
				$('#updating_table').show();
				var spy_link_id = $('#delete_spy_link_id').val();
				$.ajax({
					type: "POST",
					url: "<?php echo admin_url('admin-ajax.php'); ?>",
					data: {
						action: 'labdevs_spy_link_delete',
						spy_link_id : spy_link_id,
					}
				}).done(function( data ) {
					update_spy_links_counter();
					$('.form-horizontal').find("input[type=hidden], input[type=text], textarea").val("");
					$.ajax({
						type: "POST",
						url: "<?php echo admin_url('admin-ajax.php'); ?>",
						data: {
							action: 'labdevs_spy_link_table',
						},
						beforeSend: function() {
						}
					}).done(function( data ){
						if( data === '0' ) {
							data = '';
						}
						$("#spy_link_row").html(data);
						$('#updating_table').hide();
					});
				});
			});
			$(document).on('click', '.update_spy_link_button', function(){
				var id = $(this).attr('data-id');
				var title = $(this).attr('data-title');
				var content = $(this).attr('data-content');
				var start = $(this).attr('data-start');
				var hour = $(this).attr('data-hour');
				var minute = $(this).attr('data-minute');
				var meridian = $(this).attr('data-meridian');
				var endd = $(this).attr('data-end');
				var endhour = $(this).attr('data-endhour');
				var endminute = $(this).attr('data-endminute');
				var endmeridian = $(this).attr('data-endmeridian');
				var referral = $(this).attr('data-referral');
				var winners = $(this).attr('data-winners');
				var disclaimer = $(this).attr('data-disclaimer');
				var termsofservice = $(this).attr('data-termsofservice');
				var privacypolicybox = $(this).attr('data-privacypolicybox');
				var contest_rules = $(this).attr('data-contest_rules');
				var rules = $(this).attr('data-rules');
				var custom_scripts = $(this).attr('data-custom_scripts');
				var scripts = $(this).attr('data-scripts');
				var custom_mailing = $(this).attr('data-custom_mailing');
				var mailing = $(this).attr('data-mailing');
				var logo = $(this).attr('data-logo');
				var mc_template = $(this).attr('data-mc');
				$('#countdown').val(mc_template).change();
				$('#url').val($(this).data('slug'));
				$('#url_prefix').val($(this).data('prefix'));
				$('#thankyou-page').html($(this).data('url'));
				$('#spy_link_id').val( id );
				$('#spy_link_name').val( title );
				var content, inputid = 'post_content';
				var editor = tinyMCE.get(inputid);
				if (editor) {
					editor.setContent(content);
				} else {
					$('#'+inputid).val(content);
				}
				$('#start_date').val( start );
				$('#start_hour').val( hour );
				$('#start_minute').val( minute );
				$('#start_meridian').val( meridian );
				$('#end_date').val( endd );
				$('#end_hour').val( endhour );
				$('#end_minute').val( endminute );
				$('#end_meridian').val( endmeridian );
				$('#referral').val( referral );
				$('#winners').val( winners );
				$('#disclaimer').val( disclaimer );
				$('#termsofservice').val( termsofservice );
				$('#privacypolicybox').val( privacypolicybox );
				$('#logo_url').val(logo);
				$('#logo-preview').html('<img src="'+logo+'" alt="" width="80" />').append('<a href="#" class="remove"><i class="fa fa-times"></i></a>');
				if(contest_rules == 'yes'){
					jQuery('#contest_rules').attr('checked','checked');
					jQuery('#contest_rules').val('yes');
					jQuery('.open_rules').show();
				}else{
					jQuery('#contest_rules').val('no');
				}
				$('#rules').val( rules );
				if(custom_scripts == 'yes'){
					jQuery('#custom_scripts').attr('checked','checked');
					jQuery('#custom_scripts').val('yes');
					jQuery('.open_scripts').show();
				}else{
					jQuery('#custom_scripts').val('no');
				}
				$('#scripts').val( scripts );
				if(custom_mailing == 'yes'){
					jQuery('#custom_mailing').attr('checked','checked');
					jQuery('#custom_mailing').val('yes');
					jQuery('.open_mailing').show();
				}else{
					jQuery('#custom_scripts').val('no');
				}
				$('#mailing').val( mailing );
				$('#mailing').change();
				$('#myModal').modal('show');
				jQuery('#myModal .datepicker').datepicker({minDate: 0});
				
				var body_bgimage = $(this).attr('data-body_background_image');
				var body_bgcolor = $(this).attr('data-body_background_color');
				$('#body_background_color').val(body_bgcolor);
				$('#body_background_image_url').val(body_bgimage);
				$('#body_background_image-preview').html('<img src="'+body_bgimage+'" alt="" width="80" />').append('<a href="#" class="remove"><i class="fa fa-times"></i></a>');
				
				var contest_bgimage = $(this).attr('data-contest_background_image');
				var contest_bgcolor = $(this).attr('data-contest_background_color');
				$('#contest_background_color').val(contest_bgcolor);
				$('#contest_background_image_url').val(contest_bgimage);
				$('#contest_background_image-preview').html('<img src="'+contest_bgimage+'" alt="" width="80" />').append('<a href="#" class="remove"><i class="fa fa-times"></i></a>');
				
				
				var btimg = $(this).attr('data-button_img');
				var bttpl = $(this).attr('data-button_tpl');
				if(bttpl != "") $('#button_tpl').val(bttpl).change();
				$('#button_img_url').val(btimg);
				$('#button_image-preview').html('<img src="'+bgimage+'" alt="" width="80" />').append('<a href="#" class="remove"><i class="fa fa-times"></i></a>');
			});
			jQuery('#contest_rules').click(function(){
				if (jQuery(this).is(':checked')) {
					 jQuery(".open_rules").show();
					jQuery(this).val('yes');
				} else {
					jQuery(this).val('no');
					jQuery(".open_rules").hide();
				}
			})
			jQuery('#custom_scripts').click(function(){
				if (jQuery(this).is(':checked')) {
					 jQuery(".open_scripts").show();
					jQuery(this).val('yes');
				} else {
					jQuery(this).val('no');
					jQuery(".open_scripts").hide();
				}
			})
			jQuery('#custom_mailing').click(function(){
				if (jQuery(this).is(':checked')) {
					 jQuery(".open_mailing").show();
					jQuery(this).val('yes');
				} else {
					jQuery(this).val('no');
					jQuery(".open_mailing").hide();
				}
			})
			$(document).on('click','#update_spy_link',function( e ){
				$('#myModal').modal('hide');
				$('#updating_table').show();
				var spy_link_id = $('#spy_link_id').val();
				var spy_link_name = $('#spy_link_name').val();
				var content, inputid = 'post_content';
				var editor = tinyMCE.get(inputid);
				if (editor) {
					content = editor.getContent();
				} else {
					content = $('#'+inputid).val();
				}
				var start_date = $('#start_date').val();
				var start_hour = $('#start_hour').val();
				var start_minute = $('#start_minute').val();
				var start_meridian = $('#start_meridian').val();
				var end_date = $('#end_date').val();
				var end_hour = $('#end_hour').val();
				var end_minute = $('#end_minute').val();
				var end_meridian = $('#end_meridian').val();
				var referral = $('#referral').val();
				var winners = $('#winners').val();
				var disclaimer = $('#disclaimer').val();
				var termsofservice = $('#termsofservice').val();
				var privacypolicybox = $('#privacypolicybox').val();
				var contest_rules = $('#contest_rules').val();
				var rules = $('#rules').val();
				var custom_scripts = $('#custom_scripts').val();
				var scripts = $('#scripts').val();
				var custom_mailing = $('#custom_mailing').val();
				var mailing = $('#mailing').val();
				var logo = $('#logo_url').val();
				var mc_template = $('#countdown').val();
				$.ajax({
					type: "POST",
					url: "<?php echo admin_url('admin-ajax.php'); ?>",
					data: {
						action: 'labdevs_spy_link_update',
						spy_link_id : spy_link_id,
						spy_link_name : spy_link_name,
						url_prefix: $('#url_prefix').val(),
						post_name : $('#url').val(),
						post_content : content,
						start_date : start_date,
						start_hour : start_hour,
						start_minute : start_minute,
						start_meridian : start_meridian,
						end_date : end_date,
						end_hour : end_hour,
						end_minute : end_minute,
						end_meridian : end_meridian,
						referral : referral,
						winners : winners,
						disclaimer : disclaimer,
						termsofservice : termsofservice,
						privacypolicybox : privacypolicybox,
						contest_rules : contest_rules,
						rules : rules,
						custom_scripts : custom_scripts,
						scripts : scripts,
						custom_mailing : custom_mailing,
						mailing : mailing,
						mailing_data: parseOptInCode($('#mailing').val()),
						logo : logo,
						mc_template: mc_template,
						body_background_color: $('#body_background_color').val(),
						body_background_image: $('#body_background_image_url').val(),
						contest_background_color: $('#contest_background_color').val(),
						contest_background_image: $('#contest_background_image_url').val(),
						button_tpl: $('#button_tpl').val(),
						button_img: $('#button_img_url').val()
					}
				}).done(function( data ) {
					//$('.form-horizontal').find("input[type=hidden], input[type=text], textarea").val("");
					$.ajax({
						type: "POST",
						url: "<?php echo admin_url('admin-ajax.php'); ?>",
						data: {
							action: 'labdevs_spy_link_table',
						},
						beforeSend: function() {
						}
					}).done(function( data ){
						if( data === '0' ) {
							data = '';
						}
						$("#spy_link_row").html(data);
						$('#updating_table').hide();
							jQuery('.the_clipboard').clipboard({
								path: '<?php echo get_template_directory_uri(); ?>/member_dashboard_assets/swf/jquery.clipboard.swf',
								copy: function() {
									labdevs_show_settings_saved();
									return jQuery(this).parent().find('.spy_link_permalink').val();
								}
							});
					});
				});
				e.preventDefault();
			});
			var ajax_call_number = 0;
		$(document).on('keyup','#spy_url',function(){
			if( $('#spy_url').val() == '') {
				$('.spy_link_notification').hide();
				return;
			}
			$('.spy_link_notification').hide();
			$('#spy_link_searching').show();
			$('#update_spy_link').attr('disabled','disabld');
			ajax_call_number = ajax_call_number + 1;
			$.ajax({
				type: 'POST',
				url: "<?php echo admin_url('admin-ajax.php'); ?>",
				data: {
					action: 'labdevs_is_spy_url_available',
					slug : $('#spy_url').val(),
					ajax_call_number : ajax_call_number
				},
				success: function( data ){
					data_2 = data;
					if( ajax_call_number ==  data_2.trim().split('|').shift() ) {
						console.log('in');
						if( data.trim().split('|').pop().replace('0','') == 'false') {
							$('.spy_link_notification').hide();					;
							$('#spy_link_fail').show();
							$('#update_spy_link').attr('disabled','disabld');
						} else {
							$('.spy_link_notification').hide();					;
							$('#spy_link_success').show();
							$('#update_spy_link').removeAttr('disabled');
						}
					}
				}
			});
		});
		});
		</script>
<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4 class="modal-title" id="myModalLabel">Edit Contest</h4>
		</div>
		<div class="modal-body">
		<div class="row"><div class="col-md-8 col-md-offset-2">
			<fieldset>
				<input type="hidden" id="spy_link_id" value="">
				<!-- Text input-->
				<div class="form-group">
					<label class="control-label" for="textinput">Title</label>
					<div class="">
					<input id="spy_link_name" name="textinput" type="text" placeholder="Title here..." class="form-control input-md">
					</div>
				</div>
				<div class="form-group">
				  <label class="control-label" for="url">Contest URL</label>
				  <div class="">
					<div class="input-group">
					  <div class="input-group-btn">
						<button id='url_prefix' type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" value="<?php echo site_url('/contest/');?>"><?php echo site_url('/');?> <span class="caret"></span></button>
						<ul class="dropdown-menu">
						  <li><a href="<?php echo site_url('/contest/');?>"><?php echo site_url('/');?></a></li>
						</ul>
					  </div><!-- /btn-group -->
					  <input id="url" name="textinput" type="text" placeholder="URL here..." class="form-control input-md">
					</div><!-- /input-group -->
					<div id="url_checking" class="progress progress-striped active" style=""><div class="progress-bar" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%"><span class="sr-only">100% Complete</span></div></div>
					<div id="url_msg"></div>
					<script type="text/javascript">
					(function($) {
						$("#url_prefix + .dropdown-menu li a").click(function(e) {
							e.preventDefault();
							$('#url_prefix').html($(this).text()+' <span class="caret"></span>');
							$('#url_prefix').val($(this).attr('href'));
						});
						$('#url_checking').hide();
						$("#url").change(function(e) {
							e.preventDefault();
							$('#url_msg').html('');
							$('#url_checking').show();
							$.post(
								"<?php echo admin_url('admin-ajax.php'); ?>",
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
					})(jQuery);
					</script>
				  </div>
				</div>
				<!-- Textarea -->
				<div class="form-group">
					<label class="control-label" for="textarea">Description</label>
					<div class="">
						<!-- <textarea id="post_content" name="textarea" rows="7" placeholder="Description"  class="form-control"></textarea> -->
                      <?php wp_editor( ' ', 'post_content', array('media_buttons' => false) );?>
					  <style type="text/css">.wp-editor-container{border:1px solid #e5e5e5;}</style>
					</div>
				</div>
				<!-- Textarea -->
				<div class="form-group">
					<p class="help-block"><em>Server time: <code id="server-time"></code></em></p>
					<script type="text/javascript">
						var svrTime = <?php echo current_time('timestamp', 0);?>;
						window.setInterval('svrTimeDisplay()', 1000);
						function svrTimeDisplay() {
							svrTime += 1;
							var date = new Date(svrTime*1000);
							var month = "0" + (date.getUTCMonth() + 1);
							var day = "0" + date.getUTCDate();
							var year = date.getUTCFullYear();
							var hours = date.getUTCHours();
							var minutes = "0" + date.getUTCMinutes();
							var seconds = "0" + date.getUTCSeconds();
							var ampm = hours >= 12 ? 'PM' : 'AM';
							hours = hours % 12;
							hours = hours % 12;
							hours = "0" + hours;
							var formattedTime = month.substr(month.length-2) + "/" + day.substr(day.length-2) + "/" + year + ' ' +
								hours.substr(hours.length-2) + ':' + minutes.substr(minutes.length-2) + ':' + seconds.substr(seconds.length-2) + ' ' + ampm;
							// document.getElementById("server-time").innerHTML = date.toGMTString();
							document.getElementById("server-time").innerHTML = formattedTime;
						}
					</script>
				</div>
				<div class="form-group">
					<label class="control-label" for="textarea">Start Date</label>
					<div class="">
					<div class="row">
						<div class="col-md-4">
							<input type="text" id="start_date" class="form-control datepicker" />
						</div>
						<div class="col-md-8">
							<code style="display:inline-block;"><?php _e('at'); ?></code>
							<select name="contest-beast[start-time-hour]" id="start_hour" class="form-control" style="width:auto;display:inline-block;">
								<?php for($hour = 1; $hour <= 12; $hour++) { $hour = sprintf('%02d', $hour); ?>
								<option><?php echo $hour; ?></option>
								<?php } ?>
							</select>
							<code style="display:inline-block;">:</code>
							<select name="contest-beast[start-time-minute]" id="start_minute" class="form-control" style="width:auto;display:inline-block;">
								<?php for($minute = 0; $minute <= 59; $minute++) { $minute = sprintf('%02d', $minute); ?>
								<option><?php echo $minute; ?></option>
								<?php } ?>
							</select>
							<select name="contest-beast[start-time-meridian]" id="start_meridian" class="form-control" style="width:auto;display:inline-block;">
								<option>AM</option>
								<option>PM</option>
							</select>
						</div>
					</div>
					</div>
				</div>
				<!-- Textarea -->
				<div class="form-group">
					<label class="control-label" for="textarea">End Date</label>
					<div class="row">
					<div class="col-md-4">
						<input type="text" id="end_date" class="form-control input-md datepicker" />
					</div>
					<div class="col-md-8">
						<code ><?php _e('at'); ?></code>
						<select name="contest-beast[end-time-hour]" id="end_hour" class="form-control" style="width:auto;display:inline-block;">
							<?php for($hour = 1; $hour <= 12; $hour++) { $hour = sprintf('%02d', $hour); ?>
							<option><?php echo $hour; ?></option>
							<?php } ?>
						</select>
						<code >:</code>
						<select name="contest-beast[end-time-minute]" id="end_minute" class="form-control" style="width:auto;display:inline-block;">
							<?php for($minute = 0; $minute <= 59; $minute++) { $minute = sprintf('%02d', $minute); ?>
							<option><?php echo $minute; ?></option>
							<?php } ?>
						</select>
						<select name="contest-beast[end-time-meridian]" class="form-control" id="end_meridian" style="width:auto;display:inline-block;">
							<option>AM</option>
							<option>PM</option>
						</select>
					</div>
					</div>
				</div>
                <!-- Text input-->
				<div class="form-group">
				  <label class="control-label" for="countdown">Countdown template</label>
				  <div class="">
					<select name="countdown" id="countdown" class="form-control">
						<?php
						$mcControler = MotionCountdownController::getInstance();
						$mcLoader = MotionCountdownLoader::getInstance();
						$mcLoader->load($mcControler->pluginFilePath . 'model/generator_templates/');
						$mctURL = $mcControler->templatesDirectoryURLPath;
						$templatesInformation = array();
						foreach (get_declared_classes() as $className) {
						  if (is_subclass_of($className, MotionCountdownController::getInstance()->modelGenerator->templateAbstractClass)) {
							$classInstance = new $className();
							echo '<option value="'.$classInstance->getTemplateAlias().'">'.$classInstance->getTemplateName().'</option>';
							$templatesInformation[] = "'".$classInstance->getTemplateAlias()."': '".$classInstance->getBackgroundImageName()."'";
						  }
						}
						?>
					</select>
					<br />
					<div id="countdown-preview" class="ptr-preview"></div>
					<script type="text/javascript">
						var mctemplate = {<?php echo implode(",", $templatesInformation); ?>};
						jQuery('#countdown').on('change', function() {
							jQuery('#countdown-preview').html('<img src="<?php echo $mctURL;?>'+mctemplate[jQuery(this).val()]+'" alt="" style="max-width:100%"/>');
						}).change();
					</script>
				  </div>
				</div>
				<!-- Text input-->
				<div class="form-group">
					<label class="control-label" for="textinput">Entries per Referral</label>
					<div class="">
					<input id="referral" name="textinput" type="text" style="" class="form-control input-md">
					<small style="">Enter the number of entries you want a participant to receive when referring another user to this contest.</small>
					</div>
				</div>
				<!-- Text input-->
				<div class="form-group">
					<label class="control-label" for="textinput">Number of Winners</label>
					<div class="">
						<input id="winners" name="textinput" type="text" style="" class="form-control input-md">
						<small style="">Enter the number of winners you want this contest to have.</small>
					</div>
				</div>
				<!-- Text input-->
				<div class="form-group">
					<label class="control-label" for="textinput">Disclaimer</label>
					<div class="">
					<textarea id="disclaimer" name="textarea" rows="7" placeholder="Description"  class="form-control"></textarea>
					<small style="">Enter a short contest disclaimer. It will be displayed in a semi-prominent position on the contest page.</small>
					</div>
				</div>
                                <!-- Text input-->
				<div class="form-group">
					<label class="control-label" for="textinput">Terms Of Service</label>
					<div class="">
					<textarea id="termsofservice" name="textarea" rows="7" placeholder="Description"  class="form-control"></textarea>
					<small style="">Enter a Terms of Service Details for contest. It will be displayed in a semi-prominent position on the contest page.</small>
					</div>
				</div>
                                <!-- Text input-->
				<div class="form-group">
					<label class="control-label" for="textinput">Privacy Policy Box</label>
					<div class="">
					<textarea id="privacypolicybox" name="textarea" rows="7" placeholder="Description"  class="form-control"></textarea>
					<small style="">Enter a Privacy Policy Box Details for contest. It will be displayed in a semi-prominent position on the contest page.</small>
					</div>
				</div>
				<!-- Text input-->
				<div class="form-group">
					<label class="control-label" for="textinput">Custom Rules?</label>
					<div class="">
					<label>
					<input type="checkbox" name="contest-beast[contest-rules-custom]" id="contest_rules" />
				</label>
					<small style="">This contest has custom rules (you will enter them below)</small>
					</div>
				</div>
				<!-- Text input-->
				<div class="form-group open_rules" style="display:none;" data-dependency="contest-beast[contest-rules-custom]" data-dependency-value="yes">
					<div class="">
					 <textarea id="rules" name="textarea" rows="7" placeholder="Enter your rules"  class="form-control"></textarea>
					</div>
				</div><!-- Text input-->
				<div class="form-group">
				  <label class="control-label" for="textinput">Add Participants to Mailing List?</label>
				  <div class="">
					<input type="checkbox" name="contest-beast[contest-rules-custom]" id="custom_mailing" checked="checked" />
					<textarea id="mailing" name="textarea" rows="7" placeholder="Enter your form code"  class="form-control"></textarea>
					<p>Your thank you page url: <code id="thankyou-page"></code></p>
					<div class="contest-beast-opt-in-code-dependent">
						<label for="contest-beast-opt-in-form-url" class="control-label">Form Action</label>
						<input type="text" class="form-control" name="contest-beast[opt-in-form-url]" id="contest-beast-opt-in-form-url" />
					</div>
					<div class="row contest-beast-opt-in-code-dependent">
						<div class="col-md-6">
							<label for="contest-beast-opt-in-form-email-field" class="control-label">Email</label>
							<select type="text" class="form-control contest-beast-opt-in-code-fields" data-contest-beast-field="email" name="contest-beast[opt-in-form-email-field]" id="contest-beast-opt-in-form-email-field" ></select>
						</div>
						<div class="col-md-6">
							<label for="contest-beast-opt-in-name-field" class="control-label">Name</label>
							<select type="text" class="form-control contest-beast-opt-in-code-fields" data-contest-beast-field="name" name="contest-beast[opt-in-form-name-field]" id="contest-beast-opt-in-form-name-field" ></select>
						</div>
					</div>
				  </div>
					<script type="text/javascript">
						function parseOptInCode(code) {
							var form_code = jQuery.trim(code),
								$parsed_form = jQuery(form_code),
								$form = $parsed_form.find('form');
							if(0 == $form.size()) {
								$form = $parsed_form.filter('form');
							}
							if(0 == $form.size()) {
								return false;
							}
							var form_action = $form.attr('action'),
								hidden_inputs = {},
								other_inputs = [],
								lowercased = '',
								email_input_name = '',
								name_input_name = '';
							$parsed_form.find('input[type!="submit"]').each(function(index, input) {
								var $input = jQuery(input),
									input_name = $input.attr('name'),
									input_type = $input.attr('type'),
									input_value = $input.val();
								if('hidden' == input_type) {
									hidden_inputs[input_name] = input_value;
								} else {
									other_inputs.push(input_name);
									lowercased = input_name.toLowerCase();
									if(-1 < lowercased.indexOf('email')) {
										email_input_name = input_name;
									} else if(-1 < lowercased.indexOf('name')) {
										name_input_name = input_name;
									}
								}
							});
							return {
								'action' : form_action,
								'email'  : email_input_name,
								'name'   : name_input_name,
								'hidden' : hidden_inputs,
								'others' : other_inputs
							};
						}
						jQuery(document).ready(function ($) {
							$('#mailing').on('change', function(event) {
								var $this = $(this)
								, $dependents = $('.contest-beast-opt-in-code-dependent')
								, $parent = $this.parents('td')
								, form_code = $.trim($this.val());
								$parent.find('.contest-beast-opt-in-form-field').remove();
								if('' == form_code) {
									$dependents.hide();
								} else {
									$dependents.show();
									$parsed_form = parseOptInCode(form_code);
									if($parsed_form == false) {
										// alert('Contest Beast could not find a form element in the Opt In Form Code you entered. Please copy the entire HTML code block from your mailing list provider into the Opt In Form Code field.');
										$dependents.hide();
										return;
									}
									$('#contest-beast-opt-in-form-url').val($parsed_form.action);
									$('.contest-beast-opt-in-code-fields').each(function(index, select) {
										var $select = $(select)
										, previous_value = $select.val()
										, splash_field = $select.attr('data-contest-beast-field');
										$select.empty();
										$.each($parsed_form.others, function(other_inputs_index, other_input) {
											$select.append($('<option></option>').attr('value', other_input).text(other_input));
										});
										if('' != previous_value && -1 < $.inArray(previous_value, $parsed_form.others)) {
											$select.val(previous_value);
										} else {
											if('email' == splash_field && '' != $parsed_form.email) {
												$select.val($parsed_form.email);
											} else if('name' == splash_field && '' != $parsed_form.name) {
												$select.val($parsed_form.name);
											}
										}
									});
								}
							}).change();
						});
					</script>
					<!-- endPT -->
				</div>
				<div class="form-group">
					<label class="control-label" for="textinput">Branding Logo</label>
					<form enctype="multipart/form-data">
						<input type="file" name="logo" id="logo" />
						<input type="hidden" name="logo_url" id="logo_url" />
						<input type="hidden" name="action" value="upload_image" />
						<br />
						<div class="progress" id="logo_upload_progress">
							<div class="progress-bar"  role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style=""></div>
						</div>
					   <div id="logo-preview" class="ptr-preview"></div>
					   <script type="text/javascript">
					   (function ($) {
						$(document).ready(function () {
							$('#logo_upload_progress').hide();
							// Change this to the location of your server-side upload handler:
							$('#logo').fileupload({
								url : "<?php echo admin_url('admin-ajax.php'); ?>",
								dataType : 'json',
								done : function (e, data) {
									$('#logo_upload_progress').hide();
									$('#logo-preview').html('<img src="'+data.result.url+'" alt="" width="80" />')
										.append('<a href="#" class="remove"><i class="fa fa-times"></i></a>');
									$('#logo_url').val(data.result.url);
								},
								progressall : function (e, data) {
									$('#logo_upload_progress').show();
									var progress = parseInt(data.loaded / data.total * 100, 10);
									$('#logo_upload_progress .progress-bar').css(
										'width',
										progress + '%').text(progress + '%');
								}
							}).prop('disabled', !$.support.fileInput)
							.parent().addClass($.support.fileInput ? undefined : 'disabled');
						});
					   })(jQuery);
					   </script>
					</form>
				</div>
				
				<div class="form-group">
					<div class="row">
						<div class="col-md-6">
							<label class="control-label">Body background color</label>
							<div class="input-group">
								<span class="input-group-addon">#</span>
								<input type="text" name="body_background_color" id="body_background_color" class="form-control" />
							</div>
							<script type="text/javascript">
								(function ($) {
									$(document).ready(function () {
										var $bgc = $('#body_background_color');
										$bgc.ColorPicker({
											onSubmit: function(hsb, hex, rgb, el) {
												$(el).val(hex);
												$(el).ColorPickerHide();
											},
											onBeforeShow: function () {
												$(this).ColorPickerSetColor($bgc.val());
											},
											onChange: function (hsb, hex, rgb) {
												$bgc.val(hex);
											}
										})
										.bind('keyup', function(){
											$(this).ColorPickerSetColor($bgc.val());
										});
									});
								})(jQuery);
							</script>
						</div>
						<div class="col-md-6">
							<label class="control-label">Contest background color</label>
							<div class="input-group">
								<span class="input-group-addon">#</span>
								<input type="text" name="contest_background_color" id="contest_background_color" class="form-control" />
							</div>
							<script type="text/javascript">
								(function ($) {
									$(document).ready(function () {
										var $bgc = $('#contest_background_color');
										$bgc.ColorPicker({
											onSubmit: function(hsb, hex, rgb, el) {
												$(el).val(hex);
												$(el).ColorPickerHide();
											},
											onBeforeShow: function () {
												$(this).ColorPickerSetColor($bgc.val());
											},
											onChange: function (hsb, hex, rgb) {
												$bgc.val(hex);
											}
										})
										.bind('keyup', function(){
											$(this).ColorPickerSetColor($bgc.val());
										});
									});
								})(jQuery);
							</script>
						</div>
					</div>
				</div>
				
				<div class="form-group">
					<div class="row">
						<div class="col-md-6">
							<label class="control-label">Body background image</label>
							<form enctype="multipart/form-data">
								<input type="file" name="body_background_image" id="body_background_image" />
								<input type="hidden" name="body_background_image_url" id="body_background_image_url" />
								<input type="hidden" name="action" value="upload_image" />
								<br />
								<div class="progress" id="body_background_image_upload_progress">
									<div class="progress-bar"  role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style=""></div>
								</div>
								<div id="body_background_image-preview" class="ptr-preview"></div>
								<script type="text/javascript">
								(function ($) {
									$(document).ready(function () {
										$('#body_background_image_upload_progress').hide();
										// Change this to the location of your server-side upload handler:
										$('#body_background_image').fileupload({
											url : "<?php echo admin_url('admin-ajax.php'); ?>",
											dataType : 'json',
											done : function (e, data) {
												$('#body_background_image_upload_progress').hide();
												$('#body_background_image-preview')
													.html('<img src="'+data.result.url+'" alt="" width="80" />')
													.append('<a href="#" class="remove"><i class="fa fa-times"></i></a>');;
												$('#body_background_image_url').val(data.result.url);
											},
											progressall : function (e, data) {
												$('#body_background_image_upload_progress').show();
												var progress = parseInt(data.loaded / data.total * 100, 10);
												$('#body_background_image_upload_progress .progress-bar').css(
													'width',
													progress + '%').text(progress + '%');
											}
										}).prop('disabled', !$.support.fileInput)
										.parent().addClass($.support.fileInput ? undefined : 'disabled');
									});
								})(jQuery);
								</script>
							</form>
						</div>
						<div class="col-md-6">
							<label class="control-label">Contest background image</label>
							<form enctype="multipart/form-data">
								<input type="file" name="contest_background_image" id="contest_background_image" />
								<input type="hidden" name="contest_background_image_url" id="contest_background_image_url" />
								<input type="hidden" name="action" value="upload_image" />
								<br />
								<div class="progress" id="contest_background_image_upload_progress">
									<div class="progress-bar"  role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style=""></div>
								</div>
								<div id="contest_background_image-preview" class="ptr-preview"></div>
								<script type="text/javascript">
								(function ($) {
									$(document).ready(function () {
										$('#contest_background_image_upload_progress').hide();
										// Change this to the location of your server-side upload handler:
										$('#contest_background_image').fileupload({
											url : "<?php echo admin_url('admin-ajax.php'); ?>",
											dataType : 'json',
											done : function (e, data) {
												$('#contest_background_image_upload_progress').hide();
												$('#contest_background_image-preview')
													.html('<img src="'+data.result.url+'" alt="" width="80" />')
													.append('<a href="#" class="remove"><i class="fa fa-times"></i></a>');
												$('#contest_background_image_url').val(data.result.url);
											},
											progressall : function (e, data) {
												$('#contest_background_image_upload_progress').show();
												var progress = parseInt(data.loaded / data.total * 100, 10);
												$('#contest_background_image_upload_progress .progress-bar').css(
													'width',
													progress + '%').text(progress + '%');
											}
										}).prop('disabled', !$.support.fileInput)
										.parent().addClass($.support.fileInput ? undefined : 'disabled');
									});
								})(jQuery);
								</script>
							</form>
						</div>
					</div>
				</div>
				
				<!-- Text input-->
				<div class="form-group">
					<label class="control-label" for="">Contest Button</label>
					<div class="row">
						<div class="col-md-6">
							<p>Select from our template</p>
							<select name="button_tpl" id="button_tpl" class="form-control">
								<option value="<?php echo site_url('/wp-content/plugins/contest-beast/views/frontend/templates/resources/img/enter-contest-button.png');?>" selected="selected">Default</option>
								<?php
									$imgExt = array('gif','jpg','jpeg','png');
									$btnDir = get_stylesheet_directory() . '/member_dashboard_assets/img/buttons';
									$btnUrl = get_stylesheet_directory_uri() . '/member_dashboard_assets/img/buttons';
									$btnImg = scandir($btnDir, 1);
									foreach($btnImg as $img) {
										$info = pathinfo($img);
										$name = ucwords(preg_replace('/[_-]/', ' ', $info['filename']));
										$exts = strtolower($info['extension']);
										if (in_array($exts, $imgExt)) {
											echo "<option value='$btnUrl/$img'>$name</option>";
										}
									}
								?>
							</select>
							<br />
							<div id="button_tpl_preview"></div>
							<script type="text/javascript">
								var mctemplate = {<?php echo implode(",", $templatesInformation); ?>};
								jQuery('#button_tpl').on('change', function() {
									jQuery('#button_tpl_preview').html('<img src="'+jQuery(this).val()+'" alt="" style="max-width:100%"/>');
								}).change();
							</script>
						</div>
						<div class="col-md-6">
							<p>or Upload your own button</p>
							<form enctype="multipart/form-data">
								<input type="file" name="button_img" id="button_img" />
								<input type="hidden" name="button_img_url" id="button_img_url" />
								<input type="hidden" name="action" value="upload_image" />
								<br />
								<div class="progress" id="button_img_upload_progress">
									<div class="progress-bar"  role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style=""></div>
								</div>
								<div id="button_img-preview" class="ptr-preview"></div>
							   <script type="text/javascript">
							   (function ($) {
								$(document).ready(function () {
									$('#button_img_upload_progress').hide();
									// Change this to the location of your server-side upload handler:
									$('#button_img').fileupload({
										url : "<?php echo admin_url('admin-ajax.php'); ?>",
										dataType : 'json',
										done : function (e, data) {
											$('#button_img_upload_progress').hide();
											$('#button_img-preview')
												.html('<img src="'+data.result.url+'" alt="" width="80" />')
												.append('<a href="#" class="remove"><i class="fa fa-times"></i></a>');
											$('#button_img_url').val(data.result.url);
										},
										progressall : function (e, data) {
											$('#button_img_upload_progress').show();
											var progress = parseInt(data.loaded / data.total * 100, 10);
											$('#button_img_upload_progress .progress-bar').css(
												'width',
												progress + '%').text(progress + '%');
										}
									}).prop('disabled', !$.support.fileInput)
									.parent().addClass($.support.fileInput ? undefined : 'disabled');
								});
							   })(jQuery);
							   </script>
							</form>
						</div>
					</div>
				</div>
			</fieldset>
		</div></div>
		</div>
		<div class="modal-footer">
		<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		<button id="update_spy_link" type="button" class="btn btn-primary">Save changes</button>
		</div>
	</div>
  </div>
</div>
<!-- Modal -->
<div class="modal fade" id="myModal_2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm">
	<div class="modal-content">
		<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h4 class="modal-title" id="myModalLabel">Delete Contest</h4>
		</div>
		<div class="modal-body">
			<input type="hidden" id="delete_spy_link_id" value="">
			Do you want to delete this Contest?
		</div>
		<div class="modal-footer">
		<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
		<button id="delete_spy_link_button" type="button" class="btn btn-danger">Yes, delete it</button>
		</div>
	</div>
  </div>
</div>
<script type="text/javascript">
   /* $(document).ready(function(){
		$('input[type="checkbox"]').click(function(){
			if($(this).attr("value")=="yes"){
				$(".open_rules").toggle();
			}
		});
	});*/
</script>
<?php include('member_dashboard_files/footer.php'); ?>
dashboard main
