<?php
/*
Template Name: Member Dashboard - New Spy Link
*/

global $current_user;
get_currentuserinfo();


?>
<style type="text/css">
.contest_featured_image_error{
   background-color: #fffcfc;
   color: red; 
   width: 320px; 
   padding: 10px; 
   border: 3px solid #fc8c8a;
   margin: 10px;
}

table, th, td {
    border: 1px solid black;
}
</style>
<style type="text/css">
.required_notification {line-height: 1.4em;padding: 5px;display: inline-block;color: red;display:none;}
#url_checking {height: 10px;margin-top: 8px;}
#custom_mailing {display:none;}
.well{


background: #42275a; /* fallback for old browsers */
background: -webkit-linear-gradient(to left, #42275a , #734b6d); /* Chrome 10-25, Safari 5.1-6 */
background: linear-gradient(to left, #42275a , #734b6d); /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */

}
body{




	background: #42275a; /* fallback for old browsers */
	background: -webkit-linear-gradient(to left, #42275a , #734b6d); /* Chrome 10-25, Safari 5.1-6 */
	background: linear-gradient(to left, #42275a , #734b6d); /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */


}
.contest-buzz-create-background{
	background: #FFF;
}
</style>
<div style="margin-top:50px">

</div>
<div class="col-md-8 col-md-offset-2 ">
	<h1 class="page-header"><span class="glyphicon glyphicon-target" ></span><i class="fa fa-cog" style="color:#673AB7"></i>  Support</h1>
	<h4>Frequently Asked Questions</h4>
		
		<blockquote>Lorem ipsum dolor sit amet, consectetur adipiscing elit?</blockquote>
		<p>Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.</p><br>
		
		<blockquote>Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.?</blockquote>
		<p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem.</p></br>
		
		<blockquote>At vero eos et accusamus et iusto odio dignissimos ducimus?</blockquote>
		<p>Cupiditate non provident, similique sunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga.</p><br>
		
		<blockquote>Vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?</blockquote>
		<p>Quis autem vel eum iure reprehenderit qui in ea voluptate. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur.</p></br>
	<fieldset>
		<!-- / -->
		<div class="form-group">
		<h4>We want to hear more from you!</h4>
		<p> Just contact us autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur.</p>
			<label class="control-label" for="textinput">Name</label>
			<div class="">
			<input id="contest_title" value="" name="contest_title" type="text" class="form-control input-md">
			</div>
			
			<label class="control-label" for="textinput">E-mail</label>
			<div class="">
			<input id="contest_title" value="" name="contest_title" type="text" class="form-control input-md">
			</div>
			
			<div class="form-group">
			<label class="control-label" for="textinput">Message</label>
			<div class="">
			<textarea id="disclaimer" name="textarea" rows="7" placeholder=""  class="form-control"></textarea>
			</div><br>
			<button id="create_spy_link" name="singlebutton" type="button" class="btn btn-primary btn-lg" style="background: #6441A5; /* fallback for old browsers */
background: -webkit-linear-gradient(to left, #6441A5 , #2a0845); /* Chrome 10-25, Safari 5.1-6 */
background: linear-gradient(to left, #6441A5 , #2a0845); /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
        ;">Send</button>
			