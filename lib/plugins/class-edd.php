<?php

if ( ! class_exists( 'Shop_Metrics_EDD' ) ) {
	class Shop_Metrics_EDD {

		private static $settings;

		private static $token;

		public function __construct() {
			self::$settings = get_option( 'shopmetrics_for_wp' );

			// Hook on a new order or order status change, so we can sent the data
			//add_action( 'edd_update_payment_status', array( $this, 'order_completed' ) );
		}

		/**
		 * Hook on new orders
		 *
		 * @param $payment_id
		 */
		public function order_completed( $payment_id ) {

		}

		private function send_order( $order_data ) {

		}

	}

	new Shop_Metrics_EDD;
}