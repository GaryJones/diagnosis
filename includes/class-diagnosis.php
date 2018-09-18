<?php
/**
 * Diagnosis
 *
 * @package   Diagnosis
 * @author    Gary Jones
 * @link      https://garyjones.io
 * @copyright 2014 Gary Jones
 * @license   GPL-2.0-or-later
 */

/**
 * Plugin class.
 *
 * @package Diagnosis
 * @author  Gary Jones
 */
class Diagnosis {
	/**
	 * Page-hook for the Diagnosis page.
	 *
	 * @since 2.1.0
	 *
	 * @var string
	 */
	public $pagehook = null;

	/**
	 * Page-hook for the PHPInfo page.
	 *
	 * @since 2.1.0
	 *
	 * @var string
	 */
	public $phpinfo_pagehook = null;

	/**
	 * Attach hooks.
	 *
	 * @since 3.0.0
	 */
	public function run() {
		add_theme_support( 'diagnosis-menu' );
		add_theme_support( 'diagnosis-phpinfo-menu' );
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
		add_action( 'admin_menu', array( $this, 'menu' ) );
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since 3.0.0
	 */
	public function load_plugin_textdomain() {
		$domain = 'diagnosis';
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, FALSE, basename( dirname( __FILE__ ) ) . '/languages' );
	}

	/**
	 * Add Diagnosis as submenu item to Dashboard if role has diagnosis_read capability.
	 *
	 * If at least one page is added, initialise the rest of the plugin.
	 *
	 * @since 2.0.0
	 */
	public function menu() {

		// Add capability to administrator role
		$role = get_role( 'administrator' );
		$role->add_cap( 'diagnosis_read' );

		if ( current_theme_supports( 'diagnosis-menu' ) ) {
			$this->pagehook = add_dashboard_page(
				__( 'Diagnosis', 'diagnosis' ),
				__( 'Diagnosis', 'diagnosis' ),
				'diagnosis_read',
				'diagnosis',
				array( $this, 'diagnosis_page' )
			);
		}

		if ( current_theme_supports( 'diagnosis-phpinfo-menu' ) ) {
			$this->phpinfo_pagehook = add_dashboard_page(
				__( 'PHP Info', 'diagnosis' ),
				__( 'PHP Info', 'diagnosis' ),
				'diagnosis_read',
				'diagnosis_phpinfo',
				array( $this, 'phpinfo_page' )
			);
		}

		if ( $this->pagehook || $this->phpinfo_pagehook ) {
			add_action( 'admin_init', array( $this, 'init' ) );
		}

	}

	/**
	 * Initialise the plugin.
	 *
	 * @since 2.0.1
	 */
	public function init() {
		add_action( 'admin_print_styles-' . $this->pagehook, array( $this, 'styles' ) );
		add_action( 'admin_print_styles-' . $this->phpinfo_pagehook, array( $this, 'styles' ) );
		add_action( 'diagnosis_page', array( $this, 'section_server_information' ) );
		add_action( 'diagnosis_page', array( $this, 'section_php_information' ) );
		add_action( 'diagnosis_page', array( $this, 'section_mysql_information' ) );
		add_action( 'diagnosis_page', array( $this, 'section_mysql_load' ) );
		add_action( 'diagnosis_page', array( $this, 'section_mysql_encodings' ) );
		add_action( 'diagnosis_page', array( $this, 'section_mysql_engines' ) );
		add_action( 'diagnosis_phpinfo_page', array( $this, 'section_phpinfo' ) );
	}

	/**
	 * Add style sheet reference.
	 *
	 * Only loaded on Diagnosis admin screen.
	 *
	 * @since 2.0.0
	 */
	public function styles() {
		wp_enqueue_style(
			'diagnosis',
			plugins_url( 'css/diagnosis.css', plugin_dir_path( dirname( __FILE__ ) ) . '/diagnosis.php' ),
			array(),
			filemtime( plugin_dir_path( dirname( __FILE__ ) ) . 'css/diagnosis.css' )
		);
	}

	/**
	 * Callback for Diagnosis page wrapper.
	 *
	 * @since 2.0.0
	 */
	public function diagnosis_page() {

		?>
		<div class="wrap diagnosis">
			<h2><?php _e( 'Useful Information About Your Setup', 'diagnosis' ); ?></h2>
			<p><?php _e( 'Here you can find information about the system that is currently powering your WordPress installation.', 'diagnosis' ); ?></p>
		<?php do_action( 'diagnosis_page' ); ?>
		</div>
		<?php

	}

	/**
	 * Add Server Information section.
	 *
	 * @since 2.0.0
	 *
	 * @global string $wp_version
	 * @global string $wp_db_version
	 * @global string $required_php_version
	 * @global string $required_mysql_version
	 */
	public function section_server_information() {
		global $wp_version, $wp_db_version, $required_php_version, $required_mysql_version;

		$rows = array(
			array(
				__( 'Current WordPress version', 'diagnosis' ),
				$wp_version,
				__( 'The version of the current WordPress installation.', 'diagnosis' ),
				__( 'http://codex.wordpress.org/WordPress_Versions', 'diagnosis' )
			),
			array(
				__( 'Current WordPress database version', 'diagnosis' ),
				$wp_db_version,
				__( 'Increments when changes are made to the WordPress DB schema.', 'diagnosis' ),
				__( 'http://codex.wordpress.org/Database_Description', 'diagnosis' )
			),
			array(
				__( 'Server operating system', 'diagnosis' ),
				php_uname( 's' ),
				__( 'The operating system currently running on the server.', 'diagnosis' ),
				__( 'http://en.wikipedia.org/wiki/Operating_system', 'diagnosis' )
			),
			array(
				__( 'Current version of PHP', 'diagnosis' ),
				'PHP ' . phpversion(),
				__( 'The current version of PHP used on this server.', 'diagnosis' ),
				__( 'http://www.php.net/', 'diagnosis' )
			),
			array(
				__( 'Required version of PHP', 'diagnosis' ),
				'PHP ' . $required_php_version,
				__( 'The minimum version of PHP required to run WordPress.', 'diagnosis' ),
				__( 'http://wordpress.org/about/requirements/', 'diagnosis' )
			),
			array(
				__( 'Current version of MySQL', 'diagnosis' ),
				'MySQL ' . $this->get_mysql_variable( 'version' ),
				__( 'The current version of MySQL used by WordPress.', 'diagnosis' ),
				__( 'http://dev.mysql.com/', 'diagnosis' )
			),
			array(
				__( 'Required version of MySQL', 'diagnosis' ),
				'MySQL ' . $required_mysql_version,
				__( 'The minimum version of MySQL required to run WordPress.', 'diagnosis' ),
				__( 'http://wordpress.org/about/requirements/', 'diagnosis' )
			),
			array(
				__( 'Web server software', 'diagnosis' ),
				$_SERVER['SERVER_SOFTWARE'],
				__( 'The name of the <em>web server</em>, the computer program that serves the pages to the users.', 'diagnosis' ),
				__( 'http://en.wikipedia.org/wiki/Web_server', 'diagnosis' )
			),
			array(
				__( 'Web server <abbr title="Internet Protocol">IP</abbr> address', 'diagnosis' ),
				$_SERVER['SERVER_ADDR'],
				__( 'A unique number that identifies the web server to other computers on the internet and the local network.', 'diagnosis' ),
				__( 'http://en.wikipedia.org/wiki/IP_address', 'diagnosis' )
			),
			array(
				__( 'Web server port number', 'diagnosis' ),
				$_SERVER['SERVER_PORT'],
				__( 'Which port that is used by the web server to send pages and receive requests (usually <em>80</em>).', 'diagnosis' ),
				__( 'http://en.wikipedia.org/wiki/Port_number', 'diagnosis' )
			),
			array(
				__( 'MySQL server <abbr title="Internet Protocol">IP</abbr> address', 'diagnosis' ),
				$_SERVER['SERVER_ADDR'],
				__( 'The IP address or hostname for the MySQL server WordPress is using.', 'diagnosis' ),
				__( 'http://en.wikipedia.org/wiki/IP_address', 'diagnosis' )
			),
			array(
				__( 'MySQL database name', 'diagnosis' ),
				DB_NAME,
				__( 'The name of the database where all data within WordPress is stored.', 'diagnosis' )
			),
			array(
				__( 'MySQL database user', 'diagnosis' ),
				DB_USER,
				__( 'The username WordPress uses to authorize itself against the MySQL server.', 'diagnosis' )
			),
			array(
				__( 'Domain name', 'diagnosis' ),
				$_SERVER['SERVER_NAME'],
				__( 'A part of the address the visitors enter in order to access your page.', 'diagnosis' ),
				__( 'http://en.wikipedia.org/wiki/Domain_name', 'diagnosis' )
			),
			array(
				__( 'Web server document root', 'diagnosis' ),
				$_SERVER['DOCUMENT_ROOT'],
				__( 'Where on the web server your web pages and Wordpress are placed.', 'diagnosis' )
			),
			array(
				__( 'Current time (<abbr title="International Organization for Standardization">ISO</abbr> 861)', 'diagnosis' ),
				date( 'c' ),
				__( 'The current time and date on the server expressed in the ISO 8601 format.', 'diagnosis' ),
				__( 'http://en.wikipedia.org/wiki/ISO_8601', 'diagnosis' )
			),
			array(
				__( 'Current time (<abbr title="Request for comments">RFC</abbr> 2822)', 'diagnosis' ),
				date( 'r' ),
				__( 'The current time and date on the server expressed in the RFC 2822 format.', 'diagnosis' ),
				__( 'http://tools.ietf.org/html/3339', 'diagnosis' )
			),
		);

		$this->add_heading( __( 'Server Information', 'diagnosis' ) );
		$this->add_table( array( __( 'Variable', 'diagnosis' ), __( 'Value', 'diagnosis' ) ), $rows );
	}

	/**
	 * Add PHP Information section.
	 *
	 * @since 2.0.0
	 */
	public function section_php_information() {
		$loaded_extensions = implode( ', ', get_loaded_extensions() );

		$rows = array(
			array(
				__( 'Loaded Extensions', 'diagnosis' ),
				$loaded_extensions,
				__( 'An extension adds extra features or functions to PHP.', 'diagnosis' )
			),
			array(
				__( 'Display errors', 'diagnosis' ),
				$this->get_php_configuration_boolean( 'display_errors' ),
				__( 'Whether PHP is configured to display errors or not.', 'diagnosis' )
			),
			array(
				__( 'Register globals', 'diagnosis' ),
				$this->get_php_configuration_boolean( 'register_globals' ),
				__( 'Whether PHP is configured to accept register globals. This is known to possibly cause security problems for scripts. It should <strong>not</strong> be activated.', 'diagnosis' )
			),
			array(
				__( 'Allow <code>url_fopen</code>', 'diagnosis' ),
				$this->get_php_configuration_boolean( 'allow_url_fopen' ),
				__( 'Whether to allow the treatment of URLs (like http:// or ftp://) as files.', 'diagnosis' )
			),
			array(
				__( 'Register globals', 'diagnosis' ),
				$this->get_php_configuration_boolean( 'expose_php' ),
				__( 'Whether the web server should expose to the world that it is running PHP. If turned off (<em>No</em>) the web server hides some typical PHP exposures.', 'diagnosis' )
			),
		);

		$this->add_heading( sprintf( __( 'PHP %s Information', 'diagnosis' ), phpversion() ) );
		$this->add_table( array( __( 'Variable', 'diagnosis' ), __( 'Value', 'diagnosis' ) ), $rows );
	}

	/**
	 * Adds MySQL Information section.
	 *
	 * @since 2.0.0
	 *
	 * @todo Make mysql.com links use correct version
	 */
	public function section_mysql_information() {
		$rows = array(
			array(
				__( 'Storage engine used', 'diagnosis' )
				, $this->get_mysql_variable( 'table_type' )
				, __( 'The storage engine currently in use by MySQL.', 'diagnosis' )
				, __( 'http://dev.mysql.com/doc/refman/5.1/en/storage-engines.html', 'diagnosis' )
			),
			array(
				__( 'Large File Support', 'diagnosis' )
				, $this->get_mysql_variable( 'large_files_support' )
				, __( 'Whether MySQL has the option for <em>large file support</em> on or off.', 'diagnosis' )
				, __( 'http://mirrors.dotsrc.org/mysql/doc/refman/5.1/en/table-size.html', 'diagnosis' )
			),
		);

		if ( 'YES' == $this->get_mysql_variable( 'have_bdb' ) )
			$rows[] = array(
				__( 'Berkeley DB Version', 'diagnosis' )
				, $this->get_mysql_variable( 'version_bdb' )
				, __( 'The current version of Berkeley DB (<abbr title="Berkeley Database">BDB</abbr>) in use by MySQL.', 'diagnosis' )
				, __( 'http://dev.mysql.com/doc/refman/5.1/en/bdb-storage-engine.html', 'diagnosis' )
			);

		$this->add_heading( sprintf( __( 'MySQL %s Information', 'diagnosis' ), $this->get_mysql_variable( 'version' ) ) );
		$this->add_table( array( __( 'Variable', 'diagnosis' ), __( 'Value', 'diagnosis' ) ), $rows );
	}

	/**
	 * Adds MySQL Database Load section.
	 *
	 * @since 2.0.0
	 *
	 * @todo Don't split uptime string
	 */
	public function section_mysql_load() {
		$ratio  = $this->get_mysql_status( 'Aborted_connects' ) / $this->get_mysql_status( 'Connections' );
		$percent = round( 100 * ( 1 - $ratio ), 2 );

		$time    = $this->get_mysql_status( 'uptime' );
		$days    = (int) floor( $time / 86400 );
		$hours   = (int) floor( $time / 3600 ) % 24;
		$minutes = (int) floor( $time / 60 ) % 60;
		$uptime  = $days . ' ' . _n( 'day', 'days', $days, 'diagnosis' ) . ', '
			. $hours . ' ' . _n( 'hour', 'hours', $hours, 'diagnosis' ) . ' ' . __( 'and', 'diagnosis' ) . ' '
			. $minutes . ' ' . _n( 'minute', 'minutes', $minutes, 'diagnosis' );

		$rows = array(
			array(
				__( 'Uptime', 'diagnosis' ),
				$uptime,
				__( 'Length of time since the MySQL server was last restarted.', 'diagnosis' )
			),
			array(
				__( 'Queries per second', 'diagnosis' ),
				$this->get_mysql_statistics( 'Queries', 'seconds' ),
				__( 'The number of queries the database server has received per second on average since it started. The higher the number, the higher the load.', 'diagnosis' )
			),
			array(
				__( 'Connections per minute', 'diagnosis' ),
				$this->get_mysql_statistics( 'Connections', 'minutes' ),
				__( 'The number of connections that clients have made to the database server per minute on average since it started.', 'diagnosis' )
			),
			array(
				__( 'Connections success rate', 'diagnosis' ),
				$percent . '%',
				__( 'The percentage of connections to the database server that actually worked flawlessly.', 'diagnosis' )
			),
		);

		$this->add_heading( __( 'MySQL Database Server Load', 'diagnosis' ) );
		$this->add_table( array( __( 'Measurement', 'diagnosis' ), __( 'Result', 'diagnosis' ) ), $rows );
	}

	/**
	 * Add MySQL Character Encodings section.
	 *
	 * @since 2.0.0
	 *
	 * @global object $wpdb
	 *
	 * @todo Make mysql.com links use correct version
	 */
	public function section_mysql_encodings() {
		global $wpdb;

		$encodings = $wpdb->get_results( "SHOW VARIABLES LIKE 'character_set_%'" );

		foreach ( $encodings as $encoding ) {

			if ( 'character_sets_dir' == $encoding->Variable_name || 'character_set_filesystem' == $encoding->Variable_name ) {
				continue;
			}

			switch( str_replace( 'character_set_', '', $encoding->Variable_name ) ) {
				case 'client':
					$explanation = __( 'The character set in which statements are sent by the client.', 'diagnosis' );
					break;
				case 'connection':
					$explanation = __( 'What character set the server translates a statement to after receiving it.', 'diagnosis' );
					break;
				case 'database':
					$explanation = __( 'The character set used for databases.', 'diagnosis' );
					break;
				case 'results':
					$explanation = __( 'What character set the server translates to before shipping result sets or error messages back to the client.', 'diagnosis' );
					break;
				case 'server':
					$explanation = __( 'The character set used by the MySQL server itself.', 'diagnosis' );
					break;
				case 'system':
					$explanation = __( 'The character set used by the system.', 'diagnosis' );
					break;
				default:
					$explanation = '';
			}

			$rows[] = array(
				str_replace( '_', ' ', ucfirst( $encoding->Variable_name ) ),
				$encoding->Value,
				$explanation,
				__( 'http://mirrors.dotsrc.org/mysql/doc/refman/5.0/en/charset-syntax.html', 'diagnosis' )
			);
		}

		$this->add_heading( __( 'MySQL Character Encodings', 'diagnosis' ) );
		$this->add_table( array( __( 'Variable', 'diagnosis' ), __( 'Encoding', 'diagnosis' ) ), $rows );
	}

	/**
	 * Add MySQL Storage Engines section.
	 *
	 * @since 2.0.0
	 *
	 * @global object $wpdb
	 */
	public function section_mysql_engines() {
		global $wpdb;

		$engines = $wpdb->get_results( 'SHOW ENGINES;' );

		foreach ( $engines as $engine ) {
			$rows[] = array(
				$engine->Engine,
				$engine->Support,
				$engine->Comment . '.'
			);
		}

		$this->add_heading( __( 'MySQL Storage Engines', 'diagnosis' ) );
		$this->add_table( array( __( 'Storage Engine', 'diagnosis' ), __( 'Available?', 'diagnosis' ) ), $rows );
	}

	/**
	 * Callback for Diagnosis page wrapper.
	 *
	 * @since 2.0.0
	 */
	public function phpinfo_page() {
		?>
		<div class="wrap diagnosis phpinfo">
			<div class="icon32" id="icon-options-general"><br /></div>
			<h2><?php _e( 'Output from phpinfo()', 'diagnosis' ); ?></h2>
			<p><?php _e( '<code>phpinfo()</code> is a special function that outputs lots of information about the PHP set up for your site. The output is produced below.', 'diagnosis' ); ?></p>
			<?php do_action( 'diagnosis_phpinfo_page' ); ?>
		</div>
		<?php
	}

	/**
	 * Used to create a phpinfo() display/page.
	 *
	 * Slightly messy, but the only way to capture the contents of the output of the phpinfo() function.
	 *
	 * @since 2.0.0
	 */
	public function section_phpinfo() {
		ob_start();
		phpinfo();
		$phpinfo = ob_get_contents();
		ob_end_clean();

		echo preg_replace( '%^.*<body>(.*)</body>.*$%ms', '$1', $phpinfo );
	}

	/**
	 * Create heading markup.
	 *
	 * @since 2.0.0
	 *
	 * @param string $text Text for heading
	 */
	protected function add_heading( $text ) {
		?>
		<h2><?php echo $text; ?></h2>
		<?php
	}

	/**
	 * Create table markup.
	 *
	 * @since 2.0.0
	 *
	 * @param array $headers
	 * @param array $rows
	 */
	protected function add_table( $headers = array( ), $rows = array( ) ) {
		echo '<table>' . "\n";

		if ( $headers ) {
			array_push( $headers, __( 'Explanation', 'diagnosis' ) );
			echo "\t" . '<thead>' . "\n\t\t" . '<tr>' . "\n";
			foreach ( $headers as $header ) {
				echo "\t\t\t" . '<th>' . $header . '</th>' . "\n";
			}
			echo "\t\t" . '</tr>' . "\n\t" . '</thead>' . "\n";
		}

		if ( $rows ) {
			echo "\t" . '<tbody>' . "\n";
			foreach ( $rows as $row ) {
				$row[3] = isset( $row[3] ) ? $row[3] : ''; // $help_url optional
				list( $key, $value, $explanation, $help_url ) = $row;
				echo "\t\t" . '<tr>' . "\n";
				echo "\t\t\t" . '<th scope="row">' . $key . '</th>' . "\n";
				echo "\t\t\t" . '<td class="value">' . $value . '</td>' . "\n";
				echo "\t\t\t" . '<td class="explanation">';
				if ( $help_url )
					echo '(<a href="' . $help_url . '">?</a>) ';
				echo $explanation . '</td>' . "\n";
				echo "\t\t" . '</tr>' . "\n";
			}
			echo "\t" . '</tbody>' . "\n";
		}

		echo '</table>' . "\n";
	}

	/**
	 * Retrieve PHP setting and standardize returned value.
	 *
	 * @since 2.0.0
	 *
	 * @param string $value
	 *
	 * @return string Yes|No|Not set
	 */
	protected function get_php_configuration_boolean( $value ) {

		$setting = ini_get( $value );
		$boolean = (bool) $setting;

		if ( $boolean ) {
			return __( 'Yes', 'diagnosis' );
		}

		if ( ! $boolean ) {
			return __( 'No', 'diagnosis' );
		}

		// Ever used?
		return __( 'Not set', 'diagnosis' );
	}

	/**
	 * Get a MySQL variable.
	 *
	 * Query the DB and return the result as an associative array (ARRAY_A).
	 * We then return the value of it, using the key 'Value'.
	 *
	 * @since 2.0.0
	 *
	 * @global WPDB $wpdb
	 *
	 * @param string $variable
	 *
	 * @return string
	 */
	protected function get_mysql_variable( $variable ) {
		global $wpdb;

		$result = $wpdb->get_row( "SHOW VARIABLES LIKE '$variable';", ARRAY_A );

		return $result['Value'];
	}

	/**
	 * Get a MySQL status variable.
	 *
	 * Query the DB and return the result as an associative array (ARRAY_A).
	 * We then return the value of it, using the key 'Value'.
	 *
	 * @since 2.0.0
	 *
	 * @global WPDB $wpdb
	 * @param string $variable
	 * @return string
	 */
	protected function get_mysql_status( $variable ) {
		global $wpdb;

		$result = $wpdb->get_row( "SHOW STATUS LIKE '$variable';", ARRAY_A );

		return $result['Value'];
	}

	/**
	 * Retrieve and calculate some MySQL statistics.
	 *
	 * @since 2.0.0
	 *
	 * @param string $variable
	 * @param string $timeunit seconds|minutes|hours
	 *
	 * @return mixed
	 */
	protected function get_mysql_statistics( $variable, $timeunit ) {
		$amount         = $this->get_mysql_status( $variable );
		$uptime_seconds = $this->get_mysql_status( 'uptime' );

		switch ( $timeunit ) {
			case 'seconds':
				$result = $amount / $uptime_seconds;
				break;
			case 'minutes':
				$uptime = $uptime_seconds / MINUTE_IN_SECONDS;
				$result = $amount / $uptime;
				break;
			case 'hours':
				$uptime = $uptime_seconds / HOUR_IN_SECONDS;
				$result = $amount / $uptime;
				break;
		}

		return round( $result, 8 );

	}
}
