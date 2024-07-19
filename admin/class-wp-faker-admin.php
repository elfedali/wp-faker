	<?php

	/**
	 * The admin-specific functionality of the plugin.
	 *
	 * @link       https://codewithabdessamad.ma
	 * @since      1.0.0
	 *
	 * @package    Wp_Faker
	 * @subpackage Wp_Faker/admin
	 */

	/**
	 * The admin-specific functionality of the plugin.
	 *
	 * Defines the plugin name, version, and two examples hooks for how to
	 * enqueue the admin-specific stylesheet and JavaScript.
	 *
	 * @package    Wp_Faker
	 * @subpackage Wp_Faker/admin
	 * @author     Code with abdessamad <a.elfedali@gmail.com>
	 */
	class Wp_Faker_Admin
	{

		/**
		 * The ID of this plugin.
		 *
		 * @since    1.0.0
		 * @access   private
		 * @var      string    $plugin_name    The ID of this plugin.
		 */
		private $plugin_name;

		/**
		 * The version of this plugin.
		 *
		 * @since    1.0.0
		 * @access   private
		 * @var      string    $version    The current version of this plugin.
		 */
		private $version;

		/**
		 * Initialize the class and set its properties.
		 *
		 * @since    1.0.0
		 * @param      string    $plugin_name       The name of this plugin.
		 * @param      string    $version    The version of this plugin.
		 */
		public function __construct($plugin_name, $version)
		{

			$this->plugin_name = $plugin_name;
			$this->version = $version;
		}

		/**
		 * Register the stylesheets for the admin area.
		 *
		 * @since    1.0.0
		 */
		public function enqueue_styles()
		{

			/**
			 * This function is provided for demonstration purposes only.
			 *
			 * An instance of this class should be passed to the run() function
			 * defined in Wp_Faker_Loader as all of the hooks are defined
			 * in that particular class.
			 *
			 * The Wp_Faker_Loader will then create the relationship
			 * between the defined hooks and the functions defined in this
			 * class.
			 */

			wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/wp-faker-admin.css', array(), $this->version, 'all');
		}

		/**
		 * Register the JavaScript for the admin area.
		 *
		 * @since    1.0.0
		 */
		public function enqueue_scripts()
		{

			/**
			 * This function is provided for demonstration purposes only.
			 *
			 * An instance of this class should be passed to the run() function
			 * defined in Wp_Faker_Loader as all of the hooks are defined
			 * in that particular class.
			 *
			 * The Wp_Faker_Loader will then create the relationship
			 * between the defined hooks and the functions defined in this
			 * class.
			 */

			wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/wp-faker-admin.js', array('jquery'), $this->version, false);
		}

		/**
		 * Create admin menu
		 */
		public function navigation()
		{

			add_menu_page(
				'Wp Faker',
				'Wp Faker',
				'manage_options',
				'wp-faker',
				array($this, 'wp_faker_admin_page'),
				'dashicons-admin-generic',
				6
			);
		}
		/**
		 *  
		 * This file is used to markup the admin-facing aspects of the plugin.
		 */
		public function wp_faker_admin_page()
		{

			include_once 'partials/wp-faker-admin-display.php';
		}

		/**
		 * Register settings, sections and fields
		 */
		public function settings()
		{
			register_setting('wp_faker_options', 'wp_faker_options', array());

			add_settings_section(
				'wp_faker_options_section',
				__('Fake posts', 'wp-faker'),
				function () {

					echo 'Settings for the plugin.';
				},
				'wp_faker_options',
			);

			add_settings_field(
				'wp_faker_options_count_field',
				__('Number of posts to create', 'wp-faker'),
				function () {

	?>

				<input type="number" required min=1 name="wp_faker_options[count]" value="<?php echo get_option('wp_faker_options')['count']; ?>">
	<?php
				},
				'wp_faker_options',
				'wp_faker_options_section',
			);
		}


		public function handle_form_submission()
		{


			// Check user capabilities
			if (!current_user_can('manage_options')) {
				return;
			}

			// Check nonce for security
			check_admin_referer('wp_faker_generate_posts_nonce');

			// Get the number of posts to create
			$num_posts = isset($_POST['wp_faker_options']['count']) ? intval($_POST['wp_faker_options']['count']) : 0;

			if ($num_posts > 0) {
				$this->generate_fake_posts($num_posts);
			}

			// Redirect back to the settings page
			wp_redirect(admin_url('admin.php?page=wp-faker&status=success'));
			exit;
		}

		/**
		 * Generate fake posts
		 */
		private function generate_fake_posts($num_posts)
		{
			if (!class_exists('Faker\Factory')) {
				require_once plugin_dir_path(__FILE__) . '../vendor/autoload.php';
			}

			$faker = Faker\Factory::create();

			for ($i = 0; $i < $num_posts; $i++) {
				$image_url = $faker->imageUrl(200, 150, 'cats', true);

				$post_data = array(
					'post_title' => $faker->catchPhrase(),
					'post_content' => $faker->realText(400, 4),
					'post_excerpt' => $faker->realText(150, 2),
					'post_type' => 'post', // 'page'
					'post_date' => $faker->date($format = 'Y-m-d', $max = 'now'),
					'post_status' => 'publish',
					'post_author' => get_current_user_id(),
				);

				$post_id  = wp_insert_post($post_data);
				// save the id of the post in the option table
				$option_name = 'wp_faker_post_ids';
				$option_value = get_option($option_name, array());
				$option_value[] = $post_id;
				update_option($option_name, $option_value);

				if (!is_wp_error($post_id)) {
					$image_id = $this->wp_faker_upload_image_from_url($image_url);
					if (!is_wp_error($image_id)) {
						set_post_thumbnail($post_id, $image_id);
					}
				}
			}
		}
		/**
		 * Upload an image from a URL and return the attachment ID
		 */

		function wp_faker_upload_image_from_url($url)
		{
			$image_name = Faker\Factory::create()->slug(2) . '.png';
			$upload_dir = wp_upload_dir();
			$image_data = file_get_contents($url);


			if ($image_data === false) {
				return new WP_Error('image_upload_failed', 'Failed to fetch the image from URL');
			}

			$unique_file_name = wp_unique_filename($upload_dir['path'], $image_name);
			$file = $upload_dir['path'] . '/' . $unique_file_name;

			if (!file_put_contents($file, $image_data)) {
				return new WP_Error('image_upload_failed', 'Failed to write the image file');
			}

			$wp_filetype = wp_check_filetype($unique_file_name, null);
			$attachment = array(
				'post_mime_type' => $wp_filetype['type'],
				'post_title'     => sanitize_file_name($unique_file_name),
				'post_content'   => '',
				'post_status'    => 'inherit'
			);

			$attach_id = wp_insert_attachment($attachment, $file);
			require_once(ABSPATH . 'wp-admin/includes/image.php');
			$attach_data = wp_generate_attachment_metadata($attach_id, $file);
			wp_update_attachment_metadata($attach_id, $attach_data);

			return $attach_id;
		}


		public function handle_delete_form_submission()
		{
			// Check user capabilities
			if (!current_user_can('manage_options')) {
				return;
			}

			// Check nonce for security
			check_admin_referer('wp_faker_delete_posts_nonce');

			// Get all posts
			$posts = get_option('wp_faker_post_ids', array());
			if (empty($posts)) {
				return;
			}




			// Delete all posts
			foreach ($posts as $post) {
				wp_delete_post($post->ID, true);
			}

			// Redirect back to the settings page
			wp_redirect(admin_url('admin.php?page=wp-faker&status=deleted'));
			exit;
		}
	}
