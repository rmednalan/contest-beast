<?php if($contest_ended) { ?>
	<?php self::display_contest_winners_markup($post->ID); ?>
<?php } else { ?>
	<p><?php _e('The contest has not yet ended. If you wish to choose winners now, please adjust the end date and time above and save the contest.'); ?></p>
<?php } ?>
