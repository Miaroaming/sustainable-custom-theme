<?php
    add_theme_support('post-thumbnails');
    // Add support for custom logo
    add_theme_support('custom-logo');

    // Add CORS support
    function add_cors_http_header() {
        header("Access-Control-Allow-Origin: *");
    }
    add_action('init', 'add_cors_http_header');

    // Enque or Stylesheets - Wordpress not the React Frontend:
    function enqueue_parent_and_custom_styles() {
        // parent theme styles:
        wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');

        // custom styles:
        wp_enqueue_style('child-style', get_template_directory_uri() . '/custom.css', array('parent-style'));
    }
    add_action('wp_enqueue_scripts', 'enqueue_parent_and_custom_styles');
    

    function filter_event_by_meta($args, $request) {
        $event_type = $request->get_param('event_type');
    
        if ($event_type) {
            $args['meta_query'] = array(
                array(
                    'key' => 'event_type', // The custom field name
                    'value' => $event_type, // The value to match (e.g., 'past')
                    'compare' => '='
                )
            );
        }
    
        return $args;
    }
    
    add_filter('rest_event_query', 'filter_event_by_meta', 10, 2);
    


    function custom_excerpt_length($length) {
        return 30; // change the number of character for excerpt length
    }

    // call the function within the corrrect WP hooks
    add_filter('excerpt_length', 'custom_excerpt_length' , 999 );

    // Cutomiser settings:
    function custom_theme_customize_register( $wp_customize ) {
        
        // Register and customizer settings:
        $wp_customize->add_setting('background_color', array(
            'default' => '#ffffff', // default color
            'transport' => 'postMessage',
        ));

        // Add a control for the background color
        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'background_color', array(
            'label' => __('Background Colour', 'custom-theme'),
            'section' => 'colors',
        )));

        // Font Family
        // Add the Font section
        $wp_customize->add_section('fonts', array(
            'title' => __('Fonts', 'custom-theme'),
            'priority' => 30,
        ));

        // Add the Font setting
        $wp_customize->add_setting('font_family', array(
            'default' => 'Goudy Bookletter 1911',
            'transport' => 'postMessage',
        ));

        // Add the Control of Fonts
        $wp_customize->add_control('font_family_control', array(
            'label' => 'Font Family',
            'section' => 'fonts',
            'settings' => 'font_family',
            'type' => 'select',
            'choices' => array(
                'Goudy Bookletter 1911' => 'Goudy Bookletter 1911',
                'Baskervville' => 'Baskervville',
                'Prata' => 'Prata',
                'Spectral' => 'Spectral'
            ),
        ));

        // Mobile Menu BG Color
        $wp_customize->add_setting('mobile_menu_color', array(
            'default' => '#011739',
            'transport' => 'postMessage',
        ));

        // Mobile Menu Control
        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'mobile_menu_color', array(
            'label' => __('Mobile Menu Colour', 'custom-theme'),
            'section' => 'colors',
        )));

        // Navbar Bg Color
        $wp_customize->add_setting('navbar_color', array(
            'default' => '#011739',
            'transport' => 'postMessage',
        ));

        // Navbar Bg Control
        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'navbar_color', array (
            'label' => __('Navbar Color', 'custom-theme'),
            'section' => 'colors',
        )));

    }

    add_action('customize_register', 'custom_theme_customize_register');

    // Custom Rest API endpoint to retreive customiser settings
    function get_customizer_settings() {
        $settings = array(
            'backgroundColor' => get_theme_mod('background_color', '#ffffff'),
            // Additional settings...
            'fontFamily' => get_theme_mod('font_family', 'Goudy Bookletter 1911'),
            'mobileMenu' => get_theme_mod('mobile_menu_color', '#011739'),
            'navbarColor' => get_theme_mod('navbar_color', '#011739'),
        );

        return rest_ensure_response($settings);
    }

    add_action('rest_api_init', function () {
        register_rest_route('custom-theme/v1', '/customizer-settings', array(
            'methods' => 'GET',
            'callback' => 'get_customizer_settings'
        ));
    });

    // GET NAV LOGO THAT IS SET IN THE ADMIN DASHBOARD:
    function get_nav_logo() {
        $custom_logo_id = get_theme_mod('custom_logo');
        $logo = wp_get_attachment_image_src($custom_logo_id, 'full');

        return $logo;
    }

    add_action('rest_api_init', function () {
        register_rest_route('custom/v1', 'nav-logo', array(
            'methods' => 'GET',
            'callback' => 'get_nav_logo',
        ));
    });

?>