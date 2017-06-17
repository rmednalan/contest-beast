<div class="contest-beast-winners-box">
	
	<img class="contest-beast-winner-action-ajax ajax-feedback hide-if-js" src="<?php esc_attr_e(admin_url('images/wpspin_light.gif')); ?>" alt="" />
	
	<?php if(false === $contest_winners) { ?>
		
		<?php if(0 == $contest_submissions) { ?>
		<?php _e('You don\'t have any submissions to choose winners from!'); ?>
		<?php } else { ?>
		<select data-contest-id="<?php esc_attr_e($contest_id); ?>" class="contest-beast-winners-method">
			<option selected="selected" value="weighted"><?php _e('Random Weighted'); ?></option>
			<option value="entries"><?php _e('Most Entries'); ?></option>
		</select>
		<input data-contest-id="<?php esc_attr_e($contest_id); ?>" type="button" class="button button-primary contest-beast-choose-winners" value="<?php _e('Select Winners'); ?>" /><br />
		<?php } ?>
	
	<?php } else { ?>
	
	<ol id="contest-beast-winners-list">
	<?php foreach($contest_winners as $contest_winner) { ?>
		<li><a href="mailto:<?php esc_attr_e($contest_winner->submission_email); ?>"><?php esc_html_e($contest_winner->submission_name); ?></a><br /><a data-contest-id="<?php esc_attr_e($contest_id); ?>" data-submission-id="<?php esc_attr_e($contest_winner->submission_ID); ?>" href="#" class="contest-beast-disqualify-submission"><?php _e('Disqualify and Replace'); ?></a></li>	
	<?php } ?>
	</ol>
	
	<?php } ?>

</div>