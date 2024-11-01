<?php

if ( ! class_exists( 'Shop_Metrics_Report_Init' ) ) {
	class Shop_Metrics_Report_Init {
		/**
		 * Detect active E-Commerce plugins and try to support them for Shop Metrics Report
		 */
		public function load_smr_plugins() {
			add_option( 'shopmetrics_for_woocommerce', array(
				'enabled'        => 1,
				'webshop_salt'   => NULL,
				'webshop_pepper' => NULL
			) );

			$settings = get_option( 'shopmetrics_for_wp' );

			$classes = array(
				'lib/class-api-calls.php'
			);

			if ( isset( $settings['plugin'] ) ) {
				if( $settings['plugin']=='WC' ){
					$classes[]	=	'lib/plugins/class-woocommerce.php';
				}
				elseif( $settings['plugin']=='EDD' ){
					$classes[] = 'lib/plugins/class-edd.php';
				}
			}

			foreach ( $classes as $class ) {
				require( SMR_PATH . $class );
			}
		}
	}
}

?>