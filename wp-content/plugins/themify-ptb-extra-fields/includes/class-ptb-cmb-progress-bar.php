<?php

/**
 * Custom meta box class to create gallery
 *
 * @link       http://themify.me
 * @since      1.0.0
 *
 * @package    PTB
 * @subpackage PTB/includes
 * @author     Themify <ptb@themify.me>
 */
class PTB_CMB_Progress_Bar extends PTB_Extra_Base {

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
            'name' => __('Progress Bar', 'ptb_extra')
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
        $animationspeed = array(
            'slow' => __('Slow', 'ptb_extra'),
            'medium' => __('Meduim', 'ptb_extra'),
            'fast' => __('Fast', 'ptb_extra')
                )
        ?>
        <?php if (!empty($args['options'])): ?>
            <?php foreach ($args['options'] as $opt): ?>                     
                <fieldset>
                    <legend><?php echo PTB_Utils::get_label($opt); ?></legend>
                    <div class="ptb_fields_wrapper">
                        <div class="ptb_back_active_module_row">
                            <div class="ptb_back_active_module_label">
                                <label for="<?php echo $opt['id'] ?>_hide"><?php _e('Hide Label', 'ptb_extra') ?></label>
                            </div>
                            <div class="ptb_back_active_module_input">
                                <input type="checkbox" name="[<?php echo $id ?>][<?php echo $opt['id'] ?>_hide]" <?php echo isset($data[$opt['id'] . '_hide']) && $data[$opt['id'] . '_hide'] ? 'checked="checked"' : '' ?> id="<?php echo $opt['id'] ?>_hide" value="1" />
                            </div>
                        </div>
                        <div class="ptb_back_active_module_row">
                            <div class="ptb_back_active_module_label">
                                <label for="<?php echo $opt['id'] ?>_barcolor"><?php _e('Color', 'ptb_extra') ?></label>
                            </div>
                            <div class="ptb_back_active_module_input">
                                <?php $bgcolor = isset($data[$opt['id'] . '_barcolor']) && $data[$opt['id'] . '_barcolor'] ? $data[$opt['id'] . '_barcolor'] : false; ?> 
                                <input class="ptb_color_picker" type="text" name="[<?php echo $id ?>][<?php echo $opt['id'] ?>_barcolor]" <?php if ($bgcolor): ?>data-value="<?php echo $bgcolor ?>"<?php endif; ?> id="<?php echo $opt['id'] ?>_barcolor" />
                            </div>
                        </div>
                        <div class="ptb_back_active_module_row">
                            <div class="ptb_back_active_module_label">
                                <label for="<?php echo $opt['id'] ?>_display"><?php _e('Hide percentage text', 'ptb_extra') ?></label>
                            </div>
                            <div class="ptb_back_active_module_input">
                                <input type="checkbox" name="[<?php echo $id ?>][<?php echo $opt['id'] ?>_display]" <?php echo isset($data[$opt['id'] . '_display']) && $data[$opt['id'] . '_display'] ? 'checked="checked"' : '' ?> id="<?php echo $opt['id'] ?>_display" value="1" />
                            </div>
                        </div>
                    </div>
                </fieldset>
            <?php endforeach; ?>
        <?php endif; ?>
        <?php
    }

    /**
     * @param string $id the id template
     * @param array $languages
     */
    public function action_template_type($id, array $languages) {
        $lng_count = count($languages) > 1;
        ?>
        <div class="ptb_cmb_input_row">
            <label for="<?php echo $id; ?>_horizonal" class="ptb_cmb_input_label">
                <?php _e("Orientation", 'ptb_extra'); ?>
            </label>
            <fieldset class="ptb_cmb_input">
                <label for="<?php echo $id; ?>_horizonal">
                    <input type="radio" id="<?php echo $id; ?>_horizonal"
                           name="<?php echo $id; ?>_orientation" value="horizonal" checked="checked"/>
                    <span><?php _e("Horizonal", 'ptb_extra'); ?></span>
                </label>&nbsp;&nbsp;
                <label for="<?php print( $id); ?>_vertical">
                    <input type="radio" id="<?php echo $id; ?>_vertical"
                           name="<?php echo $id; ?>_orientation" value="vertical" />
                    <span><?php _e("Vertical", 'ptb_extra'); ?></span>
                </label><br/>
            </fieldset>
        </div>
        <div class="ptb_cmb_input_row">
            <label for="<?php echo $id; ?>_options" class="ptb_cmb_input_label">
                <?php _e("Labels", 'ptb_extra'); ?>
            </label>
            <fieldset class="ptb_cmb_input">
                <ul id="<?php echo $id; ?>_options_wrapper" class="ptb_cmb_options_wrapper">
                    <li class="<?php echo $id; ?>_option_wrapper ptb_cmb_option">
                        <span class="ti-split-v ptb_cmb_option_sort"></span>
                        <?php if ($lng_count): ?>
                            <ul class="ptb_language_tabs">
                                <?php foreach ($languages as $code => $lng): ?>
                                    <li <?php if (isset($lng['selected'])): ?>class="ptb_active_tab_lng"<?php endif; ?>>
                                        <a class="ptb_lng_<?php echo $code ?>"title="<?php echo $lng['name'] ?>" href="#"></a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                        <ul class="ptb_language_fields">
                            <?php foreach ($languages as $code => $lng): ?>
                                <li <?php if (isset($lng['selected'])): ?>class="ptb_active_lng"<?php endif; ?>>
                                    <input name="<?php echo $id; ?>_options_<?php echo $code ?>[]" type="text"/>&nbsp;&nbsp;
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <span class="<?php echo $id; ?>_remove remove ti-close"></span>
                    </li>
                </ul>
                <div id="<?php echo $id; ?>_add_new" class="ptb_cmb_option_add">
                    <span class="ti-plus"></span>
                    <?php _e("Add new", 'ptb_extra'); ?>
                </div>
            </fieldset>
        </div>

        <?php
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
        if (!defined('DOING_AJAX') && !wp_script_is(self::$plugin_name . '-jqmeter')) {
            wp_enqueue_script(self::$plugin_name . '-jqmeter');
        }
        ?>
        <?php if (!empty($args['options'])): ?>
            <div class="ptb_extra_progress_<?php echo $args['orientation'] ?>">
                <?php foreach ($args['options'] as $opt): ?>
                    <?php
                    if (isset($data[$opt['id'] . '_hide'])) {
                        continue;
                    }
                    $value = isset($meta_data[$args['key']]) ? $meta_data[$args['key']] : FALSE;
                    $value = $value && isset($value[$opt['id']]) && $value[$opt['id']] ? floatval($value[$opt['id']]) : 0;
                    ?>
                    <div class="ptb_extra_progress_item">
                        <?php if ($args['orientation'] == 'horizonal'): ?>
                            <div class="ptb_extra_progress_bar_label"><?php echo PTB_Utils::get_label($opt); ?></div>
                        <?php endif; ?>
                        <div data-meterorientation="<?php echo $args['orientation'] ?>" 
                             data-barcolor="<?php echo isset($data[$opt['id'] . '_barcolor']) ? $data[$opt['id'] . '_barcolor'] : '' ?>"
                             data-raised="<?php echo $value ? floatval($value) : 0 ?>" 
                             <?php if (isset($data[$opt['id'] . '_display'])): ?>
                                 data-displaytotal="1"
                             <?php endif; ?>
                             class="ptb_extra_progress_bar">
                        </div>
                        <?php if ($args['orientation'] != 'horizonal'): ?>
                            <div  class="ptb_extra_progress_bar_label"><?php echo PTB_Utils::get_label($opt); ?></div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
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
        ?>
        <?php if (!empty($args['options'])): ?>
            <?php
            $wp_meta_key = sprintf('%s_%s', $this->get_plugin_name(), $meta_key);
            $value = get_post_meta($post->ID, $wp_meta_key, true);
            ?>
            <table class="ptb_extra_progress_table">
                <?php foreach ($args['options'] as $option): ?>
                    <tr>
                        <td><label for="<?php echo $option['id'] ?>"><?php echo PTB_Utils::get_label($option); ?></label></td>
                        <td><input id="<?php echo $option['id'] ?>" type="number" step="any" min="0" max="100" name="<?php echo $meta_key; ?>[<?php echo $option['id'] ?>]" value="<?php echo isset($value[$option['id']]) ? intval($value[$option['id']]) : ''; ?>" /></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
        <?php
    }

    public function ptb_submission_form($post_type, array $args, array $module, $post, $lang, $languages) {
        ?>
        <?php if (!empty($args['options'])): ?>
            <?php $data = isset($post->ID) ? get_post_meta($post->ID, 'ptb_' . $args['key'], TRUE) : array(); ?>
            <div class="ptb_back_active_module_input">
                <ul class="ptb_extra_submission_bar">
                    <?php foreach ($args['options'] as $option): ?>
                        <li>
                            <label for="<?php echo $option['id'] ?>"><?php echo PTB_Utils::get_label($option); ?>:</label>
                            <input id="<?php echo $option['id'] ?>" type="number" step="any" min="0" max="100" name="submission[<?php echo $args['key']; ?>][<?php echo $option['id'] ?>]" value="<?php echo isset($data[$option['id']]) ? intval($data[$option['id']]) : 0; ?>" />
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <?php
    }

    public function ptb_submission_validate(array $post_data, array $args, array $module, $post_type, $post_id, $lang, array $languages) {

        if (!empty($args['options']) && isset($post_data[$module['key']])) {
            $values = $post_data[$module['key']];
            foreach ($args['options'] as $option) {
                $values[$option['id']] = isset($values[$option['id']]) ? intval($values[$option['id']]) : 0;
                if ($values[$option['id']] < 0) {
                    $values[$option['id']] = 0;
                } elseif ($values[$option['id']] > 100) {
                    $values[$option['id']] = 100;
                }
            }
            $post_data[$module['key']] = $values;
        } elseif (empty($args['options']) && isset($post_data[$module['key']])) {
            unset($post_data[$module['key']]);
        }
        return $post_data;
    }

    public function ptb_submission_save(array $m, $key, array $post_data, $post_id, $lng) {
        return $m;
    }

}
