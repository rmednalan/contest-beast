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
	
	<div id="schedule" style="display:none">
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
			<div class="required_notification" >This field is required!</div>
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
			<div class="required_notification" >This field is required!</div>
		</div>
		<!-- / -->
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
			<div class="required_notification" >This field is required!</div>
		</div>
	</div>
	
	<div id="mechanics" style="display:none">
	<div class="form-group">
			<label class="control-label" for="textinput">Entries per Referral</label>
			<div class="">
			<input id="referral" name="textinput" type="text"  class="form-control input-md">
			<span >Enter the number of entries you want a participant to receive when referring another user to this contest.</span>
			</div>
<div class="required_notification" >This field is required!</div>
		</div>

		<!-- / -->
		<div class="form-group">
			<label class="control-label" for="textinput">Number of Winners</label>
			<div class="">
			<input id="winners" name="textinput" type="text"  class="form-control input-md">
			<span >Enter the number of winners you want this contest to have.</span>
			</div>
<div class="required_notification" >This field is required!</div>
		</div>
		<div class="form-group">
			<div class="row">
				<div class="col-md-12 col-sm-12">
				<label class="control-label" for="">Giveaway Item Picture</label>
					<form enctype="multipart/form-data">
							<input type="file" name="contest_featured_image" id="contest_featured_image" />
							<input type="hidden" name="contest_featured_image_url" id="contest_featured_image_url" />
							<div id="img_featured_list"></div>
							<input type="hidden" name="action" value="upload_image" />
							<div class='contest_featured_image_error' hidden="hidden">
								The Giveaway Item Picture must be 1MB File Size.
							</div>
							<br />
							<div class="progress" id="contest_featured_image_upload_progress">
								<div class="progress-bar"  role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style=""></div>
							</div>
							<div id="contest_featured_image-preview" class="ptr-preview"></div>
							<script type="text/javascript">
							(function ($) { 
								$(document).ready(function () {
									$('#contest_featured_image_upload_progress').hide();
									// Change this to the location of your server-side upload handler:
									$('#contest_featured_image').fileupload({
										url : "<?php echo admin_url('admin-ajax.php'); ?>",
										dataType : 'json',
										beforeSend: function() {
											var x = Math.round(parseInt(this.files[0].size)/1024);
											if(x > 1000){
												$('.contest_featured_image_error').show();
												return false;
											}
										},
										done : function (e, data) {
											$('.contest_featured_image_error').hide();
											$('#contest_featured_image_upload_progress').hide();
											$('#contest_featured_image-preview')
												.append('<img src="'+data.result.url+'" alt="" width="80"/>')
												.append('<a href="#" class="remove"><i class="fa fa-times"></i></a>');
												
											$('#contest_featured_image_url').val(data.result.url);
											},
										progressall : function (e, data) {
											$('#contest_featured_image_upload_progress').show();
											var progress = parseInt(data.loaded / data.total * 100, 10);
											$('#contest_featured_image_upload_progress .progress-bar').css(
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
				<!-- <div class="col-md-6">
					<label class="control-label">Giveaway Item Picture Description</label>
					<textarea id="picture_desc" name="textarea" rows="6" placeholder="Enter your picture description"  class="form-control"></textarea>
				</div> -->
			</div>
		</div>
	</div>
	<div id="disclaimer" style="display:none">
		<div class="form-group">
			<label class="control-label" for="textinput">Disclaimer</label>
			<div class="">
			<textarea id="disclaimer" name="textarea" rows="7" placeholder=""  class="form-control"></textarea>
			<span >Enter a short contest disclaimer. It will be displayed in a semi-prominent position on the contest page.</span>
			</div>
			<div class="required_notification" >This field is required!</div>
		</div>
	</div>
	
	<div id="terms" style="display:none">
		<div class="form-group">
			<label class="control-label" for="textinput">Terms Of Service</label>
			<div class="">
			<textarea id="termsofservice" name="textarea" rows="7" placeholder=""  class="form-control"></textarea>
			<span >Enter a Terms of Service Details for contest. It will be displayed in a semi-prominent position on the contest page.</span>
			</div>
			<div class="required_notification" >This field is required!</div>
		</div>
	</div>
	
	<div id="privacy_policy" style="display:none">
		<div class="form-group">
			<label class="control-label" for="textinput">Privacy Policy Box</label>
			<div class="">
			<textarea id="privacypolicybox" name="textarea" rows="7" placeholder=""  class="form-control"></textarea>
			<span >Enter a Privacy Policy Box Details for contest. It will be displayed in a semi-prominent position on the contest page.</span>
			</div>
			<div class="required_notification" >This field is required!</div>
		</div>
	</div>
	
	<div id="rules" style="display:none">
		<div class="form-group">
			<label class="control-label" for="textinput">Custom Rules?</label>
			<div class="">
			<label>
			<input type="checkbox" name="contest-beast[contest-rules-custom]" id="contest_rules" />
		</label>
			<span >This contest has custom rules (you will enter them below)</span>
			</div>
		</div>
		<!-- / -->
		<div class="form-group open_rules" style="display:none;" data-dependency="contest-beast[contest-rules-custom]" data-dependency-value="yes">
			 <textarea id="rules" name="textarea" rows="7" placeholder="Enter your rules"  class="form-control"></textarea>
		</div>
	</div>
	
	<div id="mailing_list" style="display:none">
		<div class="form-group">
			<label class="control-label" for="textinput">Add Participants to Mailing List?</label>
			<div class="">
			<input type="checkbox" name="contest-beast[contest-rules-custom]" id="custom_mailing" checked="checked" />
			<textarea id="mailing" name="textarea" rows="7" placeholder="Enter your form Code"  class="form-control"></textarea>
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
			<div class="required_notification">This field is required!</div>
		</div>
	</div>
	
	<div id="tracking_scripts" style="display:none">
		<div class="form-group">
			<label class="control-label" for="textinput">Custom Tracking Scripts?</label>
			<div class="">
			<label>
			<input type="checkbox" name="contest-beast[tracking-scripts-custom]" id="custom_scripts" />
		</label>
			<span >This contest has custom tracking scripts (optional)</span>
			</div>
		</div>
		<!-- / -->
		<div class="form-group open_scripts" style="display:none;" data-dependency="contest-beast[tracking-scripts-custom]" data-dependency-value="yes">
			<label class="control-label" for="textinput">Tracking Script</label>
			<div class="">
			 <textarea id="scripts" name="opt-in-code" rows="7" placeholder=""  class="form-control"></textarea>
			</div>
		</div>
	</div>
	
	<div id="custom_scripts" style="display:none">
		<div class="form-group">
			<label class="control-label" for="user_scripts">Add Custom Scripts <code>For youtube videos copy embed code from youtube and paste it here</code></label>
			<div class="">
			  <?php wp_editor( '', 'user_scripts', array('media_buttons' => false) ); ?>
			  <style type="text/css">.wp-editor-container{border:1px solid #e5e5e5;}</style>
			</div>
		</div>
	</div>

	<div id="styling" style="display:none">
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
	</div>
	
</div>