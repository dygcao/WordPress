<?php
/**
 * Update Compatibility
 *
 * @package CartFlows
 */

if ( ! class_exists( 'Cartflows_Update' ) ) :

	/**
	 * CartFlows Update initial setup
	 *
	 * @since 1.0.0
	 */
	class Cartflows_Update {

		/**
		 * Class instance.
		 *
		 * @access private
		 * @var $instance Class instance.
		 */
		private static $instance;

		/**
		 * Initiator
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 *  Constructor
		 */
		public function __construct() {
			add_action( 'admin_init', array( $this, 'init' ) );
		}

		/**
		 * Init
		 *
		 * @since 1.0.0
		 * @return void
		 */
		function init() {

			do_action( 'cartflows_update_before' );

			// Get auto saved version number.
			$saved_version = get_option( 'cartflows-version', false );

			// Update auto saved version number.
			if ( ! $saved_version ) {
				update_option( 'cartflows-version', CARTFLOWS_VER );
				return;
			}

			// If equals then return.
			if ( version_compare( $saved_version, CARTFLOWS_VER, '=' ) ) {
				return;
			}

			$this->logger_files();

			if ( version_compare( $saved_version, '1.1.22', '<' ) ) {
				update_option( 'wcf_setup_skipped', true );
			}

			if ( version_compare( $saved_version, '1.2.0', '<' ) ) {

				$this->changed_wp_templates();
			}

			// Update auto saved version number.
			update_option( 'cartflows-version', CARTFLOWS_VER );

			do_action( 'cartflows_update_after' );
		}


		/**
		 * Loading logger files.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		function logger_files() {

			if ( ! defined( 'CARTFLOWS_LOG_DIR' ) ) {

				$upload_dir = wp_upload_dir( null, false );

				define( 'CARTFLOWS_LOG_DIR', $upload_dir['basedir'] . '/cartflows-logs/' );
			}

			wcf()->create_files();
		}

		/**
		 * Init
		 *
		 * @since 1.0.0
		 * @return void
		 */
		function changed_wp_templates() {

			$args = array(
				'post_type'      => 'cartflows_step',
				'fields'         => 'ids',
				'no_found_rows'  => true,
				'post_status'    => 'any',
				'posts_per_page' => -1,
				'meta_query'     => array(
					'relation' => 'OR',
					array(
						'key'     => '_wp_page_template',
						'value'   => 'cartflows-canvas',
						'compare' => '!=',
					),
					array(
						'key'     => '_wp_page_template',
						'value'   => 'NOT EXISTS',
						'compare' => 'NOT EXISTS',
					),
				),
			);

			$query    = new WP_Query( $args );
			$post_ids = (array) $query->posts;

			foreach ( $post_ids as $key => $post_id ) {

				update_post_meta( $post_id, '_wp_page_template', 'cartflows-default' );

			}

		}
	}
	/**
	 * Kicking this off by calling 'get_instance()' method
	 */
	Cartflows_Update::get_instance();

endif;
