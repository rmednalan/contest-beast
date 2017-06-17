<div class="wrap">
	<?php screen_icon(); ?>
	<h2><?php _e('Contest Beast - Settings'); ?></h2>
	<?php settings_errors(); ?>

	<form enctype="multipart/form-data" id="contest-beast-settings-form" method="post" action="<?php esc_url(add_query_arg(array())); ?>">
		<input type="hidden" id="contest-beast-has-active-contest" value="<?php echo intval($active_contest); ?>" />

		<?php if(true) { ?>
		<p>
			<?php
			_e('Would you like to resell this software and keep all the profits? Get complete PLR access <a href="http://jvz3.com/c/26956/55720" target="_blank">here</a>.');
			?>
		</p>
		<?php } ?>

		<p>
			<?php printf(__('Please watch <a href="%s" target="_blank">how to</a> videos if you\'re not sure how to set up and configure the plugin. <a href="%s" target="_blank">Watch videos here.</a>'), 'http://contestbeast.com/how-to/', 'http://contestbeast.com/how-to/'); ?>
		</p>

		<h3><?php _e('General Settings'); ?></h3>
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row"><label <?php pd_error_output_simple($errors, 'default-number-entries'); ?> for="contest-beast-default-number-entries"><?php _e('Default Entries per Referral'); ?></label></th>
					<td>
						<input type="text" class="code small-text" name="contest-beast[default-number-entries]" id="contest-beast-default-number-entries" value="<?php esc_attr_e($settings['default-number-entries']); ?>" /><br />
						<small><?php _e('Enter the default number of entries you want a participant to receive when referring another user. This can be changed per contest.'); ?></small>
						<?php pd_error_output($errors, 'default-number-entries'); ?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label <?php pd_error_output_simple($errors, 'default-number-winners'); ?> for="contest-beast-default-number-winners"><?php _e('Default Number of Winners'); ?></label></th>
					<td>
						<input type="text" class="code small-text" name="contest-beast[default-number-winners]" id="contest-beast-default-number-winners" value="<?php esc_attr_e($settings['default-number-winners']); ?>" /><br />
						<small><?php _e('Enter the default number of winners you want a contest to have. This can be changed per contest.'); ?></small>
						<?php pd_error_output($errors, 'default-number-winners'); ?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label <?php pd_error_output_simple($errors, 'default-contest-rules'); ?> for="contest-beast-default-contest-rules"><?php _e('Default Contest Rules'); ?></label></th>
					<td>
						<?php wp_editor($settings['default-contest-rules'], 'contest-beast-default-contest-rules', array('textarea_name' => 'contest-beast[default-contest-rules]', 'textarea_rows' => 10)); ?>
						<small><?php _e('Your contest will use the rules you enter above by default. You can modify the rules individually for each contest if you need to add special clauses or text.'); ?></small>
						<?php pd_error_output($errors, 'default-contest-rules'); ?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label <?php pd_error_output_simple($errors, 'default-tracking-scripts'); ?> for="contest-beast-default-tracking-scripts"><?php _e('Default Tracking Scripts'); ?></label></th>
					<td>
						<textarea class="large-text code" rows="6" name="contest-beast[default-tracking-scripts]" id="contest-beast-default-tracking-scripts"><?php esc_html_e($settings['default-tracking-scripts']); ?></textarea><br />
						<small><?php _e('Your contest pages will contain the markup you enter above in the footer by default. You can modify the tracking scripts individually for each contest if you need to use different parameters.'); ?></small>
						<?php pd_error_output($errors, 'default-tracking-scripts'); ?>
					</td>
				</tr>
				<?php if(isset($skins) && is_array($skins)) { ?>
				<tr valign="top">
					<th scope="row"><label for="contest-beast-skin"><?php _e('Skin'); ?></label></th>
					<td>
						<select class="code" name="contest-beast[skin]" id="contest-beast-skin">
							<option <?php selected('default', $settings['skin']); ?> data-url="<?php echo $default_skin_url; ?>" value="default"><?php _e('Default'); ?></option>

							<?php if(isset($skins['dark']) && is_array($skins['dark']) && !empty($skins['dark'])) { ?>
							<optgroup label="<?php _e('Dark'); ?>">
								<?php foreach($skins['dark'] as $skin_data) { ?>
								<option <?php selected($skin_data['path'], $settings['skin']); ?> data-url="<?php esc_attr_e($skin_data['preview_url']); ?>" value="<?php esc_attr_e($skin_data['path']); ?>"><?php esc_html_e($skin_data['name']); ?></option>
								<?php } ?>
							</optgroup>
							<?php } ?>

							<?php if(isset($skins['light']) && is_array($skins['light']) && !empty($skins['light'])) { ?>
							<optgroup label="<?php _e('Light'); ?>">
								<?php foreach($skins['light'] as $skin_data) { ?>
								<option <?php selected($skin_data['path'], $settings['skin']); ?> data-url="<?php esc_attr_e($skin_data['preview_url']); ?>" value="<?php esc_attr_e($skin_data['path']); ?>"><?php esc_html_e($skin_data['name']); ?></option>
								<?php } ?>
							</optgroup>
							<?php } ?>
						</select><br />
						<small><?php _e('Select your desired skin'); ?></small>
						<img src="#" id="contest-beast-skin-preview" alt="<?php _e('Skin Preview'); ?>" />
                    </td>
				</tr>
				<?php } else { ?>
				<tr valign="top">
					<th scope="row"></th>
					<td>
						<input type="hidden" name="contest-beast[skin]" id="contest-beast-skin" value="default" />
					</td>
				</tr>
				<?php } ?>
			</tbody>
		</table>

		<input type="hidden" name="contest-beast[branding-logo-action]" id="contest-beast-branding-logo-action" value="<?php echo empty($settings['branding-logo-url']) ? 'upload' : 'url'; ?>" />
		<h3><?php _e('Branding Settings'); ?></h3>
		<table class="form-table">
			<tbody>
				<tr valign="top" data-dependency="contest-beast[branding-logo-action]" data-dependency-value="upload">
					<th scope="row">
						<label <?php pd_error_output_simple($errors, 'branding-logo-file'); ?> for="contest-beast-branding-logo-file"><?php _e('Branding Logo'); ?></label><br />
						<small><a data-branding-logo-action="url" class="contest-branding-logo-toggle" href="#"><?php _e('Specify a url instead'); ?></a></small>
					</th>
					<td>
						<input type="file" name="contest-beast-branding-logo-file" id="contest-beast-branding-logo-file" /><br />
						<small><?php _e('A width less than 425px is recommended. Leave this field blank if you do not wish to have a branding logo at the top of your contest pages.'); ?></small>
						<?php pd_error_output($errors, 'branding-logo-file'); ?>
					</td>
				</tr>
				<tr valign="top" data-dependency="contest-beast[branding-logo-action]" data-dependency-value="url">
					<th scope="row">
						<label <?php pd_error_output_simple($errors, 'branding-logo-url'); ?> for="contest-beast-branding-logo-url"><?php _e('Branding Logo URL'); ?></label><br />
						<small><a data-branding-logo-action="upload" class="contest-branding-logo-toggle" href="#"><?php _e('Upload an image instead'); ?></a></small>
					</th>
					<td>
						<input type="text" class="code large-text" name="contest-beast[branding-logo-url]" id="contest-beast-branding-logo-url" value="<?php esc_attr_e($settings['branding-logo-url']); ?>" /><br />
						<small><?php _e('A width less than 425px is recommended. Leave this field blank if you do not wish to have a branding logo at the top of your contest pages.'); ?></small>
						<?php pd_error_output($errors, 'branding-logo-url'); ?>
					</td>
				</tr>
			</tbody>
		</table>

		<h3><?php _e('Social Settings'); ?></h3>
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row"><label for="contest-beast-twitter-handle"><?php _e('Twitter Handle'); ?></label></th>
					<td>
						<input type="text" class="code regular-text" name="contest-beast[twitter-handle]" id="contest-beast-twitter-handle" value="<?php esc_attr_e($settings['twitter-handle']); ?>" /><br />
						<small><?php _e('Enter the Twitter handle you would like entrants to mention and follow after entering a contest.'); ?></small>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="contest-beast-facebook-method"><?php _e('Preferred Facebook Social Method'); ?></label></th>
					<td>
						<ul style="margin: 0">
							<li>
								<label>
									<input <?php checked($settings['facebook-method'], 'like'); ?> type="radio" name="contest-beast[facebook-method]" id="contest-beast-facebook-method" value="like" />
									<?php _e('Like'); ?>
								</label>
							</li>
							<li>
								<label>
									<input <?php checked($settings['facebook-method'], 'subscribe'); ?> type="radio" name="contest-beast[facebook-method]" id="contest-beast-facebook-method" value="subscribe" />
									<?php _e('Subscribe'); ?> - <?php _e('You <strong>must</strong> have enabled subscriptions to your profile if you choose this option'); ?>
								</label>
							</li>
						</ul>
					</td>
				</tr>
				<tr data-dependency="contest-beast[facebook-method]" data-dependency-value="like" valign="top">
					<th scope="row"><label for="contest-beast-facebook-like-url"><?php _e('Facebook Like URL'); ?></label></th>
					<td>
						<input type="text" class="code regular-text" name="contest-beast[facebook-like-url]" id="contest-beast-facebook-like-url" value="<?php esc_attr_e($settings['facebook-like-url']); ?>" /><br />
						<small><?php _e('Enter the URL you would like users to "Like" after they have entered one of your contests.'); ?></small>
					</td>
				</tr>
				<tr data-dependency="contest-beast[facebook-method]" data-dependency-value="subscribe" valign="top">
					<th scope="row"><label for="contest-beast-facebook-profile-url"><?php _e('Facebook Profile URL'); ?></label></th>
					<td>
						<input type="text" class="code regular-text" name="contest-beast[facebook-profile-url]" id="contest-beast-facebook-profile-url" value="<?php esc_attr_e($settings['facebook-profile-url']); ?>" /><br />
						<small><?php _e('Enter the URL for your profile to have users subscribe to your updates after they have entered one of your contests.'); ?></small>
					</td>
				</tr>
			</tbody>
		</table>

		<h3><?php _e('Mailing List Settings'); ?></h3>
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row"><label <?php pd_error_output_simple($errors, 'mailing-list-provider'); ?> for="contest-beast-mailing-list-provider"><?php _e('Mailing List Provider'); ?></label></th>
					<td>
						<select name="contest-beast[mailing-list-provider]" id="contest-beast-mailing-list-provider">
							<option <?php selected($settings['mailing-list-provider'], 'none'); ?> value="none"><?php _e('None'); ?></option>
							<option <?php selected($settings['mailing-list-provider'], 'aweber'); ?> value="aweber"><?php _e('AWeber'); ?></option>
							<option <?php selected($settings['mailing-list-provider'], 'mailchimp'); ?> value="mailchimp"><?php _e('MailChimp'); ?></option>
							<option <?php selected($settings['mailing-list-provider'], 'other'); ?> value="other"><?php _e('Other'); ?></option>
						</select>
						<small><?php printf(__('<a href="%s" target="_blank">Try AWeber</a>, the best email list management service for $1'), 'http://pleasetry.aweber.com/'); ?></small><br />
						<small><?php _e('Choose the mailing list provider you wish to use for when visitors sign up for your contest. If you don\'t want users added to a mailing list, select none.'); ?></small>
						<?php pd_error_output($errors, 'mailing-list-provider'); ?>
					</td>
				</tr>
				<tr data-dependency="contest-beast[mailing-list-provider]" data-dependency-value="aweber" valign="top">
					<th scope="row"><label <?php pd_error_output_simple($errors, 'aweber-authorization'); ?> for="contest-beast-aweber-authorization"><?php _e('AWeber Authorization Code'); ?></label></th>
					<td>
						<input type="text" class="code large-text" name="contest-beast[aweber-authorization]" id="contest-beast-aweber-authorization" value="<?php esc_attr_e($settings['aweber-authorization']); ?>" /><br />
						<small><a target="_blank" href="<?php esc_attr_e(self::URLS__AWEBER_AUTH); ?>"><?php _e('Click here'); ?></a> <?php _e('to authorize and then copy the value provided into this field. If you change the value of this field, please save the settings to choose your default mailing list.'); ?></small>
						<?php pd_error_output($errors, 'aweber-authorization'); ?>
					</td>
				</tr>
				<tr data-dependency="contest-beast[mailing-list-provider]" data-dependency-value="mailchimp" valign="top">
					<th scope="row"><label <?php pd_error_output_simple($errors, 'mailchimp-api-key'); ?> for="contest-beast-mailchimp-api-key"><?php _e('MailChimp API Key'); ?></label></th>
					<td>
						<input type="text" class="code large-text" name="contest-beast[mailchimp-api-key]" id="contest-beast-mailchimp-api-key" value="<?php esc_attr_e($settings['mailchimp-api-key']); ?>" /><br />
						<small><a target="_blank" href="<?php esc_attr_e(self::URLS__MAILCHIMP_API_KEY); ?>"><?php _e('Click here'); ?></a> <?php _e('to create an API key and then copy the value provided into this field. If you change the value of this field, please save the settings to choose your default mailing list.'); ?></small>
						<?php pd_error_output($errors, 'mailchimp-api-key'); ?>
					</td>
				</tr>
				<?php self::display_mailing_list_markup($settings['mailing-list-provider'], $settings['mailing-list-list'], $settings['mailing-list-name']); ?>
				<tr data-dependency="contest-beast[mailing-list-provider]" data-dependency-value="other" valign="top">
					<th scope="row"><label for="contest-beast-opt-in-code"><?php _e('Opt In Form Code'); ?></label></th>
					<td>
						<textarea class="code large-text" rows="10" name="contest-beast[opt-in-code]" id="contest-beast-opt-in-code"><?php esc_html_e($settings['opt-in-code']); ?></textarea><br />
						<small><?php _e('Copy and paste your HTML form code from any mailing list provider. We will parse your input and prompt you for more details if appropriate.'); ?></small>

						<?php foreach((array)$settings['opt-in-form-fields'] as $field_name) { ?>
						<input class="contest-beast-opt-in-form-field" type="hidden" name="contest-beast[opt-in-form-fields][]" value="<?php esc_attr_e($field_name); ?>" />
						<?php } ?>

						<?php foreach((array)$settings['opt-in-form-fields-hidden'] as $field_name => $field_value) { ?>
						<input class="contest-beast-opt-in-form-field" type="hidden" name="contest-beast[opt-in-form-fields-hidden][<?php esc_attr_e($field_name); ?>]" value="<?php esc_attr_e($field_value); ?>" />
						<?php } ?>
					</td>
				</tr>
				<tr class="contest-beast-opt-in-code-dependent" valign="top">
					<th scope="row"><label for="contest-beast-opt-in-form-url"><?php _e('Form Action'); ?></label></th>
					<td>
						<input type="text" class="code large-text" name="contest-beast[opt-in-form-url]" id="contest-beast-opt-in-form-url" value="<?php esc_attr_e($settings['opt-in-form-url']); ?>" /><br />
						<small><?php _e('This field is parsed from the form code you entered above, but you can change it if you need to.'); ?></small>
					</td>
				</tr>
				<tr class="contest-beast-opt-in-code-dependent" valign="top">
					<th scope="row"><label for="contest-beast-opt-in-form-email-field"><?php _e('Email Field'); ?></label></th>
					<td>
						<select data-contest-beast-field="email" class="contest-beast-opt-in-code-fields code" name="contest-beast[opt-in-form-email-field]" id="contest-beast-opt-in-form-email-field">
							<?php foreach((array)$settings['opt-in-form-fields'] as $field_name) { ?>
							<option <?php selected($settings['opt-in-form-email-field'], $field_name); ?> value="<?php esc_attr_e($field_name); ?>"><?php esc_html_e($field_name); ?></option>
							<?php } ?>
						</select><br />
						<small><?php _e('This field is parsed from the form code you entered above, but you can change it if you need to.'); ?></small>
					</td>
				</tr>
				<tr class="contest-beast-opt-in-code-dependent" valign="top">
					<th scope="row"><label for="contest-beast-opt-in-name-field"><?php _e('Name Field'); ?></label></th>
					<td>
						<select data-contest-beast-field="name" class="contest-beast-opt-in-code-fields code" name="contest-beast[opt-in-form-name-field]" id="contest-beast-opt-in-form-name-field">
							<?php foreach((array)$settings['opt-in-form-fields'] as $field_name) { ?>
							<option <?php selected($settings['opt-in-form-name-field'], $field_name); ?> value="<?php esc_attr_e($field_name); ?>"><?php esc_html_e($field_name); ?></option>
							<?php } ?>
						</select>
						<label>
							<input <?php checked($settings['opt-in-form-disable-name'], 'yes'); ?> type="checkbox" name="contest-beast[opt-in-form-disable-name]" id="contest-beast-opt-in-form-disable-name" value="yes" />
							<?php _e('This mailing list doesn\'t have a name input'); ?>
						</label><br />
						<small><?php _e('This field is parsed from the form code you entered above, but you can change it if you need to.'); ?></small>
					</td>
				</tr>
			</tbody>
		</table>

		<p class="submit">
			<?php wp_nonce_field('save-contest-beast-settings', 'save-contest-beast-settings-nonce'); ?>
			<input type="submit" class="button button-primary" name="save-contest-beast-settings" value="<?php _e('Save Changes'); ?>" />
		</p>
	</form>
</div>