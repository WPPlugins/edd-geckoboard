<?php
/**
 * Plugin Name:     Easy Digital Downloads - Geckoboard
 * Plugin URI:      https://easydigitaldownloads.com/extensions/geckoboard/
 * Description:     Adds integration between EDD and Geckoboard
 * Version:         1.0.0
 * Author:          Daniel J Griffiths
 * Author URI:      http://section214.com
 * Text Domain:     edd-geckoboard
 *
 * @package         EDD\Geckoboard
 * @author          Daniel J Griffiths <dgriffiths@section214.com>
 */


// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}


if( ! class_exists( 'EDD_Geckoboard' ) ) {


	/**
	 * Main EDD_Geckoboard class
	 *
	 * @since       1.0.0
	 */
	class EDD_Geckoboard {


		/**
		 * @var         EDD_Geckoboard $instance The one true EDD_Geckoboard
		 * @since       1.0.0
		 */
		private static $instance;


		/**
		 * @var         object $api The EDD_Geckoboard_API object
		 * @since       1.0.0
		 */
		public $api;


		/**
		 * Get active instance
		 *
		 * @access      public
		 * @since       1.0.0
		 * @return      self::$instance The one true EDD_Geckoboard
		 */
		public static function instance() {
			if( ! self::$instance ) {
				self::$instance = new EDD_Geckoboard();
				self::$instance->setup_constants();
				self::$instance->includes();
				self::$instance->load_textdomain();
				self::$instance->hooks();
				self::$instance->api = new EDD_Geckoboard_API();
			}

			return self::$instance;
		}


		/**
		 * Setup plugin constants
		 *
		 * @access      private
		 * @since       1.0.0
		 * @return      void
		 */
		private function setup_constants() {
			// Plugin version
			define( 'EDD_GECKOBOARD_VER', '1.0.0' );

			// Plugin path
			define( 'EDD_GECKOBOARD_DIR', plugin_dir_path( __FILE__ ) );

			// Plugin URL
			define( 'EDD_GECKOBOARD_URL', plugin_dir_url( __FILE__ ) );
		}


		/**
		 * Include necessary files
		 *
		 * @access      private
		 * @since       1.0.0
		 * @return      void
		 */
		private function includes() {
			require_once EDD_GECKOBOARD_DIR . 'includes/admin/user/profile.php';
			require_once EDD_GECKOBOARD_DIR . 'includes/api/class.edd-geckoboard-api.php';
		}


		/**
		 * Run action and filter hooks
		 *
		 * @access      private
		 * @since       1.0.0
		 * @return      void
		 */
		private function hooks() {
			// Handle licensing
			if( class_exists( 'EDD_License' ) ) {
				$license = new EDD_License( __FILE__, 'Geckoboard', EDD_GECKOBOARD_VER, 'Daniel J Griffiths' );
			}
		}


		/**
		 * Internationalization
		 *
		 * @access      public
		 * @since       1.0.0
		 * @return      void
		 */
		public function load_textdomain() {
			// Set filter for language directory
			$lang_dir = dirname( plugin_basename( __FILE__ ) ) . '/languages/';
			$lang_dir = apply_filters( 'edd_geckoboard_language_directory', $lang_dir );

			// Traditional WordPress plugin locale filter
			$locale = apply_filters( 'plugin_locale', get_locale(), '' );
			$mofile = sprintf( '%1$s-%2$s.mo', 'edd-geckoboard', $locale );

			// Setup paths to current locale file
			$mofile_local   = $lang_dir . $mofile;
			$mofile_global  = WP_LANG_DIR . '/edd-geckoboard/' . $mofile;

			if( file_exists( $mofile_global ) ) {
				// Look in global /wp-content/languages/edd-geckoboard/ folder
				load_textdomain( 'edd-geckoboard', $mofile_global );
			} elseif( file_exists( $mofile_local ) ) {
				// Look in local /wp-content/plugins/edd-geckoboard/ folder
				load_textdomain( 'edd-geckoboard', $mofile_local );
			} else {
				// Load the default language files
				load_plugin_textdomain( 'edd-geckoboard', false, $lang_dir );
			}
		}
	}
}


/**
 * The main function responsible for returning the one true EDD_Geckoboard
 * instance to functions everywhere
 *
 * @since       1.0.0
 * @return      EDD_Geckoboard The one true EDD_Geckoboard
 */
function edd_geckoboard() {
	if( ! class_exists( 'Easy_Digital_Downloads' ) ) {
		if( ! class_exists( 'S214_EDD_Activation' ) ) {
			require_once 'includes/libraries/class.s214-edd-activation.php';
		}

		$activation = new S214_EDD_Activation( plugin_dir_path( __FILE__ ), basename( __FILE__ ) );
		$activation = $activation->run();

		return EDD_Geckoboard::instance();
	} else {
		return EDD_Geckoboard::instance();
	}
}
add_action( 'plugins_loaded', 'edd_geckoboard' );
