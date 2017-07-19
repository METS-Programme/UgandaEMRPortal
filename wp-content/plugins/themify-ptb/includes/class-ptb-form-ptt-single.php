<?php

class PTB_Form_PTT_Single extends PTB_Form_PTT_Them {

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     *
     * @param string $plugin_name
     * @param string $version
     * @param PTB_Options $options the plugin options instance
     * @param string themplate_id
     *
     */
    public function __construct($plugin_name, $version, $themplate_id=false) {
        parent::__construct($plugin_name, $version, $themplate_id);
    }

    /**
     * Single layout parametrs
     *
     * @since 1.0.0
     */
    public function add_fields($data = array()) {
        $fieldname = $this->get_field_name('navigation_post');
        $field_id_yes = $this->get_field_id('navigation_post_yes');
        $field_id_no = $this->get_field_id('navigation_post_no');
        ?>
        <div class="ptb_lightbox_row ptb_navigate_post">
            <div class="ptb_lightbox_label"><?php _e('Post navigation', 'ptb'); ?></div>
             <div class="ptb_lightbox_input">
                <input 
                    <?php if (!isset($data[$fieldname]) || ( isset($data[$fieldname]) && $data[$fieldname] == '1' )): ?>checked="checked"<?php endif; ?>
                    type="radio" name="<?php echo $fieldname ?>" value="1" id="<?php echo $field_id_yes ?>"/>
                <label for="<?php echo $field_id_yes; ?>"><?php _e('Yes', 'ptb'); ?></label>
                <input 
                    <?php if (isset($data[$fieldname]) && $data[$fieldname] == '0'): ?>checked="checked"<?php endif; ?>
                    type="radio" name="<?php echo $fieldname ?>" value="0" id="<?php echo $field_id_no; ?>"/>
                <label for="<?php echo $field_id_no ?>"><?php _e('No', 'ptb'); ?></label>
            </div>
        </div>
        <?php
    }

}
