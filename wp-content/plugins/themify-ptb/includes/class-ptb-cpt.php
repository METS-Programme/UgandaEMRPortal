<?php

/**
 * Custom Post Type class.
 *
 * This class helps to create custom post type arguments
 *
 * @link       http://themify.me
 * @since      1.0.0
 *
 * @package    PTB
 * @subpackage PTB/includes
 */

/**
 * Custom Post Type class.
 *
 * This class helps to create custom post type arguments
 *
 * @since      1.0.0
 * @package    PTB
 * @subpackage PTB/includes
 * @author     Themify <ptb@themify.me>
 */
class PTB_Custom_Post_Type {

    // Constants
    const ID = 'id';
    const SLUG = 'slug';
    const SINGULAR_LABEL = 'singular_label';
    const PLURAL_LABEL = 'plural_label';
    const DESCRIPTION = 'description';
    const IS_HIERARCHICAL = 'is_hierarchical';
    const HAS_ARCHIVE = 'has_archive';
    const SUPPORTS = 'supports';
    const TAXONOMIES = 'taxonomies';
    const META_BOXES = 'meta_boxes';
    const SUPPORT_TITLE = 'title';
    const SUPPORT_EDITOR = 'editor';
    const SUPPORT_THUMBNAIL = 'thumbnail';
    const SUPPORT_EXCERPT = 'excerpt';
    const SUPPORT_COMMENTS = 'comments';
    const SUPPORT_AUTHOR = 'author';
    const TAXONOMY_CATEGORY = 'category';
    const TAXONOMY_TAGS = 'post_tag';
    // Custom labels
    const CL_ADD_NEW = 'add_new';
    const CL_ADD_NEW_ITEM = 'add_new_item';
    const CL_EDIT_ITEM = 'edit_item';
    const CL_NEW_ITEM = 'new_item';
    const CL_ALL_ITEMS = 'all_items';
    const CL_VIEW_ITEM = 'view_item';
    const CL_SEARCH_ITEMS = 'search_items';
    const CL_NOT_FOUND = 'not_found';
    const CL_NOT_FOUND_IN_TRASH = 'not_found_in_trash';
    const CL_PARENT_ITEM_COLON = 'parent_item_colon';
    const CL_MENU_NAME = 'menu_name';
    // Advanced options
    const AD_PUBLICLY_QUERYABLE = 'publicly_queryable';
    const AD_EXCLUDE_FROM_SEARCH = 'exclude_from_search';
    const AD_CAN_EXPORT = 'can_export';
    const AD_SHOW_UI = 'show_ui';
    const AD_SHOW_IN_NAV_MENUS = 'show_in_nav_menus'; // Show in WordPress Menus
    const AD_SHOW_IN_MENU = 'show_in_menu'; // Show in Admin Menu
    const AD_MENU_POSITION = 'menu_position'; // Show in Admin Menu - Menu Position
    const AD_MENU_ICON = 'menu_icon'; // Show in Admin Menu - Menu Icon
    const AD_CAPABILITY_TYPE = 'capability_type';

    //const AD_REWRITE_SLUG = 'rewrite_slug';
    // Private Properties
    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $plugin_name The string used to uniquely identify this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;
    // Public Properties
    /**
     * The id post type in settings
     *
     * @since    1.0.0
     * @access   public
     * @var string $id The id of post type in settings. Empty string if post type is new.
     */
    public $id;
    public $singular_label;
    public $plural_label;
    public $slug;
    public $description;
    public $is_hierarchical;
    public $has_archive;
    public $supports;
    public $taxonomies;
    public $meta_boxes;
    // Custom Labels
    public $cl_add_new;
    public $cl_add_new_item;
    public $cl_edit_item;
    public $cl_new_item;
    public $cl_all_items;
    public $cl_view_item;
    public $cl_search_items;
    public $cl_not_found;
    public $cl_not_found_in_trash;
    public $cl_parent_item_colon;
    public $cl_menu_name;
    // Advanced options
    public $ad_publicly_queryable;
    public $ad_exclude_from_search;
    public $ad_can_export;
    public $ad_show_ui;
    public $ad_show_in_nav_menus;
    public $ad_show_in_menu;
    public $ad_menu_position;
    public $ad_menu_icon;
    public $ad_capability_type;
    public $ad_rewrite_slug;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @var      string $plugin_name The name of the plugin.
     * @var      string $version The version of this plugin.
     */
    public function __construct($plugin_name, $version) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;

        $this->set_defaults();
    }

    /**
     * Set default values of the properties
     *
     * @since 1.0.0
     */
    private function set_defaults() {

        $this->id = '';
        $this->slug = '';

        $this->singular_label = array();
        $this->plural_label = array();
        $this->description = array();


        $this->has_archive = true;
        $this->is_hierarchical = true;

        $this->supports = array('title', 'editor', 'thumbnail', 'excerpt', 'page-attributes');
        $this->taxonomies = array('category', 'post_tag');
        $this->meta_boxes = array();

        // Set custom labels default values
        $this->cl_add_new = array();
        $this->cl_add_new_item = array();
        $this->cl_edit_item = array();
        $this->cl_new_item = array();
        $this->cl_all_items = array();
        $this->cl_view_item = array();
        $this->cl_search_items = array();
        $this->cl_not_found = array();
        $this->cl_not_found_in_trash = array();
        $this->cl_parent_item_colon = array();
        $this->cl_menu_name = array();

        // Set advanced options default values;
        $this->ad_publicly_queryable = true;
        $this->ad_exclude_from_search = true;
        $this->ad_can_export = true;
        $this->ad_show_ui = true;
        $this->ad_show_in_nav_menus = true;
        $this->ad_show_in_menu = true;
        $this->ad_menu_position = 25;
        $this->ad_menu_icon = 'dashicons-admin-post';
        $this->ad_capability_type = 'post';
        $this->ad_rewrite_slug = array();
    }

    /**
     * Generates the $args array for custom post registration
     *
     * @since 1.0.0
     *
     * @return array
     */
    public function get_args() {

        $singular_label = PTB_Utils::get_label($this->singular_label);
        $plural_label = PTB_Utils::get_label($this->plural_label);
        $search_items = PTB_Utils::get_label($this->cl_search_items);
        $all_items = PTB_Utils::get_label($this->cl_all_items);
        $parent_item_colon = PTB_Utils::get_label($this->cl_parent_item_colon);
        $edit_item = PTB_Utils::get_label($this->cl_edit_item);
        $new_item = PTB_Utils::get_label($this->cl_new_item);
        $add_new_item = PTB_Utils::get_label($this->cl_add_new_item);
        $add_new = PTB_Utils::get_label($this->cl_add_new);
        $not_found = PTB_Utils::get_label($this->cl_not_found);
        $not_found_in_trash = PTB_Utils::get_label($this->cl_not_found_in_trash);
        $view_item = PTB_Utils::get_label($this->cl_view_item);
        $menu_name = PTB_Utils::get_label($this->cl_menu_name);

        $labels = array(
            'name' => $plural_label,
            'singular_name' => $singular_label,
            'menu_name' => sprintf($menu_name, $singular_label),
            'name_admin_bar' => $singular_label,
            'add_new' => $add_new,
            'add_new_item' => sprintf($add_new_item, $singular_label),
            'new_item' => sprintf($new_item, $singular_label),
            'edit_item' => sprintf($edit_item, $singular_label),
            'view_item' => sprintf($view_item, $singular_label),
            'all_items' => sprintf($all_items, $plural_label),
            'search_items' => sprintf($search_items, $plural_label),
            'parent_item_colon' => sprintf($parent_item_colon, $plural_label),
            'not_found' => $not_found,
            'not_found_in_trash' => $not_found_in_trash,
        );

        $args = array(
            'labels' => $labels,
            'public' => true,
            'publicly_queryable' => $this->ad_publicly_queryable,
            'exclude_from_search' => $this->ad_exclude_from_search,
            'can_export' => $this->ad_can_export,
            'show_ui' => $this->ad_show_ui,
            'show_in_menu' => $this->ad_show_in_menu,
            'menu_position' => intval($this->ad_menu_position),
            'menu_icon' => $this->ad_menu_icon,
            'query_var' => $this->slug,
            //'rewrite'             => array( 'slug' => $this->ad_rewrite_slug[$lng] ),
            'capability_type' => $this->ad_capability_type,
            'has_archive' => $this->has_archive,
            'hierarchical' => $this->is_hierarchical,
            'supports' => $this->supports,
            'taxonomies' => $this->taxonomies
        );

        return $args;
    }

    /**
     * Serialize the object to array for settings
     *
     * @since 1.0.0
     *
     * @return array
     */
    public function serialize() {
        $args = array(
            self::ID => $this->id,
            self::SLUG => $this->slug,
            self::SINGULAR_LABEL => $this->singular_label,
            self::PLURAL_LABEL => $this->plural_label,
            self::DESCRIPTION => $this->description,
            self::IS_HIERARCHICAL => $this->is_hierarchical,
            self::HAS_ARCHIVE => $this->has_archive,
            self::SUPPORTS => array_values($this->supports),
            self::TAXONOMIES => array_values($this->taxonomies),
            self::META_BOXES => $this->meta_boxes,
            // Custom labels
            self::CL_ADD_NEW => $this->cl_add_new,
            self::CL_ADD_NEW_ITEM => $this->cl_add_new_item,
            self::CL_EDIT_ITEM => $this->cl_edit_item,
            self::CL_NEW_ITEM => $this->cl_new_item,
            self::CL_ALL_ITEMS => $this->cl_all_items,
            self::CL_VIEW_ITEM => $this->cl_view_item,
            self::CL_SEARCH_ITEMS => $this->cl_search_items,
            self::CL_NOT_FOUND => $this->cl_not_found,
            self::CL_NOT_FOUND_IN_TRASH => $this->cl_not_found_in_trash,
            self::CL_PARENT_ITEM_COLON => $this->cl_parent_item_colon,
            self::CL_MENU_NAME => $this->cl_menu_name,
            // Advanced options
            self::AD_PUBLICLY_QUERYABLE => $this->ad_publicly_queryable,
            self::AD_EXCLUDE_FROM_SEARCH => $this->ad_exclude_from_search,
            self::AD_CAN_EXPORT => $this->ad_can_export,
            self::AD_SHOW_UI => $this->ad_show_ui,
            self::AD_SHOW_IN_NAV_MENUS => $this->ad_show_in_nav_menus,
            self::AD_SHOW_IN_MENU => $this->ad_show_in_menu,
            self::AD_MENU_POSITION => $this->ad_menu_position,
            self::AD_MENU_ICON => $this->ad_menu_icon,
            self::AD_CAPABILITY_TYPE => $this->ad_capability_type,
                //self::AD_REWRITE_SLUG        => empty( $this->ad_rewrite_slug ) ? $this->slug : $this->ad_rewrite_slug
        );


        return $args;
    }

    /**
     * Deserialize the post type settings to object
     *
     * @since 1.0.0
     *
     * @param array $source
     */
    public function deserialize($source) {

        if (array_key_exists(self::ID, $source)) {
            $this->id = $source[self::ID];
        }

        if (array_key_exists(self::SINGULAR_LABEL, $source)) {
            $this->singular_label = $source[self::SINGULAR_LABEL];
        }

        if (array_key_exists(self::PLURAL_LABEL, $source)) {
            $this->plural_label = $source[self::PLURAL_LABEL];
        }

        if (array_key_exists(self::SLUG, $source)) {
            $this->slug = $source[self::SLUG];
        }

        if (array_key_exists(self::DESCRIPTION, $source)) {
            $this->description = $source[self::DESCRIPTION];
        }

        if (array_key_exists(self::IS_HIERARCHICAL, $source)) {
            $this->is_hierarchical = $source[self::IS_HIERARCHICAL];
        }

        if (array_key_exists(self::HAS_ARCHIVE, $source)) {
            $this->has_archive = $source[self::HAS_ARCHIVE];
        }

        if (array_key_exists(self::SUPPORTS, $source)) {
            $this->supports = $source[self::SUPPORTS];
        }

        if (array_key_exists(self::TAXONOMIES, $source)) {
            $this->taxonomies = $source[self::TAXONOMIES];
        }

        if (array_key_exists(self::META_BOXES, $source)) {
            $this->meta_boxes = $source[self::META_BOXES];
        }

        // Custom labels

        if (array_key_exists(self::CL_ADD_NEW, $source)) {
            $this->cl_add_new = $source[self::CL_ADD_NEW];
        }

        if (array_key_exists(self::CL_ADD_NEW_ITEM, $source)) {
            $this->cl_add_new_item = $source[self::CL_ADD_NEW_ITEM];
        }

        if (array_key_exists(self::CL_EDIT_ITEM, $source)) {
            $this->cl_edit_item = $source[self::CL_EDIT_ITEM];
        }

        if (array_key_exists(self::CL_NEW_ITEM, $source)) {
            $this->cl_new_item = $source[self::CL_NEW_ITEM];
        }

        if (array_key_exists(self::CL_ALL_ITEMS, $source)) {
            $this->cl_all_items = $source[self::CL_ALL_ITEMS];
        }

        if (array_key_exists(self::CL_VIEW_ITEM, $source)) {
            $this->cl_view_item = $source[self::CL_VIEW_ITEM];
        }

        if (array_key_exists(self::CL_SEARCH_ITEMS, $source)) {
            $this->cl_search_items = $source[self::CL_SEARCH_ITEMS];
        }

        if (array_key_exists(self::CL_NOT_FOUND, $source)) {
            $this->cl_not_found = $source[self::CL_NOT_FOUND];
        }

        if (array_key_exists(self::CL_NOT_FOUND_IN_TRASH, $source)) {
            $this->cl_not_found_in_trash = $source[self::CL_NOT_FOUND_IN_TRASH];
        }

        if (array_key_exists(self::CL_PARENT_ITEM_COLON, $source)) {
            $this->cl_parent_item_colon = $source[self::CL_PARENT_ITEM_COLON];
        }

        if (array_key_exists(self::CL_MENU_NAME, $source)) {
            $this->cl_menu_name = $source[self::CL_MENU_NAME];
        }

        // Advanced options

        if (array_key_exists(self::AD_PUBLICLY_QUERYABLE, $source)) {
            $this->ad_publicly_queryable = $source[self::AD_PUBLICLY_QUERYABLE];
        }

        if (array_key_exists(self::AD_EXCLUDE_FROM_SEARCH, $source)) {
            $this->ad_exclude_from_search = $source[self::AD_EXCLUDE_FROM_SEARCH];
        }

        if (array_key_exists(self::AD_CAN_EXPORT, $source)) {
            $this->ad_can_export = $source[self::AD_CAN_EXPORT];
        }

        if (array_key_exists(self::AD_SHOW_UI, $source)) {
            $this->ad_show_ui = $source[self::AD_SHOW_UI];
        }

        if (array_key_exists(self::AD_SHOW_IN_NAV_MENUS, $source)) {
            $this->ad_show_in_nav_menus = $source[self::AD_SHOW_IN_NAV_MENUS];
        }

        if (array_key_exists(self::AD_SHOW_IN_MENU, $source)) {
            $this->ad_show_in_menu = $source[self::AD_SHOW_IN_MENU];
        }

        if (array_key_exists(self::AD_MENU_POSITION, $source)) {
            $this->ad_menu_position = $source[self::AD_MENU_POSITION];
        }

        if (array_key_exists(self::AD_MENU_ICON, $source)) {
            $this->ad_menu_icon = $source[self::AD_MENU_ICON];
        }

        if (array_key_exists(self::AD_CAPABILITY_TYPE, $source)) {
            $this->ad_capability_type = $source[self::AD_CAPABILITY_TYPE];
        }

        /* if ( array_key_exists( self::AD_REWRITE_SLUG, $source ) ) {
          $this->ad_rewrite_slug = $source[ self::AD_REWRITE_SLUG ];
          } */
    }

    /**
     * Add or remove support based on $state
     *
     * @since 1.0.0
     *
     * @param string $feature
     * @param bool $state
     */
    public function set_support_for($feature, $state) {

        if (true === $state) {

            PTB_Utils::add_to_array($feature, $this->supports);
        } else {

            PTB_Utils::remove_from_array($feature, $this->supports);
        }
    }

    /**
     * Check for post type support
     *
     * @since 1.0.0
     *
     * @param string $feature
     *
     * @return bool
     */
    public function has_support_for($feature) {

        return in_array($feature, $this->supports);
    }

    /**
     * Add or remove taxonomy based on $state
     *
     * @since 1.0.0
     *
     * @param string $taxonomy
     * @param bool $state
     */
    public function set_taxonomy($taxonomy, $state) {

        if (true == $state) {

            PTB_Utils::add_to_array($taxonomy, $this->taxonomies);
        } else {

            PTB_Utils::remove_from_array($taxonomy, $this->taxonomies);
        }
    }

    /**
     * Check for post type taxonomy
     *
     * @since 1.0.0
     *
     * @param string $taxonomy
     *
     * @return bool
     */
    public function has_taxonomy($taxonomy) {

        return in_array($taxonomy, $this->taxonomies);
    }

}
