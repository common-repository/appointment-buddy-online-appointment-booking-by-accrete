<?php 
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
if(isset($_GET['page']) && $_GET['page'] == 'appointment-buddy-settings') 
{ 
?>
<div class="ab-wrap wrap">
    
    <h1>Appointment Buddy Settings</h1>

    <div class="wp-filter">
        <ul class="filter-links">
            <li><a href="#general-profile"><span class="dashicons dashicons-format-aside"></span>&nbsp;General Profile</a></li>
            <li><a href="#time-slot"><span class="dashicons dashicons-clock"></span>&nbsp;Time Slot</a></li>
            <li><a href="#working-days"><span class="dashicons dashicons-calendar-alt"></span>&nbsp;Working Days</a></li>
           	<li><a href="#treatments"><span class="dashicons dashicons-networking"></span>&nbsp;Add Service</a></li>
            <li><a href="#list-treatments"><span class="dashicons dashicons-list-view"></span>&nbsp;Services List</a></li>
            <li><a href="#holiday"><span class="dashicons dashicons-forms"></span>&nbsp;Holidays</a></li>
        </ul>
    </div>

	<div class="tabbed-content">
        <div id="general-profile" class="tab-content">	
            <h2 class="title">General Profile</h2>
            <?php require_once ("admin-general-profile.php");?>
        </div>
        <!-- /.general-profile -->
        
        <div id="time-slot" class="tab-content">	
            <h2 class="title">Time Slot</h2>
            <?php require_once ("admin-time-slot.php");?>
        </div>
        <!-- /.time-slot -->
        
        <div id="working-days" class="tab-content">	
            <h2 class="title">Working Days</h2>
            <?php require_once ("admin-working-days.php");?>
        </div>
        <!-- /.working-days -->
        
        <div id="treatments" class="tab-content">	
            <h2 class="title">Services</h2>
            <?php require_once ("admin-treatments.php");?>
        </div>
        <!-- /.treatments -->
        
        <div id="list-treatments" class="tab-content">	
            <h2 class="title">List Services</h2>
            <?php require_once ("admin-list-treatments.php");?>
        </div>
        <!-- /.treatments -->
        
        <div id="holiday" class="tab-content">	
            <h2 class="title">Holiday</h2>
            <?php require_once ("admin-holiday.php");?>
        </div>
        <!-- /.holiday -->
	</div>
</div>
<!-- /.wrap -->
<?php } ?>