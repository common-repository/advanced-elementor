<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Plugin;
use Elementor\Repeater;
use Elementor\Scheme_Color;
use Elementor\Scheme_Typography;

/**
 * Elementor ACF Posts Widget.
 *
 * Elementor widget that inserts a list of posts as specified by an ACF repeater field.
 *
 * @since 1.0.0
 */
class Widget_Advanced_Posts extends \Elementor\Widget_Base {

	/**
	 * {@inheritdoc}
	 */
	public function get_name() {
		return 'advanced-posts';
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_title() {
		return __( 'Advanced Posts', 'plugin-name' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_icon() {
		return 'fa fa-list';
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_categories() {
		return [ 'advanced' ];
	}

	/**
	 * @return bool
	 */
	private function have_acf() {
		return class_exists( 'acf' );
	}

	/**
	 * @return array
	 */
	private function get_source_types() {
		$source_types = [
			'global_query'	=> __( 'Current Query', 'awelementor' ),
			'query'			=> __( 'Custom Query', 'awelementor' ),
		];

		// Only add ACF choice if ACF is actually active
		if ( $this->have_acf() ) {
			$source_types['acf'] = __( 'ACF', 'awelementor' );
		}

		return $source_types;
	}

	/**
	 * Register ACF Form widget controls.
	 *
	 * @since 1.0.3
	 * @access protected
	 */
	protected function _register_controls() {
		$this->render_controls_content();
		$this->render_controls_style();
	}

	/**
	 * @since 1.0.3
	 */
	private function render_controls_content() {
		$this->start_controls_section('source_section', [
			'label'	=> __( 'Source', 'awelementor' ),
			'tab'	=> Controls_Manager::TAB_CONTENT,
		]);

		$this->add_control('source_type', [
			'label'		=> __( 'Type', 'awelementor' ),
			'type'		=> Controls_Manager::SELECT,
			'options'	=> $this->get_source_types(),
			'separator'	=> 'after',
		]);

		$this->add_control('query_post_type', [
			'label'		=> __( 'Post Type', 'awelementor' ),
			'type'		=> 'awe-post-type',
			'condition'	=> [
				'source_type'	=> 'query',
			],
		]);
		$this->add_control('query_post_status', [
			'label'		=> __( 'Post Status', 'awelementor' ),
			'type'		=> 'awe-post-status',
			'condition'	=> [
				'source_type'	=> 'query',
			],
		]);
		$this->add_control('query_posts_per_page', [
			'label'		=> __( 'Page Size', 'awelementor' ),
			'type'		=> Controls_Manager::NUMBER,
			'default'	=> get_option( 'posts_per_page' ),
			'condition'	=> [
				'source_type'	=> 'query',
			],
		]);

		if ( $this->have_acf() ) {
			$this->add_control('acf_field_key', [
				'label'		=> __( 'ACF Field', 'awelementor' ),
				'type'		=> 'awe-acf-subfield',
				'condition'	=> [
					'source_type'	=> 'acf',
				],
			]);
		}

		$this->end_controls_section();
		
		$this->start_controls_section('template_section', [
			'label'		=> __( 'Template', 'awelementor' ),
			'tab'		=> Controls_Manager::TAB_CONTENT,
		]);

		$this->add_control('template', [
			'label'		=> __( 'Section Template', 'awelementor' ),
			'type'		=> Controls_Manager::SELECT,
			'options'	=> $this->get_elementor_sections(),
		]);

		$this->end_controls_section();

		$this->start_controls_section('grid_section', [
			'label'		=> __( 'Grid', 'awelementor' ),
			'tab'		=> Controls_Manager::TAB_CONTENT,
		]);
		$this->add_control('grid_columns', [
			'label'		=> __( 'Columns', 'awelementor' ),
			'type'		=> Controls_Manager::SELECT,
			'default'	=> 1,
			'options'	=> array_combine(range(1, 12), range(1, 12)),
			'selectors'	=> [
				'{{WRAPPER}} > .elementor-widget-container > .awelementor-posts'	=> 'grid-template-columns: repeat({{VALUE}}, 1fr);',
			],
		]);
		$this->end_controls_section();

		$this->start_controls_section('no_posts_section', [
			'label'		=> __( 'No Posts Found', 'awelementor' ),
			'tab'		=> Controls_Manager::TAB_CONTENT,
		]);
		$this->add_control('show_no_posts_message', [
			'label'			=> __( 'Display Message', 'awelementor' ),
			'type'			=> Controls_Manager::SWITCHER,
			'default'		=> 'yes',
		]);
		$this->add_control('no_posts_message', [
			'label'			=> __( 'Message', 'awelementor' ),
			'type'			=> Controls_Manager::TEXTAREA,
			'default'		=> __( 'No results found.', 'awelementor' ),
			'placeholder'	=> __( 'Write a message to display when no posts are found.', 'awelementor' ),
			'condition'		=> [
				'show_no_posts_message'	=> 'yes',
			],
		]);
		$this->end_controls_section();
	}

	/**
	 * @since 1.0.3
	 */
	private function render_controls_style() {
		$this->start_controls_section('post_section', [
			'label'		=> __( 'Post', 'awelementor' ),
			'tab'		=> Controls_Manager::TAB_STYLE,
		]);

		$this->add_responsive_control('post_margin', [
			'label'			=> __( 'Margin', 'awelementor' ),
			'type'			=> Controls_Manager::DIMENSIONS,
			'size_units'	=> [ 'px', '%' ],
			'selectors'		=> [
				'{{WRAPPER}} > .elementor-widget-container > .awelementor-posts > .awelementor-post' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		]);

		$this->add_control('post_border_style', [
			'label'		=> __( 'Border Style', 'awelementor' ),
			'type'		=> Controls_Manager::SELECT,
			'options'	=> [
				''			=> __( 'None', 'awelementor' ),
				'solid'		=> __( 'Solid', 'awelementor' ),
				'double'	=> __( 'Double', 'awelementor' ),
				'dotted'	=> __( 'Dotted', 'awelementor' ),
				'dashed'	=> __( 'Dashed', 'awelementor' ),
				'groove'	=> __( 'Groove', 'awelementor' ),
			],
			'separator'	=> 'before',
			'selectors'	=> [
				'{{WRAPPER}} > .elementor-widget-container > .awelementor-posts > .awelementor-post'	=> 'border-style: {{VALUE}};',
			],
		]);

		$this->add_control('border_color', [
			'label'		=> __( 'Border Color', 'awelementor' ),
			'type'		=> Controls_Manager::COLOR,
			'selectors'	=> [
				'{{WRAPPER}} > .elementor-widget-container > .awelementor-posts > .awelementor-post' => 'border-color: {{VALUE}};',
			],
		]);

		$this->add_control('post_border_width', [
			'label' => __( 'Border Width', 'awelementor' ),
			'type' => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px' ],
			'range' => [
				'px' => [
					'min' => 0,
					'max' => 50,
				],
			],
			'selectors' => [
				'{{WRAPPER}} > .elementor-widget-container > .awelementor-posts > .awelementor-post' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
			],
		]);

		$this->add_control('post_border_radius', [
			'label'			=> __( 'Border Radius', 'awelementor' ),
			'type'			=> Controls_Manager::SLIDER,
			'size_units'	=> [ 'px', '%' ],
			'range'			=> [
				'px'		=> [
					'min'	=> 0,
					'max'	=> 250,
				],
			],
			'separator'		=> 'after',
			'selectors'		=> [
				'{{WRAPPER}} > .elementor-widget-container > .awelementor-posts > .awelementor-post'	=> 'border-radius: {{SIZE}}{{UNIT}}',
			],
		]);

		$this->add_group_control(Group_Control_Box_Shadow::get_type(), [
			'name'		=> 'post_shadow',
			'label'		=> __( 'Shadow', 'awelementor' ),
			'selector'	=> '{{WRAPPER}} .awelementor-post',
		]);

		$this->add_responsive_control('post_halign', [
			'label'		=> __( 'Horizontal Alignment', 'awelementor' ),
			'type'		=> Controls_Manager::CHOOSE,
			'options'	=> [
				'start'	=> [
					'title'	=> __( 'Left', 'awelementor' ),
					'icon'	=> 'fa fa-align-left',
				],
				'center'	=> [
					'title'	=> __( 'Center', 'awelementor' ),
					'icon'	=> 'fa fa-align-center',
				],
				'end'		=> [
					'title'	=> __( 'Right', 'awelementor' ),
					'icon'	=> 'fa fa-align-right',
				],
//				'stretch'	=> [
//					'title'	=> __( 'Fill', 'awelementor' ),
//					'icon'	=> 'fa fa-align-justify',
//				],
			],
			'separator'	=> 'before',
			'selectors' => [
				'{{WRAPPER}} > .elementor-widget-container > .awelementor-posts > .awelementor-post' => 'justify-self: {{VALUE}}',
			],
		]);
		
		$this->add_responsive_control('post_valign', [
			'label'		=> __( 'Vertical Alignment', 'awelementor' ),
			'type'		=> Controls_Manager::CHOOSE,
			'options'	=> [
				'start'	=> [
					'title'	=> __( 'Top', 'awelementor' ),
					'icon'	=> 'fa fa-arrow-up',
				],
				'center'	=> [
					'title'	=> __( 'Middle', 'awelementor' ),
					'icon'	=> 'fa fa-circle',
				],
				'end'		=> [
					'title'	=> __( 'Bottom', 'awelementor' ),
					'icon'	=> 'fa fa-arrow-down',
				],
//				'stretch'	=> [
//					'title'	=> __( 'Fill', 'awelementor' ),
//					'icon'	=> 'fa fa-arrows-alt',
//				],
			],
			'selectors' => [
				'{{WRAPPER}} > .elementor-widget-container > .awelementor-posts > .awelementor-post' => 'align-self: {{VALUE}}',
			],
		]);

		$this->end_controls_section();
	}

	private function get_elementor_sections() {
		$post_type_query = new \WP_Query([
			'post_type'			=> 'elementor_library',
			'posts_per_page'	=> -1,
			'meta_query'		=> [
				[
					'key'		=> '_elementor_template_type',
					'value'		=> 'section',
				],
			],
		]);
		return wp_list_pluck($post_type_query->posts, 'post_title', 'ID');
	}

	protected function get_query() {
		$args = [
			'post__in'	=> [0],
		];

		switch ( $this->get_settings( 'source_type' ) ) {
			case 'global_query':
				global $wp_query;
				return $wp_query;
			case 'query':
				$args = [
					'post_type'			=> $this->get_settings( 'query_post_type' ),
					'post_status'		=> $this->get_settings( 'query_post_status' ),
					'posts_per_page'	=> $this->get_settings( 'query_posts_per_page' ),
				];
				break;
			case 'acf':
				$acf_field_key = $this->get_settings( 'acf_field_key' );
				if (empty($acf_field_key)) {
					break;
				}
				$post_ids = [];
				$post_meta = get_post_meta(get_queried_object_id());
				foreach ($post_meta as $meta_key => $meta_value) {
					if (in_array($acf_field_key, $meta_value)) {
						$post_ids[] = $post_meta[ltrim($meta_key, '_')][0];
					}
				}
				$args['post__in'] = empty($post_ids) ? [0] : $post_ids;
				break;
		}

		// This part is not ideal... TODO
		if (!isset($args['post_type'])) {
			$args['post_type'] = 'any';
		}
		if (!isset($args['post_status'])) {
			$args['post_status'] = 'publish';
		}

		return new \WP_Query($args);
	}

	/**
	 * Render Advanced Posts output on the frontend.
	 *
	 * @since 1.0.3
	 * @access protected
	 */
	protected function render() {
		$template = $this->get_settings( 'template' );

		if ( empty( $template ) ) {
			echo __( 'Please select a template.', 'awelementor' );
			return;
		} elseif ( get_post_status( $template ) !== 'publish' ) {
			echo __( 'Please publish your template.', 'awelementor' );
			return;
		}

		echo $this->get_posts_header();

		/* @var \WP_Query $query */
		$query = $this->get_query();

		if ( !$query->found_posts ) {
			$this->render_empty();
			return;
		}

		$this->add_render_attribute('awelementor-posts', [
			'class'	=> 'awelementor-posts',
			'style'	=> 'display: grid;',
		]);

		echo "<div {$this->get_render_attribute_string('awelementor-posts')}>";

		if ( $query->in_the_loop ) {
			$this->render_post($template);
		} else {
			while ( $query->have_posts() ) {
				$query->the_post();
				$this->render_post($template);
			}
		}
		wp_reset_postdata();

		echo '</div>';

		echo $this->get_posts_footer();
	}

	/**
	 * @since 1.0.3
	 */
	protected function render_empty() {
		if ( $this->get_settings( 'show_no_posts_message' ) === 'yes' ) {
			echo '<div>' . $this->get_settings_for_display( 'no_posts_message' ) . '</div>';
		}
	}

	/**
	 * @since 1.0.3
	 * @param int $template
	 */
	protected function render_post($template) {
		echo '<div class="awelementor-post elementor-template">';
		echo Plugin::instance()->frontend->get_builder_content_for_display( $template );
		echo '</div>';
	}

	/**
	 * @return string
	 */
	protected function get_posts_header() {
		
	}

	/**
	 * @return string
	 */
	protected function get_posts_footer() {

	}

}