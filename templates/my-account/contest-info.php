<?php
/*
Template Name: Member Dashboard - New Spy Link
*/

global $current_user;
get_currentuserinfo();


?>
<style type="text/css">
.contest_featured_image_error{
   background-color: #fffcfc;
   color: red; 
   width: 320px; 
   padding: 10px; 
   border: 3px solid #fc8c8a;
   margin: 10px;
}

table, th, td {
    border: 1px solid black;
}
</style>
<style type="text/css">
.required_notification {line-height: 1.4em;padding: 5px;display: inline-block;color: red;display:none;}
#url_checking {height: 10px;margin-top: 8px;}
#custom_mailing {display:none;}
.well{


background: #42275a; /* fallback for old browsers */
background: -webkit-linear-gradient(to left, #42275a , #734b6d); /* Chrome 10-25, Safari 5.1-6 */
background: linear-gradient(to left, #42275a , #734b6d); /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */

}
body{




	background: #42275a; /* fallback for old browsers */
	background: -webkit-linear-gradient(to left, #42275a , #734b6d); /* Chrome 10-25, Safari 5.1-6 */
	background: linear-gradient(to left, #42275a , #734b6d); /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */


}
.contest-buzz-create-background{
	background: #FFF;
}
</style>
<div style="margin-top:50px">
<div style="margin-top:50px">
	<div id="contest_info" style="display:none">
	<div class="form-group">
				<label class="control-label" for="textinput">Contest Title</label>
				<div class="">
				<input id="contest_title" value="" name="contest_title" type="text" class="form-control input-md">
				<script type="text/javascript">
				(function($) {
					$("#contest_title").change(function(e) {
						$.post(
							"<?php echo admin_url('admin-ajax.php'); ?>",
							{
								action: 'gen_contest_url',
								title: $('#contest_title').val()
							},
							function( data ) {
								if ( data != '-1' ) {
									$('#url').val(data);
									$('#thankyou-page').html($('#url_prefix').val() + data);
								}
							}
						);
					});
				})(jQuery);
				</script>
				</div>
				<div class="required_notification" >This field is required!</div>
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
				<div id="url_checking" class="progress progress-striped active" ><div class="progress-bar" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%"><span class="sr-only">100% Complete</span></div></div>
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
			<!--div class="form-group">
				<label class="control-label" for="textarea">Description</label>
				<div class="">
					<?php #wp_editor( ' ', 'post_content', array('media_buttons' => false) );?>
					<style type="text/css">.wp-editor-container{border:1px solid #e5e5e5;}</style>
				</div>
				<div class="required_notification" >This field is required!</div>
			</div-->
			
			<div class="form-group">
				<label class="control-label" for="textinput">Description <code>For youtube videos copy embed code from youtube and paste it here</code></label>
				<div class="">
				<!--input id="contest_video" value="" name="contest_video" type="text" class="form-control input-md"-->
				<?php wp_editor( '', 'contest_video', array('media_buttons' => false) ); ?>
				<!--p> Copy the Video ID of your desired video post (example: https://www.youtube.com/watch?v=l5jNyT_dsoc) this is the Video id v=l5jNyT_dsoc</p-->
				</div>
				<div class="required_notification" >This field is required!</div>
			</div>
	<div class="form-group">
				<label class="control-label" for="textinput">Branding Logo</label>
				<form enctype="multipart/form-data">
					<input type="file" name="logo" id="logo" />
					<input type="hidden" name="logo_url" id="logo_url" />
					<input type="hidden" name="action" value="upload_image" />
					<br />
					<div class="progress" id="logo_upload_progress">
						<div class="progress-bar"  role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" ></div>
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
									$('#logo-preview')
										.html('<img src="'+data.result.url+'" alt="" width="80" />')
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
	</div>