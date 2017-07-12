<?php
/**
 * Add fields to user profile
 *
 * @package     EDD\Geckoboard\Admin\User\Profile
 * @since       1.0.0
 */


// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Add API settings to user profile page
 *
 * @since       1.0.0
 * @param       object $user The current user object
 * @return      void
 */
function edd_geckoboard_api_settings( $user ) {
	// Bail if current user shouldn't be editing this user
	if( ! current_user_can( 'edit_user', $user->ID ) ) {
		return;
	}

	$user  = get_userdata( $user->ID );
	$key   = $user->edd_user_public_key;
	$token = hash( 'md5', $user->edd_user_secret_key . $user->edd_user_public_key );

	if( ( edd_get_option( 'api_allow_user_keys', false ) || current_user_can( 'manage_shop_settings' ) ) || ( ! empty( $key ) && ! empty( $token ) ) ) {
		$url_base        = get_bloginfo( 'url' ) . '/edd-api/';
		$gbsaleschart    = $url_base . 'gbsaleschart?key=' . $key . '&token=' . $token;
		$gbsales         = $url_base . 'gbsales?key=' . $key . '&token=' . $token;
		$gbearningschart = $url_base . 'gbearningschart?key=' . $key . '&token=' . $token;
		$gbearnings      = $url_base . 'gbearnings?key=' . $key . '&token=' . $token;
		$gbpurchases     = $url_base . 'gbpurchases?key=' . $key . '&token=' . $token;
		?>
		<h3><?php _e( 'Easy Digital Downloads Geckoboard URLs', 'edd-geckoboard' ); ?></h3>
		<table class="form-table">
			<tbody>
				<tr>
					<th><?php _e( 'Sales Widgets', 'edd-geckoboard' ); ?></th>
					<td>
						<div style="font-weight: bold; font-size: small"><?php _e( 'Bar/Column Chart Widget', 'edd-geckoboard' ); ?></div>
						<input type="text" disabled="disabled" class="regular-text" id="gbsaleschart" value="<?php echo $gbsaleschart; ?>">
						<br />
						<div style="font-weight: bold; font-size: small"><?php _e( 'Number &amp; Secondary Stat Widget', 'edd-geckoboard' ); ?></div>
						<input type="text" disabled="disabled" class="regular-text" id="gbsales" value="<?php echo $gbsales; ?>">
					</td>
				</tr>
				<tr>
					<th><?php _e( 'Earnings Widgets', 'edd-geckoboard' ); ?></th>
					<td>
						<div style="font-weight: bold; font-size: small"><?php _e( 'Bar/Column Chart Widget', 'edd-geckoboard' ); ?></div>
						<input type="text" disabled="disabled" class="regular-text" id="gbearningschart" value="<?php echo $gbearningschart; ?>">
						<br />
						<div style="font-weight: bold; font-size: small"><?php _e( 'Number &amp; Secondary Stat Widget', 'edd-geckoboard' ); ?></div>
						<input type="text" disabled="disabled" class="regular-text" id="gbearnings" value="<?php echo $gbearnings; ?>">
					</td>
				</tr>
				<tr>
					<th><?php _e( 'Purchase Widgets', 'edd-geckoboard' ); ?></th>
					<td>
						<div style="font-weight: bold; font-size: small"><?php _e( 'List Widget', 'edd-geckoboard' ); ?></div>
						<input type="text" disabled="disabled" class="regular-text" id="gbpurchases" value="<?php echo $gbpurchases; ?>">
					</td>
				</tr>
			</tbody>
		</table>
		<?php
	}
}
add_action( 'show_user_profile', 'edd_geckoboard_api_settings' );