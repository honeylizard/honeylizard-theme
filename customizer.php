<?php

/**
 * Customize and manipulate the Theme Customization admin screen.
 *
 * @param  WP_Customize_Manager $wp_customize
 */
function honeylizard_customize_register($wp_customize) {

	// Activate the Selective Refresh for the standard Wordpress options.

	// Site Identity (id: title_tagline)
	// |__ Logo (id: custom_logo)
	// |__ Site Title (id: blogname)
	// |__ Tagline (id: blogdescription)
	// |__ 'Display Site title and Tagline (id: display_header_text)
	// |__ Site Icon (id: site_icon)
	// Colors (id: colors)
	// |__ Background Color (id: background_color)
	// |__ Header Text Color (id: header_textcolor)
	// Header Image (id: header_image)
	// |__ Header Video (id: header_video)
	// |__ 'Or, enter a YouTube URL' (id: external_header_video)
	// |__ Current Header (id: header_image)
	// Background Image (id: background_image)
	// |__ Background Image (id: background_image)
	// |__ Preset (id: background_preset)
	// |__ Image Position (id: background_position)
	// |__ Image Size (id: background_size)
	// |__ 'Repeat Background Image' (id: background_repeat)
	// |__ 'Scroll with Page' (id: background_attachment)
	// Static Front Page (id: )
	// |__ Front page displays (id: show_on_front)
	// |__ Front page (id: page_on_front)
	// |__ Posts page (id: page_for_posts)
	// Additional CSS (id: custom_css)
	// |__ 'CSS area' (id: custom_css)

	if ( isset( $wp_customize->selective_refresh ) ) {
		$wp_customize->get_setting('blogname')->transport = 'postMessage';
		$wp_customize->selective_refresh->add_partial('blogname', [
			'selector'        => '.site-title',
			'render_callback' => 'honeylizard_customize_partial_blogname',
		]);

		$wp_customize->get_setting('blogdescription')->transport = 'postMessage';
		$wp_customize->selective_refresh->add_partial('blogdescription', [
			'selector'        => '.site-tagline',
			'render_callback' => 'honeylizard_customize_partial_blogdescription',
		]);

		$wp_customize->selective_refresh->add_partial('custom_logo', [
			'settings' => ['custom_logo'],
			'selector' => '.site-logo',
			'render_callback' => 'honeylizard_customize_partial_custom_logo',
			'fallback_refresh' => true,
			'container_inclusive' => true,
		]);
	}

	// Change order of options on Customize -> Site Identity
	$wp_customize->get_control('custom_logo')->priority = 5;
	$wp_customize->get_control('blogname')->priority = 6;
	$wp_customize->get_control('blogdescription')->priority = 7;

	$theme_defaults = Wordpress::settingDefaults();

	$wp_customize->add_section('honeylizard_settings', [
		'title' => __('Theme Specific Settings', 'honeylizard'),
		'priority' => 50,
	]);

	// Add Keywords Option
	$wp_customize->add_setting('meta_keywords', [
		'default'           => '',
		'sanitize_callback' => 'honeylizard_sanitize_text',
		'transport'         => 'postMessage',
	]);
	$wp_customize->add_control( new WP_Customize_Control($wp_customize, 'meta_keywords', [
			'label'       => __('Meta Keywords', 'honeylizard'),
			'description' => __('Applies to the meta information of the website.', 'honeylizard'),
			'section'     => 'honeylizard_settings',
			'type'        => 'text',
			'input_attrs' => [
				'placeholder' => 'e.g. blog, portfolio, photography'
			]
		])
	);

	// Add Description Option
	$wp_customize->add_setting('meta_description', [
		'default'           => '',
		'sanitize_callback' => 'honeylizard_sanitize_text',
		'transport'         => 'postMessage',
	]);
	$wp_customize->add_control( new WP_Customize_Control($wp_customize, 'meta_description', [
			'label'       => __('Meta Description', 'honeylizard'),
			'description' => __('Applies to the meta information of the website for all pages. It should be between 135 and 160 characters. If left blank, Google will create a snippet in search results. ', 'honeylizard'),
			'section'     => 'honeylizard_settings',
			'type'        => 'text',
			'input_attrs' => [
				'placeholder' => 'e.g. This is my personal travel photography blog.'
			]
		])
	);

	// Add Google Analytics Option
	$wp_customize->add_setting('google_analytics', [
		'default'           => '',
		'sanitize_callback' => 'honeylizard_sanitize_text',
		'transport'         => 'postMessage',
	]);
	$wp_customize->add_control( new WP_Customize_Control($wp_customize, 'google_analytics', [
			'label'       => __('Google Analytics Tracking ID', 'honeylizard'),
			'description' => __('Applies the Google Analytics hook. The ID can be found under your Google Analytics Settings.', 'honeylizard'),
			'section'     => 'honeylizard_settings',
		])
	);
}
add_action('customize_register', 'honeylizard_customize_register');

/**
 * Callback that sanitizes text input.
 *
 * @param string $input     The provided input that needs to be sanitized.
 *
 * @return string
 */
function honeylizard_sanitize_text($input) {
	return wp_kses_post(force_balance_tags($input));
}

/**
 * Callback that sanitizes image URL input.
 *
 * @param string $input     The provided input that needs to be sanitized.
 *
 * @return string
 */
function honeylizard_sanitize_image($input) {
	return esc_url_raw($input);
}

/**
 * Render the site title for the selective refresh partial.
 *
 * @since Honeylizard 7.02
 * @see honeylizard_customize_register()
 *
 * @return void
 */
function honeylizard_customize_partial_blogname() {
	bloginfo('name');
}

/**
 * Render the site tagline for the selective refresh partial.
 *
 * @since Honeylizard 7.02
 * @see honeylizard_customize_register()
 *
 * @return void
 */
function honeylizard_customize_partial_blogdescription() {
	bloginfo('description');
}

/**
 * Render the site logo for the selective refresh partial.
 *
 * @since Honeylizard 7.02
 * @see honeylizard_customize_register()
 *
 * @return void
 */
function honeylizard_customize_partial_custom_logo() {
	echo Wordpress::getSiteLogo();
}

/**
 * Render the site logo for the selective refresh partial.
 *
 * @since Honeylizard 7.02
 * @see honeylizard_customize_register()
 *
 * @return void
 */
function honeylizard_customize_partial_custom_header_image() {
	echo '<img class="aligncenter" alt="" src="' . get_header_image() . '">';
}

/**
 * Enqueue the assets directly into the Theme Customization admin screen.
 * This will allow real-time manipulation of the settings on the admin screen.
 */
function honeylizard_customizer_live_preview() {
	wp_enqueue_script(
		'hl-theme-customizer',
		get_template_directory_uri() . '/theme-customizer.js',
		['jquery', 'customize-preview'],
		'1.0.0',
		true
	);
}
add_action('customize_preview_init', 'honeylizard_customizer_live_preview');
