<?php do_action('contest_before_theme'); ?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="member_dashboard_assets/ico/favicon.ico">
	<?php wp_head(); ?>
	<?php do_action('contest_after_head'); ?>
  </head>
  <body>

	<?php
	 do_action('contest_before_content');
	?>

<div id="contestwhole">
	<div class="container" id="contest-container">
		<div class="row">
		<div style="min-height:40px"></div>
			<div class="col-md-12 main contest-buzz-create-background">
			<?php
			do_action('contest_content');
			?>
			</div>
		</div>
	</div>
</div>

	<?php
	do_action('contest_after_content');
	?>
	
	<?php
	/* load the footer */
	do_action('contest_before_footer');
	wp_footer();
	do_action('contest_after_footer');
	?>
  </body>
</html>
