	<div class="contest-form">		
		<?php
					
			global $wpdb;
			
			$contestId = get_query_var('cbid');
			//delete_post_meta($contestId,'_contest_beast_post_meta');
			
			$contest_post_id = $contestId;
			$contest_detail = $wpdb->get_results( "SELECT * FROM $wpdb->posts WHERE ID = $contest_post_id", OBJECT );
			
			
			$contest_data = $contest_detail[0];
			$contest_data_meta = get_post_meta( $contest_data->ID);
			
			$_contest_beast_post_meta = unserialize($contest_data_meta['_contest_beast_post_meta'][0]);
						
			if (isset($contest_data->ID)){
				$url_slug = str_replace(site_url()."/contest/","", get_permalink( $contestId ));
			}
			
						
					  /* echo "<pre>";
						
						//print_r($_contest_beast_post_meta);
						//print_r($contest_data);
						echo "</pre>"; */
					?>
					
					<div class="row"><div class="col-md-10 col-md-offset-1">
						<fieldset>
							<input type="hidden" id="contest_id" name="contest_id" value="<?=$contest_data->ID;?>">
							<!-- Text input-->
							<div class="form-group">
								<label class="control-label" for="post_title">Title</label>
								<div class="">
								<input id="post_title" value="<?=$contest_data->post_title;?>" name="post_title" type="text" placeholder="Title here..." class="form-control input-md">
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
								  <input id="url" name="contest-beast[contest-url]" value="<?=str_replace('/',"",$url_slug);?>"  type="text" placeholder="URL here..." class="form-control input-md">
								</div><!-- /input-group -->
								<div id="url_checking" class="progress progress-striped active" style=""><div class="progress-bar" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%"><span class="sr-only">100% Complete</span></div></div>
								<div id="url_msg"></div>
							  </div>
							</div>
							
							
							<div class="form-group">
								<label class="control-label" for="textinput">Branding Logo</label>
								<form enctype="multipart/form-data">
									<input type="file" name="logo" id="logo" />
									<input type="hidden" name="contest-beast[branding-logo-url]" value="<?=$_contest_beast_post_meta['branding-logo-url'];?>" id="logo_url" />
									<input type="hidden" name="action" value="upload_image" />
									<br />
									<div class="progress" id="logo_upload_progress">
										<div class="progress-bar"  role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style=""></div>
									</div>
									<?php
									if ($_contest_beast_post_meta['branding-logo-url'] !=""){
										$img_preview_brand_logo = "<img src=\"".$_contest_beast_post_meta['branding-logo-url']."\" style=\"width:80px;\">";
									}
									?>
								   <div id="logo-preview" class="ptr-preview"><?=$img_preview_brand_logo?></div>
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
												$('#logo-preview').html('<img src="'+data.result.url+'" alt="" width="80px" />')
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
							
							<!-- Textarea -->
							<div class="form-group">
								<label class="control-label" for="textarea">Description</label>
								<div class="">
									<textarea id="post_content" class="post_content" name="post_content" rows="7" placeholder="Description" ><?=$contest_data->post_content?></textarea>
									
									
									<script>
									tinymce.init({
   mode : "specific_textareas",
  editor_selector: 'post_content',
  height: 350,
  menubar: false,
  extended_valid_elements : "a[class|name|href|target|title|onclick|rel],script[type|src],iframe[src|style|width|height|scrolling|marginwidth|marginheight|frameborder],img[class|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name]",
  plugins: [
    'advlist autolink lists link image charmap print preview anchor',
    'searchreplace visualblocks code fullscreen',
    'insertdatetime media table contextmenu paste code textcolor'
  ],
  toolbar: 'undo redo | insert | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | code | forecolor backcolor',
  content_css: [
    '//fonts.googleapis.com/css?family=Lato:300,300i,400,400i',
    '//www.tinymce.com/css/codepen.min.css']
});

									</script>
									
								 <?php //wp_editor( $contest_data->post_content, 'post_content', array('media_buttons' => false) );?>
								 
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
										<input type="text" name="contest-beast[start-date]"  id="start_date" value="<?=$_contest_beast_post_meta['start-date']?>" class="form-control datepicker" />
									</div>
									<div class="col-md-8">
										<code style="display:inline-block;"><?php _e('at'); ?></code>
										<select name="contest-beast[start-time-hour]" id="start_hour" class="form-control" style="width:auto;display:inline-block;">
											<?php for($hour = 1; $hour <= 12; $hour++) { 
											$selected_hr = ($_contest_beast_post_meta['start-time-hour']==$hour)?" Selected ":"";
											$hour = sprintf('%02d', $hour); 
											
											?>
											<option value="<?php echo $hour; ?>" <?=$selected_hr;?>><?php echo $hour; ?></option>
											<?php } ?>
										</select>
										<code style="display:inline-block;">:</code>
										
										<select name="contest-beast[start-time-minute]" id="start_minute" class="form-control" style="width:auto;display:inline-block;">
											<?php for($minute = 0; $minute <= 59; $minute++) { $minute = sprintf('%02d', $minute); 
											$selected_mn = ($_contest_beast_post_meta['start-time-minute']==$minute)?" Selected ":"";
											?>
											<option value="<?php echo $minute; ?>" <?=$selected_mn?>><?php echo $minute; ?></option>
											<?php } ?>
										</select>
										<select name="contest-beast[start-time-meridian]" id="start_meridian" class="form-control" style="width:auto;display:inline-block;">
											<?php 
											$meridian = array('AM','PM');
											
											for ($i=0; $i<count($meridian);$i++){
												$selected_meridian = ($_contest_beast_post_meta['start-time-meridian']==$meridian[$i])?" Selected ":"";
												echo "<option value=\"$meridian[$i]\" $selected_meridian>$meridian[$i]</option>";
											}
											?>
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
									<input type="text" name="contest-beast[end-date]"  id="end_date" value="<?=$_contest_beast_post_meta['end-date']?>" class="form-control input-md datepicker" />
								</div>
								<div class="col-md-8">
									<code ><?php _e('at'); ?></code>
									<select name="contest-beast[end-time-hour]" id="end_hour" class="form-control" style="width:auto;display:inline-block;">
										<?php for($hour = 1; $hour <= 12; $hour++) { $hour = sprintf('%02d', $hour); 
										$selected_hr = ($_contest_beast_post_meta['end-time-hour']==$hour)?" Selected ":"";
										?>
										<option value="<?php echo $hour; ?>" <?=$selected_hr?>><?php echo $hour; ?></option>
										<?php } ?>
									</select>
									<code >:</code>
									<select name="contest-beast[end-time-minute]" id="end_minute" class="form-control" style="width:auto;display:inline-block;">
										<?php for($minute = 0; $minute <= 59; $minute++) { $minute = sprintf('%02d', $minute); 
										$selected_minute = ($_contest_beast_post_meta['end-time-minute']==$minute)?" Selected ":"";
										?>
										<option value="<?php echo $minute; ?>" <?=$selected_minute?>><?php echo $minute; ?></option>
										<?php } ?>
									</select>
									<select name="contest-beast[end-time-meridian]" id="end_meridian" class="form-control" style="width:auto;display:inline-block;">
											<?php 
											$meridian = array('AM','PM');
											
											for ($i=0; $i<count($meridian);$i++){
												$selected_meridian = ($_contest_beast_post_meta['end-time-meridian']==$meridian[$i])?" Selected ":"";
												echo "<option value=\"$meridian[$i]\" $selected_meridian>$meridian[$i]</option>";
											}
											?>
									</select>
										
									
								</div>
								</div>
							</div>
							<!-- Text input-->
							<div class="form-group">
							  <label class="control-label" for="countdown">Countdown template</label>
							  <div class="">
								<select name="contest-beast[timer-template]" id="countdown" class="form-control" value="<?=$contest_data->timer_template;?>">
									<?php
									$mcControler = MotionCountdownController::getInstance();
									$mcLoader = MotionCountdownLoader::getInstance();
									$mcLoader->load($mcControler->pluginFilePath . 'model/generator_templates/');
									$mctURL = $mcControler->templatesDirectoryURLPath;
									$templatesInformation = array();
									foreach (get_declared_classes() as $className) {
									  if (is_subclass_of($className, MotionCountdownController::getInstance()->modelGenerator->templateAbstractClass)) {
										$classInstance = new $className();
										$selected_coundown_theme = ($_contest_beast_post_meta['timer-template']==$classInstance->getTemplateAlias())?" Selected ":"";
										echo '<option '.$selected_coundown_theme.' value="'.$classInstance->getTemplateAlias().'" >'.$classInstance->getTemplateName().'</option>';
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
								<label class="control-label" for="referral">Entries per Referral</label>
								<div class="">
								<input id="referral" value="<?=$_contest_beast_post_meta['number-entries']?>" name="contest-beast[number-entries]" type="text" style="" class="form-control input-md">
								<small style="">Enter the number of entries you want a participant to receive when referring another user to this contest.</small>
								</div>
							</div>
							<!-- Text input-->
							<div class="form-group">
								<label class="control-label" for="winners">Number of Winners</label>
								<div class="">
									<input id="winners" value="<?=$_contest_beast_post_meta['number-winners']?>" name="contest-beast[number-winners]" type="text" style="" class="form-control input-md">
									<small style="">Enter the number of winners you want this contest to have.</small>
								</div>
							</div>
							<!-- Text input-->
							<div class="form-group">
								<label class="control-label" for="disclaimer">Disclaimer</label>
								<div class="">
								<textarea id="disclaimer" name="contest-beast[contest-disclaimer]" rows="7" placeholder="Description"  class="form-control"><?=$_contest_beast_post_meta['contest-disclaimer']?></textarea>
								<small style="">Enter a short contest disclaimer. It will be displayed in a semi-prominent position on the contest page.</small>
								</div>
							</div>
											<!-- Text input-->
							<div class="form-group">
								<label class="control-label" for="termsofservice">Terms Of Service</label>
								<div class="">
								<textarea id="termsofservice" name="contest-beast[contest-termsofservice]" rows="7" placeholder="Terms of Service"  class="form-control"><?=$_contest_beast_post_meta['contest-termsofservice']?></textarea>
								<small style="">Enter a Terms of Service Details for contest. It will be displayed in a semi-prominent position on the contest page.</small>
								</div>
							</div>
											<!-- Text input-->
							<div class="form-group">
								<label class="control-label" for="privacypolicybox">Privacy Policy Box</label>
								<div class="">
								<textarea id="privacypolicybox" name="contest-beast[contest-privacypolicybox]" rows="7" placeholder="Privacy Policy"  class="form-control"><?=$_contest_beast_post_meta['contest-privacypolicybox']?></textarea>
								<small style="">Enter a Privacy Policy Box Details for contest. It will be displayed in a semi-prominent position on the contest page.</small>
								</div>
							</div>
							<!-- Text input-->
							<div class="form-group">
								<label class="control-label" for="textinput">Custom Rules?</label>
								<div class="">
								<label>
								<input type="checkbox" value="on" name="contest-beast[contest-rules-custom]" <?php echo ($_contest_beast_post_meta['contest-rules-custom']=="on")?" checked=\"checked\"  ":""; ?> id="contest_rules" />
							
								<small style="">This contest has custom rules (you will enter them below)</small>
								</label>
								</div>
							</div>
							
							<!-- Text input-->
							<div class="form-group open_rules_content" style="<?php echo ($_contest_beast_post_meta['contest-rules-custom']=="on")?" display:block; ":"display:none;"; ?> ">
								<div class="">
								 <textarea id="rules" name="contest-beast[contest-rules]" rows="7" placeholder="Enter your rules"  class="form-control"><?=$_contest_beast_post_meta['contest-rules']?></textarea>
								</div>
							</div>
							
							
							<!-- Text input-->
							<div class="form-group">
								<label class="control-label" for="textinput">Custom Tracking Scripts?</label>
								<div class="">
								<label>
								<input type="checkbox" value="yes" name="contest-beast[contest-custom-tracking-script-option]" <?php echo ($_contest_beast_post_meta['contest-custom-tracking-script-option']=="yes")?" checked=\"checked\"  ":""; ?> id="contest_custom_tracking" />
							
								<small style="">This contest has custom tracking scripts (you will enter them below)</small>
								</label>
								</div>
							</div>
							<div class="form-group open_custom_tracking" style="<?php echo ($_contest_beast_post_meta['contest-rules-custom']=="on")?" display:block; ":"display:none;"; ?> ">
								<div class="">
								 <textarea id="rules" name="contest-beast[contest-custom-tracking-script]" rows="7" placeholder="Enter your tracking script"  class="form-control"><?=$_contest_beast_post_meta['contest-custom-tracking-script']?></textarea>
								</div>
							</div>
							
							<!-- Text input
							[add-to-mailing-list] => yes
							[mailing-list-provider] => other
							[mailing-list-list] => 
							[mailing-list-name] => 
							-->
							
							<div class="form-group">
							  <label class="control-label" for="custom_mailing">Add Participants to Mailing List?</label>
								<div class="">
									<label>
									<input type="checkbox" name="contest-beast[add-to-mailing-list]" id="custom_mailing" <?php echo ($_contest_beast_post_meta['add-to-mailing-list']=="on")?" checked=\"checked\"  ":""; ?> />
									<small style="">You can add participants to your mailing list</small>
									</label>
									
								</div>
							</div>
							
							<div class="form-group open_mailing_content" style="<?php echo ($_contest_beast_post_meta['add-to-mailing-list']=="on")?" display:block; ":"display:none;"; ?> ">
								
								<div id="mailing-list-error"></div>
								
								
								<?php
								
								global $current_user;
								get_currentuserinfo();  
								//print_r($current_user);
								if (get_user_meta($current_user->ID,'_mailchimApi_api',true)!=""){
								
								$selected = ($_contest_beast_post_meta['anable-mailing-list-mailchimp']=="yes")?" Checked ":"";
								
								$lists = get_user_meta($current_user->ID,'_mailchimApi_list',true);
								echo "<div class=\"col-md-3\"><div class=\"panel panel-default\">
								<div class=\"panel-heading\">MailChimp Connection</div><div class=\"panel-body\">
								<div style=\"padding:0px 0px 9px\"><input type=\"checkbox\" name=\"contest-beast[anable-mailing-list-mailchimp]\" value=\"yes\"  $selected /> Anable</div>
								";
								echo "<select name=\"contest-beast[mail_mailchimp_list]\" class=\"form-control\">";
									
									$choose_select = ($_contest_beast_post_meta['mail_mailchimp_list']=="")? " Selected ":"";
									echo "<option value=\"\">Select list name</option>";
									if ( count($lists)>1){
										$h=0;
										foreach ($lists as $lst_key=>$lst_val){
											$list_selected = ($_contest_beast_post_meta['mail_mailchimp_list']==$lst_val['list_id'])? " Selected ":"";
											
											echo '<option value="'.$lst_val['list_id'].'" '.$list_selected.' >'.$lst_val['list_name'].'</option>';
											
										}
										
									}else{
										$list_selected = ($_contest_beast_post_meta['mail_mailchimp_list']==$lists[0]['list_id'])? " Selected ":"";
										echo '<option value="'.$lists[0]['list_id'].'" '.$list_selected.'>'.$lists[0]['list_name'].'</option>';
										
									}
								echo "</select></div></div></div>";
								}
								
								
								if (get_user_meta($current_user->ID,'_getreponseApi_api',true)!=""){
								
								$selected = ($_contest_beast_post_meta['anable-mailing-list-getresponse']=="yes")?" Checked ":"";
								
								$lists = get_user_meta($current_user->ID,'_getreponseApi_list',true);
								echo "<div class=\"col-md-3\"><div class=\"panel panel-default\">
								<div class=\"panel-heading\">getResponse Connection</div><div class=\"panel-body\">
								<div style=\"padding:0px 0px 9px\"><input type=\"checkbox\" name=\"contest-beast[anable-mailing-list-getresponse]\" $selected value=\"yes\" /> Anable</div>
								";
								echo "<select name=\"contest-beast[mail_getresponse_list]\" class=\"form-control\">";
									
									$choose_select = ($_contest_beast_post_meta['mail_getresponse_list']=="")? " Selected ":"";
									echo "<option value=\"\">Select list name</option>";
									if ( count($lists)>1){
										$h=0;
										foreach ($lists as $lst_key=>$lst_val){
											$list_selected = ($_contest_beast_post_meta['mail_getresponse_list']==$lst_val['list_id'])? " Selected ":"";
											
											echo '<option value="'.$lst_val['list_id'].'" '.$list_selected.' >'.$lst_val['list_name'].'</option>';
											
										}
										
									}else{
										$list_selected = ($_contest_beast_post_meta['mail_getresponse_list']==$lists[0]['list_id'])? " Selected ":"";
										echo '<option value="'.$lists[0]['list_id'].'" '.$list_selected.'>'.$lists[0]['list_name'].'</option>';
										
									}
								echo "</select></div></div></div>";
								}								
								
								if (get_user_meta($current_user->ID,'contest_aweber_access_token',true)!=""){
								
								$selected = ($_contest_beast_post_meta['enable-mailing-list-aweber']=="1")?" Checked ":"";
								
								echo 	"<div class='col-md-3'>".
											"<div class='panel panel-default'>".
												"<div class='panel-heading'>Aweber Connection</div>".
												"<div class='panel-body'>".
												"<input type='checkbox' name='contest-beast[enable-mailing-list-aweber]' $selected value='1' />Enable".
												"</div>".
											"</div>".
										"</div>";
								}
								
								?>
								
								
							<div style="clear:both"></div>	
							</div>
							
							
							<!-- Text input-->
							<div class="form-group">
								<div style="padding-bottom:25px;"><strong>Your thank you page url: </strong><br/><code id="thankyou-page"><?=get_permalink( $contest_data->ID )?></code></div>
							
							
								<label class="control-label" for="textarea">Your Thank you page content.</label>
								<div class="">
									<!-- <textarea id="post_content" name="textarea" rows="7" placeholder="Description"  class="form-control"></textarea> -->
									
									
									<textarea id="contest_thank_you_content" class="contest_thank_you_content" name="contest_thank_you_content" rows="7" placeholder="Description" ><?=$_contest_beast_post_meta['contest_thank_you'];?></textarea>
								<script>
								tinymce.init({
  mode : "specific_textareas",
  editor_selector: 'contest_thank_you_content',
  height: 352,
  menubar: false,
  extended_valid_elements : "a[class|name|href|target|title|onclick|rel],script[type|src],iframe[src|style|width|height|scrolling|marginwidth|marginheight|frameborder],img[class|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name]",
  plugins: [
    'advlist autolink lists link image charmap print preview anchor',
    'searchreplace visualblocks code fullscreen',
    'insertdatetime media table contextmenu paste code textcolor'
  ],
  toolbar: 'undo redo | insert | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | code | forecolor backcolor',
  content_css: [
    '//fonts.googleapis.com/css?family=Lato:300,300i,400,400i',
    '//www.tinymce.com/css/codepen.min.css']
});
								</script>
								<?php  
								 
								 //wp_editor( $_contest_beast_post_meta['contest_thank_you'], 'contest_thank_you_content', array('media_buttons' => false) );?>
								 
								</div>
							
							</div>

							
							
							<div class="form-group">
								<div class="row">
									<div class="col-md-6">
										<label class="control-label">Body background color</label>
										<div class="input-group">
											<span class="input-group-addon">#</span>
											<input type="text" name="contest-beast[body_background_color]" value="<?=$_contest_beast_post_meta['body_background_color'];?>" id="body_background_color" class="form-control" />
											
										</div>
										<div id="body_background_color_preview" class="rounded " <?=($_contest_beast_post_meta['body_background_color'] != "")?"style=\"background-color:#".$_contest_beast_post_meta['body_background_color']."; height:15px; margin-top:3px\"" :""; ?>></div>
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
														
														$('#body_background_color_preview').css('background-color',$bgc.val());
														$('#body_background_color_preview').height('height','20px;');
													});
												});
											})(jQuery);
										</script>
									</div>
									<div class="col-md-6">
										<label class="control-label">Contest background color</label>
										<div class="input-group">
											<span class="input-group-addon">#</span>
											<input type="text" name="contest-beast[contest_background_color]" value="<?=$_contest_beast_post_meta['contest_background_color'];?>" id="contest_background_color" class="form-control" />
										</div>
										<div id="contest_background_color_preview" class="rounded " <?=($_contest_beast_post_meta['contest_background_color'] != "")?"style=\"background-color:#".$_contest_beast_post_meta['contest_background_color']."; height:15px; margin-top:3px\"" :""; ?>></div>
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
															
															$('#contest_background_color_preview').css('background-color',$bgc.val());
														$('#contest_background_color_preview').height('height','20px;');
														},
														onChange: function (hsb, hex, rgb) {
															
															$bgc.val(hex);
															$('#contest_background_color_preview').css('background-color',hex);
															$('#contest_background_color_preview').css('height','20px;');
														}
													})
													.bind('keyup', function(){
														$(this).ColorPickerSetColor($bgc.val());
														$('#contest_background_color_preview').css('background-color',$bgc.val());
														$('#contest_background_color_preview').height('height','20px;');
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
										<label class="control-label">Giveaway Item</label>
										<form enctype="multipart/form-data">
											<input type="file" name="contest_giveaway_item" id="contest_giveaway_item" />
											<input type="hidden" name="contest-beast[contest_giveaway_item]" value="<?=$_contest_beast_post_meta['contest_giveaway_item'];?>" id="contest_giveaway_item_image_url" />
											<input type="hidden" name="action" value="upload_image" />
											<br />
											<div class="progress" id="contest_giveaway_item_upload_progress">
												<div class="progress-bar"  role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style=""></div>
											</div>
											<?php
											if ($_contest_beast_post_meta['contest_giveaway_item'] !=""){
												$img_preview_body_bg = "<img src=\"".$_contest_beast_post_meta['contest_giveaway_item']."\" style=\"width:30%;\">";
											}
											?>
											<div id="contest_giveaway_item-preview" class="ptr-preview"><?=$img_preview_body_bg?></div>
											<script type="text/javascript">
											(function ($) {
												$(document).ready(function () {
													$('#contest_giveaway_item_upload_progress').hide();
													// Change this to the location of your server-side upload handler:
													$('#contest_giveaway_item').fileupload({
														url : "<?php echo admin_url('admin-ajax.php'); ?>",
														dataType : 'json',
														done : function (e, data) {
															$('#contest_giveaway_item_upload_progress').hide();
															$('#contest_giveaway_item-preview')
																.html('<img src="'+data.result.url+'" alt="" width="30%" />')
																.append('<a href="#" class="remove"><i class="fa fa-times"></i></a>');;
															$('#contest_giveaway_item_image_url').val(data.result.url);
														},
														progressall : function (e, data) {
															$('#contest_giveaway_item_upload_progress').show();
															var progress = parseInt(data.loaded / data.total * 100, 10);
															$('#contest_giveaway_item_upload_progress .progress-bar').css(
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
									
							<div class="form-group">
								<div class="row">
									<div class="col-md-6">
										<label class="control-label">Body background image</label>
										<form enctype="multipart/form-data">
											<input type="file" name="body_background_image" id="body_background_image" />
											<input type="hidden" name="contest-beast[body_background_image]" value="<?=$_contest_beast_post_meta['body_background_image'];?>" id="body_background_image_url" />
											<input type="hidden" name="action" value="upload_image" />
											<br />
											<div class="progress" id="body_background_image_upload_progress">
												<div class="progress-bar"  role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style=""></div>
											</div>
											<?php
											if ($_contest_beast_post_meta['body_background_image'] !=""){
												$img_preview_body_bg = "<img src=\"".$_contest_beast_post_meta['body_background_image']."\" style=\"width:30%;\">";
											}
											?>
											<div id="body_background_image-preview" class="ptr-preview"><?=$img_preview_body_bg?></div>
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
																.html('<img src="'+data.result.url+'" alt="" width="30%" />')
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
											<input type="hidden" name="contest-beast[contest_background_image]" id="contest_background_image_url" />
											<input type="hidden" name="action" value="upload_image" />
											<br />
											<div class="progress" id="contest_background_image_upload_progress">
												<div class="progress-bar"  role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style=""></div>
											</div>
											<?php
											if ($_contest_beast_post_meta['contest_background_image'] !=""){
												$img_preview_contest_bg = "<img src=\"".$_contest_beast_post_meta['contest_background_image']."\" style=\"width:30%;\">";
											}
											?>
											<div id="contest_background_image-preview" class="ptr-preview"><?=$img_preview_contest_bg?></div>
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
										<?php 
										$btnDir = ABSPATH.'wp-content/plugins/contest-beast/templates/assets/img/buttons'; 
							
										$rt =  str_replace($btnUrl,"",$_contest_beast_post_meta['button_tpl']);
										
										?>
										<select name="contest-beast[button_tpl]" id="button_tpl" class="form-control">
											
											<?php
												$imgExt = array('gif','jpg','jpeg','png');
												$btnDir = $btnDir;
												$btnUrl = plugins_url('/contest-beast/templates/assets/img/buttons');
												$btnImg = scandir($btnDir, 1);
												foreach($btnImg as $img) {
													$info = pathinfo($img);
													$name = ucwords(preg_replace('/[_-]/', ' ', $info['filename']));
													
													
													$selected_image_btn = (str_repeat($btnUrl,"",$_contest_beast_post_meta['button_tpl'])==$name)?" Selected":"";
													
													$exts = strtolower($info['extension']);
													if (in_array($exts, $imgExt)) {
														if ($_contest_beast_post_meta['button_tpl']=="$btnUrl/$img"){
															echo "<option value='$btnUrl/$img' selected >$name</option>";
														}else{
															echo "<option value='$btnUrl/$img' $selected_image_btn>$name</option>";
														}
														
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
											<input type="hidden" name="contest-beast[button_img]" id="button_img_url" />
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
			</div>
	</div>
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
		<a href="/my-account/new-contest/" class="btn btn-primary btnfrt">Add More Contest</a>
		<a href="/my-account/dashboard/" class="btn btn-primary btnrgy">Return Home</a>
		</div>
	</div>
  </div>
</div>
