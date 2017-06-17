<?php


if( !is_user_logged_in() ) {
	wp_redirect(wp_login_url(site_url('/member-dashboard/')));
	exit();
}


?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="member_dashboard_assets/ico/favicon.ico">

    <title>Member Dashboard</title>

    <!-- Bootstrap core CSS -->
    <link href="<?php echo plugins_url('contest-beast/templates/member_dashboard_assets/css/bootstrap.min.css'); ?>" rel="stylesheet">

    <link href="<?php echo plugins_url('contest-beast/templates/member_dashboard_assets/css/bootstrap-multiselect.css'); ?>" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="<?php echo plugins_url('contest-beast/templates/member_dashboard_assets/css/dashboard.css'); ?>" rel="stylesheet">
    <link type="text/css" rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/themes/smoothness/jquery-ui.css">

    <!-- Just for debugging purposes. Don't actually copy this line! -->
    <!--[if lt IE 9]><script src="<?php echo get_template_directory_uri(); ?>/member_dashboard_assets/js/ie8-responsive-file-warning.js"></script><![endif]-->

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!-- Bootstrap core JavaScript
    ================================================== -->
	<link rel="stylesheet" href="<?php echo plugins_url('contest-beast/templates/member_dashboard_assets/css/jquery.fileupload.css')?>" />
	<link rel="stylesheet" href="<?php echo plugins_url('contest-beast/templates/member_dashboard_assets/css/colorpicker.css')?>" />
    <?php
	wp_head();
	?>
	<link rel="stylesheet" type="text/css" href="http://fortawesome.github.io/Font-Awesome/assets/font-awesome/css/font-awesome.css" media="all" />
	<!-- <link href='http://fonts.googleapis.com/css?family=Lato:300,400,700|Merriweather+Sans:400,700,300' rel='stylesheet' type='text/css'/> -->
	<link href='http://fonts.googleapis.com/css?family=Roboto:400,700,500,300' rel='stylesheet' type='text/css'>
	<style type="text/css">
	body {font-family: 'Roboto', sans-serif;}
	h1, h2, h3, h4, h5, h6 {font-family: 'Roboto', sans-serif;}
	.contest-beast-winners-box {text-align:left;}
	.contest-beast-winners-box ol{padding: 0;list-style-position: inside;text-align: left;}
	.navbar-default{
			box-shadow: 0 0 4px #673AB7;
	}
	.navbar-default .navbar-nav>li>a {
    color: #fff;
		padding-top: 24px;
		padding-bottom: 28px;
    text-transform: uppercase;
		font-size: 15px;
		-webkit-transition:  1s; /* For Safari 3.1 to 6.0 */
		transition: 1s;

	}
	.navbar-default .navbar-nav>li>a:hover, .navbar-default .navbar-nav>li>a:focus {
			color: #FFF;
			background-color: #000;

	}

	.pinky-template .primary-navigation-section {
    box-shadow: 0 0 4px 0 rgba(0,0,0,.08),0 2px 4px 0 rgba(0,0,0,.12);
    z-index: 1020;
 }
	</style>
  </head>

  <body>

    <div class="navbar navbar-default navbar-fixed-top" role="navigation" style="background-color:#673AB7 !important;

background: #6441A5; /* fallback for old browsers */
background: -webkit-linear-gradient(to left, #6441A5 , #2a0845); /* Chrome 10-25, Safari 5.1-6 */
background: linear-gradient(to left, #6441A5 , #2a0845); /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
        ;min-height:65px !important">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="<?php echo get_page_link(9); ?>"><img src="<?=plugins_url('contest-beast/templates/assets/images/')?>logo_member_dashboard.png" alt=""></a>
        </div>
        <div class="navbar-collapse collapse" style="font-size: 12px;padding-right: 30px;">
          <ul class="nav navbar-nav">
            <li><a href="/my-account/dashboard"><span class="glyphicon glyphicon-stats"></span> My Contests</a></li>
            <li><a href="/my-account/new-contest"><span class="fa fa-plus"></span> New Contest</a></li>
            <li><a href="/my-account/contest-university"><span class="glyphicon glyphicon-plus-sign"></span> Contest University</a></li>
            <li><a href="/my-account/tutorials"><span class="glyphicon glyphicon-facetime-video"></span> Tutorials</a></li>
            <li><a href="/my-account/support"><span class="glyphicon glyphicon-briefcase"></span> Support</a></li>
          </ul>

          <ul class="nav navbar-nav pull-right">
            <li><a class="dropdown-toggle" data-toggle="dropdown" data-target="#" href="#"> <span class="glyphicon glyphicon-user"></span> <?php global $current_user; get_currentuserinfo(); echo $current_user->user_firstname; ?> <?php echo $current_user->user_lastname ?> <span class="caret"></span></a>
			<ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">
				<li role="presentation"><a role="menuitem" tabindex="-1" href="<?php echo wp_logout_url(); ?> ">Logout</a></li>
			  </ul>
			</li>
          </ul>

        </div>
      </div>
    </div> <!-- .navbar -->
		<div style="min-height:20px"></div>

	<div class="container">
      <div class="row">


<div style="min-height:40px"></div>
        <div class="col-md-12 main contest-buzz-create-background">
