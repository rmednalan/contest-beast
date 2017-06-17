<?php
/* if (isset($_GET['subscribe']) && $_GET['subscribe']!=""){
	echo base64_decode($_GET['subscribe']);
} */
?> 

<!doctype html>
<!--[if lt IE 7]><html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"><![endif]-->
<!--[if IE 7]><html class="no-js lt-ie9 lt-ie8" lang="en"><![endif]-->
<!--[if IE 8]><html class="no-js lt-ie9" lang="en"><![endif]-->
<!--[if gt IE 8]><!--><html class="no-js" lang="en"><!--<![endif]-->
	<head>
		<?php the_post(); ?>
		<?php $items = get_post_meta($post->ID, '_contest_beast_post_meta', true); 

		?>
		<meta charset="utf-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
		<title><?php the_title(); ?></title>
		<meta name="viewport" content="width=device-width,initial-scale=1">
		<link rel="stylesheet" href="<?php contest_beast_the_template_resources_directory_url(); ?>/css/reset.css" />
		<link rel="stylesheet" href="<?php contest_beast_the_template_resources_directory_url(); ?>/css/style.css" />
		<link rel="stylesheet" href="<?php contest_beast_the_template_resources_directory_url(); ?>/css/share-button.css" />
		<script type="text/javascript" src="<?php contest_beast_the_template_resources_directory_url(); ?>/js/libs/modernizr.min.js"></script>
		
		<!-- Latest compiled and minified CSS -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
		<!-- Optional theme -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
		<!-- Latest compiled and minified JavaScript -->
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
		<meta property="og:title" content="<?php the_title_attribute(); ?>" />
		<meta property="og:description" content="<?php esc_attr_e(strip_tags(get_the_excerpt())); ?>" />
		<meta property="og:image" content="<?=$items['contest_giveaway_item']?>" />
		<meta property="og:image:secure_url" content="<?=$items['contest_giveaway_item']?>" />
		<meta property="og:image:type" content="image/jpeg" />
		<meta property="og:image:width" content="400" />
		<meta property="og:image:height" content="300" />
		
		<?php if(has_post_thumbnail()) { list($src) = wp_get_attachment_image_src(get_post_thumbnail_id(), 'full'); ?>
		<meta property="og:image" content="<?php esc_attr_e($src); ?>" />
		<?php } ?>

		<?php do_action('contest_header'); ?>
		
		
	<?php if(isset($items['contest-custom-tracking-script']) && $items['contest-custom-tracking-script']!="") { 
		echo $items['contest-custom-tracking-script'];
	}
	?>
	
	</head>
	<body>
		<div id="contest-page-container">
			<div role="main" id="contest-container">
			
			
			<?php if(isset($items['contest_giveaway_item']) && $items['contest_giveaway_item']!="") { ?>
			
			<div id="contest-main-pic"><div class="box effect2"><div class="row"><img src="<?=$items['contest_giveaway_item'] ?>" class="img-responsive img-thumbnail"></div></div></div>
			
			<?php } ?>