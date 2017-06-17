
		</div> <!-- .main -->
      </div> <!-- .row -->
    </div> <!-- .container-fluid -->

     <?php 
	 wp_footer();
	 ?>
	 
    <!-- Placed at the end of the document so the pages load faster -->
    <!-- <script src="//code.jquery.com/jquery-1.10.2.js"></script> -->
    <script src="<?php echo plugins_url('contest-beast/templates/member_dashboard_assets/js/bootstrap.min.js';?>"></script>
    <script src="<?php echo plugins_url('contest-beast/templates/member_dashboard_assets/js/bootstrap-multiselect.js';?>"></script>
    <script src="<?php echo plugins_url('contest-beast/templates/member_dashboard_assets/js/docs.min.js';?>"></script>
    <script src="<?php echo plugins_url('contest-beast/templates/member_dashboard_assets/js/jquery.clipboard.js';?>"></script>
    <script src="<?php echo plugins_url('contest-beast/templates/member_dashboard_assets/js/jquery.deserialize.min.js';?>"></script>
    <script src="//code.jquery.com/ui/1.11.2/jquery-ui.js"></script>
	
    <script src="<?php echo plugins_url('contest-beast/templates/member_dashboard_assets/js/jquery.fileupload.js';?>"></script>
    <script src="<?php echo plugins_url('contest-beast/templates/member_dashboard_assets/js/jquery.iframe-transport.js';?>"></script>
	
    <script src="<?php echo plugins_url('contest-beast/templates/member_dashboard_assets/js/colorpicker.js';?>"></script>
	
	<script type="text/javascript">
		(function($) {
			$(document).ready(function() {
				$(document).on('click', '.ptr-preview a.remove', function(e){
					e.preventDefault();
					var group = $(this).closest('form');
					group.find('.ptr-preview').html('');
					group.find('input[name$="_url"]').val('');
					return false;
				});
			});
		})(jQuery);
	</script>
  </body>
</html>
