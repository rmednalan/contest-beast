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
	<script>
		jQuery(document).ready(function($) {
			var winheight = $(window).height(); 
			
			$('.navcontrol').css('height',winheight);

			 
			$('#openformnavi').css('height',winheight+'px');
				
			$('#openformnavi').click(function(){
				$('#contest-container').css('float','none');
				$('#contest-container').css('width','70%');
				$('#contest-container').css('margin-left','5%');
				$('#contest-container').css('margin-top','25%');
			});
			
			$('.formnav').click(function(){
				$('.formnav').removeClass('active');
				$(this).addClass('active');
				 
				
			if($(this).attr('href')=='#contest_info'){
				
				$('#contest_info').toggle('slide', {
					direction: 'left'
				}, 400, function(){ $('#form_container').fadeIn();});
				
			}
			
			if($(this).attr('href')=='#schedule'){
				
				$('#schedule').toggle('slide', {
					direction: 'left'
				}, 400, function(){ $('#form_container').fadeIn();});
				
			}
			
			if($(this).attr('href')=='#mechanics'){
				
				$('#mechanics').toggle('slide', {
					direction: 'left'
				}, 400, function(){ $('#form_container').fadeIn();});
				
			}
			
			if($(this).attr('href')=='#disclaimer'){
				
				$('#disclaimer').toggle('slide', {
					direction: 'left'
				}, 400, function(){ $('#form_container').fadeIn();});
				
			}
			if($(this).attr('href')=='#terms'){
				
				$('#terms').toggle('slide', {
					direction: 'left'
				}, 400, function(){ $('#form_container').fadeIn();});
				
			}
			if($(this).attr('href')=='#privacy_policy'){
				
				$('#privacy_policy').toggle('slide', {
					direction: 'left'
				}, 400, function(){ $('#form_container').fadeIn();});
				
			}
			if($(this).attr('href')=='#rules'){
				
				$('#rules').toggle('slide', {
					direction: 'left'
				}, 400, function(){ $('#form_container').fadeIn();});
				
			}
			
			if($(this).attr('href')=='#mailing_list'){
				
				$('#mailing_list').toggle('slide', {
					direction: 'left'
				}, 400, function(){ $('#form_container').fadeIn();});
				
			}
			
			if($(this).attr('href')=='#tracking_scripts'){
				
				$('#tracking_scripts').toggle('slide', {
					direction: 'left'
				}, 400, function(){ $('#form_container').fadeIn();});
				
			}
			
			if($(this).attr('href')=='#custom_scripts'){
				
				$('#custom_scripts').toggle('slide', {
					direction: 'left'
				}, 400, function(){ $('#form_container').fadeIn();});
				
			}
			
			if($(this).attr('href')=='#styling'){
				
				$('#styling').toggle('slide', {
					direction: 'left'
				}, 400, function(){ $('#form_container').fadeIn();});
				
			}
			
			else{
				$('#form_container').toggle('slide', {
					direction: 'left'
				}, 400, function(){$('#form_container').fadeOut();});
			}
				
			});
			
			$('#nav').affix({
				offset: {     
				  top: 40,
				  bottom: 10
				}
			});

		});
	</script>
	<style>
	
	
.navcontrol {
  background: #f3f3f3;
  width: 300px !important;
      box-shadow: 0 0 5px rgba(57,70,78,.2);
	  border-right:1px solid ##e7e7e7;
	  margin:0px;
	  padding:0px;
}

.navcontrol ul{
	background: #f3f3f3;
	  list-style:none;
	  margin:0px;
	  padding:0px;
	 
}

.navcontrol a {
  color: black;
	text-decoration:none; 
	display:block;
	padding:10px 10px 10px 20px;
}

.navcontrol li{
  padding:0px; margin:0px;
    border-top:1px solid #f2f2f2;
  border-bottom:1px solid #f2f2f2;
}

.navcontrol li a:hover,
.navcontrol li a:focus {
  background: #ddd;
}

.navcontrol .active {
  font-weight: bold;
  background: #ddd;
  border-top:1px solid #ccc;
  border-bottom:1px solid #ccc;
}

.navcontrol .navcontrol {
  display: none;
}

.navcontrol .active .navcontrol {
  display: block;
}

.navcontrol .navcontrol a {
  font-weight: normal;
  font-size: .85em;
}

.navcontrol .navcontrol span {
  margin: 0 5px 0 2px;
}

.navcontrol .navcontrol .active a,
.navcontrol .navcontrol .active:hover a,
.navcontrol .navcontrol .active:focus a {
  font-weight: bold;
  padding-left: 30px;
  border-left: 5px solid black;
}

.navcontrol .navcontrol .active span,
.navcontrol .navcontrol .active:hover span,
.navcontrol .navcontrol .active:focus span {
  display: none;
}

.affix-top {
  position: relative;
  padding-top:20px;
}

.affix {
  top: 51px;
   padding-top:20px;
}

.affix, 
.affix-bottom {
    
}
#fullheght-container{
	
}

	</style>
  </head>
  <body>
  
 

	<?php
	 do_action('contest_before_content');
	?>


  <div class="container-fluid" style="padding:0 !important">
    <div class="row">
      <div class="col-md-3 scrollspy">
		<div id="nav" class="navcontrol hidden-xs hidden-sm" data-spy="affix">
        <ul>
          <li><i class="fa fa-calendar-times-o fa-lg" aria-hidden="true"></i> <a id="nav_contest_info" class="formnav" href="#contest_info"> Contest Information</a></li>
<!--     <li><a href="#">Contest Title</a></li>
<!--     <li><a href="#">Contest URL</a></li>
    <li><a href="#">Description </a></li>
 <li><a href="#">Branding Logo</a></li>	-->
 
    <li><a id="nav_schedule" class="formnav" href="#schedule">Schedule</a></li>
	<!--    
    <li><a href="#">Start Date </a></li>
    <li><a href="#">End Date </a></li>
    <li><a href="#">Countdown template </a></li>
	 --> 
    <li><a id="nav_mechanics" class="formnav" href="#mechanics">Mechanics </a></li>
		<!-- 
    <li><a href="#">Entries per Referral </a></li>
    <li><a href="#">Number of Winners </a></li>
	<li><a href="#">Giveaway Item Picture</a></li>
	 -->
    <li><a id="nav_disclaimer" class="formnav" href="#disclaimer">Disclaimer </a></li>
    <li><a id="nav_term_service" class="formnav" href="#terms">Terms Of Service </a></li>
    <li><a id="nav_Policy" class="formnav" href="#privacy_policy">Privacy Policy Box</a></li>
    <li><a id="nav_custom_rules" class="formnav" href="#rules">Custom Rules?</a></li>
    <li><a id="nav_mailing_list" class="formnav" href="#mailing_list">Add Participants to Mailing List?</a></li>
    <li><a id="nav_tracking_script" class="formnav" href="#tracking_scripts">Custom Tracking Scripts?</a></li>
    <li><a id="nav_custom_script" class="formnav" href="#custom_scripts">Add Custom Scripts </a></li>
	
    <li><a id="nav_styling" class="formnav" href="#styling">Styling</a></li>
        </ul>
      </div>
      </div>
    </div>

  </div><!--end of .container-->
 
  <footer></footer> 
  
		
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
