<?php
	// Exit if accessed directly
	if ( ! defined( 'ABSPATH' ) ) exit;
?>
<?php
$charset_collate = '';
global $wpdb, $tbl_queries;

if ( ! empty($wpdb->charset))
	$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
if ( ! empty($wpdb->collate))
	$charset_collate .= " COLLATE $wpdb->collate";
	
//table names are defined
$abServiceMst = $wpdb->prefix . "abServiceMst"; 
$abHolidayMst = $wpdb->prefix . "abHolidayMst";
$abAdminProfileDetails = $wpdb->prefix . "abAdminProfileDetails";
$abTimeSlotMst = $wpdb->prefix . "abTimeSlotMst";
$abSlotMappingDetails = $wpdb->prefix . "abSlotMappingDetails";
$abAppointmentMst = $wpdb->prefix . "abAppointmentMst";

$tbl_queries = <<<SCHEMA
CREATE TABLE $abServiceMst (
	serviceId BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
	serviceName VARCHAR(255) NOT NULL DEFAULT '',
	serviceDescription TEXT,
	createdDate DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	createdBy int(10) unsigned NOT NULL DEFAULT '0',
	modifiedDate DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	modifiedBy int(10) unsigned NOT NULL DEFAULT '0',
	ipAddress VARCHAR(255) NOT NULL DEFAULT '',
	isDeleted tinyint(1) unsigned NOT NULL DEFAULT '0',
	PRIMARY KEY  (serviceId)
) $charset_collate;
CREATE TABLE $abHolidayMst (
	holidayId BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
	holidayName VARCHAR(255) NOT NULL DEFAULT '',
	holidayDate DATE NOT NULL DEFAULT '0000-00-00',
	createdDate DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	createdBy int(10) unsigned NOT NULL DEFAULT '0',
	modifiedDate DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	modifiedBy int(10) unsigned NOT NULL DEFAULT '0',
	ipAddress VARCHAR(255) NOT NULL DEFAULT '',
	isDeleted tinyint(1) unsigned NOT NULL DEFAULT '0',
	PRIMARY KEY  (holidayId)
) $charset_collate;
CREATE TABLE $abAdminProfileDetails (
	adminProfileId BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
	name VARCHAR(255) NOT NULL DEFAULT '',
	address TEXT,	
	emailId VARCHAR(255) NOT NULL DEFAULT '',
	mobileNo VARCHAR(15) NOT NULL DEFAULT '',
	officePhoneNo VARCHAR(15) NOT NULL DEFAULT '',
	websiteLink TEXT,
	facebookLink TEXT,
	twitterLink TEXT,
	priorDaysToBook int(10) unsigned NOT NULL DEFAULT '1',
	priorMonthsToBook int(10) unsigned NOT NULL DEFAULT '1',
	maxTimeSlots int(10) unsigned NOT NULL DEFAULT '0',
	timeZoneValue VARCHAR(255) NOT NULL DEFAULT '',
	createdDate DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	createdBy int(10) unsigned NOT NULL DEFAULT '0',
	modifiedDate DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	modifiedBy int(10) unsigned NOT NULL DEFAULT '0',
	ipAddress VARCHAR(255) NOT NULL DEFAULT '',
	isDeleted tinyint(1) unsigned NOT NULL DEFAULT '0',
	PRIMARY KEY  (adminProfileId)
) $charset_collate;
CREATE TABLE $abTimeSlotMst (
	slotId BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
	slotName VARCHAR(255) NOT NULL DEFAULT '',
	slotStartTime TIME NOT NULL DEFAULT '00:00:00',
	slotEndTime TIME NOT NULL DEFAULT '00:00:00',
	maxAppointmentsPerSlot int(10) unsigned NOT NULL DEFAULT '0',
	createdDate DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	createdBy int(10) unsigned NOT NULL DEFAULT '0',
	modifiedDate DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	modifiedBy int(10) unsigned NOT NULL DEFAULT '0',
	ipAddress VARCHAR(255) NOT NULL DEFAULT '',
	isDeleted tinyint(1) unsigned NOT NULL DEFAULT '0',
	PRIMARY KEY  (slotId)
) $charset_collate;
CREATE TABLE $abSlotMappingDetails (
	slotMappingId BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
	slotId int(10) unsigned NOT NULL DEFAULT '0',
	workingDay int(10) unsigned NOT NULL DEFAULT '0' COMMENT '"1= MON, 2= TUE, 3= WED, 4= THU, 5= FRI, 6= SAT, 7= SUN"',
	createdDate DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	createdBy int(10) unsigned NOT NULL DEFAULT '0',
	modifiedDate DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	modifiedBy int(10) unsigned NOT NULL DEFAULT '0',
	ipAddress VARCHAR(255) NOT NULL DEFAULT '',
	isDeleted tinyint(1) unsigned NOT NULL DEFAULT '0',
	PRIMARY KEY  (slotMappingId)
) $charset_collate;
CREATE TABLE $abAppointmentMst (
	appointmentId BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
	personName VARCHAR(255) NOT NULL DEFAULT '',
	personEmailId VARCHAR(255) NOT NULL DEFAULT '',
	personMobileNo VARCHAR(15) NOT NULL DEFAULT '',
	personAddress TEXT,
	serviceId int(10) unsigned NOT NULL DEFAULT '0',
	appointmentDate DATE NOT NULL DEFAULT '0000-00-00',
	appointmentSlotMappingId int(10) unsigned NOT NULL DEFAULT '0',
	remarks TEXT,
	createdDate DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	createdBy int(10) unsigned NOT NULL DEFAULT '0',
	modifiedDate DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	modifiedBy int(10) unsigned NOT NULL DEFAULT '0',
	ipAddress VARCHAR(255) NOT NULL DEFAULT '',
	isDeleted tinyint(1) unsigned NOT NULL DEFAULT '0',
	PRIMARY KEY  (appointmentId)
) $charset_collate;
SCHEMA;

require_once ABSPATH . 'wp-admin/includes/upgrade.php';
dbDelta($tbl_queries);