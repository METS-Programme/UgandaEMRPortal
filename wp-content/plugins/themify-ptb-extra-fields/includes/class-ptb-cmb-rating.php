<?php

/**
 * Custom meta box class to create rating stars
 *
 * @link       http://themify.me
 * @since      1.0.0
 *
 * @package    PTB
 * @subpackage PTB/includes
 * @author     Themify <ptb@themify.me>
 */
class PTB_CMB_Rating extends PTB_Extra_Base {

    private $uid = false;
    private $ip = false;

    public function __construct($type, $plugin_name, $version) {
        if (is_admin()) {
            add_action('save_post', array($this, 'remove_rate_data'));
            add_filter('ptb_template_modules', array($this, 'is_editable'), 10, 3);
        } else {
            add_filter('ptb_submission_render', array($this, 'is_editable'), 10, 3);
        }
        if(!is_admin() || (defined('DOING_AJAX') &&  DOING_AJAX)){
            add_action('ptb_search_rating',array($this,'search_rating_template'),10,8);
            add_filter('ptb_search_by_rating',array($this,'search_rating'),10,6);
        }
        $this->ip = self::getClientIP();
        add_action('wp_ajax_ptb_extra_rate_voted', array($this, 'voted'));
        add_action('wp_ajax_nopriv_ptb_extra_rate_voted', array($this, 'voted'));
        parent::__construct($type, $plugin_name, $version);
    }

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
            'name' => __('Rating', 'ptb_extra')
        );
        return $cmb_types;
    }

    /**
     * @param string $id the id template
     * @param array $languages
     */
    public function action_template_type($id, array $languages) {
        ?>
        <div class="ptb_cmb_input_row">
            <label for="<?php echo $id; ?>_stars_count" class="ptb_cmb_input_label">
                <?php _e("Rating Count", 'ptb_extra'); ?>
            </label>
            <div class="ptb_cmb_input">
                <select id="<?php echo $id; ?>_stars_count" name="<?php echo $id; ?>_stars_count">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <option value="<?php echo $i ?>"><?php echo $i ?></option>
                    <?php endfor; ?>
                </select>
            </div>
        </div>
        <div class="ptb_cmb_input_row">
            <label for="<?php echo $id; ?>_admin" class="ptb_cmb_input_label">
                <?php _e("Who can rate", 'ptb_extra'); ?>
            </label>
            <fieldset class="ptb_cmb_input">
                <label for="<?php echo $id; ?>_admin">
                    <input type="radio" id="<?php echo $id; ?>_admin"
                           name="<?php echo $id; ?>_readonly" value="1" checked="checked"/>
                    <span><?php _e("Admin/Editor", 'ptb_extra'); ?></span>
                </label>&nbsp;&nbsp;
                <label for="<?php echo $id; ?>_public">
                    <input type="radio" id="<?php echo $id; ?>_public"
                           name="<?php echo $id; ?>_readonly" value="0" />
                    <span><?php _e("Public Visitors", 'ptb_extra'); ?></span>
                </label><br/>
            </fieldset>
        </div>
        <?php
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
        $sizes = array('small' => __('Small', 'ptb_extra'), 'medium' => __('Meduim', 'ptb_extra'), 'large' => __('Large', 'ptb_extra'));
        ?>
        <div class="ptb_back_active_module_row">
            <div class="ptb_back_active_module_label">
                <label for="ptb_<?php echo $id ?>[icon]"><?php _e('Icon', 'ptb_extra') ?></label>
            </div>
            <div class="ptb_back_active_module_input">
                <input type="text" name="[<?php echo $id ?>][icon]" id="ptb_<?php echo $id ?>[icon]" value="<?php echo isset($data['icon']) && $data['icon'] ? $data['icon'] : 'fa-star' ?>" />
                <a title="<?php _e('Icon Picker', 'ptb_extra') ?>" class="ptb_custom_lightbox" href="<?php echo plugin_dir_url(dirname(dirname(__FILE__))) ?>themify-ptb/admin/themify-icons/list.html"><?php _e('Icon', 'ptb_extra') ?></a>
            </div>
        </div>
        <div class="ptb_back_active_module_row">
            <div class="ptb_back_active_module_label">
                <label for="ptb_<?php echo $id ?>[size]"><?php _e('Icon Size', 'ptb_extra') ?></label>
            </div>
            <div class="ptb_back_active_module_input">
                <div class="ptb_custom_select">
                    <select id="ptb_<?php echo $id ?>[size]" name="[<?php echo $id ?>][size]">
                        <?php foreach ($sizes as $s => $name): ?>
                            <option value="<?php echo $s ?>" <?php if (isset($data['size']) && $data['size'] == $s): ?>selected="selected"<?php endif; ?>><?php echo $name ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <span class="fa"></span>
            </div>
        </div>
        <div class="ptb_back_active_module_row">
            <div class="ptb_back_active_module_label">
                <label for="ptb_<?php echo $id ?>[vcolor]"><?php _e('Voted color', 'ptb_extra') ?></label>
            </div>
            <div class="ptb_back_active_module_input">
                <input type="text" class="ptb_color_picker" name="[<?php echo $id ?>][vcolor]" id="ptb_<?php echo $id ?>[vcolor]" data-value="<?php echo isset($data['vcolor']) && $data['vcolor'] ? $data['vcolor'] : '' ?>" />
            </div>
        </div>
        <?php if (!isset($args['readonly']) || !$args['readonly']): ?>
            <div class="ptb_back_active_module_row">
                <div class="ptb_back_active_module_label">
                    <label for="ptb_<?php echo $id ?>[hcolor]"><?php _e('Hover color', 'ptb_extra') ?></label>
                </div>
                <div class="ptb_back_active_module_input">
                    <input type="text" class="ptb_color_picker" name="[<?php echo $id ?>][hcolor]" id="ptb_<?php echo $id ?>[hcolor]" data-value="<?php echo isset($data['hcolor']) && $data['hcolor'] ? $data['hcolor'] : '' ?>" />
                </div>
            </div>
            <div class="ptb_back_active_module_row">
                <div class="ptb_back_active_module_label">
                    <label for="ptb_<?php echo $id ?>[before_confirmation]"><?php _e("Show confirmation message box before rate", 'ptb_extra') ?></label>
                </div>
                <div class="ptb_back_active_module_input">
                    <input class="ptb_rating_confirm" type="checkbox"  id="ptb_<?php echo $id ?>[before_confirmation]"
                           name="[<?php echo $id ?>][before_confirmation]" value="1" <?php echo isset($data['before_confirmation']) && $data['before_confirmation'] ? 'checked="checked"' : '' ?>
                           />
                    <div>
                        <?php self::module_language_tabs($id, $data, $languages, 'before_confirmation_text'); ?>
                        <div class="ptb_rate_desc"><?php _e("You can use '#rated_value#' in your text to show current voted value", 'ptb_extra') ?></div>
                    </div>
                </div>
            </div>
            <div class="ptb_back_active_module_row">
                <div class="ptb_back_active_module_label">
                    <label for="ptb_<?php echo $id ?>[after_confirmation]"><?php _e("Show text message box after rate", 'ptb_extra') ?></label>
                </div>
                <div class="ptb_back_active_module_input">
                    <input class="ptb_rating_confirm" type="checkbox"  id="ptb_<?php echo $id ?>[after_confirmation]"
                           name="[<?php echo $id ?>][after_confirmation]" value="1" <?php echo isset($data['after_confirmation']) && $data['after_confirmation'] ? 'checked="checked"' : '' ?>
                           />
                    <div>
                        <?php self::module_language_tabs($id, $data, $languages, 'after_confirmation_text'); ?>
                        <div class="ptb_rate_desc"><?php _e("You can use '#rated_value#' in your text to show current voted value", 'ptb_extra') ?></div>
                    </div>
                </div>
            </div>
            <div class="ptb_back_active_module_row">
                <div class="ptb_back_active_module_label">
                    <label for="ptb_<?php echo $id ?>[show_vote]"><?php _e('Show Vote Count', 'ptb_extra') ?></label>
                </div>
                <div class="ptb_back_active_module_input">
                    <input type="checkbox" name="[<?php echo $id ?>][show_vote]" id="ptb_<?php echo $id ?>[show_vote]" value="1" />
                </div>
            </div>
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
     * @param string $index index in themplate
     */
    public function action_public_themplate(array $args, array $data, $meta_data, $lang = false, $is_single = false, $index = false) {
        $public = isset($args['readonly']) && $args['readonly'];
        $readonly = $public;
        $wp_meta_key = sprintf('%s_%s', $this->get_plugin_name(), $args['key']);
        $vote = get_post_meta($meta_data['ID'], $wp_meta_key, true);
        $is_array = $vote && is_array($vote);
        if (!$readonly) {
            if (!$this->uid && is_user_logged_in()) {
                $this->uid = get_current_user_id();
            }
            if ($is_array) {
                if ($this->uid && in_array($this->uid, $vote['users'])) {
                    $readonly = true;
                } elseif (!$this->ip || in_array($this->ip, $vote['ip'])) {
                    $readonly = true;
                }
            }
        }

        if ($is_array) {
            $value = $vote['count'] > 0 ? floatval($vote['total'] / $vote['count']) : 0;
        } elseif ($readonly && isset($meta_data[$args['key']])) {
            $value = $meta_data[$args['key']];
        } else {
            $value = 0;
        }
        $id = str_replace('-', '_', $args['key'] . '_' . $index);
        $value = round($value);
        $size = isset($data['size']) ? $data['size'] : 'small';
        $icon = isset($data['icon']) && $data['icon'] ? $data['icon'] : 'fa-star';
        $vcolor = isset($data['vcolor']) && $data['vcolor'] ? $data['vcolor'] : false;
        $hcolor = !$readonly && isset($data['hcolor']) && $data['hcolor'] ? $data['hcolor'] : false;
        ?> 
        <div itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating"
            data-key="<?php echo $args['key'] ?>"
            data-post="<?php echo $meta_data['ID'] ?>" 
            data-id="<?php echo $id ?>" 
            <?php if (isset($data['before_confirmation']) && isset($data['before_confirmation_text'][$lang]) && $data['before_confirmation_text'][$lang]): ?>
                data-before="<?php esc_attr_e($data['before_confirmation_text'][$lang]) ?>"
            <?php endif; ?>
            <?php if (isset($data['after_confirmation']) && isset($data['after_confirmation_text'][$lang]) && $data['after_confirmation_text'][$lang]): ?>
                data-after="<?php esc_attr_e($data['after_confirmation_text'][$lang]) ?>"
            <?php endif; ?>
            data-vcolor="<?php echo $vcolor ?>" 
            data-hcolor="<?php echo $hcolor ?>" 
            class="<?php if ($readonly): ?><?php echo self::$plugin_name ?>_readonly_rating <?php endif; ?><?php echo self::$plugin_name ?>_rating <?php echo self::$plugin_name ?>_rating_<?php echo $size ?>">
                <?php for ($i = $args['stars_count']; $i > 0; $i--): ?>
                <span class="fa <?php echo $icon ?><?php echo $value >= $i ? ' ptb_extra_voted' : '' ?>"></span>
            <?php endfor; ?>
            <meta itemprop="ratingValue" content="<?php echo $value>0?($value>5?5:$value):1?>"/>
            <meta itemprop="ratingCount" content="<?php echo !$public && isset($vote['count']) && $vote['count']>0?$vote['count']:1?>"/>
        </div>
        <?php if(!$public && isset($data['show_vote'])):?>
              <p class="ptb_extra_vote_count">( <?php echo isset($vote['count']) && $vote['count']>0?$vote['count']:0?> )</p>
          <?php endif;?>
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
        $readonly = isset($args['readonly']) && $args['readonly'];
        $name = sprintf('%s', $meta_key);
        ?>
        <?php if (!$readonly): ?>
            <?php if (!is_array($value)): ?>
                <?php $value = array('count' => 0, 'total' => 0); ?>
            <?php endif; ?>
            <div class="ptb_extra_rating_table">
                <table>
                    <tr>
                        <td><strong><?php _e('Click', 'ptb_extra') ?></strong></td>
                        <td><?php _e('How many times users have clicked', 'ptb_extra') ?></td>
                        <td><input type="text" value="<?php echo $value['count'] ?>" name="ptb_rating[<?php echo $wp_meta_key ?>][count]" /></td>
                    </tr>
                    <tr>
                        <td><strong><?php _e('Sum', 'ptb_extra') ?></strong></td>
                        <td><?php _e('Total sum of the rating', 'ptb_extra') ?></td>
                        <td><input type="text" value="<?php echo $value['total'] ?>" name="ptb_rating[<?php echo $wp_meta_key ?>][total]" /></td>
                    </tr>
                    <tr>
                        <td><strong><?php _e('Rating', 'ptb_extra') ?></strong></td>
                        <td><?php echo $value['count'] > 0 ? floatval($value['total'] / $value['count']) : 0 ?></td>
                    </tr>
                </table>
            </div>
        <?php else: ?>
            <?php if (is_array($value)): ?>
                <?php $value = $value['count'] > 0 ? floatval($value['total'] / $value['count']) : 0 ?>
            <?php endif; ?>
            <input id="ptb_extra_<?php echo $meta_key; ?>" 
                   type="number" max="<?php echo intval($args['stars_count']) ?>" 
                   min="0"
                   step="1"
                   name="<?php echo $name; ?>"
                   value="<?php echo $value ? floatval($value) : '0' ?>"/>
               <?php endif; ?>

        <?php
    }

    /* Save post hook handler
     * Remove rating data of the post if user checked the checkbox
     *
     * @since 1.0.0
     */

    public function remove_rate_data($post_id) {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        if (isset($_POST['ptb_rating']) && !empty($_POST['ptb_rating'])) {
            foreach ($_POST['ptb_rating'] as $key => $rate) {
                $key = sanitize_key($key);
                $value = get_post_meta($post_id, $key, true);
                if (!$value || !is_array($value)) {
                    $value = array('total' => 0, 'count' => 0, 'users' => array(), 'ip' => array());
                }
                $value['total'] = floatval($rate['total']);
                $value['count'] = intval($rate['count']);
                update_post_meta($post_id, $key, $value);
            }
        }
    }

    /**
     * Vote ajax handler
     *
     * @since 1.0.0
     */
    public function voted() {
        if (!empty($_POST) && isset($_POST['id']) && isset($_POST['key']) && isset($_POST['value'])) {
            $post_id = intval($_POST['id']);
            $post = get_post($post_id);
            if (empty($post_id) && $post_id->post_status != 'publish') {
                wp_die();
            }
            $key = sanitize_key($_POST['key']);
            $plugin_options = new PTB_Options('ptb', self::$version);
            $options = $plugin_options->get_cpt_cmb_options($post->post_type);
            if (empty($options) || !isset($options[$key]) || $options[$key]['type'] !== 'rating') {
                wp_die();
            }
            $meta = $options[$key];
            $value = intval($_POST['value']);
            unset($options);
            $this->uid = get_current_user_id();
            if (
                    isset($meta['readonly']) && $meta['readonly'] || ($value <= 0 || $value > $meta['stars_count'])
            ) {
                wp_die();
            }
            $wp_meta_key = sprintf('%s_%s', $this->get_plugin_name(), $key);
            $vote = get_post_meta($post_id, $wp_meta_key, true);
            if (!$vote || !is_array($vote)) {
                $vote = array('total' => 0, 'count' => 0, 'users' => array(), 'ip' => array());
            }
            if ($this->uid) {
                if (in_array($this->uid, $vote['users'])) {
                    wp_die();
                }
                $vote['users'][] = $this->uid;
            }
            if (!$this->ip || in_array($this->ip, $vote['ip'])) {
                wp_die();
            }

            $vote['ip'][] = $this->ip;
            $vote['count']++;
            $vote['total']+=$value;
            update_post_meta($post_id, $wp_meta_key, $vote);

            $total = floatval($vote['total'] / $vote['count']);
            die(json_encode(array('success' => 1, 'total' => $total,'count'=>$vote['count'])));
        } else {
            wp_die();
        }
    }

    /**
     * Get User Ip address
     *
     * @since 1.0.0
     */
    private static function getClientIP() {

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            //check ip from share internet
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            //to check ip is pass from proxy
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    public function is_editable(array $cmb_options, $type, $post_type) {
        if (is_array($type) || $type == 'frontend') {
            foreach ($cmb_options as $k => $v) {
                if ($v['type'] == 'rating' && !$v['readonly']) {
                    unset($cmb_options[$k]);
                }
            }
        }
        return $cmb_options;
    }

    public function ptb_submission_form($post_type, array $args, array $module, $post, $lang, $languages) {
        wp_enqueue_script(self::$plugin_name . '-submission-rating', plugin_dir_url(dirname(__FILE__)) . 'public/submission/js/rating.js', array('ptb-submission'), self::$version, false);
        $icon = isset($module['icon']) && $module['icon'] ? $module['icon'] : 'fa-star';
        $data = isset($post->ID) ? get_post_meta($post->ID, 'ptb_' . $args['key'], TRUE) : false;
        ?>
        <div class="ptb_back_active_module_input">
            <div
                data-id="<?php echo get_the_ID() ?>"
                data-key="<?php echo $args['key'] ?>"
                data-vcolor="<?php echo isset($module['vcolor']) && $module['vcolor'] ? $module['vcolor'] : false ?>" 
                data-hcolor="<?php echo isset($module['hcolor']) && $module['hcolor'] ? $module['hcolor'] : false ?>" 
                class="ptb_extra_rating ptb_extra_not_vote ptb_extra_rating_<?php echo isset($module['size']) ? $module['size'] : 'small' ?><?php if ($data): ?> ptb_extra_readonly_rating<?php endif; ?>">
                    <?php for ($i = $args['stars_count']; $i > 0; $i--): ?>
                    <span class="fa <?php echo $icon ?> <?php echo $data >= $i ? ' ptb_extra_voted' : '' ?>"></span>
                <?php endfor; ?>
            </div>
            <input type="hidden" name="submission[<?php echo $args['key'] ?>]" value="<?php echo $data ?>" />
            <span <?php if ($data): ?>style="display:inline-block;"<?php endif; ?>class="ptb-submission-rate-cancel hide ptb-submission-upload-btn"><?php _e('Cancel', 'ptb_extra') ?></span>
            <?php if (isset($module['show_description'])): ?>
                <div class="ptb-submission-description ptb-submission-<?php echo $args['key'] ?>-description"><?php echo PTB_Utils::get_label($args['description']); ?></div>
            <?php endif; ?>
        </div>
        <?php
    }

    public function ptb_submission_validate(array $post_data, array $args, array $module, $post_type, $post_id, $lang, array $languages) {
        $value = false;
        if (isset($post_data[$module['key']])) {
            $value = intval($post_data[$module['key']]);
            if ($value <= 0 || $value > $args['stars_count']) {
                $value = false;
            } else {
                $post_data[$module['key']] = intval($post_data[$module['key']]);
            }
        }
        if (!$value && isset($module['required'])) {
            return PTB_Utils::get_label($args['name']) . __(' is required', 'ptb_extra');
        }
        return $post_data;
    }

    public function ptb_submission_save(array $m, $key, array $post_data, $post_id, $lng) {
        return $m;
    }
    
    public function search_rating_template($post_type,$id,$args,$module,$value,$label,$lang,$languages){
        $name =  PTB_Utils::get_label($args['name']);
        $name = $name ? sanitize_title($name) : $args['key'];
        $data = array();
        for($i=1;$i<=5;++$i){
            $data[$i] = $i.'+';
        }
        PTB_Search_Public::show_as('select', $post_type, $id, $name, $value, $args['key'], $label,$data);
    }
    
    public function search_rating($post_id,$post_type,$value,$args,$meta_key,$post_taxonomies){
        if($value>0 && $value<=$args['stars_count']){
            $value = round($value);
            $meta_key = 'ptb_'.$meta_key;
            $query_args = array(
                        'fields' => 'ids',
                        'post_type' => $post_type,
                        'orderby' => 'ID',
                        'order' => 'ASC',
                        'nopaging' => 1,
                        'include'=> !empty($post_id) ? implode(',', array_keys($post_id)) : '',
                        'meta_query' => array(
                                array(
                                    'key' =>$meta_key
                                )
                        )
                    );
            $readonly = isset($args['readonly']) && $args['readonly'];
            if($readonly){
                $query_args['meta_query'][0]['value'] = $value;
            }
            else{
                $query_args['meta_query'][0]['compare'] = 'EXISTS';
            }
            $posts_array = get_posts($query_args);
            $post_id = array(); 
            if(!empty($posts_array)){
                if($readonly){
                    foreach ($posts_array as $p) {
                        $post_id[$p] = 1;
                    }
                }
                else{
                    foreach ($posts_array as $p) {
                        $rating = get_post_meta($p,$meta_key,true);
                        if($rating){
                            $v = maybe_unserialize($rating);
                            if(isset($v['count'])){
                                if( $v['count']>0){
                                    $v = round($v['total']/$v['count']);
                                }
                                else{
                                    continue;
                                }
                            }
                            if($v==$value){
                                $post_id[$p] = 1;
                            }
                        }
                    }
                }
            }
        }
        return $post_id;
    }
}
