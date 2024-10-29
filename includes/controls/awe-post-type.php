<?php

use Elementor\Control_Select;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 1.0.5
 */
class AWElementor_Post_Type extends Control_Select {

	/**
	 * {@inheritdoc}
	 */
	public function get_type() {
		return 'awe-post-type';
	}

	/**
	 * {@inheritdoc}
	 */
	protected function get_default_settings() {
		return [
			'default'	=> 'post',
			'options'	=> $this->get_post_types(),
		];
	}

	/**
	 * @return array
	 */
	private function get_post_types() {
		return array_reduce(
			get_post_types([
				'public'	=> true,
			]),
			function($options, $post_type) {
				return $options + [
					$post_type => get_post_type_labels(get_post_type_object($post_type))
						->singular_name,
				];
			},
			[]
		);
	}

}
