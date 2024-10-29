<?php

use Elementor\Control_Select2;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 1.0.5
 */
class AWElementor_Post_Status extends Control_Select2 {

	/**
	 * {@inheritdoc}
	 */
	public function get_type() {
		return 'awe-post-status';
	}

	/**
	 * {@inheritdoc}
	 */
	protected function get_default_settings() {
		return array_merge(parent::get_default_settings(), [
			'default'	=> 'publish',
			'options'	=> get_post_stati(),
		]);
	}

}
