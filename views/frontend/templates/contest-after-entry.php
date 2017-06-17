<?php include('_inc/header.php'); ?>
<!-- Facebook Like Button -->
<div id="fb-root"></div>

<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>



<div id="contest-main">
	<div class="container-fluid"> 
   <?php
   if (isset($items["branding-logo-url"]) AND $items["branding-logo-url"]!=""){
   ?>
   <div class="row">
	<div class="col-sm-4 col-dm-4 col-md-offset-4"><img src="<?php echo $items["branding-logo-url"]; ?>" class="img-responsive img-thumbnail" alt="" /></div>
	<div style="clear:both; padding-bottom:20px;"></div>
  </div>
   <?php } ?>	
   <!--
   	<div class="row">
	<div class="col-sm-12 col-dm-12"><h1 class="text-center"><?php the_title(); ?></h1></div>
	<div style="clear:both; padding-bottom:20px;"></div>
	</div>
-->
	<div class="contest-entry-count"><?php printf(__('So Far You\'ve Earned <strong>%d</strong> Entries.'), contest_beast_get_contest_number_entries_for_email()); ?></div>
	<h1 class="contest-title contest-title-get-friends"><?php printf(__('Get Your Friends To Enter...<br />Get <strong>%d</strong> More Chances To Win!'), contest_beast_get_contest_referral_entries()); ?></h1>
	<h2 class="contest-instructions contest-instructions-step-2"><?php _e('STEP 2: Tell Some Folks!'); ?></h2>

	<div class="contest-share text-center">

		<ul>

			<li>

				<span class="contest-share-social-label"><?php _e('Share on Facebook:'); ?></span>

				<span class="contest-share-social-widget contest-share-social-widget-facebook">

					<?php $fb_share_url = add_query_arg(array('u' => urlencode(contest_beast_get_referral_url()), 't' => urlencode(get_the_title())), 'http://www.facebook.com/sharer.php'); ?>

					<a class="contest-fb-share" href="<?php esc_attr_e($fb_share_url); ?>"><span class="FBConnectButton FBConnectButton_Small" style="cursor:pointer;"><span class="FBConnectButton_Text">Share</span></span></a>

				</span>

				<span class="contest-clear"></span>

			</li>

			<li>

				<span class="contest-share-social-label"><?php _e('Share on Twitter:'); ?></span>

				<span class="contest-share-social-widget contest-share-social-widget-twitter">

					<a href="https://twitter.com/share" class="twitter-share-button" data-lang="en" data-count="none" data-text="<?php the_title_attribute(); ?>" data-via="<?php contest_beast_the_twitter_handle(); ?>" data-url="<?php contest_beast_the_referral_url(); ?>">Tweet</a>

				</span>

				<span class="contest-clear"></span>

			</li>

			<li>

				<span class="contest-share-social-label"><?php _e('Share on LinkedIn:'); ?></span>

				<span class="contest-share-social-widget contest-share-social-widget-linkedin">

					<script type="IN/Share" data-url="<?php contest_beast_the_referral_url(); ?>"></script>

				</span>

				<span class="contest-clear"></span>

			</li>

			<li>

				<?php _e('Share anywhere with this special link:'); ?>

			</li>

			<li>

				<input id="contest-referral-link-field" class="contest-entry-field-text" type="text" value="<?php contest_beast_the_referral_url(); ?>" />

			</li>

		</ul>

	</div>

	

	<?php if(contest_beast_has_twitter_handle() || ('like' == contest_beast_get_facebook_social_method() && contest_beast_has_facebook_like_url()) || ('subscribe' == contest_beast_get_facebook_social_method() && contest_beast_has_facebook_profile_url())) { ?>

	

	<h2 class="contest-instructions contest-instructions-step-3"><?php _e('STEP 3: Stay in Touch!'); ?></h2>

	

	<div class="contest-social">

		<ul>

			<?php if(contest_beast_has_twitter_handle()) { ?>

			<li class="contest-twitter-follow">

				<a href="https://twitter.com/<?php contest_beast_the_twitter_handle(); ?>" class="twitter-follow-button" data-show-count="false" data-lang="en"><?php printf(__('Follow @%s'), contest_beast_get_twitter_handle()); ?></a>

				<?php _e('to see if you win'); ?>

				<style type="text/css">

				.contest-twitter-follow iframe {

					width: <?php echo intval(72 + (6.25 * strlen(contest_beast_get_twitter_handle()))); ?>px !important;

				}

				</style>

			</li>

			<?php } ?>

			<?php if('like' == contest_beast_get_facebook_social_method() && contest_beast_has_facebook_like_url()) { ?>

			<li class="contest-fb-like">

				<div class="fb-like" data-send="false" data-href="<?php contest_beast_the_facebook_like_url(); ?>" data-layout="button_count" data-width="74" data-show-faces="false"></div>

				<?php _e('us for more contests and info'); ?>

			</li>

			<?php } ?>

			<?php if('subscribe' == contest_beast_get_facebook_social_method() && contest_beast_has_facebook_profile_url()) { ?>

			<li class="contest-fb-subscribe">

				<div class="fb-subscribe" data-send="false" data-href="<?php contest_beast_the_facebook_profile_url(); ?>" data-layout="button_count" data-width="118" data-show-faces="false"></div>

				<?php _e('to our updates'); ?>

			</li>

			<?php } ?>

		</ul>

	</div>

	

	<?php } ?>

	<!--User Custom scripts-->
	
	<?php 
	if (isset($items["contest_thank_you"]) AND $items["contest_thank_you"]!=""){
	?>
   	<div class="row">
	<div class="col-sm-12 col-dm-12"><?=$items["contest_thank_you"]; ?></div>
	<div style="clear:both; padding-bottom:20px;"></div>
	</div>
	<?php } ?>

	<div class="contest-date">

		<?php _e('Contest Ends'); ?>
		<strong><?php contest_beast_the_contest_end_date(); ?></strong>
		<br />
		<img src="<?php echo site_url('/?dmc=1&tpl=' . $items['timer-template'] .'&ed=' . $items['end-timestamp']);?>" alt="" />
	</div>

</div>
</div>



<?php include('_inc/sidebar.php'); ?>



<!-- Twitter Buttons -->

<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>



<!-- LinkedIn Share Button -->

<script src="http://platform.linkedin.com/in.js" type="text/javascript"></script>



<?php include('_inc/footer.php'); ?>