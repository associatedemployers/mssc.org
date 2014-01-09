<?php
/**
 * Feature Name:	Documentation
 * Version:			0.1
 * Author:			Inpsyde GmbH
 * Author URI:		http://inpsyde.com
 * Licence:			GPLv3
 */

require_once( ABSPATH . 'wp-includes/pluggable.php' );

class BackWPup_Pro_Marketpress_Documentation {

	/**
	 * Instance holder
	 *
	 * @since	0.1
	 * @var		NULL | __CLASS__
	 */
	private static $instance = NULL;

	/**
	 * The parent product name
	 *
	 * @since	0.1
	 * @var		array
	 */
	public static $product_name = '';

	/**
	 * The textdomain for translations
	 *
	 * @since	0.1
	 * @var		string
	 */
	public static $textdomain = '';

	/**
	 * The URL for the menu
	 *
	 * @since	0.1
	 * @var		string
	 */
	public static $url_menu = '';

	/**
	 * The URL for the content
	 *
	 * @since	0.1
	 * @var		string
	 */
	public static $url_content = '';

	/**
	 * The product basename
	 *
	 * @since	0.1
	 * @var		string
	 */
	public static $product_base_name = '';

	/**
	 * The product filepath
	 *
	 * @since	0.1
	 * @var		string
	 */
	public static $product_filepath = '';

	/**
	 * Method for ensuring that only one instance of this object is used
	 *
	 * @since	0.1
	 * @return	__CLASS__
	 */
	public static function get_instance() {

		if ( ! self::$instance )
			self::$instance = new self;

		return self::$instance;
	}

	/**
	 * Setting up some data, all vars and start the hooks
	 *
	 * @since	0.1
	 * @uses	sanitize_title_with_dashes, add_filter
	 * @return	void
	 */
	public function __construct( $product_name = '', $product_filepath = '' ) {

		// Setting up Plugin identifier and textdomain
		self::$product_filepath = $product_filepath;

		// Setting up Plugin identifier and textdomain
		self::$product_name = sanitize_title_with_dashes( $product_name );
		self::$textdomain = sanitize_title_with_dashes( $this->get_plugin_header() );
		self::$product_base_name = plugin_basename( self::$product_filepath );

		$locale = get_locale();
		switch( $locale ) {
			case 'de_DE':
				$domain = 'de';
				break;
			default:
				$domain = 'com';
				break;
		}

		// Setting up the license checkup URL
		self::$url_menu = 'http://marketpress.' . $domain . '/mp-doc/' . self::$product_name . '/menu/';
		self::$url_content = 'http://marketpress.' . $domain . '/mp-doc/' . self::$product_name;

		// Load Menu Ajax
		add_filter( 'wp_ajax_load_marketpress_documentation_menu', array( $this, 'load_documentation_menu' ) );
		// Load Content Ajax
		add_filter( 'wp_ajax_load_marketpress_documentation_content', array( $this, 'load_documentation_content' ) );
	}

	/**
	 * Get a value of the plugin header
	 *
	 * @since	0.1
	 * @uses	get_plugin_data, ABSPATH
	 * @param	string $value
	 * @return	string The plugin header value
	 */
	protected function get_plugin_header( $value = 'TextDomain' ) {

		if ( ! function_exists( 'get_plugin_data' ) )
			require_once( ABSPATH . '/wp-admin/includes/plugin.php' );

		$plugin_data = get_plugin_data( self::$product_filepath );
		$plugin_value = $plugin_data[ $value ];
		return $plugin_value;
	}

	/**
	 * Shows the documentation page
	 *
	 * @since	0.1
	 * @uses	_e
	 * @return	void
	 */
	public function documentation_page() {
		?>
		<div class="wrap">
			<h2><span id="backwpup-page-icon">&nbsp;</span><?php echo $this->get_plugin_header( 'Name' ); ?> <?php _e( 'Documentation', self::$textdomain ); ?></h2>

			<div id="poststuff" class="metabox-holder has-right-sidebar">

					<div id="post-body">
						<div id="post-body-content">
							<div id="documentation_content">
								<?php $this->build_content(); ?>
							</div>
						</div>
					</div>

				</div>
		</div>
		<?php
	}

	/**
	 * Builds the menu
	 *
	 * @since	0.1
	 * @uses	admin_url, _e
	 * @return	void
	 */
	public function build_menu() {
		?>
		<p><img src="<?php echo admin_url( 'images/wpspin_light.gif' ); ?>" class="alignleft" style="margin-right: 5px;" /><?php _e( 'Loading Menu ...', self::$textdomain ); ?></p>

		<script type="text/javascript">
		( function( $ ) {
			var documentation = {
				init : function () {
					documentation.load_menu();
				},

				load_menu : function() {
					var post_vars = {
						action: 'load_marketpress_documentation_menu'
					};

					$.post( ajaxurl, post_vars, function( response ) {
						$( '#documentation_menu' ).html( response ); }
					);
				}
			};
			$( document ).ready( function( $ ) {
				documentation.init();
			} );
		} )( jQuery );
		</script>
		<?php
	}

	/**
	 * Loads the documentation menu and
	 * parses the json to html
	 *
	 * @since	0.1
	 * @uses
	 * @return	void
	 */
	public function load_documentation_menu() {

		// Connect to our remote host
		$remote = wp_remote_get( self::$url_menu );

		// check for response code 200
		$code = wp_remote_retrieve_response_code( $remote );
		if ( 200 !== $code ) {
			$msg = _x(
				'Could not connect to remote host, code %d. Please try again later.',
				'%s = Remote Code',
				self::$textdomain
			);
			printf( $msg, $code );
			exit;
		}

		// check for empty code
		$body = wp_remote_retrieve_body( $remote );
		if ( empty( $body ) ) {
			_e( 'Could not find content for this page. Please try again later.' , self::$textdomain );
			exit;
		}

		// If wp error
		if ( is_wp_error( $remote ) ) {
			_e( 'Could not connect to remote host. Please try again later.', self::$textdomain );
			exit;
		}
		// Load the response
		$response = json_decode( wp_remote_retrieve_body( $remote ) );
		if ( ! empty( $response ) )
			$this->parse_menu( $response );

		exit;
	}

	/**
	 * Parses the json to HTML
	 *
	 * @since	0.1
	 * @uses
	 * @return	void
	 */
	public function parse_menu( $menu ) {

		?>
		<ul style="margin: 0; padding: 0; list-style: disc;">
			<?php foreach ( $menu as $menu_point ) { ?>
				<li style="margin: 0 0 0 20px; padding: 3px;">
					<a href="#" class="load-documentation" pageid="<?php echo $menu_point->id; ?>">
						<?php echo $menu_point->title; ?>
					</a>

					<?php if ( isset( $menu_point->sub ) && ! empty( $menu_point->sub ) ) { ?>
						<?php $this->parse_menu( $menu_point->sub ); ?>
					<?php } ?>
				</li>
			<?php } ?>
		</ul>
		<?php
	}

	/**
	 * Builds the content
	 *
	 * @since	0.1
	 * @uses	admin_url, _e
	 * @return	void
	 */
	public function build_content() {
		?>
		<p><img src="<?php echo admin_url( 'images/wpspin_light.gif' ); ?>" class="alignleft" style="margin-right: 5px;" /><?php _e( 'Loading Content ...', self::$textdomain ); ?></p>

		<script type="text/javascript">
		( function( $ ) {
			var documentation_content = {
				init : function () {
					documentation_content.load_content( <?php echo isset( $_GET[ 'docname' ] ) && $_GET[ 'docname' ] != '' ? '"' . $_GET[ 'docname' ] . '"' : '0'; ?> );
					$( '.load-documentation' ).live( 'click', function() {
						$( '#documentation_content' ).html( '<p><img src="<?php echo admin_url( 'images/wpspin_light.gif' ); ?>" class="alignleft" style="margin-right: 5px;" /><?php _e( 'Loading Content ...', self::$textdomain ); ?></p>' );
						documentation_content.load_content( $( this ).attr( 'pageid' ) );
						return false;
					} );
				},

				load_content : function( pageid ) {
					var post_vars = {
						pageid: pageid,
						action: 'load_marketpress_documentation_content'
					};

					$.post( ajaxurl, post_vars, function( response ) {
						$( '#documentation_content' ).html( response );
					} );
				}
			};
			$( document ).ready( function( $ ) {
				documentation_content.init();
			} );
		} )( jQuery );
		</script>
		<?php
	}

	/**
	 * Loads the documentation menu and
	 * parses the json to html
	 *
	 * @since	0.1
	 * @uses
	 * @return	void
	 */
	public function load_documentation_content() {
		// Check Pageid
		if ( isset( $_REQUEST[ 'pageid' ] ) && $_REQUEST[ 'pageid' ] != '0' )
			self::$url_content .= '/' . $_REQUEST[ 'pageid' ] . '/';

		// Connect to our remote host
		$remote = wp_remote_get( self::$url_content );

		// check for response code 200
		$code = wp_remote_retrieve_response_code( $remote );
		if ( 200 !== $code ) {
			$msg = _x(
				'Could not connect to remote host, code %d. Please try again later.',
				'%s = Remote Code',
				self::$textdomain
			);
			printf( $msg, $code );
			exit;
		}

		// check for empty code
		$body = wp_remote_retrieve_body( $remote );
		if ( empty( $body ) ) {
			_e( 'Could not find content for this page. Please try again later.' , self::$textdomain );
			exit;
		}
		// If wp error
		if ( is_wp_error( $remote ) ) {
			_e( 'Could not connect to remote host. Please try again later.', self::$textdomain );
			exit;
		}

		// Load the response
		$response = wp_remote_retrieve_body( $remote );
		echo $response;

		exit;
	}
}