<?php
/*
 Plugin Name: Contest Buzz
 Plugin URI: http://staging.contestbuzz.net
 Description: Quickly and easily run contests on your site with this WordPress plugin.
 Version: 1.1
 Author: Contest Buzz
 Author URI: http://staging.contestbuzz.net
 */

include('lib/global.php');
require_once('lib/template-tags.php');
require_once('lib/utility.php');
require_once('lib/frontend.php');
require_once('lib/functions.php');
require_once('lib/mc/src/MailChimp.php');
require_once('lib/getresponse/GetResponseAPI3.class.php');

if (!class_exists('Contest_Beast')) {

    class Contest_Beast
    {
        /// CONSTANTS

        //// VERSION
        const VERSION = '1.1.0';

        //// KEYS
        const POST_META_KEY = '_contest_beast_post_meta';
        const POST_META_ERRORS_KEY = '_contest_beast_post_meta_errors';
        const SETTING_ERRORS_KEY = '_contest_beast_setting_errors';
        const SETTINGS_KEY = '_contest_beast_settings';
        const COOKIE_ENTRY_EMAIL_KEY = '_cd_entry_email_';

        //// SLUGS
        const SETTINGS_PAGE_SLUG = 'contest-beast-settings';

        //// TYPES
        const TYPE_CONTEST = 'contest';

        //// CACHE
        const CACHE_PERIOD = 86400; // 24 HOURS

        //// URLS
        const URLS__AWEBER_AUTH = 'https://auth.aweber.com/1.0/oauth/authorize_app/5cf4ce12';
        const URLS__CONTEST_BEAST = 'http://contestbeast.com';
        const URLS__MAILCHIMP_API_KEY = 'https://us1.admin.mailchimp.com/account/api';
        const URLS__PROMOTE_ON_CLICKBANK = 'http://%s.contestdom.hop.clickbank.net';

        /// DATA STORAGE
        //private $contest_action_navs = array();
        public $my_account_action = "";
        private static $admin_page_hooks = array('post.php', 'post-new.php', 'edit.php');
        private static $default_meta = array();
        private static $default_settings = array();
        private static $entry_errors = null;
        private static $request_data = null;

        /// LISTS
        private static $aweber_lists = null;
        private static $mailchimp_lists = null;

        private static $this_user = array();


        public static function init()
        {
            //add_action('init', array(__CLASS__, 'do_member_action'));


            self::add_actions();
            self::add_filters();
            self::initialize_database_names();
            self::initialize_defaults();

            register_activation_hook(__FILE__, array(__CLASS__, 'do_activation_actions'));
            register_deactivation_hook(__FILE__, array(__CLASS__, 'do_deactivation_actions'));

            //include the theming

        }

        private static function add_actions()
        {
            if (is_admin()) {
                add_action('admin_enqueue_scripts', array(__CLASS__, 'enqueue_administrative_resources'));
                add_action('admin_init', array(__CLASS__, 'process_export'));
                add_action('admin_menu', array(__CLASS__, 'add_administrative_interface_items'));
            }

            //add_action( 'init', array($this,'prefix_movie_rewrite_rule'));
            //add_filter( 'query_vars', array($this,'prefix_register_query_var' ));
            add_action('plugins_loaded', array(__CLASS__, 'check_if_user_logged_in'));

            add_action('after_setup_theme', array(__CLASS__, 'add_post_thumbnail_support'));
            //
            add_action('init', array($this, 'contest_my_account'));
            add_action('init', array(__CLASS__, 'detect_submissions'));
            add_action('init', array(__CLASS__, 'register_content_types'));
            add_action('manage_' . self::TYPE_CONTEST . '_posts_custom_column', array(__CLASS__, 'custom_column_output'), 10, 2);
            add_action('parse_request', array(__CLASS__, 'redirect_referral'));
            add_action('save_post', array(__CLASS__, 'save_post_meta'), 10, 2);

            add_action('wp_ajax_contest_dashboard_update', array(__CLASS__, 'contest_dashboard_update'));

            add_action('wp_ajax_contest_beast_choose_winners', array(__CLASS__, 'ajax_choose_winners'));
            add_action('wp_ajax_contest_beast_get_mailing_list_markup', array(__CLASS__, 'ajax_get_mailing_list_markup'));
            add_action('wp_ajax_contest_beast_replace_winner', array(__CLASS__, 'ajax_replace_winner'));

            add_action('contest_header', array(__CLASS__, 'output_background_image_style'));

            add_action('contest_before_theme', array(__CLASS__, 'contest_beast_permission'));


        }

        public static function check_if_user_logged_in($user)
        {


            $current_user = wp_get_current_user();
            if (is_object($current_user)) {
                self::$this_user = $current_user;
            }

        }

        private static function add_filters()
        {

            add_filter('contest_beast_pre_meta_save', array(__CLASS__, 'sanitize_meta'), 10, 3);
            add_filter('contest_beast_pre_settings_save', array(__CLASS__, 'sanitize_settings'), 10, 2);
            add_filter('contest_beast_the_contest_rules', 'wpautop');

            add_filter('manage_edit-' . self::TYPE_CONTEST . '_columns', array(__CLASS__, 'custom_column_additions'));
            add_filter('plugin_action_links_' . plugin_basename(__FILE__), array(__CLASS__, 'add_settings_link'));
            add_filter('query_vars', array(__CLASS__, 'add_query_vars'));
            add_filter('rewrite_rules_array', array(__CLASS__, 'add_rewrite_rules'));
            add_filter('template_include', array(__CLASS__, 'correct_template'));
            add_filter('page_template', array(__CLASS__, 'contest_beast_template_load'));

        }

        private static function initialize_database_names()
        {
            global $wpdb;

            $wpdb->contest_beast_submissions = $wpdb->prefix . 'contest_beast_submissions';
            $wpdb->contest_beast_entries = $wpdb->prefix . 'contest_beast_entries';
        }

        private static function initialize_defaults()
        {
            self::$default_settings['default-number-entries'] = 10;
            self::$default_settings['default-number-winners'] = 5;
            self::$default_settings['default-contest-rules'] = '';
            self::$default_settings['default-tracking-scripts'] = '';
            self::$default_settings['mailing-list-list'] = '';
            self::$default_settings['mailing-list-provider'] = 'none';
            self::$default_settings['mailing-list-name'] = '';
            self::$default_settings['promote-contest-beast'] = 'yes';
            self::$default_settings['facebook-method'] = 'like';
            //self::$default_settings['default-custom_css_class'] = '';
            //self::$default_settings['default-custom_css_desc'] = '';

            $settings = self::get_settings();

            self::$default_meta['start-date'] = date('m/d/Y', current_time('timestamp') + (60 * 60 * 24));
            self::$default_meta['start-time-hour'] = '12';
            self::$default_meta['start-time-minute'] = '01';
            self::$default_meta['start-time-meridian'] = 'AM';

            self::$default_meta['end-date'] = date('m/d/Y', current_time('timestamp') + (60 * 60 * 24 * 8));
            self::$default_meta['end-time-hour'] = '11';
            self::$default_meta['end-time-minute'] = '59';
            self::$default_meta['end-time-meridian'] = 'PM';

            self::$default_meta['number-entries'] = $settings['default-number-entries'];
            self::$default_meta['number-winners'] = $settings['default-number-winners'];

            self::$default_meta['contest-disclaimer'] = __('Enter the contest and receive exclusive offers and updates. Unsubscribe anytime.');
            self::$default_meta['contest-rules-custom'] = 'no';
            self::$default_meta['contest-rules'] = $settings['default-contest-rules'];

            // Moderni Contest TOS
            self::$default_meta['contest-termsofservice'] = $settings['default-contest-termsofservice'];
            self::$default_meta['contest-privacypolicybox'] = $settings['default-contest-privacypolicybox'];

            self::$default_meta['tracking-scripts'] = $settings['default-tracking-scripts'];
            //added 4/7/2017
            //self::$default_meta['custom_css_class'] = $settings['default-custom_css_class'];
            //self::$default_meta['custom_css_desc'] = $settings['default-custom_css_desc'];

            self::$default_meta['add-to-mailing-list'] = 'yes';
            self::$default_meta['mailing-list-provider'] = $settings['mailing-list-provider'];
            self::$default_meta['mailing-list-list'] = $settings['mailing-list-list'];
            self::$default_meta['mailing-list-name'] = $settings['mailing-list-name'];

            self::$default_meta['winner-type'] = 'weighted';
        }

        /// ACTIVATION / DEACTIVATION

        public static function do_activation_actions()
        {
            self::create_database_tables();
            self::register_content_types();
            flush_rewrite_rules();
        }


        /* gene terry  */
        public static function contest_beast_template_load($page_template)
        {
            global $contest_query_vars;

            $my_account_action = get_query_var('action');

            //print_r(self::$this_user);
            /* $getresponse = new GetResponse('3b5891093b77a4aef05f2f5bc273e999');
            $getresponse->enterprise_domain = 'staging.contestbuzz.net';

            $resultgr = $getresponse->getCampaigns();
            //$resultgr = $getresponse->accounts();
            print_r($resultgr); */

            //my-account/?action=new-content
            if (array_key_exists($my_account_action, $contest_query_vars)) {
                $page_template = dirname(__FILE__) . '/templates/templates.php';
                if ($my_account_action == "dashboard") {
                    $settings = self::get_settings();
                    /* echo "<pre>";
                    print_r($settings);
                    echo "<pre>"; */
                    add_action('contest_after_head', array(__CLASS__, 'contest_call_dashboard_after_head'), 2);
                }

                //add action for new contest
                if ($my_account_action == "new-contest") {
                    add_action('contest_after_head', array(__CLASS__, 'contest_call_dashboard_after_head'), 2);
                    //add_action('contest_after_head',array(__CLASS__,'contest_call_editcontest_after_head'),2);
                    //add_action('contest_before_content',array(__CLASS__,'contest_call_after_content_new_contest'));
                    add_action('contest_content', array(__CLASS__, 'contest_call_edit_contest'));

                }

                //add action for new contest
                if ($my_account_action == "edit-contest") {
                    add_action('contest_after_head', array(__CLASS__, 'contest_call_dashboard_after_head'), 2);
                    add_action('contest_content', array(__CLASS__, 'contest_call_edit_contest'));

                }

                //add action for new contest
                if ($my_account_action == "build-contest") {
                    add_action('contest_after_head', array(__CLASS__, 'contest_call_new_contest_after_head'), 2);
                    add_action('contest_before_content', array(__CLASS__, 'contest_call_after_content_new_contest'));

                }

                //add action for contest university
                if ($my_account_action == "contest-university") {
                    add_action('contest_after_head', array(__CLASS__, 'contest_call_new_contest_after_head'), 2);
                    add_action('contest_before_content', array(__CLASS__, 'contest_call_after_content_contest_university'));

                }

                //add action for tutorials
                if ($my_account_action == "tutorials") {
                    add_action('contest_before_content', array(__CLASS__, 'contest_call_after_content_tutorials'));

                }

                //add action for contest support
                if ($my_account_action == "settings") {
                    add_action('contest_after_head', array(__CLASS__, 'contest_call_dashboard_after_head'), 2);
                }
                if ($my_account_action == "support") {
                    add_action('contest_before_content', array(__CLASS__, 'contest_call_after_content_support'));

                }
            }


            add_action('contest_content', array(__CLASS__, 'contest_call_content'));


            add_action('contest_after_head', array(__CLASS__, 'contest_call_after_head'), 1);
            add_action('contest_before_content', array(__CLASS__, 'contest_call_before_content'), 2);
            add_action('contest_after_footer', array(__CLASS__, 'contest_call_after_footer'));

            return $page_template;
        }


        //dashboard after header
        public static function contest_beast_permission()
        {
            if (!is_user_logged_in()) {
                header('Location: /wp-login.php');
                exit;
            }
        }

        public static function contest_call_new_contest_after_head()
        {
            ?>
            <?php
        }

        public static function contest_call_dashboard_after_head()
        {

            ?>
            <script src='https://cloud.tinymce.com/stable/tinymce.min.js?apiKey=9ez1msy4zpfsltla5qfih2dgk0i0vt9e2tnbo8chp3n1csld'></script>
            <link rel="stylesheet"
                  href="//cdn.datatables.net/plug-ins/3cfcc339e89/integration/bootstrap/3/dataTables.bootstrap.css"/>
            <script type="text/javascript" language="javascript"
                    src="//cdn.datatables.net/1.10.4/js/jquery.dataTables.min.js"></script>
            <script type="text/javascript" language="javascript"
                    src="//cdn.datatables.net/plug-ins/3cfcc339e89/integration/bootstrap/3/dataTables.bootstrap.js"></script>
            <script type="text/javascript" language="javascript"
                    src="<?php echo plugins_url('contest-beast/templates/assets/js/js_dashboard.js') ?>"></script>

            <?php
        }

        public static function contest_call_editcontest_after_head()
        {
            ?>
            <script type="text/javascript" language="javascript"
                    src="<?php echo plugins_url('contest-beast/templates/assets/js/js_dashboard.js') ?>"></script>
            <?php
        }

        //edit-contest function
        public function contest_call_edit_contest()
        {


            $contestId = get_query_var('cbid');

            ?>
            <div class="container">
                <div class="row">
                    <div class="col-md-10 col-md-offset-1" style="padding:0px">
                        <div class="contest-head">
                            <h2><i class="fa fa-edit"></i> <?= ($contestId > 0) ? "Edit Contest" : "New Contest"; ?>
                            </h2>
                            <div class="contest-form-action">
                                <a href="/my-account/dashboard" class="btn btn-default ">Back to Dashboard</a>
                                <?= ($contestId > 0) ? '<a href="' . get_permalink($contestId) . '" target="_blank" class="btn btn-default ">Preview</a>' : ''; ?>
                                <button class="btn  btn-primary doupdate_contest"><?= ($contestId > 0) ? "Update" : "Create Contest"; ?> </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            include("templates/my-account/contest-form.php");
            ?>
            <div class="container">
                <div class="row">
                    <div class="col-md-10 col-md-offset-1" style="padding:0px">
                        <div class="contest-footer">

                            <div class="contest-form-action">
                                <a href="/my-account/dashboard" class="btn btn-default ">Back to Dashboard</a>
                                <button class="btn  btn-primary doupdate_contest"
                                        data-action="<?= ($contestId > 0) ? "Update" : "Create Contest"; ?>"
                                        id="addeditaction"><?= ($contestId > 0) ? "Update" : "Create Contest"; ?></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php
        }

        //new-contest function
        public function contest_call_after_content_new_contest()
        {
            //echo "action here //submit";
        }

        //contest-university function
        public function contest_call_after_content_contest_university()
        {
            //echo "Test for the contest-university page";
        }

        //tutorials function
        public function contest_call_after_content_contest_tutorials()
        {
            //echo "Test for the tutorials page...";
        }

        //support function
        public function contest_call_after_content_contest_support()
        {
            //echo "Test for the support page...";
        }

        public function contest_call_before_content()
        {
            //navigation
            global $current_user;
            // $current_user = get_currentuserinfo();

            $userinfo = $current_user->data;

            ?>

            <div class="navbar navbar-default navbar-fixed-top" role="navigation">
                <div class="container">
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle" data-toggle="collapse"
                                data-target=".navbar-collapse">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                        <a class="navbar-brand" href="<?php echo get_page_link(9); ?>"><img
                                    src="<?= plugins_url('contest-beast/templates/assets/images/') ?>logo_member_dashboard.png"
                                    alt=""></a>
                    </div>
                    <div class="navbar-collapse collapse" style="font-size: 12px;padding-right: 30px;">
                        <ul class="nav navbar-nav">
                            <li><a href="/my-account/dashboard"><span class="glyphicon glyphicon-stats"></span> My
                                    Contests</a></li>
                            <li><a href="/my-account/new-contest"><span class="fa fa-plus"></span> New Contest</a></li>
                            <li><a href="/my-account/contest-university"><span
                                            class="glyphicon glyphicon-plus-sign"></span> Contest University</a></li>
                            <li><a href="/my-account/tutorials"><span class="glyphicon glyphicon-facetime-video"></span>
                                    Tutorials</a></li>
                            <li><a href="/my-account/support"><span class="glyphicon glyphicon-briefcase"></span>
                                    Support</a></li>
                        </ul>

                        <ul class="nav navbar-nav pull-right">
                            <li><a class="dropdown-toggle" id="menuaccount" data-toggle="dropdown" data-target="#"
                                   href="#"> <span class="glyphicon glyphicon-user"></span></span></a>
                                <ul class="" id="profilemenus" role="menu" aria-labelledby="dropdownMenu1">
                                    <li role="presentation"><a role="menuitem" tabindex="-1"
                                                               href="/my-account/settings/">Settings</a></li>
                                    <li role="presentation"><a role="menuitem" tabindex="-1"
                                                               href="<?php echo $userinfo->user_firstname; ?> <?php echo $userinfo->user_lastname ?>">Profile</a>
                                    </li>
                                    <li role="presentation"><a role="menuitem" tabindex="-1"
                                                               href="<?php echo wp_logout_url(); ?> ">Logout</a></li>
                                </ul>
                            </li>

                        </ul>

                    </div>
                </div>
            </div> <!-- .navbar -->


            <?php
        }

        public static function contest_call_after_head()
        {
            ?>
            <!-- start: call after hook -->
            <!-- Bootstrap core CSS -->
            <link href="<?php echo plugins_url('contest-beast/templates/assets/css/bootstrap.min.css'); ?>"
                  rel="stylesheet">
            <link href="<?php echo plugins_url('contest-beast/templates/assets/css/bootstrap-multiselect.css'); ?>"
                  rel="stylesheet">
            <!-- Custom styles for this template -->
            <link href="<?php echo plugins_url('contest-beast/templates/assets/css/dashboard.css'); ?>"
                  rel="stylesheet">
            <link type="text/css" rel="stylesheet"
                  href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/themes/smoothness/jquery-ui.css">
            <!-- Just for debugging purposes. Don't actually copy this line! -->
            <!--[if lt IE 9]>
            <script src="<?php echo plugins_url('contest-beast/templates/assets/js/ie8-responsive-file-warning.js')?>"></script><![endif]-->
            <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
            <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
            <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
            <![endif]-->
            <!-- Bootstrap core JavaScript
            ================================================== -->
            <link rel="stylesheet"
                  href="<?php echo plugins_url('contest-beast/templates/assets/css/jquery.fileupload.css') ?>"/>
            <link rel="stylesheet"
                  href="<?php echo plugins_url('contest-beast/templates/assets/css/colorpicker.css') ?>"/>
            <link rel="stylesheet" href="<?php echo plugins_url('contest-beast/templates/assets/css/custom.css') ?>"/>
            <link rel="stylesheet" type="text/css"
                  href="http://fortawesome.github.io/Font-Awesome/assets/font-awesome/css/font-awesome.css"
                  media="all"/>

            <!-- End: call after hook -->
            <script type="text/javascript">
                var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
            </script>
            <?php
        }

        public static function contest_call_content()
        {
            //$this_action = $this->my_account_action;
            $my_account_page = get_query_var('action');
            if (file_exists(dirname(__FILE__) . "/templates/my-account/$my_account_page.php")) {
                include(dirname(__FILE__) . "/templates/my-account/$my_account_page.php");
            }
        }

        public static function contest_call_after_footer()
        {
            ?>
            <!-- Placed at the end of the document so the pages load faster -->
            <!-- <script src="//code.jquery.com/jquery-1.10.2.js"></script> -->
            <div class="container-fluid" style="background:#2a0845">
                <div class="container">
                    <div class="row">
                        <div style="padding:20px 0px; color:#fff;">
                            <div class="col-md-6 col-sm-6 text-left">Copyright 2017 ContestBuzz</div>
                            <div class="col-md-6 col-sm-6 text-right">Term | Privacy Policy</div>
                            <div style="clear:both"></div>
                        </div>
                    </div>
                </div>
            </div>
            <script src="<?php echo plugins_url('contest-beast/templates/assets/js/bootstrap.min.js'); ?>"></script>
            <script src="<?php echo plugins_url('contest-beast/templates/assets/js/bootstrap-multiselect.js'); ?>"></script>
            <script src="<?php echo plugins_url('contest-beast/templates/assets/js/docs.min.js'); ?>"></script>
            <script src="<?php echo plugins_url('contest-beast/templates/assets/js/jquery.clipboard.js'); ?>"></script>
            <script src="<?php echo plugins_url('contest-beast/templates/assets/js/jquery.deserialize.min.js'); ?>"></script>
            <script src="//code.jquery.com/ui/1.11.2/jquery-ui.js"></script>

            <script src="<?php echo plugins_url('contest-beast/templates/assets/js/jquery.fileupload.js'); ?>"></script>
            <script src="<?php echo plugins_url('contest-beast/templates/assets/js/jquery.iframe-transport.js'); ?>"></script>

            <script src="<?php echo plugins_url('contest-beast/templates/assets/js/colorpicker.js'); ?>"></script>

            <script type="text/javascript">
                (function ($) {
                    $(document).ready(function () {
                        $(document).on('click', '.ptr-preview a.remove', function (e) {
                            e.preventDefault();
                            var group = $(this).closest('form');
                            group.find('.ptr-preview').html('');
                            group.find('input[name$="_url"]').val('');
                            return false;
                        });
                    });
                })(jQuery);
            </script>
            <?php
        }


        public function contest_get_template_part($template_names, $load = false, $require_once = true)
        {
            $located = '';
            foreach ((array)$template_names as $template_name) {
                if (!$template_name) continue;

                /* search file within the PLUGIN_DIR_PATH only */
                if (file_exists(PLUGIN_DIR_PATH . '/' . $template_name)) {
                    $located = PLUGIN_DIR_PATH . '/' . $template_name;
                    break;
                }
            }

            if ($load && '' != $located)
                load_template($located, $require_once);

            return $located;
        }


        /// AJAX CALLBACKS

        public static function contest_dashboard_update()
        {

            global $wpdb;


            $contest_post_id = $_POST['post_id'];
            $contest_detail = $wpdb->get_results("SELECT * FROM $wpdb->posts WHERE ID = $contest_post_id", OBJECT);

            if ($contest_detail) {

                $contest_data = $contest_detail[0];
                $contest_data_meta = get_post_meta($contest_data->ID);
                $_contest_beast_post_meta = unserialize($contest_data_meta['_contest_beast_post_meta'][0]);

                ?>
                <!-- Modal -->
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title" id="myModalLabel">Edit Contest</h4>
                        </div>
                        <div class="modal-body">
					<pre>
					<?php
                    $url_slug = str_replace(site_url() . "/contest/", "", get_permalink($contest_data->ID));
                    /* print_r($_contest_beast_post_meta);
                    print_r($contest_data);
                    print_r($contest_data_meta); */
                    ?>
					</pre>
                            <div class="row">
                                <div class="col-md-10 col-md-offset-1">
                                    <fieldset>
                                        <input type="hidden" id="spy_link_id" value="<?= $contest_data->ID; ?>">
                                        <!-- Text input-->
                                        <div class="form-group">
                                            <label class="control-label" for="textinput">Title</label>
                                            <div class="">
                                                <input id="spy_link_name" value="<?= $contest_data->post_title; ?>"
                                                       name="textinput" type="text" placeholder="Title here..."
                                                       class="form-control input-md">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label" for="url">Contest URL</label>
                                            <div class="">
                                                <div class="input-group">
                                                    <div class="input-group-btn">
                                                        <button id='url_prefix' type="button"
                                                                class="btn btn-default dropdown-toggle"
                                                                data-toggle="dropdown"
                                                                value="<?php echo site_url('/contest/'); ?>"><?php echo site_url('/'); ?>
                                                            <span class="caret"></span></button>
                                                        <ul class="dropdown-menu">
                                                            <li>
                                                                <a href="<?php echo site_url('/contest/'); ?>"><?php echo site_url('/'); ?></a>
                                                            </li>
                                                        </ul>
                                                    </div><!-- /btn-group -->
                                                    <input id="url" name="textinput"
                                                           value="<?= str_replace('/', "", $url_slug); ?>" type="text"
                                                           placeholder="URL here..." class="form-control input-md">
                                                </div><!-- /input-group -->
                                                <div id="url_checking" class="progress progress-striped active"
                                                     style="">
                                                    <div class="progress-bar" role="progressbar" aria-valuenow="100"
                                                         aria-valuemin="0" aria-valuemax="100" style="width: 100%"><span
                                                                class="sr-only">100% Complete</span></div>
                                                </div>
                                                <div id="url_msg"></div>
                                            </div>
                                        </div>
                                        <!-- Textarea -->
                                        <div class="form-group">
                                            <label class="control-label" for="textarea">Description</label>
                                            <div class="">
                                                <!-- <textarea id="post_content" name="textarea" rows="7" placeholder="Description"  class="form-control"></textarea> -->
                                                <?php
                                                $settings = array(
                                                    'quicktags' => array('buttons' => 'em,strong,link',),
                                                    'text_area_name' => 'extra_content',//name you want for the textarea
                                                    'quicktags' => true,
                                                    'media_buttons' => false,
                                                    'tinymce' => true
                                                );
                                                $id = 'post_content';//has to be lower case

                                                wp_editor($contest_data->post_content, $id, $settings);

                                                ?>
                                            </div>
                                        </div>
                                        <!-- Textarea -->
                                        <div class="form-group">
                                            <p class="help-block"><em>Server time: <code id="server-time"></code></em>
                                            </p>
                                            <script type="text/javascript">
                                                var svrTime = <?php echo current_time('timestamp', 0);?>;
                                                window.setInterval('svrTimeDisplay()', 1000);
                                                function svrTimeDisplay() {
                                                    svrTime += 1;
                                                    var date = new Date(svrTime * 1000);
                                                    var month = "0" + (date.getUTCMonth() + 1);
                                                    var day = "0" + date.getUTCDate();
                                                    var year = date.getUTCFullYear();
                                                    var hours = date.getUTCHours();
                                                    var minutes = "0" + date.getUTCMinutes();
                                                    var seconds = "0" + date.getUTCSeconds();
                                                    var ampm = hours >= 12 ? 'PM' : 'AM';
                                                    hours = hours % 12;
                                                    hours = hours % 12;
                                                    hours = "0" + hours;
                                                    var formattedTime = month.substr(month.length - 2) + "/" + day.substr(day.length - 2) + "/" + year + ' ' +
                                                        hours.substr(hours.length - 2) + ':' + minutes.substr(minutes.length - 2) + ':' + seconds.substr(seconds.length - 2) + ' ' + ampm;
                                                    // document.getElementById("server-time").innerHTML = date.toGMTString();
                                                    document.getElementById("server-time").innerHTML = formattedTime;
                                                }
                                            </script>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label" for="textarea">Start Date</label>
                                            <div class="">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <input type="text" id="start_date"
                                                               value="<?= $_contest_beast_post_meta['start-date'] ?>"
                                                               class="form-control datepicker"/>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <code style="display:inline-block;"><?php _e('at'); ?></code>
                                                        <select name="contest-beast[start-time-hour]" id="start_hour"
                                                                class="form-control"
                                                                style="width:auto;display:inline-block;">
                                                            <?php for ($hour = 1; $hour <= 12; $hour++) {
                                                                $selected_hr = ($_contest_beast_post_meta['start-time-hour'] == $hour) ? " Selected " : "";
                                                                $hour = sprintf('%02d', $hour);

                                                                ?>
                                                                <option value="<?php echo $hour; ?>" <?= $selected_hr; ?>><?php echo $hour; ?></option>
                                                            <?php } ?>
                                                        </select>
                                                        <code style="display:inline-block;">:</code>
                                                        <select name="contest-beast[start-time-minute]"
                                                                id="start_minute" class="form-control"
                                                                style="width:auto;display:inline-block;">
                                                            <?php for ($minute = 0; $minute <= 59; $minute++) {
                                                                $minute = sprintf('%02d', $minute);
                                                                $selected_mn = ($_contest_beast_post_meta['start-time-minute'] == $minute) ? " Selected " : "";
                                                                ?>
                                                                <option value="<?php echo $minute; ?>"
                                                                        $selected_mn><?php echo $minute; ?></option>
                                                            <?php } ?>
                                                        </select>
                                                        <select name="contest-beast[start-time-meridian]"
                                                                id="start_meridian" class="form-control"
                                                                style="width:auto;display:inline-block;">
                                                            <?php
                                                            $meridian = array('AM', 'PM');

                                                            for ($i = 0; $i < count($meridian); $i++) {
                                                                $selected_meridian = ($_contest_beast_post_meta['start-time-meridian'] == $meridian[$i]) ? " Selected " : "";
                                                                echo "<option value=\"$meridian[$i]\" $selected_meridian>$meridian[$i]</option>";
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Textarea -->
                                        <div class="form-group">
                                            <label class="control-label" for="textarea">End Date</label>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <input type="text" id="end_date"
                                                           class="form-control input-md datepicker"/>
                                                </div>
                                                <div class="col-md-8">
                                                    <code><?php _e('at'); ?></code>
                                                    <select name="contest-beast[end-time-hour]" id="end_hour"
                                                            class="form-control"
                                                            style="width:auto;display:inline-block;">
                                                        <?php for ($hour = 1; $hour <= 12; $hour++) {
                                                            $hour = sprintf('%02d', $hour);
                                                            $selected_hr = ($_contest_beast_post_meta['end-time-hour'] == $hour) ? " Selected " : "";
                                                            ?>
                                                            <option value="<?php echo $hour; ?>" <?= $selected_hr ?>><?php echo $hour; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                    <code>:</code>
                                                    <select name="contest-beast[end-time-minute]" id="end_minute"
                                                            class="form-control"
                                                            style="width:auto;display:inline-block;">
                                                        <?php for ($minute = 0; $minute <= 59; $minute++) {
                                                            $minute = sprintf('%02d', $minute);
                                                            $selected_minute = ($_contest_beast_post_meta['end-time-minute'] == $minute) ? " Selected " : "";
                                                            ?>
                                                            <option value="<?php echo $minute; ?>"
                                                                    $selected_minute><?php echo $minute; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                    <select name="contest-beast[end-time-meridian]" id="end_meridian"
                                                            class="form-control"
                                                            style="width:auto;display:inline-block;">
                                                        <?php
                                                        $meridian = array('AM', 'PM');

                                                        for ($i = 0; $i < count($meridian); $i++) {
                                                            $selected_meridian = ($_contest_beast_post_meta['end-time-meridian'] == $meridian[$i]) ? " Selected " : "";
                                                            echo "<option value=\"$meridian[$i]\" $selected_meridian>$meridian[$i]</option>";
                                                        }
                                                        ?>
                                                    </select>


                                                </div>
                                            </div>
                                        </div>
                                        <!-- Text input-->
                                        <div class="form-group">
                                            <label class="control-label" for="countdown">Countdown template</label>
                                            <div class="">
                                                <select name="contest-beast[timer-template]" id="countdown"
                                                        class="form-control">
                                                    <?php
                                                    $mcControler = MotionCountdownController::getInstance();
                                                    $mcLoader = MotionCountdownLoader::getInstance();
                                                    $mcLoader->load($mcControler->pluginFilePath . 'model/generator_templates/');
                                                    $mctURL = $mcControler->templatesDirectoryURLPath;
                                                    $templatesInformation = array();
                                                    foreach (get_declared_classes() as $className) {
                                                        if (is_subclass_of($className, MotionCountdownController::getInstance()->modelGenerator->templateAbstractClass)) {
                                                            $classInstance = new $className();
                                                            $selected_coundown_theme = ($_contest_beast_post_meta['timer-template'] == $classInstance->getTemplateAlias()) ? " Selected " : "";
                                                            echo '<option value="' . $classInstance->getTemplateAlias() . '" >' . $classInstance->getTemplateName() . '</option>';
                                                            $templatesInformation[] = "'" . $classInstance->getTemplateAlias() . "': '" . $classInstance->getBackgroundImageName() . "'";
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                                <br/>
                                                <div id="countdown-preview" class="ptr-preview"></div>
                                                <script type="text/javascript">
                                                    var mctemplate = {<?php echo implode(",", $templatesInformation); ?>};
                                                    jQuery('#countdown').on('change', function () {
                                                        jQuery('#countdown-preview').html('<img src="<?php echo $mctURL;?>' + mctemplate[jQuery(this).val()] + '" alt="" style="max-width:100%"/>');
                                                    }).change();
                                                </script>
                                            </div>
                                        </div>
                                        <!-- Text input-->
                                        <div class="form-group">
                                            <label class="control-label" for="referral">Entries per Referral</label>
                                            <div class="">
                                                <input id="referral"
                                                       value="<?= $_contest_beast_post_meta['number-entries'] ?>"
                                                       name="contest-beast[number-entries]" type="text" style=""
                                                       class="form-control input-md">
                                                <small style="">Enter the number of entries you want a participant to
                                                    receive when referring another user to this contest.
                                                </small>
                                            </div>
                                        </div>
                                        <!-- Text input-->
                                        <div class="form-group">
                                            <label class="control-label" for="winners">Number of Winners</label>
                                            <div class="">
                                                <input id="winners"
                                                       value="<?= $_contest_beast_post_meta['number-winners'] ?>"
                                                       name="contest-beast[number-winners]" type="text" style=""
                                                       class="form-control input-md">
                                                <small style="">Enter the number of winners you want this contest to
                                                    have.
                                                </small>
                                            </div>
                                        </div>
                                        <!-- Text input-->
                                        <div class="form-group">
                                            <label class="control-label" for="disclaimer">Disclaimer</label>
                                            <div class="">
                                                <textarea id="disclaimer" name="contest-beast[contest-disclaimer]"
                                                          rows="7" placeholder="Description"
                                                          class="form-control"><?= $_contest_beast_post_meta['contest-disclaimer'] ?></textarea>
                                                <small style="">Enter a short contest disclaimer. It will be displayed
                                                    in a semi-prominent position on the contest page.
                                                </small>
                                            </div>
                                        </div>
                                        <!-- Text input-->
                                        <div class="form-group">
                                            <label class="control-label" for="termsofservice">Terms Of Service</label>
                                            <div class="">
                                                <textarea id="termsofservice"
                                                          name="contest-beast[contest-termsofservice]" rows="7"
                                                          placeholder="Terms of Service"
                                                          class="form-control"><?= $_contest_beast_post_meta['contest-termsofservice'] ?></textarea>
                                                <small style="">Enter a Terms of Service Details for contest. It will be
                                                    displayed in a semi-prominent position on the contest page.
                                                </small>
                                            </div>
                                        </div>
                                        <!-- Text input-->
                                        <div class="form-group">
                                            <label class="control-label" for="privacypolicybox">Privacy Policy
                                                Box</label>
                                            <div class="">
                                                <textarea id="privacypolicybox"
                                                          name="contest-beast[contest-privacypolicybox]" rows="7"
                                                          placeholder="Privacy Policy"
                                                          class="form-control"><?= $_contest_beast_post_meta['contest-privacypolicybox'] ?></textarea>
                                                <small style="">Enter a Privacy Policy Box Details for contest. It will
                                                    be displayed in a semi-prominent position on the contest page.
                                                </small>
                                            </div>
                                        </div>
                                        <!-- Text input-->
                                        <div class="form-group">
                                            <label class="control-label" for="textinput">Custom Rules?</label>
                                            <div class="">
                                                <label>
                                                    <input type="checkbox"
                                                           name="contest-beast[contest-rules-custom]" <?php echo ($_contest_beast_post_meta['contest-rules-custom'] == "yes") ? " checked=\"checked\"  " : ""; ?>
                                                           id="contest_rules"/>

                                                    <small style="">This contest has custom rules (you will enter them
                                                        below)
                                                    </small>
                                                </label>
                                            </div>
                                        </div>
                                        <!-- Text input-->
                                        <div class="form-group open_rules_content"
                                             style="<?php echo ($_contest_beast_post_meta['contest-rules-custom'] == "yes") ? " display:block; " : "display:none;"; ?> ">
                                            <div class="">
                                                <textarea id="rules" name="contest-beast[contest-rules]" rows="7"
                                                          placeholder="Enter your rules"
                                                          class="form-control"><?= $_contest_beast_post_meta['contest-rules'] ?></textarea>
                                            </div>
                                        </div>
                                        <!-- Text input
                                        [add-to-mailing-list] => yes
                                        [mailing-list-provider] => other
                                        [mailing-list-list] =>
                                        [mailing-list-name] =>
                                        -->
                                        <div class="form-group">
                                            <label class="control-label" for="custom_mailing">Add Participants to
                                                Mailing List?</label>
                                            <div class="">
                                                <label>
                                                    <input type="checkbox" name="contest-beast[add-to-mailing-list]"
                                                           id="custom_mailing" <?php echo ($_contest_beast_post_meta['add-to-mailing-list'] == "on") ? " checked=\"checked\"  " : ""; ?> />
                                                    <small style="">You can add participants to your mailing list
                                                    </small>
                                                </label>

                                            </div>
                                        </div>

                                        <div class="form-group open_mailing_content"
                                             style="<?php echo ($_contest_beast_post_meta['add-to-mailing-list'] == "on") ? " display:block; " : "display:none;"; ?> ">
                                            <div class="">
                                                <textarea id="mailing" name="contest-beast[mailing-list-list]" rows="7"
                                                          placeholder="Enter your form code"
                                                          class="form-control"><?php echo $_contest_beast_post_meta['add-to-list-name'];
                                                    echo $_contest_beast_post_meta['add-to-list-provider'];
                                                    echo $_contest_beast_post_meta['add-to-list-list']; ?></textarea>
                                            </div>
                                        </div>

                                        <!-- Text input-->
                                        <div class="form-group">
                                            <strong>Your thank you page url: </strong><br/><code
                                                    id="thankyou-page"><?= get_permalink($contest_data->ID) ?></code>
                                        </div>


                                        <div class="form-group">
                                            <label class="control-label" for="textinput">Branding Logo</label>
                                            <form enctype="multipart/form-data">
                                                <input type="file" name="logo" id="logo"/>
                                                <input type="hidden" name="logo_url" id="logo_url"/>
                                                <input type="hidden" name="action" value="upload_image"/>
                                                <br/>
                                                <div class="progress" id="logo_upload_progress">
                                                    <div class="progress-bar" role="progressbar" aria-valuenow="0"
                                                         aria-valuemin="0" aria-valuemax="100" style=""></div>
                                                </div>
                                                <div id="logo-preview" class="ptr-preview"></div>
                                                <script type="text/javascript">
                                                    (function ($) {
                                                        $(document).ready(function () {
                                                            $('#logo_upload_progress').hide();
                                                            // Change this to the location of your server-side upload handler:
                                                            $('#logo').fileupload({
                                                                url: "<?php echo admin_url('admin-ajax.php'); ?>",
                                                                dataType: 'json',
                                                                done: function (e, data) {
                                                                    $('#logo_upload_progress').hide();
                                                                    $('#logo-preview').html('<img src="' + data.result.url + '" alt="" width="80" />')
                                                                        .append('<a href="#" class="remove"><i class="fa fa-times"></i></a>');
                                                                    $('#logo_url').val(data.result.url);
                                                                },
                                                                progressall: function (e, data) {
                                                                    $('#logo_upload_progress').show();
                                                                    var progress = parseInt(data.loaded / data.total * 100, 10);
                                                                    $('#logo_upload_progress .progress-bar').css(
                                                                        'width',
                                                                        progress + '%').text(progress + '%');
                                                                }
                                                            }).prop('disabled', !$.support.fileInput)
                                                                .parent().addClass($.support.fileInput ? undefined : 'disabled');
                                                        });
                                                    })(jQuery);
                                                </script>
                                            </form>
                                        </div>

                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label class="control-label">Body background color</label>
                                                    <div class="input-group">
                                                        <span class="input-group-addon">#</span>
                                                        <input type="text" name="body_background_color"
                                                               id="body_background_color" class="form-control"/>
                                                    </div>
                                                    <div id="body_background_color_preview"></div>
                                                    <script type="text/javascript">
                                                        (function ($) {
                                                            $(document).ready(function () {
                                                                var $bgc = $('#body_background_color');
                                                                $bgc.ColorPicker({
                                                                    onSubmit: function (hsb, hex, rgb, el) {
                                                                        $(el).val(hex);
                                                                        $(el).ColorPickerHide();
                                                                    },
                                                                    onBeforeShow: function () {
                                                                        $(this).ColorPickerSetColor($bgc.val());
                                                                    },
                                                                    onChange: function (hsb, hex, rgb) {
                                                                        $bgc.val(hex);
                                                                    }
                                                                })
                                                                    .bind('keyup', function () {
                                                                        $(this).ColorPickerSetColor($bgc.val());
                                                                        $('#body_background_color_preview').css('background-color', $bgc.val());
                                                                        $('#body_background_color_preview').height('height', '20px;');
                                                                    });
                                                            });
                                                        })(jQuery);
                                                    </script>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="control-label">Contest background color</label>
                                                    <div class="input-group">
                                                        <span class="input-group-addon">#</span>
                                                        <input type="text" name="contest_background_color"
                                                               id="contest_background_color" class="form-control"/>
                                                    </div>
                                                    <script type="text/javascript">
                                                        (function ($) {
                                                            $(document).ready(function () {
                                                                var $bgc = $('#contest_background_color');
                                                                $bgc.ColorPicker({
                                                                    onSubmit: function (hsb, hex, rgb, el) {
                                                                        $(el).val(hex);
                                                                        $(el).ColorPickerHide();
                                                                    },
                                                                    onBeforeShow: function () {
                                                                        $(this).ColorPickerSetColor($bgc.val());
                                                                    },
                                                                    onChange: function (hsb, hex, rgb) {
                                                                        $bgc.val(hex);
                                                                    }
                                                                })
                                                                    .bind('keyup', function () {
                                                                        $(this).ColorPickerSetColor($bgc.val());
                                                                    });
                                                            });
                                                        })(jQuery);
                                                    </script>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label class="control-label">Body background image</label>
                                                    <form enctype="multipart/form-data">
                                                        <input type="file" name="body_background_image"
                                                               id="body_background_image"/>
                                                        <input type="hidden" name="body_background_image_url"
                                                               id="body_background_image_url"/>
                                                        <input type="hidden" name="action" value="upload_image"/>
                                                        <br/>
                                                        <div class="progress"
                                                             id="body_background_image_upload_progress">
                                                            <div class="progress-bar" role="progressbar"
                                                                 aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"
                                                                 style=""></div>
                                                        </div>
                                                        <div id="body_background_image-preview"
                                                             class="ptr-preview"></div>
                                                        <script type="text/javascript">
                                                            (function ($) {
                                                                $(document).ready(function () {
                                                                    $('#body_background_image_upload_progress').hide();
                                                                    // Change this to the location of your server-side upload handler:
                                                                    $('#body_background_image').fileupload({
                                                                        url: "<?php echo admin_url('admin-ajax.php'); ?>",
                                                                        dataType: 'json',
                                                                        done: function (e, data) {
                                                                            $('#body_background_image_upload_progress').hide();
                                                                            $('#body_background_image-preview')
                                                                                .html('<img src="' + data.result.url + '" alt="" width="80" />')
                                                                                .append('<a href="#" class="remove"><i class="fa fa-times"></i></a>');
                                                                            ;
                                                                            $('#body_background_image_url').val(data.result.url);
                                                                        },
                                                                        progressall: function (e, data) {
                                                                            $('#body_background_image_upload_progress').show();
                                                                            var progress = parseInt(data.loaded / data.total * 100, 10);
                                                                            $('#body_background_image_upload_progress .progress-bar').css(
                                                                                'width',
                                                                                progress + '%').text(progress + '%');
                                                                        }
                                                                    }).prop('disabled', !$.support.fileInput)
                                                                        .parent().addClass($.support.fileInput ? undefined : 'disabled');
                                                                });
                                                            })(jQuery);
                                                        </script>
                                                    </form>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="control-label">Contest background image</label>
                                                    <form enctype="multipart/form-data">
                                                        <input type="file" name="contest_background_image"
                                                               id="contest_background_image"/>
                                                        <input type="hidden" name="contest_background_image_url"
                                                               id="contest_background_image_url"/>
                                                        <input type="hidden" name="action" value="upload_image"/>
                                                        <br/>
                                                        <div class="progress"
                                                             id="contest_background_image_upload_progress">
                                                            <div class="progress-bar" role="progressbar"
                                                                 aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"
                                                                 style=""></div>
                                                        </div>
                                                        <div id="contest_background_image-preview"
                                                             class="ptr-preview"></div>
                                                        <script type="text/javascript">
                                                            (function ($) {
                                                                $(document).ready(function () {
                                                                    $('#contest_background_image_upload_progress').hide();
                                                                    // Change this to the location of your server-side upload handler:
                                                                    $('#contest_background_image').fileupload({
                                                                        url: "<?php echo admin_url('admin-ajax.php'); ?>",
                                                                        dataType: 'json',
                                                                        done: function (e, data) {
                                                                            $('#contest_background_image_upload_progress').hide();
                                                                            $('#contest_background_image-preview')
                                                                                .html('<img src="' + data.result.url + '" alt="" width="80" />')
                                                                                .append('<a href="#" class="remove"><i class="fa fa-times"></i></a>');
                                                                            $('#contest_background_image_url').val(data.result.url);
                                                                        },
                                                                        progressall: function (e, data) {
                                                                            $('#contest_background_image_upload_progress').show();
                                                                            var progress = parseInt(data.loaded / data.total * 100, 10);
                                                                            $('#contest_background_image_upload_progress .progress-bar').css(
                                                                                'width',
                                                                                progress + '%').text(progress + '%');
                                                                        }
                                                                    }).prop('disabled', !$.support.fileInput)
                                                                        .parent().addClass($.support.fileInput ? undefined : 'disabled');
                                                                });
                                                            })(jQuery);
                                                        </script>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Text input-->
                                        <div class="form-group">
                                            <label class="control-label" for="">Contest Button</label>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <p>Select from our template</p>

                                                    <select name="button_tpl" id="button_tpl" class="form-control">

                                                        <option value="<?php echo plugins_url('/contest-beast/views/frontend/templates/resources/img/enter-contest-button.png'); ?>"
                                                                selected="selected">Default
                                                        </option>
                                                        <?php
                                                        $imgExt = array('gif', 'jpg', 'jpeg', 'png');
                                                        $btnDir = plugin_dir_path('/contest-beast/templates/assets/img/buttons');
                                                        $btnUrl = plugins_url('/contest-beast/templates/assets/img/buttons');
                                                        $btnImg = scandir($btnDir, 1);
                                                        foreach ($btnImg as $img) {
                                                            $info = pathinfo($img);
                                                            $name = ucwords(preg_replace('/[_-]/', ' ', $info['filename']));
                                                            $exts = strtolower($info['extension']);
                                                            if (in_array($exts, $imgExt)) {
                                                                echo "<option value='$btnUrl/$img'>$name</option>";
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                    <br/>
                                                    <div id="button_tpl_preview"></div>
                                                    <script type="text/javascript">
                                                        var mctemplate = {<?php echo implode(",", $templatesInformation); ?>};
                                                        jQuery('#button_tpl').on('change', function () {
                                                            jQuery('#button_tpl_preview').html('<img src="' + jQuery(this).val() + '" alt="" style="max-width:100%"/>');
                                                        }).change();
                                                    </script>
                                                </div>
                                                <div class="col-md-6">
                                                    <p>or Upload your own button</p>
                                                    <form enctype="multipart/form-data">
                                                        <input type="file" name="button_img" id="button_img"/>
                                                        <input type="hidden" name="button_img_url" id="button_img_url"/>
                                                        <input type="hidden" name="action" value="upload_image"/>
                                                        <br/>
                                                        <div class="progress" id="button_img_upload_progress">
                                                            <div class="progress-bar" role="progressbar"
                                                                 aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"
                                                                 style=""></div>
                                                        </div>
                                                        <div id="button_img-preview" class="ptr-preview"></div>
                                                        <script type="text/javascript">
                                                            (function ($) {
                                                                $(document).ready(function () {
                                                                    $('#button_img_upload_progress').hide();
                                                                    // Change this to the location of your server-side upload handler:
                                                                    $('#button_img').fileupload({
                                                                        url: "<?php echo admin_url('admin-ajax.php'); ?>",
                                                                        dataType: 'json',
                                                                        done: function (e, data) {
                                                                            $('#button_img_upload_progress').hide();
                                                                            $('#button_img-preview')
                                                                                .html('<img src="' + data.result.url + '" alt="" width="80" />')
                                                                                .append('<a href="#" class="remove"><i class="fa fa-times"></i></a>');
                                                                            $('#button_img_url').val(data.result.url);
                                                                        },
                                                                        progressall: function (e, data) {
                                                                            $('#button_img_upload_progress').show();
                                                                            var progress = parseInt(data.loaded / data.total * 100, 10);
                                                                            $('#button_img_upload_progress .progress-bar').css(
                                                                                'width',
                                                                                progress + '%').text(progress + '%');
                                                                        }
                                                                    }).prop('disabled', !$.support.fileInput)
                                                                        .parent().addClass($.support.fileInput ? undefined : 'disabled');
                                                                });
                                                            })(jQuery);
                                                        </script>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </fieldset>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button id="update_spy_link" type="button" class="btn btn-primary">Save changes</button>
                        </div>
                    </div>
                </div>

                <?php
            } //edn if
            exit;
        }


        public static function ajax_choose_winners()
        {
            $data = self::get_request_data();
            $contest_id = $data['contest-id'];
            $method = 'weighted' === $data['method'] ? 'weighted' : 'entries';

            $meta = self::get_meta($contest_id);
            $meta['winner-type'] = $method;
            $meta = self::set_meta($contest_id, $meta);

            self::choose_winners($contest_id, self::get_meta($contest_id, 'number-winners'), $method);
            self::display_contest_winners_markup($contest_id);
            exit;
        }

        public static function ajax_get_mailing_list_markup()
        {
            $data = self::get_request_data();

            if (!$data['contest-beast-is-edit-page']) {
                $old_settings = self::get_settings();
                $settings = apply_filters('contest_beast_pre_settings_save', $data['contest-beast'], $old_settings);
                if ($settings['aweber-authorization'] != $old_settings['aweber-authorization'] && !empty($settings['aweber-tokens'])) {
                    $old_settings['aweber-authorization'] = $settings['aweber-authorization'];
                    $old_settings['aweber-tokens'] = $settings['aweber-tokens'];
                    self::set_settings($old_settings);
                }

                $credential = 'aweber' === $settings['mailing-list-provider'] ? $settings['aweber-tokens'] : $settings['mailchimp-api-key'];
            } else {
                $settings = $data['contest-beast'];
                $credential = false;
            }

            self::display_mailing_list_markup($settings['mailing-list-provider'], $settings['mailing-list-list'], $settings['mailing-list-name'], $credential);

            exit;
        }

        public static function ajax_replace_winner()
        {
            $data = self::get_request_data();
            $contest_id = $data['contest-id'];
            $submission_id = $data['submission-id'];

            self::disqualify_submission($submission_id);
            self::choose_winners($contest_id, 1, self::get_meta($contest_id, 'winner-type'));
            self::display_contest_winners_markup($contest_id);
            exit;
        }

        /// CALLBACKS

        public static function add_administrative_interface_items()
        {
            self::$admin_page_hooks[] = $settings = add_submenu_page(sprintf('edit.php?post_type=%s', self::TYPE_CONTEST), __('Contest Beast - Settings'), __('Settings'), 'manage_options', self::SETTINGS_PAGE_SLUG, array(__CLASS__, 'display_settings_page'));

            add_action("load-{$settings}", array(__CLASS__, 'process_settings_save'));
        }

        public static function add_meta_boxes($post)
        {
            add_meta_box('contest-beast-contest-details', __('Contest Details'), array(__CLASS__, 'display_contest_details_meta_box'), $post->post_type, 'normal');
            add_meta_box('contest-beast-contest-winners', __('Contest Winners'), array(__CLASS__, 'display_contest_winners_meta_box'), $post->post_type, 'normal');
        }

        public static function add_post_thumbnail_support()
        {
            add_theme_support('post-thumbnails');
        }

        public static function add_query_vars($vars)
        {
            $vars[] = 'action';
            $vars[] = 'cbid';
            $vars[] = 'cdr';

            return $vars;
        }


        public static function add_rewrite_rules($rewrite_rules)
        {
            $new = array(
                '~cd(.*)$' => 'index.php?cdr=$matches[1]',
                'my-account/([^/]+)/([^/]+)/?$' => 'index.php?pagename=my-account&action=$matches[1]&cbid=$matches[2]'
            );

            return $new + $rewrite_rules;
        }

        public function contest_my_account()
        {
            if (get_query_var('action') != "") {
                echo get_query_var('action');
            }
        }


        /* ---------------------- */
        /* 	function themeslug_query_vars( $qvars ) {
                $qvars[] = 'dealer';
                return $qvars;
            }

            add_filter( 'query_vars', 'themeslug_query_vars' , 10, 1 );

            function add_rewrite_rules($aRules) {
            $aNewRules = array(
                'dealer-profile/([^/]+)/?$' => 'index.php? pagename=dealer-profile&dealer=$matches[1]'
            );
            $aRules = $aNewRules + $aRules;
            return $aRules;
            }
            add_filter('rewrite_rules_array', 'add_rewrite_rules'); */
        /* ------------------------------ */

        public static function add_settings_link($links)
        {
            $settings_link = sprintf('<a href="%s">%s</a>', add_query_arg(array('page' => self::SETTINGS_PAGE_SLUG, 'post_type' => self::TYPE_CONTEST), admin_url('edit.php')), __('Settings'));

            return array('settings' => $settings_link) + $links;
        }

        public static function correct_template($template)
        {

            if (contest_beast_is_contest()) {

                $data = self::get_request_data();
                $possible_directories = array(STYLESHEETPATH, TEMPLATEPATH, path_join(dirname(__FILE__), 'views/frontend/templates'));

                if (1 == $data['contest-ppb']) {
                    $template_name = 'contest-ppb.php';
                } else if (1 == $data['contest-tos']) {
                    $template_name = 'contest-tos.php';
                } else if (1 == $data['contest-only-rules']) {
                    $template_name = 'contest-only-rules.php';
                } else if (contest_beast_has_entered()) {
                    $template_name = 'contest-after-entry.php';
                } else if (contest_beast_has_ended()) {
                    $template_name = 'contest-after-end.php';
                } else if (contest_beast_has_started()) {
                    $template_name = 'contest-after-start.php';
                } else {
                    $template_name = 'contest-after-creation.php';
                }

                foreach ($possible_directories as $possible_directory) {
                    $template_path = path_join($possible_directory, $template_name);
                    if (file_exists($template_path)) {
                        $template = $template_path;
                        break;
                    }
                }
            }
            return $template;
        }

        public function contest_template_dashboard_before_head()
        {
            echo "before head";
        }

        public static function custom_column_additions($columns)
        {
            $columns['list-name'] = __('List Name');
            $columns['start'] = __('Start');
            $columns['end'] = __('End');
            $columns['submissions'] = __('Submissions');
            $columns['entries'] = __('Entries');
            $columns['number-winners'] = __('Number of Winners');
            $columns['winners'] = __('Winners');

            return $columns;
        }

        public static function custom_column_output($column_name, $post_id)
        {
            $settings = self::get_settings();

            switch ($column_name) {
                case 'list-name' :
                    $add_to_mailing_list = 'yes' === self::get_meta($post_id, 'add-to-mailing-list');

                    if (!$add_to_mailing_list) {
                        _e('None');
                    } else if ($add_to_mailing_list && in_array($settings['mailing-list-provider'], array('aweber', 'mailchimp'))) {
                        $lists = 'aweber' === $settings['mailing-list-provider'] ? self::get_aweber_lists() : self::get_mailchimp_lists();

                        $list_key = self::get_meta($post_id, 'mailing-list-list');
                        if (isset($lists[$list_key])) {
                            esc_html_e($lists[$list_key]);
                        } else {
                            _e('None');
                        }
                    } else if ($add_to_mailing_list && 'other' === $settings['mailing-list-provider']) {
                        _e('Defined in Settings');
                    }

                    break;
                case 'start' :
                    contest_beast_the_contest_start_date($post_id);
                    break;
                case 'end' :
                    contest_beast_the_contest_end_date($post_id);
                    break;
                case 'submissions' :
                    contest_beast_the_contest_number_submissions($post_id);

                    $nonce_url = add_query_arg(array('export-contest-beast-entries-nonce' => wp_create_nonce("export-contest-beast-entries-{$post_id}"), 'ID' => $post_id), admin_url('admin.php'));
                    printf(' - <a href="%s">%s</a>', $nonce_url, __('Export Entrants'));
                    break;
                case 'entries' :
                    contest_beast_the_contest_number_entries($post_id);
                    break;
                case 'number-winners' :
                    contest_beast_the_contest_number_winners($post_id);
                    break;
                case 'winners' :
                    $winners = contest_beast_get_contest_winners($post_id);

                    if (!contest_beast_has_ended($post_id)) {
                        _e('N/A');
                    } else {
                        self::display_contest_winners_markup($post_id);
                    }
                    break;
            }
        }

        public static function detect_submissions()
        {
            $data = self::get_request_data();

            $contest_entry = $data['contest-entry'];
            //print_r($contest_entry);

            $nonce_action = "contest-beast-enter-contest-{$contest_entry['contest-id']}";

            if (is_array($contest_entry) && isset($contest_entry['contest-id']) && isset($data["{$nonce_action}-nonce"]) && wp_verify_nonce($data["{$nonce_action}-nonce"], $nonce_action)) {
                self::_process_entry($contest_entry);
            }


        }


        public static function enqueue_administrative_resources($hook)
        {
            if (!in_array($hook, self::$admin_page_hooks) || (in_array($hook, array('post.php', 'post-new.php', 'edit.php')) && self::TYPE_CONTEST !== get_post_type())) {
                return;
            }

            wp_enqueue_style('contest-beast-jquery-ui', plugins_url('resources/vendor/jquery-ui/css/jquery-ui.css', __FILE__), array());
            wp_enqueue_script('contest-beast-jquery-ui-timepicker', plugins_url('resources/vendor/jquery-ui-timepicker/timepicker.js', __FILE__), array('jquery-ui-slider', 'jquery-ui-datepicker'));

            wp_enqueue_script('contest-beast-backend', plugins_url('resources/backend/contest-beast.js', __FILE__), array('jquery', 'jquery-form', 'contest-beast-jquery-ui-timepicker'), self::VERSION);
            wp_enqueue_style('contest-beast-backend', plugins_url('resources/backend/contest-beast.css', __FILE__), array(), self::VERSION);


        }

        public static function output_background_image_style()
        {
            $skin = self::get_settings('skin');
            $skins = self::get_skins();

            if ($skins && 'default' !== $skin && '' !== $skin) {
                printf('<style type="text/css">body { background-image: url(%s); }</style>', plugins_url(sprintf('resources/skins/%s', $skin), __FILE__));

                if (0 === strpos($skin, 'dark/')) {
                    echo '<style type="text/css">.contest-disclaimer,.contest-disclaimer a,.contest-powered,.contest-powered a { color: #eeeeee; }</style>';
                }
            }
        }

        public static function process_export()
        {
            $data = self::get_request_data();

            if (!empty($data['export-contest-beast-entries-nonce']) && !empty($data['ID']) && wp_verify_nonce($data['export-contest-beast-entries-nonce'], "export-contest-beast-entries-{$data['ID']}")) {
                $entries = array();

                $contest = get_post($data['ID']);
                if (self::TYPE_CONTEST !== $contest->post_type) {
                    wp_die(__('You selected an invalid contest. Please go back and select a valid contest.'));
                }

                set_time_limit(0);
                $filename = sanitize_title_with_dashes(sprintf('%s Entries Report', $contest->post_title)) . '.csv';

                header('Content-Type: text/csv');
                header(sprintf('Content-Disposition: attachment; filename="%s"', $filename));

                echo implode(',', array('Name', 'Email', 'Date', 'Entries')) . "\n";
                foreach (self::get_contest_submissions($contest->ID) as $submission) {
                    echo implode(',', get_object_vars($submission)) . "\n";
                }

                exit;
            }
        }

        public static function process_settings_save()
        {
            $data = self::get_request_data();

            if (!empty($data['save-contest-beast-settings-nonce']) && wp_verify_nonce($data['save-contest-beast-settings-nonce'], 'save-contest-beast-settings')) {
                $settings = apply_filters('contest_beast_pre_settings_save', $data['contest-beast'], self::get_settings());
                $settings = self::set_settings($settings);

                $setting_errors = self::get_setting_errors();

                if (!is_wp_error($setting_errors) || !$setting_errors->get_error_code()) {
                    add_settings_error('general', 'settings_updated', __('Settings saved.'), 'updated');
                    set_transient('settings_errors', get_settings_errors(), 30);
                }

                wp_redirect(add_query_arg(array('page' => self::SETTINGS_PAGE_SLUG, 'post_type' => self::TYPE_CONTEST, 'settings-updated' => 'true'), admin_url('edit.php')));
                exit;
            }
        }

        public static function redirect_referral($wp)
        {
            if (isset($wp->query_vars['cdr']) && !empty($wp->query_vars['cdr'])) {
                $referring_submission = self::get_submission_by_referral_code($wp->query_vars['cdr']);

                if (false === $referring_submission) {
                    $redirect = home_url('/');
                } else {
                    $redirect = add_query_arg(array('ref' => $referring_submission->submission_code), get_permalink($referring_submission->submission_contest_ID));
                }

                wp_redirect($redirect);
                exit;
            }
        }

        public static function register_content_types()
        {
            self::_register_contest();
        }

        public static function sanitize_meta($meta, $old_meta, $post_id)
        {
            $meta = pd_trim_r($meta);
            $meta_errors = new WP_Error;

            $meta['start-timestamp'] = strtotime("{$meta['start-date']} {$meta['start-time-hour']}:{$meta['start-time-minute']} {$meta['start-time-meridian']}");
            $meta['end-timestamp'] = strtotime("{$meta['end-date']} {$meta['end-time-hour']}:{$meta['end-time-minute']} {$meta['end-time-meridian']}");

            if ($meta['end-timestamp'] < $meta['start-timestamp']) {
                $meta_errors->add('end-timestamp', __('You cannot end the contest before it begins.'));
            }

            $number_keys = array('number-entries', 'number-winners');
            foreach ($number_keys as $field_key) {
                if (!is_numeric($meta[$field_key])) {
                    $meta[$field_key] = self::$default_meta[$field_key];

                    $meta_errors->add($field_key, __('You provided an invalid non-numeric value. The default value has been used.'));
                } else if ($meta[$field_key] < 0) {
                    $meta[$field_key] = self::$default_meta[$field_key];

                    $meta_errors->add($field_key, __('You provided an invalid numeric value less than 0. The default value has been used.'));
                }

                $meta[$field_key] = intval($meta[$field_key]);
            }

            $meta['contest-disclaimer'] = wp_kses_post($meta['contest-disclaimer']);
            $meta['contest-rules-custom'] = pd_yes_no($meta['contest-rules-custom']);
            $meta['contest-rules'] = wp_kses_post($meta['contest-rules']);

            // Moderni Contest TOS
            $meta['contest-termsofservice'] = wp_kses_post($meta['contest-termsofservice']);
            $meta['contest-privacypolicybox'] = wp_kses_post($meta['contest-privacypolicybox']);

            $meta['tracking-scripts-custom'] = pd_yes_no($meta['tracking-scripts-custom']);

            $meta['add-to-mailing-list'] = pd_yes_no($meta['add-to-mailing-list']);

            self::set_meta_errors($post_id, $meta_errors);

            return $meta;
        }

        public static function sanitize_settings($settings, $old_settings)
        {
            $settings = pd_trim_r($settings);
            $setting_errors = new WP_Error;

            /** Check for Numeric Values on Defaults **/
            $default_number_keys = array('default-number-entries', 'default-number-winners');
            foreach ($default_number_keys as $field_key) {
                if (!is_numeric($settings[$field_key])) {
                    $settings[$field_key] = self::$default_settings[$field_key];

                    $setting_errors->add($field_key, __('You provided an invalid non-numeric value. A default value has been used.'));
                } else if ($settings[$field_key] < 0) {
                    $settings[$field_key] = self::$default_settings[$field_key];

                    $setting_errors->add($field_key, __('You provided an invalid numeric value less than 0. A default value has been used.'));
                }

                $settings[$field_key] = intval($settings[$field_key]);
            }

            if ('upload' === $settings['branding-logo-action'] && !empty($_FILES['contest-beast-branding-logo-file']['size'])) {
                $settings['branding-logo-url'] = '';

                $file_upload_result = wp_handle_upload($_FILES['contest-beast-branding-logo-file'], array('test_form' => false));
                if (!empty($file_upload_result['url'])) {
                    $image_size_result = @getimagesize($file_upload_result['file']);
                    if (false === $image_size_result) {
                        $setting_errors->add('branding-logo-file', __('Please upload a valid image filetype.'));
                    } else {
                        $settings['branding-logo-url'] = $file_upload_result['url'];
                    }
                } else if (!empty($file_upload_result['error'])) {
                    $setting_errors->add('branding-logo-file', $file_upload_result['error']);
                }
            }

            $settings['default-contest-rules'] = wp_kses_post($settings['default-contest-rules']);
            $settings['promote-contest-beast'] = pd_yes_no($settings['promote-contest-beast']);
            $settings['clickbank-id'] = preg_replace('/[^a-zA-Z0-9\-\_\.]/', '', $settings['clickbank-id']);

            $settings['twitter-handle'] = strip_tags($settings['twitter-handle']);
            $settings['facebook-method'] = 'like' === $settings['facebook-method'] ? 'like' : 'subscribe';
            $settings['facebook-like-url'] = strip_tags($settings['facebook-like-url']);
            $settings['facebook-profile-url'] = strip_tags($settings['facebook-profile-url']);

            $mailing_list_providers = array('none', 'aweber', 'mailchimp', 'other');
            if (!in_array($settings['mailing-list-provider'], $mailing_list_providers)) {
                $settings['mailing-list-provider'] = self::$default_settings['mailing-list-provider'];

                $setting_errors->add('mailing-list-provider', __('You specified an invalid mailing list provider. Mailing list integration has been turned off.'));
            }

            /** Parse the AWeber Tokens **/
            if ((empty($old_settings['aweber-tokens']) && !empty($settings['aweber-authorization'])) || $settings['aweber-authorization'] != $old_settings['aweber-authorization']) {
                self::_aweber_api();

                $settings['aweber-tokens'] = AWeberAPI::getDataFromAweberID($settings['aweber-authorization']);

                if (empty($settings['aweber-tokens'])) {
                    $setting_errors->add('aweber-authorization', __('The AWeber Authorization string you supplied was invalid. Please click the link above and authorize again.'));
                }
            } else {
                $settings['aweber-tokens'] = $old_settings['aweber-tokens'];
            }

            /** Check the MailChimp API **/
            if ($settings['mailchimp-api-key'] != $old_settings['mailchimp-api-key']) {
                $mailchimp_api = self::_mailchimp_api($settings['mailchimp-api-key']);
                $account_details = $mailchimp_api->getAccountDetails();

                if (empty($account_details) || !isset($account_details['user_id'])) {
                    $setting_errors->add('mailchimp-api-key', __('The MailChimp API Key you provided was invalid and could not be used to access your account. Please click the link above and copy a valid API key.'));
                }
            }

            $settings['opt-in-form-fields-hidden'] = is_array($settings['opt-in-form-fields-hidden']) ? $settings['opt-in-form-fields-hidden'] : array();
            $settings['opt-in-form-fields'] = is_array($settings['opt-in-form-fields']) ? $settings['opt-in-form-fields'] : array();
            $settings['opt-in-form-disable-name'] = pd_yes_no($settings['opt-in-form-disable-name']);

            self::set_setting_errors($setting_errors);
            return $settings;
        }

        public static function save_post_meta($post_id, $post)
        {
            $data = self::get_request_data();
            if (wp_is_post_autosave($post_id) || wp_is_post_revision($post_id) || !wp_verify_nonce($data['save-contest-beast-meta-nonce'], 'save-contest-beast-meta')) {
                return;
            }

            $meta = apply_filters('contest_beast_pre_meta_save', $data['contest-beast'], self::get_meta($post_id), $post_id);
            $meta = self::set_meta($post_id, $meta);
            $meta_errors = self::get_meta_errors($post_id);
        }

        /// DISPLAY CALLBACKS

        public static function display_contest_details_meta_box($post)
        {
            $errors = self::get_meta_errors($post->ID);
            $meta = self::get_meta($post->ID);
            $settings = self::get_settings();

            include('views/backend/meta-boxes/contest-details.php');
        }

        public static function display_contest_winners_meta_box($post)
        {
            $meta = self::get_meta($post->ID);
            $contest_ended = contest_beast_has_ended($post->ID);
            $contest_winners = contest_beast_get_contest_winners($post->ID);

            include('views/backend/meta-boxes/contest-winners.php');
        }

        public static function display_contest_winners_markup($contest_id)
        {
            $contest_submissions = contest_beast_get_contest_number_submissions($contest_id);
            $contest_winners = contest_beast_get_contest_winners($contest_id);
            $number_winners = contest_beast_get_contest_number_winners($contest_id);

            include('views/backend/misc/winner-list.php');
        }

        public static function display_mailing_list_markup($mailing_list_provider, $mailing_list, $field_name, $credential = false)
        {
            $lists = 'aweber' === $mailing_list_provider ? self::get_aweber_lists($credential) : self::get_mailchimp_lists($credential);
            $fields = 'aweber' === $mailing_list_provider || false === $lists ? false : self::get_mailchimp_fields(isset($lists[$mailing_list]) ? $mailing_list : key($lists), $credential);

            include('views/backend/mailing-list/field-rows.php');
        }

        public static function display_settings_page()
        {
            $errors = self::get_setting_errors();
            $settings = self::get_settings();

            if (is_wp_error($errors)) {
                add_settings_error('general', 'settings_updated', __('Some errors were detected in your settings. Your contests will still work, but please check the items below and correct them.'), 'error');
            }

            $active_contest = false;
            $all_contests = get_posts(array('post_type' => self::TYPE_CONTEST, 'post_status' => 'publish', 'nopaging' => true));
            foreach ($all_contests as $a_contest) {
                if (contest_beast_has_started($a_contest->ID) && !contest_beast_has_ended($a_contest->ID)) {
                    $active_contest = true;
                    break;
                }
            }

            $skins = self::get_skins();
            $default_skin_url = plugins_url('views/frontend/templates/resources/img/screenshot.jpg', __FILE__);

            include('views/backend/settings/settings.php');
        }

        public static function display_third_party_form($form_action, $hidden_fields, $email_field_name, $email, $name_field_name, $name)
        {
            include('views/backend/misc/third-party-form.php');
        }

        /// PROCESSING AND REGISTRATION

        private static function _process_entry($contest_entry)
        {
            $entry_errors = new WP_Error;

            global $current_user;
            get_currentuserinfo();

            $contest_id = $contest_entry['contest-id'];
            $email = $contest_entry['email'];
            $ip_address = $_SERVER['REMOTE_ADDR'];
            $name = $contest_entry['name'];
            $referral_code = $contest_entry['referral-code'];

            if (empty($name)) {
                $entry_errors->add('name', __('Please provide a name'));
            }

            if (empty($email) || !is_email($email)) {
                $entry_errors->add('email', __('Please provide a valid email'));
            }

            if (!$entry_errors->get_error_code()) {
                $meta = self::get_meta($contest_id);
                $author_details = self::get_author_details($contest_id);
                $settings = self::get_settings();
                $submission = self::get_submission($contest_id, $email);

                self::set_cookie_entry_email($contest_id, $email);

                // echo "<pre>";
                // print_r($submission);

                if (false === $submission) {
                    $referral_id = 0;
                    if (!empty($referral_code)) {
                        $referral_submission = self::get_submission_by_referral_code($referral_code);
                        if (false !== $referral_submission) {
                            $referral_id = $referral_submission->submission_ID;

                            $referral_entries = self::add_entries($referral_id, true, $meta['number-entries']);
                        }
                    }

                    $submission = self::add_submission($contest_id, $email, $ip_address, $name, self::get_unique_referral_code(), $referral_id);
                    $entries = self::add_entries($submission->submission_ID, false, 1);

                    $contest_data_meta = get_post_meta($contest_id);

                    $_contest_beast_post_meta = unserialize($contest_data_meta['_contest_beast_post_meta'][0]);

                    /*
                    if ('yes' === $meta['add-to-mailing-list']) { // PhungTran
                        if(isset($meta['mailing-list-data']['action'])) {
                            self::add_to_mailing_list('custom', $meta['mailing-list-list'], $meta['mailing-list-name'], $email, $ip_address, $name, $meta['mailing-list-data']);
                        } else {
                            self::add_to_mailing_list($settings['mailing-list-provider'], $meta['mailing-list-list'], $meta['mailing-list-name'], $email, $ip_address, $name);
                        }
                    } */


                    if ($_contest_beast_post_meta['add-to-mailing-list'] == 'on') {

                        if ($_contest_beast_post_meta['enable-mailing-list-aweber'] == "1") {
                            self::update_aweber_contact_data([ 'user_id'=>$author_details['ID'],'name'=>'','email'=>$email]);
                        }

                        $mc_data['email'] = $email;

                        if ($_contest_beast_post_meta['mail_mailchimp_list'] != "" && $_contest_beast_post_meta['anable-mailing-list-mailchimp'] == "yes") {
                            $mailchimp_api = get_user_meta($author_details['ID'], '_mailchimApi_api', true);


                            $MailChimp = new MailChimp($mailchimp_api);

                            $list_id = $_contest_beast_post_meta['mail_mailchimp_list'];

                            $result = $MailChimp->post("lists/$list_id/members", [
                                'email_address' => $email,
                                'description' => "Subscribe from contest buszz",
                                'status' => 'subscribed',
                            ]);

                            $subscribe = "[{subscriber:'mailchimp',email:'$email',api:'$mailchimp_api {$current_user->ID}'}]";
                            $subscribe_param = "?subscribe=" . base64_encode($subscribe);

                        }


                        if ($_contest_beast_post_meta['mail_getresponse_list'] != "" && $_contest_beast_post_meta['anable-mailing-list-getresponse'] == "yes") {

                            $getreponseApi_api = get_user_meta($author_details['ID'], '_getreponseApi_api', true);
                            $lists = get_user_meta($author_details['ID'], '_getreponseApi_list', true);

                            if (preg_match('/"' . $_contest_beast_post_meta['mail_getresponse_list'] . '"/i', json_encode($lists))) {
                                $arr_pos = array_search($_contest_beast_post_meta['mail_getresponse_list'], $lists);
                                $list_id = $lists[$arr_pos]['list_id'];
                                $list_name = $lists[$arr_pos]['list_name'];
                            }

                            $getresponse = new GetResponse($getreponseApi_api);
                            $getresponse->enterprise_domain = 'staging.contestbuzz.net';

                            //$list_id = $_contest_beast_post_meta['mail_getresponse_list'];

                            $getresponse->addContact(array(
                                'name' => $list_name,
                                'email' => $email,
                                'dayOfCycle' => 10,
                                'campaign' => array('campaignId' => $list_id),
                                'ipAddress' => $_SERVER['REMOTE_ADDR']
                            ));

                        }
                    }
                }

                wp_redirect(get_permalink($contest_id));
                exit;
            } else {
                self::$entry_errors = $entry_errors;
            }
        }

        /**
         * Add contact to Aweber
         *
         * @param array $userData
         *
         * returns void
         */
        private static function update_aweber_contact_data($userData)
        {
            try {
                /* include Aweber lib */
                include_once CONTEST_LIB_DIR . 'aweber/aweber.class.php';

                $data = array();
                $data['email'] = $userData['email'];
                $data['name'] = $userData['name'];

                $Contest_AweberSubscription = new Contest_AweberSubscription($userData['user_id']);
                $Contest_AweberSubscription->addSubscriber($data);
            } catch (Exception $e) {
                echo $e;
            }
        }

        private static function _register_contest()
        {
            $content_type_labels = array('name' => __('Contests'), 'singular_name' => __('Contest'), 'add_new' => __('Add New'), 'add_new_item' => __('Add New Contest'), 'edit_item' => __('Edit Contest'), 'new_item' => __('New Contest'), 'view_item' => __('View Contest'), 'search_items' => __('Search Contests'), 'not_found' => __('No contests found.'), 'not_found_in_trash' => __('No contests found in Trash.'), 'parent_item_colon' => null, 'all_items' => __('All Contests'),);
            $content_type_options = array('labels' => $content_type_labels, 'description' => __('Contests allow you to generate interest and add users to your mailing list!'), 'publicly_queryable' => true, 'exclude_from_search' => true, 'capability_type' => 'post', 'capabilities' => array(), 'map_meta_cap' => null, 'hierarchical' => true, 'public' => true, 'rewrite' => true, 'has_archive' => false, 'query_var' => true, 'supports' => array('title', 'editor', 'excerpt', 'thumbnail'), 'register_meta_box_cb' => array(__CLASS__, 'add_meta_boxes'), 'taxonomies' => array(), 'show_ui' => true, 'menu_position' => null, 'menu_icon' => null, 'permalink_epmask' => EP_PERMALINK, 'can_export' => true, 'show_in_nav_menus' => true, 'show_in_menu' => true, 'show_in_admin_bar' => null,);

            register_post_type(self::TYPE_CONTEST, $content_type_options);
        }

        /// POST META

        private static function get_meta_errors($post_id)
        {
            if (empty($post_id)) {
                $post_id = get_the_ID();
            }

            $meta_errors = wp_cache_get(self::POST_META_ERRORS_KEY, $post_id);

            if (false === $meta_errors) {
                $meta_errors = get_post_meta($post_id, self::POST_META_ERRORS_KEY, 'no');
                wp_cache_set(self::POST_META_ERRORS_KEY, $meta_errors, $post_id, time() + self::CACHE_PERIOD);
            }

            return is_wp_error($meta_errors) ? $meta_errors : false;
        }

        private static function set_meta_errors($post_id, $meta_errors)
        {
            if (empty($post_id)) {
                $post_id = get_the_ID();
            }

            if (is_wp_error($meta_errors) && $meta_errors->get_error_code()) {
                update_post_meta($post_id, self::POST_META_ERRORS_KEY, $meta_errors);
                wp_cache_set(self::POST_META_ERRORS_KEY, $meta_errors, $post_id, time() + self::CACHE_PERIOD);
            } else {
                delete_post_meta($post_id, self::POST_META_ERRORS_KEY);
                wp_cache_delete(self::POST_META_ERRORS_KEY, $post_id);
            }
        }

        private static function get_author_details($post_id)
        {
            $auth = get_post($post_id); // gets author from post
            $author_meta['ID'] = $authid = $auth->post_author; // gets author id for the post
            $author_meta['user_email'] = get_the_author_meta('user_email', $authid); // retrieve user email
            $author_meta['user_firstname'] = get_the_author_meta('user_firstname', $authid); // retrieve firstname
            $author_meta['user_nickname'] = get_the_author_meta('nickname', $authid); // retrieve user nickname
            return $author_meta;
        }

        private static function get_meta($post_id, $meta_item = null)
        {
            if (empty($post_id)) {
                $post_id = get_the_ID();
            }

            $meta = wp_cache_get(self::POST_META_KEY, $post_id);

            if (!is_array($meta)) {
                $meta = wp_parse_args((array)get_post_meta($post_id, self::POST_META_KEY, true), self::$default_meta);

                $settings = self::get_settings();
                if ($settings['mailing-list-provider'] != $meta['mailing-list-provider']) {
                    $meta['mailing-list-list'] = $settings['mailing-list-list'];
                    $meta['mailing-list-name'] = $settings['mailing-list-name'];
                }

                wp_cache_set(self::POST_META_KEY, $meta, $post_id, time() + self::CACHE_PERIOD);
            }

            return null === $meta_item ? $meta : $meta[$meta_item];
        }

        private static function set_meta($post_id, $meta)
        {
            if (is_array($meta)) {
                if (empty($post_id)) {
                    $post_id = get_the_ID();
                }

                $meta = wp_parse_args($meta, self::$default_meta);
                update_post_meta($post_id, self::POST_META_KEY, $meta);
                wp_cache_set(self::POST_META_KEY, $meta, $post_id, time() + self::CACHE_PERIOD);
            }

            return $meta;
        }

        /// SETTINGS

        private static function get_setting_errors()
        {
            $setting_errors = wp_cache_get(self::SETTING_ERRORS_KEY);

            if (false === $setting_errors) {
                $setting_errors = get_option(self::SETTING_ERRORS_KEY, 'no');
                wp_cache_set(self::SETTING_ERRORS_KEY, $setting_errors, null, time() + self::CACHE_PERIOD);
            }

            return is_wp_error($setting_errors) ? $setting_errors : false;
        }

        private static function set_setting_errors($setting_errors)
        {
            if (is_wp_error($setting_errors) && $setting_errors->get_error_code()) {
                update_option(self::SETTING_ERRORS_KEY, $setting_errors);
                wp_cache_set(self::SETTING_ERRORS_KEY, $setting_errors, null, time() + self::CACHE_PERIOD);
            } else {
                delete_option(self::SETTING_ERRORS_KEY);
                wp_cache_delete(self::SETTING_ERRORS_KEY);
            }
        }

        private static function get_settings($setting_item = null)
        {
            $settings = wp_cache_get(self::SETTINGS_KEY);

            if (!is_array($settings)) {
                $settings = wp_parse_args(get_option(self::SETTINGS_KEY, self::$default_settings), self::$default_settings);
                wp_cache_set(self::SETTINGS_KEY, $settings, null, time() + self::CACHE_PERIOD);
            }

            return null === $setting_item ? $settings : $settings[$setting_item];
        }

        private static function set_settings($settings)
        {
            if (is_array($settings)) {
                $settings = wp_parse_args($settings, self::$default_settings);
                update_option(self::SETTINGS_KEY, $settings);
                wp_cache_set(self::SETTINGS_KEY, $settings, null, time() + self::CACHE_PERIOD);
            }
        }

        /// SKINS

        private static function get_skins()
        {
            $skins_directory = path_join(dirname(__FILE__), 'resources/skins/');
            $skins_directory_dark = path_join($skins_directory, 'dark/');
            $skins_directory_light = path_join($skins_directory, 'light/');

            if (is_dir($skins_directory)
                && is_readable($skins_directory)
                && ((is_dir($skins_directory_dark) && is_readable($skins_directory_dark))
                    || (is_dir($skins_directory_light) && is_readable($skins_directory_light)))
            ) {

                $dark = array();
                $dark_directory_iterator = new DirectoryIterator($skins_directory_dark);
                foreach ($dark_directory_iterator as $file_information) {
                    if ($file_information->isFile()) {
                        $filename = $file_information->getFilename();

                        if (false !== strpos($filename, 'preview')) {
                            continue;
                        }

                        $extension_parts = explode('.', $filename);
                        $extension = array_pop($extension_parts);

                        $filename_no_extension = str_replace(".{$extension}", '', $filename);

                        $name = ucwords(str_replace(array('-', '_'), ' ', $filename_no_extension));
                        $path = sprintf('dark/%s', $filename);
                        $preview_url = plugins_url(sprintf('resources/skins/%s', "dark/{$filename_no_extension}-preview.{$extension}"), __FILE__);
                        $url = plugins_url(sprintf('resources/skins/%s', $path), __FILE__);

                        $dark[] = compact('name', 'path', 'preview_url', 'url');
                    }
                }

                $light = array();
                $light_directory_iterator = new DirectoryIterator($skins_directory_light);
                foreach ($light_directory_iterator as $file_information) {
                    if ($file_information->isFile()) {
                        $filename = $file_information->getFilename();

                        if (false !== strpos($filename, 'preview')) {
                            continue;
                        }

                        $extension_parts = explode('.', $filename);
                        $extension = array_pop($extension_parts);

                        $filename_no_extension = str_replace(".{$extension}", '', $filename);

                        $name = ucwords(str_replace(array('-', '_'), ' ', $filename_no_extension));
                        $path = sprintf('light/%s', $filename);
                        $preview_url = plugins_url(sprintf('resources/skins/%s', "light/{$filename_no_extension}-preview.{$extension}"), __FILE__);
                        $url = plugins_url(sprintf('resources/skins/%s', $path), __FILE__);

                        $light[] = compact('name', 'path', 'preview_url', 'url');
                    }
                }

                return compact('dark', 'light');
            } else {
                return false;
            }
        }

        /// MAILING LISTS

        //// AWEBER

        private static function _aweber_api()
        {
            require_once('lib/aweber/aweber_api.php');
        }

        /**
         * @return AWeberAPI
         */
        private static function _aweber_account($authorization_tokens = false)
        {
            self::_aweber_api();

            try {
                $authorization_tokens = false === $authorization_tokens ? self::get_settings('aweber-tokens') : $authorization_tokens;

                if (empty($authorization_tokens) || !is_array($authorization_tokens)) {
                    $result = new WP_Error('aweber_api_credentials_invalid', __('The authorization code provided is invalid.'));
                } else {
                    list($consumer_key, $consumer_secret, $access_token, $access_secret) = $authorization_tokens;

                    $aweber_api = new AWeberAPI($consumer_key, $consumer_secret);
                    $aweber_account = $aweber_api->getAccount($access_token, $access_secret);

                    $result = $aweber_account;
                }
            } catch (Exception $e) {
                $result = new WP_Error($e->getCode(), $e->getMessage() . ' ' . $e->getTraceAsString());
            }

            return $result;
        }

        private static function add_aweber_subscriber($email, $name, $ip_address, $mailing_list, $authorization_tokens = false)
        {

            $aweber_account = self::_aweber_account($authorization_tokens);

            $result = false;
            if (!is_wp_error($aweber_account)) {
                try {
                    $list = $aweber_account->loadFromUrl("/accounts/{$aweber_account->id}/lists/{$mailing_list}");
                    if (!empty($list)) {
                        $subscriber = array('email' => $email, 'ip_address' => $ip_address, 'name' => $name);

                        $new_subscriber = $list->subscribers->create($subscriber);
                        $result = !empty($new_subscriber->email);
                    }
                } catch (Exception $e) {
                }
            }

            return $result;
        }

        private static function get_aweber_lists($authorization_tokens = false)
        {
            if (null === self::$aweber_lists) {
                $aweber_account = self::_aweber_account($authorization_tokens);

                if (is_wp_error($aweber_account)) {
                    self::$aweber_lists = false;
                } else {
                    self::$aweber_lists = array();

                    try {
                        foreach ($aweber_account->lists as $list) {
                            self::$aweber_lists[$list->id] = $list->name;
                        }
                    } catch (Exception $e) {
                        self::$aweber_lists = false;
                    }
                }
            }

            return self::$aweber_lists;
        }

        //// MAILCHIMP

        private static function _mailchimp_api($api_key = false)
        {
            require_once('lib/mailchimp/MCAPI.class.php');

            $api_key = false === $api_key ? self::get_settings('mailchimp-api-key') : $api_key;

            return new MCAPI($api_key, false);
        }

        private static function add_mailchimp_subscriber($email, $name, $ip_address, $mailing_list, $name_field, $api_key = false)
        {
            $mailchimp_api = self::_mailchimp_api($api_key);

            $subscribe_result = $mailchimp_api->listSubscribe($mailing_list, $email, array($name_field => $name));

            return !empty($subscribe_result);
        }

        private static function get_mailchimp_fields($list_id, $api_key = false)
        {
            if (empty($list_id)) {
                $fields = false;
            } else {
                $mailchimp_api = self::_mailchimp_api($api_key);
                $mailchimp_merge_vars = $mailchimp_api->listMergeVars($list_id);

                if (!is_array($mailchimp_merge_vars)) {
                    $fields = false;
                } else {
                    $fields = array();

                    foreach ($mailchimp_merge_vars as $field) {
                        $fields[$field['tag']] = $field['name'];
                    }
                }
            }

            return $fields;
        }

        private static function get_mailchimp_lists($api_key = false)
        {
            if (null === self::$mailchimp_lists) {
                $mailchimp_api = self::_mailchimp_api($api_key);
                $mailchimp_lists = $mailchimp_api->lists();

                if (!is_array($mailchimp_lists)) {
                    self::$mailchimp_lists = false;
                } else {
                    self::$mailchimp_lists = array();

                    foreach ($mailchimp_lists['data'] as $list) {
                        self::$mailchimp_lists[$list['id']] = $list['name'];
                    }
                }
            }

            return self::$mailchimp_lists;
        }

        /// UTILITY

        private static function create_database_tables()
        {
            if (!function_exists('dbDelta')) {
                require_once(ABSPATH . '/wp-admin/includes/upgrade.php');
            }

            global $charset_collate, $wpdb;

            $queries = array();

            $queries[] = "CREATE TABLE $wpdb->contest_beast_submissions (
			  submission_ID bigint(20) unsigned NOT NULL auto_increment,
			  submission_contest_ID bigint(20) unsigned NOT NULL default '0',
			  submission_referral_ID bigint(20) NOT NULL default '0',
			  submission_code varchar(20) NOT NULL default '',
			  submission_date datetime NOT NULL default '0000-00-00 00:00:00',
			  submission_email varchar(200) NOT NULL default '',
			  submission_IP varchar(100) NOT NULL default '',
			  submission_name varchar(200) NOT NULL default '',
			  submission_is_winner tinyint(1) NOT NULL default '0',
			  submission_is_disqualified tinyint(1) NOT NULL default '0',
			  PRIMARY KEY (submission_ID),
			  KEY submission_code (submission_code),
			  KEY submission_referral_ID (submission_referral_ID),
			  KEY submission_is_winner (submission_is_winner),
			  KEY submission_is_disqualified (submission_is_disqualified)
			) $charset_collate;";

            $queries[] = "CREATE TABLE $wpdb->contest_beast_entries (
			  entry_ID bigint(20) unsigned NOT NULL auto_increment,
			  entry_submission_ID bigint(20) unsigned NOT NULL default '0',
			  entry_was_referral tinyint(1) unsigned NOT NULL default '0',
			  PRIMARY KEY  (entry_ID),
			  KEY entry_submission_ID (entry_submission_ID)
			) $charset_collate;";

            dbDelta($queries);
        }

        private static function get_request_data()
        {
            if (null === self::$request_data) {
                self::$request_data = stripslashes_deep($_REQUEST);
            }

            return self::$request_data;
        }

        /// ENTRY DATA

        //// ADD

        private static function add_entries($submission_id, $was_referral, $number_entries = 1)
        {
            $number_entries = intval($number_entries);
            $submission_id = intval($submission_id);

            $entry_ids = array();
            if ($number_entries > 0 && $submission_id > 0) {
                global $wpdb;

                $entry_data = array('entry_submission_ID' => $submission_id, 'entry_was_referral' => $was_referral);
                $entry_data_formats = array('%d', '%d');
                for ($i = 0; $i < $number_entries; $i++) {
                    $wpdb->insert($wpdb->contest_beast_entries, $entry_data, $entry_data_formats);

                    $entry_ids[] = $wpdb->insert_id;
                }
            }

            $entry_ids = array_map('intval', $entry_ids);
            $entry_ids_in = implode(',', $entry_ids);

            return empty($entry_ids) ? array() : $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->contest_beast_entries} WHERE entry_ID IN ({$entry_ids_in})"));
        }

        private static function add_submission($contest_id, $email, $ip_address, $name, $referral_code, $referral_id)
        {
            global $wpdb;

            $submission_data = array('submission_contest_ID' => $contest_id, 'submission_referral_ID' => intval($referral_id), 'submission_code' => $referral_code, 'submission_date' => current_time('mysql'), 'submission_email' => $email, 'submission_IP' => $ip_address, 'submission_name' => $name);
            $submission_data_formats = array('%d', '%d', '%s', '%s', '%s', '%s', '%s');

            $wpdb->insert($wpdb->contest_beast_submissions, $submission_data, $submission_data_formats);

            return $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->contest_beast_submissions} WHERE submission_ID = %d", $wpdb->insert_id));
        }

        private static function add_mailing_list($mailing_list_data)
        {

            global $current_user;
            get_currentuserinfo();

            $mailchimp = get_user_meta($current_user->ID, '_mailchimApi_api', true);

            $MailChimp = new MailChimp($mailchimp);

            $list_id = $mailing_list_data['list_id'];

            $result = $MailChimp->post("lists/$list_id/members", [
                'email_address' => $mailing_list_data['email'],
                'status' => 'subscribed',
            ]);
            print_r($result);
            //return true;
        }

        private static function add_to_mailing_list($mailing_list_provider, $mailing_list, $name_field, $email, $ip_address, $name, $custom_mailing)
        {
            $result = false;
            switch ($mailing_list_provider) {
                case 'aweber' :
                    $result = self::add_aweber_subscriber($email, $name, $ip_address, $mailing_list);
                    break;
                case 'mailchimp' :
                    $result = self::add_mailchimp_subscriber($email, $name, $ip_address, $mailing_list, $name_field);
                    break;
                case 'other' :
                    $settings = self::get_settings();

                    $form_action = $settings['opt-in-form-url'];
                    $hidden_fields = (array)$settings['opt-in-form-fields-hidden'];
                    $email_field_name = $settings['opt-in-form-email-field'];
                    $name_field_name = 'yes' === $settings['opt-in-form-disable-name'] ? false : $settings['opt-in-form-name-field'];

                    if (!empty($form_action) && !empty($email_field_name)) {
                        self::display_third_party_form($form_action, $hidden_fields, $email_field_name, $email, $name_field_name, $name);
                        exit;
                    }
                    break;
                case 'custom' : // PhungTran
                    $form_action = $custom_mailing['action'];
                    $hidden_fields = (array)$custom_mailing['hidden'];
                    $email_field_name = $custom_mailing['email'];
                    // $name_field_name = 'yes' === $custom_mailing['opt-in-form-disable-name'] ? false : $custom_mailing['opt-in-form-name-field'];
                    $name_field_name = $custom_mailing['name'];

                    if (!empty($form_action) && !empty($email_field_name)) {
                        self::display_third_party_form($form_action, $hidden_fields, $email_field_name, $email, $name_field_name, $name);
                        exit;
                    }
                    break;
            }

            return $result;
        }

        //// GET

        private static function get_submission($contest_id, $email_address)
        {
            global $wpdb;

            $submission = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->contest_beast_submissions} WHERE submission_contest_ID = %d AND submission_email = %s", $contest_id, $email_address));
            if (is_object($submission)) {
                $submission->submission_entries_count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->contest_beast_entries} WHERE entry_submission_ID = %d", $submission->submission_ID));
            }

            return empty($submission) ? false : $submission;
        }

        private static function get_submission_by_referral_code($referral_code)
        {
            global $wpdb;

            $submission = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->contest_beast_submissions} WHERE submission_code = %s ", $referral_code));
            if (is_object($submission)) {
                $submission->submission_entries_count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->contest_beast_entries} WHERE entry_submission_ID = %d", $submission->submission_ID));
            }

            return empty($submission) ? false : $submission;
        }

        private static function get_referral_code($contest_id, $email_address)
        {
            if (empty($contest_id)) {
                $contest_id = get_the_ID();
            }

            if (empty($email_address)) {
                $email_address = $_COOKIE[self::get_cookie_entry_email_key($contest_id)];
            }

            $submission = self::get_submission($contest_id, $email_address);

            return false === $submission ? false : $submission->submission_code;
        }

        private static function get_unique_referral_code()
        {
            do {
                $referral_code = uniqid();

                $submission = self::get_submission_by_referral_code($referral_code);
            } while (false !== $submission);

            return $referral_code;
        }

        //// WINNERS

        private static function choose_winners($contest_id, $number_winners, $method = 'weighted')
        {
            global $wpdb;

            $winning_submissions = array();
            if ($number_winners > 0) {
                $number_winners = intval($number_winners);

                for ($i = 0; $i < $number_winners; $i++) {
                    if ('weighted' === $method) {
                        $winning_entry = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->contest_beast_entries} WHERE entry_submission_ID IN(SELECT submission_ID FROM {$wpdb->contest_beast_submissions} WHERE submission_contest_ID = %d AND submission_is_winner = 0 AND submission_is_disqualified = 0) ORDER BY RAND() LIMIT 1", $contest_id));
                    } else {
                        $winning_entry = $wpdb->get_row($wpdb->prepare("SELECT *, count(entry_ID) as entries FROM {$wpdb->contest_beast_entries} WHERE entry_submission_ID IN(SELECT submission_ID FROM {$wpdb->contest_beast_submissions} WHERE submission_contest_ID = %d AND submission_is_winner = 0 AND submission_is_disqualified = 0) GROUP BY entry_submission_ID ORDER BY entries DESC LIMIT 1", $contest_id));
                    }

                    if (empty($winning_entry)) {
                        break;
                    } else {
                        $wpdb->query($wpdb->prepare("UPDATE {$wpdb->contest_beast_submissions} SET submission_is_winner = 1 WHERE submission_ID = %d", $winning_entry->entry_submission_ID));
                        $winning_submissions[] = $winning_submission = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->contest_beast_submissions} WHERE submission_ID = %d", $winning_entry->entry_submission_ID));
                    }
                }
            }

            return array_filter($winning_submissions);
        }

        private static function disqualify_submission($submission_id)
        {
            global $wpdb;

            $wpdb->query($wpdb->prepare("UPDATE {$wpdb->contest_beast_submissions} SET submission_is_winner = 0, submission_is_disqualified = 1 WHERE submission_ID = %d", $submission_id));
        }

        /// COOKIES

        private static function get_cookie_entry_email_key($contest_id)
        {
            return self::COOKIE_ENTRY_EMAIL_KEY . $contest_id;
        }

        private static function get_cookie_entry_email($contest_id)
        {
            return '' == $_COOKIE[self::get_cookie_entry_email_key($contest_id)] ? false : $_COOKIE[self::get_cookie_entry_email_key($contest_id)];
        }

        private static function set_cookie_entry_email($contest_id, $email_address)
        {
            setcookie(self::get_cookie_entry_email_key($contest_id), $email_address, time() + 24 * 60 * 60 * 365, COOKIEPATH, COOKIE_DOMAIN);
        }

        /// TEMPLATE TAGS

        //// FLAGS

        public static function is_contest()
        {
            return is_singular(array(self::TYPE_CONTEST));
        }

        public static function is_promoting()
        {
            return true;
        }

        public static function has_ended($contest_id = null)
        {
            return current_time('timestamp') > self::get_meta($contest_id, 'end-timestamp');
        }

        public static function has_started($contest_id = null)
        {
            return current_time('timestamp') > self::get_meta($contest_id, 'start-timestamp');
        }

        public static function has_entered($contest_id = null, $email_address = null)
        {
            if (empty($contest_id)) {
                $contest_id = get_the_ID();
            }

            if (empty($email_address)) {
                $email_address = $_COOKIE[self::get_cookie_entry_email_key($contest_id)];
            }

            return false !== self::get_submission($contest_id, $email_address);
        }

        //// URLS

        public static function get_branding_logo_url()
        {
            return self::get_settings('branding-logo-url');
        }

        public static function get_promotion_url()
        {
            return 'http://jvz3.com/c/26956/55720';
        }

        public static function get_referral_url($contest_id, $email_address)
        {
            if (empty($contest_id)) {
                $contest_id = get_the_ID();
            }

            if (empty($email_address)) {
                $email_address = $_COOKIE[self::get_cookie_entry_email_key($contest_id)];
            }

            $referral_code = self::get_referral_code($contest_id, $email_address);
            $permalinks = get_option('permalink_structure');
            if (empty($permalinks)) {
                $referral_url = add_query_arg(array('cdr' => $referral_code), home_url('/'));
            } else {
                $referral_url = sprintf(home_url('/~cd%s'), $referral_code);
            }

            return $referral_url;
        }

        public static function get_template_resources_directory_url()
        {
            return plugins_url('views/frontend/templates/resources', __FILE__);
        }

        //// DISPLAY

        public static function get_entry_form()
        {
            $settings = self::get_settings();

            ob_start();
            include('views/frontend/misc/entry-form.php');
            return ob_get_clean();
        }

        //// ETC

        public static function get_twitter_handle()
        {
            return self::get_settings('twitter-handle');
        }

        public static function get_facebook_like_url()
        {
            return self::get_settings('facebook-like-url');
        }

        public static function get_facebook_profile_url()
        {
            return self::get_settings('facebook-profile-url');
        }

        public static function get_facebook_social_method()
        {
            return self::get_settings('facebook-method');
        }

        //// EVENT META

        public static function get_contest_disclaimer($contest_id)
        {
            return self::get_meta($contest_id, 'contest-disclaimer');
        }

        public static function get_contest_number_winners($contest_id)
        {
            return self::get_meta($contest_id, 'number-winners');
        }

        public static function get_contest_referral_entries($contest_id)
        {
            return self::get_meta($contest_id, 'number-entries');
        }

        public static function get_contest_rules($contest_id)
        {
            if ('yes' === self::get_meta($contest_id, 'contest-rules-custom')) {
                $rules = self::get_meta($contest_id, 'contest-rules');
            } else {
                $rules = self::get_settings('default-contest-rules');
            }

            return $rules;
        }

        public static function get_contest_tos($contest_id)
        {
            return self::get_meta($contest_id, 'contest-termsofservice');
        }

        public static function get_contest_ppb($contest_id)
        {
            return self::get_meta($contest_id, 'contest-privacypolicybox');
        }

        public static function get_contest_tracking_scripts($contest_id)
        {
            if ('yes' === self::get_meta($contest_id, 'tracking-scripts-custom')) {
                $scripts = self::get_meta($contest_id, 'tracking-scripts');
            } else {
                $scripts = self::get_settings('default-tracking-scripts');
            }

            return $scripts;
        }

        ///// DATES

        public static function get_contest_end_date($contest_id, $date_format)
        {
            $end_timestamp = self::get_meta($contest_id, 'end-timestamp');

            if (null === $date_format) {
                $date_format = get_option('date_format') . ' \a\t ' . get_option('time_format');
            }

            return false === $date_format ? $start_timestamp : date($date_format, $end_timestamp);
        }

        public static function get_contest_start_date($contest_id, $date_format)
        {
            $start_timestamp = self::get_meta($contest_id, 'start-timestamp');

            if (null === $date_format) {
                $date_format = get_option('date_format') . ' \a\t ' . get_option('time_format');
            }

            return false === $date_format ? $start_timestamp : date($date_format, $start_timestamp);
        }

        ///// ENTRIES, SUBMISSIONS & WINNERS

        public static function get_contest_number_entries($contest_id)
        {
            if (empty($contest_id)) {
                $contest_id = get_the_ID();
            }

            global $wpdb;
            return $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->contest_beast_entries} WHERE entry_submission_ID IN (SELECT submission_ID FROM {$wpdb->contest_beast_submissions} WHERE submission_contest_ID = %d AND submission_is_disqualified = 0)", $contest_id));
        }

        public static function get_contest_number_entries_for_email($contest_id, $email_address)
        {
            if (empty($contest_id)) {
                $contest_id = get_the_ID();
            }

            if (empty($email_address)) {
                $email_address = $_COOKIE[self::get_cookie_entry_email_key($contest_id)];
            }

            $submission = self::get_submission($contest_id, $email_address);

            return false === $submission ? 0 : $submission->submission_entries_count;
        }

        public static function get_contest_number_submissions($contest_id)
        {
            if (empty($contest_id)) {
                $contest_id = get_the_ID();
            }

            global $wpdb;
            return $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->contest_beast_submissions} WHERE submission_contest_ID = %d AND submission_is_disqualified = 0", $contest_id));
        }

        public static function get_contest_submissions($contest_id)
        {
            if (empty($contest_id)) {
                $contest_id = get_the_ID();
            }

            global $wpdb;

            $query = $wpdb->prepare("SELECT s.submission_name AS name, s.submission_email AS email, s.submission_date AS date, COUNT( e.entry_ID ) AS entries
						FROM  {$wpdb->contest_beast_submissions} s
						LEFT JOIN  {$wpdb->contest_beast_entries} e ON s.submission_ID = e.entry_submission_ID
						WHERE submission_contest_ID = %d
						GROUP BY s.submission_ID ORDER BY entries DESC", $contest_id);

            return $wpdb->get_results($query);
        }

        public static function get_contest_winners($contest_id)
        {
            if (empty($contest_id)) {
                $contest_id = get_the_ID();
            }

            global $wpdb;
            $winners = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->contest_beast_submissions} WHERE submission_contest_ID = %d AND submission_is_winner = 1", $contest_id));

            return empty($winners) ? false : $winners;
        }

    }

    Contest_Beast::init();

}

