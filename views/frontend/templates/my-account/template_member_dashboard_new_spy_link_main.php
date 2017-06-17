<?php
/*
Template Name: Member Dashboard - New Spy Link
*/
get_template_part('member_dashboard_files/header');

global $current_user;
get_currentuserinfo();
?>

<style type="text/css">
.required_notification {line-height: 1.4em;padding: 5px;display: inline-block;color: red;display:none;}
#url_checking {height: 10px;margin-top: 8px;}
#custom_mailing {display:none;}
</style>

<h1 class="page-header"><span class="glyphicon glyphicon-target" style="color:#000;"></span><i class="fa fa-edit"></i>  New Contest</h1>
<div class="col-md-8 col-md-offset-2">
	<fieldset>
		<!-- / -->
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
		<div class="form-group">
			<label class="control-label" for="textarea">Description</label>
			<div class="">
				<!-- <textarea id="post_content" name="textarea" rows="7" class="form-control"></textarea> -->
				<?php wp_editor( ' ', 'post_content', array('media_buttons' => false) );?>
				<style type="text/css">.wp-editor-container{border:1px solid #e5e5e5;}</style>
			</div>
			<div class="required_notification" >This field is required!</div>
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
		<!-- / -->
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
		<!-- / -->
		<div class="form-group">
			<label class="control-label" for="textinput">Disclaimer</label>
			<div class="">
			<textarea id="disclaimer" name="textarea" rows="7" placeholder=""  class="form-control"></textarea>
			<span >Enter a short contest disclaimer. It will be displayed in a semi-prominent position on the contest page.</span>
			</div>
<div class="required_notification" >This field is required!</div>
		</div>
                <!-- / -->
		<div class="form-group">
			<label class="control-label" for="textinput">Terms Of Service</label>
			<div class="">
			<textarea id="termsofservice" name="textarea" rows="7" placeholder=""  class="form-control"></textarea>
			<span >Enter a Terms of Service Details for contest. It will be displayed in a semi-prominent position on the contest page.</span>
			</div>
<div class="required_notification" >This field is required!</div>
		</div>
		<div class="form-group">
			<label class="control-label" for="textinput">Privacy Policy Box</label>
			<div class="">
			<textarea id="privacypolicybox" name="textarea" rows="7" placeholder=""  class="form-control"></textarea>
			<span >Enter a Privacy Policy Box Details for contest. It will be displayed in a semi-prominent position on the contest page.</span>
			</div>
<div class="required_notification" >This field is required!</div>
		</div>
		<!-- / -->
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
		<!-- / -->
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
		<!-- / -->
		<div class="form-group">
			<label class="control-label" for="textinput">Custom Tracking Scripts?</label>
			<div class="">
			<label>
			<input type="checkbox" name="contest-beast[contest-rules-custom]" id="custom_scripts" />
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
		<!-- / -->
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
											.append('<a href="#" class="remove"><i class="fa fa-times"></i></a>');
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
		
		<!-- Button -->
		<div class="form-group">
			<button id="create_spy_link" name="singlebutton" type="button" class="btn btn-primary">Create Contest</button>
		</div>
		<script type="text/javascript">
		jQuery(document).ready(function ($) {
			$('.datepicker').datepicker({minDate: 0});
			function parseOptInCode(code) {
				var form_code = $.trim(code),
					$parsed_form = $(form_code),
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
					var $input = $(input),
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
			var ajax_call_number = 0;
			$(document).on('keyup', '#spy_url', function () {
				if ($('#spy_url').val() == '') {
					$('.spy_link_notification').hide();
					return;
				}
				$('.spy_link_notification').hide();
				$('#spy_link_searching').show();
				$('#create_spy_link').attr('disabled', 'disabld');
				ajax_call_number = ajax_call_number + 1;
				$.ajax({
					type : 'POST',
					url : "<?php echo admin_url('admin-ajax.php'); ?>",
					data : {
						action : 'labdevs_is_spy_url_available',
						slug : $('#spy_url').val(),
						ajax_call_number : ajax_call_number
					},
					success : function (data) {
						data_2 = data;
						if (ajax_call_number == data_2.trim().split('|').shift()) {
							console.log('in');
							if (data.trim().split('|').pop().replace('0', '') == 'false') {
								$('.spy_link_notification').hide(); ;
								$('#spy_link_fail').show();
								$('#create_spy_link').attr('disabled', 'disabld');
							} else {
								$('.spy_link_notification').hide(); ;
								$('#spy_link_success').show();
								$('#create_spy_link').removeAttr('disabled');
							}
						}
					}
				});
			});
			jQuery('#contest_rules').click(function () {
				if (jQuery(this).is(':checked')) {
					jQuery(".open_rules").show();
					jQuery(this).val('yes');
				} else {
					jQuery(this).val('no');
					jQuery(".open_rules").hide();
				}
				// jQuery(".open_rules").toggle();
			})
			jQuery('#custom_scripts').click(function () {
				if (jQuery(this).is(':checked')) {
					jQuery(".open_scripts").show();
					jQuery(this).val('yes');
				} else {
					jQuery(this).val('no');
					jQuery(".open_scripts").hide();
				}
				// jQuery(".open_rules").toggle();
			})
			jQuery('#custom_mailing').click(function () {
				if (jQuery(this).is(':checked')) {
					jQuery(".open_mailing").show();
					jQuery(this).val('yes');
				} else {
					jQuery(this).val('no');
					jQuery(".open_mailing").hide();
				}
				// jQuery(".open_rules").toggle();
			})
			$(document).on('click', '#create_spy_link', function (e) {
				e.preventDefault();
				$('.required_notification').hide();
				var contest_title = $(this).attr('#contest_title');
				var post_content = $(this).attr('#post_content');
				var start_date = $(this).attr('#start_date');
				var start_hour = $(this).attr('#start_hour');
				var start_minute = $(this).attr('#start_minute');
				var start_meridian = $(this).attr('#start_meridian');
				var end_date = $(this).attr('#end_date');
				var end_hour = $(this).attr('#end_hour');
				var end_minute = $(this).attr('#end_minute');
				var end_meridian = $(this).attr('#end_meridian');
				var referral = $(this).attr('#referral');
				var winners = $(this).attr('#winners');
				var disclaimer = $(this).attr('#disclaimer');
				var termsofservice = $(this).attr('#termsofservice');
				var privacypolicybox = $(this).attr('#privacypolicybox');
				var contest_rules = $(this).attr('#contest_rules');
				var rules = $(this).attr('#rules');
				var contest_title = $('#contest_title').val();
				var post_content = $('#post_content').val();
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
				// $(logo).eq('#logo').val();
				//alert(contest_rules);
				if (contest_rules == 'yes') {
					jQuery('#contest_rules').attr('checked', 'checked');
					jQuery('#contest_rules').val('yes');
					jQuery('.open_rules').show();
				} else {
					jQuery('#contest_rules').val('no');
				}
				//$('#contest_rules').val( contest_rules );
				$('#rules').val(rules);
				//alert(contest_rules);
				if (custom_scripts == 'yes') {
					jQuery('#custom_scripts').attr('checked', 'checked');
					jQuery('#custom_scripts').val('yes');
					jQuery('.open_scripts').show();
				} else {
					jQuery('#custom_scripts').val('no');
				}
				//$('#contest_rules').val( contest_rules );
				$('#scripts').val(scripts);
				//alert(contest_rules);
				if (custom_mailing == 'yes') {
					jQuery('#custom_mailing').attr('checked', 'checked');
					jQuery('#custom_mailing').val('yes');
					jQuery('.open_mailing').show();
				} else {
					jQuery('#custom_scripts').val('no');
				}
				//$('#contest_rules').val( contest_rules );
				var required_flag = false;
				if ($('#contest_title').val() === "") {
					$('#contest_title').closest('.form-group').find('.required_notification').show();
					required_flag = true;
				}
				var content, inputid = 'post_content';
				var editor = tinyMCE.get(inputid);
				if (editor) {
					content = editor.getContent();
				} else {
					content = $('#'+inputid).val();
				}
				if (content === "") {
					$('#'+inputid).parent().closest('.form-group').find('.required_notification').show();
					required_flag = true;
				}
				if ($('#start_date').val() === "") {
					$('#start_date').parent().closest('.form-group').find('.required_notification').show();
					required_flag = true;
				}
				if ($('#start_hour').val() === "") {
					$('#start_hour').parent().closest('.form-group').find('.required_notification').show();
					required_flag = true;
				}
				if ($('#start_minute').val() === "") {
					$('#start_minute').parent().closest('.form-group').find('.required_notification').show();
					required_flag = true;
				}
				if ($('#start_meridian').val() === "") {
					$('#start_meridian').parent().closest('.form-group').find('.required_notification').show();
					required_flag = true;
				}
				if ($('#end_date').val() === "") {
					$('#end_date').parent().closest('.form-group').find('.required_notification').show();
					required_flag = true;
				}
				if ($('#end_hour').val() === "") {
					$('#end_hour').parent().closest('.form-group').find('.required_notification').show();
					required_flag = true;
				}
				if ($('#end_minute').val() === "") {
					$('#end_minute').parent().closest('.form-group').find('.required_notification').show();
					required_flag = true;
				}
				if ($('#end_meridian').val() === "") {
					$('#end_meridian').parent().closest('.form-group').find('.required_notification').show();
					required_flag = true;
				}
				if ($('#referral').val() === "") {
					$('#referral').parent().closest('.form-group').find('.required_notification').show();
					required_flag = true;
				}
				if ($('#winners').val() === "") {
					$('#winners').parent().closest('.form-group').find('.required_notification').show();
					required_flag = true;
				}
				if ($('#disclaimer').val() === "") {
					$('#disclaimer').parent().closest('.form-group').find('.required_notification').show();
					required_flag = true;
				}
                                if ($('#termsofservice').val() === "") {
					$('#termsofservice').parent().closest('.form-group').find('.required_notification').show();
					required_flag = true;
				}
                                if ($('#privacypolicybox').val() === "") {
					$('#privacypolicybox').parent().closest('.form-group').find('.required_notification').show();
					required_flag = true;
				}
				if ($('#mailing').val() == "") {
					$('#mailing').parent().closest('.form-group').find('.required_notification').show();
					required_flag = true;
				}
				if (required_flag === true) {
					return;
				}
				$.ajax({
					type : "POST",
					url : "<?php echo admin_url('admin-ajax.php'); ?>",
					data : {
						action : 'labdevs_create_spy_link',
						contest_title : contest_title,
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
						mc_template: $('#countdown').val(),
						body_background_color: $('#body_background_color').val(),
						body_background_image: $('#body_background_image_url').val(),
						contest_background_color: $('#contest_background_color').val(),
						contest_background_image: $('#contest_background_image_url').val(),
						button_tpl: $('#button_tpl').val(),
						button_img: $('#button_img_url').val()
					}
				}).done(function (data) {
					console.log(data);
					$('#myModal').modal('show');
					update_spy_links_counter();
					$('.form-horizontal').find("input[type=text], textarea").val("");
				});
				e.preventDefault();
			});
		});
		</script>
	</fieldset>
</div>
<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
	<div class="modal-content">
		<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h4 class="modal-title" id="myModalLabel">Success!</h4>
		</div>
		<div class="modal-body">
		Contest was successfully created!
		</div>
		<div class="modal-footer">
		<button type="button" class="btn btn-primary" data-dismiss="modal">Add More Contests</button>
		<a href="<?php echo get_page_link(9); ?>" type="button" class="btn btn-primary">Return Home</a>
		</div>
	</div>
  </div>
</div>
<?php get_template_part('member_dashboard_files/footer'); ?>