<?php
global $current_user;
get_currentuserinfo();
?>

<div class="labdevs_small_success" style="position: fixed; top: 40px; left: 0px; z-index: 100000; width: 100%; padding: 10px; text-align: center; height: auto; display: none;">
	<span style="color: white;background-color: #80C751;padding: 10px 30px;border-radius: 9px;font-family: monospace;line-height: 1em;">Copied To Clipboard!</span>
</div>
<div>

<h1 class="page-header"><span class="glyphicon glyphicon-stats" style="color:#000;"></span> Your Contests</h1>

					<a href="/my-account/new-contest" style="float:right; margin-top:-70px;" class="btn btn-default ">Add New Contest</a>
					
				
</div>
				
				
		
			
<div id="updating_table" class="alert alert-info" style="display: none;padding: 10px;">
	<strong>Updating Table!</strong> Please wait.
	<div class="progress progress-striped active" style="margin-bottom: 0;margin-top: 9px;height: 10px;">
		<div class="progress-bar" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
		<span class="sr-only">Fetching</span>
		</div>
	</div>
</div>  <!-- #updating_table -->
			
			<div class="table-responsive">
			<table class="table table-striped table table-striped table-bordered dataTable no-footer contestbuzz-table-responsive" style="text-align:center;">
				<thead>
				<tr>
					<th>Title</th>
					<th>Date Published</th>
					<th>Start Date</th>
					<th>End Date</th>
					<th>Submissions</th>
					<th>Entries Per Referral</th>
					<th># Of Winners</th>
					<th>Winners</th>
					<th>Actions</th>
				</tr>
				</thead>

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
				/* echo "<pre>";
				print_r($home_query);
				echo "</pre>";  */
				
				
				if (is_array($home_query->posts) && count($home_query->posts)>0){
					
				
				foreach ($home_query->posts as $contest_post => $obj_post){
				/*show wp object array
				* echo "<pre>";
				* print_r($obj_post);
				* echo "</pre>"; 
				*/
				
				/*
				 * DISPLAYING FUNCTIONS
				 * ====================
				 * Title => the_title();
				 * Permalink => the_permalink();
				 * Content => the_content();
				 *
				 */
				 $tmp = get_permalink($obj_post->ID);
				 $tmp = split('/r/' , $tmp);
				 $permalink = $tmp[1];
				 $post_id = $obj_post->ID;
				 $contest_id = $post_id;
				 
				 ?>
					 <tr>
						<td><?php echo $obj_post->post_title; ?></td>
						<td class="text-center"><?php echo get_the_time('Y-m-d', $obj_post->ID); ?></td>
						<td class="text-center">
							<?php contest_beast_the_contest_start_date(); ?>
						</td>
						<td><?php contest_beast_the_contest_end_date(); ?>
						</td>
						<td><?php contest_beast_the_contest_number_submissions($post_id); ?> <?php $nonce_url = add_query_arg(array('export-contest-beast-entries-nonce' => wp_create_nonce("export-contest-beast-entries-{$post_id}"), 'ID' => $post_id), admin_url('admin.php'));
					printf('<br /><a href="%s">%s</a>', $nonce_url, __('Export Entrants')); ?></td>
						<td><?php contest_beast_the_contest_number_entries($post_id); ?></td>
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
						<!--Dashboard Info by Array deleted...-->
						 
						<input type="hidden" value="<?=get_permalink($obj_post->ID); ?>" class="spy_link_permalink" />
						<button class="the_clipboard btn btn-default btn-sm"><span class="glyphicon glyphicon-paperclip"></span></button>
						 <a href="<?=get_permalink($obj_post->ID); ?>" role="button" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-eye-open"></span></a>
						
						
						<A href="/my-account/edit-contest/<?=$obj_post->ID?>" class="btn btn-default btn-sm" >
								<span class="glyphicon glyphicon-edit"></span> <!--edit button: create on click JQuery to proceed on the dashboard-edit-popup-->
								
								</a>
							<button
								data-id="<?=$obj_post->ID?>"
								class="delete_spy_link_button btn btn-default btn-sm">
								<span class="glyphicon glyphicon-trash"></span>
							</button></td>
					</tr>
				 <?php
				} //end foreach
				} //end if

				// while while posts
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
