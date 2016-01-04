<?php
/**
 * Diagnosis.
 *
 * @package           Diagnosis
 * @author            Gary Jones <gamajo@gamajo.com>
 * @link              http://gamajo.com/
 * @copyright         2014 Gary Jones, Gamajo Tech
 * @license           GPL-2.0+
 *
 * @wordpress-plugin
 * Plugin Name:       Diagnosis
 * Plugin URI:        http://github.com/GaryJones/diagnosis
 * Description:       Adds pages to the Dashboard menu with technical details about PHP, MySQL and other server details.
 * Version:           3.0.0
 * Author:            Gary Jones
 * Author URI:        http://gamajo.com/
 * Text Domain:       diagnosis
 * Domain Path:       /languages
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * GitHub Plugin URI: https://github.com/GaryJones/diagnosis
 * GitHub Branch:     master
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Create a helper function for easy SDK access.
function dia_fs() {
	global $dia_fs;

	if ( ! isset( $dia_fs ) ) {
		// Include Freemius SDK.
		require_once dirname( __FILE__ ) . '/freemius/start.php';

		$dia_fs = fs_dynamic_init( array(
			'id'             => '109',
			'slug'           => 'diagnosis',
			'public_key'     => 'pk_974f23676469e12ac6bdfe5fdb91b',
			'is_premium'     => false,
			'has_addons'     => false,
			'has_paid_plans' => false,
			'menu'           => array(
				'slug'    => 'diagnosis',
				'account' => false,
				'contact' => false,
				'support' => false,
				'parent'  => array(
					'slug' => 'index.php',
				)
			)
		) );
	}

	return $dia_fs;
}

// Init Freemius.
dia_fs();

require_once plugin_dir_path( __FILE__ ) . 'includes/class-diagnosis.php';

// Register hooks that are fired when the plugin is activated, deactivated, and uninstalled, respectively.
// register_activation_hook( __FILE__, array( 'Diagnosis', 'activate' ) );
// register_deactivation_hook( __FILE__, array( 'Diagnosis', 'deactivate' ) );

$gmj_diagnosis = new Diagnosis;
$gmj_diagnosis->run();
