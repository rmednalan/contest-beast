<?php
function frontend_tools() {
	
	if(is_user_logged_in()) {
	
	global $post;
	?>
		<link rel="stylesheet" href="<?php contest_beast_the_template_resources_directory_url(); ?>/css/frontend_tool.css" />
		<br>
		<br>
		<!-- Textarea -->
		<div class="frontend_tool">
			<div class="form-group">
				<label class="control-label" for="textarea">Custom Scripts</label>
				<div class="">
					<!-- <textarea id="post_content" name="textarea" rows="7" placeholder="Description"  class="form-control"></textarea> -->
				  <?php wp_editor( '', 'post_content', array('media_buttons' => false, 'textarea_rows' => 10) ); ?>
				  <style type="text/css">.wp-editor-container{border:1px solid #e5e5e5;}</style>
				</div>
			</div>
		</div>
		<!-- Textarea -->
		<br>
	<?php
		wp_footer();
	}
}
add_shortcode('frontend_tools','frontend_tools');

?>