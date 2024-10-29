<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Scheme_Color;
use Elementor\Scheme_Typography;
use Elementor\Repeater;

/**
 * Elementor ACF Form Widget.
 *
 * Elementor widget that inserts an ACF form content into the page, from any given post type.
 *
 * @since 1.0.0
 */
class Widget_ACF_Form extends \Elementor\Widget_Base {

	/**
	 * {@inheritdoc}
	 */
	public function get_name() {
		return 'acf-form';
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_title() {
		return __( 'ACF Form', 'plugin-name' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_icon() {
		return 'fa fa-edit';
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_categories() {
		return [ 'advanced' ];
	}

	/**
	 * @param array $fields
	 * @return array
	 */
	private function build_simple_repeater($fields = array()) {
		$repeater = new Repeater();

		foreach ($fields as $id => $args) {
			$repeater->add_control($id, $args);
		}

		return $repeater->get_controls();
	}

	/**
	 * Register ACF Form widget controls.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function _register_controls() {
		$this->register_content_controls();
		$this->register_style_controls();
	}

	private function register_content_controls() {
		$this->start_controls_section('posting_section', [
			'label'	=> __( 'Posting', 'awelementor' ),
			'tab'	=> Controls_Manager::TAB_CONTENT,
		]);

		/* (array) An array of post data used to create a post. See wp_insert_post for available parameters.
		The above 'post_id' setting must contain a value of 'new_post' */
		$this->add_control('new_post', [
			'label'			=> __( 'New Post', 'awelementor' ),
			'type'			=> Controls_Manager::SWITCHER,
			'default'		=> 'no',
			'label_off'		=> __( 'No', 'awelementor' ),
			'label_on'		=> __( 'Yes', 'awelementor' ),
		]);

		/* (int|string) The post ID to load data from and save data to. Defaults to the current post ID. 
		Can also be set to 'new_post' to create a new post on submit */
		$this->add_control('post_id', [
			'label'			=> __( 'Post ID', 'awelementor' ),
			'type'			=> Controls_Manager::TEXT,
			'input_type'	=> 'text',
			'placeholder'	=> get_the_ID(),
			'dynamic'		=> [
				'active'	=> true,
			],
			'condition'		=> [
				'new_post!'	=> 'yes',
			],
		]);

		/* (boolean) Whether or not to sanitize all $_POST data with the wp_kses_post() function. Defaults to true. Added in v5.6.5 */
		$this->add_control('kses', [
			'label'			=> __( 'KSES Sanitize', 'awelementor' ),
			'type'			=> Controls_Manager::SWITCHER,
			'default'		=> 'yes',
			'label_off'		=> __( 'Off', 'awelementor' ),
			'label_on'		=> __( 'On', 'awelementor' ),
			'separator'		=> 'before',
		]);
		
		$this->add_control('updated_message_heading', [
			'label'		=> __( 'Updated Message', 'awelementor' ),
			'type'		=> Controls_Manager::HEADING,
			'separator' => 'before',
		]);

		$this->add_control('show_updated_message', [
			'label'			=> __( 'Enabled', 'awelementor' ),
			'type'			=> Controls_Manager::SWITCHER,
			'default'		=> 'yes',
			'label_off'		=> __( 'No', 'awelementor' ),
			'label_on'		=> __( 'Yes', 'awelementor' ),
		]);

		/* (string) A message displayed above the form after being redirected. Can also be set to false for no message */
		$this->add_control('updated_message', [
			'label'			=> __( 'Updated Message', 'awelementor' ),
			'type'			=> Controls_Manager::TEXTAREA,
			'placeholder'	=> __( 'Post updated', 'awelementor' ),
			'default'		=> __( 'Post updated', 'awelementor' ),
			'condition'		=> [
				'show_updated_message'	=> 'yes',
			],
		]);

		/* (string) HTML used to render the updated message. Added in v5.5.10 */
		$this->add_control('html_updated_message_switch', [
			'label'			=> __( 'Custom HTML', 'awelementor' ),
			'type'			=> Controls_Manager::SWITCHER,
			'default'		=> 'no',
			'label_off'		=> __( 'Off', 'awelementor' ),
			'label_on'		=> __( 'On', 'awelementor' ),
			'condition'		=> [
				'show_updated_message'	=> 'yes',
			],
		]);
		/* (string) Extra HTML to add before the fields */
		$this->add_control('html_updated_message', [
			'type'			=> Controls_Manager::CODE,
			'label'			=> __( 'Custom Updated Message HTML', 'awelementor' ),
			'default'		=> '<div id="message" class="updated">
	<p>%s</p>
</div>',
			'language'		=> 'html',
			'render_type'	=> 'ui',
			'show_label'	=> false,
			'separator'		=> 'none',
			'condition'		=> [
				'show_updated_message'			=> 'yes',
				'html_updated_message_switch'	=> 'yes',
			],
		]);

		/* (string) The URL to be redirected to after the form is submit. Defaults to the current URL with a GET parameter '?updated=true'.
		// A special placeholder '%post_url%' will be converted to post's permalink (handy if creating a new post)
		// A special placeholder '%post_id%' will be converted to post's ID (handy if creating a new post) */
		$this->add_control('return', [
			'label'			=> __( 'Return URL', 'awelementor' ),
			'type'			=> Controls_Manager::TEXT,
			'input_type'	=> 'text',
			'separator'		=> 'before',
			'dynamic'		=> [
				'active'	=> true,
			],
		]);
		$this->add_control('return_description', [
			'raw'				=> nl2br( __(
				"The URL to be redirected to after the form is submit. Defaults to the current URL with a GET parameter '?updated=true'.\n" .
				"A special placeholder '%post_url%' will be converted to post's permalink (handy if creating a new post).\n" .
				"A special placeholder '%post_id%' will be converted to post's ID (handy if creating a new post).",
				'awelementor'
			) ),
			'type'				=> Controls_Manager::RAW_HTML,
			'content_classes'	=> 'elementor-descriptor',
		]);

		$this->end_controls_section();

		// ************** NEW POST **************

		$this->start_controls_section('new_post_section', [
			'label'			=> __( 'New Post', 'awelementor' ),
			'tab'			=> Controls_Manager::TAB_CONTENT,
			'condition'		=> [
				'new_post'	=> 'yes',
			],
		]);

		$this->add_control('post_type', [
			'label'			=> __( 'Post Type', 'awelementor' ),
			'type'			=> 'awe-post-type',
		]);

		$this->add_control('post_status', [
			'label'			=> __( 'Post Status', 'awelementor' ),
			'type'			=> 'awe-post-status',
			'default'		=> 'pending',
		]);

		$this->add_control('post_title', [
			'label'			=> __( 'Post Title', 'awelementor' ),
			'type'			=> Controls_Manager::TEXT,
			'input_type'	=> 'text',
			'dynamic'		=> [
				'active'	=> true,
			],
		]);

		$this->add_control('post_name', [
			'label'			=> __( 'Post Slug', 'awelementor' ),
			'type'			=> Controls_Manager::TEXT,
			'input_type'	=> 'text',
			'dynamic'		=> [
				'active'	=> true,
			],
		]);

		$this->add_control('post_content', [
			'label'			=> __( 'Post Content', 'awelementor' ),
			'type'			=> Controls_Manager::WYSIWYG,
			'dynamic'		=> [
				'active'	=> true,
			],
		]);

		$this->add_control('post_parent', [
			'label'			=> __( 'Post Parent', 'awelementor' ),
			'type'			=> Controls_Manager::TEXT,
			'input_type'	=> 'text',
			'dynamic'		=> [
				'active'	=> true,
			],
		]);

		$this->add_control('tax_input', [
			'label'			=> __( 'Taxonomies', 'awelementor' ),
			'type'			=> Controls_Manager::REPEATER,
			'title_field'	=> '{{{ taxonomy }}}',
			//'separator'		=> 'before',
			'fields'		=> $this->build_simple_repeater([
				'taxonomy'		=> [
					'label'		=> __( 'Taxonomy', 'awelementor' ),
					'type'		=> Controls_Manager::SELECT,
					'options'	=> array_reduce(get_taxonomies(['public' => true], 'objects'), function($taxonomies, $taxonomy) {
						return $taxonomies + [
							$taxonomy->name => $taxonomy->label . ' (' . $taxonomy->name . ')'
						];
					}, []),
				],
				'value'		=> [
					'label'		=> __( 'Value', 'awelementor' ),
					'type'		=> Controls_Manager::TEXT,
					'dynamic'	=> [
						'active'	=> true,
					],
				],
			]),
		]);

		$this->add_control('meta_input', [
			'label'			=> __( 'Custom Fields', 'awelementor' ),
			'type'			=> Controls_Manager::REPEATER,
			'title_field'	=> '{{{ key }}}',
			//'separator'		=> 'before',
			'fields'		=> $this->build_simple_repeater([
				'key'		=> [
					'label'		=> __( 'Key', 'awelementor' ),
					'type'		=> Controls_Manager::TEXT,
					'default'	=> '',
				],
				'value'		=> [
					'label'		=> __( 'Value', 'awelementor' ),
					'type'		=> Controls_Manager::TEXT,
					'dynamic'	=> [
						'active'	=> true,
					],
				],
			]),
		]);

		$this->end_controls_section();

		// **************** FORM ****************

		$this->start_controls_section('form_section', [
			'label'	=> __( 'Form', 'awelementor' ),
			'tab'	=> Controls_Manager::TAB_CONTENT,
		]);

		/* (boolean) Whether or not to create a form element. Useful when a adding to an existing form. Defaults to true */
		$this->add_control('form', [
			'label'		=> __( 'Form Tag', 'awelementor' ),
			'type'		=> Controls_Manager::SWITCHER,
			'default'	=> 'yes',
			'label_off'	=> __( 'Off', 'awelementor' ),
			'label_on'	=> __( 'On', 'awelementor' ),
		]);
		$this->add_control('form_description', [
			'raw'				=> __( 'Whether or not to create a form element. Useful when a adding to an existing form.', 'awelementor' ),
			'type'				=> Controls_Manager::RAW_HTML,
			'content_classes'	=> 'elementor-descriptor',
		]);

		/* (string) Unique identifier for the form. Defaults to 'acf-form' */
		// should actually be 'id' but that appears to be reserved
		$this->add_control('acf_id', [
			'label'			=> __( 'Form ID', 'awelementor' ),
			'type'			=> Controls_Manager::TEXT,
			'input_type'	=> 'text',
			'placeholder'	=> $this->generate_acf_form_id(),
			'condition'		=> [
				'form'		=> 'yes',
			],
		]);
		$this->add_control('acf_id_description', [
			'raw'				=> __( 'Unique identifier for the form. This will also become the id attribute of the HTML form element.', 'awelementor' ),
			'type'				=> Controls_Manager::RAW_HTML,
			'content_classes'	=> 'elementor-descriptor',
		]);

		/* (array) An array or HTML attributes for the form element */
		$this->add_control('form_attributes', [
			'label'			=> __( 'Custom Attributes', 'awelementor' ),
			'type'			=> Controls_Manager::TEXTAREA,
			'input_type'	=> 'text',
			'placeholder'	=> __( 'key|value', 'awelementor' ),
			'condition'		=> [
				'form'		=> 'yes',
			],
		]);
		$this->add_control('form_attributes_description', [
			'raw'				=> __( 'Custom HTML attributes for the form element.', 'awelementor' ),
			'type'				=> Controls_Manager::RAW_HTML,
			'content_classes'	=> 'elementor-descriptor',
			'condition'			=> [
				'form'			=> 'yes',
			],
		]);

//		/* (array) An array of field group IDs/keys to override the fields displayed in this form */
//		'field_groups' => false,
//
//		/* (array) An array of field IDs/keys to override the fields displayed in this form */
//		'fields' => false,

		$this->add_control('html_before_fields_label', [
			'label'			=> __( 'Extra HTML Before Fields', 'awelementor' ),
			'type'			=> Controls_Manager::SWITCHER,
			'default'		=> 'no',
			'separator'		=> 'before',
			'label_off'		=> __( 'Off', 'awelementor' ),
			'label_on'		=> __( 'On', 'awelementor' ),
		]);
		/* (string) Extra HTML to add before the fields */
		$this->add_control('html_before_fields', [
			'type'			=> Controls_Manager::CODE,
			'label'			=> __( 'HTML Before Fields', 'awelementor' ),
			'language'		=> 'html',
			'render_type'	=> 'ui',
			'show_label'	=> false,
			'separator'		=> 'none',
			'condition'		=> [
				'html_before_fields_label'	=> 'yes',
			],
		]);

		$this->add_control('html_after_fields_label', [
			'label'		=> __( 'Extra HTML After Fields', 'awelementor' ),
			'type'		=> Controls_Manager::SWITCHER,
			'default'	=> 'no',
			'label_off'	=> __( 'Off', 'awelementor' ),
			'label_on'	=> __( 'On', 'awelementor' ),
		]);
		/* (string) Extra HTML to add after the fields */
		$this->add_control('html_after_fields', [
			'type'			=> Controls_Manager::CODE,
			'label'			=> __( 'Extra HTML After Fields', 'awelementor' ),
			'language'		=> 'html',
			'render_type'	=> 'ui',
			'show_label'	=> false,
			'separator'		=> 'none',
			'condition'		=> [
				'html_after_fields_label'	=> 'yes',
			],
		]);

		$this->end_controls_section();



		$this->start_controls_section('field_section', [
			'label'		=> __( 'Field', 'awelementor' ),
			'tab'		=> Controls_Manager::TAB_CONTENT,
		]);

		/* (boolean) Whether or not to show the post title text field. Defaults to false */
		$this->add_control('show_post_title', [
			'label'		=> __( 'Title', 'awelementor' ),
			'type'		=> Controls_Manager::SWITCHER,
			'default'	=> 'yes',
			'label_off'	=> __( 'Hide', 'awelementor' ),
			'label_on'	=> __( 'Show', 'awelementor' ),
		]);

		/* (boolean) Whether or not to show the post content editor field. Defaults to false */
		$this->add_control('show_post_content', [
			'label'		=> __( 'Content', 'awelementor' ),
			'type'		=> Controls_Manager::SWITCHER,
			'default'	=> 'yes',
			'label_off'	=> __( 'Hide', 'awelementor' ),
			'label_on'	=> __( 'Show', 'awelementor' ),
		]);

		/* (boolean) Whether to include a hidden input field to capture non human form submission. Defaults to true. Added in v5.3.4 */
		$this->add_control('honeypot', [
			'label'		=> __( 'Honeypot', 'awelementor' ),
			'type'		=> Controls_Manager::SWITCHER,
			'default'	=> 'yes',
			'label_off'	=> __( 'Off', 'awelementor' ),
			'label_on'	=> __( 'On', 'awelementor' ),
		]);

		$this->add_control('show_label', [
			'label'		=> __( 'Label', 'awelementor' ),
			'type'		=> Controls_Manager::SWITCHER,
			'default'	=> 'yes',
			'label_off'	=> __( 'Hide', 'awelementor' ),
			'label_on'	=> __( 'Show', 'awelementor' ),
		]);
		$this->add_control('label_display', [
			'type'		=> Controls_Manager::HIDDEN,
			'default'	=> 'none', // a non-empty value is required here otherwise this does not work
			'selectors' => [
				'{{WRAPPER}} .acf-label' => 'display: {{VALUE}}',
			],
			'condition'	=> [
				'show_label!'	=> 'yes',
			],
		]);

		$this->add_control('show_required_mark', [
			'label'		=> __( 'Required Mark', 'awelementor' ),
			'type'		=> Controls_Manager::SWITCHER,
			'default'	=> 'yes',
			'label_off'	=> __( 'Hide', 'awelementor' ),
			'label_on'	=> __( 'Show', 'awelementor' ),
			'condition'	=> [
				'show_label'	=> 'yes',
			],
		]);
		$this->add_control('required_mark_display', [
			'type'		=> Controls_Manager::HIDDEN,
			'default'	=> 'none', // a non-empty value is required here otherwise this does not work
			'selectors' => [
				'{{WRAPPER}} .acf-label .acf-required' => 'display: {{VALUE}}',
			],
			'condition'	=> [
				'show_required_mark!'	=> 'yes',
			],
		]);

		/* (string) Whether to use the WP uploader or a basic input for image and file fields. Defaults to 'wp' */
		$this->add_control('uploader', [
			'label'		=> __( 'Uploader', 'awelementor' ),
			'type'		=> Controls_Manager::SELECT,
			'options'	=> [
				'wp'	=> __( 'WordPress Uploader', 'awelementor' ),
				'basic'	=> __( 'HTML File Input', 'awelementor' ),
			],
			'default' => 'wp',
		]);

		$this->end_controls_section();

		$this->start_controls_section('submit_button_content_section', [
			'label'		=> __( 'Submit Button', 'awelementor' ),
			'tab'		=> Controls_Manager::TAB_CONTENT,
		]);
		/* (string) The text displayed on the submit button */
		$this->add_control('submit_value', [
			'label'			=> __( 'Label', 'awelementor' ),
			'type'			=> Controls_Manager::TEXT,
			'input_type'	=> 'text',
			'placeholder'	=> __( 'Submit', 'awelementor' ),
			'default'		=> __( 'Submit', 'awelementor' ),
		]);
		$this->end_controls_section();
	}

	private function register_style_controls() {
		$this->start_controls_section('field_label_style', [
			'label'		=> __( 'Field', 'awelementor' ),
			'tab'		=> Controls_Manager::TAB_STYLE,
		]);

		$this->add_control('field_padding', [
			'label'			=> __( 'Padding', 'awelementor' ),
			'type'			=> Controls_Manager::DIMENSIONS,
			'size_units'	=> [ 'px', 'em', '%' ],
			'selectors'		=> [
				'{{WRAPPER}} .acf-fields > .acf-field' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		]);

		/* (string) Determines element used to wrap a field. Defaults to 'div' */
		$this->add_control('field_el', [
			'label'		=> __( 'Field Tag', 'awelementor' ),
			'type'		=> Controls_Manager::SELECT,
			'options'	=> [
				'div'	=> 'div',
				'tr'	=> 'tr',
				'td'	=> 'td',
				'ul'	=> 'ul',
				'ol'	=> 'ol',
				'dl'	=> 'dl',
			],
			'default' => 'div',
		]);
		$this->add_control('field_label', [
			'label'		=> __( 'Label', 'awelementor' ),
			'type'		=> Controls_Manager::HEADING,
			'separator' => 'before',
			'condition'	=> [
				'show_label'	=> 'yes',
			],
		]);
		/* (string) Determines where field labels are places in relation to fields. Defaults to 'top'. */
		$this->add_control('label_placement', [
			'label'		=> __( 'Placement', 'awelementor' ),
			'type'		=> Controls_Manager::SELECT,
			'options'	=> [
				''		=> __( 'Default', 'awelementor' ),
				'top'	=> __( 'Top aligned', 'awelementor' ),
				'left'	=> __( 'Left aligned', 'awelementor' ),
			],
			'condition'	=> [
				'show_label'	=> 'yes',
			],
		]);
		$this->add_responsive_control('label_alignment', [
			'label'		=> __( 'Alignment', 'awelementor' ),
			'type'		=> Controls_Manager::CHOOSE,
			'options'	=> [
				'left'	=> [
					'title'	=> __( 'Left', 'awelementor' ),
					'icon'	=> 'fa fa-align-left',
				],
				'center'	=> [
					'title'	=> __( 'Center', 'awelementor' ),
					'icon'	=> 'fa fa-align-center',
				],
				'right'		=> [
					'title'	=> __( 'Right', 'awelementor' ),
					'icon'	=> 'fa fa-align-right',
				],
			],
			'selectors' => [
				'{{WRAPPER}} .acf-label > label' => 'text-align: {{VALUE}}',
			],
			'condition'	=> [
				'show_label'	=> 'yes',
			],
		]);
		$this->add_control('field_label_color', [
			'label'		=> __( 'Color', 'awelementor' ),
			'type'		=> Controls_Manager::COLOR,
			'default'	=> '',
			'selectors'	=> [
				'{{WRAPPER}} .acf-label > label'	=> 'color: {{COLOR}};',
			],
			'condition'	=> [
				'show_label'	=> 'yes',
			],
		]);
		$this->add_group_control(Group_Control_Typography::get_type(), [
			'name'		=> 'field_label_typography',
			'scheme'	=> Scheme_Typography::TYPOGRAPHY_1,
			'selector'	=> '{{WRAPPER}} .acf-label > label',
			'condition'	=> [
				'show_label'	=> 'yes',
			],
		]);

		$this->add_control('field_required_mark_label', [
			'label'		=> __( 'Required Mark', 'awelementor' ),
			'type'		=> Controls_Manager::HEADING,
			'separator' => 'before',
			'condition'	=> [
				'show_label'			=> 'yes',
				'show_required_mark'	=> 'yes',
			],
		]);
		$this->add_control('mark_required_color', [
			'label'		=> __( 'Color', 'awelementor' ),
			'type'		=> Controls_Manager::COLOR,
			'default'	=> '',
			'selectors'	=> [
				'{{WRAPPER}} .acf-required'	=> 'color: {{COLOR}};',
			],
			'condition'	=> [
				'show_label'			=> 'yes',
				'show_required_mark'	=> 'yes',
			],
		]);

		$this->add_control('field_description_label', [
			'label'		=> __( 'Description (Instructions)', 'awelementor' ),
			'type'		=> Controls_Manager::HEADING,
			'separator' => 'before',
		]);
		/* (string) Determines where field instructions are placed in relation to fields. Defaults to 'label'. */
		$this->add_control('instruction_placement', [
			'label'		=> __( 'Placement', 'awelementor' ),
			'type'		=> Controls_Manager::SELECT,
			'options'	=> [
				''		=> __( 'Default', 'awelementor' ),
				'label'	=> __( 'Below labels', 'awelementor' ),
				'field'	=> __( 'Below fields', 'awelementor' ),
			],
		]);
		$this->add_control('field_description_color', [
			'label'		=> __( 'Color', 'awelementor' ),
			'type'		=> Controls_Manager::COLOR,
			'default'	=> '',
			'selectors'	=> [
				'{{WRAPPER}} .acf-label > .description'	=> 'color: {{COLOR}};',
			],
		]);
		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'		=> 'field_description_typography',
			'scheme'	=> Scheme_Typography::TYPOGRAPHY_1,
			'selector'	=> '{{WRAPPER}} .acf-label > .description',
		]);

		$this->end_controls_section();

		$in_input_selectors = [
			'{{WRAPPER}} .acf-field input[type="text"]',
			'{{WRAPPER}} .acf-field input[type="password"]',
			'{{WRAPPER}} .acf-field input[type="number"]',
			'{{WRAPPER}} .acf-field input[type="search"]:not(.select2-search__field)',
			'{{WRAPPER}} .acf-field input[type="email"]',
			'{{WRAPPER}} .acf-field input[type="url"]',
			'{{WRAPPER}} .acf-field select',
			// extra
			'{{WRAPPER}} .acf-field .select2 > .selection > .select2-selection',
			//'{{WRAPPER}} .acf-field .select2 .select2-selection__rendered ui-sortable', // does not work
			'{{WRAPPER}} .acf-field textarea',
		];

		$border_input_selectors = [
			'{{WRAPPER}} .acf-field input[type="text"]',
			'{{WRAPPER}} .acf-field input[type="password"]',
			'{{WRAPPER}} .acf-field input[type="number"]',
			'{{WRAPPER}} .acf-field input[type="search"]:not(.select2-search__field)',
			'{{WRAPPER}} .acf-field input[type="email"]',
			'{{WRAPPER}} .acf-field input[type="url"]',
			'{{WRAPPER}} .acf-field select',

			// WP Editor TextArea
			'{{WRAPPER}} .wp-editor-container',
			// Select2
			'{{WRAPPER}} .select2-container--default .select2-selection--multiple',
		];
		//$input_selector = implode(',', $input_selectors);

		$this->start_controls_section('section_input_style', [
			'label'	=> __('Input', 'awelementor'),
			'tab'	=> Controls_Manager::TAB_STYLE,
		]);
//		$this->start_controls_tab('tab_button_text', [
//			'label' => __( 'Text', 'awelementor' ),
//		]);
		$this->add_responsive_control('input_padding', [
			'label'			=> __( 'Text Padding', 'awelementor' ),
			'type'			=> Controls_Manager::DIMENSIONS,
			'size_units'	=> [ 'px', '%' ],
			'selectors'		=> [
				implode(',', [
					'{{WRAPPER}} .acf-field input[type="text"]',
					'{{WRAPPER}} .acf-field input[type="password"]',
					'{{WRAPPER}} .acf-field input[type="number"]',
					'{{WRAPPER}} .acf-field input[type="search"]:not(.select2-search__field)',
					'{{WRAPPER}} .acf-field input[type="email"]',
					'{{WRAPPER}} .acf-field input[type="url"]',
					'{{WRAPPER}} .acf-field textarea',
					'{{WRAPPER}} .acf-field .select2-selection__rendered',
				]) => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		]);
		$this->add_group_control(Group_Control_Typography::get_type(), [
			'name'		=> 'input_typography',
			'scheme'	=> Scheme_Typography::TYPOGRAPHY_1,
			'selector'	=> implode(',', [
				'{{WRAPPER}} .acf-field input[type="text"]',
				'{{WRAPPER}} .acf-field input[type="password"]',
				'{{WRAPPER}} .acf-field input[type="number"]',
				'{{WRAPPER}} .acf-field input[type="search"]',
				'{{WRAPPER}} .acf-field input[type="email"]',
				'{{WRAPPER}} .acf-field input[type="url"]',
				'{{WRAPPER}} .acf-field select',
				'{{WRAPPER}} .acf-field textarea',
			]),
		]);
		$this->add_control('input_text_color', [
			'label'		=> __( 'Text Color', 'awelementor' ),
			'type'		=> Controls_Manager::COLOR,
			'selectors'	=> [
				implode(',', [
					'{{WRAPPER}} .acf-field input[type="text"]',
					'{{WRAPPER}} .acf-field input[type="password"]',
					'{{WRAPPER}} .acf-field input[type="number"]',
					'{{WRAPPER}} .acf-field input[type="search"]',
					'{{WRAPPER}} .acf-field input[type="email"]',
					'{{WRAPPER}} .acf-field input[type="url"]',
					'{{WRAPPER}} .acf-field select',
					'{{WRAPPER}} .acf-field textarea',
				]) => 'color: {{COLOR}};',
			],
		]);
		$this->add_control('input_background_color', [
			'label'		=> __( 'Background Color', 'awelementor' ),
			'type'		=> Controls_Manager::COLOR,
			'selectors'	=> [
				implode(',', $in_input_selectors)	=> 'background-color: {{VALUE}};',
			],
		]);
		$this->add_control('input_min_height', [
			'label'			=> __( 'Min Height', 'awelementor' ),
			'type'			=> Controls_Manager::SLIDER,
			'default'		=> [
				'size'		=> 47,
			],
			'range'			=> [
				'px'		=> [
					'min'	=> 1,
					'max'	=> 100,
				],
			],
			'selectors'		=> [
				implode(',', $in_input_selectors) => 'min-height: {{SIZE}}{{UNIT}};',
			],
		]);

		$this->add_control('input_border_heading', [
			'label'		=> __( 'Border', 'awelementor' ),
			'type'		=> Controls_Manager::HEADING,
			'separator' => 'before',
		]);
		$this->add_group_control(Group_Control_Border::get_type(), [
			'name'		=> 'input_border',
			'selector'	=> implode(',', $border_input_selectors),
			// Focused: '.select2-container--default.select2-container--focus .select2-selection--multiple'
		]);
		//$input_selector = '{{WRAPPER}} .acf-field input[type="text"], {{WRAPPER}} .acf-field input[type="password"], {{WRAPPER}} .acf-field input[type="number"], {{WRAPPER}} .acf-field input[type="search"], {{WRAPPER}} .acf-field input[type="email"], {{WRAPPER}} .acf-field input[type="url"], {{WRAPPER}} .acf-field textarea, {{WRAPPER}} .acf-field select';
		$this->add_control('input_border_radius', [
			'label'			=> __( 'Border Radius', 'awelementor' ),
			'type'			=> Controls_Manager::DIMENSIONS,
			'size_units'	=> [ 'px', '%' ],
			'selectors'		=> [
				implode(',', $border_input_selectors)	=> 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				'{{WRAPPER}} .acf-field textarea.wp-editor-area' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		]);
//		$this->end_controls_tab();
		$this->end_controls_section();

		$this->start_controls_section('section_separator', [
			'label' => __( 'Separator', 'awelementor' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		]);
		/*
		// We will not do gaps because they introduce all kinds of complexity.
		// The desired functionality can be better achieved using padding/margins.
		$this->add_responsive_control('field_separator_gap_horizontal', [
			'label'		=> __( 'Horizontal Gap', 'awelementor' ),
			'type'		=> Controls_Manager::SLIDER,
			'range'		=> [
				'px'	=> [
					'min'	=> 0,
					'max'	=> 500,
				],
			],
			'selectors'	=> [
				// This is the only way to do it for all fields without messing up the alignments in any way
				'{{WRAPPER}} .acf-form > .acf-fields > .acf-field:not(:last-child)' => 'padding-bottom: {{SIZE}}{{UNIT}};',
			],
		]);
		$this->add_responsive_control('field_separator_gap_vertical', [
			'label'		=> __( 'Vertical Gap', 'awelementor' ),
			'type'		=> Controls_Manager::SLIDER,
			'range'		=> [
				'px'	=> [
					'min'	=> 0,
					'max'	=> 500,
				],
			],
			'selectors'	=> [
				'{{WRAPPER}} .acf-form > .acf-fields > .acf-field[data-width] + .acf-field[data-width]:not([data-width]:last-child)' => 'padding-right: {{SIZE}}{{UNIT}};',
			],
		]);
		*/

		// ACF-drawn inner borders
		$this->add_control('field_separator_heading_h', [
			'label'		=> __( 'Horizontal', 'awelementor' ),
			'type'		=> Controls_Manager::HEADING,
		]);
		$this->add_control('field_separator_color_h', [
			'label'		=> __( 'Color', 'awelementor' ),
			'type'		=> Controls_Manager::COLOR,
			'selectors'	=> [
				'{{WRAPPER}} .acf-field:not(:first-child)'	=> 'border-top-color: {{COLOR}};',
			],
		]);
		$this->add_control('field_separator_style_h', [
			'type'		=> 'awe-border-style',
			'selectors'	=> [
				'{{WRAPPER}} .acf-field:not(:first-child)' => 'border-top-style: {{VALUE}};',
			],
		]);
		$this->add_control('field_separator_width_h', [
			'label'		=> __( 'Width', 'awelementor' ),
			'type'		=> Controls_Manager::SLIDER,
			'default'	=> [
				'size'	=> 1,
			],
			'range'		=> [
				'px'	=> [
					'min'	=> 0,
					'max'	=> 100,
				],
			],
			'selectors' => [
				'{{WRAPPER}} .acf-field:not(:first-child)' => 'border-top-width: {{SIZE}}{{UNIT}};',
			],
		]);
		$this->add_control('field_separator_heading_v', [
			'label'		=> __( 'Vertical', 'awelementor' ),
			'type'		=> Controls_Manager::HEADING,
			'separator'	=> 'before',
		]);
		$this->add_control('field_separator_color_v', [
			'label' => __('Color', 'awelementor'),
			'type' => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .acf-field[data-width] + .acf-field[data-width]' => 'border-left-color: {{COLOR}};',
			],
		]);
		$this->add_control('field_separator_style_v', [
			'type' => 'awe-border-style',
			'selectors' => [
				'{{WRAPPER}} .acf-field[data-width] + .acf-field[data-width]' => 'border-left-style: {{VALUE}};',
			],
		]);
		$this->add_control('field_separator_width_v', [
			'label' => __('Width', 'awelementor'),
			'type' => Controls_Manager::SLIDER,
			'default' => [
				'size' => 1,
			],
			'range' => [
				'px' => [
					'min' => 0,
					'max' => 100,
				],
			],
			'selectors' => [
				'{{WRAPPER}} .acf-field[data-width] + .acf-field[data-width]' => 'border-left-width: {{SIZE}}{{UNIT}};',
			],
		]);
		$this->end_controls_section();

		$this->start_controls_section('section_submit_button_style', [
			'label' => __( 'Submit Button', 'awelementor' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		]);
		
		$this->start_controls_tabs('tabs_submit_button_style');
		$this->start_controls_tab('tab_submit_button_normal', [
			'label'		=> __( 'Normal', 'awelementor' ),
		]);
		$this->add_group_control(Group_Control_Typography::get_type(), [
			'name'		=> 'submit_button_typography',
			'scheme'	=> Scheme_Typography::TYPOGRAPHY_1,
			'selector'	=> '{{WRAPPER}} input[type=submit]',
		]);
		$this->add_control('submit_button_background_color', [
			'label'		=> __( 'Background Color', 'awelementor' ),
			'type'		=> Controls_Manager::COLOR,
			'default'	=> '',
			'selectors'	=> [
				'{{WRAPPER}} .acf-form-submit > .acf-button'	=> 'background-color: {{COLOR}};',
			],
		]);
		$this->add_group_control(Group_Control_Border::get_type(), [
			'name'		=> 'submit_button_border',
			'selector'	=> '{{WRAPPER}} .acf-form-submit > .acf-button',
		]);
		$this->add_control('submit_button_border_radius', [
			'label'			=> __( 'Border Radius', 'awelementor' ),
			'type'			=> Controls_Manager::DIMENSIONS,
			'size_units'	=> [ 'px', '%' ],
			'selectors'		=> [
				'{{WRAPPER}} .acf-form-submit > .acf-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		]);
		$this->end_controls_tab();
		$this->start_controls_tab('tab_submit_button_hover', [
			'label'		=> __( 'Hover', 'awelementor' ),
		]);
		$this->add_group_control(Group_Control_Typography::get_type(), [
			'name'		=> 'submit_button_typography_hover',
			'scheme'	=> Scheme_Typography::TYPOGRAPHY_1,
			'selector'	=> '{{WRAPPER}} input[type=submit]:hover',
		]);
		$this->add_control('submit_button_background_color_hover', [
			'label'		=> __( 'Background Color', 'awelementor' ),
			'type'		=> Controls_Manager::COLOR,
			'default'	=> '',
			'selectors'	=> [
				'{{WRAPPER}} .acf-form-submit > .acf-button:hover'	=> 'background-color: {{COLOR}};',
			],
		]);
		$this->add_group_control(Group_Control_Border::get_type(), [
			'name'		=> 'submit_button_border_hover',
			'selector'	=> '{{WRAPPER}} .acf-form-submit > .acf-button:hover',
		]);
		$this->add_control('submit_button_border_radius_hover', [
			'label'			=> __( 'Border Radius', 'awelementor' ),
			'type'			=> Controls_Manager::DIMENSIONS,
			'size_units'	=> [ 'px', '%' ],
			'selectors'		=> [
				'{{WRAPPER}} .acf-form-submit > .acf-button:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		]);
		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_responsive_control('submit_button_width', [
			'label'		=> __( 'Width', 'awelementor' ),
			'type'		=> 'awe-width',
			'separator'	=> 'before',
			'selectors'	=> [
				'{{WRAPPER}} .acf-form-submit > .acf-button' => 'width: {{VALUE}}%',
			],
		]);
		$this->add_responsive_control('submit_button_alignment', [
			'label'		=> __( 'Alignment', 'awelementor' ),
			'type'		=> Controls_Manager::CHOOSE,
			'options'	=> [
				'left'	=> [
					'title'	=> __( 'Left', 'awelementor' ),
					'icon'	=> 'fa fa-align-left',
				],
				'center'	=> [
					'title'	=> __( 'Center', 'awelementor' ),
					'icon'	=> 'fa fa-align-center',
				],
				'right'		=> [
					'title'	=> __( 'Right', 'awelementor' ),
					'icon'	=> 'fa fa-align-right',
				],
			],
			'selectors' => [
				'{{WRAPPER}} .acf-form-submit' => 'text-align: {{VALUE}}',
			],
		]);

		$this->add_responsive_control('submit_button_margin', [
			'label'			=> __( 'Margin', 'awelementor' ),
			'type'			=> Controls_Manager::DIMENSIONS,
			'size_units'	=> [ 'px', '%' ],
			'selectors'		=> [
				'{{WRAPPER}} .acf-form .acf-form-submit' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		]);

		$this->add_responsive_control('submit_button_padding', [
			'label'			=> __( 'Padding', 'awelementor' ),
			'type'			=> Controls_Manager::DIMENSIONS,
			'size_units'	=> [ 'px', '%' ],
			'selectors'		=> [
				'{{WRAPPER}} .acf-form .acf-form-submit .acf-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		]);

		/* (string) HTML used to render the submit button. Added in v5.5.10 */
		$this->add_control('custom_html_button', [
			'label'			=> __( 'Custom HTML', 'awelementor' ),
			'type'			=> Controls_Manager::SWITCHER,
			'default'		=> 'off',
			'label_off'		=> __( 'Off', 'awelementor' ),
			'label_on'		=> __( 'On', 'awelementor' ),
			'separator'		=> 'before',
		]);
		$this->add_control('html_submit_button', [
			'type'			=> Controls_Manager::CODE,
			'label'			=> __( 'Submit Button', 'awelementor' ),
			'language'		=> 'html',
			'render_type'	=> 'ui',
			'show_label'	=> false,
			'separator'		=> 'none',
			'default'		=> '<input type="submit" class="acf-button button button-primary button-large" value="%s" />',
			'condition'		=> [
				'custom_html_button'	=> 'yes',
			],
		]);
		$this->add_control('html_submit_button_description', [
			'raw'				=> __( 'HTML used to render the submit button.', 'awelementor' ),
			'type'				=> Controls_Manager::RAW_HTML,
			'content_classes'	=> 'elementor-descriptor',
			'condition'			=> [
				'custom_html_button'	=> 'yes',
			],
		]);
		$this->end_controls_section();

		$this->start_controls_section('spinner_style', [
			'label' => __( 'Spinner', 'awelementor' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		]);

		$this->add_control('custom_submit_spinner', [
			'label'		=> __( 'Custom HTML', 'awelementor' ),
			'type'		=> Controls_Manager::SWITCHER,
			'default'	=> 'no',
			'label_off'	=> __( 'Off', 'awelementor' ),
			'label_on'	=> __( 'On', 'awelementor' ),
		]);
		/* (string) HTML used to render the submit button loading spinner. Added in v5.5.10 */
		$this->add_control('html_submit_spinner', [
			'type'			=> Controls_Manager::CODE,
			'label'			=> __( 'HTML', 'awelementor' ),
			'language'		=> 'html',
			'render_type'	=> 'ui',
			'show_label'	=> false,
			'separator'		=> 'none',
			'default'		=> '<span class="acf-spinner"></span>',
			'condition'		=> [
				'custom_submit_spinner'	=> 'yes',
			],
		]);
		$this->end_controls_section();
	}

	/**
	 * @param array $array
	 */
	private function normalize_booleans(array &$array) {
		foreach ($array as $key => $value) {
			if ($value === 'yes') {
				$args[$key] = true;
			} elseif ($value === 'no') {
				$args[$key] = false;
			}
		}
	}

	/**
	 * @param string $string
	 * @param mixed $default
	 * @return boolean|mixed
	 */
	private function yesno_to_bool($string, $default = null) {
		if (in_array($string, array('yes', 'true', 'on'), true)) {
			return true;
		} elseif (in_array($string, array('no', 'false', 'off'), true)) {
			return false;
		} else {
			return $default;
		}
	}

	/**
	 * @param array $settings
	 * @return array
	 */
	private function get_acf_new_post($settings = array()) {
		$new_post = array();

		if (!empty($settings['post_type'])) {
			$new_post['post_type'] = $settings['post_type'];
		}

		if (!empty($settings['post_status'])) {
			$new_post['post_status'] = $settings['post_status'];
		}

		if (!empty($settings['post_title'])) {
			$new_post['post_title'] = $settings['post_title'];
		}

		if (!empty($settings['post_name'])) { // Slug
			$new_post['post_name'] = $settings['post_name'];
		}

		if (!empty($settings['post_content'])) {
			$new_post['post_content'] = $settings['post_content'];
		}

		if (!empty($settings['post_parent'])) {
			$new_post['post_parent'] = (int) $settings['post_parent'];
		}

		if (!empty($settings['tax_input'])) {
			foreach ($settings['tax_input'] as $taxonomy) {
				if (isset($taxonomy['taxonomy'], $taxonomy['value'])) {
					$new_post['tax_input'][$taxonomy['taxonomy']] = $taxonomy['value'];
				}
			}
		}

		if (!empty($settings['meta_input'])) {
			foreach ($settings['meta_input'] as $meta) {
				if (isset($meta['key'], $meta['value'])) {
					$new_post['meta_input'][$meta['key']] = $meta['value'];
				}
			}
		}

		return $new_post;
	}

	/**
	 * @return array
	 */
	private function get_acf_form_args() {
		$args = [];
		$settings = $this->get_settings_for_display();

		if (isset($settings['acf_id'])) {
			if (!empty($args['acf_id'])) {
				$args['id'] = $args['acf_id'];
			}
		} else {
			$args['id'] = $this->generate_acf_form_id();
		}

		if (!empty($settings['form'])) {
			if (($bool = $this->yesno_to_bool($settings['form'])) !== null) {
				$args['form'] = $bool;
			}
		}

		if (!empty($settings['show_post_title'])) {
			if (($bool = $this->yesno_to_bool($settings['show_post_title'])) !== null) {
				$args['post_title'] = $bool;
			}
		}

		if (!empty($settings['show_post_content'])) {
			if (($bool = $this->yesno_to_bool($settings['show_post_content'])) !== null) {
				$args['post_content'] = $bool;
			}
		}

		if (!empty($settings['honeypot'])) {
			if (($bool = $this->yesno_to_bool($settings['honeypot'])) !== null) {
				$args['honeypot'] = $bool;
			}
		}

		if (!empty($settings['kses'])) {
			if (($bool = $this->yesno_to_bool($settings['kses'])) !== null) {
				$args['kses'] = $bool;
			}
		}

		// ACF 'new_post' can be FALSE or ARRAY
		// ACF 'post_id' can be int/string or 'new_post'
		if ($this->yesno_to_bool($settings['new_post'])) {
			$args['post_id'] = 'new_post';

			if (!empty(($new_post = $this->get_acf_new_post($settings)))) {
				$args['new_post'] = $new_post;
			}
		} else {
			if (!empty($settings['post_id'])) {
				$args['post_id'] = $settings['post_id'];
			}
		}

		if (!empty($settings['show_updated_message'])) {
			$show_updated_message = $this->yesno_to_bool($settings['show_updated_message']);
			if ($show_updated_message === true) {

				// Set the updated message
				if (!empty($settings['updated_message'])) {
					$args['updated_message'] = $settings['updated_message'];
				}

				// Set the custom html for updated message
				if (!empty($settings['html_updated_message'])) {
					$args['html_updated_message'] = $settings['html_updated_message'];
				}

			} elseif ($show_updated_message === false) {
				// Disable updated message altogether.
				$args['updated_message'] = false;
			}
		}

		if (!empty($args['form_attributes'])) {
			if (($form_attributes = $this->parse_keyvalue_rows($args['form_attributes'])) !== null) {
				$args['form_attributes'] = $form_attributes;
			}
		}

		if (!empty($settings['uploader'])) {
			$args['uploader'] = $settings['uploader'];
		}

		if (!empty($settings['field_el'])) {
			$args['field_el'] = $settings['field_el'];
		}

		if (!empty($settings['label_placement'])) {
			$args['label_placement'] = $settings['label_placement'];
		}

		if (!empty($settings['instruction_placement'])) {
			$args['instruction_placement'] = $settings['instruction_placement'];
		}

		if (!empty($settings['submit_value'])) {
			$args['submit_value'] = $settings['submit_value'];
		}

		if (!empty($settings['html_before_fields'])) {
			$args['html_before_fields'] = $settings['html_before_fields'];
		}

		if (!empty($settings['html_after_fields'])) {
			$args['html_after_fields'] = $settings['html_after_fields'];
		}

		if (!empty($settings['html_submit_button'])) {
			$args['html_submit_button'] = $settings['html_submit_button'];
		}

		if (!empty($settings['html_submit_spinner'])) {
			//$args['html_submit_spinner'] = '<span class="acf-spinner"></span>';
			$args['html_submit_spinner'] = $settings['html_submit_spinner'];
		}

		if (!empty($settings['return'])) {
			$args['return'] = $settings['return'];
		}

		return $args;
	}

	/**
	 * @return string
	 */
	private function generate_acf_form_id() {
		return 'acf-form-post-'.get_the_ID();
	}

	/**
	 * Convert 'key|value' rows notation into a k-v array.
	 *
	 * @param string $text
	 * @return array|null
	 */
	private function parse_keyvalue_rows($text) {
		$matches = [];
		$n_matches = preg_match_all('/^(\w+)\|(\w+)$/m', $text, $matches);
		if ($n_matches >= 1) {
			$form_attributes = [];
			for ($i = 0; $i < $n_matches; $i++) {
				$form_attributes[$matches[1][$i]] = $matches[2][$i];
			}
			return $form_attributes;
		} else {
			return null;
		}
	}

	/**
	 * Render ACF Form output on the frontend.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {
		echo '<div class="elementor-acf-form">';
		if ( function_exists( 'acf_form' ) ) {
			acf_form($this->get_acf_form_args());
		} else {
			echo sprintf(
				__('Please install and activate the %sAdvanced Custom Fields%s plugin.', 'awelementor' ),
				'<a href="https://wordpress.org/plugins/advanced-custom-fields/">',
				'</a>'
			);
		}
		echo '</div>';
	}

}