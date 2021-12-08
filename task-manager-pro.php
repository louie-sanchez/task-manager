<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://netwarriorservices.com
 * @since             3.5.0
 * @package           Task_Manager
 *
 * @wordpress-plugin
 * Plugin Name:       Task Manager
 * Plugin URI:        https://github.com/andrei-louie/task-manager
 * Description:       Task Manager is a Task Management Module/Tools for wordpress, where you can create, manage, assign user, update and delete different tasks. It has all features of Task Management Application.
 * Version:           3.5.2
 * Domain Path:       /languages
 * Requires at least: 4.0
 * Tags: task manager, tasks, manager
 * Tested up to: 5.5
 * Author:            Louie Sanchez
 * Author URI:        http://netwarriorservices.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       task-manager
 *
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/task-manager-activator.php
 */
function activate_task_manager() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/task-manager-activator.php';
	Task_Manager_Activator::activate();
	Task_Manager_Activator::createTables();
}



/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/task-manager-deactivator.php
 */
function deactivate_task_manager() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/task-manager-deactivator.php';
	Task_Manager_Deactivator::deactivate();
}


/**
 * The code that runs during plugin uninstall.
 * This action is documented in includes/task-manager-uninstall.php
 */
function uninstall_task_manager() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/task-manager-uninstall.php';
	Task_Manager_Uninstall::uninstall();
}

register_activation_hook( __FILE__, 'activate_task_manager' );
register_deactivation_hook( __FILE__, 'deactivate_task_manager' );
register_uninstall_hook( __FILE__, 'uninstall_task_manager' );



/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/task-manager.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_task_manager() {

	$plugin = new Task_Manager();
	$plugin->run();

}
run_task_manager();
