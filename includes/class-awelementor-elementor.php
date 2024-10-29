<?php

/**
 * Elementor Handler Class
 *
 * This class loads our Advanced Elementor widgets.
 *
 * @since      1.0.0
 * @package    AWElementor
 * @subpackage AWElementor/includes
 * @author     Mae Company <wordpress@mae.company>
 */
class AWElementor_Elementor {

	/**
	 * @var array
	 */
	private $widgets = [];

	/**
	 * @var array
	 */
	private $controls = [];

	/**
	 * @var array
	 */
	private $categories = [];

	/**
	 * Class constructor.
	 *
	 * Configures the default widgets.
	 */
	public function __construct() {
		$control_deps = [
			'Elementor\Controls_Manager',
			'Elementor\Core\DynamicTags\Data_Tag',
		];
		$widget_deps = [
			'Elementor\Widget_Base',
			'Elementor\Element_Base',
		];

		$this->add_category('advanced', __( 'Advanced', 'awelementor' ));

		$this->add_control('awe-acf-subfield', 'AWElementor_ACF_Subfield', $control_deps);
		$this->add_control('awe-post-type', 'AWElementor_Post_Type', $control_deps);
		$this->add_control('awe-post-status', 'AWElementor_Post_Status', $control_deps);
		$this->add_control('awe-width', 'AWElementor_Width', $control_deps);
		$this->add_control('awe-border-style', 'AWElementor_Border_Style', $control_deps);

		$this->add_widget('Widget_ACF_Form', ['acf-form.php'], $widget_deps);
		$this->add_widget('Widget_Advanced_Posts', ['advanced-posts.php'], $widget_deps);
	}

	/**
	 * @since 1.0.3
	 * @return Elementor
	 */
	private static function elementor() {
		return \Elementor\Plugin::instance();
	}

	/**
	 * Register Elementor categories.
	 *
	 * @since 1.0.0
	 */
	public function register_categories() {
		if ( defined( 'ELEMENTOR_PATH' ) ) {
			$elements_manager = self::elementor()->elements_manager;

			foreach ($this->categories as $id => $category) {
				$elements_manager->add_category($id, $category);
			}
		}
	}

	/**
	 * Register Elementor controls.
	 *
	 * @since 1.0.3
	 */
	public function register_controls() {
		if ( defined( 'ELEMENTOR_PATH' ) ) {
			$controls_manager = self::elementor()->controls_manager;

			foreach ($this->controls as $id => $control) {
				foreach ($control['dependencies'] as $dependencyClass) {
					class_exists($dependencyClass);
				}

				require_once implode(DIRECTORY_SEPARATOR, [__DIR__, 'controls', "{$id}.php"]);
				$control_class = $control['class'];
				$controls_manager->register_control( $id, new $control_class() );
			}
		}
	}

	/**
	 * Register Elementor widgets.
	 *
	 * @since 1.0.0
	 */
	public function register_widgets() {
		if ( defined( 'ELEMENTOR_PATH' ) ) {
			$widgets_manager = self::elementor()->widgets_manager;

			foreach ($this->widgets as $widget) {
				$this->register_widget( $widgets_manager, $widget['class'], $widget['files'], $widget['dependencies'] );
			}
		}
	}

	/**
	 * @param \Elementor\Widgets_Manager $widgets_manager
	 * @param string $class
	 * @param string|array $files
	 * @param array $dependencies
	 */
	private function register_widget($widgets_manager, $class, array $files, array $dependencies = []) {
		foreach ($files as $file) {
			require_once __DIR__ . '/widgets/' . $file;
		}

		if (class_exists($class)) {
			foreach ($dependencies as $dependencyClass) {
				class_exists($dependencyClass);
			}

			$widgets_manager->register_widget_type(new $class());
		}
	}

	/**
	 * @param string $class
	 * @param array $files
	 * @param array $dependencies
	 */
	public function add_widget( $class, $files, array $dependencies = [] ) {
		$this->widgets[] = array(
			'class'			=> $class,
			'files'			=> $files,
			'dependencies'	=> $dependencies,
		);
	}

	public function add_control( $id, $class, array $dependencies = [] ) {
		$this->controls[$id] = [
			'id'			=> $id,
			'class'			=> $class,
			'dependencies'	=> $dependencies,
		];
	}

	public function add_category( $id, $title ) {
		$this->categories[$id] = [
			'id'	=> $id,
			'title'	=> $title,
		];
	}

}
