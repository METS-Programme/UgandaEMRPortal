<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Base class for metaboxes
 *
 * @author am
 */
class PTB_Extra_Base extends PTB_CMB_Base {

    /**
     * The ID of plugin.
     *
     * @since    1.0.0
     * @access   public
     * @var      string $plugin_name The ID of this plugin.
     */
    public static $plugin_name;

    /**
     * The version of plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    protected static $version;
    protected static $sort_filed = false;

    /**
     * The type of custom meta box.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $type The type of custom meta box.
     */
    public function __construct($type, $plugin_name, $version) {
        parent::__construct($type, $plugin_name, $version);
        if (!is_admin()) {
            add_action('ptb_submission_' . $type, array($this, 'ptb_submission_form'), 10, 6);
        } else {
            add_action('ptb_submission_template_' . $type, array($this, 'ptb_submission_themplate'), 10, 5);
            add_filter('ptb_submission_validate_' . $type, array($this, 'ptb_submission_validate'), 10, 7);
            add_filter('ptb_submission_metabox_save_' . $type, array($this, 'ptb_submission_save'), 10, 5);
        }
    }
    
    public static function add_plugin_admin_menu($menu) {

        $menu['ptb-map'] = array(__('Google Map', 'ptb_extra'), __('Google Map', 'ptb_extra'), 'manage_options', array(__CLASS__, 'google_map_settings'));
        return $menu;
    }
    
    public static function get_google_map_key(){
        $value = get_option('ptb_google_map_key');
        if(empty($value) && function_exists('themify_get')){
            $value =  themify_get( 'setting-google_map_key' );
        }
        return $value;
    }

    public static function google_map_settings(){
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'ptb_extra'));
        }
        $value = '';
        if (!empty($_POST['ptb_google_map_key']) && !empty($_POST['ptb_google_map_nonce']) && wp_verify_nonce($_POST['ptb_google_map_nonce'], 'ptb_google_map_nonce_field')) {
            $value = sanitize_text_field($_POST['ptb_google_map_key']);
            update_option('ptb_google_map_key',$value,false);
        }
        else{
            $value = self::get_google_map_key();
        }
    ?> 
        <h2><?php _e('Google Map API Settings', 'ptb_extra') ?></h2>
        <form  method="post" action="" id="ptb_extra_map_key">
            <?php wp_nonce_field('ptb_google_map_nonce_field', 'ptb_google_map_nonce'); ?>
            <label for="ptb_google_map_key"><?php _e('Google Map Key','ptb_extra')?></label>
            <div class="ptb_extra_map_input">
                <input required="required" type="text" name="ptb_google_map_key" id="ptb_google_map_key" value="<?php echo $value?>"/>
                <br/>
                <small><?php _e('Google API key is required to use PTB Map module','ptb_extra')?>. </small> <a href="//developers.google.com/maps/documentation/javascript/get-api-key#key" target="_blank"><?php _e('Generate Api key','ptb_extra')?></a>
            </div>
            <?php submit_button(__('Save', 'ptb')); ?>
        </form>
    <?php
    }

    /**
     * Creates or returns an instance of this class.
     *
     * @return	A single instance of this class.
     */
    public static function Init($version) {
        self::$plugin_name = 'ptb_extra';
        self::$version = $version;
        self::load_dependencies();
        self::set_locale();
        if (is_admin()) {
            add_action('admin_enqueue_scripts', array(__CLASS__, 'enqueue_scripts'));
            add_action('ptb_deactivated', array(__CLASS__, 'deactivate'));
            add_filter('ptb_sort_fields', array(__CLASS__, 'ptb_sort_fields'), 10, 2);
            add_filter('ptb_admin_menu', array(__CLASS__, 'add_plugin_admin_menu'));
        } else {
            add_filter('themify_ptb_shortcode_query', array(__CLASS__, 'check_order_field'), 10, 1);
            add_filter('posts_orderby', array(__CLASS__, 'order_by_date'), 10, 1);
            add_filter('script_loader_tag', array(__CLASS__, 'ptb_ignore_rocket_loader'));
            add_action('wp_enqueue_scripts', array(__CLASS__, 'public_enqueue_scripts'));
        }
        new PTB_CMB_Map('map', 'ptb', self::$version);
        new PTB_CMB_Slider('slider', 'ptb', self::$version);
        new PTB_CMB_Gallery('gallery', 'ptb', self::$version);
        new PTB_CMB_Event_Date('event_date', 'ptb', self::$version);
        new PTB_CMB_Video('video', 'ptb', self::$version);
        new PTB_CMB_Audio('audio', 'ptb', self::$version);
        new PTB_CMB_File('file', 'ptb', self::$version);
        new PTB_CMB_Rating('rating', 'ptb', self::$version);
        new PTB_CMB_Progress_Bar('progress_bar', 'ptb', self::$version);
        new PTB_CMB_Icon('icon', 'ptb', self::$version);
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - PTB_Loader. Orchestrates the hooks of the plugin.
     * - PTB_i18n. Defines internationalization functionality.
     * - PTB_Admin. Defines all hooks for the dashboard.
     * - PTB_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private static function load_dependencies() {
        $plugin_dir = plugin_dir_path(dirname(__FILE__));

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once $plugin_dir . 'includes/class-ptb-extra-i18n.php';

        // The classes of metaboxes
        require_once $plugin_dir . 'includes/class-ptb-cmb-map.php';
        require_once $plugin_dir . 'includes/class-ptb-cmb-video.php';
        require_once $plugin_dir . 'includes/class-ptb-cmb-audio.php';
        require_once $plugin_dir . 'includes/class-ptb-cmb-slider.php';
        require_once $plugin_dir . 'includes/class-ptb-cmb-gallery.php';
        require_once $plugin_dir . 'includes/class-ptb-cmb-file.php';
        require_once $plugin_dir . 'includes/class-ptb-cmb-event-date.php';
        require_once $plugin_dir . 'includes/class-ptb-cmb-rating.php';
        require_once $plugin_dir . 'includes/class-ptb-cmb-progress-bar.php';
        require_once $plugin_dir . 'includes/class-ptb-cmb-icon.php';
        do_action('ptb_extra_loaded');
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the PTB_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private static function set_locale() {

        $plugin_i18n = new PTB_Extra_i18n();
        $plugin_i18n->set_domain(self::$plugin_name);
        $plugin_i18n->load_plugin_textdomain();
    }

    /**
     * Register the script/stylesheets for the adminpanel.
     *
     * @since    1.0.0
     */
    public static function enqueue_scripts() {

        $screen = get_current_screen();
        if ($screen->id != 'customize') {
            $pluginurl = plugin_dir_url(dirname(__FILE__));
            $id = __('Post Type Builder', 'ptb'); //multilanguage screen id
            $id = sanitize_title($id);
            $options = PTB::get_option();
            $screens = array($id . '_page_ptb-ptt', 'toplevel_page_ptb-cpt',$id.'_page_ptb-map');
            if ($options->has_custom_post_type($screen->id)) {
                $screens[] = $screen->id;
            }
            $screens = apply_filters('ptb_screens', $screens, $screen);
            if (in_array($screen->id, $screens)) {
                wp_enqueue_style(self::$plugin_name, $pluginurl . 'admin/css/ptb-extra.css', array('ptb'), self::$version, 'all');
                wp_enqueue_style(self::$plugin_name . '-timepicker', $pluginurl . 'admin/css/jquery-ui-timepicker.css', array(), self::$version, 'all');

                wp_enqueue_script(self::$plugin_name . '-video', $pluginurl . 'admin/js/ptb-extra-video.js', array('ptb'), self::$version, false);
                wp_enqueue_script(self::$plugin_name . '-audio', $pluginurl . 'admin/js/ptb-extra-audio.js', array('ptb'), self::$version, false);
                wp_enqueue_script(self::$plugin_name . '-slider', $pluginurl . 'admin/js/ptb-extra-slider.js', array('ptb'), self::$version, false);
                wp_enqueue_script(self::$plugin_name . '-gallery', $pluginurl . 'admin/js/ptb-extra-gallery.js', array('ptb'), self::$version, false);
                wp_enqueue_script(self::$plugin_name . '-rating', $pluginurl . 'admin/js/ptb-extra-rating.js', array('ptb'), self::$version, false);
                wp_enqueue_script(self::$plugin_name . '-progress-bar', $pluginurl . 'admin/js/ptb-extra-progress-bar.js', array('ptb'), self::$version, false);
                wp_enqueue_script(self::$plugin_name . '-icon', $pluginurl . 'admin/js/ptb-extra-icon.js', array('ptb'), self::$version, false);
                wp_enqueue_script(self::$plugin_name . '-file', $pluginurl . 'admin/js/ptb-extra-file.js', array('ptb'), self::$version, false);
                wp_enqueue_script('jquery-ui-datepicker');
                wp_enqueue_script(self::$plugin_name . '-timepicker', $pluginurl . 'admin/js/jquery-ui-timepicker.js', array('jquery-ui-datepicker'), self::$version, false);
                wp_enqueue_script(self::$plugin_name . '-date', $pluginurl . 'admin/js/ptb-extra-event-date.js', array(self::$plugin_name . '-timepicker', 'ptb'), self::$version, false);
                wp_enqueue_script(self::$plugin_name . '-map', $pluginurl . 'admin/js/ptb-extra-map.js', array(), self::$version, true);
                wp_localize_script(self::$plugin_name.'-map', 'ptb_extra', array(
                    'lng'=>PTB_Utils::get_current_language_code(),
                    'map_key'=>self::get_google_map_key()
                ));
            }
        }
    }

    /**
     * Add attribute so Allground scripts are ignored by Rocket Loader.
     * @param string $script_tag
     * @return string
     */
    public static function ptb_ignore_rocket_loader($script_tag) {
        if (false !== stripos($script_tag, 'themify-ptb-extra-fields/public/js/') || false !== stripos($script_tag, 'themify-ptb-extra-fields/public/submission/js/')) {
            $script_tag = str_replace('src=', 'data-cfasync="false" src=', $script_tag);
        }
        return $script_tag;
    }

    /**
     * Deactivate plugin if PTB is deactivated
     *
     * @since    1.0.0
     */
    public static function deactivate() {
        deactivate_plugins(plugin_basename(__FILE__));
    }

    public static function public_enqueue_scripts() {
        $pluginurl = plugin_dir_url(dirname(__FILE__));

        wp_enqueue_style(self::$plugin_name, $pluginurl . 'public/css/ptb-extra.css', array(), self::$version, 'all');
        wp_enqueue_style(self::$plugin_name . '-submission', $pluginurl . 'public/submission/css/ptb-extra-submission.css', array('ptb-submission'), self::$version, 'all');
        wp_register_style(self::$plugin_name . '-bxslider', $pluginurl . 'public/css/jquery.bxslider.css', array(), self::$version, 'all');
        wp_register_script(self::$plugin_name . '-easing', $pluginurl . 'public/js/jquery.easing.1.3.js', array('ptb-lightbox'), self::$version, true);
        wp_register_script(self::$plugin_name . '-fitvideo', $pluginurl . 'public/js/jquery.fitvids.js', array('ptb-lightbox'), self::$version, true);
        wp_register_script(self::$plugin_name . '-bxslider', $pluginurl . 'public/js/jquery.bxslider.min.js', array('ptb-lightbox'), self::$version, true);
        wp_register_script(self::$plugin_name . '-jqmeter', $pluginurl . 'public/js/jqmeter.min.js', array('ptb-lightbox'), self::$version, true);

        $translation_ = array(
            'lng' => PTB_Utils::get_current_language_code(),
            'url'=>$pluginurl.'public/',
            'ver'=>self::$version,
            'map_key'=>self::get_google_map_key()
        );
        wp_register_script(self::$plugin_name, $pluginurl . 'public/js/ptb-extra.js', array('ptb'), self::$version, true);
        wp_localize_script(self::$plugin_name, 'ptb_extra', $translation_);
        wp_enqueue_script(self::$plugin_name);
    }

    public function ptb_submission_themplate($id, array $args, array $data = array(), array $post_support, array $languages = array()) {
        
    }

    public function ptb_submission_lng_data(array $data, $id, $key, $post_id, $post_type, array $languages) {
        global $sitepress;
        $result = array();
        foreach ($languages as $code => $lng) {
            if (isset($sitepress)) {
                $post_ml_Id = icl_object_id($post_id, $post_type, FALSE, $code);
                $values = get_post_meta($post_ml_Id, 'ptb_' . $id, TRUE);
                if (isset($values[$key])) {
                    if (!is_array($values[$key])) {
                        $values[$key] = array($values[$key]);
                    }
                    foreach ($values[$key] as $index => $val) {
                        $result[$index][$id . '_' . $key][$code] = $val;
                    }
                }
            } else {
                foreach ($data as $index => $v) {
                    $result[$index][$id . '_' . $key][$code] = $v;
                }
            }
        }

        return $result;
    }

    public static function ptb_sort_fields(array $fields, array $metaboxes = array()) {
        foreach ($metaboxes as $k => $m) {
            if ($m['type'] === 'event_date' || $m['type'] === 'rating') {
                $label = PTB_Utils::get_label($m['name']);
                if (!isset($m['showrange'])) {
                    $fields[$k] = $label;
                } else {
                    $fields[$k . '_start'] = sprintf(__('%s -> Start Date', 'ptb_extra'), $label);
                    $fields[$k . '_end'] = sprintf(__('%s -> End Date', 'ptb_extra'), $label);
                }
            }
        }
        return $fields;
    }

    public static function check_order_field($args) {
        if (PTB_Public::$shortcode) {
            if (isset($args['meta_key']) && preg_match('/^ptb_event_date_[1-9]+_(start|end)$/', $args['meta_key'])) {
                self::$sort_filed = strpos($args['meta_key'], '_start') !== FALSE ? 'start' : 'end';
                $args['meta_key'] = str_replace(array('_start', '_end'), '', $args['meta_key']);
            }
        }
        return $args;
    }

    public static function order_by_date($orderby) {

        if (PTB_Public::$shortcode && self::$sort_filed) {
            
            $order = strpos($orderby, 'DESC') !== false ? 'DESC' : 'ASC';
            $orderby = self::$sort_filed === 'start' ?
                    "STR_TO_DATE(REPLACE(SUBSTRING_INDEX(SUBSTRING_INDEX(meta_value,'\"',4),'\"',-1),'@',' '),'%Y-%m-%d %h:%i%p')" :
                    "STR_TO_DATE(REPLACE(SUBSTRING_INDEX(SUBSTRING_INDEX(meta_value,'\"',-2),'\"',1),'@',' '),'%Y-%m-%d %h:%i%p')";
            $orderby.=' ' . $order;
            self::$sort_filed = false;
        }
        return $orderby;
    }

}
