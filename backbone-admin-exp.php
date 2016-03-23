<?php
/**
Plugin Name: Backbone Admin Tabs
 */
add_action( 'init', function(){
	if ( is_admin() ) {
		new JP_BB_Tabs();
	}
});


class JP_BB_Tabs {

	/**
	 * Slug for admin page
	 *
	 * @var string
	 */
	protected $slug = 'jp-bb-tabs';

	/**
	 * Add actions
	 */
	public function __construct(){
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
			'manage_options',
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
				<a href="<?php echo esc_url( $this->tab_url( 'settings' ) ); ?>" class="nav-tab nav-tab-active">
					<?php esc_html_e( 'Settings' ); ?>
				</a>
				<a href="<?php echo esc_url( $this->tab_url( 'info' ) ); ?>" class="nav-tab">
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







