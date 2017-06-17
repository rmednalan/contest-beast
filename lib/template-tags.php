<?php



// DISPLAY



add_filter( 'contest_beast_the_basic_content', 'wptexturize'        );

add_filter( 'contest_beast_the_basic_content', 'convert_smilies'    );

add_filter( 'contest_beast_the_basic_content', 'convert_chars'      );

add_filter( 'contest_beast_the_basic_content', 'wpautop'            );

add_filter( 'contest_beast_the_basic_content', 'shortcode_unautop'  );

add_filter( 'contest_beast_the_basic_content', 'prepend_attachment' );



function contest_beast_get_basic_content() {

	global $post;

	return apply_filters('contest_beast_get_basic_content', $post->post_content);

}



function contest_beast_the_basic_content() {

	echo apply_filters('contest_beast_the_basic_content', contest_beast_get_basic_content());

}



function contest_beast_get_entry_form() {

	return apply_filters('contest_beast_get_entry_form', Contest_Beast::get_entry_form());

}



function contest_beast_the_entry_form() {

	echo apply_filters('contest_beast_the_entry_form', contest_beast_get_entry_form());

}



// FLAGS



/// IS FLAGS



function contest_beast_is_contest() {

	return apply_filters('contest_beast_is_contest', Contest_Beast::is_contest());

}



function contest_beast_is_promoting() {

	return apply_filters('contest_beast_is_promoting', Contest_Beast::is_promoting());

}



/// START + END + ENTERED



function contest_beast_has_started($contest_id = null) {

	return apply_filters('contest_beast_has_started',
		Contest_Beast::has_started($contest_id), $contest_id);

}



function contest_beast_has_ended($contest_id = null) {

	return apply_filters('contest_beast_has_ended',
		Contest_Beast::has_ended($contest_id), $contest_id);

}



function contest_beast_has_entered($contest_id = null, $email_address = null) {

	return apply_filters('contest_beast_has_entered',
		Contest_Beast::has_entered($contest_id, $email_address), $contest_id, $email_address);

}



// URLS



function contest_beast_has_branding_logo_url() {

	$value = contest_beast_get_branding_logo_url();

	return apply_filters('contest_beast_has_branding_logo_url', !empty($value));

}



function contest_beast_get_branding_logo_url() {

	return apply_filters('contest_beast_get_branding_logo_url', Contest_Beast::get_branding_logo_url());

}



function contest_beast_the_branding_logo_url() {

	echo apply_filters('contest_beast_the_branding_logo_url', contest_beast_get_branding_logo_url());

}



function contest_beast_get_promotion_url() {

	return apply_filters('contest_beast_get_promotion_url', Contest_Beast::get_promotion_url());

}



function contest_beast_the_promotion_url() {

	echo apply_filters('contest_beast_the_promotion_url', contest_beast_get_promotion_url());

}



function contest_beast_get_referral_url($contest_id = null, $email_address = null) {

	return apply_filters('contest_beast_get_referral_url', Contest_Beast::get_referral_url($contest_id, $email_address), $contest_id, $email_address);

}



function contest_beast_the_referral_url($contest_id = null, $email_address = null) {

	echo apply_filters('contest_beast_the_referral_url', contest_beast_get_referral_url($contest_id, $email_address), $contest_id, $email_address);

}



function contest_beast_get_template_resources_directory_url() {

	return apply_filters('contest_beast_get_template_resources_directory_url', Contest_Beast::get_template_resources_directory_url());

}



function contest_beast_the_template_resources_directory_url() {

	echo apply_filters('contest_beast_the_template_resources_directory_url', contest_beast_get_template_resources_directory_url());

}



function contest_beast_get_website_url() {

	return apply_filters('contest_beast_get_website_url', Contest_Beast::URLS__CONTEST_BEAST);

}



function contest_beast_the_website_url() {

	echo apply_filters('contest_beast_the_website_url', contest_beast_get_website_url());

}



// Entry



function contest_beast_get_entry_email() {

	return apply_filters('contest_beast_get_entry_email', isset($_POST['contest-entry']['email']) ? stripslashes($_POST['contest-entry']['email']) : '');

}



function contest_beast_the_entry_email() {

	echo apply_filters('contest_beast_the_entry_email', contest_beast_get_entry_email());

}



function contest_beast_get_entry_name() {

	return apply_filters('contest_beast_get_entry_name', isset($_POST['contest-entry']['name']) ? stripslashes($_POST['contest-entry']['name']) : '');

}



function contest_beast_the_entry_name() {

	echo apply_filters('contest_beast_the_entry_name', contest_beast_get_entry_name());

}



function contest_beast_get_entry_referral_code() {

	if (!empty($_POST['contest-entry']['referral-code'])) {

		$return = stripslashes($_POST['contest-entry']['referral-code']);

	} else if (!empty($_REQUEST['ref'])) {

		$return = stripslashes($_REQUEST['ref']);

	} else {

		$return = '';

	}



	return apply_filters('contest_beast_get_entry_referral_code', $return);

}



function contest_beast_the_entry_referral_code() {

	echo apply_filters('contest_beast_the_entry_referral_code', contest_beast_get_entry_referral_code());

}



// EVENT META



function contest_beast_has_twitter_handle() {

	$value = contest_beast_get_twitter_handle();

	return apply_filters('contest_beast_has_twitter_handle', !empty($value));

}

function contest_beast_get_twitter_handle() {

	return apply_filters('contest_beast_get_twitter_handle', Contest_Beast::get_twitter_handle());

}

function contest_beast_the_twitter_handle() {

	echo apply_filters('contest_beast_the_twitter_handle', contest_beast_get_twitter_handle());

}



function contest_beast_get_facebook_social_method() {

	return apply_filters('contest_beast_get_facebook_social_method', Contest_Beast::get_facebook_social_method());

}



function contest_beast_has_facebook_like_url() {

	$value = contest_beast_get_facebook_like_url();

	return apply_filters('contest_beast_has_facebook_like_url', !empty($value));

}

function contest_beast_get_facebook_like_url() {

	return apply_filters('contest_beast_get_facebook_like_url', Contest_Beast::get_facebook_like_url());

}

function contest_beast_the_facebook_like_url() {

	echo apply_filters('contest_beast_the_facebook_like_url', contest_beast_get_facebook_like_url());

}



function contest_beast_has_facebook_profile_url() {

	$value = contest_beast_get_facebook_profile_url();

	return apply_filters('contest_beast_has_facebook_profile_url', !empty($value));

}

function contest_beast_get_facebook_profile_url() {

	return apply_filters('contest_beast_get_facebook_profile_url', Contest_Beast::get_facebook_profile_url());

}

function contest_beast_the_facebook_profile_url() {

	echo apply_filters('contest_beast_the_facebook_profile_url', contest_beast_get_facebook_profile_url());

}





/// GENERAL



function contest_beast_get_contest_disclaimer($contest_id = null) {

	return apply_filters('contest_beast_get_contest_disclaimer', Contest_Beast::get_contest_disclaimer($contest_id), $contest_id);

}



function contest_beast_the_contest_disclaimer($contest_id = null) {

	echo apply_filters('contest_beast_the_contest_disclaimer', contest_beast_get_contest_disclaimer($contest_id), $contest_id);

}



function contest_beast_get_contest_number_winners($contest_id = null) {

	return apply_filters('contest_beast_get_contest_number_winners', Contest_Beast::get_contest_number_winners($contest_id), $contest_id);

}



function contest_beast_the_contest_number_winners($contest_id = null) {

	echo apply_filters('contest_beast_the_contest_number_winners', contest_beast_get_contest_number_winners($contest_id), $contest_id);

}



function contest_beast_get_contest_referral_entries($contest_id = null) {

	return apply_filters('contest_beast_get_contest_referral_entries', Contest_Beast::get_contest_referral_entries($contest_id), $contest_id);

}



function contest_beast_the_contest_referral_entries($contest_id = null) {

	echo apply_filters('contest_beast_the_contest_referral_entries', contest_beast_get_contest_referral_entries($contest_id), $contest_id);

}



function contest_beast_get_contest_rules($contest_id = null) {

	return apply_filters('contest_beast_get_contest_rules', Contest_Beast::get_contest_rules($contest_id), $contest_id);

}
function contest_beast_get_contest_tos($contest_id = null) {

	return apply_filters('contest_beast_get_contest_tos', Contest_Beast::get_contest_tos($contest_id), $contest_id);

}
function contest_beast_get_contest_ppb($contest_id = null) {

	return apply_filters('contest_beast_get_contest_ppb', Contest_Beast::get_contest_ppb($contest_id), $contest_id);

}


//added 4/18/2017
function contest_beast_get_contest_video($contest_id = null) {

	return apply_filters('contest_beast_get_contest_video', Contest_Beast::get_contest_video($contest_id), $contest_id);

}

//added 4/18/2017
function contest_beast_the_contest_video($contest_id = null) {

	echo apply_filters('contest_beast_the_contest_video', contest_beast_get_contest_video($contest_id), $contest_id);

}


function contest_beast_the_contest_rules($contest_id = null) {

	echo apply_filters('contest_beast_the_contest_rules', contest_beast_get_contest_rules($contest_id), $contest_id);

}
function contest_beast_the_contest_tos($contest_id = null) {

	echo apply_filters('contest_beast_the_contest_tos', contest_beast_get_contest_tos($contest_id), $contest_id);

}
function contest_beast_the_contest_ppb($contest_id = null) {
	echo apply_filters('contest_beast_the_contest_ppb', contest_beast_get_contest_ppb($contest_id), $contest_id);
}

function contest_beast_get_contest_tracking_scripts($contest_id = null) {

	return apply_filters('contest_beast_get_contest_tracking_scripts', Contest_Beast::get_contest_tracking_scripts($contest_id), $contest_id);

}



function contest_beast_the_contest_tracking_scripts($contest_id = null) {

	echo apply_filters('contest_beast_the_contest_tracking_scripts', contest_beast_get_contest_tracking_scripts($contest_id), $contest_id);

}



/// DATES



function contest_beast_get_contest_end_date($contest_id = null, $date_format = null) {

	return apply_filters('contest_beast_get_contest_end_date', Contest_Beast::get_contest_end_date($contest_id, $date_format), $contest_id, $date_format);

}



function contest_beast_the_contest_end_date($contest_id = null, $date_format = null) {

	echo apply_filters('contest_beast_the_contest_end_date', contest_beast_get_contest_end_date($contest_id, $date_format), $contest_id, $date_format);

}



function contest_beast_get_contest_start_date($contest_id = null, $date_format = null) {

	return apply_filters('contest_beast_get_contest_start_date', Contest_Beast::get_contest_start_date($contest_id, $date_format), $contest_id, $date_format);

}



function contest_beast_the_contest_start_date($contest_id = null, $date_format = null) {

	echo apply_filters('contest_beast_the_contest_start_date', contest_beast_get_contest_start_date($contest_id, $date_format), $contest_id, $date_format);

}



/// ENTRIES, SUBMISSIONS, & WINNERS



function contest_beast_get_contest_number_entries($contest_id = null) {

	return apply_filters('contest_beast_get_contest_number_entries', Contest_Beast::get_contest_number_entries($contest_id), $contest_id);

}



function contest_beast_the_contest_number_entries($contest_id = null) {

	echo apply_filters('contest_beast_the_contest_number_entries', contest_beast_get_contest_number_entries($contest_id), $contest_id);

}



function contest_beast_get_contest_number_entries_for_email($contest_id = null, $email_address = null) {

	return apply_filters('contest_beast_get_contest_number_entries_for_email', Contest_Beast::get_contest_number_entries_for_email($contest_id, $email_address), $contest_id, $email_address);

}



function contest_beast_the_contest_number_entries_for_email($contest_id = null, $email_address = null) {

	echo apply_filters('contest_beast_the_contest_number_entries_for_email', contest_beast_get_contest_number_entries_for_email($contest_id, $email_address), $contest_id, $email_address);

}



function contest_beast_get_contest_number_submissions($contest_id = null) {

	return apply_filters('contest_beast_get_contest_number_submissions', Contest_Beast::get_contest_number_submissions($contest_id), $contest_id);

}



function contest_beast_the_contest_number_submissions($contest_id = null) {

	echo apply_filters('contest_beast_the_contest_number_submissions', contest_beast_get_contest_number_submissions($contest_id), $contest_id);

}



function contest_beast_get_contest_winners($contest_id = null) {

	return apply_filters('contest_beast_get_winners', Contest_Beast::get_contest_winners($contest_id), $contest_id);

}