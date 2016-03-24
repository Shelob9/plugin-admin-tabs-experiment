<?php
/**
Plugin Name: Backbone Admin Tabs
 */
add_action( 'init', function(){
	if ( is_admin() ) {
		new JP_BB_Tabs( 'manage_options' );
	}
});

add_action( 'rest_api_init', function(){
	new JP_BB_Route( 'manage_options' );
});




class JP_BB_Tabs {

	/**
	 * Slug for admin page
	 *
	 * @var string
	 */
	protected $slug = 'jp-bb-tabs';

	/**
	 * Capability for menu page
	 *
	 * @var string
	 */
	protected $cap;



	/**
	 * Add actions
	 */
	public function __construct( $cap ){
		$this->cap = $cap;
		add_action( 'admin_menu', [ $this, 'add_page' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'scripts' ] );
	}

	/**
	 * Load scripts
	 *
	 * @uses "admin_enqueue_scripts" action
	 *
	 * @param string $hook
	 */
	public function scripts( $hook ){
		if ( 'toplevel_page_' . $this->slug == $hook  ) {
			wp_enqueue_script( 'jp-bb-exp', plugin_dir_url( __FILE__ ) . '/assets/js/admin.js', [
				'backbone',
				'jquery',
				'underscore'
			] );

			wp_localize_script( 'jp-bb-exp', 'JP_BB_VARS', array( 'root' => esc_url_raw( rest_url( '/jp-bb/v1/settings' ) ), 'nonce' => wp_create_nonce( 'wp_rest' ) ) );
		}
	}

	/**
	 * Add menu page
	 *
	 * @uses "admin_menu" hook
	 */
	public function add_page(){
		add_menu_page(
			__( 'Backbone Tabs Experiment', 'textdomain' ),
			'Backbone Exp',
			$this->cap,
			$this->slug,
			[ $this, 'page' ],
			false,
			6
		);
	}

	/**
	 * Output HTML for the admin page
	 */
	public function page(){
		?>
		<div class="wrap">
			<h2 class="nav-tab-wrapper">
				<a href="<?php echo esc_url( $this->tab_url( 'settings' ) ); ?>" class="nav-tab nav-tab-active" id="summary-tab">
					<?php esc_html_e( 'Settings' ); ?>
				</a>
				<a href="<?php echo esc_url( $this->tab_url( 'info' ) ); ?>" class="nav-tab" id="info-tab">
					<?php esc_html_e( 'Information' ); ?>
				</a>
			</h2>
			<div id="tab_container">
			</div>
		</div>

		<?php
		$this->load_templates();
	}

	/**
	 * Create a URL for a tab
	 *
	 * @param string $tab
	 *
	 * @return string
	 */
	public function tab_url( $tab ){
		$location = sprintf( 'admin.php?page=%s#tab/%s', $this->slug, sanitize_title( $tab ) );
		return admin_url( $location );
	}


	/**
	 * Output templates
	 */
	public function load_templates(  ){
		include dirname( __FILE__ ) . '/templates/settings.html';
		include dirname( __FILE__  ) . '/templates/info.html';
	}


}

/**
 * Add API route/endpoint
 */
class JP_BB_Route {

	/**
	 * Capability for menu page
	 *
	 * @var string
	 */
	protected $cap;

	protected $prefix = 'jpbb_';

	public function __construct( $cap ){
		$this->cap = $cap;
		$this->add_route();
	}

	/**
	 * Add routes
	 */
	public function add_route(){
		register_rest_route( '/jp-bb/v1', 'settings', [
				[
					'methods'             => 'GET',
					'permission_callback' => function () {
						return current_user_can( $this->cap );
					},
					'callback'            => [ $this, 'get_settings' ]
				],
				[
					'methods'             => 'POST',
					'permission_callback' => function () {
						return current_user_can( $this->cap );
					},
					'callback'            => [ $this, 'update_settings' ],
					'args' => $this->fields()
				]
			]

		);


	}

	/**
	 * Get settings
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return \WP_REST_Response
	 */
	public function get_settings( WP_REST_Request $request ){
		return rest_ensure_response( $this->current_settings() );
	}

	/**
	 * Update settings
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return \WP_REST_Response
	 */
	public function update_settings( WP_REST_Request $request ){
		$current = $this->current_settings();

		foreach( $this->fields() as $field => $options ){
			$new_value = $request->get_param( $field );
			if( $new_value != $current[ $field ] ){
				update_option( $this->prefix . $field, $new_value );
			}
		}

		return rest_ensure_response( $this->current_settings() );
	}

	/**
	 * Get current settings
	 *
	 * @return array
	 */
 	protected function current_settings(){
	    $settings = [];
	    foreach( $this->fields() as $field => $options ){
		   $settings[ $field ] = get_option( $this->prefix . $field, $options[ 'default' ] );
	    }

	    return $settings;

	}

	/**
	 * Sanatize string
	 *
	 * @param $value
	 *
	 * @return string
	 */
	public function safe_string( $value ){
		return sanitize_text_field( trim( $value ) );
	}

	/**
	 * Settings fields
	 *
	 * @return array
	 */
	protected function fields() {
		return [
			'postType' => [
				'default'           => 'post',
				'sanitize_callback' => [ $this, 'safe_string' ],
			],
			'postID'   => [
				'default'           => 1,
				'sanitize_callback' => 'absint',
			]
		];
	}
}





