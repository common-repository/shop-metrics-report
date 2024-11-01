<?php

if ( ! class_exists( 'Shop_Metrics_for_WP' ) ) {

	class Shop_Metrics_for_WP {

		public function __construct() {
			add_action( 'admin_init', array( $this, 'init_smr_for_wp' ) );
			add_action( 'admin_menu', array( $this, 'create_menu' ) );

			if ( ! is_admin() ) {
				die( "What are your doing here? We don't like script kiddies!" );
			}
		}

		/**
		 * Init Shop Metrics Report for WP
		 */
		public function init_smr_for_wp() {
			$settings = get_option( 'shopmetrics_for_wp' );

			if ( empty( $settings['webshop_salt'] ) || empty( $settings['webshop_pepper'] ) ) {
				add_action( 'admin_notices', array( $this, 'config_api_keys' ) );
			}

			register_setting( 'smr_for_wp', 'smr_for_wp', 'intval' );

			add_settings_error(
				'smr_for_wp',
				'smr_for_wp',
				__( 'Settings saved!', 'shopmetrics-for-wp' ),
				'updated'
			);
		}

		/**
		 * Show an error when the plugin isn't configured
		 */
		public function config_api_keys() {
			echo '<div class="error"><p>' . sprintf( __( 'Please configure your Shop Metrics Report API keys on the %1$sSettings page%2$s and enable your specific plugin.', 'shopmetrics-for-wp' ), '<a href="' . admin_url() . 'admin.php?page=smr_for_wp_settings">', '</a>' ) . '</p></div>';
		}

		/**
		 * Create the Shop Metrics menu item
		 */
		public function create_menu() {
			add_menu_page( __( 'Shop Metrics:', 'shopmetrics-for-wp' ) . ' ' . __( 'Basic API settings', 'shopmetrics-for-wp' ), __( 'Shop Metrics', 'shopmetrics-for-wp' ), 'manage_options', 'smr_for_wp_settings', array(
				$this,
				'getter'
			), NULL, '55.10084827173' );
			$submenu_pages = array(
				array(
					'smr_for_wp_settings',
					__( 'Shop Metrics:', 'shopmetrics-for-wp' ) . ' ' . __( 'Basic API Settings', 'shopmetrics-for-wp' ),
					__( 'Settings', 'shopmetrics-for-wp' ),
					'manage_options',
					'smr_for_wp_settings',
					array( $this, 'getter' ),
					array( array( $this, 'smr_for_wp_settings' ) )
				),
				array(
					'smr_for_wp_settings',
					__( 'Shop Metrics:', 'shopmetrics-for-wp' ) . ' ' . __( 'Test API Connection', 'shopmetrics-for-wp' ),
					__( 'Test API connection', 'shopmetrics-for-wp' ),
					'manage_options',
					'smr_for_wp_test',
					array( $this, 'getter' ),
					array( array( $this, 'smr_for_wp_settings' ) )
				),
			);

			if ( count( $submenu_pages ) ) {
				foreach ( $submenu_pages as $submenu_page ) {
					// Add submenu page
					add_submenu_page( $submenu_page[0], $submenu_page[1], $submenu_page[2], $submenu_page[3], $submenu_page[4], $submenu_page[5] );
				}
			}
		}

		/**
		 * Getter for a page
		 */
		public function getter() {
			if ( isset( $_GET['page'] ) ) {
				switch ( $_GET['page'] ) {
					case 'smr_for_wp_test':
						$this->page_test();
						break;
					case 'smr_for_wp_settings':
						$this->page_settings();
						break;
				}
			}
		}

		/**
		 * Show the settings page
		 */
		private function page_settings() {
			$nonce = wp_create_nonce( 'shopmetrics-for-wp-settings' );

			if ( isset( $_POST['submit'] ) && wp_verify_nonce( $_POST['_wpnonce'], 'shopmetrics-for-wp-settings' ) ) {

				if ( ! empty( $_POST['webshop_salt'] ) && ! empty( $_POST['webshop_pepper'] ) ) {
					if ( ! isset( $_POST['enabled'] ) ) {
						$_POST['enabled'] = 0;
					}

					update_option( 'shopmetrics_for_wp', array(
						'plugin'         => esc_attr( $_POST['plugin'] ),
						'webshop_salt'   => esc_attr( $_POST['webshop_salt'] ),
						'webshop_pepper' => esc_attr( $_POST['webshop_pepper'] )
					) );

					$success = __( 'The settings are saved successfully! You can now test your connection and your orders will appear in your Shop Metrics Report dashboard.', 'shopmetrics-for-wp' );

					add_settings_error(
						'smr_for_wp',
						'smr_for_wp',
						$success,
						'updated'
					);
				} else {
					$error = __( 'Please fill in a valid salt and pepper! You can <a href="https://dashboard.shopmetrics.report/account/webshops" target="_blank">retrieve them here</a>.', 'shopmetrics-for-wp' );

					add_settings_error(
						'smr_for_wp',
						'smr_for_wp',
						$error,
						'error'
					);
				}
			}

			$settings = get_option( 'shopmetrics_for_wp' );

			$installed = array();
			if ( class_exists( 'WC_Product' ) ) {
				$installed['woocommerce'] = true;
			}
			if ( class_exists( 'EDD_API' ) ) {
				$installed['edd'] = true;
			}

			require_once( SMR_PATH . 'templates/settings.php' );
		}

		/**
		 * The main test module of the plugin, to test the connections and the salt / pepper
		 */
		private function page_test() {
			// Set default values on fail
			$result = array(
				'basic' => false,
				'api'   => false
			);

			$settings = get_option( 'shopmetrics_for_wp' );

			$basic = wp_remote_post( 'https://api.shopmetrics.report' );
			if ( ! isset( $basic->errors ) ) {
				if ( $basic['response']['code'] == 200 ) {
					$result['basic'] = true;
				}
			}

			$api = Shop_Metrics_Api_Calls::do_call( '/shop/testconnection',
				array(
					'salt'   => $settings['webshop_salt'],
					'pepper' => $settings['webshop_pepper']
				)
			);

			if ( ! isset( $api->errors ) ) {
				if ( isset( $api->data ) ) {
					if ( isset( $api->data->domain ) ) {
						$result['api']    = true;
						$result['domain'] = $api->data->domain;
					}
				}
			}

			require_once( SMR_PATH . 'templates/test.php' );
		}
	}

	new Shop_Metrics_for_WP();

}

?>