<?php

/**
 * Customize and manipulate the Theme Customization admin screen.
 *
 * @param  WP_Customize_Manager $wp_customize
 */
function honeylizard_customize_register($wp_customize) {

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
		'description' => __('Applies to the meta information of the website.', 'honeylizard'),
		'section'     => 'honeylizard_settings',
		])
	);

	// Add Site Logo Option
	$wp_customize->add_setting('site_logo_image', [
		'default'           => $theme_defaults['site_logo_image'],
		'sanitize_callback' => 'honeylizard_sanitize_image',
		'transport'         => 'postMessage',
	]);
	$wp_customize->add_control( new WP_Customize_Image_Control($wp_customize, 'site_logo_image', [
		'label'       => __('Site Logo', 'honeylizard'),
		'description' => __('Applies to the header of the website. Logo must not exceed 80px tall.', 'honeylizard'),
		'section'     => 'honeylizard_settings',
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
