<tr valign="top" class="contest-beast-mailing-list-details-container contest-beast-mailing-list-aweber-details-container contest-beast-mailing-list-mailchimp-details-container">
	<th scope="row"><label for="contest-beast-mailing-list-list"><?php _e('Mailing List'); ?></label></th>
	<td>
		<div>
			<div id="contest-beast-mailing-list-list-container">
				<?php if(false === $lists) { ?>
				<?php _e('Your mailing lists could not be retrieved. Please check your credentials above.'); ?>
				<?php } else { ?>
				<select class="code" name="contest-beast[mailing-list-list]" id="contest-beast-mailing-list-list">
					<?php foreach($lists as $list_key => $list_name) { ?>
					<option <?php selected($mailing_list, $list_key); ?> value="<?php esc_attr_e($list_key); ?>"><?php esc_html_e($list_name); ?></option>	
					<?php } ?>
				</select>
				<?php } ?>
			</div>
			<img id="contest-beast-mailing-list-list-ajax" class="ajax-feedback hide-if-js" src="<?php esc_attr_e(admin_url('images/wpspin_light.gif')); ?>" alt="" />
		</div>
	</td>
</tr>
<tr valign="top" class="contest-beast-mailing-list-details-container contest-beast-mailing-list-mailchimp-details-container">
	<th scope="row"><label for="contest-beast-mailing-list-name"><?php _e('Mailing List Name Field'); ?></label></th>
	<td>
		<div>
			<div id="contest-beast-mailing-list-name-container">
				<?php if(false === $fields) { ?>
				<?php _e('The fields for the selected list could not be retrieved. Please check your credentials above and the selected list.'); ?>	
				<?php } else { ?>
				<select class="code" name="contest-beast[mailing-list-name]" id="contest-beast-mailing-list-name">
					<option <?php selected($field_name, ''); ?> value=""><?php _e('== None =='); ?></option>
					<?php foreach($fields as $field_key => $field_descriptor) { ?>
					<option <?php selected($field_name, $field_key); ?> value="<?php esc_attr_e($field_key); ?>"><?php esc_html_e($field_descriptor); ?></option>	
					<?php } ?>
				</select>
				<?php } ?>
			</div>
			<img id="contest-beast-mailing-list-name-ajax" class="ajax-feedback hide-if-js" src="<?php esc_attr_e(admin_url('images/wpspin_light.gif')); ?>" alt="" />
		</div>
	</td>
</tr>
