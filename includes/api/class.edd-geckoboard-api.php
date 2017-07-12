<?php
/**
 * Add Geckoboard stats to the EDD API
 *
 * @package     EDD\Geckoboard\API
 * @since       1.0.0
 */


// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * The Geckoboard API class
 *
 * @since       1.0.0
 */
class EDD_Geckoboard_API extends EDD_API {


	/**
	 * @since       1.0.0
	 * @var         object $stats The EDD_Stats object
	 */
	public $stats;


	/**
	 * Get things started
	 *
	 * @access      public
	 * @since       1.0.0
	 * @return      void
	 */
	public function __construct() {
		add_action( 'edd_api_valid_query_modes', array( $this, 'valid_query_modes' ) );
		add_filter( 'edd_api_output_data', array( $this, 'output_data' ), 10, 3 );

		$this->stats = new EDD_Payment_Stats;
	}


	/**
	 * Add Geckoboard query modes
	 *
	 * @access      public
	 * @since       1.0.0
	 * @param       array $modes The valid query modes
	 * @return      array $modes The updated query modes
	 */
	public function valid_query_modes( $modes ) {
		$geckoboard_modes = array( 'gbsaleschart', 'gbearningschart', 'gbsales', 'gbearnings', 'gbpurchases' );

		return array_merge( $geckoboard_modes, $modes );
	}


	/**
	 * Setup output data
	 *
	 * @access      public
	 * @since       1.0.0
	 * @param       array $data Current output data
	 * @param       string $query_mode The called query mode
	 * @param       object $this The API object
	 * @return      array $data The updated output data
	 */
	public function output_data( $data, $query_mode, $parent ) {
		$geckoboard_data = false;

		switch( $query_mode ) {
			case 'gbsaleschart' :
				$sales_stats = $parent->get_stats( array(
					'type'      => 'sales',
					'product'   => null,
					'date'      => 'range',
					'startdate' => date( 'Ymd', strtotime( '-7 days' ) ),
					'enddate'   => date( 'Ymd' )
				) );

				foreach( $sales_stats['sales'] as $date => $sales ) {
					$geckoboard_data['x_axis']['labels'][] = date( apply_filters( 'edd_geckoboard_date_format', 'm/d' ), strtotime( $date ) );
					$geckoboard_sales[] = $sales;
				}

				$geckoboard_data['series'] = array( array(
					'data' => $geckoboard_sales
				) );

				break;
			case 'gbearningschart' :
				$earnings_stats = $parent->get_stats( array(
					'type'      => 'earnings',
					'product'   => null,
					'date'      => 'range',
					'startdate' => date( 'Ymd', strtotime( '-7 days' ) ),
					'enddate'   => date( 'Ymd' )
				) );

				$geckoboard_data['y_axis'] = array(
					'format' => 'currency',
					'unit'   => edd_get_option( 'currency', 'USD' )
				);

				foreach( $earnings_stats['earnings'] as $date => $earnings ) {
					$geckoboard_data['x_axis']['labels'][] = date( apply_filters( 'edd_geckoboard_date_format', 'm/d' ), strtotime( $date ) );
					$geckoboard_earnings[] = $earnings;
				}

				$geckoboard_data['series'] = array( array(
					'data' => $geckoboard_earnings
				) );

				break;
			case 'gbsales' :
				$geckoboard_data = array(
					'item' => array(
						array(
							'value' => (float) $this->stats->get_sales( 0, 'this_month' )
						),
						array(
							'value' => (float) $this->stats->get_sales( 0, 'last_month' )
						)
					)
				);

				break;
			case 'gbearnings' :
				$geckoboard_data = array(
					'item' => array(
						array(
							'value' => edd_format_amount( $this->stats->get_earnings( 0, 'this_month' ) ),
							'prefix' => edd_currency_symbol( edd_get_option( 'currency', 'USD' ) )
						),
						array(
							'value' => edd_format_amount( $this->stats->get_earnings( 0, 'last_month' ) ),
							'prefix' => edd_currency_symbol( edd_get_option( 'currency', 'USD' ) )
						)
					)
				);

				break;
			case 'gbpurchases' :
				$sales           = $parent->get_recent_sales();
				$geckoboard_data = array();
				//var_dump( $sales ); exit;

				foreach( $sales['sales'] as $sale ) {
					$geckoboard_sale = new stdClass();
					$geckoboard_sale->title = new stdClass();
					$geckoboard_sale->title->text = $sale['email'];
					$geckoboard_sale->description = date( apply_filters( 'edd_geckoboard_list_date_format', 'm/d H:m' ), strtotime( $sale['date'] ) ) . ' / ' . html_entity_decode( edd_currency_symbol( edd_get_option( 'currency', 'USD' ) ) ) . edd_format_amount( $sale['total'] );

					array_push( $geckoboard_data, $geckoboard_sale );
				}

				// Geckoboard handles the list widget in a silly way
				// As a result, we have to hijack the API output and
				// handle it ourselves.
				header( 'Content-Type: application/json' );
				echo json_encode( $geckoboard_data );
				exit;

				break;
		}

		if( $geckoboard_data ) {
			$data = $geckoboard_data;
		}

		return $data;
	}
}