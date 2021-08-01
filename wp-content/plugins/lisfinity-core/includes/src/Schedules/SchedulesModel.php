<?php


namespace Lisfinity\Schedules;


use Lisfinity\Models\Notifications\NotificationModel;
use Lisfinity\Models\PromotionsModel;
use Lisfinity\Models\SubscriptionModel;

class SchedulesModel {

	public static $is_demo;

	protected static $_instance = null;

	/**
	 * Class instance
	 * --------------
	 *
	 * @return SchedulesModel|null
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Class basic initialization of the cron schedulers
	 * -------------------------------------------------
	 */
	public function init() {
		$options        = get_option( 'lisfinity-options' );
		$this::$is_demo = isset( $options['_site-mode'] ) ? lisfinity_is_enabled( $options['_site-mode'] ) : false;

		if ( $this::$is_demo ) {
			$this->schedule_demo_cron_jobs();
		} else {
			$this->clear_schedules();
		}
		$this->schedule_cron_jobs();
	}

	/**
	 * Schedule cron jobs that will be ran on demo sites
	 * -------------------------------------------------
	 */
	private function schedule_demo_cron_jobs() {
		if ( ! wp_next_scheduled( 'lisfinity__demo_cron_daily' ) ) {
			wp_schedule_event( time(), 'daily', 'lisfinity__demo_cron_daily' );
		}
		if ( ! wp_next_scheduled( 'lisfinity__demo_cron_hourly' ) ) {
			wp_schedule_event( time(), 'hourly', 'lisfinity__demo_cron_hourly' );
		}
		if ( ! wp_next_scheduled( 'lisfinity__demo_cron_half_hourly' ) ) {
			wp_schedule_event( time(), 'half_hourly', 'lisfinity__demo_cron_half_hourly' );
		}
	}

	/**
	 * Clear initialized schedules
	 * ---------------------------
	 */
	public function clear_schedules() {
		wp_clear_scheduled_hook( 'lisfinity__demo_cron_daily' );
		wp_clear_scheduled_hook( 'lisfinity__demo_cron_hourly' );
		wp_clear_scheduled_hook( 'lisfinity__demo_cron_half_hourly' );
	}

	/**
	 * Schedule default cron jobs
	 */
	private function schedule_cron_jobs() {
		if ( ! wp_next_scheduled( 'lisfinity__cron_twice_daily' ) ) {
			wp_schedule_event( time(), 'twicedaily', 'lisfinity__cron_twice_daily' );
		}
		if ( ! wp_next_scheduled( 'lisfinity__cron_daily' ) ) {
			wp_schedule_event( time(), 'daily', 'lisfinity__cron_daily' );
		}
		if ( ! wp_next_scheduled( 'lisfinity__cron_hourly' ) ) {
			wp_schedule_event( time(), 'hourly', 'lisfinity__cron_hourly' );
		}
	}

	public function send_subscription_reminders() {
		global $wpdb;
		$model = new SubscriptionModel();

		$users = $model->where( [
			[ 'status', 'active' ],
			[ 'expires_at', '<=', 'NOW() + INTERVAL 3 DAY' ],
		] )->get( '', '', 'user_id, expires_at' );

		if ( ! empty( $users ) ) {
			foreach ( $users as $user ) {
				$email_sent = get_user_meta( $user->user_id, 'subscription_expired_mail' );
				if ( 'sent' !== $email_sent ) {
					$user_data = get_userdata( $user->user_id );

					$subject = esc_html__( 'Subscription Near Expiration', 'lisfinity-core' );
					$body    = sprintf( esc_html__( 'Your payment subscription will expires within the next three days at %s ', 'lisfinity-core' ), date( 'Y-m-d H:i:s', $user->expires_at ) );
					$headers = [ 'Content-Type: text/html; charset=UTF-8' ];

					update_user_meta( $user->user_id, 'subscription_expired_mail', 'sent' );
					wp_mail( $user_data->user_email, $subject, $body, $headers );
				}
			}
		}
	}

	public function delete_expired_listings() {
		$days_passed = lisfinity_get_option( 'listings-expiration-days-removal' ) ?? 30;
		$ad_ids      = get_posts(
			[
				'post_type'      => 'product',
				'post_status'    => 'publish',
				'posts_per_page' => - 1,
				'tax_query'      => [
					[
						'taxonomy' => 'product_type',
						'field'    => 'name',
						'terms'    => 'listing',
						'operator' => 'IN',
					],
				],
				'fields'         => 'ids',
				'meta_query'     => [
					[
						'key'     => '_product-expiration',
						'value'   => strtotime( "-{$days_passed} days", current_time( 'timestamp' ) ),
						'compare' => '<',
					],
					[
						'key'     => '_product-status',
						'value'   => 'expired',
						'compare' => '=',
					]
				],
			]
		);

		if ( ! empty( $ad_ids ) ) {
			foreach ( $ad_ids as $ad_id ) {

				// delete expired listing attachments.
				$image = get_post_thumbnail_id( $ad_id );
				if ( ! empty( $image ) ) {
					wp_delete_attachment( $image, true );
				}
				$images = get_post_meta( $ad_id, '_product_image_gallery', true );
				if ( ! empty ( $images ) ) {
					$images = explode( ',', $images );
					foreach ( $images as $img ) {
						if ( (int) $img !== $image ) {
							wp_delete_attachment( (int) $img, true );
						}
					}

				}

				// delete expired listing.
				wp_delete_post( $ad_id, true );
			}
		}
	}

	/**
	 * Change the status of the expired ads to expired
	 * -----------------------------------------------
	 */
	public function change_expired_ads_status() {
		$ad_ids = get_posts(
			[
				'post_type'      => 'product',
				'post_status'    => 'publish',
				'posts_per_page' => - 1,
				'tax_query'      => [
					[
						'taxonomy' => 'product_type',
						'field'    => 'name',
						'terms'    => 'listing',
						'operator' => 'IN',
					],
				],
				'fields'         => 'ids',
				'meta_query'     => [
					[
						'key'     => '_product-expiration',
						'value'   => current_time( 'timestamp' ),
						'compare' => '<',
					],
					[
						'key'     => '_product-status',
						'value'   => 'active',
						'compare' => '=',
					]
				],
			]
		);

		if ( ! empty( $ad_ids ) ) {
			foreach ( $ad_ids as $ad_id ) {
				carbon_set_post_meta( $ad_id, 'product-status', 'expired' );
			}
		}
	}

	public function change_expired_promotions_status() {
		$model = new PromotionsModel();
		$date  = current_time( 'mysql' );
		$model->set( 'status', 'inactive' )->where( [
			[ 'status', 'active' ],
			[ 'expires_at', '<', "'{$date}'" ],
		] )->update();
	}

	/**
	 * Send email to owners of the ads near expiration
	 * -----------------------------------------------
	 */
	public function send_ad_expiration_email() {
		$ad_ids = get_posts(
			[
				'post_type'      => 'product',
				'post_status'    => 'publish',
				'posts_per_page' => - 1,
				'tax_query'      => [
					[
						'taxonomy' => 'product_type',
						'field'    => 'name',
						'terms'    => 'listing',
						'operator' => 'IN',
					],
				],
				'fields'         => 'ids',
				'meta_query'     => [
					[
						'key'     => '_product-expiration',
						'value'   => strtotime( '+1 day', current_time( 'timestamp' ) ),
						'compare' => '<',
					],
					[
						'key'     => '_product-status',
						'value'   => 'active',
						'compare' => '=',
					]
				],
			]
		);

		if ( ! empty( $ad_ids ) ) {
			foreach ( $ad_ids as $ad_id ) {
				// send email day before expiration.
				$owner         = carbon_get_post_meta( $ad_id, 'product-owner' );
				$business      = carbon_get_post_meta( $ad_id, 'product-business' );
				$email         = carbon_get_post_meta( $business, 'profile-email' );
				$ad_title      = get_the_title( $ad_id );
				$ad_link       = get_permalink( $ad_id );
				$ad_expires    = carbon_get_post_meta( $ad_id, 'product-expiration' );
				$ad_expiration = human_time_diff( current_time( 'timestamp' ), $ad_expires );
				// check if business email has been set and use user email if not.
				if ( empty( $email ) ) {
					$userdata = get_userdata( $owner );
					$email    = $userdata->user_email;
				}
				// if an email hasn't been sent already.
				$expiration_email_sent = get_transient( "{$ad_id}_expiration_email_sent_day" );
				if ( empty( $expiration_email_sent ) ) {
					$to      = $email;
					$subject = sprintf( esc_html__( 'Your ad will expire soon | %s', 'lisfinity-core' ), get_option( 'blogname' ) );
					$body    = sprintf( __( 'Ad %1$s will expire in %2$s. <br /><br />', 'lisfinity-core' ), "<a href='{$ad_link}'>{$ad_title}</a>", $ad_expiration );
					$headers = [ 'Content-Type: text/html; charset=UTF-8' ];

					wp_mail( $to, $subject, $body, $headers );

					set_transient( "{$ad_id}_expiration_email_sent_day", true, 2 * DAY_IN_SECONDS );
				}

				// send notification to subscribers.
				$notification_model = new NotificationModel();
				$bidders            = lisfinity_get_product_subscribers( $ad_id, $owner );
				if ( ! empty( $bidders ) ) {
					foreach ( $bidders as $bidder ) {
						$expiration_email_sent = get_transient( "{$ad_id}-{$bidder}_expiration_email_sent_day" );
						if ( empty( $expiration_email_sent ) && 'yes' === get_user_meta( $bidder, '_email_subscription|product_expiration', true ) ) {
							$to      = $email;
							$subject = sprintf( esc_html__( 'Listing will expire soon | %s', 'lisfinity-core' ), get_option( 'blogname' ) );
							$body    = sprintf( __( 'The listing %1$s will expire in %2$s. <br /><br />', 'lisfinity-core' ), "<a href='{$ad_link}'>{$ad_title}</a>", $ad_expiration );
							$headers = [ 'Content-Type: text/html; charset=UTF-8' ];

							wp_mail( $to, $subject, $body, $headers );

							set_transient( "{$ad_id}-{$bidder}_expiration_email_sent_day", true, 2 * DAY_IN_SECONDS );
						}
					}
				}
			}

		}
	}

	/**
	 * Relist ads on the demo site
	 * ---------------------------
	 */
	public function relist_ads() {
		$ad_ids = get_posts(
			[
				'post_type'      => 'product',
				'post_status'    => 'publish',
				'posts_per_page' => - 1,
				'tax_query'      => [
					[
						'taxonomy' => 'product_type',
						'field'    => 'name',
						'terms'    => 'listing',
						'operator' => 'IN',
					],
				],
				'fields'         => 'ids',
				'meta_query'     => [
					[
						'key'     => '_product-expiration',
						'value'   => strtotime( '+3 days', current_time( 'timestamp' ) ),
						'compare' => '<',
					]
				],
			]
		);

		if ( $ad_ids ) {
			foreach ( $ad_ids as $ad_id ) {
				carbon_set_post_meta( $ad_id, 'product-expiration', strtotime( '+30 days', current_time( 'timestamp' ) ) );
				carbon_set_post_meta( $ad_id, 'product-listed', current_time( 'timestamp' ) );

				// extend promotions for the listing.
				$this->extend_promotions( $ad_id );
			}
		}

		// extend premium profiles.
		$this->extend_premium_profiles();
	}

	public function relist_promotions() {
		$this->extend_promotions();
	}

	public function restart_ads_promotions() {
		$ad_ids = get_posts(
			[
				'post_type'      => 'product',
				'post_status'    => 'publish',
				'posts_per_page' => - 1,
				'tax_query'      => [
					[
						'taxonomy' => 'product_type',
						'field'    => 'name',
						'terms'    => 'listing',
						'operator' => 'IN',
					],
				],
				'fields'         => 'ids',
				'meta_query'     => [
					[
						'key'     => '_product-price-type',
						'value'   => 'auction',
						'compare' => '=',
					],
					[
						'key'     => '_product-auction-ends',
						'value'   => strtotime( '+2 hours', current_time( 'timestamp' ) ),
						'compare' => '<',
					],
				],
			]
		);

		if ( $ad_ids ) {
			foreach ( $ad_ids as $ad_id ) {
				carbon_set_post_meta( $ad_id, 'product-auction-ends', strtotime( '+3 hours', current_time( 'timestamp' ) ) );
			}
		}
	}

	/**
	 * Extend expiring promotions on the demo site
	 * -------------------------------------------
	 *
	 * @param $ad_id
	 */
	private function extend_promotions( $ad_id = '' ) {
		$model = new PromotionsModel();
		$date  = date( 'Y-m-d H:i:s', strtotime( '+2 days', current_time( 'timestamp' ) ) );
		if ( ! empty( $ad_id ) ) {
			$promotions = $model->where( [
				[ 'product_id', $ad_id ],
				[ 'type', 'product' ],
				[ 'expires_at', '<', "'{$date}'" ],
				[ 'status', 'active' ],
			] )->get( '', 'ORDER BY created_at DESC', 'id, expires_at' );
		} else {
			$promotions = $model->where( [
				[ 'type', 'product' ],
				[ 'expires_at', '<', "'{$date}'" ],
				[ 'status', 'active' ],
			] )->get( '', 'ORDER BY created_at DESC', 'id, expires_at' );
		}

		if ( ! empty( $promotions ) ) {
			foreach ( $promotions as $promotion ) {
				$model->update_wp( [ 'expires_at' => date( 'Y-m-d H:i:s', strtotime( '+30 days', current_time( 'timestamp' ) ) ) ], [ 'id' => $promotion->id ], [ '%s' ], [ '%s' ] );
			}
		}
	}

	/**
	 * Extend all premium profiles on the demo site
	 * --------------------------------------------
	 */
	public function extend_premium_profiles() {
		$model      = new PromotionsModel();
		$date       = date( 'Y-m-d H:i:s', strtotime( '+3 days', current_time( 'timestamp' ) ) );
		$promotions = $model->where( [
			[ 'type', 'premium_profile' ],
			[ 'expires_at', '<', "'{$date}'" ],
		] )->get( '1', 'ORDER BY id DESC', 'id, expires_at' );

		if ( ! empty( $promotions ) ) {
			foreach ( $promotions as $promotion ) {
				$model->update_wp( [ 'expires_at' => date( 'Y-m-d H:i:s', strtotime( '+30 days', strtotime( $promotion->expires_at ) ) ) ], [ 'id' => $promotion->id ], [ '%s' ], [ '%s' ] );
			}
		}
	}

}

if ( ! function_exists( 'lisfinity_schedules' ) ) {
	/**
	 * Lisfinity Schedules class instance
	 * ----------------------------------
	 *
	 * @return SchedulesModel|null
	 */
	function lisfinity_schedules() {
		return SchedulesModel::instance();
	}
}
