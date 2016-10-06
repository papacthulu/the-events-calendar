<?php


/**
 * Class Tribe__Events__Integrations__WPML__WPML
 *
 * Handles anything relating to The Events Calendar and WPML integration
 *
 * This class is meant to be an entry point hooking specialized classes and not
 * a logic hub per se.
 */
class Tribe__Events__Integrations__WPML__WPML {

	/**
	 * @var Tribe__Events__Integrations__WPML__WPML
	 */
	protected static $instance;

	/**
	 * The class singleton constructor.
	 *
	 * @return Tribe__Events__Integrations__WPML__WPML
	 */
	public static function instance() {
		if ( empty( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Hooks into The Events Calendar and WPML hooks to make the plugins play nice.
	 */
	public function hook() {
		// the WPML API is not included by default
		require_once ICL_PLUGIN_PATH . '/inc/wpml-api.php';

		$this->hook_actions();
		$this->hook_filters();
	}

	protected function hook_actions(  ) {
		$defaults = Tribe__Events__Integrations__WPML__Defaults::instance();

		if ( ! $defaults->has_set_defaults() ) {
			add_action( 'icl_save_settings', array( $defaults, 'set_defaults' ) );
		}

		$this->setup_cache_expiration_triggers();
	}

	protected function hook_filters() {
		$filters = Tribe__Events__Integrations__WPML__Filters::instance();

		add_filter( 'tribe_events_post_type_permalink', 'wpml_permalink_filter' );
		add_filter( 'tribe_events_rewrite_i18n_slugs_raw', array( $filters, 'filter_tribe_events_rewrite_i18n_slugs_raw' ), 10, 3 );
	}

	protected function setup_cache_expiration_triggers() {
		$cache_listener = Tribe__Cache_Listener::instance();
		add_action( 'wpml_cache_clear', array( $cache_listener, 'wpml_updates' ) );
		add_action( 'wpml_activated', array( $cache_listener, 'wpml_updates' ) );
		add_action( 'wpml_deactivated', array( $cache_listener, 'wpml_updates' ) );
		add_action( 'update_option_icl_sitepress_settings', array( $cache_listener, 'wpml_updates' ) );
		add_action( 'tribe_settings_save', array( $cache_listener, 'wpml_updates' ) );
	}
}