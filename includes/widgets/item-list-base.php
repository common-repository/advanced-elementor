<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Scheme_Color;
use Elementor\Scheme_Typography;
use Elementor\Repeater;
use Elementor\Utils;

/**
 * Elementor ACF Form Widget.
 *
 * Elementor widget that inserts an ACF form content into the page, from any given post type.
 *
 * @since 1.0.3
 */
abstract class Widget_Item_List_Base extends \Elementor\Widget_Base {

	/**
	 * {@inheritdoc}
	 */
	public function get_categories() {
		return [ 'advanced' ];
	}

	/**
	 * @return Repeater
	 */
	abstract protected function get_item_repeater();

	/**
	 * Register ACF Form widget controls.
	 *
	 * @since 1.0.3
	 * @access protected
	 */
	protected function _register_controls() {
		$this->start_controls_section(
			'item_section_content',
			[
				'label'	=> __( 'Items', 'awelementor' ),
				'tab'	=> Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'items',
			[
				//'label'			=> __( 'Items', 'awelementor' ),
				'type'			=> Controls_Manager::REPEATER,
				//'title_field'	=> '{{{ icon }}}',
				//'separator'		=> 'before',
				'fields'		=> $this->get_item_repeater()->get_controls(),
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'item_section_style',
			[
				'label'	=> __( 'Icons', 'awelementor' ),
				'tab'	=> Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_responsive_control(
			'align',
			[
				'label' => __( 'Alignment', 'awelementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left'    => [
						'title' => __( 'Left', 'awelementor' ),
						'icon' => 'fa fa-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'awelementor' ),
						'icon' => 'fa fa-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'awelementor' ),
						'icon' => 'fa fa-align-right',
					],
				],
				'default' => 'center',
				'selectors' => [
					'{{WRAPPER}}' => 'text-align: {{VALUE}};',
				],
			]
		);
		$this->end_controls_section();
	}

	/**
	 * @param array $link
	 * @param string $key
	 * @return string
	 */
	protected function get_link_html(array $link, $key = 'link') {
		$this->add_render_attribute($key, 'href', $link['url']);

		if (!empty($link['is_external'])) {
			$this->add_render_attribute($key, 'target', '_blank');
		}

		if (!empty($link['nofollow'])) {
			$this->add_render_attribute($key, 'rel', 'nofollow');
		}

		return "<a {$this->get_render_attribute_string($key)}>";
	}

	/**
	 * @param array $item
	 * @param string $key
	 * @return string
	 */
	abstract protected function get_item_html(array $item, $key = 'item');

	/**
	 * Render Item List output on the frontend.
	 *
	 * @since 1.0.3
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		echo '<div class="awelementor-item-list">';

		if (!empty($settings['items'])) {
			foreach ($settings['items'] as $i => $item) {
				echo $this->get_item_html($item, "item-{$i}");
			}
		}

		echo '</div>';
	}

}