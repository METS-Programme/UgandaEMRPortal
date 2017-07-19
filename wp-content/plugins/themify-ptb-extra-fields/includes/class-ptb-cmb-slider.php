<?php

/**
 * Custom meta box class to create slider
 *
 * @link       http://themify.me
 * @since      1.0.0
 *
 * @package    PTB
 * @subpackage PTB/includes
 * @author     Themify <ptb@themify.me>
 */
class PTB_CMB_Slider extends PTB_Extra_Base {

    private $data = array();

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
            'name' => __('Slider', 'ptb_extra')
        );

        return $cmb_types;
    }

    /**
     * Renders the meta boxes for themplates
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
        $default = empty($data);
        $speed = array('200' => __('Slow', 'ptb_extra'), '400' => __('Normal', 'ptb_extra'), '600' => __('Fast', 'ptb_extra'));
        $mode = array('horizontal' => __('Horizontal', 'ptb_extra'), 'vertical' => __('Vertical', 'ptb_extra'), 'fade' => __('Fade', 'ptb_extra'));
        ?>
        <div class="ptb_back_active_module_row">
            <div class="ptb_back_active_module_label">
                <label><?php _e('Transition', 'ptb_extra') ?></label>
            </div>
            <div class="ptb_back_active_module_input">
                <?php foreach ($mode as $k => $m): ?>
                    <input type="radio" id="ptb_mode_<?php echo $k ?>"
                           name="[<?php echo $id ?>][mode]" value="<?php echo $k ?>"
                           <?php if (($default && $k == 'horizontal') || ( isset($data['mode']) && $data['mode'] == $k )): ?>checked="checked"<?php endif; ?>/>
                    <label for="ptb_mode_<?php echo $k ?>"><?php echo $m ?></label>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="ptb_back_active_module_row">
            <div class="ptb_back_active_module_label">
                <label for="ptb_<?php echo $id ?>[minSlides]"><?php _e('Visible', 'ptb_extra') ?></label>
            </div>
            <div class="ptb_back_active_module_input ptb_back_text">
                <div class="ptb_custom_select">
                    <select id="ptb_<?php echo $id ?>[minSlides]"
                            name="[<?php echo $id ?>][minSlides]">
                                <?php for ($i = 1; $i < 8; $i++): ?>
                            <option <?php if (isset($data['minSlides']) && $data['minSlides'] == $i): ?>selected="selected"<?php endif; ?>value="<?php echo $i ?>"><?php echo $i ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <span class="ptb_option_desc"><?php _e('The minimum number of slides to be shown.Works only for vertical and horizonal mode', 'ptb_extra') ?></span>
            </div>
        </div>
        <div class="ptb_back_active_module_row">
            <div class="ptb_back_active_module_label">
                <label for="ptb_<?php echo $id ?>[slideWidth]"><?php _e('Width', 'ptb_extra') ?></label>
            </div>
            <div class="ptb_back_active_module_input ptb_back_text">
                <input type="number" step="1" name="[<?php echo $id ?>][slideWidth]" id="ptb_<?php echo $id ?>[slideWidth]" value="<?php echo isset($data['slideWidth']) && $data['slideWidth'] > 0 ? $data['slideWidth'] : '' ?>" min="0"/>
                <span class="ptb_option_desc"><?php _e('The width of each slide', 'ptb_extra') ?></span>
            </div>
        </div>
        <div class="ptb_back_active_module_row">
            <div class="ptb_back_active_module_label">
                <label for="ptb_<?php echo $id ?>[slideHeight]"><?php _e('Height', 'ptb_extra') ?></label>
            </div>
            <div class="ptb_back_active_module_input ptb_back_text">
                <input type="number" step="1" name="[<?php echo $id ?>][slideHeight]" id="ptb_<?php echo $id ?>[slideHeight]" value="<?php echo isset($data['slideHeight']) && $data['slideHeight'] > 0 ? $data['slideHeight'] : '' ?>" min="0"/>
                <span class="ptb_option_desc"><?php _e('The height of each slide', 'ptb_extra') ?></span>
            </div>
        </div>
        <div class="ptb_back_active_module_row">
            <div class="ptb_back_active_module_label">
                <label for="ptb_<?php echo $id ?>[speed]"><?php _e('Speed', 'ptb_extra') ?></label>
            </div>
            <div class="ptb_back_active_module_input ptb_back_text">
                <div class="ptb_custom_select">
                    <select id="ptb_<?php echo $id ?>[speed]" name="[<?php echo $id ?>][speed]">
                        <?php foreach ($speed as $k => $v): ?>
                            <option <?php if (isset($data['speed']) && $data['speed'] == $k): ?>selected="selected"<?php endif; ?>value="<?php echo $k ?>"><?php echo $v ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="ptb_back_active_module_row">
            <div class="ptb_back_active_module_label">
                <label for="ptb_<?php echo $id ?>[autoHover]"><?php _e('Pause On Hover', 'ptb_extra') ?></label>
            </div>
            <div value="1" class="ptb_back_active_module_input ptb_back_text">
                <div class="ptb_custom_select">
                    <select id="ptb_<?php echo $id ?>[autoHover]" name="[<?php echo $id ?>][autoHover]">
                        <option <?php if (isset($data['autoHover']) && $data['autoHover']): ?>selected="selected"<?php endif; ?> value="1"><?php _e('Yes', 'ptb_extra') ?></option>
                        <option <?php if (isset($data['autoHover']) && !$data['autoHover']): ?>selected="selected"<?php endif; ?> value="0"><?php _e('No', 'ptb_extra') ?></option>
                    </select>
                </div>
            </div>
        </div>
        <div class="ptb_back_active_module_row">
            <div class="ptb_back_active_module_label">
                <label for="ptb_<?php echo $id ?>[pause]"><?php _e('Auto Scroll', 'ptb_extra') ?></label>
            </div>
            <div class="ptb_back_active_module_input ptb_back_text">
                <div class="ptb_custom_select">
                    <select id="ptb_<?php echo $id ?>[pause]" name="[<?php echo $id ?>][pause]">
                        <?php for ($i = 0; $i <= 10; $i++): ?>
                            <option <?php if ((!isset($data['pause']) && $i == 3) || (isset($data['pause']) && $data['pause'] == $i)): ?>selected="selected"<?php endif; ?>value="<?php echo $i ?>">
                                <?php if ($i == 0): ?>
                                    <?php _e('Off', 'ptb_extra') ?>
                                <?php else: ?>
                                    <?php echo $i ?> <?php _e('sec', 'ptb_extra') ?>
                                <?php endif; ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>
                <span class="ptb_option_desc"><?php _e('The amount of time between each auto transition', 'ptb_extra') ?></span>
            </div>
        </div>
        <div class="ptb_back_active_module_row">
            <div class="ptb_back_active_module_label">
                <label><?php _e('Show slider pagination', 'ptb_extra') ?></label>
            </div>
            <div class="ptb_back_active_module_input">
                <div class="ptb_custom_select">
                    <select id="ptb_<?php echo $id ?>[pager]" name="[<?php echo $id ?>][pager]">
                        <option <?php if (isset($data['pager']) && $data['pager']): ?>selected="selected"<?php endif; ?> value="1"><?php _e('Yes', 'ptb_extra') ?></option>
                        <option <?php if (isset($data['pager']) && !$data['pager']): ?>selected="selected"<?php endif; ?> value="0"><?php _e('No', 'ptb_extra') ?></option>
                    </select>
                </div>
            </div>
        </div>
        <div class="ptb_back_active_module_row">
            <div class="ptb_back_active_module_label">
                <label for="ptb_<?php echo $id ?>[controls]"><?php _e('Show slider arrow buttons', 'ptb_extra') ?></label>
            </div>
            <div class="ptb_back_active_module_input">
                <div class="ptb_custom_select">
                    <select id="ptb_<?php echo $id ?>[controls]" name="[<?php echo $id ?>][controls]">
                        <option <?php if (isset($data['controls']) && $data['controls']): ?>selected="selected"<?php endif; ?> value="1"><?php _e('Yes', 'ptb_extra') ?></option>
                        <option <?php if (isset($data['controls']) && !$data['controls']): ?>selected="selected"<?php endif; ?> value="0"><?php _e('No', 'ptb_extra') ?></option>
                    </select>
                </div>
            </div>
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
        if (!empty($meta_data) && isset($meta_data[$args['key']]) && !empty($meta_data[$args['key']])) {
            if (!defined('DOING_AJAX') && !wp_script_is(self::$plugin_name . '-bxslider')) {
                wp_enqueue_style(self::$plugin_name . '-bxslider');
                wp_enqueue_script(self::$plugin_name . '-easing');
                wp_enqueue_script(self::$plugin_name . '-fitvideo');
                wp_enqueue_script(self::$plugin_name . '-bxslider');
            }
            $js_data = array();
            foreach ($data as $key => $arg) {
                if (!is_array($arg)) {
                    $js_data[$key] = $arg;
                } elseif (isset($arg[$lang])) {
                    $js_data[$key] = $arg[$lang];
                }
            }
            ?>
            <ul data-slider="<?php echo esc_attr(json_encode($js_data)); ?>" class="ptb_extra_bxslider">
                <?php foreach ($meta_data[$args['key']]['url'] as $index => $slider): ?>
                    <?php if ($slider): ?>
                        <?php
                        $title = $meta_data[$args['key']]['title'][$index] ? esc_attr($meta_data[$args['key']]['title'][$index]) : '';
                        $video = !in_array(pathinfo($slider, PATHINFO_EXTENSION), array('png', 'jpg', 'gif', 'jpeg', 'bmp'));
                        $slider = esc_url($slider);
                        ?>
                        <li>
                            <?php if (!$video): ?>
                                <img class="ptb_extra_image" src="<?php echo $slider ?>" alt="<?php echo $title ?>" title="<?php echo $title ?>" />
                            <?php else: ?>
                                <?php
                                $remote = strpos($slider, 'vimeo.com') !== false || strpos($slider, 'youtu.be') !== false || strpos($slider, 'youtube.com') !== false;
                                ?>
                                <?php if ($remote): ?>
                                    <?php
                                    $video = PTB_CMB_Video::parse_video_url($slider);
                                    echo $video['data'];
                                    ?>
                                <?php else: ?>
                                    <video width="100%" controls><source src="<?php echo $slider ?>"></video>
                                <?php endif; ?>
                            <?php endif; ?>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
            <?php
        }
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
        $url_name = sprintf('%s[url][]', $meta_key);
        $title_name = sprintf('%s[title][]', $meta_key);
        ?>
        <fieldset class="ptb_cmb_input">
            <ul id="<?php echo $meta_key ?>_options_wrapper" class="ptb_cmb_options_wrapper">
                <?php $values = is_array($value) && isset($value['url']) ? $value['url'] : array($value); ?>
                <?php foreach ($values as $index => $v): ?>
                    <?php
                    $v = esc_url_raw($v);
                    $video = $v && in_array(pathinfo($v,PATHINFO_EXTENSION),array('mp4','wmv','m4v','ogv','webm','mov','avi','flv','mpg','mpeg','mpe','qt'));
                    $style = $v && !$video? sprintf('style="background-image:url(%s)"', $v) : '';
                    $title = isset($value['title']) && $value['title'][$index] ? esc_attr($value['title'][$index]) : '';
                    ?>

                    <li class="<?php echo $meta_key; ?>_option_wrapper ptb_cmb_option">
                        <span class="ti-split-v ptb_cmb_option_sort"></span>
                        <div class="ptb_post_cmb_image_wrapper">
                            <a href="#" class="ptb_post_cmb_image<?php echo $video?' ptb_uploaded':''?>" <?php echo $style; ?>>
                                <span class="ti-plus"></span>
                            </a>
                        </div>
                        <input type="text" name="<?php echo $url_name; ?>"
                               value="<?php echo $v; ?>" placeholder="<?php _e('Image/Video Url', 'ptb_extra') ?>"/>
                        <input type="text" name="<?php echo $title_name; ?>"
                               value="<?php echo $title ?>" placeholder="<?php _e('Title', 'ptb_extra') ?>" class="ptb_extra_row_margin"/>
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

    public function ptb_submission_themplate($id, array $args, array $module = array(), array $post_support, array $languages = array()) {
        $max_upload = wp_max_upload_size();
        if (!isset($module['size'])) {
            $module['size'] = $max_upload;
        }
        if (!isset($module['extensions']) || empty($module['extensions'])) {
            $module['extensions'] = array('all');
        }
        $size = PTB_Submissiion_Options::max_upload_size($module['size']);
        $can_be_allowed = array_keys(PTB_Submissiion_Options::get_allow_ext());

        $all = in_array('all', $module['extensions']);
        ?>
        <div class="ptb_back_active_module_row">
            <div class="ptb_back_active_module_label">
                <label for="ptb_<?php echo $id ?>_extensions"><?php _e('Allowed extensions', 'ptb-submission') ?></label>
            </div>
            <div class="ptb_back_active_module_input">
                <select size="10" class="ptb-select" multiple="multiple" name="[<?php echo $id ?>][extensions][arr]" id="ptb_<?php echo $id ?>_extensions">
                    <option <?php if ($all): ?>selected="selected"<?php endif; ?> value="all"><?php _e('ALL', 'ptb-submission') ?></option>
                    <?php foreach ($can_be_allowed as $ext): ?>
                        <option  <?php echo $all ? 'disabled="disabled"' : (in_array($ext, $module['extensions']) ? 'selected="selected"' : '') ?>  value="<?php echo $ext ?>"><?php echo $ext ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="ptb_back_active_module_row">
            <div class="ptb_back_active_module_label">
                <label for="ptb_<?php echo $id ?>_size"><?php _e('Maximum image size(b)', 'ptb-submission') ?></label>
            </div>
            <div class="ptb_back_active_module_input">
                <input type="number" name="[<?php echo $id ?>][size]" value="<?php echo $size ?>" min="1" max="<?php echo $max_upload ?>" />
            </div>
        </div>
        <?php
    }

    public function ptb_submission_form($post_type, array $args, array $module, $post, $lang, $languages) {
        $data = isset($post->ID) ? get_post_meta($post->ID, 'ptb_' . $args['key'], TRUE) : array();
        if (empty($data)) {
            $data = array('url' => array(false));
            $title = array();
        } else {
            $title = $this->ptb_submission_lng_data($data['title'], $args['key'], 'title', $post->ID, $post_type, $languages);
        }
        if (!isset($module['size'])) {
            $module['size'] = false;
        }
        if (!isset($module['extensions']) || empty($module['extensions'])) {
            $module['extensions'] = array('all');
        }
        $module['extensions'] = array_keys(PTB_Submissiion_Options::get_allow_ext($module['extensions']));
        $size = PTB_Submissiion_Options::max_upload_size($module['size']);
        ?>
        <div class="ptb_back_active_module_input ptb-submission-multi-text ptb_extra_submission_images">
            <ul>
                <?php foreach ($data['url'] as $k => $v): ?>
                    <li class="ptb-submission-text-option">
                        <i title="<?php _e('Sort', 'ptb_extra') ?>" class="fa fa-sort ptb-submission-option-sort"></i>
                        <div class="ptb-submission-priview-wrap" style="width: 1px;">
                            <div class="ptb-submission-priview">
                                <?php if ($v): ?>
                                    <img src="<?php echo esc_url_raw($v) ?>" width="80" height="80" />
                                    <input type="hidden" value="<?php echo $v ?>" name="submission[<?php echo $args['key'] ?>]" />
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="ptb_back_active_module_input">
                            <div class="ptb_extra_image_title"><label data-label="<?php _e('Upload Image', 'ptb_extra') ?>" class="fa fa-upload ptb-submission-upload-btn" for="ptb_submission_<?php echo $args['key'] ?>"><?php _e('Upload Image', 'ptb_extra') ?></label></div>
                            <?php PTB_CMB_Base::module_language_tabs('submission', isset($title[$k]) ? $title[$k] : array(), $languages, $args['key'] . '_title', 'text', __('Image Title', 'ptb_extra'), true); ?>
                            <div class="ptb-submission-file-wrap">
                                <input data-extension="<?php echo esc_attr(str_replace(',', '|', implode('|', $module['extensions']))) ?>" data-size="<?php echo $size ?>"  data-width="80" data-height="80" id="ptb_submission_<?php echo $args['key'] ?>" class="ptb-submission-file" type="file" name="<?php echo $args['key'] ?>" />
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

        $error = FALSE;
        $key = $module['key'];
        $file = isset($_FILES[$key]) && isset($_FILES[$key]['tmp_name']) ? $_FILES[$key] : array();
        $data = $post_id && isset($post_data[$key]) ? $post_data[$key] : array();
        if (!isset($module['size'])) {
            $module['size'] = false;
        }
        if (!isset($module['extensions']) || empty($module['extensions'])) {
            $module['extensions'] = array('all');
        }
        $allow = PTB_Submissiion_Options::get_allow_ext($module['extensions']);
        $fsize = PTB_Submissiion_Options::max_upload_size($module['size']);
        if ($data) {
            $extensions = str_replace(',', '|', implode('|', array_keys($allow)));
        }
        $this->data[$key]['url'] = array();
        if (isset($post_data[$key . '_title'][$lang])) {
            foreach ($post_data[$key . '_title'][$lang] as $k => $v) {
                $error = false;
                if (isset($data[$k]) && $data[$k]) {
                    $f = esc_url_raw($data[$k]);
                    $path = ABSPATH . trim(parse_url($f, PHP_URL_PATH), '/');
                    if (file_exists($path)) {
                        $ext = pathinfo($f, PATHINFO_EXTENSION);
                        if (preg_match('/(' . $extensions . ')/i', $ext, $m)) {
                            $size = filesize($path);
                            if ($size <= $fsize) {
                                $this->data[$key]['url'][$k] = $f;
                            }
                        }
                    }
                } elseif (isset($file['tmp_name'][$k])) {
                    $f = array('name' => $file['name'][$k], 'size' => $file['size'][$k], 'tmp_name' => $file['tmp_name'][$k]);
                    $check = PTB_Submissiion_Options::validate_file($f, $allow, $fsize);
                    if (!isset($check['error'])) {
                        $this->data[$key]['url'][$k] = $check['file']['url'];
                        PTB_Submission_Public::$files[$key][] = $check['file'];
                    } else {
                        $error = $check['error'];
                    }
                }

                if (isset($this->data[$key]['url'][$k])) {
                    foreach ($languages as $code => $lng) {
                        $this->data[$key]['title'][$code][$k] = isset($post_data[$key . '_title'][$code]) && isset($post_data[$key . '_title'][$code][$k]) ? sanitize_text_field($post_data[$key . '_title'][$code][$k]) : false;
                    }
                }
            }
        }
        if (isset($module['required']) && empty($this->data[$key]['url'])) {
            return $error ? $error : PTB_Utils::get_label($args['name']) . __(' is required', 'ptb_extra');
        }
        return $post_data;
    }

    public function ptb_submission_save(array $m, $key, array $post_data, $post_id, $lng) {

        return array('url' => isset($this->data[$key]['url']) ? $this->data[$key]['url'] : array(),
            'title' => isset($this->data[$key]['title']) && isset($this->data[$key]['title'][$lng]) ? $this->data[$key]['title'][$lng] : false
        );
    }

}
