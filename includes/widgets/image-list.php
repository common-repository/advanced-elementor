<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Control_Media;
use Elementor\Group_Control_Image_Size;
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
class Widget_Image_List extends Widget_Item_List_Base {

	/**
	 * {@inheritdoc}
	 */
	public function get_name() {
		return 'image-list';
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_title() {
		return __( 'Image List', 'plugin-name' );
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
			'image',
			[
				'label'	=> __( 'Image', 'awelementor' ),
				'type' => Controls_Manager::MEDIA,
				'dynamic' => [
					'active' => true,
				],
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
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
	protected function get_item_html(array $item, $key = 'item') {
		//return print_r($item, true);
//		$image_url = Group_Control_Image_Size::get_attachment_image_src( $item['image']['id'], $key, $item['image'] );
//		return '<img class="image-item" src="' . esc_attr( $image_url ) . '" alt="' . esc_attr( Control_Media::get_image_alt( $item['image'] ) ) . '" />';

//		$this->add_render_attribute($key, 'class', "fa fa-{$item['icon']}");
//		$icon_html = "<i {$this->get_render_attribute_string($key)}></i>";
//		return empty($item['link']['url'])
//			? $icon_html
//			: $this->get_link_html($item['link'], "{$key}-link") . $icon_html . '</a>'
//		;

		$has_caption = !empty($item['caption']);
		$link = $item['link'];

		$this->add_render_attribute("{$key}-wrapper", 'class', 'elementor-image');
		
		echo "<div {$this->get_render_attribute_string("{$key}-wrapper")}>";

		if ($has_caption) {
			echo "<figure class=\"wp-caption\">";
		}

		if ($link) {
			echo $this->get_link_html($link, "{$key}-link");
		}

		echo Group_Control_Image_Size::get_attachment_image_html($item);

		if ($link) {
			echo '</a>';
		}

		if ($has_caption) {
			echo "<figcaption class=\"widget-image-caption wp-caption-text\">{$item['caption']}</figcaption>";
			echo '</figure>';
		}

		echo '</div>';
	}

}