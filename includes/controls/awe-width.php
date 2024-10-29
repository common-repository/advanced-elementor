<?php

use Elementor\Control_Select;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 1.0.6
 */
class AWElementor_Width extends Control_Select {

	/**
	 * @var array
	 */
	private static $widths = [100, 80, 75, 66, 60, 50, 40, 33, 25, 20];

	/**
	 * {@inheritdoc}
	 */
	public function get_type() {
		return 'awe-width';
	}

	/**
	 * {@inheritdoc}
	 */
	protected function get_default_settings() {
		return [
			'options'	=> $this->get_widths(),
			'default'	=> '100',
		];
	}

	/**
	 * @return array
	 */
	private function get_widths() {
		return array_reduce(self::$widths, function($c,$v) {
			return $c + [$v => "{$v}%"];
		}, [
			''	=> __( 'Default', 'awelementor' ),
		]);
	}

}
