<?php

define('CONTEST_PLUGIN_NAME', 'contest');
define('CONTEST_DIR', plugin_dir_path(__DIR__));
define('CONTEST_LIB_DIR', CONTEST_DIR . 'lib/');

global $current_user;
//print_r($current_user); //all user related information
//echo labdevs_get_user_current_id(); //get current user id 

function labdevs_short_url_link()
{
    return get_option("labdevs_settings_option_short_url");
}

/* Written by Omar Tariq (Like A Boss Developers) */
function labdevs_get_user_current_id()
{
    global $current_user;
    get_currentuserinfo();
    return $current_user->ID;
}

function labdevs_current_user_rt_cookies()
{

    $posts = new WP_Query();
    $posts->query(array(
        'posts_per_page' => -1,
        'author' => labdevs_get_user_current_id(),
        'post_type' => 'labdevsrtcookie'
    ));
    wp_reset_postdata();
    return sizeof($posts->posts);
}

// add_action( 'init', 'labdevs_rtcookie_post_type_init' );
function labdevs_rtcookie_post_type_init()
{
    $labels = array(
        'name' => _x('labdevsrtcookies', 'post type general name', 'your-plugin-textdomain'),
        'singular_name' => _x('labdevsrtcookie', 'post type singular name', 'your-plugin-textdomain'),
        'menu_name' => _x('labdevsrtcookies', 'admin menu', 'your-plugin-textdomain'),
        'name_admin_bar' => _x('labdevsrtcookie', 'add new on admin bar', 'your-plugin-textdomain'),
        'add_new' => _x('Add New', 'labdevsrtcookie', 'your-plugin-textdomain'),
        'add_new_item' => __('Add New labdevsrtcookie', 'your-plugin-textdomain'),
        'new_item' => __('New labdevsrtcookie', 'your-plugin-textdomain'),
        'edit_item' => __('Edit labdevsrtcookie', 'your-plugin-textdomain'),
        'view_item' => __('View labdevsrtcookie', 'your-plugin-textdomain'),
        'all_items' => __('All labdevsrtcookies', 'your-plugin-textdomain'),
        'search_items' => __('Search labdevsrtcookies', 'your-plugin-textdomain'),
        'parent_item_colon' => __('Parent labdevsrtcookies:', 'your-plugin-textdomain'),
        'not_found' => __('No labdevsrtcookies found.', 'your-plugin-textdomain'),
        'not_found_in_trash' => __('No labdevsrtcookies found in Trash.', 'your-plugin-textdomain'),
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'labdevsrtcookie'),
        'capability_type' => 'post',
        'has_archive' => true,
        'hierarchical' => false,
        'menu_position' => null,
        'supports' => array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments')
    );

    register_post_type('labdevsrtcookie', $args);
}

add_filter('contest_beast_the_promotion_url', 'labdevs_contest_beast_the_promotion_url');
function labdevs_contest_beast_the_promotion_url()
{
    return 'http://contestbeast.com';
}

add_action('admin_menu', 'register_my_custom_menu_page');
function register_my_custom_menu_page()
{
    add_menu_page('Short URL Site', 'Short URL Site', 'manage_options', 'shortcode-filter', 'my_custom_menu_page');
}


add_action('wp_ajax_contest_update_datacontest', 'contest_update_datacontest');
function contest_update_datacontest()
{
    //save function post
    global $current_user;
    global $user_ID;
    get_currentuserinfo();


    $contestDataPost = $_POST['contest-beast'];

    //echo print_r($contestDataPost);
    //echo print_r($_POST);

    if ($_POST['contest_id'] > 0) {

        $post_contest = array(
            'ID' => $_POST['contest_id'],
            'post_title' => $_POST['post_title'],
            'post_content' => $_POST['post_content']
        );

        $contest_id = $_POST['contest_id'];
        wp_update_post($post_contest);

    } else {
        $newContest = array(
            'post_title' => $_POST['post_title'],
            'post_content' => $_POST['post_content'],
            'post_author' => $user_ID,
            'post_status' => 'publish',
            'post_type' => 'contest'
        );

        $contest_id = wp_insert_post($newContest);
    }


    /* get complete time */
    $string_time = $contestDataPost['start-date'] . " " . $contestDataPost['start-time-hour'] . ":" . $contestDataPost['start-time-minute'] . ":00 " . $contestDataPost['start-time-meridian'];
    $start_front = strtotime($string_time);
    $contestDataPost['start-timestamp'] = $start_front;


    /* get complete time */
    $string_time = $contestDataPost['end-date'] . " " . $contestDataPost['end-time-hour'] . ":" . $contestDataPost['end-time-minute'] . ":00 " . $contestDataPost['end-time-meridian'];
    $end_front = strtotime($string_time);
    $contestDataPost['end-timestamp'] = $end_front;

    $contestDataPost['contest_thank_you'] = $_POST['contest_thank_you'];

    update_post_meta($contest_id, '_contest_beast_post_meta', $contestDataPost);


    /* if (isset($_POST['contest_mail_list'])){
        update_post_meta( $contest_id , 'contest_mail_list',$_POST['contest_mail_list']);
    }
     */
    echo var_dump($_POST);


    //delete_post_meta($contest_id);

    exit;

}

function my_login_redirect($redirect_to, $request, $user)
{
    //is there a user to check?
    if (isset($user->roles) && is_array($user->roles)) {
        //check for admins
        if (in_array('administrator', $user->roles)) {
            // redirect them to the default place
            return $redirect_to;
        } else {
            return home_url('/my-account/dashboard/');
        }
    } else {
        return $redirect_to;
    }
}


add_filter('login_redirect', 'my_login_redirect', 10, 3);


function wpse_253580_prevent_author_access()
{

    $user_id = get_current_user_id();

    $user_info = get_userdata($user_id);
    $res = implode(', ', $user_info->roles);

    /*  echo 'Username: ' . $user_info->user_login . "\n";
     echo 'User roles: ' . implode(', ', $user_info->roles) . "\n";
     echo 'User ID: ' . $user_info->ID . "\n"; */

    if ($res == 'editor') {

        //echo "<script> window.location='".home_url('/my-account/dashboard/')."'</script>";
    }

    /*  if( current_user_can( 'author' ) )  {
         // do something here. maybe redirect to homepage
         wp_redirect( get_bloginfo( '/my-account/dashboard/' ) );
     } */
}

add_action('admin_init', 'wpse_253580_prevent_author_access');


add_action('wp_ajax_verify_aweber', 'mailing_verify_aweber');
function mailing_verify_aweber()
{
    error_reporting(E_ALL); ini_set('display_errors', 1);
    global $current_user,$wpdb;
    if (isset($_POST) && isset($_POST['_aweber_auth_code']) && !empty($_POST['_aweber_auth_code'])) {
        $authCode = explode('|', $_POST['_aweber_auth_code']);
        $authKeys = array(
            CONTEST_PLUGIN_NAME . '_aweber_consumer_key',
            CONTEST_PLUGIN_NAME . '_aweber_consumer_secret',
            CONTEST_PLUGIN_NAME . '_aweber_request_token',
            CONTEST_PLUGIN_NAME . '_aweber_token_secret',
            CONTEST_PLUGIN_NAME . '_aweber_verifier'
        );
        foreach ($authCode as $key => $value) {
            if (!empty($authKeys[$key])) {
                $auth[$authKeys[$key]] = $value;
            }
        }

        require(CONTEST_LIB_DIR . 'aweber/src/aweber_api/aweber_api.php');
        try{
            # create new instance of AWeberAPI
            $application = new AWeberAPI($auth[CONTEST_PLUGIN_NAME . '_aweber_consumer_key'], $auth[CONTEST_PLUGIN_NAME . '_aweber_consumer_secret']);
            
            # exchange request token + verifier code for an access token
            $application->user->requestToken = $auth[CONTEST_PLUGIN_NAME . '_aweber_request_token'];
            $application->user->tokenSecret = $auth[CONTEST_PLUGIN_NAME . '_aweber_token_secret'];
            $application->user->verifier = $auth[CONTEST_PLUGIN_NAME . '_aweber_verifier'];

            list($auth[CONTEST_PLUGIN_NAME . '_aweber_access_token'], $auth[CONTEST_PLUGIN_NAME . '_aweber_access_secret']) = $application->getAccessToken();

            $account = $application->getAccount($auth[CONTEST_PLUGIN_NAME . '_aweber_access_token'], $auth[CONTEST_PLUGIN_NAME . '_aweber_access_secret']);

            $auth[CONTEST_PLUGIN_NAME . '_aweber_account_id'] = $account->id;

            $listId = $account->lists->find(array('name' => $_POST['_aweber_list_name']));

            $auth[CONTEST_PLUGIN_NAME . '_aweber_list_id'] = $listId->data['entries'][0]['id'];
            
            $wpdb->query("DELETE FROM {$wpdb->prefix}usermeta WHERE meta_key like '%_aweber_%' AND user_id={$current_user->ID}");
            $auth['_aweber_auth_code'] = $_POST['_aweber_auth_code'];
            $auth['_aweber_list_name'] = $_POST['_aweber_list_name'];
            foreach ($auth as $key => $value) {
                add_user_meta($current_user->ID, $key, $value);
            }
            echo '1';
        }catch(Exception $e){
            echo '0';
        }
    }
    exit();
}

add_action('wp_ajax_save_mailchimp', 'mailing_save_mailchimp');

function mailing_save_mailchimp()
{
    global $current_user;


    include('mc/src/MailChimp.php');
    include('mc/src/Batch.php');
    include('mc/src/Webhook.php');
    $MailChimp = new MailChimp($_POST['mailchimp']);
    $lists = $MailChimp->get('lists');

    if (count($lists['lists']) > 1) {

        foreach ($lists['lists'] as $lst_key => $lst_val) {
            $record_mc[$lst_key]['list_id'] = $lst_val['id'];
            $record_mc[$lst_key]['list_name'] = $lst_val['name'];
        }


    } else {
        $record_mc[0]['list_id'] = $lists['lists'][0]['id'];
        $record_mc[0]['list_name'] = $lists['lists'][0]['name'];

    }

    get_currentuserinfo();

    add_user_meta($current_user->ID, '_mailchimApi_api', $_POST['mailchimp']);
    add_user_meta($current_user->ID, '_mailchimApi_list', $record_mc);
    echo var_dump($record_mc);
    exit;

}

;

add_action('wp_ajax_save_getresponse', 'mailing_save_getresponse');

function mailing_save_getresponse()
{
    global $current_user;


    require_once('getresponse/GetResponseAPI3.class.php');

    $getresponse = new GetResponse($_POST['getresponse']);
    $getresponse->enterprise_domain = 'staging.contestbuzz.net';


    $gr_account = $getresponse->accounts();

    //echo "sdfasdf".$gr_account->accountId."".var_dump($gr_account);
    //print_r($resultgr);


    $lists = (array)$getresponse->getCampaigns();


    if (count($lists) > 1) {

        foreach ($lists as $lst_key => $lst_val) {
            $record_mc[$lst_key]['list_id'] = $lst_val->campaignId;
            $record_mc[$lst_key]['list_name'] = $lst_val->name;
        }


    } else {
        $record_mc[0]['list_id'] = $lists[0]->campaignId;
        $record_mc[0]['list_name'] = $lists[0]->name;

    }

    get_currentuserinfo();

    add_user_meta($current_user->ID, '_getreponseApi_api', $_POST['getresponse']);
    add_user_meta($current_user->ID, '_getreponseApi_list', $record_mc);
    echo var_dump($record_mc);
    exit;

}

;


function mailing_submit_mailchimp($submitted)
{

    global $current_user;
    get_currentuserinfo();

    include('mc/src/MailChimp.php');
    include('mc/src/Batch.php');
    include('mc/src/Webhook.php');

    $mailchimp = get_user_meta($current_user->ID, '_mailchimApi_api', true);

    $MailChimp = new MailChimp($mailchimp);

    $list_id = $submitted['list_id'];

    $result = $MailChimp->post("lists/$list_id/members", [
        'email_address' => $submitted['email'],
        'status' => 'subscribed',
    ]);

    return true;
}

;


add_action('wp_ajax_verify_mailchimp', 'mailing_verify_mailchimp');

function mailing_verify_mailchimp()
{
    include('mc/src/MailChimp.php');
    include('mc/src/Batch.php');
    include('mc/src/Webhook.php');

    $MailChimp = new MailChimp($_POST['mcapi']);
    $mc_account = $MailChimp->apiKeyValidate();

    if (isset($mc_account['login_id'])) {

        $lists = $MailChimp->get('lists');
        echo 'You have ' . count($lists['lists']) . " lists to your MailChimp account.";


        echo "<div style=\"padding:10px 0px;\" id=\"mailchimprest\">
		<table class=\"table table-bordered\" style=\"width:320px !important\">";
        echo '<thead>';

        echo '<th>List Id</th>';
        echo '<th>List Name</th>';
        echo '</thead>';
        if (count($lists['lists']) > 1) {
            $h = 0;
            foreach ($lists['lists'] as $lst_key => $lst_val) {
                echo '<tr>';
                echo '<td><input type="hidden" name="mc_list[' . $h . ']" value="' . $lst_val['id'] . '">' . $lst_val['id'] . '</td>';
                echo '<td><input type="hidden" name="mc_name[' . $h . ']" value="' . $lst_val['name'] . '">' . $lst_val['name'] . '</td>';
                echo '</tr>';
                $h++;
            }

        } else {
            echo '<tr>';
            echo '<td><input type="hidden" name="mc_list[0]" value="' . $lists['lists'][0]['id'] . '">' . $lists['lists'][0]['id'] . '</td>';
            echo '<td><input type="hidden" name="mc_name[0]" value="' . $lists['lists'][0]['name'] . '">' . $lists['lists'][0]['name'] . '</td>';
            echo '</tr>';
        }

        echo "</table>";
        echo '<div style="padding:15px 0px 5px">
		<button class="btn btn-info" id="mailchimp_save">Save</button>
		<button class="btn btn-defualt">Cancel</button></div>
		</div>';
        //print_r($lists);
    }

    exit;

}


add_action('wp_ajax_verify_getresponse', 'mailing_verify_getresponse');

function mailing_verify_getresponse()
{
    require_once('getresponse/GetResponseAPI3.class.php');

    $getresponse = new GetResponse($_POST['grapi']);
    $getresponse->enterprise_domain = 'staging.contestbuzz.net';


    $gr_account = $getresponse->accounts();

    //echo "sdfasdf".$gr_account->accountId."".var_dump($gr_account);
    //print_r($resultgr);

    if (isset($gr_account->accountId)) {


        $lists = (array)$getresponse->getCampaigns();

        echo 'You have ' . count($lists) . " campaigns to your getRseponse account.";

        //print_r($lists);

        echo "<div style=\"padding:10px 0px;\" id=\"gtprest\">
		<table class=\"table table-bordered\" style=\"width:320px !important\">";
        echo '<thead>';

        echo '<th>Campaign Id</th>';
        echo '<th>Campaign Name</th>';
        echo '</thead>';
        if (count($lists) > 1) {
            $h = 0;
            foreach ($lists as $lst_key => $lst_val) {
                echo '<tr>';
                echo '<td><input type="hidden" name="gr_list[' . $h . ']" value="' . $lst_val->campaignId . '">' . $lst_val->campaignId . '</td>';
                echo '<td><input type="hidden" name="gr_list[' . $h . ']" value="' . $lst_val->name . '">' . $lst_val->name . '</td>';
                echo '</tr>';
                $h++;
            }

        } else {
            echo '<tr>';
            echo '<td><input type="hidden" name="gr_list[0]" value="' . $lists[0]->campaignId . '">' . $lists[0]->campaignId . '</td>';
            echo '<td><input type="hidden" name="gr_list[0]" value="' . $lists[0]->name . '">' . $lists[0]->name . '</td>';
            echo '</tr>';
        }

        echo "</table>";
        echo '<div style="padding:15px 0px 5px">
		<button class="btn btn-info" id="getreponse_save">Save</button>
		<button class="btn btn-defualt">Cancel</button></div>
		</div>';
        //print_r($lists);

    }

    exit;

}

add_action('wp_ajax_gen_contest_url', 'labdevs_gen_contest_url');
function labdevs_gen_contest_url()
{
    $post_name = sanitize_title($_POST['title']);
    $post_name = wp_unique_post_slug($post_name, 0, 'publish', 'contest', 0);
    echo $post_name;
    exit;
}

add_action('wp_ajax_check_contest_url', 'labdevs_check_contest_url');
function labdevs_check_contest_url()
{
    $sample = wp_unique_post_slug($_POST['post_name'], intval($_POST['post_id']), 'publish', 'contest', 0);
    echo ($sample == $_POST['post_name']) ? 'Yes' : $sample;
    exit;
}

add_action('wp_ajax_upload_image', 'labdevs_upload_image');
function labdevs_upload_image()
{
    if (!function_exists('wp_handle_upload')) require_once(ABSPATH . 'wp-admin/includes/file.php');
    $success = false;
    $upload_overrides = array('test_form' => false);
    foreach ($_FILES as $uploadedfile) {
        $movefile = wp_handle_upload($uploadedfile, $upload_overrides);
        if ($movefile) {
            $success = true;
            echo json_encode($movefile);
        }
    }
    if (!$success) echo 0;
    exit();
}

/* 
add_action( 'wp_ajax_upload_logo', 'labdevs_upload_logo' );
function labdevs_upload_logo() {
	if ( ! function_exists( 'wp_handle_upload' ) ) require_once( ABSPATH . 'wp-admin/includes/file.php' );
	$uploadedfile = $_FILES['logo'];
	$upload_overrides = array( 'test_form' => false );
	$movefile = wp_handle_upload( $uploadedfile, $upload_overrides );
	if ( $movefile ) {
		echo json_encode($movefile);
	} else {
		echo 0;
	}
	exit();
}

add_action( 'wp_ajax_upload_button_img', 'labdevs_upload_button_img' );
function labdevs_upload_button_img() {
	if ( ! function_exists( 'wp_handle_upload' ) ) require_once( ABSPATH . 'wp-admin/includes/file.php' );
	$uploadedfile = $_FILES['button_img'];
	$upload_overrides = array( 'test_form' => false );
	$movefile = wp_handle_upload( $uploadedfile, $upload_overrides );
	if ( $movefile ) {
		echo json_encode($movefile);
	} else {
		echo 0;
	}
	exit();
}

add_action( 'wp_ajax_upload_background_image', 'labdevs_upload_background_image' );
function labdevs_upload_background_image() {
	if ( ! function_exists( 'wp_handle_upload' ) ) require_once( ABSPATH . 'wp-admin/includes/file.php' );
	$uploadedfile = $_FILES['background_image'];
	$upload_overrides = array( 'test_form' => false );
	$movefile = wp_handle_upload( $uploadedfile, $upload_overrides );
	if ( $movefile ) {
		echo json_encode($movefile);
	} else {
		echo 0;
	}
	exit();
}

 */


add_action('contest_header', 'labdevs_contest_header');
function labdevs_contest_header()
{
    global $post;
    $items = get_post_meta($post->ID, '_contest_beast_post_meta', true);

    $body_background_color = $items['body_background_color'];
    $body_background_image = $items['body_background_image'];

    /* $contest_background_color = $items['contest_background_color']; */
    $contest_background_image = $items['contest_background_image'];

    $button_tpl = $items['button_tpl'];
    $button_img = $items['button_img'];

    echo '<style type="text/css">.contest-description, .contest-title, .contest-instructions {text-shadow: none}';
    if ($contest_background_color) echo '#contest-container {background-color: #' . $contest_background_color . ';}';
    if ($contest_background_image) echo '#contest-container {background-image: url("' . $contest_background_image . '");}';
    if ($contest_background_color && !$contest_background_image) echo '#contest-container {background-image: none !important;}';

    if ($body_background_color) echo 'body {background-color: #' . $body_background_color . ';}';
    if ($body_background_image) echo 'body {background-image: url("' . $body_background_image . '");}';
    if ($body_background_color && !$body_background_image) echo 'body {background-image: none !important;}';

    if ($button_img) {
        echo '.contest-entry-field-submit {background-image:url("' . $button_img . '");}';
    } elseif ($button_tpl) {
        echo '.contest-entry-field-submit {background-image:url("' . $button_tpl . '");background-size: contain;}';
    }

    echo '</style>';
}

add_action('wp_ajax_labdevs_settings_update', 'labdevs_settings_update_callback');
function labdevs_settings_update_callback()
{
    $params = array();
    parse_str($_POST['data'], $params);
    foreach ($params as $k => $v) {

        update_option($k, $v);
    }
}

add_action('wp_ajax_labdevs_spy_link_counter', 'labdevs_spy_link_counter_callback');
add_action('wp_ajax_nopriv_labdevs_spy_link_counter', 'labdevs_spy_link_counter_callback');
function labdevs_spy_link_counter_callback()
{
    echo labdevs_get_current_author_number_of_custom_posts('teeoptimize');
    exit;
}

function labdevs_get_current_author_number_of_custom_posts($custom_post)
{
    $posts = new WP_Query();
    $posts->query(array(
        'posts_per_page' => -1,
        'author' => labdevs_get_user_current_id(),
        'post_type' => $custom_post
    ));
    wp_reset_postdata();
    return sizeof($posts->posts);
}

function my_custom_menu_page()
{
    ?>
    <script type="text/javascript">
        function labdevs_show_settings_saved() {
            jQuery('.labdevs_small_success').fadeIn(500);
            setTimeout(function () {
                jQuery('.labdevs_small_success').fadeOut(500);
            }, 5000);
        }
    </script>
    <div class="labdevs_small_success"
         style="position: fixed; top: 40px; left: 0px; z-index: 100000; width: 100%; padding: 10px; text-align: center; height: auto; display: none;">
    <span style="
    color: white;
    background-color: #80C751;
    padding: 10px 30px;
    border-radius: 9px;
    font-family: monospace;
    line-height: 1em;
">Settings Saved!</span>
    </div>
    <div class="wrap">
        <h2>Short URL</h2>
        <script type="text/javascript">
            jQuery(document).ready(function ($) {
                jQuery('.labdevs_settings_button_save_changes').click(function () {
                    var key = jQuery(this).attr('id');
                    var value = jQuery(this).val();
                    $.ajax({
                        type: "POST",
                        url: "<?php echo admin_url('admin-ajax.php'); ?>",
                        data: {
                            action: 'labdevs_settings_update',
                            data: jQuery('.the_settings').serialize()
                        }
                    }).done(function (data) {
                        console.log(data);
                        labdevs_show_settings_saved();
                    });

                });

            });
        </script>
        <form class="the_settings">
            <table class="form-table">

                <tbody>

                <?php $i = 0; ?>

                <tr valign="top">
                    <th scope="row"><label for="labdevs_settings_option_short_url">Short URL</label></th>
                    <td>
                        <input name="labdevs_settings_option_short_url" type="text"
                               value="<?php echo get_option("labdevs_settings_option_short_url"); ?>"
                               class="regular-text">
                        <p class="description">Write URL that needs to be treated as prefix. For example:
                            http://spyreach.com/r/</p>
                    </td>
                </tr>


                </tbody>
            </table>
            <p class="submit">
                <input type="button" name="submit" class="labdevs_settings_button_save_changes button button-primary"
                       value="Save Changes">
            </p>

        </form>
    </div>
    <?php
}

/*
function restrict_admin() {
    if ( ! current_user_can( 'update_core' ) ) {
        wp_redirect( home_url() );
        exit;
    }
}
add_action( 'admin_init', 'restrict_admin', 1 );
*/

/*
add_action('admin_init', 'redirect_user_to_member_dashboard');
function redirect_user_to_member_dashboard() {
	if( is_page( 172 ) ) {
	//if( is_page( 'SpyReach Member Login' ) ) {
		//if( is_user_logged_in() && ! current_user_can('update_core') ) {
			wp_redirect( home_url() );
			exit;
		//}
	}
}
*/


add_action('wp_ajax_labdevs_increment_hits', 'labdevs_increment_hits_callback');
add_action('wp_ajax_nopriv_labdevs_increment_hits', 'labdevs_increment_hits_callback');
function labdevs_increment_hits_callback()
{

    $key = $_POST['key'];
    if ($key == 'aabX24kd*9#da!df;i*4ln;lk') {
        $id = $_POST['id'];
        $hits = intval(get_post_meta($id, 'labdevs_hit_counter', true));
        $unique_hits = intval(get_post_meta($id, 'labdevs_unique_hit_counter', true));

        if (empty($hits)) {
            $hits = 0;
        }
        $hits = $hits + 1;
        if (empty($unique_hits)) {
            $unique_hits = 0;
        }
        //echo 'unique:' . $_POST['unique'] . ':';
        if ($_POST['unique'] === '1') {
            $unique_hits = $unique_hits + 1;
        }
        update_post_meta($id, 'labdevs_hit_counter', $hits);
        update_post_meta($id, 'labdevs_unique_hit_counter', $unique_hits);

    }

}

add_action('wp_ajax_labdevs_create_spy_link', 'labdevs_create_spy_link_callback');
function labdevs_create_spy_link_callback()
{

    $user_id = get_current_user_id();

    $my_post = array(
        'post_title' => wp_strip_all_tags($_POST['contest_title']),
        'post_content' => $_POST['post_content'],
        'contest_video' => $_POST['contest_video'],
        'post_name' => $_POST['post_name'],
        'post_type' => 'contest',
        'post_author' => $user_id,
        'post_status' => 'publish'
    );

    // Insert the post into the database
    $id = wp_insert_post($my_post);

    $my_contest_start = get_post_meta($id, '_contest_beast_post_meta', true);
    $my_contest_start['start-date'] = $_POST['start_date'];
    $my_contest_start['start-time-hour'] = $_POST['start_hour'];
    $my_contest_start['start-time-minute'] = $_POST['start_minute'];
    $my_contest_start['start-time-meridian'] = $_POST['start_meridian'];

    //$date = str_replace('/', '-', $_POST['start_date']);
    //$date_start =  date('Y-m-d', strtotime($date));
    //date("F j, Y, g:i a");
    $mystart = $_POST['start_date'] . " " . $_POST['start_hour'] . ":" . $_POST['start_minute'] . ":00 " . $_POST['start_meridian'];

    $start_front = strtotime($mystart);

    $my_contest_start['start-timestamp'] = $start_front;
    //print_r($my_contest_cus);die();
    update_post_meta($id, '_contest_beast_post_meta', $my_contest_start);


    $my_contest_end = get_post_meta($id, '_contest_beast_post_meta', true);
    $my_contest_end['end-date'] = $_POST['end_date'];
    $my_contest_end['end-time-hour'] = $_POST['end_hour'];
    $my_contest_end['end-time-minute'] = $_POST['end_minute'];
    $my_contest_end['end-time-meridian'] = $_POST['end_meridian'];

    //$date = str_replace('/', '-', $_POST['start_date']);
    //$date_start =  date('Y-m-d', strtotime($date));
    //date("F j, Y, g:i a");
    $myend = $_POST['end_date'] . " " . $_POST['end_hour'] . ":" . $_POST['end_minute'] . ":00 " . $_POST['end_meridian'];

    $end_front = strtotime($myend);

    $my_contest_end['end-timestamp'] = $end_front;
    //print_r($my_contest_cus);die();
    update_post_meta($id, '_contest_beast_post_meta', $my_contest_end);

    if (isset($_POST['contest_rules'])) {
        $my_contest_settings = get_post_meta($id, '_contest_beast_post_meta', true);
        $my_contest_settings['contest-rules-custom'] = 'yes';
        $my_contest_settings['contest-rules'] = $_POST['rules'];
        update_post_meta($id, '_contest_beast_post_meta', $my_contest_settings);
    } else {
        $my_contest_settings = get_post_meta($id, '_contest_beast_post_meta', true);
        $my_contest_settings['contest-rules-custom'] = 'no';
        $my_contest_settings['contest-rules'] = $_POST['rules'];
        update_post_meta($id, '_contest_beast_post_meta', $my_contest_settings);
    }

    //tracking 4/5/2017 morning
    if (isset($_POST['tracking-scripts'])) {
        $my_contest_settings = get_post_meta($id, '_contest_beast_post_meta', true);
        $my_contest_settings['tracking-scripts-custom'] = 'yes';
        $my_contest_settings['tracking-scripts'] = $_POST['scripts'];
        update_post_meta($id, '_contest_beast_post_meta', $my_contest_settings);
    } else {
        $my_contest_settings = get_post_meta($id, '_contest_beast_post_meta', true);
        $my_contest_settings['tracking-scripts-custom'] = 'no';
        $my_contest_settings['tracking-scripts'] = $_POST['scripts'];
        update_post_meta($id, '_contest_beast_post_meta', $my_contest_settings);
    }


    $my_contest_settings = get_post_meta($id, '_contest_beast_post_meta', true);
    $my_contest_settings['number-entries'] = $_POST['referral'];
    $my_contest_settings['number-winners'] = $_POST['winners'];
    $my_contest_settings['contest-disclaimer'] = $_POST['disclaimer'];
    $my_contest_settings['contest-termsofservice'] = $_POST['termsofservice'];
    $my_contest_settings['contest-privacypolicybox'] = $_POST['privacypolicybox'];
    $my_contest_settings['contest-rules-custom'] = $_POST['contest_rules'];
    $my_contest_settings['contest-rules'] = $_POST['rules'];
    $my_contest_settings['tracking-scripts-custom'] = $_POST['custom_scripts'];
    $my_contest_settings['tracking-scripts'] = $_POST['scripts'];
    $my_contest_settings['contest_video'] = $_POST['contest_video']; //added 4/17/2017
    $my_contest_settings['add-to-mailing-list'] = $_POST['custom_mailing'];
    $my_contest_settings['mailing-list-list'] = $_POST['mailing'];
    $my_contest_settings['mailing-list-data'] = $_POST['mailing_data'];
    $my_contest_settings['url_prefix'] = $_POST['url_prefix'];
    $my_contest_settings['branding-logo-url'] = $_POST['logo'];
    $my_contest_settings['mc-template'] = $_POST['mc_template'];
    $my_contest_settings['youtube_video'] = $_POST['youtube_video'];
    $my_contest_settings['user_scripts'] = $_POST['user_scripts'];

    //new added 4/6/17
    //$my_contest_settings['custom_css_class'] = $_POST['custom_css_class'];
    //$my_contest_settings['custom_css_desc'] = $_POST['custom_css_desc'];

    $my_contest_settings['body_background_color'] = $_POST['body_background_color'];
    $my_contest_settings['body_background_image'] = $_POST['body_background_image'];
    $my_contest_settings['contest_background_color'] = $_POST['contest_background_color'];
    $my_contest_settings['contest_background_image'] = $_POST['contest_background_image'];
    //$my_contest_settings['picture_description'] = $_POST['picture_description']; //add picture description dated april 4, 2017
    $my_contest_settings['button_tpl'] = $_POST['button_tpl'];
    $my_contest_settings['button_img'] = $_POST['button_img'];


    update_post_meta($id, '_contest_beast_post_meta', $my_contest_settings);

    //featured_image
    //$_POST['featured_image'] =  url1, url2 etcc....
    // do explode
    //$dfasd = explode("," $_POST['featured_image']); this would be output array
    //$dfasd[0]
    //foreach

    Generate_Featured_Image($_POST['featured_image'], $id, 'Contest Featured Image');

    //return var_dump($_FILES);
    echo var_dump($_POST);

}

function Generate_Featured_Image($file, $post_id, $desc)
{
    // Set variables for storage, fix file filename for query strings.
    preg_match('/[^\?]+\.(jpe?g|jpe|gif|png)\b/i', $file, $matches);
    if (!$matches) {
        return new WP_Error('image_sideload_failed', __('Invalid image URL'));
    }

    $file_array = array();
    $file_array['name'] = basename($matches[0]);

    // Download file to temp location.
    $file_array['tmp_name'] = download_url($file);

    // If error storing temporarily, return the error.
    if (is_wp_error($file_array['tmp_name'])) {
        return $file_array['tmp_name'];
    }

    // Do the validation and storage stuff.
    $id = media_handle_sideload($file_array, $post_id, $desc);

    // If error storing permanently, unlink.
    if (is_wp_error($id)) {
        @unlink($file_array['tmp_name']);
        return $id;
    }
    return set_post_thumbnail($post_id, $id);

}

add_action('wp_ajax_labdevs_rt_cookie_post', 'labdevs_rt_cookie_post_callback');
function labdevs_rt_cookie_post_callback()
{

    $my_post = array(
        'post_title' => $_POST['rt_cookie_title'],
        'post_type' => 'contest',
        'post_status' => 'publish',
        'post_author' => labdevs_get_user_current_id()
    );

    // Insert the post into the database
    $id = wp_insert_post($my_post);
    //echo '$id:'.$id;
    $meta_key = 'rt_cookie_content';
    $meta_value = $_POST['rt_cookie_content'];

    update_post_meta($id, $meta_key, $meta_value);
    update_post_meta($id, 'rt_cookie_description', $_POST['rt_cookie_description']);

}

add_action('wp_ajax_labdevs_spy_link_update', 'labdevs_spy_link_update_callback');
function labdevs_spy_link_update_callback()
{

    $my_post = array(
        'ID' => $_POST['spy_link_id'],
        'post_title' => $_POST['spy_link_name'],
        'post_name' => $_POST['post_name'],
        'post_content' => $_POST['post_content'],
        'user_scripts' => $_POST['user_scripts'],
        'contest_video' => $_POST['contest_video'],
        'post_name' => $_POST['spy_url']
    );

    $id = $_POST['spy_link_id'];
    wp_update_post($my_post);

    $my_contest_start = get_post_meta($id, '_contest_beast_post_meta', true);
    $my_contest_start['start-date'] = $_POST['start_date'];
    $my_contest_start['start-time-hour'] = $_POST['start_hour'];
    $my_contest_start['start-time-minute'] = $_POST['start_minute'];
    $my_contest_start['start-time-meridian'] = $_POST['start_meridian'];

    //$date = str_replace('/', '-', $_POST['start_date']);
    //$date_start =  date('Y-m-d', strtotime($date));
    //date("F j, Y, g:i a");
    $mystart = $_POST['start_date'] . " " . $_POST['start_hour'] . ":" . $_POST['start_minute'] . ":00 " . $_POST['start_meridian'];

    $start_front = strtotime($mystart);

    $my_contest_start['start-timestamp'] = $start_front;
    //print_r($my_contest_cus);die();
    update_post_meta($id, '_contest_beast_post_meta', $my_contest_start);


    $my_contest_end = get_post_meta($id, '_contest_beast_post_meta', true);
    $my_contest_end['end-date'] = $_POST['end_date'];
    $my_contest_end['end-time-hour'] = $_POST['end_hour'];
    $my_contest_end['end-time-minute'] = $_POST['end_minute'];
    $my_contest_end['end-time-meridian'] = $_POST['end_meridian'];

    //$date = str_replace('/', '-', $_POST['start_date']);
    //$date_start =  date('Y-m-d', strtotime($date));
    //date("F j, Y, g:i a");
    $myend = $_POST['end_date'] . " " . $_POST['end_hour'] . ":" . $_POST['end_minute'] . ":00 " . $_POST['end_meridian'];

    $end_front = strtotime($myend);

    $my_contest_end['end-timestamp'] = $end_front;
    //print_r($my_contest_cus);die();
    update_post_meta($id, '_contest_beast_post_meta', $my_contest_end);


    $my_contest_settings = get_post_meta($id, '_contest_beast_post_meta', true);
    $my_contest_settings['number-entries'] = $_POST['referral'];
    $my_contest_settings['number-winners'] = $_POST['winners'];
    $my_contest_settings['contest-disclaimer'] = $_POST['disclaimer'];
    $my_contest_settings['contest-termsofservice'] = $_POST['termsofservice'];
    $my_contest_settings['contest-privacypolicybox'] = $_POST['privacypolicybox'];
    $my_contest_settings['contest-rules-custom'] = $_POST['contest_rules'];
    $my_contest_settings['contest-rules'] = $_POST['rules'];
    $my_contest_settings['tracking-scripts-custom'] = $_POST['custom_scripts'];
    $my_contest_settings['tracking-scripts'] = $_POST['scripts'];
    $my_contest_settings['contest_video'] = $_POST['contest_video']; //added 4/17/2017
    $my_contest_settings['add-to-mailing-list'] = $_POST['custom_mailing'];
    $my_contest_settings['mailing-list-list'] = $_POST['mailing'];
    $my_contest_settings['mailing-list-data'] = $_POST['mailing_data'];
    $my_contest_settings['url_prefix'] = $_POST['url_prefix'];
    $my_contest_settings['branding-logo-url'] = $_POST['logo'];
    $my_contest_settings['mc-template'] = $_POST['mc_template'];
    $my_contest_settings['youtube_video'] = $_POST['youtube_video'];
    $my_contest_settings['user_scripts'] = $_POST['user_scripts'];

    //new added 4/6/17
    //$my_contest_settings['custom_css_class'] = $_POST['custom_css_class'];
    //$my_contest_settings['custom_css_desc'] = $_POST['custom_css_desc'];

    $my_contest_settings['body_background_color'] = $_POST['body_background_color'];
    $my_contest_settings['body_background_image'] = $_POST['body_background_image'];
    $my_contest_settings['contest_background_color'] = $_POST['contest_background_color'];
    $my_contest_settings['contest_background_image'] = $_POST['contest_background_image'];
    $my_contest_settings['button_tpl'] = $_POST['button_tpl'];
    $my_contest_settings['button_img'] = $_POST['button_img'];

    update_post_meta($id, '_contest_beast_post_meta', $my_contest_settings);


}

add_action('wp_ajax_labdevs_rt_cookie_update', 'labdevs_rt_cookie_update_callback');
function labdevs_rt_cookie_update_callback()
{
    // Create post object
    global $current_user;
    get_currentuserinfo();

    $my_post = array(
        'ID' => $_POST['rt_cookie_id'],
        'post_title' => $_POST['rt_cookie_title'],
    );

    $id = $_POST['rt_cookie_id'];
    $meta_key = 'rt_cookie_content';
    $meta_value = $_POST['rt_cookie_content'];


    wp_update_post($my_post);
    update_post_meta($id, $meta_key, $meta_value);
    update_post_meta($id, 'rt_cookie_description', $_POST['rt_cookie_description']);
}

add_action('wp_ajax_labdevs_my_rt_cookies_table', 'labdevs_my_rt_cookies_table_callback');
function labdevs_my_rt_cookies_table_callback()
{

    /* ----------------------------------------------------- */
    /* WP_QUERY - THE LOOP  RT COOKIE */
    /* ----------------------------------------------------- */

    /* arguments */
    $args = array(
        'pagination' => true,
        'posts_per_page' => '-1',
        'author' => labdevs_get_user_current_id(),
        'post_type' => 'labdevsrtcookie',
    );

    /* query */
    $home_query = new WP_Query($args);

    while ($home_query->have_posts()) {
        $home_query->the_post();

        /*
         * DISPLAYING FUNCTIONS
         * ====================
         * Title => the_title();
         * Permalink => the_permalink();
         * Content => the_content();
         *
         */
        ?>

        <tr>
            <td><?php the_title(); ?></td>
            <td style="width:300px;"><?php echo htmlspecialchars(get_post_meta(get_the_ID(), 'rt_cookie_content', true)); ?></td>
            <td><?php echo get_post_meta(get_the_ID(), 'rt_cookie_description', true); ?></td>
            <td>
                <button data-description="<?php echo get_post_meta(get_the_ID(), 'rt_cookie_description', true); ?>"
                        data-content="<?php echo htmlspecialchars(get_post_meta(get_the_ID(), 'rt_cookie_content', true)); ?>"
                        data-title="<?php the_title(); ?>" data-id="<?php the_ID(); ?>"
                        class="update_rt_cookie_button btn btn-default btn-sm"><span
                            class="glyphicon glyphicon-edit"></span></button>
                <button data-id="<?php the_ID(); ?>" class="delete_rt_cookie_button btn btn-default btn-sm"><span
                            class="glyphicon glyphicon-trash"></span></button>
            </td>
        </tr>

        <?php
    } // while while posts
    /* restore the global $post variable of the main query loop after a secondary query loop using new WP_Query */
    wp_reset_postdata();
}

add_action('wp_ajax_get_post_info', 'do_get_post_info');
function do_get_post_info()
{

    echo $_POST['postid'];
    //tytle, des etcc..
}

add_action('wp_ajax_labdevs_spy_link_table', 'labdevs_spy_link_table_callback');
function labdevs_spy_link_table_callback()
{

    /* ----------------------------------------------------- */
    /* WP_QUERY - THE LOOP */ /* SPY LINK LOOP */
    /* ----------------------------------------------------- */

    /* arguments */
    $args = array(
        'pagination' => true,
        'posts_per_page' => '-1',
        'author' => labdevs_get_user_current_id(),
        'post_type' => 'contest',
    );

    /* query */
    $home_query = new WP_Query($args);

    while ($home_query->have_posts()) {
        $home_query->the_post();

        /*
         * DISPLAYING FUNCTIONS
         * ====================
         * Title => the_title();
         * Permalink => the_permalink();
         * Content => the_content();
         *
         */
        $tmp = get_permalink();
        $tmp = split('/r/', $tmp);
        $permalink = $tmp[1];
        ?>

        <tr>

            <td style="width:130px; vertical-align:middle; text-align:left;"><?php the_title(); ?></td>

            <td style="width:100px; vertical-align:middle; text-align:center;"><?php echo get_the_time('Y-m-d', $post->ID); ?></td>

            <td style="width:130px; vertical-align:middle;">
                <?php contest_beast_the_contest_start_date(); ?>
            </td>

            <td style="width:130px; vertical-align:middle;"><?php contest_beast_the_contest_end_date(); ?>

            </td>

            <td style="width:80px; vertical-align:middle;"><?php contest_beast_the_contest_number_submissions($post_id); ?><?php $nonce_url = add_query_arg(array('export-contest-beast-entries-nonce' => wp_create_nonce("export-contest-beast-entries-{$post_id}"), 'ID' => $post_id), admin_url('admin.php'));

                printf('<br /><a href="%s">%s</a>', $nonce_url, __('Export Entrants')); ?></td>
            <td style="width:80px; vertical-align:middle;"><?php contest_beast_the_contest_number_entries($post_id); ?></td>
            <td style="width:60px; vertical-align:middle;"><?php contest_beast_the_contest_number_winners($post_id); ?></td>
            <td style="width:120px; vertical-align:middle;"><?php $winners = contest_beast_get_contest_winners($post_id); ?>
                <?php //if (!contest_beast_has_ended($post_id)) {

                //_e('N/A');

                //} else {

                //self::display_contest_winners_markup($post_id);

                //}
                ?>
            </td>
            <td style=" vertical-align:middle; width:150px;">

                <input type="hidden" value="<?php the_permalink(); ?>" class="spy_link_permalink"/>

                <button class="the_clipboard btn btn-default btn-sm"><span class="glyphicon glyphicon-paperclip"></span>
                </button>

                <a href="<?php the_permalink(); ?>" role="button" class="btn btn-default btn-sm"><span
                            class="glyphicon glyphicon-eye-open"></span></a>

                <?php $items = get_post_meta(get_the_ID(), '_contest_beast_post_meta', true); ?>
                <button

                        data-id="<?php the_ID(); ?>"

                        data-title="<?php the_title(); ?>"

                        data-content="<?php echo $post->post_content; ?> "

                        data-start="<?php echo $items["start-date"]; ?>"

                        data-hour="<?php echo $items["start-time-hour"]; ?>"

                        data-minute="<?php echo $items["start-time-minute"]; ?>"

                        data-meridian="<?php echo $items["start-time-meridian"]; ?>"

                        data-end="<?php echo $items["end-date"]; ?>"

                        data-endhour="<?php echo $items["end-time-hour"]; ?>"

                        data-endminute="<?php echo $items["end-time-minute"]; ?>"

                        data-endmeridian="<?php echo $items["end-time-meridian"]; ?>"

                        data-referral="<?php echo $items["number-entries"]; ?>"

                        data-winners="<?php echo $items["number-winners"]; ?>"

                        data-disclaimer="<?php echo $items["contest-disclaimer"]; ?>"

                        data-termsofservice="<?php echo $items["contest-termsofservice"]; ?>"

                        data-privacypolicybox="<?php echo $items["contest-privacypolicybox"]; ?>"

                        data-contest_rules="<?php echo $items["contest-rules-custom"]; ?>"

                        data-rules="<?php echo $items["contest-rules"]; ?>"

                        data-custom_scripts="<?php echo $items["tracking-scripts-custom"]; //added 4/5/2017 ?>"

                        data-scripts="<?php echo $items["tracking-scripts"]; //added 4/5/2017  ?>"

                        data-video="<?php echo $items["contest_video"]; //added 4/17/2017  ?> "

                        data-youtube_video="<?php echo $items["youtube_video"]; //added 4/17/2017  ?> "

                        data-user_scripts="<?php echo $items["user_scripts"]; //added 4/17/2017  ?> "

                        data-custom-css-class="<?php echo $items["tracking-scripts"]; //added 4/5/2017  ?>"

                        class="update_spy_link_button btn btn-default btn-sm">

                    <span class="glyphicon glyphicon-edit"></span>

                </button>

                <button

                        data-id="<?php the_ID(); ?>"

                        class="delete_spy_link_button btn btn-default btn-sm">

                    <span class="glyphicon glyphicon-trash"></span>

                </button>
            </td>

        </tr>


        <?php

    } // while while posts

    /* restore the global $post variable of the main query loop after a secondary query loop using new WP_Query */

    wp_reset_postdata();
}

add_action('wp_ajax_labdevs_rt_cookie_delete', 'labdevs_rt_cookie_delete_callback');
function labdevs_rt_cookie_delete_callback()
{
    wp_delete_post($_POST['rt_cookie_id'], true);
}

add_action('wp_ajax_labdevs_is_spy_url_available', 'labdevs_is_spy_url_available_callback');
function labdevs_is_spy_url_available_callback()
{
    if (labdevs_is_post_present_by_slug($_POST['slug'], 'teeoptimize') === false) {
        echo $_POST['ajax_call_number'] . '|true';
    } else {
        echo $_POST['ajax_call_number'] . '|false';
    }
}


function labdevs_is_post_present_by_slug($slug, $post = 'post')
{
    $query = new WP_Query(array('name' => $slug, 'post_type' => $post));
    if ($query->have_posts()) {
        wp_reset_postdata();
        return true;
    }
    wp_reset_postdata();
    return false;
}


add_action('wp_ajax_labdevs_spy_link_delete', 'labdevs_spy_link_delete_callback');
function labdevs_spy_link_delete_callback()
{
    wp_delete_post($_POST['spy_link_id'], true);
}

remove_filter('the_content', 'wpautop');
remove_filter('the_excerpt', 'wpautop');

if (!isset($content_width)) {
    $content_width = 900;
}

function enable_threaded_comments()
{
    if (!is_admin() && is_singular() && comments_open() && (get_option('thread_comments') == 1)) {
        wp_enqueue_script('comment-reply');
    }
}

add_action('get_header', 'enable_threaded_comments');

function template_directory_uri()
{
    echo get_template_directory_uri();
} // template_directory_uri


/**
 * remove the register link from the wp-login.php script
 */
add_filter('option_users_can_register', function ($value) {
    $script = basename(parse_url($_SERVER['SCRIPT_NAME'], PHP_URL_PATH));

    if ($script == 'wp-login.php') {
        $value = false;
    }

    return $value;
});


// add the shortcode handler for YouTube videos
function addYouTube($atts, $content = null)
{
    extract(shortcode_atts(array("id" => ''), $atts));
    return '<p style="text-align:center"> \
        <a href="http://www.youtube.com/v/' . $id . '"> \
        <img src="http://img.youtube.com/vi/' . $id . '/0.jpg" width="400" height="300" class="aligncenter" /> \
        <span>Watch the video</span></a></p>';
}

add_shortcode('youtube', 'addYouTube');
