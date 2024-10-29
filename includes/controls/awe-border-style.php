<?php

use Elementor\Control_Select2;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 1.0.6
 */
class AWElementor_Border_Style extends Control_Select2 {

	/**
	 * {@inheritdoc}
	 */
	public function get_type() {
		return 'awe-border-style';
	}

	/**
	 * {@inheritdoc}
	 */
	protected function get_default_settings() {
		return [
			'label'		=> __( 'Border Style', 'awelementor' ),
			'options'	=> $this->get_styles(),
			'default'	=> 'solid',
		];
	}

	/**
	 * @return array
	 */
	private function get_styles() {
		return [
			'none'		=> __( 'None', 'awelementor' ),
			'solid'		=> __( 'Solid', 'awelementor' ),
			'double'	=> __( 'Double', 'awelementor' ),
			'dotted'	=> __( 'Dotted', 'awelementor' ),
			'dashed'	=> __( 'Dashed', 'awelementor' ),
		];
	}

}
