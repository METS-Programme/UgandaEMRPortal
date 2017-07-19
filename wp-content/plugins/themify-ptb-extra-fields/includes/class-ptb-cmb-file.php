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
class PTB_CMB_File extends PTB_Extra_Base {

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
            'name' => __('File', 'ptb_extra')
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
        $links = array('lightbox' => __('Lightbox', 'ptb_extra'), 'new_window' => __('New Window'), '0' => __('No', 'ptb_extra'));
        $show_icons = array('1'=>__('Yes','ptb_extra'),'0'=>__('No','ptb_extra'));
        $show_as = array('list'=>__('List','ptb_extra'),'block'=>__('Block','ptb_extra'));
        ?>
        <div class="ptb_back_active_module_row">
            <div class="ptb_back_active_module_label">
                <label><?php _e('Open in', 'ptb_extra') ?></label>
            </div>
            <div class="ptb_back_active_module_input">
                <?php foreach ($links as $k => $v): ?>
                    <input type="radio" id="ptb_<?php echo $id ?>_radio_<?php echo $k ?>"
                           name="[<?php echo $id ?>][file_link]" value="<?php echo $k ?>"
                           <?php if ((!isset($data['file_link']) && $k == '0') || ( isset($data['file_link']) && $data['file_link'] == "$k")): ?>checked="checked"<?php endif; ?>/>
                    <label for="ptb_<?php echo $id ?>_radio_<?php echo $k ?>"><?php echo $v ?></label>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="ptb_back_active_module_row">
            <div class="ptb_back_active_module_label">
                <label><?php _e('Show file icons', 'ptb_extra') ?></label>
            </div>
            <div class="ptb_back_active_module_input">
                <?php foreach ($show_icons as $k => $v): ?>
                    <input type="radio" id="ptb_<?php echo $id ?>_radio_<?php echo $k ?>"
                           name="[<?php echo $id ?>][show_icons]" value="<?php echo $k ?>"
                           <?php if ((!isset($data['show_icons']) && $k == '1') || ( isset($data['show_icons']) && $data['show_icons'] == "$k")): ?>checked="checked"<?php endif; ?>/>
                    <label for="ptb_<?php echo $id ?>_radio_<?php echo $k ?>"><?php echo $v ?></label>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="ptb_back_active_module_row">
            <div class="ptb_back_active_module_label">
                <label for="ptb_<?php echo $id ?>[color]"><?php _e('Color', 'ptb_extra') ?></label>
            </div>
            <div class="ptb_back_active_module_input">
                <input type="text" class="ptb_color_picker" name="[<?php echo $id ?>][color]" id="ptb_<?php echo $id ?>[color]" data-value="<?php echo isset($data['color']) && $data['color'] ? $data['color'] : '' ?>" />
            </div>
        </div>
        <div class="ptb_back_active_module_row">
            <div class="ptb_back_active_module_label">
                <label for="ptb_<?php echo $id ?>[show_as]"><?php _e('Show as', 'ptb_extra') ?></label>
            </div>
            <div class="ptb_back_active_module_input">
                <?php foreach ($show_as as $k => $v): ?>
                    <input type="radio" id="ptb_<?php echo $id ?>_radio_<?php echo $k ?>"
                           name="[<?php echo $id ?>][show_as]" value="<?php echo $k ?>"
                           <?php if ((!isset($data['show_as']) && $k == 'list') || ( isset($data['show_as']) && $data['show_as'] == "$k")): ?>checked="checked"<?php endif; ?>/>
                    <label for="ptb_<?php echo $id ?>_radio_<?php echo $k ?>"><?php echo $v ?></label>
                <?php endforeach; ?>
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
           
            $lightbox = isset($data['file_link']) && $data['file_link'] == 'lightbox';
            $length = count($meta_data[$args['key']]['url'])-1;
            $disable_lightbox = apply_filters('ptb_extra_disable_file_lightbox',array('zip','doc','docx','xls','xlsx','xlsm','tar','gzip','7z'));
            $new_window = !$lightbox && isset($data['file_link']) && $data['file_link'] == 'new_window';
            $show_icons = isset($data['show_icons']) && $data['show_icons'];
            $color = $show_icons && isset($data['color']) && $data['color']?$data['color']:false;
            $show_as = isset($data['show_as']) && $data['show_as']?$data['show_as']:'l';
            ?>
            <ul  class="ptb_extra_files ptb_extra_files_<?php echo $show_as?>">
                <?php foreach ($meta_data[$args['key']]['url'] as $index => $file): ?>
                    <?php if ($file): ?>
                        <?php $title = $meta_data[$args['key']]['title'][$index] ? esc_attr($meta_data[$args['key']]['title'][$index]) : '';
                              $file = esc_url($file);
                              $ext =  pathinfo($file,PATHINFO_EXTENSION);
                              $class = array();
                              if($lightbox && !in_array($ext,$disable_lightbox)){
                                  $class[] = 'ptb_lightbox';
                              }
                              if($show_icons){
                                  $class[] = 'fa ptb_extra_file_icons ptb_extra_'.$ext;
                              }
                              
                        ?>
                        <li>
                            <a <?php if($color):?>style="color:<?php echo $color?>"<?php endif;?><?php if($new_window):?>target="_blank"<?php endif;?> <?php echo !empty($class)?'class="'.implode(' ',$class).'"':''?> href="<?php echo $file?>"><span><?php echo $title?></span></a><?php if($index!=$length):?>, <?php endif;?>
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
                    $style = false;
                    if($v){
                        $ext = pathinfo($v,PATHINFO_EXTENSION);
                        $style = ' ptb_uploaded ptb_extra_'.$ext;
                    }
                    $title = isset($value['title']) && $value['title'][$index] ? esc_attr($value['title'][$index]) : '';
                    ?>

                    <li class="<?php echo $meta_key; ?>_option_wrapper ptb_cmb_option">
                        <span class="ti-split-v ptb_cmb_option_sort"></span>
                        <div class="ptb_post_cmb_image_wrapper">
                            <a href="#" class="ptb_post_cmb_image<?php echo $style; ?>">
                                <span class="ti-plus"></span>
                            </a>
                        </div>

                        <input type="text" name="<?php echo $url_name; ?>"
                               value="<?php echo $v; ?>" placeholder="<?php _e('File Url', 'ptb_extra') ?>"/>
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
        $can_be_allowed = array_keys(PTB_Submissiion_Options::get_allow_ext(array(),'application'));

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
                <br/>
                <?php _e("You can only chose the extensions that are allowed in your site", 'ptb-submission')?>
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
        wp_enqueue_script(self::$plugin_name . '-file', plugin_dir_url(dirname(__FILE__)) . 'public/submission/js/file.js', array('jquery'), self::$version, true);
        $data = isset($post->ID) ? get_post_meta($post->ID, 'ptb_' . $args['key'], TRUE) : array();
        if (empty($data) || empty($data['url'])) {
            $data = array('url' => array(false));
            $title = array();
        } else {
            if (!$data['title']) {
                $data['title'] = array();
            }
            $title = $this->ptb_submission_lng_data($data['title'], $args['key'], 'title', $post->ID, $post_type, $languages);
        }
        if (!isset($module['size'])) {
            $module['size'] = false;
        }
        if (!isset($module['extensions']) || empty($module['extensions'])) {
            $module['extensions'] = array('all');
        }
        $module['extensions'] = array_keys(PTB_Submissiion_Options::get_allow_ext($module['extensions'], 'application'));
        $size = PTB_Submissiion_Options::max_upload_size($module['size']);
        ?>
        <div class="ptb_back_active_module_input ptb-submission-multi-text ptb_extra_submission_images ptb_extra_submission_files">
            <ul>
                <?php foreach ($data['url'] as $k => $v): ?>
                    <li class="ptb-submission-text-option">
                        <i title="<?php _e('Sort', 'ptb_extra') ?>" class="fa fa-sort ptb-submission-option-sort"></i>
                        <div class="ptb-submission-priview-wrap">
                            <div class="ptb-submission-priview">
                                <?php if($v):?>
                                    <input type="hidden" value="<?php echo esc_url_raw($v) ?>" name="submission[<?php echo $args['key'] ?>][f]" />
                                <?php endif;?>
                            </div>
                        </div>
                        <div class="ptb_back_active_module_input">
                            <div class="ptb_extra_image_title"><label data-label="<?php _e('Upload File', 'ptb_extra') ?>" class="fa fa-upload ptb-submission-upload-btn" for="ptb_submission_<?php echo $args['key'] ?>"><?php echo $v?pathinfo($v,PATHINFO_FILENAME):__('Upload File', 'ptb_extra') ?></label></div>
                            <?php PTB_CMB_Base::module_language_tabs('submission', isset($title[$k]) ? $title[$k] : array(), $languages, $args['key'] . '_title', 'text', __('File Title', 'ptb_extra'), true); ?>
                            <div class="ptb-submission-file-wrap">
                                <input data-extension="<?php echo esc_attr(str_replace(',', '|', implode('|', $module['extensions']))) ?>" data-size="<?php echo $size ?>" data-width="80" data-height="80" id="ptb_submission_<?php echo $args['key'] ?>" class="ptb-submission-file" type="file" name="<?php echo $args['key'] ?>" />
                            </div>
                            <i title="<?php _e('Remove', 'ptb_extra') ?>" class="ptb-submission-remove fa fa-times-circle"></i>
                        </div>
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
        $error = false;
        $key = $module['key'];
        $file = isset($_FILES[$key]) && isset($_FILES[$key]['tmp_name']) ? $_FILES[$key] : array();
        $data = $post_id && isset($post_data[$key]['f']) ? $post_data[$key]['f'] : array();
        if (!isset($module['size'])) {
            $module['size'] = false;
        }
        if (!isset($module['extensions']) || empty($module['extensions'])) {
            $module['extensions'] = array('all');
        }
        $allow = PTB_Submissiion_Options::get_allow_ext($module['extensions'], 'application');
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
