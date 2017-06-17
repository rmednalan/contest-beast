<?php

//print_r ($meta);
?>
		<input type="hidden" name="contest-beast[branding-logo-action]" id="contest-beast-branding-logo-action" value="<?php echo empty($settings['branding-logo-url']) ? 'upload' : 'url'; ?>" />

		<h4><?php //_e('Custom CSS'); ?></h4>
		<!-- for custom css added 4/7/17 -->
		<!-- <table class="form-table">
		
			<tbody>
				
				<tr valign="top"> 

					<th scope="row">
						<label for="contest-beast-contest-cuscssclass"><?php //_e('Custom CSS Class'); ?></label>
					</th>

			<td>

                <textarea class="large-text" name="contest-beast[picture_description]" id="contest-beast-contest-pd"><?php //esc_attr_e($meta['custom_css_class']); ?></textarea>

			</td>

				</tr>
				<tr valign="top"> 

					<th scope="row">
						<label for="contest-beast-contest-cuscssclatt"><?php //_e('Custom CSS Attributes'); ?></label>
					</th>

			<td>

                <textarea class="large-text" name="contest-beast[picture_description]" id="contest-beast-contest-pd"><?php //esc_attr_e($meta['custom_css_desc']); ?></textarea>

			</td>

				</tr>
				
			</tbody>
			
		
		</table> -->
		<!-- end for custom css -->
		
		
		<!-- new added 4/17/2017 -->
		<h4><?php _e('Video Advertisement'); ?></h4>

		<table class="form-table">
		
			<tbody>
				
				<tr valign="top"> 

					<th scope="row">
						<label for="contest-beast-contest-video"><?php _e('Contest Video ID'); ?></label>
					</th>

			<td>
                <textarea class="large-text" name="contest-beast[contest_video]" id="contest-beast-contest-video"><?php esc_attr_e($meta['contest_video']); ?></textarea>

			</td>

				</tr>
				
				<tr valign="top"> 

					<th scope="row">
						<label for="contest-beast-contest-video"><?php _e('Embedded Youtube Video'); ?></label>
					</th>

					<td>
						<textarea class="large-text" name="contest-beast[youtube_video]" id="contest-beast-youtube-video"><?php esc_attr_e($meta['youtube_video']); ?></textarea>

					</td>

				</tr>
				<tr valign="top"> 

					<th scope="row">
						<label for="contest-beast-contest-video"><?php _e('Add Custom Scripts'); ?></label>
					</th>

					<td>
						<textarea class="large-text" name="contest-beast[user_scripts]" id="contest-beast-user_scripts"><?php esc_attr_e($meta['user_scripts']); ?></textarea>

					</td>

				</tr>
				
			</tbody>
			
		
		</table>
		
		
		<h4><?php _e('Branding Settings'); ?></h4>

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

<h4><?php _e('Start &amp; End Date'); ?></h4>

<table class="form-table">

	<tbody>

		<tr valign="top">

			<th scope="row"><label for="contest-beast-start-date"><?php _e('Start Date &amp; Time'); ?></label></th>

			<td>

				<input type="text" class="code contest-beast-date" name="contest-beast[start-date]" id="contest-beast-start-date" value="<?php esc_attr_e($meta['start-date']); ?>" />

				

				<code><?php _e('at'); ?></code>

				

				<select name="contest-beast[start-time-hour]" id="contest-beast-start-time-hour">

					<?php for($hour = 1; $hour <= 12; $hour++) { $hour = sprintf('%02d', $hour); ?>

					<option <?php selected($hour, $meta['start-time-hour']); ?> value="<?php echo $hour; ?>"><?php echo $hour; ?></option>

					<?php } ?>

				</select>

				

				<code style="padding: 1px 0px;">:</code>

				

				<select name="contest-beast[start-time-minute]" id="contest-beast-start-time-minute">

					<?php for($minute = 0; $minute <= 59; $minute++) { $minute = sprintf('%02d', $minute); ?>

					<option <?php selected($minute, $meta['start-time-minute']); ?> value="<?php echo $minute ?>"><?php echo $minute; ?></option>

					<?php } ?>

				</select>

				<select name="contest-beast[start-time-meridian]" id="contest-beast-start-time-meridian">

					<option <?php selected('AM', $meta['start-time-meridian']); ?> value="AM">AM</option>

					<option <?php selected('PM', $meta['start-time-meridian']); ?> value="PM">PM</option>

				</select>

			</td>

		</tr>

		<tr valign="top">

			<th scope="row"><label <?php pd_error_output_simple($errors, 'end-timestamp'); ?> for="contest-beast-end-date"><?php _e('End Date &amp; Time'); ?></label></th>

			<td>

				<input type="text" class="code contest-beast-date" name="contest-beast[end-date]" id="contest-beast-end-date" value="<?php esc_attr_e($meta['end-date']); ?>" />

				

				<code><?php _e('at'); ?></code>

				

				<select name="contest-beast[end-time-hour]" id="contest-beast-end-time-hour">

					<?php for($hour = 1; $hour <= 12; $hour++) { $hour = sprintf('%02d', $hour); ?>

					<option <?php selected($hour, $meta['end-time-hour']); ?> value="<?php echo $hour; ?>"><?php echo $hour; ?></option>

					<?php } ?>

				</select>

				

				<code style="padding: 1px 0px;">:</code>

				

				<select name="contest-beast[end-time-minute]" id="contest-beast-end-time-minute">

					<?php for($minute = 0; $minute <= 59; $minute++) { $minute = sprintf('%02d', $minute); ?>

					<option <?php selected($minute, $meta['end-time-minute']); ?> value="<?php echo $minute ?>"><?php echo $minute; ?></option>

					<?php } ?>

				</select>

				<select name="contest-beast[end-time-meridian]" id="contest-beast-end-time-meridian">

					<option <?php selected('AM', $meta['end-time-meridian']); ?> value="AM">AM</option>

					<option <?php selected('PM', $meta['end-time-meridian']); ?> value="PM">PM</option>

				</select>

				<?php pd_error_output($errors, 'end-timestamp'); ?>

			</td>

		</tr>

	</tbody>

</table>



<h4><?php _e('Entries &amp; Winners'); ?></h4>

<table class="form-table">

	<tbody>

		<tr valign="top">

			<th scope="row"><label <?php pd_error_output_simple($errors, 'number-entries'); ?> for="contest-beast-number-entries"><?php _e('Entries per Referral'); ?></label></th>

			<td>

				<input type="text" class="code small-text" name="contest-beast[number-entries]" id="contest-beast-number-entries" value="<?php esc_attr_e($meta['number-entries']); ?>" /><br />

				<small><?php _e('Enter the number of entries you want a participant to receive when referring another user to this contest.'); ?></small>

				<?php pd_error_output($errors, 'number-entries'); ?> 

			</td>

		</tr>

		<tr valign="top">

			<th scope="row"><label <?php pd_error_output_simple($errors, 'number-winners'); ?> for="contest-beast-number-winners"><?php _e('Number of Winners'); ?></label></th>

			<td>

				<input type="text" class="code small-text" name="contest-beast[number-winners]" id="contest-beast-number-winners" value="<?php esc_attr_e($meta['number-winners']); ?>" /><br />

				<small><?php _e('Enter the number of winners you want this contest to have.'); ?></small>

				<?php pd_error_output($errors, 'number-winners'); ?>

			</td>

		</tr>

	</tbody>

</table>



<h4><?php _e('Disclaimer &amp; Rules'); ?></h4>

<table class="form-table">

	<tbody>

		<tr valign="top">

			<th scope="row"><label for="contest-beast-contest-disclaimer"><?php _e('Disclaimer'); ?></label></th>

			<td>

				<input type="text" class="large-text" name="contest-beast[contest-disclaimer]" id="contest-beast-contest-disclaimer" value="<?php esc_attr_e($meta['contest-disclaimer']); ?>" /><br />

				<small><?php _e('Enter a short contest disclaimer. It will be displayed in a semi-prominent position on the contest page.'); ?></small>

			</td>

		</tr>

		<tr valign="top">

			<th scope="row">
			<label for="contest-beast-contest-tos"><?php _e('Terms Of Service'); ?></label>
			</th>

			<td>

                            <textarea class="large-text" name="contest-beast[contest-termsofservice]" id="contest-beast-contest-tos"><?php esc_attr_e($meta['contest-termsofservice']); ?></textarea>

			</td>

		</tr>
		<tr valign="top">

			<th scope="row"><label for="contest-beast-contest-ppb"><?php _e('Privacy Policy Box'); ?></label></th>

			<td>

                            <textarea class="large-text" name="contest-beast[contest-privacypolicybox]" id="contest-beast-contest-ppb"><?php esc_attr_e($meta['contest-privacypolicybox']); ?></textarea>

			</td>

		</tr>

		<tr valign="top">

			<th scopr="row"><label for="contest-beast-contest-rules-custom"><?php _e('Custom Rules?'); ?></label></th>

			<td>

				<label>

					<input <?php checked($meta['contest-rules-custom'], 'yes'); ?> type="checkbox" name="contest-beast[contest-rules-custom]" id="contest-beast-contest-rules-custom" value="yes" />

					<?php _e('This contest has custom rules (you will enter them below)'); ?>

				</label>

			</td>

		</tr>

		<tr valign="top" data-dependency="contest-beast[contest-rules-custom]" data-dependency-value="yes">

			<th scope="row"><label for="contest-beast-contest-rules"><?php _e('Rules'); ?></label></th>

			<td>

				<?php wp_editor($meta['contest-rules'], 'contest-beast-contest-rules', array('textarea_name' => 'contest-beast[contest-rules]', 'textarea_rows' => 10)); ?>

			</td>

		</tr>

	</tbody>

</table>

<h4><?php _e('Tracking Scripts'); ?></h4>

<table class="form-table">

	<tbody>

		<tr valign="top">

			<th scope="row"><label for="contest-beast-tracking-scripts-custom"><?php _e('Custom Tracking Scripts?'); ?></label></th>

			<td>

				<label>

					<input <?php checked($meta['tracking-scripts-custom'], 'yes'); ?> type="checkbox" name="contest-beast[tracking-scripts-custom]" id="contest-beast-tracking-scripts-custom" value="yes" />

					<?php _e('This contest has custom tracking scripts (you will enter them below)'); ?>

				</label>

			</td>

		</tr>

		<tr valign="top" data-dependency="contest-beast[tracking-scripts-custom]" data-dependency-value="yes">

			<th scope="row"><label for="contest-beast-tracking-scripts"><?php _e('Rules'); ?></label></th>

			<td>

				<textarea class="large-text code" rows="6" name="contest-beast[tracking-scripts]" id="contest-beast-tracking-scripts"><?php esc_html_e($meta['tracking-scripts']); ?></textarea>

			</td>

		</tr>

	</tbody>

</table>



<?php if('none' !== $settings['mailing-list-provider']) { ?>

<h4><?php _e('Mailing List'); ?></h4>

<table class="form-table">

	<tbody>

		<tr valign="top">

			<th scope="row"><label for="contest-beast-add-to-mailing-list"><?php _e('Add Participants to Mailing List?'); ?></label></th>

			<td>

				<label>

					<input <?php checked('yes', $meta['add-to-mailing-list']); ?> type="checkbox" name="contest-beast[add-to-mailing-list]" id="contest-beast-add-to-mailing-list" value="yes" />

					<?php _e('Add each user who submits the entry form to the specified mailing list'); ?>

				</label>

			</td>

		</tr>

		<?php if(in_array($settings['mailing-list-provider'], array('aweber', 'mailchimp'))) { ?>

		<?php self::display_mailing_list_markup($settings['mailing-list-provider'], $meta['mailing-list-list'], $meta['mailing-list-name']); ?>

		<?php } ?>

	</tbody>

</table>

<?php } ?>



<input type="hidden" name="contest-beast[winner-type]" id="contest-beast-winner-type" value="<?php esc_attr_e($meta['winner-type']); ?>" />

<input type="hidden" name="contest-beast[mailing-list-provider]" id="contest-beast-mailing-list-provider" value="<?php esc_attr_e($settings['mailing-list-provider']); ?>" />

<?php wp_nonce_field('save-contest-beast-meta', 'save-contest-beast-meta-nonce');

