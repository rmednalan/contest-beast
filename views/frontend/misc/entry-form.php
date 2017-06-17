<?php $errors = self::$entry_errors; ?>

<form class="contest-entry-form" method="post" action="<?php the_permalink(); ?>">
	<div class="contest-entry-fieldbox <?php pd_error_output_simple($errors, 'name', 'contest-entry-error-text'); ?>" hidden>
		<label for="contest-entry-name"><?php _e('Enter your name:'); ?><?php pd_error_output($errors, 'name', ' <span>(', ')</span>'); ?></label>
		<input type="text" class="contest-entry-field contest-entry-field-text" name="contest-entry[name]" id="contest-entry-name" value="<?php contest_beast_the_entry_name(); ?>" data-default="<?php _e('Ex: John Smith'); ?>" />
	</div>
	
	<div class="contest-entry-fieldbox <?php pd_error_output_simple($errors, 'email', 'contest-entry-error-text'); ?>">
		<label for="contest-entry-email"><?php _e('Enter your email:'); ?><?php pd_error_output($errors, 'email', ' <span>(', ')</span>'); ?></label>
		<input type="text" class="contest-entry-field contest-entry-field-text" name="contest-entry[email]" id="contest-entry-email" value="<?php contest_beast_the_entry_email(); ?>" data-default="<?php _e('Ex: john@johnsmith.com'); ?>" />
	</div>
	
	<div class="contest-entry-fieldbox contest-entry-fieldbox-submit">
		<input type="submit" class="contest-entry-field contest-entry-field-submit" name="contest-entry[submit]" id="contest-entry-submit" value="<?php _e('Enter Contest!'); ?>" />
	</div>
	
	<input type="hidden" name="contest-entry[referral-code]" id="contest-entry-referral-code" value="<?php contest_beast_the_entry_referral_code(); ?>" /> 
	<input type="hidden" name="contest-entry[contest-id]" id="contest-entry-contest-id" value="<?php the_ID(); ?>" />
	<?php wp_nonce_field('contest-beast-enter-contest-' . get_the_ID(), 'contest-beast-enter-contest-' . get_the_ID() . '-nonce'); ?>
</form>
