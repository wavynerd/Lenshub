<?php


namespace Lisfinity\REST_API\Users;


use Lisfinity\Abstracts\Route;
use Lisfinity\Models\Notifications\NotificationModel;

class UserRoute extends Route {
	/**
	 * Register Taxonomy Routes
	 * ------------------------
	 *
	 * @var array
	 */
	protected $routes = [
		'user'        => [
			'path'                => '/user',
			'callback'            => 'get_user',
			'permission_callback' => 'allow_access',
			'methods'             => 'POST',
		],
		'user_action' => [
			'rest_path'           => '/user',
			'path'                => '/user/(?P<action>\S+)',
			'callback'            => 'user_action',
			'permission_callback' => 'is_user_logged_in',
			'methods'             => 'POST',
		],
	];

	/**
	 * Get current user
	 * ----------------
	 *
	 * @return bool|object|\stdClass
	 */
	public function get_user() {
		if ( ! is_user_logged_in() ) {
			return false;
		}
		$user               = wp_get_current_user();
		$user->bookmarks    = carbon_get_user_meta( $user->ID, 'bookmarks' );
		$user->ip           = lisfinity_get_ip_address();
		$display_name       = ! empty( $user->first_name ) && ! empty( $user->last_name ) ? "$user->first_name $user->last_name" : $user->display_name;
		$user->display_name = $display_name;
		unset( $user->user_pass );

		return $user->data;
	}

	/**
	 * Do a defined action for a current user
	 * --------------------------------------
	 *
	 * @param \WP_REST_Request $request_data
	 *
	 * @return array|bool
	 */
	public function user_action( \WP_REST_Request $request_data ) {
		$data   = $request_data->get_params();
		$result = [];

		if ( empty( $data['action'] ) ) {
			return false;
		}

		if ( $data['action'] === 'bookmark' ) {
			$result = $this->manage_bookmarks( $data['product_id'] );
		}

		if ( $data['action'] === 'unblock' ) {
			$result = $this->unblock_user( $data['user_id'], $data['user_to_unblock'] );
		}

		if ( $data['action'] === 'subscribe' ) {
			$result = $this->subscribe_user( get_current_user_id(), $data['subscriptions'] );
		}

		return $result;
	}

	protected function subscribe_user( $user_id, $subscriptions ) {
		$result = [];

		if ( ! empty( $subscriptions ) ) {
			foreach ( $subscriptions as $name => $value ) {
				if ( $value ) {
					update_user_meta( $user_id, "_email_subscription|{$name}", 'yes' );
					$result['subscriptions'][ $name ] = true;
				} else {
					delete_user_meta( $user_id, "_email_subscription|{$name}" );
					$result['subscriptions'][ $name ] = false;
				}
			}
		}

		return $result;
	}

	protected function unblock_user( $user_id, $user_to_unblock ) {
		$result              = [];
		$business            = lisfinity_get_premium_profile_id( $user_id );
		$business_to_unblock = lisfinity_get_premium_profile_id( $user_to_unblock );
		$blocked_users       = carbon_get_post_meta( $business, 'blocked-profiles' );
		$to_unblock          = array_search( $business_to_unblock, array_column( $blocked_users, 'id' ) );
		unset( $blocked_users[ $to_unblock ] );
		carbon_set_post_meta( $business, 'blocked-profiles', $blocked_users );

		$result['success'] = true;
		$result['data']    = $blocked_users;

		return $result;
	}

	/**
	 * Manage current user bookmarked products
	 * ---------------------------------------
	 *
	 * @param $product_id
	 *
	 * @return array
	 */
	//todo should be separate table for storing bookmarks in the future.
	private function manage_bookmarks( $product_id ) {
		$result = [];
		if ( empty( $product_id ) ) {
			$result['error']   = true;
			$result['message'] = __( 'The product id has not been set.', 'lisfinity-core' );
		}

		$user_id = get_current_user_id();

		// store bookmark to a user.
		$bookmarks      = carbon_get_user_meta( $user_id, 'bookmarks' );
		$bookmarked_ids = array_column( $bookmarks, 'id' );
		if ( ! in_array( $product_id, $bookmarked_ids ) ) {
			// bookmark product.
			$bookmark_data = [
				'id'      => $product_id,
				'subtype' => 'product',
				'type'    => 'post',
				'value'   => "post:product:{$product_id}"
			];
			$bookmarks[]   = $bookmark_data;

			// store bookmark notification.
			$notification_model = new NotificationModel();
			$is_bookmarked      = $notification_model->where( [
				[ 'user_id', $user_id ],
				[ 'parent_id', $product_id ],
				[ 'parent_type', 4 ],
			] )->get( '1', '', 'id' );
			if ( empty( $is_bookmarked ) ) {
				$business = carbon_get_post_meta( $product_id, 'product-business' );
				// todo integrate emails and notifications here!.
				$business_profile  = lisfinity_get_premium_profile_id( $user_id );
				$notification_data = [
					'user_id'     => $user_id,
					'type'        => 1,
					'product_id'  => $product_id,
					'business_id' => $business,
					'parent_id'   => $business_profile,
					'parent_type' => 4,
					'status'      => 0,
				];

				$notification_model->store_notification( $notification_data );
			}

			$result['success'] = true;
			$result['message'] = __( 'The product has been bookmarked.', 'lisfinity-core' );
			$result['class']   = 'fill-theme';
		} else {
			// remove from bookmarks.
			$remove_key = array_search( $product_id, array_column( $bookmarks, 'id' ) );
			unset( $bookmarks[ $remove_key ] );
			$result['error']   = true;
			$result['message'] = __( 'The product is not bookmarked anymore.', 'lisfinity-core' );
			$result['class']   = 'fill-white';
		}
		$result['bookmarks'] = $bookmarks;

		carbon_set_user_meta( $user_id, 'bookmarks', $bookmarks );

		return $result;
	}
}
