<?php
/*
Template Name: Member Dashboard Page
*/
include('member_dashboard_files/header.php');

global $current_user;
get_currentuserinfo(); 
?>

<link rel="stylesheet" href="//cdn.datatables.net/plug-ins/3cfcc339e89/integration/bootstrap/3/dataTables.bootstrap.css" />
<script type="text/javascript" language="javascript" src="//cdn.datatables.net/1.10.4/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" language="javascript" src="//cdn.datatables.net/plug-ins/3cfcc339e89/integration/bootstrap/3/dataTables.bootstrap.js"></script>


<style type="text/css">
#url_checking {height: 10px;margin-top: 8px;}
#custom_mailing {display:none;}
.ui-widget{z-index:9999 !important;}
.contest-beast-winners-box img{display:none;}
</style>
	<h1 class="page-header"><?php the_title(); ?></h1>
	<?php the_content(); ?>

<?php include('member_dashboard_files/footer.php'); ?>
page main