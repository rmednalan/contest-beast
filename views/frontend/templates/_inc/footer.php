				<div class="clear"></div>
			</div><!-- /#contest-container -->
		</div><!-- /#contest-page-container -->
		
		<?php include('rules.php'); ?>
		
		<!-- Modal -->
	<div id="myModal" class="modal fade" role="dialog">
	  <div class="modal-dialog">

		<!-- Modal content-->
		<div class="modal-content">
		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal">&times;</button>
			<h4 class="modal-title">Modal Header</h4>
		  </div>
		  <div class="modal-body">
			<p>Some text in the modal.</p>
		  </div>
		  <div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		  </div>
		</div>

	  </div>
	</div>

		<script src="<?php contest_beast_the_template_resources_directory_url(); ?>/js/libs/jquery.min.js"></script>
		<script src="<?php contest_beast_the_template_resources_directory_url(); ?>/js/script.js?ver=1.0"></script>
		<?php//echo "<input type='hidden' value='"contest_beast_the_contest_tracking_scripts();"'/>";  ?>
	</body>
</html>