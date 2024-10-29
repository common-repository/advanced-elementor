<?php

use Elementor\Control_Select;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 1.0.3
 */
class AWElementor_ACF_Subfield extends Control_Select {

	/**
	 * {@inheritdoc}
	 */
	public function get_type() {
		return 'awe-acf-subfield';
	}

	/**
	 * {@inheritdoc}
	 */
	protected function get_default_settings() {
		return [
			'options'	=> [],
			'groups'	=> $this->get_acf_groups(),
		];
	}

	/**
	 * @return array
	 */
	private function get_acf_groups() {
		if (!(function_exists( 'acf_get_field_groups' ) && function_exists( 'acf_get_fields' ))) {
			return [];
		}

		$groups = [];
		foreach (acf_get_field_groups() as $group) {
			$arrayable_fields_in_group = array_filter(
				acf_get_fields( $group['ID'] ),
				[$this, 'filter_acf_field']
			);

			$group_options = $this->get_acf_group_options($arrayable_fields_in_group);

			if ( !empty($group_options) ) {
				$groups[] = [
					'label'		=> $group['title'],
					'options'	=> $group_options,
				];
			}
		}
		return $groups;
	}

	/**
	 * @param array $fields
	 * @return array
	 */
	private function get_acf_group_options($fields) {
		$options = [];

		foreach ($fields as $field) {
			foreach ($field['sub_fields'] as $subfield) {
				if (in_array($subfield['type'], ['post_object'])) { // see if subfield is a relation to another post
					$options[ $subfield['key'] ] = $field['label'] . ' / ' . $subfield['label'];
				}
			}
		}

		return $options;
	}

	/**
	 * @param array $field
	 * @return bool
	 */
	protected function filter_acf_field($field) {
		return in_array($field['type'], $this->acf_arrayable_types());
	}

	/**
	 * @return array
	 */
	protected function acf_arrayable_types() {
		return [ 'message', 'accordion', 'tab', 'group', 'repeater', 'flexible_content', 'clone' ];
	}

}
