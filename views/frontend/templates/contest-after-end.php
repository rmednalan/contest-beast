<?php include('_inc/header.php'); ?>
<div id="contest-main">

	
   <?php $items = get_post_meta($post->ID, '_contest_beast_post_meta', true); ?>
   <img src="<?php echo $items["branding-logo-url"]; ?>" alt="" />

	

	<h1 class="contest-title"><?php the_title(); ?></h1>

	<div class="contest-description"><?php strip_tags(contest_beast_the_basic_content()); ?></div>

	

	<h2 class="contest-instructions"><?php _e('This contest has ended.'); ?></h2>

</div>



<?php include('_inc/sidebar.php'); ?>



<?php include('_inc/footer.php'); ?>