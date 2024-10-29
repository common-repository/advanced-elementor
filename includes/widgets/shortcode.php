<?php

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor ACF shortcode widget.
 *
 * Elementor widget that inserts any shortcodes into the page,
 * with dynamically configurable attributes.
 *
 * @since 1.0.0
 */
class Widget_Advanced_Shortcode extends Widget_Base {

	/**
	 * Get widget name.
	 *
	 * Retrieve shortcode widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'advanced-shortcode';
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_categories() {
		return [ 'advanced' ];
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_title() {
		return __( 'Advanced Shortcode', 'awelementor' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_icon() {
		return 'eicon-shortcode';
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_keywords() {
		return [ 'shortcode', 'code', 'advanced' ];
	}

	/**
	 * {@inheritdoc}
	 */
	public function is_reload_preview_required() {
		return true;
	}

	/**
	 * Register shortcode widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function _register_controls() {
		$this->start_controls_section(
			'section_shortcode',
			[
				'label' => __( 'Shortcode', 'awelementor' ),
			]
		);

		$this->add_control(
			'shortcode',
			[
				'label'			=> __( 'Enter your shortcode', 'awelementor' ),
				'type'			=> Controls_Manager::TEXTAREA,
				'dynamic'		=> [
					'active'	=> true,
				],
				'placeholder'	=> 'gallery',
				'default'		=> '',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_args',
			[
				'label' => __( 'Arguments', 'awelementor' ),
			]
		);
		$repeater = new Repeater();
		$repeater->add_control(
			'argument',
			[
				'label'			=> __( 'Argument', 'awelementor' ),
				'type'			=> Controls_Manager::TEXT,
			]
		);
		$repeater->add_control(
			'value',
			[
				'label'			=> __( 'Value', 'awelementor' ),
				'type'			=> Controls_Manager::TEXT,
				'dynamic'		=> [
					'active'	=> true,
				],
			]
		);
		$this->add_control(
			'args',
			[
				'type'			=> Controls_Manager::REPEATER,
				'fields'		=> $repeater->get_controls(),
				'title_field'	=> '{{{ argument }}}="{{{ value }}}"',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Render shortcode widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {
		$shortcode = trim($this->get_settings_for_display( 'shortcode' ), '[]');
		$args = array_reduce($this->get_settings_for_display( 'args' ), function($args, $arg) {
			if (isset($arg['argument'], $arg['value'])) {
				$args .= " {$arg['argument']}=\"{$arg['value']}\"";
			}
			return $args;
		});

		echo '<div class="elementor-shortcode awelementor-shortcode">';
		echo do_shortcode(shortcode_unautop('[' . $shortcode . $args . ']'));
		echo '</div>';
	}

	/**
	 * {@inheritdoc}
	 */
	public function render_plain_content() {
		echo $this->get_settings( 'shortcode' );
	}

	/**
	 * {@inheritdoc}
	 */
	protected function _content_template() {}

}