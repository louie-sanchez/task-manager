<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://netwarriorservices.com
 * @since      1.0.0
 *
 * @package    Task_Manager
 * @subpackage Task_Manager/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Task_Manager
 * @subpackage Task_Manager/includes
 * @author     Louie Sanchez <netwarriorservices@gmail.com>
 */
class Task_Manager {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Task_Manager_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $task_manager    The string used to uniquely identify this plugin.
	 */
	protected $task_manager;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->task_manager = 'task-manager';
		$this->version = '3.5.2';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Task_Manager_Loader. Orchestrates the hooks of the plugin.
	 * - Task_Manager_i18n. Defines internationalization functionality.
	 * - Task_Manager_Admin. Defines all hooks for the admin area.
	 * - Task_Manager_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/task-manager-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/task-manager-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/task-manager-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/task-manager-public.php';

		$this->loader = new Task_Manager_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Task_Manager_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Task_Manager_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Task_Manager_Admin( $this->get_task_manager(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		// Admin Menus
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'task_manager_tasks_page' );

		// Dashboard Widget
		$this->loader->add_action( 'wp_dashboard_setup', $plugin_admin, 'tmp_dashboard_widgets' );

		// Option Setting Page
        $this->loader->add_action( 'admin_menu', $plugin_admin, 'add_admin_menu' );
        $this->loader->add_action( 'admin_init', $plugin_admin, 'settings_api_init' );


		$this->loader->add_action( 'widgets_init', $plugin_admin, 'tmp_widgets' );


		$this->loader->add_action('admin_menu', $plugin_admin, 'tmp_modify_admin_menu_badge');

		$this->loader->add_action('wp_ajax_export_task_csv', $plugin_admin, 'export_task_csv');
		$this->loader->add_action('wp_ajax_import_task_csv', $plugin_admin, 'import_task_csv');

		$this->loader->add_action('wp_ajax_export_ticket_csv', $plugin_admin, 'export_ticket_csv');

		$this->loader->add_action('wp_ajax_export_project_csv', $plugin_admin, 'export_project_csv');
		$this->loader->add_action('wp_ajax_duplicate_project', $plugin_admin, 'duplicate_project');
		$this->loader->add_action('wp_ajax_duplicate_task', $plugin_admin, 'duplicate_task');

		/** Create New Ticket With Ajax call */
		$this->loader->add_action('wp_ajax_add_new_ticket', $plugin_admin, 'add_new_ticket');

		/** PHP Mailer Function */
//        $this->loader->add_action('phpmailer_init', $plugin_admin, 'wpse8170_phpmailer_init');

        /** Custom Navigation Menu */
        $this->loader->add_action('init', $plugin_admin, 'tmp_top_nav_menu');


	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Task_Manager_Public( $this->get_task_manager(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		$this->loader->add_shortcode('tmp', $plugin_public, 'tmp_shortcodes');

		$this->loader->add_shortcode('tmp-ticket-new', $plugin_public, 'tmp_new_ticket_shortcode');


	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_task_manager() {
		return $this->task_manager;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Task_Manager_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
