<?php
/**
 * Constructive API
 *
 * @package constructive-api
 *
 * @wordpress-plugin
 * Plugin Name: Constructive API
 * Description: Provides an API to expose the 10 most recent posts.
 * Version: 0.0.1
 * Author: Scott Sawyer
 * Text Domain: constructive-api
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Constructive_API' ) ) {

	/**
	 * Provides the Constructive API class.
	 */
	class Constructive_API {

		/**
		 * Constructs a new Constructive API object.
		 */
		public function __construct() {
			add_action( 'rest_api_init', array( $this, 'registerRoute' ) );
		}

		/**
		 * Expose most recent 10 posts.
		 *
		 * @return array An array of posts.
		 */
		public function getRecentPosts() {
			$post_data = array();
			$args = array(
				'posts_per_page'   => 10,
				'status'           => 'publish',
			);
			$posts = get_posts( $args );
			if ( ! empty( $posts ) ) {
				foreach ( $posts as $post ) {
					$post_data[] = [
						'id'        => $post->ID,
						'title'     => $post->post_title,
						'published' => $post->post_date,
						'body'      => $post->post_content,
					];
				}
			}
			return $post_data;
		}

		/**
		 * Register route for endpoint.
		 */
		public function registerRoute() {
			register_rest_route( 'constructive-api/v1', '/posts', array(
				'methods' => 'GET',
				'callback' => array( $this, 'getRecentPosts' ),
				'permission_callback' => '__return_true',
			));
		}
	}

}

new Constructive_API();