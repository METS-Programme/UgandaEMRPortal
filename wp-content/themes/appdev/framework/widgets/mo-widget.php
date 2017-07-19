<?php

/**
 * Plugin Name: Livemesh Framework Widget
 * Plugin URI: http://portfoliotheme.org/
 * Description: An abstract class for all Livemesh framework widgets
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */
abstract class MO_Widget extends WP_Widget {

    /**
     * Widget setup.
     */
    public function init() {

    }

    public function WP_Widget( $id_base, $name, $widget_options = array(), $control_options = array() ) {
        parent::__construct( $id_base, $name, $widget_options, $control_options );
    }

}

?>