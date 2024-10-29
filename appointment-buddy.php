<?php
/**
 * Plugin Name: Appointment Buddy Widget
 * Description: This is a appointment scheduling plugin which allow you to book appointment online quickly and easily.
 * Author: Accrete InfoSolution Technologies LLP
 * Version: 1.2
 * Author URI: http://www.accreteinfo.com/
 */


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) die( '<h3>Direct access to this file do not allow!</h3>' );

// PRIMARY URL CONSTANTS 
if(! defined('apbud_ROOT_DIR')) define( 'apbud_ROOT_DIR', str_replace('\\', '/', dirname(__FILE__)) );

if(! defined('apbud_ROOT_PAGE')) define( 'apbud_ROOT_PAGE', __FILE__ );

if(! defined('apbud_ROOT_URL')) define( 'apbud_ROOT_URL', plugin_dir_url(__FILE__) );

if(! defined('apbud_INC')) define( 'apbud_INC', apbud_ROOT_DIR . ('/includes') );

if(! defined('apbud_LAN')) define( 'apbud_LAN', apbud_ROOT_URL . ('/languages') );

if(! defined('apbud_ADMIN')) define( 'apbud_ADMIN', apbud_ROOT_DIR . ('/admctrlfd') );

if(! defined('apbud_CSS')) define( 'apbud_CSS', apbud_ROOT_URL . ('css') );

if(! defined('apbud_IMAGES')) define( 'apbud_IMAGES', apbud_ROOT_URL . ('/images') );

if(! defined('apbud_JS')) define( 'apbud_JS', apbud_ROOT_URL . ('js') );

if(! defined('apbud_AJAX_URL')) define( 'apbud_AJAX_URL', admin_url("admin-ajax.php"));

if(! defined('apbud_CURRENT_TIMEZONE')) define( 'apbud_CURRENT_TIMEZONE', date( 'Y-m-d H:i:s', current_time( 'timestamp', 1 )));

if(! defined('apbud_CURRENT_DATE')) define( 'apbud_CURRENT_DATE', date( 'Y-m-d', current_time( 'timestamp', 1 )));

if(! defined('apbud_CURRENT_TIME')) define( 'apbud_CURRENT_TIME', date( 'H:i:s', current_time( 'timestamp', 1 )));

class apbud_PLUGIN { //class-1

	//constuctor method
	public function __construct() 
	{
		register_activation_hook(apbud_ROOT_PAGE, array($this, 'apbud_tblgeneration'));
		add_action('admin_menu', array($this, 'apbud_addAppointmentBuddyMainMenu'), 1);
		add_action( 'plugins_loaded', array( $this, 'includes' ), 2 );
		add_action( 'widgets_init', array( $this, 'register_widget' ) );
		add_action( 'admin_enqueue_scripts', array($this, 'apbud_plugin_admin_scripts'));
		add_action( 'admin_enqueue_scripts', array($this, 'apbud_plugin_admin_styles'));
	}
	
	public function apbud_tblgeneration() {
		require apbud_ADMIN . '/generateTbl.php';
	}
	
	public function apbud_plugin_admin_scripts()
	{
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'ab-moment-js', apbud_ROOT_URL . 'admctrlfd/js/moment.min.js' );
		wp_enqueue_script( 'ab-fullCalendar-js', apbud_ROOT_URL . 'admctrlfd/js/fullcalendar.min.js' );	
		wp_enqueue_script( 'ab-validate-js', apbud_JS . '/jquery.validate.min.js' );
		wp_enqueue_script( 'ab-datepicker-js', apbud_JS . '/datetimepicker.full.min.js' );
		wp_enqueue_script( 'ab-tabbed-js', apbud_ROOT_URL . 'admctrlfd/js/tabbed.min.js' );
		wp_enqueue_script( 'ab-sweetalert-js', apbud_ROOT_URL . 'admctrlfd/js/sweetalert.min.js' );
		wp_enqueue_script( 'ab-popover-js', apbud_JS . '/webui-popover.min.js' );
		wp_enqueue_script( 'ab-custom-js', apbud_ROOT_URL . 'admctrlfd/js/custom.js' );
	}
	
	public function apbud_plugin_admin_styles()
	{
		wp_enqueue_style( 'ab-datepicker-min', apbud_CSS . '/datetimepicker.min.css' );
		wp_enqueue_style( 'ab-fullCalendar-css', apbud_ROOT_URL . 'admctrlfd/css/fullcalendar.min.css' );
		wp_enqueue_style( 'ab-sweetalert-css', apbud_ROOT_URL . 'admctrlfd/css/sweetalert.css' );
		wp_enqueue_style( 'ab-admin-style', apbud_ROOT_URL . 'admctrlfd/css/admin-style.css' ); 
		wp_enqueue_style( 'ab-popover-style', apbud_CSS . '/webui-popover.min.css' ); 
		
	}
	
	public function apbud_addAppointmentBuddyMainMenu()
	{
		//main menu
		add_menu_page( '', 'Appointment Buddy&nbsp;&nbsp;', 'manage_options', 'appointment-calendar', array($this, 'apbud_viewCalendar'), 'dashicons-admin-plugins', 23 );
		
		//submenu 1
		add_submenu_page('appointment-calendar', 'Appointment List', 'Appointment List', 'manage_options', 'view-booked-appointment-list', array($this, 'apbud_viewAppointmentList'));
		
		//submenu 2
		add_submenu_page('appointment-calendar', 'Settings', 'Settings', 'manage_options', 'appointment-buddy-settings', array($this, 'apbud_Setting'));
	}
	
	function apbud_viewCalendar()
	{
        require apbud_ADMIN . '/admin-calendar.php';
	}
	
	function apbud_viewAppointmentList()
	{
    	require apbud_ADMIN . '/admin-appointment-list.php';
	}
	
	function apbud_Setting()
	{
        require apbud_ADMIN . '/admin-setting.php';
	}
	
	// add extra php files
	function includes()
	{
		require apbud_ADMIN . '/functions.php';
		require apbud_INC . '/widget.php';		
	}
	
	//It will register the widget -> most important
	function register_widget() {
		register_widget( 'Appointment_buddy_Widget' );
	}
	
	// Translate all text & labels of plugin
	public function apbud_loadAppointmentBuddyLanguage() {
		load_plugin_textdomain('appointmentBuddy', FALSE, apbud_LAN );
	}
} //class-1 ends	

new apbud_PLUGIN;
