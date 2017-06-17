<?php include('_inc/header.php'); ?>

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
	<div class="col-sm-12 col-dm-12"><h1><?php the_title(); ?></h1></div>
	<div style="clear:both; padding-bottom:20px;"></div>
	</div>
-->
	<div class="contest-description"><?php contest_beast_the_basic_content(); ?></div>
	<h2 class="contest-instructions"><?php _e('This contest has not started yet! Come back soon!'); ?></h2>

	<div class="contest-date">
		<?php _e('Contest Starts'); ?>
		<strong><?php contest_beast_the_contest_start_date(); ?></strong>
	</div>
</div>
</div>

<?php include('_inc/sidebar.php'); ?>



<?php include('_inc/footer.php'); ?>