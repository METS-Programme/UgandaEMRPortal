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
class PTB_CMB_Gallery extends PTB_Extra_Base {

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
            'name' => __('Gallery', 'ptb_extra')
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
        ?>
        <div class="ptb_back_active_module_row">
            <div class="ptb_back_active_module_label">
                <label><?php _e('Gallery Layout', 'ptb_extra') ?></label>
            </div>
            <div class="ptb_back_active_module_input ptb_extra_gallery_mode">
                <input type="radio" id="ptb_<?php echo $id ?>_layout_grid"
                       name="[<?php echo $id ?>][layout]" value="grid"
                       <?php if ($default || ( isset($data['layout']) && $data['layout'] == 'grid' )): ?>checked="checked"<?php endif; ?>/>
                <label for="ptb_<?php echo $id ?>_layout_grid"><?php _e('Grid', 'ptb_extra') ?></label>
                <input type="radio" id="ptb_<?php echo $id ?>_layout_showcase"
                       name="[<?php echo $id ?>][layout]" value="showcase"
                       <?php if (isset($data['layout']) && $data['layout'] == 'showcase'): ?>checked="checked"<?php endif; ?> />
                <label for="ptb_<?php echo $id ?>_layout_showcase"><?php _e('Showcase', 'ptb_extra') ?></label>
                <input type="radio" id="ptb_<?php echo $id ?>_layout_lightbox"
                       name="[<?php echo $id ?>][layout]" value="lightbox"
                       <?php if (isset($data['layout']) && $data['layout'] == 'lightbox'): ?>checked="checked"<?php endif; ?> />
                <label for="ptb_<?php echo $id ?>_layout_lightbox"><?php _e('Lightbox', 'ptb_extra') ?></label>
            </div>
        </div>
        <div class="ptb_back_active_module_row ptb_extra_gallery_columns">
            <div class="ptb_back_active_module_label">
                <label for="ptb_<?php echo $id ?>[columns]"><?php _e('Columns', 'ptb_extra') ?></label>
            </div>
            <div class="ptb_back_active_module_input ptb_back_text">
                <div class="ptb_custom_select">
                    <select id="ptb_<?php echo $id ?>[columns]" name="[<?php echo $id ?>][columns]">
                        <?php for ($i = 1; $i <= 9; $i++): ?>
                            <option <?php if (isset($data['columns']) && $data['columns'] == $i): ?>selected="selected"<?php endif; ?> value="<?php echo $i ?>">
                                <?php echo $i ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="ptb_back_active_module_row ptb_extra_gallery_size">
            <div class="ptb_back_active_module_label">
                <label for="ptb_<?php echo $id ?>[size]"><?php _e('image dimension', 'ptb_extra') ?></label>
            </div>
            <div class="ptb_back_active_module_input">
                <div class="ptb_custom_select">
                    <select id="ptb_<?php echo $id ?>[size]" name="[<?php echo $id ?>][size]">
                        <?php foreach(get_intermediate_image_sizes() as $s): ?>
                            <?php
                                $width = get_option( "{$s}_size_w" );
                                $heigth = get_option( "{$s}_size_h" );
                                if($width<=0 || $heigth<=0){
                                    continue;
                                }
                            ?>
                            <option <?php if (isset($data['size']) && $data['size'] == $s): ?>selected="selected"<?php endif; ?> value="<?php echo $s ?>">
                                <?php echo $s,' ( ',$width,' X ',$heigth,' ) '?>
                            </option>
                        <?php endforeach; ?>
                            <option value="f" <?php if(!isset($data['size']) || $data['size']==='f'):?>selected="selected"<?php endif;?>><?php _e('Full','ptb_extra')?></option>
                    </select>
                </div>
            </div>
        </div>
        <div class="ptb_back_active_module_row">
            <div class="ptb_back_active_module_label">
                <label><?php _e('Image Appearance', 'ptb_extra') ?></label>
            </div>
            <?php
            if (!isset($data['assign']) || !is_array($data['assign'])) {
                $data['assign'] = !isset($data['assign']) ? array() : array($data['assign']);
            }
            ?>
            <div class="ptb_back_active_module_input ptb_back_text">
                <?php
                $assign = array('rounded' => __('Rounded', 'ptb_extra'),
                    'drop-shadow' => __('Drop Shadow', 'ptb_extra'),
                    'bordered' => __('Bordered', 'ptb_extra'),
                    'circle' => __('Circle', 'ptb_extra')
                );
                ?>
                <?php foreach ($assign as $key => $name): ?>
                    <input value="<?php echo $key ?>" id="ptb_<?php echo $id ?>_assign_<?php echo $key ?>" type="checkbox"
                           name="[<?php echo $id ?>][assign][]"
                           <?php if (isset($data['assign']) && in_array($key, $data['assign'])): ?>checked="checked"<?php endif; ?>  />
                    <label for="ptb_<?php echo $id ?>_assign_<?php echo $key ?>"><?php echo $name ?></label>
                <?php endforeach; ?>
            </div>
        </div>
        <?php if ($type == PTB_Post_Type_Template::ARCHIVE): ?>
            <?php self::link_to_post($id, $type, $data, 'link', __('Only for layout Grid', 'ptb_extra')); ?>
        <?php endif; ?>

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
        ?>
        <?php if (!empty($meta_data) && isset($meta_data[$args['key']]) && !empty($meta_data[$args['key']])): ?>
            <?php
            $class = array();
            if($data['layout'] !== 'showcase'){
                $class[] = 'ptb_extra_columns_' . $data['columns'];
            }
            $class[] = 'ptb_extra_' . $data['layout'];
            if (isset($data['assign'])) {
                if (!is_array($data['assign'])) {
                    $data['assign'] = array($data['assign']);
                }
                foreach ($data['assign'] as $styles) {
                    $styles = str_replace('-', '_', $styles);
                    $class[] = 'ptb_extra_' . $styles;
                }
            }
            $class = implode(' ', $class);
            $close_tag = true;
            $size = isset($data['size']) && $data['size']!='f'?$data['size']:false;
            if($size){
                $width = get_option( "{$size}_size_w" );
                $height = get_option( "{$size}_size_h" );
            }
            ?>
            <div  class="<?php echo $class ?>">
                <?php if ($data['layout'] === 'showcase'): ?>
                    <div class="ptb_extra_main_image"></div>
                <?php endif; ?>
                <div class="ptb_extra_gallery">
                    <?php foreach ($meta_data[$args['key']]['url'] as $index => $slider):?>
                        <?php if ($slider): ?>
                            <?php $title = $meta_data[$args['key']]['title'][$index] ? esc_attr($meta_data[$args['key']]['title'][$index]) : ''; 
                                  $description =  $meta_data[$args['key']]['description'][$index] ? esc_textarea($meta_data[$args['key']]['description'][$index]) : ''
                            ?>
                            <div class="<?php if ($data['layout'] !== 'showcase'): ?>ptb_extra_item <?php endif; ?>ptb_extra_gallery_item">
                             
                                <?php if ($data['layout'] === 'lightbox'): ?>
                                    <a <?php if($description):?>title="<?php echo $title?>"<?php endif;?> data-rel="lightcase:collection:<?php echo $args['key'] ?>" href="<?php echo $slider ?>">
                                    <?php elseif (!$is_single && $data['layout'] === 'grid' && isset($data['link']) && $data['link']): ?>
                                        <a <?php if ($data['link'] === 'lightbox'): ?>
                                                data-href="<?php echo admin_url('admin-ajax.php?id=' . $meta_data['ID'] . '&action=ptb_single_lightbox') ?>" class="ptb_open_lightbox"
                                            <?php elseif ($data['link'] == 'new_window'): ?>
                                                target="_blank"<?php endif; ?>  href="<?php echo $meta_data['post_url'] ?>">
                                            <?php else: ?>
                                                <?php $close_tag = false; ?>
                                            <?php endif; ?>
                                            <?php if($size){
                                                $slider = PTB_CMB_Base::ptb_resize($slider, $width, $height);
                                            }
                                            ?>
                                            <img class="ptb_image ptb_extra_icon" src="<?php echo $slider ?>" alt="<?php echo  $description?esc_attr($description):$title ?>" title="<?php echo  $description?esc_attr($description):$title ?>" />
                                        <?php if ($close_tag): ?>
                                        </a>
                                    <?php endif; ?>
                            
                                <?php if($data['layout']!=='showcase' && $description):?>
                                    <span class="ptb_extra_gallery_description">
                                        <?php echo $description ?>
                                    </span>
                                <?php endif;?>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
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
        $url_name = sprintf('%s[url][]', $meta_key);
        $title_name = sprintf('%s[title][]', $meta_key);
        $description_name = sprintf('%s[description][]', $meta_key);
        ?>
        <fieldset class="ptb_cmb_input">
            <ul id="<?php echo $meta_key; ?>_options_wrapper" class="ptb_cmb_options_wrapper">
                <?php $values = is_array($value) && isset($value['url']) ? $value['url'] : array($value); ?>
                <?php foreach ($values as $index => $v): ?>
                    <?php
                    $v = esc_url_raw($v);
                    $style = $v ? sprintf('style="background-image:url(%s)"', $v) : '';
                    $title = isset($value['title']) && $value['title'][$index] ? esc_attr($value['title'][$index]) : '';
                    $description = isset($value['description']) && $value['description'][$index] ? esc_attr($value['description'][$index]) : '';
                    ?>

                    <li class="<?php echo $meta_key; ?>_option_wrapper ptb_cmb_option">
                        <span class="ti-split-v ptb_cmb_option_sort"></span>
                        <div class="ptb_post_cmb_image_wrapper">
                            <a href="#" class="ptb_post_cmb_image" <?php echo $style; ?>>
                                <span class="ti-plus"></span>
                            </a>
                        </div>
                        <input type="text" name="<?php echo $url_name; ?>"
                               value="<?php echo $v; ?>" placeholder="<?php _e('Image Url', 'ptb_extra') ?>"/>
                        <input type="text" name="<?php echo $title_name; ?>"
                               value="<?php echo $title ?>" placeholder="<?php _e('Title', 'ptb_extra') ?>" class="ptb_extra_row_margin"/>
                        <textarea name="<?php echo $description_name ?>" placeholder="<?php _e('Description', 'ptb_extra') ?>"><?php echo esc_textarea($description) ?></textarea>
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
        do_action('ptb_submission_slider', $post_type, $args, $module, $post, $lang, $languages);
    }

    public function ptb_submission_themplate($id, array $args, array $module = array(), array $post_support, array $languages = array()) {
        do_action('ptb_submission_template_slider', $id, $args, $module, $post_support, $languages);
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
                    $check = PTB_Submissiion_Options::validate_file($f, $allow, isset($module['size']) ? $module['size'] : NULL);
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
