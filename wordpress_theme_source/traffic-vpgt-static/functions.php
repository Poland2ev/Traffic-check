<?php
/**
 * Theme setup for the static traffic lookup demo.
 */

function traffic_vpgt_static_assets() {
    $theme_uri = get_template_directory_uri();

    wp_enqueue_style(
        'traffic-vpgt-adminlte',
        '/traffic_offense/dist/css/adminlte.css',
        array(),
        '1.0'
    );

    wp_enqueue_style(
        'traffic-vpgt-style',
        get_stylesheet_uri(),
        array('traffic-vpgt-adminlte'),
        '1.0'
    );

    wp_enqueue_script(
        'traffic-vpgt-bootstrap',
        '/traffic_offense/plugins/bootstrap/js/bootstrap.bundle.min.js',
        array(),
        '1.0',
        true
    );

    wp_enqueue_script(
        'traffic-vpgt-data',
        $theme_uri . '/data.js',
        array(),
        '1.0',
        true
    );

    wp_enqueue_script(
        'traffic-vpgt-app',
        $theme_uri . '/app.js',
        array('traffic-vpgt-data'),
        '1.0',
        true
    );
}
add_action('wp_enqueue_scripts', 'traffic_vpgt_static_assets');

add_theme_support('title-tag');
