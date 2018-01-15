<?php
/**
 * Plugin Name: Pickle Mega Menu
 * Plugin URI:
 * Description: Create awesome mega menus.
 * Version: 1.0.0-alpha
 * Author:
 * Author URI:
 * Requires at least: 4.0
 * Tested up to: 49.1
 * Text Domain: pickle-mega-menu
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

if ( ! defined( 'PMM_PLUGIN_FILE' ) ) {
    define( 'PMM_PLUGIN_FILE', __FILE__ );
}

final class PickleMegaMenu {

    public $version = '1.0.0-alpha';

    public $settings = '';
    
    public $admin = '';

    protected static $_instance = null;

    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * Construct class.
     *
     * @access public
     * @return void
     */
    public function __construct() {
        $this->define_constants();
        $this->includes();
        $this->init_hooks();
    }

    /**
     * Define constants.
     *
     * @access private
     * @return void
     */
    private function define_constants() {
        $this->define( 'PMM_VERSION', $this->version );
        $this->define( 'PMM_PATH', plugin_dir_path( __FILE__ ) );
        $this->define( 'PMM_URL', plugin_dir_url( __FILE__ ) );
    }

    /**
     * Custom define function.
     *
     * @access private
     * @param mixed $name string.
     * @param mixed $value string.
     * @return void
     */
    private function define( $name, $value ) {
        if ( ! defined( $name ) ) {
            define( $name, $value );
        }
    }

    /**
     * Include plugin files.
     *
     * @access public
     * @return void
     */
    public function includes() {
        include_once(PMM_PATH.'functions.php');
        include_once(PMM_PATH.'class-pmm-nav-walker.php');
         
        include_once(PMM_PATH.'admin/class-pmm-admin.php');
        include_once(PMM_PATH.'admin/class-pmm-admin-build-menu.php');
        include_once(PMM_PATH.'admin/class-pmm-admin-save-menu.php');        
        include_once(PMM_PATH.'admin/items/item.php');
        include_once(PMM_PATH.'admin/items/pages.php');
                
        if (is_admin()) :
            $this->admin=new PMM_Admin();
        endif;
    }

    /**
     * Init hooks for plugin.
     *
     * @access private
     * @return void
     */
    private function init_hooks() {
/*
        register_activation_hook( DM_PLUGIN_FILE, array( 'Document_Manager_Install', 'install' ) );

        add_action( 'admin_init', array( $this, 'plugin_updater' ) );
        add_action( 'init', array( $this, 'get_settings' ), 99 );
        add_action( 'init', array( $this, 'init' ), 0 );
*/        
        add_action( 'wp_enqueue_scripts', array( $this, 'scripts_styles' ) );
    }

    /**
     * Init function.
     *
     * @access public
     * @return void
     */
    public function init() {

    }

    /**
     * Pull in settings.
     *
     * @access public
     * @return void
     */
    public function get_settings() {
        //$this->settings = get_option( 'dm_settings', '' );
    }

    /**
     * Include front end scripts and styles.
     *
     * @access public
     * @return void
     */
    public function scripts_styles() {
        wp_enqueue_style('pmm-frontend-styles', PMM_URL.'css/pmm-frontend.css', '', $this->version);
    }

    /**
     * Setup plugin updater.
     *
     * @access public
     * @return WP_GitHub_Updater
     */
    public function plugin_updater() {
        if ( ! is_admin() ) {
            return false;
        }

        if ( ! defined( 'WP_GITHUB_FORCE_UPDATE' ) ) {
            define( 'WP_GITHUB_FORCE_UPDATE', true );
        }

        $username    = 'erikdmitchell';
        $repo_name   = 'pickle-mega-menu';
        $folder_name = 'pickle-mega-menu';

        $config = array(
            'slug'               => plugin_basename( __FILE__ ), // this is the slug of your plugin.
            'proper_folder_name' => $folder_name, // this is the name of the folder your plugin lives in.
            'api_url'            => 'https://api.github.com/repos/' . $username . '/' . $repo_name, // the github API url of your github repo.
            'raw_url'            => 'https://raw.github.com/' . $username . '/' . $repo_name . '/master', // the github raw url of your github repo.
            'github_url'         => 'https://github.com/' . $username . '/' . $repo_name, // the github url of your github repo.
            'zip_url'            => 'https://github.com/' . $username . '/' . $repo_name . '/zipball/master', // the zip url of the github repo.
            'sslverify'          => true, // wether WP should check the validity of the SSL cert when getting an update, see https://github.com/jkudish/WordPress-GitHub-Plugin-Updater/issues/2 and https://github.com/jkudish/WordPress-GitHub-Plugin-Updater/issues/4 for details.
            'requires'           => '4.0', // which version of WordPress does your plugin require?
            'tested'             => '4.9', // which version of WordPress is your plugin tested up to?
            'readme'             => 'readme.txt', // which file to use as the readme for the version number.
        );

        new PMM_GitHub_Updater( $config );
    }

}

function PickleMegaMenu() {
    return PickleMegaMenu::instance();
}

// Global for backwards compatibility.
$GLOBALS['picklemegamenu'] = PickleMegaMenu();

