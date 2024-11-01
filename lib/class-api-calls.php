<?php

if ( ! class_exists( 'Shop_Metrics_Api_Calls' ) ) {
	class Shop_Metrics_Api_Calls {

		public static $api_url = 'https://api.shopmetrics.report';

		/**
		 * Do an API call
		 *
		 * @param $action The url for the API call
		 * @param $body   An array containing the arguments in the API call
		 *
		 * @return mixed Json string on success or an string with the error message
		 */
		public static function do_call( $action, $body ) {
			$api = wp_remote_post( self::$api_url . $action, array(
				'method' => 'POST',
				'body'   => $body
			) );

			if ( ! isset( $api->errors ) ) {
				if ( $api['response']['code'] == 200 && isset( $api['body'] ) ) {
					return json_decode( $api['body'] );
				}
			} else {
				return $api->errors;
			}
		}


	}
}