<form id="contest-third-party-form" method="post" action="<?php esc_attr_e($form_action); ?>">
	<?php foreach($hidden_fields as $field_key => $field_value) { ?>
	<input type="hidden" name="<?php esc_attr_e($field_key); ?>" value="<?php esc_attr_e($field_value); ?>" />	
	<?php } ?>
	
	<input type="hidden" name="<?php esc_attr_e($email_field_name); ?>" value="<?php esc_attr_e($email); ?>" />
	
	<?php if(!empty($name_field_name)) { ?>
	<input type="hidden" name="<?php esc_attr_e($name_field_name); ?>" value="<?php esc_attr_e($name); ?>" />	
	<?php } ?>
	
	<!-- <input type="submit" id="submitform" value="Continue" /> -->
</form>


<script type="text/javascript">
	window.onload = function() {
		document.forms['contest-third-party-form'].submit();
	};
</script>
