<?php

class PTB_Form_PTT_Them {

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $plugin_name The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $version The current version of the plugin.
     */
    protected $version;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      template type
     */
    protected $type;
    protected $settings_section;
    protected $post_taxonomies;

    /**
     * The options management class of the the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      PTB_Options $options Manipulates with plugin options
     */
    protected $options;
    public static $key = 'ptt';
    protected $themplate_id = false;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     *
     * @param string $plugin_name
     * @param string $version
     * @param PTB_Options $options the plugin options instance
     *
     */
    public function __construct($plugin_name, $version, $themplate_id = false) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->themplate_id = $themplate_id;
        $this->options = PTB::get_option();
    }

    /**
     * Add settings section for themplage
     *
     * @since    1.0.0
     *
     * @param string $type
     *
     */
    public function add_settings_section($type) {

        $this->type = $type;
        $this->settings_section = $this->plugin_name . '-ptt-' . $type;
        add_settings_section(
                $this->settings_section, '', array($this, 'main_section_cb'), $this->settings_section
        );
        require_once plugin_dir_path(dirname(__FILE__)) . '/admin/partials/ptb-admin-display-edit-ptt-them.php';
    }

    public function main_section_cb() {

        $value = $this->get_edit_value($this->type, array());
        $ptt = $this->get_ptt();
        $languages = PTB_Utils::get_all_languages();
        $layout = isset($ptt[$this->type]['layout']) ? $ptt[$this->type]['layout'] : false;
        $cmb_options = $this->options->get_cpt_cmb_options($ptt['post_type']); //post type metaboxes
        $post_support = $this->options->get_cpt_cmb_support($ptt['post_type']); //post type support
        $taxes = $this->options->get_custom_taxonomies();
        $post_taxonomies = $this->options->get_cpt_cmb_taxonomies($ptt['post_type']); //post type taxonomies
        $unset = array_search('page-attributes', $post_support);
        if ($unset !== false && isset($post_support[$unset])) {
            unset($post_support[$unset]);
        }

        $replace_name = array(
            'editor' => __('Content', 'ptb'),
            'thumbnail' => __('Featured Image', 'ptb')
        );
        if (!empty($post_support)) {
            foreach ($post_support as $support) {
                $name = isset($replace_name[$support]) ? $replace_name[$support] : ucfirst($support);
                $cmb_options[$support] = array('type' => $support, 'name' => $name);
            }
        }
        $this->post_taxonomies = array();
        if (!empty($post_taxonomies)) {
            foreach ($post_taxonomies as $t) {
                if ($t != 'category' && $t != 'post_tag') {
                    foreach ($taxes as $t2) {
                        if ($t == $t2->slug) {
                            $this->post_taxonomies[$t] = PTB_Utils::get_label($t2->singular_label);
                        }
                    }
                } else {
                    $this->post_taxonomies[$t] = 1;
                }
            }
            unset($post_taxonomies);
            if (isset($this->post_taxonomies['post_tag'])) {
                $post_support[] = 'post_tag';
                $cmb_options['post_tag'] = array('type' => 'post_tag', 'name' => __('Tags', 'ptb'));
                unset($this->post_taxonomies['post_tag']);
            }

            if (!empty($this->post_taxonomies)) {
                if (isset($this->post_taxonomies['category'])) {
                    $post_support[] = 'category';
                    $cmb_options['category'] = array(
                        'type' => 'category',
                        'name' => __('Categories', 'ptb')
                    );
                    unset($this->post_taxonomies['category']);
                }
                if (!empty($this->post_taxonomies)) {
                    $post_support[] = 'taxonomies';
                    $cmb_options['taxonomies'] = array(
                        'type' => 'taxonomies',
                        'name' => __('Taxonomies', 'ptb')
                    );
                }
            }
        }

        $post_support[] = 'custom_text';
        $post_support[] = 'date';
        $post_support[] = 'custom_image';
        $cmb_options['custom_text'] = array('type' => 'custom_text', 'name' => __('Static Text', 'ptb'));
        $cmb_options['date'] = array('type' => 'date', 'name' => __('Date', 'ptb'));
        $cmb_options['custom_image'] = array('type' => 'custom_image', 'name' => __('Static Image', 'ptb'));
        $post_support[] = 'permalink';
        $cmb_options['permalink'] = array('type' => 'permalink', 'name' => __('Permalink', 'ptb'));
        $cmb_options = apply_filters('ptb_template_modules', $cmb_options, $this->type, $ptt['post_type']);
        $method = 'ptb_' . $this->type . '_template';
        $this->add_fields($ptt[$this->type]);
        $sort_cmb = array();
        foreach ($cmb_options as $key=>$cmb){
            $sort_cmb[$key] = PTB_Utils::get_label($cmb['name']);
        }
        natcasesort($sort_cmb);
        ?>  
        <input type="hidden" value="<?php echo $this->type ?>" name="ptb_type"/>
        <input type="hidden" value="<?php echo $this->themplate_id ?>" name="ptb-<?php echo self::$key ?>"/>
        <input type="hidden" value="" name="ptb_layout" id="ptb_layout"/>
        <div class="ptb_back_builder">
            <?php //Metabox Buttons   ?>
            <div class="ptb_back_module_panel">
                <?php  foreach ($sort_cmb as $meta_key => $name): ?>
                    <?php
                    $args = $cmb_options[$meta_key];
                    $type = sanitize_key($args['type']);
                    $meta_key = sanitize_key($meta_key);
                    $metabox = in_array($type, $post_support);
                    $id = !$metabox ? $meta_key : $type;
                    ?>
                    <div data-type="<?php echo $type ?>"
                         id="ptb_cmb_<?php echo $meta_key ?>"
                         class="ptb_back_module<?php if (!$metabox): ?> ptb_is_metabox<?php endif; ?>">
                        <strong class="ptb_module_name"><?php echo $name ?></strong>

                        <div class="ptb_active_module">
                            <div class="ptb_back_module_top">
                                <div class="ptb_left">
                                    <span class="ptb_back_active_module_title"><?php echo $name ?></span>
                                </div>
                                <div class="ptb_right">
                                    <a href="#" class="ptb_module_btn ptb_toggle_module"></a>
                                    <a href="#" class="ptb_module_btn ptb_delete_module"></a>
                                </div>
                            </div>
                            <div data-type="<?php echo $type ?>"
                                 class="ptb_back_active_module_content">
                                     <?php do_action('before_template_row', $id, array(), $this->type, $languages); ?>
                                     <?php if (has_action($method)): ?>
                                         <?php do_action($method, $type, $id, $args, array(), $post_support, $languages); ?>
                                     <?php else: ?>
                                         <?php if (!$metabox): ?>
                                             <?php do_action('ptb_template_' . $type, $id, $this->type, $args, array(), $languages) ?>
                                         <?php else: ?>
                                             <?php $this->get_main_fields($id, $name, array(), $languages) ?>
                                         <?php endif; ?>

                                    <?php
                                    if ($type != 'custom_text' && $type != 'editor') {
                                        PTB_CMB_Base::module_multi_text($id, array(), $languages, 'text_before', __('Text Before', 'ptb'));
                                        PTB_CMB_Base::module_multi_text($id, array(), $languages, 'text_after', __('Text After', 'ptb'));
                                    }
                                    ?>
                                    <div class="ptb_back_active_module_row">
                                        <div class="ptb_back_active_module_label">
                                            <label for="ptb_<?php echo $id ?>[css]"><?php _e('Custom CSS Class', 'ptb') ?></label>
                                        </div>
                                        <div class="ptb_back_active_module_input">
                                            <input id="ptb_<?php echo $id ?>[css]" class="ptb_towidth" type="text"  name="[<?php echo $id ?>][css]" value="<?php echo isset($module['css']) && $module['css'] ? $module['css'] : '' ?>" />
                                        </div>
                                    </div>
                                    <div class="ptb_back_active_module_row">	
                                        <div class="ptb_back_active_module_label">
                                            <label for="ptb_<?php echo $id ?>[display_inline]"><?php _e('Display Inline', 'ptb') ?></label>
                                        </div>
                                        <div class="ptb_back_active_module_input">
                                            <label>
                                                <input id="ptb_<?php echo $id ?>[display_inline]" type="checkbox" name="[<?php echo $id ?>][display_inline]" />
                                                <?php _e('Display this module inline (float left)', 'ptb'); ?>
                                            </label>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <?php do_action('after_template_row', $id, array(), $this->type, $languages); ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php //Dropping container  ?>
            <div class="ptb_back_row_panel" id="ptb_row_wrapper">
                <?php if (!empty($layout)): ?>
                    <?php foreach ($layout as $row_key => $_row): ?>
                        <?php
                        $grid_keys = array_keys($_row);
                        $array_gid_keys = array();
                        foreach ($grid_keys as $keys) {
                            $tmp_keys = explode('-', $keys);
                            $array_gid_keys[] = $tmp_keys[0] . '-' . $tmp_keys[1];
                        }
                        $grid_keys = implode('-', $array_gid_keys);
                        ?>
                        <div
                            class="ptb_back_row<?php if ($row_key == 0): ?> ptb_first_row<?php endif; ?>">
                            <div class="ptb_back_row_top">
                                <div class="ptb_left">
                                    <div class="ptb_grid_menu">
                                        <a class="ptb_row_btn ptb_grid_options"></a>

                                        <div class="ptb_grid_list_wrapper">
                                            <ul class="ptb_grid_list clearfix">
                                                <li>
                                                    <ul>
                                                        <li <?php if ($grid_keys == '1-1'): ?>class="selected"<?php endif; ?>>
                                                            <a href="#" class="ptb_column_select ptb_grid_1_1"
                                                               data-grid=["1-1"]></a></li>
                                                        <li <?php if ($grid_keys == '4-2-4-2'): ?>class="selected"<?php endif; ?>>
                                                            <a href="#" class="ptb_column_select ptb_grid_4_2_4_2"
                                                               data-grid=["4-2","4-2"]></a></li>
                                                        <li <?php if ($grid_keys == '3-1-3-1-3-1'): ?>class="selected"<?php endif; ?>>
                                                            <a href="#" class="ptb_column_select ptb_grid_3_1_3_1_3_1"
                                                               data-grid=["3-1","3-1","3-1"]></a></li>
                                                        <li <?php if ($grid_keys == '4-1-4-1-4-1-4-1'): ?>class="selected"<?php endif; ?>>
                                                            <a href="#" class="ptb_column_select ptb_grid_4_1_4_1_4_1_4_1"
                                                               data-grid=["4-1","4-1","4-1","4-1"]></a></li>
                                                        <li <?php if ($grid_keys == '5-1-5-1-5-1-5-1-5-1'): ?>class="selected"<?php endif; ?>>
                                                            <a href="#" class="ptb_column_select ptb_grid_5_1_5_1_5_1_5_1_5_1"
                                                               data-grid=["5-1","5-1","5-1","5-1","5-1"]></a></li>
                                                        <li <?php if ($grid_keys == '6-1-6-1-6-1-6-1-6-1-6-1'): ?>class="selected"<?php endif; ?>>
                                                            <a href="#" class="ptb_column_select ptb_grid_6_1_6_1_6_1_6_1_6_1_6_1"
                                                               data-grid=["6-1","6-1","6-1","6-1","6-1","6-1"]></a></li>
                                                    </ul>
                                                </li>
                                                <li>
                                                    <ul>
                                                        <li <?php if ($grid_keys == '4-1-4-3'): ?>class="selected"<?php endif; ?>>
                                                            <a href="#" class="ptb_column_select ptb_grid_4_1_4_3"
                                                               data-grid=["4-1","4-3"]></a></li>
                                                        <li <?php if ($grid_keys == '4-1-4-1-4-2'): ?>class="selected"<?php endif; ?>>
                                                            <a href="#" class="ptb_column_select ptb_grid_4_1_4_1_4_2"
                                                               data-grid=["4-1","4-1","4-2"]></a></li>
                                                        <li <?php if ($grid_keys == '4-1-4-2-4-1'): ?>class="selected"<?php endif; ?>>
                                                            <a href="#" class="ptb_column_select ptb_grid_4_1_4_2_4_1"
                                                               data-grid=["4-1","4-2","4-1"]></a></li>
                                                        <li <?php if ($grid_keys == '4-2-4-1-4-1'): ?>class="selected"<?php endif; ?>>
                                                            <a href="#" class="ptb_column_select ptb_grid_4_2_4_1_4_1"
                                                               data-grid=["4-2","4-1","4-1"]></a></li>
                                                        <li <?php if ($grid_keys == '4-3-4-1'): ?>class="selected"<?php endif; ?>>
                                                            <a href="#" class="ptb_column_select ptb_grid_4_3_4_1"
                                                               data-grid=["4-3","4-1"]></a></li>
                                                    </ul>
                                                </li>
                                                <li>
                                                    <ul>
                                                        <li <?php if ($grid_keys == '3-2-3-1'): ?>class="selected"<?php endif; ?>>
                                                            <a href="#" class="ptb_column_select ptb_grid_3_2_3_1"
                                                               data-grid=["3-2","3-1"]></a></li>
                                                        <li <?php if ($grid_keys == '3-1-3-2'): ?>class="selected"<?php endif; ?>>
                                                            <a href="#"  class="ptb_column_select ptb_grid_3_1_3_2"
                                                               data-grid=["3-1","3-2"]></a></li>
                                                    </ul>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="ptb_right">
                                    <a href="#" class="ptb_row_btn ptb_toggle_module"></a>
                                    <a href="#" class="ptb_row_btn ptb_delete_module"></a>
                                </div>
                            </div>
                            <div class="ptb_back_row_content">
                                <?php $count = 6 - count($_row);  //6 is the maximum number of grids   ?>
                                <?php if ($count > 0): ?>
                                    <?php for ($i = 0; $i < $count; ++$i): ?>
                                        <?php $_row[] = array(); //fill array for set maximum colums count ?>
                                    <?php endfor; ?>
                                <?php endif; ?>
                                <?php $first = true; ?>
                                <?php foreach ($_row as $col_key => $col): ?>
                                    <?php
                                    $grid_keys = false;
                                    if (!is_numeric($col_key)) {
                                        $tmp_key = explode('-', $col_key);
                                        $grid_keys = $tmp_key[0] . '-' . $tmp_key[1];
                                    }
                                    ?>
                                    <div
                                        class="<?php if ($first && $grid_keys): ?>first <?php $first = false; ?><?php endif; ?>ptb_back_col<?php if ($grid_keys): ?> ptb_col<?php echo $grid_keys ?> ptb_show_grid<?php endif; ?>"
                                        <?php if ($grid_keys): ?>data-grid="<?php echo $grid_keys ?>"<?php endif; ?>>
                                        <div class="ptb_module_holder">
                                            <div class="ptb_empty_holder_text"><?php _e('Drop module here', 'ptb') ?></div>
                                            <?php if (!empty($col)): ?>
                                                <?php foreach ($col as $module): ?>
                                                    <?php
                                                    $meta_key = sanitize_key($module['key']);
                                                    if (!isset($cmb_options[$meta_key])) {
                                                        continue;
                                                    }
                                                    $args = $cmb_options[$meta_key];
                                                    $name = esc_html(PTB_Utils::get_label($args['name']));
                                                    if ($module['type'] != 'custom_text') {
                                                        foreach ($module as &$values) {
                                                            if (!is_array($values)) {
                                                                $values = sanitize_text_field($values);
                                                            } elseif (!empty($values)) {
                                                                foreach ($values as &$value) {
                                                                    $value = sanitize_text_field($value);
                                                                }
                                                            }
                                                        }
                                                    }
                                                    $type = $module['type'];
                                                    $metabox = in_array($type, $post_support);
                                                    $id = !$metabox ? $meta_key : $type;
                                                    ?>
                                                    <div data-type="<?php echo $type ?>" class="ptb_back_module ptb_dragged">
                                                        <strong class="ptb_module_name"><?php echo $name ?></strong>

                                                        <div class="ptb_active_module">
                                                            <div class="ptb_back_module_top">
                                                                <div class="ptb_left">
                                                                    <span class="ptb_back_active_module_title"><?php echo $name ?></span>
                                                                </div>
                                                                <div class="ptb_right">
                                                                    <a href="#" class="ptb_module_btn ptb_toggle_module"></a>
                                                                    <a href="#" class="ptb_module_btn ptb_delete_module"></a>
                                                                </div>
                                                            </div>
                                                            <div data-type="<?php echo $type ?>" class="ptb_back_active_module_content">                                                                                                                         
                                                                <?php do_action('before_template_row', $id, $module, $this->type, $languages); ?>
                                                                <?php if (has_action($method)): ?>
                                                                    <?php do_action($method, $type, $id, $args, $module, $post_support, $languages); ?>
                                                                <?php else: ?>
                                                                    <?php if (!$metabox): ?>
                                                                        <?php do_action('ptb_template_' . $type, $id, $this->type, $args, $module, $languages) ?>
                                                                    <?php else: ?>
                                                                        <?php $this->get_main_fields($id, $name, $module, $languages) ?>
                                                                    <?php endif; ?>  
                                                                    <?php
                                                                    if ($type != 'custom_text' && $type != 'editor') {
                                                                        PTB_CMB_Base::module_multi_text($id, $module, $languages, 'text_before', __('Text Before', 'ptb'));
                                                                        PTB_CMB_Base::module_multi_text($id, $module, $languages, 'text_after', __('Text After', 'ptb'));
                                                                    }
                                                                    ?>
                                                                    <div class="ptb_back_active_module_row">
                                                                        <div class="ptb_back_active_module_label">
                                                                            <label for="ptb_<?php echo $id ?>[css]"><?php _e('Custom CSS Class', 'ptb') ?></label>
                                                                        </div>
                                                                        <div class="ptb_back_active_module_input">
                                                                            <input id="ptb_<?php echo $id ?>[css]" class="ptb_towidth" type="text"  name="[<?php echo $id ?>][css]" value="<?php echo isset($module['css']) && $module['css'] ? $module['css'] : '' ?>" />
                                                                        </div>
                                                                    </div>
                                                                    <div class="ptb_back_active_module_row">
                                                                        <div class="ptb_back_active_module_label">
                                                                            <label for="ptb_<?php echo $id ?>[display_inline]"><?php _e('Display Inline', 'ptb') ?></label>
                                                                        </div>
                                                                        <div class="ptb_back_active_module_input">
                                                                            <label>
                                                                                <input id="ptb_<?php echo $id ?>[display_inline]" type="checkbox"
                                                                                       name="[<?php echo $id ?>][display_inline]"
                                                                                       <?php if (isset($module['display_inline']) && $module['display_inline']): ?>checked="checked"<?php endif; ?>  />
                                                                                       <?php _e('Display this module inline (float left)', 'ptb'); ?>
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                <?php endif; ?>
                                                                <?php do_action('after_template_row', $id, $module, $this->type, $languages); ?>
                                                            </div>
                                                        </div>
                                                    </div>

                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="ptb_back_row ptb_first_row ptb_new-themplate">
                        <div class="ptb_back_row_top">
                            <div class="ptb_left">
                                <div class="ptb_grid_menu">
                                    <a class="ptb_row_btn ptb_grid_options"></a>
                                    <div class="ptb_grid_list_wrapper">
                                        <ul class="ptb_grid_list clearfix">
                                            <li>
                                                <ul>
                                                    <li><a href="#" class="ptb_column_select ptb_grid_1_1"
                                                           data-grid=["1-1"]></a></li>
                                                    <li><a href="#" class="ptb_column_select ptb_grid_4_2_4_2"
                                                           data-grid=["4-2","4-2"]></a></li>
                                                    <li><a href="#" class="ptb_column_select ptb_grid_3_1_3_1_3_1"
                                                           data-grid=["3-1","3-1","3-1"]></a></li>
                                                    <li><a href="#" class="ptb_column_select ptb_grid_4_1_4_1_4_1_4_1"
                                                           data-grid=["4-1","4-1","4-1","4-1"]></a></li>
                                                    <li><a href="#" class="ptb_column_select ptb_grid_5_1_5_1_5_1_5_1_5_1"
                                                           data-grid=["5-1","5-1","5-1","5-1","5-1"]></a></li>
                                                    <li><a href="#" class="ptb_column_select ptb_grid_6_1_6_1_6_1_6_1_6_1_6_1"
                                                           data-grid=["6-1","6-1","6-1","6-1","6-1","6-1"]></a></li>
                                                </ul>
                                            </li>
                                            <li>
                                                <ul>
                                                    <li><a href="#" class="ptb_column_select ptb_grid_4_1_4_3"
                                                           data-grid=["4-1","4-3"]></a></li>
                                                    <li><a href="#" class="ptb_column_select ptb_grid_4_1_4_1_4_2"
                                                           data-grid=["4-1","4-1","4-2"]></a></li>
                                                    <li><a href="#" class="ptb_column_select ptb_grid_4_1_4_2_4_1"
                                                           data-grid=["4-1","4-2","4-1"]></a></li>
                                                    <li><a href="#" class="ptb_column_select ptb_grid_4_2_4_1_4_1"
                                                           data-grid=["4-2","4-1","4-1"]></a></li>
                                                    <li><a href="#" class="ptb_column_select ptb_grid_4_3_4_1"
                                                           data-grid=["4-3","4-1"]></a></li>
                                                </ul>
                                            </li>
                                            <li>
                                                <ul>
                                                    <li><a href="#" class="ptb_column_select ptb_grid_3_2_3_1"
                                                           data-grid=["3-2","3-1"]></a></li>
                                                    <li><a href="#" class="ptb_column_select ptb_grid_3_1_3_2"
                                                           data-grid=["3-1","3-2"]></a></li>
                                                </ul>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="ptb_right">
                                <a href="#" class="ptb_row_btn ptb_toggle_module"></a>
                                <a href="#" class="ptb_row_btn ptb_delete_module"></a>
                            </div>
                        </div>
                        <div class="ptb_back_row_content">
                            <?php //6 is the maximum number of grids   ?>
                            <?php for ($i = 0; $i < 6; ++$i): ?>
                                <div class="ptb_back_col">
                                    <div class="ptb_module_holder">
                                        <div class="ptb_empty_holder_text"><?php _e('Drop module here', 'ptb') ?></div>
                                    </div>
                                </div>
                            <?php endfor; ?>
                        </div>
                    </div>
                <?php endif; ?>
                <div class="ptb_add_row ptb_cmb_add_field"><span class="ti-plus circle"></span><?php _e('Add Row', 'ptb') ?></div>
            </div>
        </div>
        <?php
    }

    protected function get_field_name($input_key) {
        return sprintf('%s_%s_%s', $this->plugin_name, self::$key, $input_key);
    }

    protected function get_field_id($field_key) {

        return sprintf('%s_%s_%s', $this->plugin_name, self::$key, $field_key);
    }

    /**
     * Save post themplate
     *
     * @since 1.0.0
     *
     * @param post array $data
     */
    public function save_themplate($data) {
        $post_type = $this->get_ptt();

        if ($post_type) {
            $this->type = $data[$this->plugin_name . '_type'];
            if (!isset($post_type[$this->type])) {
                $post_type[$this->type] = array();
            }
            $post_type[$this->type]['layout'] = array();
            if (isset($data[$this->plugin_name . '_layout'])) {
                $layout = stripslashes_deep($data[$this->plugin_name . '_layout']);
                $post_type[$this->type]['layout'] = json_decode($layout, true);
            }

            $_keys = $this->type == PTB_Post_Type_Template::ARCHIVE ? array('layout_post', 'offset_post', 'orderby_post', 'order_post', 'pagination_post') : array('navigation_post');
            foreach ($_keys as $key) {
                $fieldname = $this->get_field_name($key);
                if (isset($data[$fieldname])) {
                    $post_type[$this->type][$fieldname] = sanitize_text_field($data[$fieldname]);
                }
            }
            $post_type = apply_filters('ptb_template_save', $post_type, $data);
            $this->options->option_post_type_templates[$this->themplate_id] = $post_type;
            $this->options->update();
            die(json_encode(array(
                'status' => '1',
                'text' => __('Template successfully updated', 'ptb')
            )));
        }
    }

    protected function get_ptt() {
        $ptt = null;
        if ($this->options->has_post_type_template($this->themplate_id)) {
            $ptt_options = $this->options->get_templates_options();
            $ptt = $ptt_options[$this->themplate_id];
        }

        return $ptt;
    }

    protected function get_edit_value($key, $default) {

        $ptt = $this->get_ptt();

        $value = ( isset($ptt) && array_key_exists($key, $ptt) ? $ptt[$key] : $default );

        return $value;
    }

    /**
     * Render post fields
     *
     * @since 1.0.0
     * @param string $type
     * @param string $name
     * @param array $data
     * @param array $languages
     */
    protected function get_main_fields($type, $name, array $data = array(), array $languages = array()) {
        switch ($type):
            case 'editor':
            case 'author':
            case 'comments':
                ?>
                <input type="hidden" name="[<?php echo $type ?>][<?php echo $type ?>]"/>
                <?php break; ?>
            <?php
            case 'title':
                ?>
                <div class="ptb_back_active_module_row">
                    <div class="ptb_back_active_module_label">
                        <label for="ptb_title_tag"><?php _e('HTML Tag', 'ptb') ?></label>
                    </div>
                    <div class="ptb_back_active_module_input">
                        <div class="ptb_custom_select">
                            <select name="[<?php echo $type ?>][title_tag]" id="<?php echo $this->plugin_name ?>_title_tag">
                                <?php for ($i = 1; $i <= 6; ++$i): ?>
                                    <option
                                        <?php if (isset($data['title_tag']) && $data['title_tag'] == $i): ?>selected="selected"<?php endif; ?>
                                        value="<?php echo $i ?>">h<?php echo $i ?></option>
                                    <?php endfor; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <?php PTB_CMB_Base::link_to_post('title', $this->type, $data); ?>
                <?php break; ?>
            <?php case 'excerpt': ?>
                <div class="ptb_back_active_module_row">
                    <div class="ptb_back_active_module_label"><label for="ptb_excerpt_count"><?php _e('Word Count', 'ptb') ?></label></div>
                    <div class="ptb_back_active_module_input">
                        <input id="ptb_excerpt_count" type="text" class="ptb_xsmall"
                               name="[<?php echo $type ?>][excerpt_count]"
                               <?php if (isset($data['excerpt_count'])): ?>value="<?php echo $data['excerpt_count'] ?>"<?php endif; ?> />
                               <?php _e('Words', 'ptb') ?>
                        <input type="hidden" value="1" name="[<?php echo $type ?>][can_be_empty]" />
                    </div>
                </div>
                <?php break; ?>
            <?php case 'custom_text': ?>
                <div class="ptb_back_active_module_row ptb_<?php echo $type ?>">
                    <?php if (count($languages) > 1): ?>
                        <ul class="ptb_language_tabs">
                            <?php foreach ($languages as $code => $lng): ?>
                                <li <?php if (isset($lng['selected'])): ?>class="ptb_active_tab_lng"<?php endif; ?>>
                                    <a class="ptb_lng_<?php echo $code ?>" title="<?php echo $lng['name'] ?>" href="#"></a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                    <ul class="ptb_language_fields">
                        <?php foreach ($languages as $code => $lng): ?>
                            <li <?php if (isset($lng['selected'])): ?>class="ptb_active_lng"<?php endif; ?>>
                                <textarea class="ptb_wp_editor"
                                          name="[<?php echo $type ?>][text][<?php echo $code ?>]">
                                              <?php if (isset($data['text'][$code])): ?> <?php echo $data['text'][$code] ?><?php endif; ?>
                                </textarea>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php break; ?>
            <?php case 'taxonomies': ?>
                <?php if (!empty($this->post_taxonomies)): ?>

                    <div class="ptb_back_active_module_row">
                        <div class="ptb_back_active_module_label">
                            <label for="ptb_select_taxonomies"><?php _e('Select Taxonomies', 'ptb') ?></label>
                        </div>
                        <div class="ptb_back_active_module_input">
                            <div class="ptb_custom_select">
                                <select id="ptb_select_taxonomies" name="[<?php echo $type ?>][taxonomies]">
                                    <?php foreach ($this->post_taxonomies as $tax => $tax_name): ?>
                                        <option
                                            <?php if (isset($data['taxonomies']) && $data['taxonomies'] == $tax): ?>selected="selected"<?php endif; ?>
                                            value="<?php echo $tax ?>"><?php echo $tax_name ?></option>
                                        <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="ptb_back_active_module_row">
                        <div class="ptb_back_active_module_label">
                            <label for="ptb_seperator_taxonomies"><?php _e('Seperator', 'ptb') ?></label>
                        </div>
                        <div class="ptb_back_active_module_input">
                            <input id="ptb_seperator_taxonomies" type="text" class="ptb_towidth"
                                   name="[<?php echo $type ?>][seperator]"
                                   <?php if (isset($data['seperator'])): ?>value="<?php echo $data['seperator'] ?>"<?php endif; ?> />
                        </div>
                    </div>
                <?php endif; ?>
                <?php break; ?>
            <?php case 'date': ?>
                <div class="ptb_back_active_module_row">
                    <div class="ptb_back_active_module_label">
                        <label for="ptb_date_format"><?php _e('Date Format', 'ptb') ?></label>
                    </div>
                    <div class="ptb_back_active_module_input">
                        <input id="ptb_date_format" type="text"
                               class="ptb_towidth" name="[<?php echo $type ?>][date_format]"
                               <?php if (isset($data['date_format'])): ?>value="<?php echo $data['date_format'] ?>"<?php endif; ?> />
                               <?php _e('(e.g. M j,Y)', 'ptb') ?> <a
                            href="//codex.wordpress.org/Formatting_Date_and_Time"
                            target="_blank"><?php _e('More info', 'ptb') ?></a>
                    </div>
                </div>
                <?php break; ?>
            <?php
            case 'post_tag':
            case 'category':
                ?>
                <div class="ptb_back_active_module_row">
                    <div class="ptb_back_active_module_label">
                        <label for="ptb_category_seperator"><?php _e('Seperator', 'ptb') ?></label>
                    </div>
                    <div class="ptb_back_active_module_input">
                        <input id="ptb_category_seperator" type="text" class="ptb_towidth"
                               name="[<?php echo $type ?>][seperator]"
                               <?php if (isset($data['seperator'])): ?>value="<?php echo $data['seperator'] ?>"<?php endif;
                               ?> />
                    </div>
                </div>
                <?php break; ?>
            <?php case 'thumbnail': ?>
                <div class="ptb_back_active_module_row">
                    <div class="ptb_back_active_module_label">
                        <label for="ptb_thumbnail_width"><?php _e('Image Dimension', 'ptb') ?></label>
                    </div>
                    <div class="ptb_back_active_module_input">
                        <input id="ptb_thumbnail_width" type="text" class="ptb_xsmall"
                               name="[<?php echo $type ?>][width]"
                               <?php if (isset($data['width'])): ?>value="<?php echo $data['width'] ?>"<?php endif; ?> />
                        <label><?php _e('Width', 'ptb') ?></label>
                        <input type="text" class="ptb_xsmall"
                               name="[<?php echo $type ?>][height]"
                               <?php if (isset($data['height'])): ?>value="<?php echo $data['height'] ?>"<?php endif; ?> />
                        <label><?php _e('Height', 'ptb') ?>(px)</label>
                    </div>
                </div>
                <?php PTB_CMB_Base::link_to_post('thumbnail', $this->type, $data); ?>
                <?php break; ?>
            <?php case 'custom_image': ?>
                <div class="ptb_back_active_module_row">
                    <div class="ptb_back_active_module_label">
                        <label for="ptb_custom_image_file"><?php _e('Image File', 'ptb') ?></label>
                    </div>
                    <div class="ptb_back_active_module_input">
                        <div class="ptb_post_image_wrapper">
                            <div class="ptb_post_image_thumb_wrapper">
                                <div class="ptb_post_image_thumb" <?php if (isset($data['image'])): ?> style="background-image: url(<?php echo $data['image'] ?>)"<?php endif; ?>></div>
                            </div>
                            <div class="ptb_post_image_add_wrapper">
                                <input id="ptb_custom_image_file" type="text" class="ptb_towidth"
                                       name="[<?php echo $type ?>][image]"
                                       <?php if (isset($data['image'])): ?>value="<?php echo $data['image'] ?>"<?php endif; ?> />
                                <a href="#" onclick="PTB.ImageUpload(this)" class="ptb_post_image_add">+<?php _e('Media Library', 'ptb') ?></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="ptb_back_active_module_row">
                    <div class="ptb_back_active_module_label">
                        <label><?php _e('Image Dimension', 'ptb') ?></label>
                    </div>
                    <div class="ptb_back_active_module_input">
                        <input id="ptb_width" type="text"
                               class="ptb_xsmall"
                               name="[<?php echo $type ?>][width]"
                               <?php if (isset($data['width'])): ?>value="<?php echo $data['width'] ?>"<?php endif; ?> />
                        <label for="ptb_width"><?php _e('Width', 'ptb') ?></label>
                        <input id="ptb_height" type="text"
                               class="ptb_xsmall"
                               name="[<?php echo $type ?>][height]"
                               <?php if (isset($data['height'])): ?>value="<?php echo $data['height'] ?>"<?php endif; ?> />
                        <label for="ptb_height"><?php _e('Height', 'ptb') ?>(px)</label>
                    </div>
                </div>
                <div class="ptb_back_active_module_row">
                    <div class="ptb_back_active_module_label">
                        <label for="ptb_custom_image_link"><?php _e('Image Link', 'ptb') ?></label>
                    </div>
                    <div class="ptb_back_active_module_input">
                        <input id="ptb_custom_image_link" type="text" class="ptb_towidth" name="[<?php echo $type ?>][link]"
                               <?php if (isset($data['link'])): ?>value="<?php echo $data['link'] ?>"<?php endif; ?>/>
                    </div>
                </div>
                <?php break; ?>
            <?php case 'permalink': ?>
                <?php PTB_CMB_Base::module_multi_text($type, $data, $languages, 'text', __('Text', 'ptb')); ?>
                <?php do_action('ptb_template_link_button', $type, $this->type, array(), $data, $languages) ?>
                <?php break; ?>
        <?php endswitch; ?>

        <?php
    }

    /**
     * Frontend layout render
     *
     * @since 1.0.0
     * @param array   $layout
     * @param array   $post_support
     * @param array   $cmb_options
     * @param array   $post_meta
     * @param string  $post_type 
     * @param boolean $is_single 
     */
    public function display_public_themplate(array $template, array $post_support, array $cmb_options, array $post_meta, $post_type, $is_single = false) {
        $post_meta = apply_filters('ptb_filter_post_meta', $post_meta, $post_type, $cmb_options, $is_single);
        $lang = PTB_Utils::get_current_language_code();
        $layout = $template['layout'];
        $count = count($layout) - 1;
        ob_start();
        ?>
        <div class="ptb_items_wrapper entry-content" itemscope itemtype="http://schema.org/Article">
            <?php foreach ($layout as $k => $row): ?>
                <?php
                $class = '';
                if ($k == 0) {
                    $class .= 'first';
                } elseif ($k == $count) {
                    $class .= 'last';
                }
                ?>
                <div class="<?php if ($class): ?><?php echo 'ptb_' . $class . '_row' ?> <?php endif; ?>ptb_row ptb_<?php echo $post_type ?>_row">
                    <?php
                    if (!empty($row)):
                        $colums_count = count($row) - 1;
                        $i = 0;
                        foreach ($row as $col_key => $col):
                            ?>
                            <?php
                            $tmp_key = explode('-', $col_key);
                            $key = $tmp_key[0] . '-' . $tmp_key[1];
                            ?>
                            <div class="ptb_col ptb_col<?php echo $key ?><?php if ($i == 0): ?> ptb_col_first<?php elseif ($i == $colums_count): ?> ptb_col_last<?php endif; ?>">
                                <?php if (!empty($col)): ?>
                                    <?php foreach ($col as $index => $module): ?>
                                        <?php
                                        $meta_key = $module['key'];
                                        if (!isset($cmb_options[$meta_key])) {
                                            continue;
                                        }
                                        if ($module['type'] != 'custom_text' && $module['type'] != 'editor') {
                                            foreach ($module as &$values) {
                                                if (!is_array($values)) {
                                                    $values = sanitize_text_field($values);
                                                } elseif (!empty($values)) {
                                                    foreach ($values as &$value) {
                                                        $value = sanitize_text_field($value);
                                                    }
                                                }
                                            }
                                        } else {
                                            $meta_data = array();
                                        }
                                        $args = $cmb_options[$meta_key];
                                        $type = $module['type'];
                                        $args['key'] = $meta_key;
                                        $fields = in_array($type, $post_support);
                                        ?>
                                        <?php if ($fields || (isset($args['can_be_empty'])) || (isset($post_meta['ptb_' . $meta_key]) && (!empty($post_meta['ptb_' . $meta_key]) || $post_meta['ptb_' . $meta_key]==='0'))): ?>

                                            <div class="<?php echo isset($module['css']) && $module['css'] ? trim($module['css']) . ' ' : '' ?>ptb_module ptb_<?php echo $type ?><?php if (!$fields): ?> ptb_<?php echo $meta_key ?><?php endif; ?><?php echo isset($module['display_inline']) ? ' ptb_module_inline' : '' ?>">
                                                <?php
                                                if (!$fields) {
                                                    $meta_value = isset($post_meta['ptb_' . $meta_key]) && (!empty($post_meta['ptb_' . $meta_key]) || $post_meta['ptb_' . $meta_key]==='0')? $post_meta['ptb_' . $meta_key] : false;
                                                    if ($meta_value || $meta_value==='0') {
                                                        $meta_data = maybe_unserialize(current($meta_value));
                                                        if ($meta_data === false) {
                                                            $meta_data = current($meta_value);
                                                        }
                                                        if (!is_array($meta_data) || !isset($meta_data[$meta_key])) {
                                                            $post_meta[$meta_key] = $meta_data;
                                                            if (!is_array($meta_data)) {
                                                                $meta_data = array($meta_data);
                                                            }
                                                        }
                                                        $meta_data = array_merge($meta_data, $post_meta);
                                                    } else {
                                                        $meta_data = $post_meta;
                                                    } 
                                                }
                                                if (has_action('ptb_custom_' . $type)) {
                                                    do_action('ptb_custom_' . $type, $args, $module, $meta_data, $lang, $is_single, $k . '_' . $col_key . '_' . $index);
                                                } else {
                                                    ob_start();
                                                    if ($fields) {
                                                        $this->get_public_main_fields($type, $args, $module, $post_meta, $lang, $is_single, $k . '_' . $col_key . '_' . $index);
                                                    } else {
                                                        apply_filters('ptb_template_public' . $type, $args, $module, $meta_data, $lang, $is_single, $k . '_' . $col_key . '_' . $index);
                                                    }
                                                    $cont = trim(ob_get_contents());
                                                    ob_end_clean();
                                                    if ($cont || $cont==='0') {
                                                        if (isset($module['text_before'][$lang])) {
                                                            PTB_CMB_Base::get_text_after_before($module['text_before'][$lang], true);
                                                        }
                                                        echo $cont;
                                                        if (isset($module['text_after'][$lang])) {
                                                            PTB_CMB_Base::get_text_after_before($module['text_after'][$lang], false);
                                                        }
                                                    }
                                                }
                                                ?>
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                            <?php ++$i; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
            <?php if ($is_single && isset($template['ptb_ptt_navigation_post']) && $template['ptb_ptt_navigation_post'] > 0): ?>
                <div class="ptb-post-nav clearfix">
                    <?php previous_post_link('<span class="ptb-prev">%link</span>', '<span class="ptb-arrow">' . _x('&laquo;', 'Previous entry link arrow', 'ptb') . '</span> %title', 1) ?>
                    <?php next_post_link('<span class="ptb-next">%link</span>', '<span class="ptb-arrow">' . _x('&raquo;', 'Next entry link arrow', 'ptb') . '</span> %title', 1) ?>
                </div> 
            <?php endif; ?>  
        </div>  
        <?php
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }

    /**
     * Frontend post fields render
     *
     * @since 1.0.0
     * @param string $type
     * @param array $args
     * @param array $data
     * @param array $meta_data
     * @param array $lang
     * @param boolean $is_single single page
     * @param string $index index in themplate
     */
    protected function get_public_main_fields($type, array $args, array $data, array $meta_data, $lang = false, $is_single = false, $index = false) {
        switch ($type):
            case 'title':
                ?>
                <h<?php echo $data['title_tag'] ?> class="ptb_post_title ptb_entry_title" itemprop="name">
                    <?php
                    if (!empty($data['title_link'])) {
                        echo '<a ' . ($data['title_link'] == 'lightbox' ? 'data-href="' . admin_url('admin-ajax.php?id=' . get_the_ID() . '&action=ptb_single_lightbox') . '" class="ptb_open_lightbox"' : '') . ($data['title_link'] == 'new_window' ? 'target="_blank"' : '') . 'href="' . $meta_data['post_url'] . '">';
                    }
                    the_title();
                    if (isset($data['title_link']) && $data['title_link']) {
                        echo '</a>';
                    }
                    ?>
                </h<?php echo $data['title_tag'] ?>>
                <?php
                break;
            case 'excerpt':
                ?>
                <?php
                PTB_Public::$render_content = true;
                $excerpt = $meta_data['post_excerpt'];
                ?>
                <div itemprop="articleBody">
                    <?php echo $excerpt && $data['excerpt_count'] > 0 ? wp_trim_words($excerpt, $data['excerpt_count']) : get_the_excerpt(); ?>
                </div>
                <?php
                PTB_Public::$render_content = false;
                break;
            case 'author':
                ?>
                <?php if (isset($meta_data['post_author']) && $meta_data['post_author']): ?>
                    <span class="ptb_post_author ptb_post_meta">
                        <span class="ptb_author" itemprop="author" itemscope itemtype="http://schema.org/Person"><a href="<?php echo esc_url(get_author_posts_url($meta_data['post_author'])) ?>" rel="author" itemprop="url"><span itemprop="name"><?php echo get_the_author(); ?></span></a></span>
                    </span>
                <?php endif; ?>
                <?php
                break;
            case 'custom_text':
                ?>
                <?php if (!empty($data['text'][$lang])): ?>
                    <?php echo PTB_CMB_Base::format_text($data['text'][$lang],$is_single); ?>
                <?php endif; ?>
                <?php
                break;
            case 'taxonomies':
                ?>
                <?php if (!empty($meta_data['taxonomies'])): ?>
                        <?php $taxs = array(); ?>
                        <?php foreach ($meta_data['taxonomies'] as $tax): ?>
                            <?php if (isset($tax->taxonomy) && $data['taxonomies'] == $tax->taxonomy): ?>
                                <?php
                                $term_link = get_term_link($tax, $tax->taxonomy);
                                $taxs[$tax->term_id] = '<a href="' . $term_link . '">' . $tax->name . '</a>';
                                ?>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        <?php if (!empty($taxs)): ?>
                            <?php
                            if (!$data['seperator']) {
                                $data['seperator'] = ', ';
                            }
                            ?>
                            <div class="ptb_module_inline ptb_taxonomies_<?php echo str_replace('-', '_', $data['taxonomies']) ?>">
                                <?php echo implode($data['seperator'], $taxs) ?>
                            </div>
                        <?php endif; ?>
                <?php endif; ?>
                <?php
                break;
            case 'date':
                ?>
                <time class="ptb_post_date ptb_post_meta" datetime="<?php echo date('Y-m-d', strtotime($meta_data['post_date'])) ?>" itemprop="datePublished">
                    <?php if (isset($data['date_format']) && $data['date_format']): ?>
                        <?php echo date_i18n($data['date_format'], strtotime($meta_data['post_date'])) ?>
                    <?php else: ?>
                        <?php echo $meta_data['post_date'] ?>
                    <?php endif; ?>
                </time>
                <?php
                break;
            case 'post_tag':
            case 'category':
                $key = $type == 'post_tag' ? 'tags_input' : 'post_category';
                ?>
                <?php if (!empty($meta_data[$key])): ?>
                    <span class="ptb_post_category ptb_post_meta">
                        <?php
                        if (!$data['seperator']) {
                            $data['seperator'] = ',';
                        }
                        ?>
                        <?php if ($key == 'tags_input'): ?>
                            <?php the_tags('', $data['seperator'], ''); ?>
                        <?php else: ?>
                            <?php the_category($data['seperator']); ?>
                        <?php endif; ?>
                    </span>   
                <?php endif; ?>
                <?php
                break;
            case 'thumbnail':
                ?>
                <?php if (has_post_thumbnail()): ?>

                    <?php
                    $thumb_id = get_post_thumbnail_id();
                    $thumb = get_post(get_post_thumbnail_id());
                    $url = wp_get_attachment_url($thumb_id);
                    $url = PTB_CMB_Base::ptb_resize($url, $data['width'], $data['height']);
                    $title = $thumb->post_title ? $thumb->post_title : (isset($meta_data['post_title']) ? $meta_data['post_title'] : '');
                    $alt = get_post_meta($thumb_id, '_wp_attachment_image_alt', true);
                    if (!$alt) {
                        $alt = $title;
                    }
                    ?>
                    <figure class="ptb_post_image clearfix">
                        <?php
                        if (isset($data['thumbnail_link']) && $data['thumbnail_link']): echo '<a ' . ($data['thumbnail_link'] == 'lightbox' ? 'data-href="' . admin_url('admin-ajax.php?id=' . get_the_ID() . '&action=ptb_single_lightbox') . '" class="ptb_open_lightbox"' : '') . ($data['thumbnail_link'] == 'new_window' ? 'target="_blank"' : '') . ' href="' . $meta_data['post_url'] . '">';
                        endif;
                        ?>
                        <img src="<?php echo $url ?>" alt="<?php echo $alt ?>"
                             title="<?php echo $title ?>"/>
                             <?php
                             if (isset($data['thumbnail_link']) && $data['thumbnail_link']): echo '</a>';
                             endif;
                             ?>
                    </figure>
                <?php endif; ?>
                <?php
                break;
            case 'custom_image':
                ?>
                <?php if (!empty($data['image'])): ?>
                    <?php
                    $url = PTB_CMB_Base::ptb_resize($data['image'], $data['width'], $data['height']);
                    ?>
                    <figure class="ptb_post_image clearfix">
                        <?php
                        if (isset($data['link']) && $data['link']): echo '<a href="' . $data['link'] . '">';
                        endif;
                        ?>
                        <img src="<?php echo $url ?>" />
                        <?php
                        if (isset($data['link']) && $data['link']): echo '</a>';
                        endif;
                        ?>
                    </figure>
                <?php endif; ?>
                <?php
                break;
            case 'editor':
                PTB_Public::$render_content = true;
                ?>
                <div class="ptb_entry_content" itemprop="articleBody">
                    <?php the_content(); ?>
                </div>
                <?php
                PTB_Public::$render_content = false;
                break;
            case 'comments':
                ?>
                <div class="ptb_comments">
                    <?php
                    //Gather comments for a specific page/post
                    $comments = get_comments(array(
                        'post_id' => get_the_ID(),
                        'status' => 'approve' //Change this to the type of comments to be displayed
                    ));
                    ?>
                    <ul class="commentlist">    
                        <?php
                        //Display the list of comments
                        wp_list_comments(array(
                            'per_page' => 10, //Allow comment pagination
                            'reverse_top_level' => false //Show the latest comments at the top of the list
                                ), $comments);
                        ?>
                    </ul>
                    <?php comment_form(); ?>
                </div>
                <?php
                break;
            case 'permalink':
                $class = $style = array();
                $none = true;
                if (!empty($data['styles'])) {
                    if (!is_array($data['styles'])) {
                        $data['styles'] = array($data['styles']);
                    }
                    $none = array_search('none', $data['styles']) === FALSE;
                    $class[] = $none ? implode(' ', $data['styles']) : 'none';
                }
                if ($none) {
                    if (isset($data['custom_color']) && $data['custom_color']) {
                        $style[] = 'background-color:' . $data['custom_color'] . ' !important;';
                    } elseif (isset($data['color'])) {
                        $class[] = $data['color'];
                    }
                }
                if (isset($data['size']) && $data['size']) {
                    $class[] = $data['size'];
                }
                if (isset($data['icon']) && $data['icon']) {
                    $class[] = 'fa';
                    $class[] = $data['icon'];
                }
                if (isset($data['link_link']) && $data['link_link'] === 'lightbox') {
                    $class[] = 'ptb_open_lightbox';
                    $meta_data['post_url'] = admin_url('admin-ajax.php?id=' . get_the_ID() . '&action=ptb_single_lightbox');
                }

                if (isset($data['text_color']) && $data['text_color']) {
                    $style[] = 'color:' . $data['text_color'] . ' !important;';
                }
                ?>
                <div class="ptb_permalink">
                    <a  <?php if (!empty($style)): ?>style="<?php esc_attr_e(implode(' ', $style)) ?>"<?php endif; ?> class="ptb_link_button <?php if (!empty($class)): ?><?php if ($none): ?>shortcode <?php endif; ?><?php esc_attr_e(implode(' ', $class)) ?><?php endif; ?>" <?php if (isset($data['link_link']) && $data['link_link'] === 'new_window'): ?>target="_blank"<?php endif; ?>
                                                     href="<?php echo $meta_data['post_url'] ?>"><?php echo isset($data['text'][$lang]) ? $data['text'][$lang] : '' ?></a>    
                </div>
                <?php break; ?>
        <?php endswitch; ?>
        <?php
    }

}
