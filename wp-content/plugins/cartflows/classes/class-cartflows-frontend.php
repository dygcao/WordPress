<?php
/**
 * CartFlows Frontend.
 *
 * @package CartFlows
 */

/**
 * Class Cartflows_Frontend.
 */
class Cartflows_Frontend {

	/**
	 * Member Variable
	 *
	 * @var instance
	 */
	private static $instance;

	/**
	 *  Initiator
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	/**
	 * Constructor
	 */
	public function __construct() {

		/* Set / Destroy Flow Sessions. Set data */
		add_action( 'wp', array( $this, 'init_actions' ), 1 );

		add_action( 'init', array( $this, 'debug_data_setting_actions' ) );
		/* Enqueue global required scripts */
		add_action( 'wp', array( $this, 'wp_actions' ), 55 );

		/* Modify the checkout order received url to go thank you page in our flow */
		add_filter( 'woocommerce_get_checkout_order_received_url', array( $this, 'redirect_to_thankyou_page' ), 10, 2 );

	}

	/**
	 * Redirect to thank page if upsell not exists
	 *
	 * @param string $order_recieve_url url.
	 * @param object $order order object.
	 * @since 1.0.0
	 */
	function redirect_to_thankyou_page( $order_recieve_url, $order ) {

		/* Only for thank you page */
		wcf()->logger->log( 'Start-' . __CLASS__ . '::' . __FUNCTION__ );
		wcf()->logger->log( 'Only for thank you page' );

		if ( wcf()->flow->is_thankyou_page_exists( $order ) ) {

			if ( _is_wcf_doing_checkout_ajax() ) {

				$checkout_id = wcf()->utils->get_checkout_id_from_post_data();

				if ( ! $checkout_id ) {
					$checkout_id = wcf()->utils->get_checkout_id_from_order( $order->get_id() );
				}
			} else {
				$checkout_id = wcf()->utils->get_checkout_id_from_order( $order->get_id() );
			}

			wcf()->logger->log( 'Checkout ID : ' . $checkout_id );

			if ( $checkout_id ) {

				$thankyou_step_id = wcf()->flow->get_thankyou_page_id( $order );

				if ( $thankyou_step_id ) {

					$order_recieve_url = get_permalink( $thankyou_step_id );

					$order_recieve_url = add_query_arg(
						array(
							'wcf-key'   => $order->get_order_key(),
							'wcf-order' => $order->get_id(),
						),
						$order_recieve_url
					);
				}
			}
		}

		wcf()->logger->log( 'End-' . __CLASS__ . '::' . __FUNCTION__ );

		return $order_recieve_url;
	}

	/**
	 * Cancel and redirect to checkout
	 *
	 * @param string $return_url url.
	 * @since 1.0.0
	 */
	function redirect_to_checkout_on_cancel( $return_url ) {

		if ( _is_wcf_doing_checkout_ajax() ) {

			$checkout_id = wcf()->utils->get_checkout_id_from_post_data();

			if ( ! $checkout_id ) {
				$checkout_id = wcf()->utils->get_checkout_id_from_order( $order->get_id() );
			}
		} else {
			$checkout_id = wcf()->utils->get_checkout_id_from_order( $order->get_id() );
		}

		if ( $checkout_id ) {

			$return_url = add_query_arg(
				array(
					'cancel_order' => 'true',
					'_wpnonce'     => wp_create_nonce( 'woocommerce-cancel_order' ),
				),
				get_permalink( $checkout_id )
			);
		}

		return $return_url;
	}


	/**
	 * Remove theme styles.
	 *
	 * @since 1.0.0
	 */
	function remove_theme_styles() {

		if ( Cartflows_Compatibility::get_instance()->is_compatibility_theme_enabled() ) {
			return;
		}

		$page_template = get_post_meta( _get_wcf_step_id(), '_wp_page_template', true );

		$page_template = apply_filters( 'cartflows_page_template', $page_template );

		if ( 'default' === $page_template ) {
			return;
		}

		// get all styles data.
		global $wp_styles;
		global $wp_scripts;

		$get_stylesheet = 'themes/' . get_stylesheet() . '/';
		$get_template   = 'themes/' . get_template() . '/';

		$remove_styles = apply_filters( 'cartflows_remove_theme_styles', true );

		if ( $remove_styles ) {

			// loop over all of the registered scripts..
			foreach ( $wp_styles->registered as $handle => $data ) {

				if ( strpos( $data->src, $get_template ) !== false || strpos( $data->src, $get_stylesheet ) !== false ) {

					// remove it.
					wp_deregister_style( $handle );
					wp_dequeue_style( $handle );
				}
			}
		}

		$remove_scripts = apply_filters( 'cartflows_remove_theme_scripts', true );

		if ( $remove_scripts ) {

			// loop over all of the registered scripts.
			foreach ( $wp_scripts->registered as $handle => $data ) {

				if ( strpos( $data->src, $get_template ) !== false || strpos( $data->src, $get_stylesheet ) !== false ) {

					// remove it.
					wp_deregister_script( $handle );
					wp_dequeue_script( $handle );
				}
			}
		}

	}

	/**
	 * Update main order data in transient.
	 *
	 * @param array $woo_styles new styles array.
	 * @since 1.0.0
	 * @return array.
	 */
	function woo_default_css( $woo_styles ) {

		$woo_styles = array(
			'woocommerce-layout'      => array(
				'src'     => plugins_url( 'assets/css/woocommerce-layout.css', WC_PLUGIN_FILE ),
				'deps'    => '',
				'version' => WC_VERSION,
				'media'   => 'all',
				'has_rtl' => true,
			),
			'woocommerce-smallscreen' => array(
				'src'     => plugins_url( 'assets/css/woocommerce-smallscreen.css', WC_PLUGIN_FILE ),
				'deps'    => 'woocommerce-layout',
				'version' => WC_VERSION,
				'media'   => 'only screen and (max-width: ' . apply_filters( 'woocommerce_style_smallscreen_breakpoint', '768px' ) . ')',
				'has_rtl' => true,
			),
			'woocommerce-general'     => array(
				'src'     => plugins_url( 'assets/css/woocommerce.css', WC_PLUGIN_FILE ),
				'deps'    => '',
				'version' => WC_VERSION,
				'media'   => 'all',
				'has_rtl' => true,
			),
		);

		return $woo_styles;
	}

	/**
	 * Init Actions.
	 *
	 * @since 1.0.0
	 */
	function init_actions() {

		$this->set_flow_session();
	}

	/**
	 * Set flow session.
	 *
	 * @since 1.0.0
	 */
	function set_flow_session() {

		if ( wcf()->utils->is_step_post_type() ) {

			add_action( 'wp_head', array( $this, 'noindex_flow' ) );

			wcf()->utils->do_not_cache();

			/* Set key to support pixel */
			if ( isset( $_GET['wcf-key'] ) ) {
				$_GET['key']     = $_GET['wcf-key'];
				$_REQUEST['key'] = $_GET['wcf-key'];
			}

			if ( isset( $_GET['wcf-order'] ) ) {
				$_GET['order']              = $_GET['wcf-order'];
				$_REQUEST['order']          = $_GET['wcf-order'];
				$_GET['order-received']     = $_GET['wcf-order'];
				$_REQUEST['order-received'] = $_GET['wcf-order'];
			}
		}
	}

	/**
	 * Add noindex, nofollow.
	 *
	 * @since 1.0.0
	 */
	function noindex_flow() {

		$common = Cartflows_Helper::get_common_settings();

		if ( 'enable' === $common['disallow_indexing'] ) {
			echo '<meta name="robots" content="noindex,nofollow">';
		}
	}

	/**
	 * WP Actions.
	 *
	 * @since 1.0.0
	 */
	function wp_actions() {

		if ( wcf()->utils->is_step_post_type() ) {

			if ( ! wcf()->is_woo_active && wcf()->utils->check_is_woo_required_page() ) {
				wp_die( ' This page requires WooCommerce plugin installed and activated!', 'WooCommerce Required' );
			}

			/* CSS Compatibility for All theme */
			add_filter( 'woocommerce_enqueue_styles', array( $this, 'woo_default_css' ), 9999 );

			add_action( 'wp_enqueue_scripts', array( $this, 'remove_theme_styles' ), 9999 );
			add_action( 'wp_enqueue_scripts', array( $this, 'global_flow_scripts' ), 20 );

			/* Load woo templates from plugin */
			add_filter( 'woocommerce_locate_template', array( $this, 'override_woo_template' ), 20, 3 );

			/* Add version class to body in frontend. */
			add_filter( 'body_class', array( $this, 'add_cartflows_lite_version_to_body' ) );

			/* Custom Script Option */
			add_action( 'wp_head', array( $this, 'custom_script_option' ) );

			/* Remove the action applied by the Flatsome theme */
			if ( Cartflows_Compatibility::get_instance()->is_flatsome_enabled() ) {
				$this->remove_flatsome_action();
			}
		}
	}

	/**
	 * Debug Data Setting Actions.
	 *
	 * @since 1.1.14
	 */
	function debug_data_setting_actions() {

		add_filter( 'cartflows_load_min_assets', array( $this, 'allow_load_minify' ) );
	}

	/**
	 * Get/Set the allow minify option.
	 *
	 * @since 1.1.14
	 */
	function allow_load_minify() {
		$debug_data     = Cartflows_Helper::get_debug_settings();
		$allow_minified = $debug_data['allow_minified_files'];
		$allow_minify   = false;

		if ( 'enable' === $allow_minified ) {
			$allow_minify = true;
		}

		return $allow_minify;
	}

	/**
	 * Global flow scripts.
	 *
	 * @since 1.0.0
	 */
	function global_flow_scripts() {

		global $post;

		$flow           = get_post_meta( $post->ID, 'wcf-flow-id', true );
		$current_step   = $post->ID;
		$next_step_link = '';
		$compatibility  = Cartflows_Compatibility::get_instance();

		if ( _is_wcf_landing_type() ) {

			$next_step_id   = wcf()->utils->get_next_step_id( $flow, $current_step );
			$next_step_link = get_permalink( $next_step_id );
		}

		$page_template = get_post_meta( _get_wcf_step_id(), '_wp_page_template', true );

		$localize = array(
			'ajax_url'      => admin_url( 'admin-ajax.php', 'relative' ),
			'is_pb_preview' => $compatibility->is_page_builder_preview(),
			'current_theme' => $compatibility->get_current_theme(),
			'current_flow'  => $flow,
			'current_step'  => $current_step,
			'next_step'     => $next_step_link,
			'page_template' => $page_template,
		);

		wp_localize_script( 'jquery', 'cartflows', apply_filters( 'global_cartflows_js_localize', $localize ) );

		wp_enqueue_style( 'wcf-frontend-global', wcf()->utils->get_css_url( 'frontend' ), array(), CARTFLOWS_VER );

		wp_enqueue_script(
			'wcf-frontend-global',
			wcf()->utils->get_js_url( 'frontend' ),
			array( 'jquery' ),
			CARTFLOWS_VER,
			false
		);
	}

	/**
	 * Custom Script in head.
	 *
	 * @since 1.0.0
	 */
	function custom_script_option() {

		/* Add custom script to header in frontend. */
		$script = $this->get_custom_script();
		if ( '' !== $script ) {
			if ( false === strpos( $script, '<script' ) ) {
				$script = '<script>' . $script . '</script>';
			}
			echo '<!-- Custom CartFlows Script -->';
			echo $script;
			echo '<!-- End Custom CartFlows Script -->';
		}
	}

	/**
	 * Override woo templates.
	 *
	 * @param string $template new  Template full path.
	 * @param string $template_name Template name.
	 * @param string $template_path Template Path.
	 * @since 1.1.5
	 * @return string.
	 */
	function override_woo_template( $template, $template_name, $template_path ) {

		global $woocommerce;

		$_template = $template;

		$plugin_path = CARTFLOWS_DIR . 'woocommerce/template/';

		if ( file_exists( $plugin_path . $template_name ) ) {
			$template = $plugin_path . $template_name;
		}

		if ( ! $template ) {
			$template = $_template;
		}

		return $template;
	}

	/**
	 * Remove the action applied by the Flatsome theme.
	 *
	 * @since 1.1.5
	 * @return void.
	 */
	function remove_flatsome_action() {

		// Remove action where flatsome dequeued the woocommerce's default styles.
		remove_action( 'wp_enqueue_scripts', 'flatsome_woocommerce_scripts_styles', 98 );
	}

	/**
	 * Add version class to body in frontend.
	 *
	 * @since 1.1.5
	 * @param array $classes classes.
	 * @return array $classes classes.
	 */
	function add_cartflows_lite_version_to_body( $classes ) {

		$classes[] = 'cartflows-' . CARTFLOWS_VER;

		return $classes;

	}

	/**
	 *  Get custom script data.
	 *
	 * @since 1.0.0
	 */
	function get_custom_script() {

		global $post;

		$script = get_post_meta( $post->ID, 'wcf-custom-script', true );

		return $script;
	}
}

/**
 *  Prepare if class 'Cartflows_Frontend' exist.
 *  Kicking this off by calling 'get_instance()' method
 */
Cartflows_Frontend::get_instance();
