
		<!-- Modal -->
			
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
		