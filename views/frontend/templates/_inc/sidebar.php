<div id="contest-auxiliary">
	<?php //if(has_post_thumbnail()) { ?>
	<div class="contest-image"><?php //the_post_thumbnail('contest-image'); ?><!-- original position of the image --></div>
	<?php //} ?>
        <?php /*contest_beast_the_contest_disclaimer();*/ ?>
        <div class="termsLinks">
			<div class="contest-disclaimer"><a href="<?php esc_attr_e(add_query_arg(array('contest-only-rules' => 1), get_permalink())); ?>" class="contest-rules-toggle"><?php _e('See Official Rules'); ?></a></div>
				<?php /*if(contest_beast_get_contest_tos()){*/ ?>        
			<div class="contest-tos"><a href="<?php esc_attr_e(add_query_arg(array('contest-tos' => 1), get_permalink())); ?>" class="contest-tos-toggle"><?php _e('See Terms Of Service'); ?></a></div>
				<?php /*}*/ ?>        
				<?php /*if(contest_beast_get_contest_ppb()){*/ ?>        
			<div class="contest-ppb"><a href="<?php esc_attr_e(add_query_arg(array('contest-ppb' => 1), get_permalink())); ?>" class="contest-ppb-toggle"><?php _e('See Privacy Policy'); ?></a></div>
        <?php /*}*/ ?>
        </div>
	<?php if(contest_beast_is_promoting()) { ?>
	<div class="contest-powered">
		<?php _e('powered by:'); ?>
		<a href="<?php contest_beast_the_promotion_url(); ?>"><?php _e('Contest Beast'); ?></a>
	</div>
	<?php } ?>
</div>