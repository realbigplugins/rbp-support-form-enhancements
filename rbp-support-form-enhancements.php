<?php
/**
 * Plugin Name: RBP Support Form Enhancements
 * Description: Makes the Support From a little more useful
 * Version: 0.1.0
 * Text Domain: rbp-support-form-enhancements
 * Author: Eric Defore
 * Author URI: https://realbigmarketing.com/
 * Contributors: d4mation
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'RBP_Support_Form_Enhancements' ) ) {

	/**
	 * Main RBP_Support_Form_Enhancements class
	 *
	 * @since	  1.0.0
	 */
	class RBP_Support_Form_Enhancements {
		
		/**
		 * @var			RBP_Support_Form_Enhancements $plugin_data Holds Plugin Header Info
		 * @since		1.0.0
		 */
		public $plugin_data;
		
		/**
		 * @var			RBP_Support_Form_Enhancements $admin_errors Stores all our Admin Errors to fire at once
		 * @since		1.0.0
		 */
		private $admin_errors;

		/**
		 * Get active instance
		 *
		 * @access	  public
		 * @since	  1.0.0
		 * @return	  object self::$instance The one true RBP_Support_Form_Enhancements
		 */
		public static function instance() {
			
			static $instance = null;
			
			if ( null === $instance ) {
				$instance = new static();
			}
			
			return $instance;

		}
		
		protected function __construct() {
			
			$this->setup_constants();
			$this->load_textdomain();
			
			if ( version_compare( get_bloginfo( 'version' ), '4.4' ) < 0 ) {
				
				$this->admin_errors[] = sprintf( _x( '%s requires v%s of %s or higher to be installed!', 'Outdated Dependency Error', 'rbp-support-form-enhancements' ), '<strong>' . $this->plugin_data['Name'] . '</strong>', '4.4', '<a href="' . admin_url( 'update-core.php' ) . '"><strong>WordPress</strong></a>' );
				
				if ( ! has_action( 'admin_notices', array( $this, 'admin_errors' ) ) ) {
					add_action( 'admin_notices', array( $this, 'admin_errors' ) );
				}
				
				return false;
				
			}
			
			$this->require_necessities();
			
			// Register our CSS/JS for the whole plugin
			add_action( 'init', array( $this, 'register_scripts' ) );
			
		}

		/**
		 * Setup plugin constants
		 *
		 * @access	  private
		 * @since	  1.0.0
		 * @return	  void
		 */
		private function setup_constants() {
			
			// WP Loads things so weird. I really want this function.
			if ( ! function_exists( 'get_plugin_data' ) ) {
				require_once ABSPATH . '/wp-admin/includes/plugin.php';
			}
			
			// Only call this once, accessible always
			$this->plugin_data = get_plugin_data( __FILE__ );

			if ( ! defined( 'RBP_Support_Form_Enhancements_VER' ) ) {
				// Plugin version
				define( 'RBP_Support_Form_Enhancements_VER', $this->plugin_data['Version'] );
			}

			if ( ! defined( 'RBP_Support_Form_Enhancements_DIR' ) ) {
				// Plugin path
				define( 'RBP_Support_Form_Enhancements_DIR', plugin_dir_path( __FILE__ ) );
			}

			if ( ! defined( 'RBP_Support_Form_Enhancements_URL' ) ) {
				// Plugin URL
				define( 'RBP_Support_Form_Enhancements_URL', plugin_dir_url( __FILE__ ) );
			}
			
			if ( ! defined( 'RBP_Support_Form_Enhancements_FILE' ) ) {
				// Plugin File
				define( 'RBP_Support_Form_Enhancements_FILE', __FILE__ );
			}

		}

		/**
		 * Internationalization
		 *
		 * @access	  private 
		 * @since	  1.0.0
		 * @return	  void
		 */
		private function load_textdomain() {

			// Set filter for language directory
			$lang_dir = RBP_Support_Form_Enhancements_DIR . '/languages/';
			$lang_dir = apply_filters( 'rbp_support_form_enhancements_languages_directory', $lang_dir );

			// Traditional WordPress plugin locale filter
			$locale = apply_filters( 'plugin_locale', get_locale(), 'rbp-support-form-enhancements' );
			$mofile = sprintf( '%1$s-%2$s.mo', 'rbp-support-form-enhancements', $locale );

			// Setup paths to current locale file
			$mofile_local   = $lang_dir . $mofile;
			$mofile_global  = WP_LANG_DIR . '/rbp-support-form-enhancements/' . $mofile;

			if ( file_exists( $mofile_global ) ) {
				// Look in global /wp-content/languages/rbp-support-form-enhancements/ folder
				// This way translations can be overridden via the Theme/Child Theme
				load_textdomain( 'rbp-support-form-enhancements', $mofile_global );
			}
			else if ( file_exists( $mofile_local ) ) {
				// Look in local /wp-content/plugins/rbp-support-form-enhancements/languages/ folder
				load_textdomain( 'rbp-support-form-enhancements', $mofile_local );
			}
			else {
				// Load the default language files
				load_plugin_textdomain( 'rbp-support-form-enhancements', false, $lang_dir );
			}

		}
		
		/**
		 * Include different aspects of the Plugin
		 * 
		 * @access	  private
		 * @since	  1.0.0
		 * @return	  void
		 */
		private function require_necessities() {

			require_once RBP_Support_Form_Enhancements_DIR . '/core/front/class-rbp-support-form-enhancements-gravity-form.php';
			
		}
		
		/**
		 * Show admin errors.
		 * 
		 * @access	  public
		 * @since	  1.0.0
		 * @return	  HTML
		 */
		public function admin_errors() {
			?>
			<div class="error">
				<?php foreach ( $this->admin_errors as $notice ) : ?>
					<p>
						<?php echo $notice; ?>
					</p>
				<?php endforeach; ?>
			</div>
			<?php
		}
		
		/**
		 * Register our CSS/JS to use later
		 * 
		 * @access	  public
		 * @since	  1.0.0
		 * @return	  void
		 */
		public function register_scripts() {
			
			wp_register_style(
				'rbp-support-form-enhancements',
				RBP_Support_Form_Enhancements_URL . 'assets/css/style.css',
				null,
				defined( 'WP_DEBUG' ) && WP_DEBUG ? time() : RBP_Support_Form_Enhancements_VER
			);
			
			wp_register_script(
				'rbp-support-form-enhancements',
				RBP_Support_Form_Enhancements_URL . 'assets/js/script.js',
				array( 'jquery' ),
				defined( 'WP_DEBUG' ) && WP_DEBUG ? time() : RBP_Support_Form_Enhancements_VER,
				true
			);
			
			wp_localize_script( 
				'rbp-support-form-enhancements',
				'rBPSupportFormEnhancements',
				apply_filters( 'rbp_support_form_enhancements_localize_script', array() )
			);
			
			wp_register_style(
				'rbp-support-form-enhancements-admin',
				RBP_Support_Form_Enhancements_URL . 'assets/css/admin.css',
				null,
				defined( 'WP_DEBUG' ) && WP_DEBUG ? time() : RBP_Support_Form_Enhancements_VER
			);
			
			wp_register_script(
				'rbp-support-form-enhancements-admin',
				RBP_Support_Form_Enhancements_URL . 'assets/js/admin.js',
				array( 'jquery' ),
				defined( 'WP_DEBUG' ) && WP_DEBUG ? time() : RBP_Support_Form_Enhancements_VER,
				true
			);
			
			wp_localize_script( 
				'rbp-support-form-enhancements-admin',
				'rBPSupportFormEnhancements',
				apply_filters( 'rbp_support_form_enhancements_localize_admin_script', array() )
			);
			
		}
		
	}
	
} // End Class Exists Check

/**
 * The main function responsible for returning the one true RBP_Support_Form_Enhancements
 * instance to functions everywhere
 *
 * @since	  1.0.0
 * @return	  \RBP_Support_Form_Enhancements The one true RBP_Support_Form_Enhancements
 */
add_action( 'plugins_loaded', 'rbp_support_form_enhancements_load' );
function rbp_support_form_enhancements_load() {

	require_once __DIR__ . '/core/rbp-support-form-enhancements-functions.php';
	RBPSUPPORTFORMENHANCEMENTS();

}
