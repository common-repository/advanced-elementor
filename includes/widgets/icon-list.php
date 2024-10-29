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
class Widget_Advanced_Icons extends Widget_Item_List_Base {

	/**
	 * {@inheritdoc}
	 */
	public function get_name() {
		return 'advanced-icons';
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_title() {
		return __( 'Advanced Icons', 'plugin-name' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_icon() {
		return 'fa fa-ellipsis-h';
	}

	/**
	 * @return Repeater
	 */
	protected function get_item_repeater() {
		$repeater = new Repeater();

		$repeater->add_control(
			'icon',
			[
				'label'	=> __( 'Icon', 'awelementor' ),
				'type'	=> Controls_Manager::ICON,
//				'condition'	=> [
//					'icon_type'	=> 'icon',
//				],
			]
		);

		$repeater->add_control(
			'link',
			[
				'label' => __( 'Link', 'awelementor' ),
				'type' => Controls_Manager::URL,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => __( 'https://your-link.com', 'awelementor' ),
			]
		);

		return $repeater;
	}

	/**
	 * @param array $item
	 * @param string $key
	 * @return string
	 */
	protected function get_item_html(array $item, $key = 'icon') {
		$this->add_render_attribute($key, 'class', "fa fa-{$item['icon']}");

		$icon_html = "<i {$this->get_render_attribute_string($key)}></i>";

		return empty($item['link']['url'])
			? $icon_html
			: $this->get_link_html($item['link'], "{$key}-link") . $icon_html . '</a>'
		;
	}

}