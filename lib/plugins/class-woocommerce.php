<?php

if ( ! class_exists( 'Shop_Metrics_WC' ) ) {
	class Shop_Metrics_WC {

		private static $settings;

		public function __construct() {
			self::$settings = get_option( 'shopmetrics_for_wp' );

			// Hook on a new order or order status change, so we can sent the data
			add_action( 'woocommerce_order_status_changed', array( $this, 'order_completed' ) );
		}

		/**
		 * Hook on new orders
		 *
		 * @param $order_id
		 */
		public function order_completed( $order_id ) {
			$order = wc_get_order( $order_id );

			$order_post = get_post( $order_id );

			$order_data = array(
				'id'                        => $order->id,
				'order_number'              => $order->get_order_number(),
				'created_at'                => $order_post->post_date_gmt,
				'updated_at'                => $order_post->post_modified_gmt,
				'completed_at'              => $order->completed_date,
				'status'                    => $order->get_status(),
				'currency'                  => $order->get_order_currency(),
				'total'                     => wc_format_decimal( $order->get_total(), 2 ),
				'subtotal'                  => wc_format_decimal( $order->get_subtotal( $order ), 2 ),
				'total_line_items_quantity' => $order->get_item_count(),
				'total_tax'                 => wc_format_decimal( $order->get_total_tax(), 2 ),
				'total_shipping'            => wc_format_decimal( $order->get_total_shipping(), 2 ),
				'cart_tax'                  => wc_format_decimal( $order->get_cart_tax(), 2 ),
				'shipping_tax'              => wc_format_decimal( $order->get_shipping_tax(), 2 ),
				'total_discount'            => wc_format_decimal( $order->get_total_discount(), 2 ),
				'cart_discount'             => wc_format_decimal( $order->get_cart_discount(), 2 ),
				'order_discount'            => wc_format_decimal( $order->get_order_discount(), 2 ),
				'shipping_methods'          => $order->get_shipping_method(),
				'payment_details'           => array(
					'method_id'    => $order->payment_method,
					'method_title' => $order->payment_method_title,
					'paid'         => isset( $order->paid_date ),
				),
				'billing_address'           => array(
					'first_name' => $order->billing_first_name,
					'last_name'  => $order->billing_last_name,
					'company'    => $order->billing_company,
					'address_1'  => $order->billing_address_1,
					'address_2'  => $order->billing_address_2,
					'city'       => $order->billing_city,
					'state'      => $order->billing_state,
					'postcode'   => $order->billing_postcode,
					'country'    => $order->billing_country,
					'email'      => $order->billing_email,
					'phone'      => $order->billing_phone,
				),
				'shipping_address'          => array(
					'first_name' => $order->shipping_first_name,
					'last_name'  => $order->shipping_last_name,
					'company'    => $order->shipping_company,
					'address_1'  => $order->shipping_address_1,
					'address_2'  => $order->shipping_address_2,
					'city'       => $order->shipping_city,
					'state'      => $order->shipping_state,
					'postcode'   => $order->shipping_postcode,
					'country'    => $order->shipping_country,
				),
				'note'                      => $order->customer_note,
				'customer_ip'               => $order->customer_ip_address,
				'customer_user_agent'       => $order->customer_user_agent,
				'customer_id'               => $order->customer_user,
				'view_order_url'            => $order->get_view_order_url(),
				'line_items'                => array(),
				'shipping_lines'            => array(),
				'tax_lines'                 => array(),
				'fee_lines'                 => array(),
			);

			// add line items
			foreach ( $order->get_items() as $item_id => $item ) {

				$product = $order->get_product_from_item( $item );

				$meta = new WC_Order_Item_Meta( $item['item_meta'], $product );

				$item_meta = array();

				$hideprefix = ( isset( $filter['all_item_meta'] ) && $filter['all_item_meta'] === 'true' ) ? null : '_';

				foreach ( $meta->get_formatted( $hideprefix ) as $meta_key => $formatted_meta ) {
					$item_meta[] = array(
						'key'   => $meta_key,
						'label' => $formatted_meta['label'],
						'value' => $formatted_meta['value'],
					);
				}

				$order_data['line_items'][] = array(
					'id'           => $item_id,
					'subtotal'     => wc_format_decimal( $order->get_line_subtotal( $item ), 2 ),
					'subtotal_tax' => wc_format_decimal( $item['line_subtotal_tax'], 2 ),
					'total'        => wc_format_decimal( $order->get_line_total( $item ), 2 ),
					'total_tax'    => wc_format_decimal( $order->get_line_tax( $item ), 2 ),
					'price'        => wc_format_decimal( $order->get_item_total( $item ), 2 ),
					'quantity'     => (int) $item['qty'],
					'tax_class'    => ( ! empty( $item['tax_class'] ) ) ? $item['tax_class'] : null,
					'name'         => $item['name'],
					'product_id'   => ( isset( $product->variation_id ) ) ? $product->variation_id : $product->id,
					'sku'          => is_object( $product ) ? $product->get_sku() : null,
					'meta'         => $item_meta,
				);
			}

			// add shipping
			foreach ( $order->get_shipping_methods() as $shipping_item_id => $shipping_item ) {

				$order_data['shipping_lines'][] = array(
					'id'           => $shipping_item_id,
					'method_id'    => $shipping_item['method_id'],
					'method_title' => $shipping_item['name'],
					'total'        => wc_format_decimal( $shipping_item['cost'], 2 ),
				);
			}

			// add taxes
			foreach ( $order->get_tax_totals() as $tax_code => $tax ) {

				$order_data['tax_lines'][] = array(
					'id'       => $tax->id,
					'rate_id'  => $tax->rate_id,
					'code'     => $tax_code,
					'title'    => $tax->label,
					'total'    => wc_format_decimal( $tax->amount, 2 ),
					'compound' => (bool) $tax->is_compound,
				);
			}

			// add fees
			foreach ( $order->get_fees() as $fee_item_id => $fee_item ) {

				$order_data['fee_lines'][] = array(
					'id'        => $fee_item_id,
					'title'     => $fee_item['name'],
					'tax_class' => ( ! empty( $fee_item['tax_class'] ) ) ? $fee_item['tax_class'] : null,
					'total'     => wc_format_decimal( $order->get_line_total( $fee_item ), 2 ),
					'total_tax' => wc_format_decimal( $order->get_line_tax( $fee_item ), 2 ),
				);
			}

			$this->send_order( $order_data );
		}

		/**
		 * Cancelled order
		 */
		public function order_canceled() {

		}

		private function send_order( $order_data ) {
			$o = array();
			$p = array();

			if ( count( $order_data ) >= 1 ) {
				$o = array(
					'ordernumber'   => str_replace( '#', '', $order_data['order_number'] ),
					'orderdate'     => date( 'Y-m-d H:i:s', strtotime( $order_data['created_at'] ) ),
					'totalamount'   => $order_data['total'],
					'vat'           => $order_data['total_tax'],
					'shipping'      => $order_data['total_shipping'],
					'currency'      => $order_data['currency'],
					'zipcode'       => $order_data['shipping_address']['postcode'],
					'country'       => $order_data['shipping_address']['country'],
					'customeremail' => $order_data['billing_address']['email'],
					'customername'  => $order_data['shipping_address']['first_name'] . ' ' . $order_data['shipping_address']['last_name'],
				);

				foreach ( $order_data['line_items'] as $product ) {
					$p[] = array(
						'name'   => $product['name'],
						'price'  => $product['price'],
						'vat'    => $product['total_tax'],
						'number' => $product['quantity'],
					);
				}

				$data = array(
					'o' => $o,
					'p' => $p,
				);

				$body = array(
					'salt'   => self::$settings['webshop_salt'],
					'pepper' => self::$settings['webshop_pepper'],
					'data'   => json_encode( $data )
				);

			}
			$api = Shop_Metrics_Api_Calls::do_call( '/shop/addorder',
				$body
			);
		}

	}

	new Shop_Metrics_WC;
}