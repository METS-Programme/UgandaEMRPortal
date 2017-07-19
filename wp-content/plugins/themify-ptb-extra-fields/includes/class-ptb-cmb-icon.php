<?php

/**
 * Custom meta box class to create icon
 *
 * @link       http://themify.me
 * @since      1.0.0
 *
 * @package    PTB
 * @subpackage PTB/includes
 * @author     Themify <ptb@themify.me>
 */
class PTB_CMB_Icon extends PTB_Extra_Base {

    private $icons = array();

    /**
     * Adds the custom meta type to the plugin meta types array
     *
     * @since 1.0.0
     *
     * @param array $cmb_types Array of custom meta types of plugin
     *
     * @return array
     */
    public function filter_register_custom_meta_box_type($cmb_types) {

        $cmb_types[$this->get_type()] = array(
            'name' => __('Icon', 'ptb_extra')
        );

        return $cmb_types;
    }

    /**
     * Renders the meta boxes for themplate
     *
     * @since 1.0.0
     *
     * @param string $id the metabox id
     * @param string $type the type of the page(Arhive or Single)
     * @param array $args Array of custom meta types of plugin
     * @param array $data saved data
     * @param array $languages languages array
     */
    public function action_them_themplate($id, $type, $args, $data = array(), array $languages = array()) {
        $sizes = array('small' => __('Small', 'ptb_extra'), 'medium' => __('Medium', 'ptb_extra'), 'large' => __('Large', 'ptb_extra'));
        $links = array('lightbox' => __('Lightbox', 'ptb_extra'), 'new_window' => __('New Window'), '0' => __('No', 'ptb_extra'));
        ?>
        <div class="ptb_back_active_module_row">
            <div class="ptb_back_active_module_label">
                <label for="ptb_<?php echo $id ?>[size]"><?php _e('Size', 'ptb_extra') ?></label>
            </div>
            <div class="ptb_back_active_module_input">
                <div class="ptb_custom_select">
                    <select id="ptb_<?php echo $id ?>[size]"
                            name="[<?php echo $id ?>][size]">
                                <?php foreach ($sizes as $s => $name): ?>
                            <option <?php if (isset($data['size']) && $data['size'] == $s): ?>selected="selected"<?php endif; ?>value="<?php echo $s ?>"><?php echo $name ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="ptb_back_active_module_row">
            <div class="ptb_back_active_module_label">
                <label for="ptb_<?php echo $id ?>[lightbox]"><?php _e('Open in', 'ptb_extra') ?></label>
            </div>
            <div class="ptb_back_active_module_input">
                <?php foreach ($links as $l => $n): ?>
                    <input type="radio" id="ptb_<?php echo $id ?>_radio_<?php echo $l ?>"
                           name="[<?php echo $id ?>][icon_link]" value="<?php echo $l ?>"
                           <?php if ((!isset($data['icon_link']) && $l == '0') || ( isset($data['icon_link']) && $data['icon_link'] == "$l")): ?>checked="checked"<?php endif; ?>/>
                    <label for="ptb_<?php echo $id ?>_radio_<?php echo $l ?>"><?php echo $n ?></label>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    }

    /**
     * @param string $id the id template
     * @param array $languages
     */
    public function action_template_type($id, array $languages) {
        
    }

    /**
     * Renders the meta boxes in public
     *
     * @since 1.0.0
     *
     * @param array $args Array of custom meta types of plugin
     * @param array $data themplate data
     * @param array or string $meta_data post data
     * @param string $lang language code
     * @param boolean $is_single single page
     */
    public function action_public_themplate(array $args, array $data, $meta_data, $lang = false, $is_single = false) {
        $icons = $meta_data[$args['key']];
        ?>
        <?php if (isset($icons['icon']) && !empty($icons)): ?>
            <?php
            $size = isset($data['size']) ? 'ptb_extra_icons_' . $data['size'] : '';
            $classes = array();
            $new_window = false;
            $classes[] = 'ptb_extra_icon_link';
            if (isset($data['icon_link']) && $data['icon_link'] == 'lightbox') {
                $classes[] = 'ptb_lightbox';
            }
            elseif(isset($data['icon_link']) && $data['icon_link'] == 'new_window'){
                $new_window = true;
            }
            $classes = implode(' ', $classes);
            ?>
            <ul class="ptb_extra_icons <?php echo $size; ?>">
                <?php foreach ($icons['icon'] as $key => $ic): ?>
                    <li class="ptb_extra_icon">
                        <?php $color = isset($icons['color'][$key]) && $icons['color'][$key] ? 'style="color:' . esc_attr($icons['color'][$key]) . ';"' : ''; ?>
                        <?php if (isset($icons['url'][$key]) && $icons['url'][$key]): ?>
                            <a <?php echo $color ?> class="<?php echo $classes ?>" <?php if ($new_window): ?>target="_blank"<?php endif; ?> href="<?php echo esc_url($icons['url'][$key]) ?>">
                                <i class="fa fa-<?php esc_attr_e($ic) ?>"></i>
                                <span class="ptb_extra_icon_label"><?php esc_attr_e($icons['label'][$key]) ?></span>
                            </a>
                        <?php else: ?>
                            <i <?php echo $color ?> class="fa fa-<?php esc_attr_e($ic) ?>"></i>
                            <span <?php echo $color ?> class="ptb_extra_icon_label"><?php esc_attr_e($icons['label'][$key]) ?></span>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
        <?php
    }

    /**
     * Renders the meta boxes on post edit dashboard
     *
     * @since 1.0.0
     *
     * @param WP_Post $post
     * @param string $meta_key
     * @param array $args
     */
    public function render_post_type_meta($post, $meta_key, $args) {
        $wp_meta_key = sprintf('%s_%s', $this->get_plugin_name(), $meta_key);
        $value = get_post_meta($post->ID, $wp_meta_key, true);
        $icon_name = sprintf('%s[icon][]', $meta_key);
        $url_name = sprintf('%s[url][]', $meta_key);
        $title_name = sprintf('%s[label][]', $meta_key);
        $color_name = sprintf('%s[color][]', $meta_key);
        $plugin_dir = plugin_dir_url(dirname(dirname(__FILE__)));
        ?>
        <fieldset class="ptb_cmb_input">
            <ul id="<?php echo $meta_key; ?>_options_wrapper" class="ptb_cmb_options_wrapper">
                <?php $values = is_array($value) && isset($value['icon']) ? $value['icon'] : array($value); ?>
                <?php foreach ($values as $index => $v): ?>
                    <?php
                    $label = isset($value['label']) && $value['label'][$index] ? esc_attr($value['label'][$index]) : '';
                    $url = isset($value['url']) && $value['url'][$index] ? esc_url($value['url'][$index]) : '';
                    $color = isset($value['color']) && $value['color'][$index] ? esc_attr($value['color'][$index]) : '';
                    ?>
                    <li class="<?php echo $meta_key; ?>_option_wrapper ptb_cmb_option">
                        <span class="ti-split-v ptb_cmb_option_sort"></span>
                        <div class="ptb_post_cmb_image_wrapper">
                            <a <?php if ($color): ?>style="color:<?php echo $color; ?>"<?php endif; ?> title="<?php _e('Choose icon', 'ptb_extra') ?>" href="<?php echo $plugin_dir ?>themify-ptb/admin/themify-icons/list.html" class="ptb_custom_lightbox ptb_post_cmb_image <?php if ($v): ?>fa fa-<?php echo $v ?><?php endif; ?>">
                                <span class="ti-plus"></span>
                            </a>
                        </div>
                        <input type="text" readonly="readonly" value="<?php esc_attr_e($v); ?>" name="<?php echo $icon_name ?>" value="" placeholder="<?php _e('Icon', 'ptb_extra') ?>" class="ptb_extra_input_icon" />
                        <input type="text" name="<?php echo $title_name; ?>" value="<?php esc_attr_e($label); ?>" placeholder="<?php _e('Label', 'ptb_extra') ?>"  class="ptb_extra_row_margin" />
                        <input type="text" name="<?php echo $url_name; ?>" value="<?php echo esc_url_raw($url) ?>" placeholder="<?php _e('Link', 'ptb_extra') ?>"/>
                        <input class="ptb_color_picker" value="<?php echo $color ?>" type="text" placeholder="<?php _e('Color', 'ptb_extra') ?>" name="<?php echo $color_name; ?>" />
                        <span class="<?php echo $meta_key; ?>_remove remove ti-close"></span>
                    </li>
                <?php endforeach; ?>
            </ul>
            <div id="<?php echo $meta_key; ?>_add_new" class="ptb_cmb_option_add">
                <span class="ti-plus"></span>
                <?php _e('Add new', 'ptb_extra') ?>
            </div>
        </fieldset>

        <?php
    }

    public function ptb_submission_form($post_type, array $args, array $module, $post, $lang, $languages) {
        $pluginurl = plugin_dir_url(dirname(__FILE__));
        wp_enqueue_style(self::$plugin_name . '-submission-icon-color', dirname($pluginurl) . '/themify-ptb/admin/css/jquery/jquery.minicolors.css', array(), self::$version, 'all');
        wp_enqueue_script(self::$plugin_name . '-submission-icon-color', dirname($pluginurl) . '/themify-ptb/admin/js/jquery/jquery.minicolors.js', array('ptb-submission'), self::$version, true);
        wp_enqueue_script(self::$plugin_name . '-submission-icon', $pluginurl . 'public/submission/js/icon.js', array(self::$plugin_name . '-submission-icon-color'), self::$version, true);
        $data = isset($post->ID) ? get_post_meta($post->ID, 'ptb_' . $args['key'], TRUE) : array();
        if (empty($data)) {
            $data = array('icon' => array(false));
            $title = array();
        } else {
            $title = $this->ptb_submission_lng_data($data['label'], $args['key'], 'label', $post->ID, $post_type, $languages);
        }
        ?>
        <div class="ptb_back_active_module_input ptb-submission-multi-text ptb_extra_submission_images">
            <ul>
                <?php foreach ($data['icon'] as $k => $v): ?>
                    <?php
                    $v = esc_attr($v);
                    $color = isset($data['color'][$k]) && $data['color'][$k] ? esc_attr($data['color'][$k]) : '';
                    ?>
                    <li class="ptb-submission-text-option">
                        <i title="<?php _e('Sort', 'ptb_extra') ?>" class="fa fa-sort ptb-submission-option-sort"></i>
                        <div class="ptb_back_active_module_input ptb_icon_wrap">
                            <div>
                                <a title="<?php _e('Choose icon', 'ptb_extra') ?>"  rel="nofollow" href="<?php echo plugin_dir_url(dirname(dirname(__FILE__))) ?>themify-ptb/admin/themify-icons/list.html" class="ptb_extra_submission_icon">
                                    <i <?php if ($color): ?>style="color:<?php echo $color ?>"<?php endif; ?> class="fa <?php echo $v ? 'fa-' . $v : 'fa-plus-circle' ?>"></i>
                                    <input type="hidden" value="<?php echo $v ?>" name="submission[<?php echo $args['key'] ?>][icon]" />
                                </a>
                            </div>
                            <div>
                                <?php PTB_CMB_Base::module_language_tabs('submission', isset($title[$k]) ? $title[$k] : array(), $languages, $args['key'] . '_label', 'text', __('Label', 'ptb_extra'), true); ?>
                                <input type="text" value="<?php echo isset($data['url'][$k]) && $data['url'][$k] ? esc_url_raw($data['url'][$k]) : '' ?>" name="submission[<?php echo $args['key'] ?>][url]" placeholder="<?php _e('Link', 'ptb_extra') ?>" />
                                <input class="ptb_extra_color_picker" value="<?php echo $color ?>" type="text" placeholder="<?php _e('Color', 'ptb_extra') ?>" name="submission[<?php echo $args['key'] ?>][color]" />
                            </div>
                        </div>
                        <i title="<?php _e('Remove', 'ptb_extra') ?>" class="ptb-submission-remove fa fa-times-circle"></i>
                    </li>
                <?php endforeach; ?>
            </ul>
            <div class="ptb-submission-option-add">
                <i class="fa fa-plus-circle"></i>
                <?php _e('Add new', 'ptb_extra') ?>                           
            </div>
            <?php if (isset($module['show_description'])): ?>
                <div class="ptb-submission-description ptb-submission-<?php echo $args['key'] ?>-description"><?php echo PTB_Utils::get_label($args['description']); ?></div>
            <?php endif; ?>
        </div>
        <?php
    }

    public function ptb_submission_validate(array $post_data, array $args, array $module, $post_type, $post_id, $lang, array $languages) {
        $key = $module['key'];
        $this->icons[$key] = array();
        if (isset($post_data[$key]) && isset($post_data[$key]['icon']) && is_array($post_data[$key]['icon'])) {
            $icons_array = array();
            $dom = new DOMDocument();
            $dom->load(plugin_dir_path(dirname(dirname(__FILE__))) . 'themify-ptb/admin/themify-icons/list.html');
            $icons = $dom->getElementsByTagName('i');
            foreach ($icons as $i) {
                $cl = trim(str_replace('fa-', '', $i->getAttribute('class')));
                $cl = explode(' ', $cl);
                $icons_array[] = trim($cl[0]) == 'fa' ? trim($cl[1]) : trim($cl[0]);
            }
            unset($dom, $icons);
            foreach ($post_data[$key]['icon'] as $k => $icon) {
                if (in_array($icon, $icons_array)) {
                    $this->icons[$key][$k] = $icon;
                }
            }
        }

        if (empty($this->icons[$key]) && isset($module['required'])) {
            return PTB_Utils::get_label($args['name']) . __(' is required', 'ptb_extra');
        }
        return $post_data;
    }

    public function ptb_submission_save(array $m, $key, array $post_data, $post_id, $lng) {
        if (!empty($this->icons[$key])) {
            foreach ($this->icons[$key] as $i => $icon) {
                $m['value']['icon'][$i] = $icon;
                if (isset($post_data[$key]['color']) && isset($post_data[$key]['color'][$i]) && strpos($post_data[$key]['color'][$i], '#') !== false) {
                    $m['value']['color'][$i] = sanitize_text_field($post_data[$key]['color'][$i]);
                }
                if (isset($post_data[$key . '_label']) && isset($post_data[$key . '_label'][$lng]) && isset($post_data[$key . '_label'][$lng][$i])) {
                    $m['value']['label'][$i] = sanitize_text_field($post_data[$key . '_label'][$lng][$i]);
                }
            }
        } else {
            return array();
        }
        return $m;
    }

}
