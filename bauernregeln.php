<?php
/*
	Plugin Name: Bauernregeln
	Plugin URI: http://rvincent.digital-nerv.net/wordpress-plugins/
	Description: Shows a german weather proverb or country saying, called Bauernregel, for every month.
	Version: 1.0.1
	Author: Rally Vincent
	Author URI: http://rvincent.digital-nerv.net/
	License: GPL2
	Text Domain: bauernregeln
	Domain Path: /languages
*/

/*  Copyright 2009 - 2014 Rally Vincent (email : wordpress@digital-nerv.net)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

defined( 'ABSPATH' ) or exit;

/**
 * Function Reference/load plugin textdomain
 *
 * @link https://codex.wordpress.org/Function_Reference/load_plugin_textdomain
 *
 * @param $domain (string) (required) Unique identifier for retrieving translated strings.
 * @param $abs_rel_path (string) (optional) Relative path to ABSPATH of a folder, where the .mo file resides. Deprecated, but still functional until 2.7
 * @param $plugin_rel_path (string) (optional) Relative path to WP_PLUGIN_DIR, with a trailing slash. This is the preferred argument to use. It takes precendence over $abs_rel_path
 */
load_plugin_textdomain( 'bauernregeln', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

$description = __( 'Shows a german weather proverb or country saying, called Bauernregel, for every month.', 'bauernregeln' );

$bauernregeln_db_version = "0.1.7";

class BauernregelnPlugin {
	var $options, $table, $db_version;

	public function __construct() {
		global $wpdb, $bauernregeln_db_version;
		$this->table = $wpdb->prefix . 'bauernregeln';
		$this->db_version = $bauernregeln_db_version;
		$this->options = get_option( 'bauernregeln_options' );
		add_action( 'plugins_loaded', array( $this, 'bauernregeln_update' ) );
		add_filter( 'plugin_row_meta', array( $this, 'extend_plugin_links' ), 10, 2 );
		register_activation_hook( __FILE__, array( $this, 'bauernregeln_activate' ) );
		register_deactivation_hook( __FILE__, array( $this, 'bauernregeln_deactivate' ) );
		register_uninstall_hook( __FILE__, 'bauernregeln_uninstall' );
	}

	function extend_plugin_links( $links, $file ) {
		if ( strpos( $file, 'bauernregeln.php' ) !== false ) {
			$custom_links = array(
				sprintf(
					'<a href="options-general.php?page=%1$s">%2$s</a>',
					dirname( plugin_basename( __FILE__ ) ),
					__( 'Settings', 'bauernregeln' )
				)
			);
			$links = array_merge( $links, $custom_links );
		}
		return $links;
	}

	function bauernregeln_update() {
		global $wpdb;
		if ( get_site_option( 'bauernregeln_db_version' ) ) {
			/**
			 * Convert old Option
			 */
			if ( ! empty( $this->options['onlyCustom'] ) ) {
				$this->options['only_custom'] = $this->options['onlyCustom'];
				unset( $this->options['onlyCustom'] );
				update_option( 'bauernregeln_options', $this->options );
				$this->options = get_option( 'bauernregeln_options' );
			}
			/**
			 * Disable only_custom if no custom entries available
			 */
			if ( ! empty( $this->options['only_custom'] ) ) {
				if ( $wpdb->get_var( $wpdb->prepare( "SHOW tables LIKE '%s'", "%{$this->table}%" ) ) == $this->table ) {
					$sql = "SELECT * FROM `$this->table` WHERE `custom` = 1 LIMIT 0, 1";
					$results = $wpdb->get_results( $sql );
					if ( ! $wpdb->num_rows > 0 ) {
						unset( $this->options['only_custom'] );
						update_option( 'bauernregeln_options', $this->options );
						$this->options = get_option( 'bauernregeln_options' );
					}
				}
			}
			if ( get_site_option( 'bauernregeln_db_version' ) != $this->db_version ) {
				// Todo: Update Database if it is an old one; Notice: `language`, 'de'
			}
		}
	}

	function bauernregeln_activate() {
		global $bauernregeln_db_version, $wpdb;
		if ( $wpdb->get_var( $wpdb->prepare( "SHOW tables LIKE '%s'", "%{$this->table}%" ) ) != $this->table ) {
			$sql = "CREATE TABLE `$this->table` (`id` int(10) NOT NULL auto_increment, `month` int(10) NOT NULL, `text` text NOT NULL, `custom` tinyint(1) NOT NULL, UNIQUE KEY `id` (`id`))";
			$wpdb->query( $sql );
			$sql = "INSERT INTO `$this->table` (`id`, `month`, `text`, `custom`) VALUES
					(NULL, 1, 'Im Januar Reif und arger Schnee tut B&auml;umchen und den Fr&uuml;chten weh.', 0),
					(NULL, 1, 'Januarsonne hat weder Kraft noch Wonne.', 0),
					(NULL, 1, 'Ist der Januar gelinde, folgen im Lenz die rauen Winde.', 0),
					(NULL, 1, 'Werden die Tage l&auml;nger wird der Winter strenger.', 0),
					(NULL, 1, 'Anfang und Ende Januar zeigt uns das Wetter f&uuml;rs ganze Jahr.', 0),
					(NULL, 1, 'Reichlich Schnee im Januar, Dung genug f&uuml;rs ganze Jahr.', 0),
					(NULL, 1, 'Tanzen im Januar die Mucken &mdash; Bauer, musst nach Futter gucken!', 0),
					(NULL, 1, 'Wenn die Tage langen, kommt die gro&szlig;e K&auml;te gegangen.', 0),
					(NULL, 1, 'Ist der J&auml;nner rau und hart, ist&apos;s f&uuml;r das Jahr ein guter Start.', 0),
					(NULL, 1, 'Januar Schnee zuhauf: Bauer halt den Sack auf!', 0),
					(NULL, 1, 'Je n&auml;her die Hasen dem Dorfe r&uuml;cken, desto &auml;rger sind des Eismondes T&uuml;cken.', 0),
					(NULL, 1, 'Wenn&apos;s nicht mehr richtig wintern tut, wird selten auch der Sommer gut.', 0),
					(NULL, 1, 'Januar muss krachen, soll der Fr&uuml;hling lachen.', 0),
					(NULL, 1, 'Gelinder Januar bringt sp&auml;tes Fr&uuml;hjahr.', 0),
					(NULL, 1, 'Auf trockenen, kalten Januar folgt meist viel Schnee im Februar.', 0),
					(NULL, 1, 'Januar wei&szlig;, der Sommer hei&szlig;. &mdash; Januar warm, dass Gott erbarm!', 0)";
			$wpdb->query( $sql );
			$sql = "INSERT INTO `$this->table` (`id`, `month`, `text`, `custom`) VALUES
					(NULL, 2, 'Wenn der Februar wie ein Wolf heranschleicht, geht er wie ein Lamm. Kommt er wie ein Lamm, geht er wie ein Wolf.', 0),
					(NULL, 2, 'Viel Regen im Februar, viel Regen im ganzen Jahr.', 0),
					(NULL, 2, 'L&auml;sst der Februar Wasser fallen, l&auml;sst es der M&auml;rz gefrieren.', 0),
					(NULL, 2, 'Ist der Februar sehr warm, friert man zu Ostern bis in den Darm.', 0),
					(NULL, 2, 'Besser im Hornung zu frieren, als drau&szlig;en im Sonnenschein zu spazieren.', 0),
					(NULL, 2, 'Der Hornung hat so seine Mucken, er baut mit Eis oft feste Brucken.', 0),
					(NULL, 2, 'Nasser Februar &mdash; nass das ganze Jahr.', 0),
					(NULL, 2, 'Im Hornung recht viel Schnee und Eis macht den Sommer lange hei&szlig;.', 0),
					(NULL, 2, 'Der Hornung ist ein eigner Kauz, wenn&apos;s nicht gefroren ist, so taut&apos;s.', 0),
					(NULL, 2, 'Fr&uuml;her Vogelgesang macht den Winter lang.', 0),
					(NULL, 2, 'Viel Nebel im Februar, bringt Regen oft im Jahr.', 0),
					(NULL, 2, 'Wenn&apos;s im Hornung nicht recht wintert, so kommt die K&auml;lte um Ostern.', 0),
					(NULL, 2, 'Sonnt sich die Katze im Feber, friert sie sicher im M&auml;rz.', 0)";
			$wpdb->query( $sql );
			$sql = "INSERT INTO `$this->table` (`id`, `month`, `text`, `custom`) VALUES
					(NULL, 3, 'Der M&auml;rz soll kommen wie ein Wolf, aber gehen wie ein Lamm.', 0),
					(NULL, 3, 'Bleibt die Lerche stumm und steigt nicht hoch, folgt ein nasses Fr&uuml;hjahr noch.', 0),
					(NULL, 3, 'Viel Nebel im M&auml;rz, viel Gewitter im Sommer.', 0),
					(NULL, 3, 'Zu Anfang und zu End&apos; der M&auml;rz sein Gift versend&apos;t.', 0),
					(NULL, 3, 'Ein feuchter M&auml;rz ist der Bauern Schmerz.', 0),
					(NULL, 3, 'In dem M&auml;rzen geh &mdash; sag manchem Ast ade.', 0),
					(NULL, 3, 'Auf M&auml;rzenregen folgt kein Sommersegen.', 0),
					(NULL, 3, 'M&auml;rzenschnee tut \(den\) Saaten weh.', 0),
					(NULL, 3, 'Taut&apos;s im M&auml;rz nach Sommerart, kriegt der April einen wei&szlig;en Bart.', 0)";
			$wpdb->query( $sql );
			$sql = "INSERT INTO `$this->table` (`id`, `month`, `text`, `custom`) VALUES
					(NULL, 4, 'Ein Wind, der von Ostern bis Pfingsten regiert im ganzen Jahr sich wenig verliert.', 0),
					(NULL, 4, 'Bald tr&uuml;b und rau, bald licht und mild, ist der April des Menschen Ebenbild.', 0),
					(NULL, 4, 'Kommen die Bienen nicht heraus, ist&apos;s mit dem sch&ouml;nen Wetter aus.', 0),
					(NULL, 4, 'D&uuml;rrer April ist nicht des Bauern Will. April-Regen ist dem Bauern gelegen.', 0),
					(NULL, 4, 'Ist der April auch noch so gut, er schneit dem Bauern auf den Hut.', 0),
					(NULL, 4, 'Bl&auml;st April mit beiden Backen, ist genug zu j&auml;ten, hacken.', 0),
					(NULL, 4, 'Aprilsturm und Regenwucht bescheren Wein und gute Frucht.', 0),
					(NULL, 4, 'Wenn der April wie ein L&ouml;we kommt, so geht er wie ein Lamm.', 0),
					(NULL, 4, 'Ist der April recht sch&ouml;n und rein, wird der Mai umso wilder sein.', 0),
					(NULL, 4, 'Aprilenglut tut selten gut.', 0)";
			$wpdb->query( $sql );
			$sql = "INSERT INTO `$this->table` (`id`, `month`, `text`, `custom`) VALUES
					(NULL, 5, 'Erst Mitte Mai ist der Winter vorbei.', 0),
					(NULL, 5, 'Fliegt abends lang die Fledermaus, sagt gutes Wetter sie voraus.', 0),
					(NULL, 5, 'Fliegen die Schwalben in den H&ouml;h&apos;n, kommt ein Wetter, das ist sch&ouml;n.', 0),
					(NULL, 5, 'Auf nassen Mai kommt trockner Juni herbei.', 0),
					(NULL, 5, 'Nordwind im Mai, schafft Trockenheit herbei.', 0),
					(NULL, 5, 'Wenn im Mai die Bienen schw&auml;rmen, sollte man vor Freude l&auml;rmen.', 0),
					(NULL, 5, 'Maienfr&ouml;ste sind unn&uuml;tze G&auml;ste.', 0),
					(NULL, 5, 'Maientau macht gr&uuml;ne Au.', 0)";
			$wpdb->query( $sql );
			$sql = "INSERT INTO `$this->table` (`id`, `month`, `text`, `custom`) VALUES
					(NULL, 6, 'Stellt der Juni mild sich ein, wird mild auch der Dezember sein.', 0),
					(NULL, 6, 'Wie&apos;s wittert am Medardustag, so bleibt&apos;s sechs Wochen lang danach.', 0),
					(NULL, 6, 'Geben die Johannisw&uuml;rmchen ungew&ouml;hnlich viel Licht, so ist sch&ouml;nes Wetter in Sicht.', 0),
					(NULL, 6, 'Fliegen die Flederm&auml;use abends umher, so kommt ein lang Sch&ouml;nwetter her.', 0),
					(NULL, 6, 'Juniregen &mdash; reicher Segen.', 0),
					(NULL, 6, 'Viermal Juniregen bringt zw&ouml;lffachen Segen.', 0),
					(NULL, 6, 'Wenn die Nacht zu langen beginnt, dann die Hitze am meisten zunimmt.', 0),
					(NULL, 6, 'Sind am Sommerabend &uuml;ber Wies&apos; und Fluss Nebel zu schauen, so wird die Luft anhaltend sch&ouml;n Wetter brauen.', 0)";
			$wpdb->query( $sql );
			$sql = 	"INSERT INTO `$this->table` (`id`, `month`, `text`, `custom`) VALUES
					(NULL, 7, 'Der Juli voller Sonnenschein wird jedermann willkommen sein.', 0),
					(NULL, 7, 'Genauso wie der Juli war, wird n&auml;chstes Mal der Januar.', 0),
					(NULL, 7, 'Schnappt im Juli das Weidvieh nach Luft, riecht es schon Gewitterduft.', 0),
					(NULL, 7, 'Ein Juli ohne Hagel ist so selten wie ein Kopf ohne Nagel.', 0),
					(NULL, 7, 'Macht der Juli uns hei&szlig;, bringt der Winter viel Eis.', 0),
					(NULL, 7, 'Juliregen nimmt Erntesegen.', 0),
					(NULL, 7, 'Wenn die Schwalben im Fluge das Wasser ber&uuml;hren, werden wir bald Regen versp&uuml;ren.', 0),
					(NULL, 7, 'Wenn Donner kommt im Julius viel Regen man erwarten muss.', 0)";
			$wpdb->query( $sql );
			$sql = "INSERT INTO `$this->table` (`id`, `month`, `text`, `custom`) VALUES
					(NULL, 8, 'Ist der August am Anfang hei&szlig;, wird der Winter streng und wei&szlig;.', 0),
					(NULL, 8, 'Wenn die M&uuml;cken spielen, wird sch&ouml;nes Wetter.', 0),
					(NULL, 8, 'Du kannst ohne Schirm aufs Felde gehen, wenn Sch&auml;fchenwolken am Himmel stehen.', 0),
					(NULL, 8, 'Wenn die M&uuml;cken tanzen und spielen, sie morgiges gut Wetter f&uuml;hlen.', 0),
					(NULL, 8, 'Springende Fische bringen Gewitterfrische.', 0),
					(NULL, 8, 'Ist der August recht hell und hei&szlig;, so lacht der G&auml;rtner im vollen Schwei&szlig;.', 0),
					(NULL, 8, 'Wenn abends die Flederm&auml;use herumfliegen, so folgt ein anhaltend gut Wetter.', 0),
					(NULL, 8, 'F&auml;ngt August mit Hitze an, bleibt sehr lang die Schlittenbahn.', 0),
					(NULL, 8, 'Ist&apos;s im August recht dr&uuml;ckend schw&uuml;l, dann ist&apos;s im Schatten auch nicht k&uuml;hl.', 0),
					(NULL, 8, 'Wie der August, so der k&uuml;nftige Februar.', 0),
					(NULL, 8, 'Wie im August das Wetter f&auml;llt, so ist&apos;s das ganze Jahr bestellt.', 0),
					(NULL, 8, 'Wie der August heuer war, wird sein der n&auml;chste Februar.', 0)";
			$wpdb->query( $sql );
			$sql = "INSERT INTO `$this->table` (`id`, `month`, `text`, `custom`) VALUES
					(NULL, 9, 'Wie&apos;s in der ersten H&auml;lfte September soll&apos;s meistens das ganze Jahr &uuml;ber bleiben.', 0),
					(NULL, 9, 'Septemberdonner prophezeit reichlich Schnee zur Weihnachtszeit.', 0),
					(NULL, 9, 'Auf Schwalb und Eichhorn achte bald, sind sie verschwunden, wird&apos;s schnell kalt.', 0),
					(NULL, 9, 'F&auml;llt im Wald das Laub sehr schnell, ist der Winter bald zur Stell.', 0),
					(NULL, 9, 'Soll der September den G&auml;rtner erfreun, so muss er gleich dem M&auml;rze sein.', 0),
					(NULL, 9, 'September sch&ouml;n in den ersten Tagen, will den ganzen Herbst ansagen.', 0),
					(NULL, 9, 'So der n&auml;chste M&auml;rz, wie der September, so der Juni, wie der Dezember.', 0),
					(NULL, 9, 'Donnert&apos;s im September noch, liegt zu Weihnacht der Schnee recht hoch.', 0),
					(NULL, 9, 'Kommen im Herbst viel Spinnen ins Haus, webt der Winter mit hartem Graus.', 0),
					(NULL, 9, 'Der September ist der Mai des Herbstes.', 0)";
			$wpdb->query( $sql );
			$sql = "INSERT INTO `$this->table` (`id`, `month`, `text`, `custom`) VALUES
					(NULL, 10, 'Wenn&apos;s im Oktober friert und schneit, bringt der J&auml;nner milde Zeit.', 0),
					(NULL, 10, 'Schneit&apos;s im Oktober gleich, dann wird der Winter weich.', 0),
					(NULL, 10, 'Oktoberschnee tut Tieren und Pflanzen weh.', 0),
					(NULL, 10, 'Bringt der Oktober viel Frost und Wind, so sind der Januar und Hornung gelind.', 0),
					(NULL, 10, 'Oktoberwetter zeigt stets an, wie&apos;s k&uuml;nftig um den M&auml;rz wird stahn.', 0),
					(NULL, 10, 'Oktoberwetter warm und hell, bringt kalten Wind und Winter schnell.', 0),
					(NULL, 10, 'F&auml;llt im Oktober das Laub sehr schnell, ist der Winter bald zur Stell&apos;.', 0),
					(NULL, 10, 'Hat der Oktober viel Regen gebracht, hat er die Gottes&auml;cker bedacht.', 0)";
			$wpdb->query( $sql );
			$sql = "INSERT INTO `$this->table` (`id`, `month`, `text`, `custom`) VALUES
					(NULL, 11, 'H&auml;ngt das Laub bis zum November hinein, wird der Winter ein langer sein.', 0),
					(NULL, 11, 'F&uuml;r den November gilt althergebracht, dass er die Gottes&auml;cker bedacht.', 0),
					(NULL, 11, 'Wie der November, so der folgende Mai.', 0),
					(NULL, 11, 'Glatter Pelz beim Wilde, dann wird der Winter milder.', 0),
					(NULL, 11, 'November warm und klar, wenig Segen f&uuml;rs n&auml;chste Jahr.', 0),
					(NULL, 11, 'Tummeln sich noch die Haselm&auml;use, ist es noch weit mit des Winters Eise.', 0),
					(NULL, 11, 'Friert im November fr&uuml;h das Wasser, dann wird der J&auml;nner um so nasser.', 0),
					(NULL, 11, 'H&auml;lt die Buche noch lange die Bl&auml;tter fest, basld gro&szlig;e K&auml;lt&apos; erwarten l&auml;sst.', 0),
					(NULL, 11, 'Bringt der November Morgenrot, sei sicher, dass langer Regen droht.', 0)";
			$wpdb->query( $sql );
			$sql = "INSERT INTO `$this->table` (`id`, `month`, `text`, `custom`) VALUES
					(NULL, 12, 'Wintert&apos;s vor Weihnachten nicht, so wintert&apos;s nach.', 0),
					(NULL, 12, 'Kalter Dezember &mdash; zeitiger Fr&uuml;hling.', 0),
					(NULL, 12, 'Dezember kalt mit Schnee, gibt Korn auf jeder H&ouml;h&apos;.', 0),
					(NULL, 12, 'Dezember lau und lind, der ganze Winter ein Kind.', 0),
					(NULL, 12, 'Wenn in der ersten Adventswoche kaltes strenges Wetter herrscht, so soll es 18 volle Wochen anhalten.', 0),
					(NULL, 12, 'Dezember ver&auml;nderlich und lind, der ganze Winter ein Kind.', 0),
					(NULL, 12, 'Wenn es vor Weihnachten nicht vorwintert, so wintert es im Fr&uuml;hjahr nach.', 0),
					(NULL, 12, 'Trockener Dezember, trockenes Fr&uuml;hjahr und trockener Sommer.', 0),
					(NULL, 12, 'So kalt im Dezember, so hei&szlig; wird&apos;s im Juni.', 0),
					(NULL, 12, 'Donner im Winterquartal bringt uns Eiszapfen ohne Zahl.', 0),
					(NULL, 12, 'Gefriert&apos;s zu Sylvester in Berg und Tal, ist&apos;s dieses Jahr das letzte Mal.', 0)";
			$wpdb->query( $sql );
			add_option( 'bauernregeln_db_version', $this->db_version );
		}
	}
	function bauernregeln_deactivate() {}
}

class BauernregelnPluginSettings {
	public static $default_settings = array();

	var $options, $page, $slug, $settings_field, $table;

	public function __construct() {
		global $wpdb;
		$this->table = $wpdb->prefix . 'bauernregeln';
		$this->settings_field = 'bauernregeln_options';
		$this->options = get_option( $this->settings_field );
		$this->page = dirname( plugin_basename( __FILE__ ) );
		$this->slug = $this->page;
		add_action( 'admin_init', array( $this, 'init_plugin_page' ) );
		add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
	}

	/**
	 * Register Settings
	 */
	function init_plugin_page() {
		/**
		 * Function Reference/register setting
		 *
		 * http://codex.wordpress.org/Function_Reference/register_setting
		 *
		 * @param $option_group (string) (required) A settings group name. Must exist prior to the register_setting call. This must match the group name in settings_fields()
		 * @param $option_name (string) (required) The name of an option to sanitize and save.
		 * @param $sanitize_callback  $sanitize_callback (callback) (optional) A callback function that sanitizes the option's value.
		 */
		register_setting(
			$this->settings_field,
			$this->settings_field,
			array( $this, 'plugin_options_sanitize' )
		);

		// add_option( $this->settings_field, BauernregelnPluginSettings::$default_settings );

		/**
		 * Function Reference/add settings section
		 *
		 * http://codex.wordpress.org/Function_Reference/add_settings_section
		 *
		 * @param $id (string) (required) String for use in the 'id' attribute of tags.
		 * @param $title (string) (required) Title of the section.
		 * @param $callback (string) (required) Function that fills the section with the desired content. The function should echo its output.
		 * @param $page (string) (required) The menu page on which to display this section. Should match $menu_slug from Function Reference/add theme page
		 */
		add_settings_section(
			'plugin_settings_section_custom_proverbs',
			__( 'Custom proverbs', 'bauernregeln' ),
			array( $this, 'print_plugin_settings_section_custom_proverbs' ),
			$this->page
		);

		/**
		 * Function Reference/add settings field
		 *
		 * http://codex.wordpress.org/Function_Reference/add_settings_field
		 *
		 * @param $id (string) (required) String for use in the 'id' attribute of tags.
		 * @param $title (string) (required) Title of the field.
		 * @param $callback (string) (required) Function that fills the field with the desired inputs as part of the larger form. Passed a single argument, the $args array. Name and id of the input should match the $id given to this function. The function should echo its output.
		 * @param $page (string) (required) The menu page on which to display this field. Should match $menu_slug from add_theme_page()
		 * @param $section (string) (optional) The section of the settings page in which to show the box (default or a section you added with add_settings_section(), look at the page in the source to see what the existing ones are.)
		 * @param $args (array) (optional) Additional arguments that are passed to the $callback function. The 'label_for' key/value pair can be used to format the field title like so: <label for="value">$title</label>.
		 */
		/**
		 * Add Settings Fields here
		 */
		add_settings_field(
			'only_custom',
			__( 'Display only custom proverbs', 'bauernregeln' ),
			array( $this, 'only_custom_callback' ),
			$this->page,
			'plugin_settings_section_custom_proverbs'
		);
	}

	/**
	 * Add Page
	 */
	function add_plugin_page() {
		global $wpdb;
		if ( isset( $_POST['bauernregeln_delete'] ) ) {
			if ( isset( $_POST['proverb'] ) ) {
				foreach ( $_POST['proverb'] as $key => $value ) {
					$sql = $wpdb->prepare(
								"DELETE FROM `$this->table` WHERE `id` = %d AND `custom` = 1",
								$key
							);
					$wpdb->query( $sql );
				}
			}
		}
		if ( isset( $_POST['bauernregeln_upload'] ) ) {
			if ( $_FILES ) {
				$patterns = array( 'text/csv', 'application/octet-stream', 'text/comma-separated-values' );
				$patterns_flattend = sprintf( '#%s#i', implode( '|', $patterns ) );
				if ( preg_match( $patterns_flattend, $_FILES['upload_custom']['type'] ) ) {
					if ( $wpdb->get_var( $wpdb->prepare( "SHOW tables LIKE '%s'", "%{$this->table}%" ) ) == $this->table ) {
						if ( ( $handle = fopen( $_FILES['upload_custom']['tmp_name'], "r" ) ) !== false ) {
							while( ( $data = fgetcsv( $handle ) ) !== false ) {
								if ( count( $data ) == 2 ) {
									$sql = $wpdb->prepare(
												"INSERT INTO `$this->table` (`id`, `month`, `text`, `custom`) VALUES (NULL, %d, '%s', 1)",
												$data[0],
												trim($data[1])
											);
									$wpdb->query( $sql );
								}
							}
							fclose($handle);
						}
					}
				}
			}
		}
		/**
		 * Function Reference/add options page
		 *
		 * @link http://codex.wordpress.org/Function_Reference/add_options_page
		 *
		 * @param $page_title (string) (required) The text to be displayed in the title tags of the page when the menu is selected
		 * @param $menu_title (string) (required) The text to be used for the menu
		 * @param $capability (string) (required) The capability required for this menu to be displayed to the user.
		 * @param $menu_slug (string) (required) The slug name to refer to this menu by (should be unique for this menu).
		 * @param $function (callback) (optional) The function to be called to output the content for this page.
		 */
		add_options_page(
			__( 'Settings &rsaquo; Bauernregeln', 'bauernregeln' ),
			__( 'Bauernregeln', 'bauernregeln' ),
			'administrator',
			$this->page,
			array( $this, 'plugin_page_callback' )
		);
	}

	/**
	 * Page Callback
	 */
	function plugin_page_callback() {
		global $wpdb;
		?>
		<div class="wrap">
			<h2><?php _e( 'Settings &rsaquo; Bauernregeln', 'bauernregeln' ); ?></h2>
			<form method="post" action="options.php">
			<?php
				/**
				 * Function Reference/settings fields
				 *
				 * http://codex.wordpress.org/Function_Reference/settings_fields
				 *
				 * @param $option_group (string) (required) A settings group name. This should match the group name used in register_setting().
				 */
				settings_fields( $this->settings_field );

				/**
				 * Function Reference/do settings sections
				 *
				 * http://codex.wordpress.org/Function_Reference/do_settings_sections
				 *
				 * @param $page (string) (required) The slug name of the page whose settings sections you want to output. This should match the page name used in add_settings_section().
				 */
				do_settings_sections( $this->page );

				/**
				 * Function Reference/submit button
				 *
				 * http://codex.wordpress.org/Function_Reference/submit_button
				 *
				 * @param $text (string) (optional) The text of the button
				 * @param $type (string|array) (optional) The type of button. Common values: primary, secondary, delete.
				 * @param $name (string) (optional) The HTML name of the submit button. If no id attribute is given in $other_attributes below, $name will be used as the button's id.
				 * @param $wrap (boolean) (optional) True if the output button should be wrapped in a paragraph tag, false otherwise. Defaults to true
				 * @param $other_attributes (array|string) (optional) Other attributes that should be output with the button, mapping attributes to their values, such as array( 'tabindex' => '1' ).
				 */
				submit_button();
			?>
			</form>
			<form action="<?php echo $_SERVER['PHP_SELF'] . '?page=' . $this->slug; ?>" method="post" class="wp-upload-form" id="upload-form" enctype="multipart/form-data">
				<table class="form-table">
					<tr>
						<th scope="row">
							<?php echo _e( 'Upload custom proverbs', 'bauernregeln' ); ?>
						</th>
						<td>
							<p>
								<?php echo _e( 'You can upload a CSV File (comma separated values) that contains your proverbs.', 'bauernregeln' ); ?>
							</p>
							<p>
								<b><?php echo _e( 'Example', 'bauernregeln' ); ?></b>
							</p>
							<p>
								<code>1, Januarsonne hat weder Kraft noch Wonne.</code>
								<br />
								<code>7, Wenn Donner kommt im Julius viel Regen man erwarten muss.</code>
							</p>
							<p>
								<label for="upload_custom"><?php echo _e( 'Choose a file from your harddisk', 'bauernregeln' ); ?></label>
								<br />
								<input type="file" name="upload_custom" id="upload_custom" />
								<input type="hidden" name="bauernregeln_upload" id="bauernregeln_upload" />
								<input type="submit" value="<?php echo _e( 'Upload', 'bauernregeln' ); ?>" class="button" name="submit" />
							</p>
						</td>
					</tr>
				</table>
			</form>
			<?php
				$sql = "SELECT * FROM `$this->table` WHERE `custom` = 1 ORDER BY `month`";
				$results = $wpdb->get_results( $sql );
				$month = array(
					__( 'January', 'bauernregeln' ),
					__( 'February', 'bauernregeln' ),
					__( 'March', 'bauernregeln' ),
					__( 'April', 'bauernregeln' ),
					__( 'May', 'bauernregeln' ),
					__( 'June', 'bauernregeln' ),
					__( 'July', 'bauernregeln' ),
					__( 'August', 'bauernregeln' ),
					__( 'September', 'bauernregeln' ),
					__( 'October', 'bauernregeln' ),
					__( 'November', 'bauernregeln' ),
					__( 'December', 'bauernregeln' )
				);
				if ( $results ) {
			?>
			<h3><?php _e( 'Delete Custom proverbs', 'bauernregeln' ); ?></h3>
			<form action="<?php echo $_SERVER['PHP_SELF'] . '?page=' . $this->slug; ?>" method="post" id="proverbs-form">
				<table class="wp-list-table widefat fixed" id="proverbs">
					<thead>
						<tr>
							<th id="cb" class="manage-column check-column" scope="col">
								<label class="screen-reader-text" for="cb-select-all">
									<?php _e( 'Select all', 'bauernregeln' ); ?>
								</label>
								<input id="cb-select-all" type="checkbox" />
							</th>
							<th id="month" class="manage-column column-author" scope="col">
								<?php _e( 'Month', 'bauernregeln' ); ?>
							</th>
							<th id="title" class="manage-column column-title" scope="col">
								<?php _e( 'Proverb', 'bauernregeln' ); ?>
							</th>
						</tr>
					</thead>
					<tbody>
				<?php
					foreach ($results as $result) {
						printf(
							'<tr>
								<td>
									<label class="screen-reader-text" for="cb-select-all-%1$s">
										%2$s %1$s
									</label>
									<input class="cb-select-all" id="cb-select-all-%1$s" name="proverb[%1$s]" type="checkbox" />
								</td>
								<td>%3$s</td>
								<td>%4$s</td>
							</tr>',
							$result->id,
							__( 'Select', 'bauernregeln' ),
							$month[$result->month - 1],
							$result->text
						);
					}
				?>
					</tbody>
				</table>
				<p class="submit">
					<input class="button-secondary" type="submit" name="bauernregeln_delete" value="<?php _e( 'Delete Selected', 'bauernregeln' ); ?>" />
				</p>
			</form>
			<script>
				jQuery('#cb-select-all').on('click', function() {
					jQuery(".cb-select-all").attr('checked', jQuery('#cb-select-all').is(':checked'));
				});
			</script>
			<?php
				}
			?>
		</div>
		<?php
	}

	/**
	 * Settings Callbacks
	 *
	 * Add Callbacks for Settings Fields here
	 */
	function only_custom_callback() {
		printf(
			'<input type="checkbox" name="bauernregeln_options[only_custom]" id="only_custom" %1$s />',
			isset( $this->options['only_custom'] ) ? esc_attr( 'checked="checked"' ) : ''
		);
	}

	/**
	 * Print before Settings
	 */
	function print_plugin_settings_section_custom_proverbs() {}

	/**
	 * Sanitize Inputs
	 */
	function plugin_options_sanitize( $input ) {
		foreach( $input as $key => $value ) {
			$valid_input[$key] = $value;
		}
		return $valid_input;
	}
}

class BauernregelnPluginWidget extends WP_Widget {
	var $options, $table;

	function __construct() {
		global $wpdb;
		$this->table = $wpdb->prefix . 'bauernregeln';
		$this->options = get_option( 'bauernregeln_options' );
		$widget_ops = array(
			'classname' => 'widget_bauernregeln',
			'description' => __( 'Shows a german weather proverb or country saying, called Bauernregel, for every month.', 'bauernregeln' )
		);
		$control_ops = array( 'width' => 300, 'height' => 300 );
		// Fixed for WordPress 4.3
		parent::__construct(
			'Bauernregeln',
			__( 'Bauernregeln', 'bauernregeln' ),
			$widget_ops,
			$control_ops
		);
		// $this->WP_Widget( 'Bauernregeln', __( 'Bauernregeln', 'bauernregeln' ), $widget_ops, $control_ops );
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	function widget( $args, $instance ) {
		global $wpdb;
		extract( $args );
		$month = array(
			__( 'January', 'bauernregeln' ),
			__( 'February', 'bauernregeln' ),
			__( 'March', 'bauernregeln' ),
			__( 'April', 'bauernregeln' ),
			__( 'May', 'bauernregeln' ),
			__( 'June', 'bauernregeln' ),
			__( 'July', 'bauernregeln' ),
			__( 'August', 'bauernregeln' ),
			__( 'September', 'bauernregeln' ),
			__( 'October', 'bauernregeln' ),
			__( 'November', 'bauernregeln' ),
			__( 'December', 'bauernregeln' )
		);
		$current_month = date( 'n' );
		if ( ! empty( $instance['title'] ) ) {
			$title = sprintf( $instance['title'], $month[$current_month - 1] );
		}
		if ( ! empty( $this->options['only_custom'] ) ) {
			$sql = $wpdb->prepare(
						"SELECT * FROM `$this->table` WHERE `month` = %d AND `custom` = 1 ORDER BY RAND() LIMIT 0, 1",
						$current_month
					);
		} else {
			$sql = $wpdb->prepare(
						"SELECT * FROM `$this->table` WHERE `month` = %d ORDER BY RAND() LIMIT 0, 1",
						$current_month
					);
		}
		$results = $wpdb->get_results( $sql );
		if ( $results ) {
			foreach ( $results as $result ) {
				$text = sprintf( '<p>%s</p>', $result->text );
			}
		}
		if ( ! empty( $text ) ) {
			echo $before_widget;
			if ( ! empty( $title ) ) {
				echo $before_title . $title . $after_title;
			}
			echo $text;
			echo $after_widget;
		}
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array)$instance, array( 'title' => '' ) );
		$title = strip_tags( $instance['title'] );
		?>
		<p>
		  <label for="<?php echo $this->get_field_id( 'title' ); ?>">
			<?php _e( 'Title', 'bauernregeln' ); ?>:
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		  </label>
		</p>
		<p>
			<?php _e( 'To show the current month in the title, add <b>%s</b> anywhere in the title text.', 'bauernregeln' ); ?>
		</p>
		<p>
			<b><?php _e( 'Examples', 'bauernregeln' ); ?>:</b>
			<br />
			<i>Spruch des Monats %s</i>
			<br />
			<?php _e( 'or', "bauernregeln" ); ?>
			<br />
			<i>Das sagt im %s der Bauer</i>
		</p>
		<?php
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( stripslashes( $new_instance['title'] ) );
		return $instance;
	}
}

if ( is_admin() ) {
	$bauernregeln_plugin = new BauernregelnPlugin();
	$bauernregeln_plugin_settings = new BauernregelnPluginSettings();
}

/**
 * Delete Database Table and Remove Options
 */
function bauernregeln_uninstall() {
	global $wpdb;
	$table = $wpdb->prefix . 'bauernregeln';
	$wpdb->query( "DROP TABLE `$table`" );
	delete_option( 'bauernregeln_db_version' );
	delete_option( 'bauernregeln_options' );
	delete_option( 'widget_bauernregeln' );
}

/**
 * Register Widget
 */
function bauernregeln_plugin_widget() {
	register_widget( 'BauernregelnPluginWidget' );
}
add_action( 'widgets_init', 'bauernregeln_plugin_widget' );
