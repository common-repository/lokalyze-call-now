<?php
/*
Plugin Name: CALL ME NOW
Description: The Lokalyze Call Me Now Button
Version: 3.0
Author: LOKALYZE
Author URI: http://lokalyze.com 
*/ 

/***************** Static *****************/
if ( !defined( 'CMN_PLUGIN_BASENAME' ) ) define( 'CMN_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
if ( !defined( 'CMN_PLUGIN_NAME' ) ) define( 'CMN_PLUGIN_NAME', trim( dirname( CMN_PLUGIN_BASENAME ), '/' ) );
if ( !defined( 'CMN_PLUGIN_URL' ) )	define( 'CMN_PLUGIN_URL', WP_PLUGIN_URL . '/' . CMN_PLUGIN_NAME );
/***************** End Static *****************/

/***************** Enqueuing Scripts and Styles For Frontend as well as Backend *****************/
if(is_admin()){
	add_action('admin_enqueue_scripts', 'cmn_enqueue_style');
	if( ! function_exists( 'cmn_enqueue_style' ) ){
		function cmn_enqueue_style(){
			wp_register_style( 'cmn-admin-style', CMN_PLUGIN_URL.'/css/lcn_admin_style.css' );
			wp_enqueue_style( 'cmn-admin-style' );
			wp_enqueue_style( 'wp-color-picker' );
			wp_register_script( 'cmn-admin-js', CMN_PLUGIN_URL.'/js/lcn_adminjs.js', array( 'wp-color-picker' ), false, true );
			wp_localize_script( 'cmn-admin-js', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ), 'we_value' => 12343335 ) );
			wp_enqueue_script( 'cmn-admin-js' );
		}
	}
}else {
	add_action('wp_enqueue_scripts', 'cmn_enqueue_style');
	if( ! function_exists( 'cmn_enqueue_style' ) ){
		function cmn_enqueue_style(){
		    wp_register_style( 'cmn-admin-style', CMN_PLUGIN_URL.'/css/lcn_admin_style.css' );
			wp_enqueue_style( 'cmn-admin-style' );
			wp_enqueue_style( 'wp-color-picker' );
			wp_register_script( 'cmn-admin-js', CMN_PLUGIN_URL.'/js/lcn_adminjs.js', array( 'wp-color-picker' ), false, true );
			wp_localize_script( 'cmn-admin-js', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ), 'we_value' => 12343335 ) );
			wp_enqueue_script( 'cmn-admin-js' );
		}
	}
}
/***************** End Enqueuing Scripts and Styles For Frontend as well as Backend *****************/

/***************** Plugin Activation Hook *****************/
register_activation_hook( __FILE__, 'callmenow_activate' );
function callmenow_activate(){
	global $wp_version, $wpdb;
	$required_wp_version = '3.5';
	if(version_compare($wp_version, $required_wp_version, '<')){
		$err_msg = __('This plugin requires WordPress version '.$required_wp_version.' or higher.');
		die($err_msg);
	}
	$charset_collate = $wpdb->get_charset_collate();
	$table_name = $wpdb->prefix.'callmenow';
	if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name){
		$wpdb->query($wpdb->prepare( "CREATE TABLE $table_name ( id mediumint(9) NOT NULL AUTO_INCREMENT, openinghours varchar(500) DEFAULT '' NOT NULL, openinghourformat varchar(500) DEFAULT '' NOT NULL, callnowbutton varchar(500) DEFAULT '' NOT NULL, UNIQUE KEY id (id) ) $charset_collate", "" ));
		$openinghourformat = '12';
		$callnw = '1';
		$phonenumber = '';
		$iconcolor = '#009900';
		$appearance = 'right';
		$tracking = '0';
		$tscript = '';
		$callnowbut = $callnw."-|$|-".$phonenumber."-|$|-".$iconcolor."-|$|-".$appearance."-|$|-".$tracking."-|$|-".$tscript;
		$wpdb->query($wpdb->prepare( "INSERT INTO $table_name ( openinghourformat, callnowbutton ) VALUES ( %s, %s )", array( $openinghourformat, $callnowbut ) ));
	}
	return true;
}
/***************** End Plugin Activation Hook *****************/

/***************** Plugin Uninstall Hook *****************/
register_uninstall_hook( __FILE__, 'callmenow_uninstall' );
function callmenow_uninstall() {
	return true;
}
/***************** End Plugin Uninstall Hook *****************/

/***************** Plugin Deactivation Hook *****************/
register_deactivation_hook( __FILE__, 'callmenow_deactivation' );
function callmenow_deactivation() {
  	global $wpdb;
	$table_name = $wpdb->prefix.'callmenow';
	$wpdb->query("DROP TABLE IF EXISTS $table_name");
	return true;
}
/***************** End Plugin Deactivation Hook *****************/

/***************** Adding Admin Menus *****************/
add_action('admin_menu', 'cmn_setup_menu');
function cmn_setup_menu(){
	add_menu_page( 'Call Me Now', 'Call Me Now', 'manage_options', 'lokalyze-call-now-button', 'lcn_callnowalone_function', 'dashicons-phone' );
	add_submenu_page( 'lokalyze-call-now-button', 'Working Hours', 'Working Hours', 'manage_options', 'lokalyze-call-now-button-working-hours', 'lcn_hours_function' );
	add_submenu_page( 'lokalyze-call-now-button', 'User Guide', 'User Guide', 'manage_options', 'lokalyze-call-now-button-user-guide', 'lcn_user_guide_function' );
}
/***************** End Adding Admin Menus *****************/

/***************** Call Me Now Menu content *****************/
function lcn_callnowalone_function(){
	global $wpdb;
	$table_name = $wpdb->prefix.'callmenow'; 
	$callnowbuttons = $wpdb->get_results("SELECT * FROM $table_name");
	foreach($callnowbuttons as $callnowbut){
		$callsbutton = $callnowbut->callnowbutton;
		$callnowbutton = explode("-|$|-", $callsbutton);
		
		?>
		<div class="maincontainer">
			<div class="mainwrap">
				<div class="maincontent">
					<h1>CALL ME NOW BUTTON - PLUGIN SETTINGS</h1>
					<p>The Call Now button here can be integrated into your Google Analytics reporting. It will only show on mobile devices.<br />If you have already installed the default Google Universal Analytics (analytics.js) on your site, you are set up to start tracking calls to your business through this plugin.</p>
					<div class="formcont">
						<form id="lokalyze_ls_callnowbutton_form" action="" name="" method="post">
							<table class="lsform_table">
								<tbody>
									<tr>
										<th><label for="lokalyze_lcn_cnbutton">Call Now Button</label></th>
										<td><fieldset><input type="radio" id="lokalyze_lcn_cnbutton" name="lokalyze_lcn_cnbutton" value="1" <?php if( $callnowbutton[0] == "1" ){ ?> checked="checked" <?php } ?> /> Enable<br /><input type="radio" id="lokalyze_lcn_cnbutton" name="lokalyze_lcn_cnbutton" value="0" <?php if( $callnowbutton[0] == "0" ){ ?> checked="checked" <?php } ?> /> Disable</fieldset></td>
									</tr>
									<tr>
										<th><label for="lokalyze_lcn_callnowphone">Phone number:</label></th>
										<td><fieldset><input type="text" id="lokalyze_lcn_callnowphone" name="lokalyze_lcn_callnowphone" value="<?php echo esc_html( $callnowbutton[1] ); ?>" /></fieldset></td>
									</tr>
									<tr>
										<th><label for="lokalyze_lcn_iconcolor">Icon color:</label></th>
										<td><fieldset><input type="text" id="lokalyze_lcn_iconcolor" name="lokalyze_lcn_iconcolor" value="<?php echo esc_html( $callnowbutton[2] ); ?>" data-default-color="#009900" /></fieldset></td>
									</tr>
									<tr>
										<th><label for="lokalyze_lcn_position">Appearance:</label></th>
										<td><fieldset><input type="radio" id="lokalyze_lcn_position" name="lokalyze_lcn_position" value="topr" <?php if( $callnowbutton[3] == "topr" ){ ?>  checked="checked" <?php } ?> /> Top Right corner<br /><input type="radio" id="lokalyze_lcn_position" name="lokalyze_lcn_position" value="topl" <?php if( $callnowbutton[3] == "topl" ){ ?>  checked="checked" <?php } ?> /> Top Left corner<br /><input type="radio" id="lokalyze_lcn_position" name="lokalyze_lcn_position" value="right" <?php if( $callnowbutton[3] == "right" ){ ?>  checked="checked" <?php } ?> /> Bottom Right corner<br /><input type="radio" id="lokalyze_lcn_position" name="lokalyze_lcn_position" value="left" <?php if( $callnowbutton[3] == "left" ){ ?> checked="checked" <?php } ?> /> Bottom Left corner<br /><input type="radio" id="lokalyze_lcn_position" name="lokalyze_lcn_position" value="full" <?php if( $callnowbutton[3] == "full" ){ ?> checked="checked" <?php } ?> /> Full width bottom</fieldset></td>
									</tr>
									<tr>
										<th><label for="lokalyze_lcn_flltxt">Full Width Button Text: ( Maximum 15 CAPITAL characters ) </label></th>
										<td><fieldset><input type="text" maxlength="15" id="lokalyze_lcn_flltxt" name="lokalyze_lcn_flltxt" value="<?php echo esc_html( $callnowbutton[6] ); ?>" /></fieldset></td>
									</tr>
									<tr>
										<th><label for="lokalyze_lcn_tracking">Click tracking:</label></th>
										<td><fieldset><input type="radio" id="lokalyze_lcn_tracking" name="lokalyze_lcn_tracking" value="1" <?php if( $callnowbutton[4] == "1" ){ ?> checked="checked" <?php } ?> /> Google Universal Analytics (analytics.js)<br /><input type="radio" id="lokalyze_lcn_tracking" name="lokalyze_lcn_tracking" value="0" <?php if( $callnowbutton[4] == "0" ){ ?> checked="checked" <?php } ?> /> Disabled</fieldset><div class="desccription"><p>Once click tracking has been set up and working on your site for a day, go to the Content section of the reports and view Event Tracking.</p></div></td>
									</tr>
									<tr>
										<th><label for="lokalyze_lcn_tracking_code">Tracking script:</label></th>
										<td><fieldset><textarea id="lokalyze_lcn_tracking_code" name="lokalyze_lcn_tracking_code"><?php echo esc_textarea( $callnowbutton[5] ); ?></textarea></fieldset><div class="desccription"><p>If you have added Google Analytics tracking to your website with a plugin such as the Monster Google Analytics plugin, you will need to add the Google Analytics tracking code into the 'Tracking Script' text box.  To do this, go to:<br /><br />Google Analytics account > Admin > Tracking Info > Tracking Code.<br /><br />Copy the script and then go to the admin section of the plugin in your Wordpress Control Panel.  Once there, select the Call Now Button page of the Lokalyze SEO Plugin and paste the code into the tracking script box.  The final step is clicking 'save changes'.<br /><br />A generic version of the code is found here for your reference only:<br /><br /><?php $scval = "&lt;script&gt;<br/>(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){<br/>(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),<br/>m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)<br/>})(window,document,'script','https://www.google-analytics.com/analytics.js','ga');<br/><br/>ga('create', 'UA-xxxxxxxxx-1', 'auto');<br/>ga('send', 'pageview');<br/><br/>&lt;/script&gt;"; echo $scval; ?></p></div></td>
									</tr>
								</tbody>
							</table>
							<p><input type="button" value="Save Changes" name="callnowbtnfrm" class="button button-primary lokalyze-save-callnowbutton"><span class="successmessagenew">Settings updated successfully</span></p>
						</form>
					</div>
					<div class="createdby"><p>Developed by: <a href="http://www.lokalyze.com/" target="_blank">LOKALYZE</a></p></div>
				</div>
			</div>
		</div>
<?php
	}
}
/***************** End Call Me Now Menu Content *****************/

/***************** CALL NOW BUTTON Settings *****************/
add_action( 'wp_ajax_savecallnowbutton', 'lcn_ajax_add_btnsettings' );
function lcn_ajax_add_btnsettings(){
	$user = wp_get_current_user();
	$allowed_roles = array('administrator');
	if( array_intersect($allowed_roles, $user->roles ) ){
		if( current_user_can('manage_options') ){
			$butstatus = $_POST['clbtstus'];
			if($butstatus != ""){ $butstatus = sanitize_text_field( $butstatus ); }else { $butstatus = sanitize_text_field( $butstatus ); }
			$butnumber = $_POST['clbtnum'];
			if($butnumber != ""){ $butnumber = sanitize_text_field( $butnumber ); }else { $butnumber = sanitize_text_field( $butnumber ); }
			$butcolour = $_POST['clbtclr'];
			if($butcolour != ""){ $butcolour = sanitize_text_field( $butcolour ); }else { $butcolour = sanitize_text_field( $butcolour ); }
			$butappear = $_POST['clbtapp'];
			if($butappear != ""){ $butappear = sanitize_text_field( $butappear ); }else { $butappear = sanitize_text_field( $butappear ); }
			$butcaltrk = $_POST['clbtcltrk'];
			if($butcaltrk != ""){ $butcaltrk = sanitize_text_field( $butcaltrk ); }else { $butcaltrk = sanitize_text_field( $butcaltrk ); }			
			$buttrkcod = $_POST['clbttrkcod'];
			if($buttrkcod != ""){
				$buttrkcod = implode( "\n", array_map( 'sanitize_text_field', explode( "\n", $buttrkcod ) ) );
				$finaltrkcode = '<script>'.$buttrkcod.'</script>';
			}else {
				$buttrkcod = implode( "\n", array_map( 'sanitize_text_field', explode( "\n", $buttrkcod ) ) );
				$finaltrkcode = $buttrkcod;
			}	
			
			$butfulltxt = $_POST['clbtfulltxt'];
			if($butfulltxt != ""){ $butfulltxt = sanitize_text_field( $butfulltxt ); }else { $butfulltxt = sanitize_text_field( $butfulltxt ); }
			$callnowbutton = $butstatus."-|$|-".$butnumber."-|$|-".$butcolour."-|$|-".$butappear."-|$|-".$butcaltrk."-|$|-".$finaltrkcode."-|$|-".$butfulltxt;
			global $wpdb;
	        $table_name = $wpdb->prefix.'callmenow';
            $wpdb->query( "UPDATE $table_name SET callnowbutton = '$callnowbutton' where id = 1" );
		}
	}
	echo "Setting Updated Successfully";
	exit();
}
/***************** CALL NOW BUTTON Settings *****************/

/***************** Bind Analytics Code in Header *****************/
add_action('wp_head', 'lcn_aloneanalyticshead');
function lcn_aloneanalyticshead(){
	global $wpdb;
	$table_name = $wpdb->prefix.'callmenow';
	$callnowbuttons = $wpdb->get_results("SELECT * FROM $table_name");
	foreach($callnowbuttons as $callnowbut){
		$callsbutton = $callnowbut->callnowbutton;
		$callnowbutton = explode("-|$|-", $callsbutton);
		echo $callnowbutton[5];
	}
}
/***************** End Bind Analytics Code in Header *****************/

/***************** Display Icon **************************************/
if(!is_admin()){
	global $wpdb;
	$table_name = $wpdb->prefix.'callmenow';
	$callnowbuttons = $wpdb->get_results("SELECT * FROM $table_name");
	foreach($callnowbuttons as $callnowbut){
		$callsbutton = $callnowbut->callnowbutton;
		$callnowbutton = explode("-|$|-", $callsbutton);
		if($callnowbutton[0] == '1'){
			function lcn_alonechangecolor($color, $direction){
				if(!preg_match('/^#?([0-9a-f]{2})([0-9a-f]{2})([0-9a-f]{2})$/i', $color, $parts));
				if(!isset($direction) || $direction == "lighter"){ $change = 45; }else { $change = -50; }
				for($i = 1; $i <= 3; $i++){
					$parts[$i] = hexdec($parts[$i]);
					$parts[$i] = round($parts[$i] + $change);
					if($parts[$i] > 255){ $parts[$i] = 255; }else if($parts[$i] < 0){ $parts[$i] = 0; }
					$parts[$i] = dechex($parts[$i]);
				}
				$output = '#' . str_pad($parts[1],2,"0",STR_PAD_LEFT) . str_pad($parts[2],2,"0",STR_PAD_LEFT) . str_pad($parts[3],2,"0",STR_PAD_LEFT);
				return $output;
			} 
			function lcn_alonesvg($color2){
				$phone1 = '<path d="M7.104 14.032l15.586 1.984c0 0-0.019 0.5 0 0.953c0.029 0.756-0.26 1.534-0.809 2.1 l-4.74 4.742c2.361 3.3 16.5 17.4 19.8 19.8l16.813 1.141c0 0 0 0.4 0 1.1 c-0.002 0.479-0.176 0.953-0.549 1.327l-6.504 6.505c0 0-11.261 0.988-25.925-13.674C6.117 25.3 7.1 14 7.1 14" fill="'.$color2.'"/><path d="M7.104 13.032l6.504-6.505c0.896-0.895 2.334-0.678 3.1 0.35l5.563 7.8 c0.738 1 0.5 2.531-0.36 3.426l-4.74 4.742c2.361 3.3 5.3 6.9 9.1 10.699c3.842 3.8 7.4 6.7 10.7 9.1 l4.74-4.742c0.897-0.895 2.471-1.026 3.498-0.289l7.646 5.455c1.025 0.7 1.3 2.2 0.4 3.105l-6.504 6.5 c0 0-11.262 0.988-25.925-13.674C6.117 24.3 7.1 13 7.1 13" fill="#fff"/>';
				$svg = '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 60 60">' . $phone1 . '</svg>';
				return base64_encode($svg);
			}
			add_action('wp_head', 'lcn_alonecallnowbutton_head');
			function lcn_alonecallnowbutton_head(){
				global $wpdb;
	            $table_name = $wpdb->prefix.'callmenow';
				$callnowbuttons = $wpdb->get_results("SELECT * FROM $table_name");
				foreach($callnowbuttons as $callnowbut){
					$callsbutton = $callnowbut->callnowbutton;
					$callnowbutton = explode("-|$|-", $callsbutton);
					if($callnowbutton[3] == 'topr'){
						$ButtonAppearance = "width:48px;right:0;border-radius:50px;top:0px; margin:5px;";
						$ButtonExtra = "";
					}else if($callnowbutton[3] == 'topl'){
						$ButtonAppearance = "width:48px;left:0;border-radius:50px;top:0px; margin:5px;";
						$ButtonExtra = "";
					}else if($callnowbutton[3] == 'full'){
						$ButtonAppearance = "width:100%;left:0;bottom:0px;color: #fff;text-align: center;font-weight: bold;font-size: 20px;line-height: 40px;background-position: 80px center !important; margin:0;";
						$ButtonExtra = "body {padding-bottom:0;}";
					}else if($callnowbutton[3] == 'left'){
						$ButtonAppearance = "width:48px;left:0;border-radius:50px;bottom:0px; margin:5px;";
						$ButtonExtra = "";
					}else {
						$ButtonAppearance = "width:48px;right:0;border-radius:50px;bottom:0px; margin:5px;";
						$ButtonExtra = "";
					}
					echo "<style>#lcn-callnowbutton {display:none;} @media screen and (max-width:980px){#lcn-callnowbutton {display:block; ".$ButtonAppearance." height:48px; position:fixed; border:2px solid ".lcn_alonechangecolor($callnowbutton[2],'darker')."; background:url(data:image/svg+xml;base64,".lcn_alonesvg(lcn_alonechangecolor($callnowbutton[2], 'darker') ).") center 2px no-repeat ".$callnowbutton[2]."; text-decoration:none; box-shadow:0 0 5px #888; z-index:999998; background-size:30px 40px;}".$ButtonExtra."}</style>";
				}
			}
			add_action('wp_footer', 'lcn_alonecallnowbutton_footer');
			function lcn_alonecallnowbutton_footer(){
				global $wpdb;
	            $table_name = $wpdb->prefix.'callmenow';
				$callnowbuttons = $wpdb->get_results("SELECT * FROM $table_name");
				foreach($callnowbuttons as $callnowbut){
					$callsbutton = $callnowbut->callnowbutton;
					$callnowbutton = explode("-|$|-", $callsbutton);
					$hrformat = $callnowbut->openinghourformat;
					$openinghours = $callnowbut->openinghours;
					$openhours = explode("-|$|-", $openinghours);
					$monday = explode("-|#|-", $openhours[0]);
					$tuesday = explode("-|#|-", $openhours[1]);
					$wednesday = explode("-|#|-", $openhours[2]);
					$thursday = explode("-|#|-", $openhours[3]);
					$friday = explode("-|#|-", $openhours[4]);
					$saturday = explode("-|#|-", $openhours[5]);
					$sunday = explode("-|#|-", $openhours[6]);
					if($callnowbutton[4] == '1'){
						$tracking = "onclick=\"ga('send', 'event', 'Contact', 'Call Now Button', 'Phone');\"";
					}else {
						$tracking = "";
					}
					$fullwidthtxt = $callnowbutton[6];
					$currentday = date("l");
					$days_array = array("Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday");
					$days_variable_array = array($monday,$tuesday,$wednesday,$thursday,$friday,$saturday,$sunday);
					
					if( in_array($currentday,$days_array) ){
						if( $hrformat == "12"){
							$current_time = current_time( 'mysql' );
							$currenttime = strtotime($current_time);
							$current_date_use = date("Y-m-d");
							$current_key = array_search($currentday, $days_array);
							$check_variable = $days_variable_array[$current_key];
								
							if(($check_variable[0] == 12) && ($check_variable[2] == "AM")){
								$check_variable[0] = $check_variable[0] - 12;
							}
							if(($check_variable[0] == 12) && ($check_variable[2] == "PM")){
								$check_variable[0] = $check_variable[0];
							}
							if(($check_variable[0] < 12) && ($check_variable[2] == "AM")){
								$check_variable[0] = $check_variable[0];
							}
							if(($check_variable[0] < 12) && ($check_variable[2] == "PM")){
								$check_variable[0] = $check_variable[0] + 12;
							}
							$starttime = "$current_date_use $check_variable[0]:$check_variable[1]:00";
							
							if(($check_variable[3] == 12) && ($check_variable[5] == "AM")){
								$check_variable[3] = $check_variable[3] - 12;
							}
							if(($check_variable[3] == 12) && ($check_variable[5] == "PM")){
								$check_variable[3] = $check_variable[3];
							}
							if(($check_variable[3] < 12) && ($check_variable[5] == "AM")){
								$check_variable[3] = $check_variable[3];
							}
							if(($check_variable[3] < 12) && ($check_variable[5] == "PM")){
								$check_variable[3] = $check_variable[3] + 12;
							}
							$endtime = "$current_date_use $check_variable[3]:$check_variable[4]:00";					
							
							$datetime4 = new DateTime($current_time);
							$datetime5 = new DateTime($starttime);
							$datetime6 = new DateTime($endtime); 
							
							if($callnowbutton[3] == 'full'){
								$mybtntxt = $fullwidthtxt;
							}else {
								$mybtntxt = '&nbsp;';
							}
							
							if(($datetime4 >= $datetime5) && ($datetime4 <= $datetime6)){
								echo '<a class="lcmnbtn12" href="tel:'.$callnowbutton[1].'" id="lcn-callnowbutton" '.$tracking.'>'.$mybtntxt.'</a>';
							}
						}else {
							$current_time = current_time( 'mysql' );
							$currenttime = strtotime($current_time);
							$current_date_use = date("Y-m-d");
							$current_key = array_search($currentday, $days_array);
							$check_variable = $days_variable_array[$current_key];
							
							$starttime = "$current_date_use $check_variable[0]:$check_variable[1]:00";
							$endtime = "$current_date_use $check_variable[3]:$check_variable[4]:00";
							
							$datetime1 = new DateTime($current_time);
							$datetime2 = new DateTime($starttime);
							$datetime3 = new DateTime($endtime); 
							
							if($callnowbutton[3] == 'full'){
								$mybtntxt = $fullwidthtxt;
							}else {
								$mybtntxt = '&nbsp;';
							}
							
							if(($datetime1 >= $datetime2) && ($datetime1 <= $datetime3)){
								echo '<a class="lcmnbtn24" href="tel:'.$callnowbutton[1].'" id="lcn-callnowbutton" '.$tracking.'>'.$mybtntxt.'</a>';
							}
						}
					}
				}
			}
		}
	}
}
/***************** End Display Icon ***************************************/

/***************** Working Hours Content *****************/
function lcn_hours_function(){
	global $wpdb;
	$table_name = $wpdb->prefix.'callmenow';
	$locationdetails = $wpdb->get_results("SELECT * FROM $table_name");
	foreach($locationdetails as $locdet){
		$openinghoursformat = $locdet->openinghourformat;
		$openinghours = $locdet->openinghours;
		$openinghourdet = explode("-|$|-", $openinghours);
		$mondaydetails = $openinghourdet[0];
		$monday = explode("-|#|-", $mondaydetails);
		$tuesdaydetails = $openinghourdet[1];
		$tuesday = explode("-|#|-", $tuesdaydetails);
		$wednesdaydetails = $openinghourdet[2];
		$wednesday = explode("-|#|-", $wednesdaydetails);
		$thursdaydetails = $openinghourdet[3];
		$thursday = explode("-|#|-", $thursdaydetails);
		$fridaydetails = $openinghourdet[4];
		$friday = explode("-|#|-", $fridaydetails);
		$saturdaydetails = $openinghourdet[5];
		$saturday = explode("-|#|-", $saturdaydetails);
		$sundaydetails = $openinghourdet[6];
		$sunday = explode("-|#|-", $sundaydetails);
?>
	<div class="maincontainer">
		<div class="mainwrap">
			<div class="maincontent">
				<h1>CALL ME NOW BUTTON - CHANGE WORKING HOURS</h1>
				<div class="formcont">
					<form id="lokalyze_lcs_hours_settings_form" action="" name="" method="post">
						<div class="contentcontainer">
							<h3 class="title">Working hours format</h3>
							<table class="lsform_table openhours">
								<tbody>
									<tr>
										<th><label for="lcn_opening_hours_format" style="text-align:left; margin:0;">Working hours format:</label></th>
										<td><fieldset><select style="text-align:left; margin:0;" id="lcn_opening_hours_format" name="lcn_opening_hours_format"><option value="12" <?php if($openinghoursformat == "12"){ ?> selected="selected" <?php } ?>>12 hours format</option><option value="24" <?php if($openinghoursformat == "24"){ ?> selected="selected" <?php } ?>>24 hours format</option></select></fieldset><div class="desccription"><p>Working hours formats are either 12 or 24 hours.</p></div></td>
									</tr>
								</tbody>
							</table>
						</div>
						<div class="contentcontainer">
							<h3 class="title">Working hours</h3>
							<table class="lsform_table openhours">
								<tbody>
									<tr>
										<td>&nbsp;</td>
										<td colspan="2"><label>Working hours</label></td>
									</tr>
									<tr>
										<td><label>Day</label></td>
										<td><label>Opens at</label></td>
										<td><label>Closes at</label></td>
									</tr>
									<tr>
										<td></td>
										<td><label><?php if($openinghoursformat == "12"){ ?> (Hour | Minutes | AM-PM) <? }else { ?> (Hour | Minutes) <?php } ?></label></td>
										<td><label><?php if($openinghoursformat == "12"){ ?> (Hour | Minutes | AM-PM) <? }else { ?> (Hour | Minutes) <?php } ?></label></td>
									</tr>
									<tr>
										<td><label>Monday</label></td>
										<td><select id="lcn_opening_hours_opens_at_hour_monday" name="lcn_opening_hours_opens_at_hour_monday"><?php if($openinghoursformat == "12"){ ?><option value="" <?php if( $monday[0] == "" ){ ?> selected="selected" <?php } ?>></option><option value="01" <?php if( $monday[0] == "01" ){ ?> selected="selected" <?php } ?>>01</option><option value="02" <?php if( $monday[0] == "02" ){ ?> selected="selected" <?php } ?>>02</option><option value="03" <?php if( $monday[0] == "03" ){ ?> selected="selected" <?php } ?>>03</option><option value="04" <?php if( $monday[0] == "04" ){ ?> selected="selected" <?php } ?>>04</option><option value="05" <?php if( $monday[0] == "05" ){ ?> selected="selected" <?php } ?>>05</option><option value="06" <?php if( $monday[0] == "06" ){ ?> selected="selected" <?php } ?>>06</option><option value="07" <?php if( $monday[0] == "07" ){ ?> selected="selected" <?php } ?>>07</option><option value="08" <?php if( $monday[0] == "08" ){ ?> selected="selected" <?php } ?>>08</option><option value="09" <?php if( $monday[0] == "09" ){ ?> selected="selected" <?php } ?>>09</option><option value="10" <?php if( $monday[0] == "10" ){ ?> selected="selected" <?php } ?>>10</option><option value="11" <?php if( $monday[0] == "11" ){ ?> selected="selected" <?php } ?>>11</option><option value="12" <?php if( $monday[0] == "12" ){ ?> selected="selected" <?php } ?>>12</option><? }else { ?><option value="" <?php if( $monday[0] == "" ){ ?> selected="selected" <?php } ?>></option><option value="00" <?php if( $monday[0] == "00" ){ ?> selected="selected" <?php } ?>>00</option><option value="01" <?php if( $monday[0] == "01" ){ ?> selected="selected" <?php } ?>>01</option><option value="02" <?php if( $monday[0] == "02" ){ ?> selected="selected" <?php } ?>>02</option><option value="03" <?php if( $monday[0] == "03" ){ ?> selected="selected" <?php } ?>>03</option><option value="04" <?php if( $monday[0] == "04" ){ ?> selected="selected" <?php } ?>>04</option><option value="05" <?php if( $monday[0] == "05" ){ ?> selected="selected" <?php } ?>>05</option><option value="06" <?php if( $monday[0] == "06" ){ ?> selected="selected" <?php } ?>>06</option><option value="07" <?php if( $monday[0] == "07" ){ ?> selected="selected" <?php } ?>>07</option><option value="08" <?php if( $monday[0] == "08" ){ ?> selected="selected" <?php } ?>>08</option><option value="09" <?php if( $monday[0] == "09" ){ ?> selected="selected" <?php } ?>>09</option><option value="10" <?php if( $monday[0] == "10" ){ ?> selected="selected" <?php } ?>>10</option><option value="11" <?php if( $monday[0] == "11" ){ ?> selected="selected" <?php } ?>>11</option><option value="12" <?php if( $monday[0] == "12" ){ ?> selected="selected" <?php } ?>>12</option><option value="13" <?php if( $monday[0] == "13" ){ ?> selected="selected" <?php } ?>>13</option><option value="14" <?php if( $monday[0] == "14" ){ ?> selected="selected" <?php } ?>>14</option><option value="15" <?php if( $monday[0] == "15" ){ ?> selected="selected" <?php } ?>>15</option><option value="16" <?php if( $monday[0] == "16" ){ ?> selected="selected" <?php } ?>>16</option><option value="17" <?php if( $monday[0] == "17" ){ ?> selected="selected" <?php } ?>>17</option><option value="18" <?php if( $monday[0] == "18" ){ ?> selected="selected" <?php } ?>>18</option><option value="19" <?php if( $monday[0] == "19" ){ ?> selected="selected" <?php } ?>>19</option><option value="20" <?php if( $monday[0] == "20" ){ ?> selected="selected" <?php } ?>>20</option><option value="21" <?php if( $monday[0] == "21" ){ ?> selected="selected" <?php } ?>>21</option><option value="22" <?php if( $monday[0] == "22" ){ ?> selected="selected" <?php } ?>>22</option><option value="23" <?php if( $monday[0] == "23" ){ ?> selected="selected" <?php } ?>>23</option><?php } ?></select><select id="lcn_opening_hours_opens_at_minutes_monday" name="lcn_opening_hours_opens_at_minutes_monday"><option value="" <?php if( $monday[1] == "" ){ ?> selected="selected" <?php } ?>></option><option value="00" <?php if( $monday[1] == "00" ){ ?> selected="selected" <?php } ?>>00</option><option value="15" <?php if( $monday[1] == "15" ){ ?> selected="selected" <?php } ?>>15</option><option value="30" <?php if( $monday[1] == "30" ){ ?> selected="selected" <?php } ?>>30</option><option value="45" <?php if( $monday[1] == "45" ){ ?> selected="selected" <?php } ?>>45</option></select><?php if($openinghoursformat == "12"){ ?><select id="lcn_opening_hours_opens_at_am_pm_monday" name="lcn_opening_hours_opens_at_am_pm_monday"><option value="" <?php if( $monday[2] == "" ){ ?> selected="selected" <?php } ?>></option><option value="AM" <?php if( $monday[2] == "AM" ){ ?> selected="selected" <?php } ?>>AM</option><option value="PM" <?php if( $monday[2] == "PM" ){ ?> selected="selected" <?php } ?>>PM</option></select><? } ?></td>
										<td><select id="lcn_opening_hours_closes_at_hour_monday" name="lcn_opening_closes_opens_at_hour_monday"><?php if($openinghoursformat == "12"){ ?><option value="" <?php if( $monday[3] == "" ){ ?> selected="selected" <?php } ?>></option><option value="01" <?php if( $monday[3] == "01" ){ ?> selected="selected" <?php } ?>>01</option><option value="02" <?php if( $monday[3] == "02" ){ ?> selected="selected" <?php } ?>>02</option><option value="03" <?php if( $monday[3] == "03" ){ ?> selected="selected" <?php } ?>>03</option><option value="04" <?php if( $monday[3] == "04" ){ ?> selected="selected" <?php } ?>>04</option><option value="05" <?php if( $monday[3] == "05" ){ ?> selected="selected" <?php } ?>>05</option><option value="06" <?php if( $monday[3] == "06" ){ ?> selected="selected" <?php } ?>>06</option><option value="07" <?php if( $monday[3] == "07" ){ ?> selected="selected" <?php } ?>>07</option><option value="08" <?php if( $monday[3] == "08" ){ ?> selected="selected" <?php } ?>>08</option><option value="09" <?php if( $monday[3] == "09" ){ ?> selected="selected" <?php } ?>>09</option><option value="10" <?php if( $monday[3] == "10" ){ ?> selected="selected" <?php } ?>>10</option><option value="11" <?php if( $monday[3] == "11" ){ ?> selected="selected" <?php } ?>>11</option><option value="12" <?php if( $monday[3] == "12" ){ ?> selected="selected" <?php } ?>>12</option><? }else { ?><option value="" <?php if( $monday[3] == "" ){ ?> selected="selected" <?php } ?>></option><option value="00" <?php if( $monday[3] == "00" ){ ?> selected="selected" <?php } ?>>00</option><option value="01" <?php if( $monday[3] == "01" ){ ?> selected="selected" <?php } ?>>01</option><option value="02" <?php if( $monday[3] == "02" ){ ?> selected="selected" <?php } ?>>02</option><option value="03" <?php if( $monday[3] == "03" ){ ?> selected="selected" <?php } ?>>03</option><option value="04" <?php if( $monday[3] == "04" ){ ?> selected="selected" <?php } ?>>04</option><option value="05" <?php if( $monday[3] == "05" ){ ?> selected="selected" <?php } ?>>05</option><option value="06" <?php if( $monday[3] == "06" ){ ?> selected="selected" <?php } ?>>06</option><option value="07" <?php if( $monday[3] == "07" ){ ?> selected="selected" <?php } ?>>07</option><option value="08" <?php if( $monday[3] == "08" ){ ?> selected="selected" <?php } ?>>08</option><option value="09" <?php if( $monday[3] == "09" ){ ?> selected="selected" <?php } ?>>09</option><option value="10" <?php if( $monday[3] == "10" ){ ?> selected="selected" <?php } ?>>10</option><option value="11" <?php if( $monday[3] == "11" ){ ?> selected="selected" <?php } ?>>11</option><option value="12" <?php if( $monday[3] == "12" ){ ?> selected="selected" <?php } ?>>12</option><option value="13" <?php if( $monday[3] == "13" ){ ?> selected="selected" <?php } ?>>13</option><option value="14" <?php if( $monday[3] == "14" ){ ?> selected="selected" <?php } ?>>14</option><option value="15" <?php if( $monday[3] == "15" ){ ?> selected="selected" <?php } ?>>15</option><option value="16" <?php if( $monday[3] == "16" ){ ?> selected="selected" <?php } ?>>16</option><option value="17" <?php if( $monday[3] == "17" ){ ?> selected="selected" <?php } ?>>17</option><option value="18" <?php if( $monday[3] == "18" ){ ?> selected="selected" <?php } ?>>18</option><option value="19" <?php if( $monday[3] == "19" ){ ?> selected="selected" <?php } ?>>19</option><option value="20" <?php if( $monday[3] == "20" ){ ?> selected="selected" <?php } ?>>20</option><option value="21" <?php if( $monday[3] == "21" ){ ?> selected="selected" <?php } ?>>21</option><option value="22" <?php if( $monday[3] == "22" ){ ?> selected="selected" <?php } ?>>22</option><option value="23" <?php if( $monday[3] == "23" ){ ?> selected="selected" <?php } ?>>23</option><?php } ?></select><select id="lcn_opening_hours_closes_at_minutes_monday" name="lcn_opening_hours_closes_at_minutes_monday"><option value="" <?php if( $monday[4] == "" ){ ?> selected="selected" <?php } ?>></option><option value="00" <?php if( $monday[4] == "00" ){ ?> selected="selected" <?php } ?>>00</option><option value="15" <?php if( $monday[4] == "15" ){ ?> selected="selected" <?php } ?>>15</option><option value="30" <?php if( $monday[4] == "30" ){ ?> selected="selected" <?php } ?>>30</option><option value="45" <?php if( $monday[4] == "45" ){ ?> selected="selected" <?php } ?>>45</option></select><?php if($openinghoursformat == "12"){ ?><select id="lcn_opening_hours_closes_at_am_pm_monday" name="lcn_opening_hours_closes_at_am_pm_monday"><option value="" <?php if( $monday[5] == "" ){ ?> selected="selected" <?php } ?>></option><option value="AM" <?php if( $monday[5] == "AM" ){ ?> selected="selected" <?php } ?>>AM</option><option value="PM" <?php if( $monday[5] == "PM" ){ ?> selected="selected" <?php } ?>>PM</option></select><? } ?></td>
									</tr>
									<tr>
										<td><label>Tuesday</label></td>
										<td><select id="lcn_opening_hours_opens_at_hour_tuesday" name="lcn_opening_hours_opens_at_hour_tuesday"><?php if($openinghoursformat == "12"){ ?><option value="" <?php if( $tuesday[0] == "" ){ ?> selected="selected" <?php } ?>></option><option value="01" <?php if( $tuesday[0] == "01" ){ ?> selected="selected" <?php } ?>>01</option><option value="02" <?php if( $tuesday[0] == "02" ){ ?> selected="selected" <?php } ?>>02</option><option value="03" <?php if( $tuesday[0] == "03" ){ ?> selected="selected" <?php } ?>>03</option><option value="04" <?php if( $tuesday[0] == "04" ){ ?> selected="selected" <?php } ?>>04</option><option value="05" <?php if( $tuesday[0] == "05" ){ ?> selected="selected" <?php } ?>>05</option><option value="06" <?php if( $tuesday[0] == "06" ){ ?> selected="selected" <?php } ?>>06</option><option value="07" <?php if( $tuesday[0] == "07" ){ ?> selected="selected" <?php } ?>>07</option><option value="08" <?php if( $tuesday[0] == "08" ){ ?> selected="selected" <?php } ?>>08</option><option value="09" <?php if( $tuesday[0] == "09" ){ ?> selected="selected" <?php } ?>>09</option><option value="10" <?php if( $tuesday[0] == "10" ){ ?> selected="selected" <?php } ?>>10</option><option value="11" <?php if( $tuesday[0] == "11" ){ ?> selected="selected" <?php } ?>>11</option><option value="12" <?php if( $tuesday[0] == "12" ){ ?> selected="selected" <?php } ?>>12</option><? }else { ?><option value="" <?php if( $tuesday[0] == "" ){ ?> selected="selected" <?php } ?>></option><option value="00" <?php if( $tuesday[0] == "00" ){ ?> selected="selected" <?php } ?>>00</option><option value="01" <?php if( $tuesday[0] == "01" ){ ?> selected="selected" <?php } ?>>01</option><option value="02" <?php if( $tuesday[0] == "02" ){ ?> selected="selected" <?php } ?>>02</option><option value="03" <?php if( $tuesday[0] == "03" ){ ?> selected="selected" <?php } ?>>03</option><option value="04" <?php if( $tuesday[0] == "04" ){ ?> selected="selected" <?php } ?>>04</option><option value="05" <?php if( $tuesday[0] == "05" ){ ?> selected="selected" <?php } ?>>05</option><option value="06" <?php if( $tuesday[0] == "06" ){ ?> selected="selected" <?php } ?>>06</option><option value="07" <?php if( $tuesday[0] == "07" ){ ?> selected="selected" <?php } ?>>07</option><option value="08" <?php if( $tuesday[0] == "08" ){ ?> selected="selected" <?php } ?>>08</option><option value="09" <?php if( $tuesday[0] == "09" ){ ?> selected="selected" <?php } ?>>09</option><option value="10" <?php if( $tuesday[0] == "10" ){ ?> selected="selected" <?php } ?>>10</option><option value="11" <?php if( $tuesday[0] == "11" ){ ?> selected="selected" <?php } ?>>11</option><option value="12" <?php if( $tuesday[0] == "12" ){ ?> selected="selected" <?php } ?>>12</option><option value="13" <?php if( $tuesday[0] == "13" ){ ?> selected="selected" <?php } ?>>13</option><option value="14" <?php if( $tuesday[0] == "14" ){ ?> selected="selected" <?php } ?>>14</option><option value="15" <?php if( $tuesday[0] == "15" ){ ?> selected="selected" <?php } ?>>15</option><option value="16" <?php if( $tuesday[0] == "16" ){ ?> selected="selected" <?php } ?>>16</option><option value="17" <?php if( $tuesday[0] == "17" ){ ?> selected="selected" <?php } ?>>17</option><option value="18" <?php if( $tuesday[0] == "18" ){ ?> selected="selected" <?php } ?>>18</option><option value="19" <?php if( $tuesday[0] == "19" ){ ?> selected="selected" <?php } ?>>19</option><option value="20" <?php if( $tuesday[0] == "20" ){ ?> selected="selected" <?php } ?>>20</option><option value="21" <?php if( $tuesday[0] == "21" ){ ?> selected="selected" <?php } ?>>21</option><option value="22" <?php if( $tuesday[0] == "22" ){ ?> selected="selected" <?php } ?>>22</option><option value="23" <?php if( $tuesday[0] == "23" ){ ?> selected="selected" <?php } ?>>23</option><?php } ?></select><select id="lcn_opening_hours_opens_at_minutes_tuesday" name="lcn_opening_hours_opens_at_minutes_tuesday"><option value="" <?php if( $tuesday[1] == "" ){ ?> selected="selected" <?php } ?>></option><option value="00" <?php if( $tuesday[1] == "00" ){ ?> selected="selected" <?php } ?>>00</option><option value="15" <?php if( $tuesday[1] == "15" ){ ?> selected="selected" <?php } ?>>15</option><option value="30" <?php if( $tuesday[1] == "30" ){ ?> selected="selected" <?php } ?>>30</option><option value="45" <?php if( $tuesday[1] == "45" ){ ?> selected="selected" <?php } ?>>45</option></select><?php if($openinghoursformat == "12"){ ?><select id="lcn_opening_hours_opens_at_am_pm_tuesday" name="lcn_opening_hours_opens_at_am_pm_tuesday"><option value="" <?php if( $tuesday[2] == "" ){ ?> selected="selected" <?php } ?>></option><option value="AM" <?php if( $tuesday[2] == "AM" ){ ?> selected="selected" <?php } ?>>AM</option><option value="PM" <?php if( $tuesday[2] == "PM" ){ ?> selected="selected" <?php } ?>>PM</option></select><? } ?></td>
										<td><select id="lcn_opening_hours_closes_at_hour_tuesday" name="lcn_opening_closes_opens_at_hour_tuesday"><?php if($openinghoursformat == "12"){ ?><option value="" <?php if( $tuesday[3] == "" ){ ?> selected="selected" <?php } ?>></option><option value="01" <?php if( $tuesday[3] == "01" ){ ?> selected="selected" <?php } ?>>01</option><option value="02" <?php if( $tuesday[3] == "02" ){ ?> selected="selected" <?php } ?>>02</option><option value="03" <?php if( $tuesday[3] == "03" ){ ?> selected="selected" <?php } ?>>03</option><option value="04" <?php if( $tuesday[3] == "04" ){ ?> selected="selected" <?php } ?>>04</option><option value="05" <?php if( $tuesday[3] == "05" ){ ?> selected="selected" <?php } ?>>05</option><option value="06" <?php if( $tuesday[3] == "06" ){ ?> selected="selected" <?php } ?>>06</option><option value="07" <?php if( $tuesday[3] == "07" ){ ?> selected="selected" <?php } ?>>07</option><option value="08" <?php if( $tuesday[3] == "08" ){ ?> selected="selected" <?php } ?>>08</option><option value="09" <?php if( $tuesday[3] == "09" ){ ?> selected="selected" <?php } ?>>09</option><option value="10" <?php if( $tuesday[3] == "10" ){ ?> selected="selected" <?php } ?>>10</option><option value="11" <?php if( $tuesday[3] == "11" ){ ?> selected="selected" <?php } ?>>11</option><option value="12" <?php if( $tuesday[3] == "12" ){ ?> selected="selected" <?php } ?>>12</option><? }else { ?><option value="" <?php if( $tuesday[3] == "" ){ ?> selected="selected" <?php } ?>></option><option value="00" <?php if( $tuesday[3] == "00" ){ ?> selected="selected" <?php } ?>>00</option><option value="01" <?php if( $tuesday[3] == "01" ){ ?> selected="selected" <?php } ?>>01</option><option value="02" <?php if( $tuesday[3] == "02" ){ ?> selected="selected" <?php } ?>>02</option><option value="03" <?php if( $tuesday[3] == "03" ){ ?> selected="selected" <?php } ?>>03</option><option value="04" <?php if( $tuesday[3] == "04" ){ ?> selected="selected" <?php } ?>>04</option><option value="05" <?php if( $tuesday[3] == "05" ){ ?> selected="selected" <?php } ?>>05</option><option value="06" <?php if( $tuesday[3] == "06" ){ ?> selected="selected" <?php } ?>>06</option><option value="07" <?php if( $tuesday[3] == "07" ){ ?> selected="selected" <?php } ?>>07</option><option value="08" <?php if( $tuesday[3] == "08" ){ ?> selected="selected" <?php } ?>>08</option><option value="09" <?php if( $tuesday[3] == "09" ){ ?> selected="selected" <?php } ?>>09</option><option value="10" <?php if( $tuesday[3] == "10" ){ ?> selected="selected" <?php } ?>>10</option><option value="11" <?php if( $tuesday[3] == "11" ){ ?> selected="selected" <?php } ?>>11</option><option value="12" <?php if( $tuesday[3] == "12" ){ ?> selected="selected" <?php } ?>>12</option><option value="13" <?php if( $tuesday[3] == "13" ){ ?> selected="selected" <?php } ?>>13</option><option value="14" <?php if( $tuesday[3] == "14" ){ ?> selected="selected" <?php } ?>>14</option><option value="15" <?php if( $tuesday[3] == "15" ){ ?> selected="selected" <?php } ?>>15</option><option value="16" <?php if( $tuesday[3] == "16" ){ ?> selected="selected" <?php } ?>>16</option><option value="17" <?php if( $tuesday[3] == "17" ){ ?> selected="selected" <?php } ?>>17</option><option value="18" <?php if( $tuesday[3] == "18" ){ ?> selected="selected" <?php } ?>>18</option><option value="19" <?php if( $tuesday[3] == "19" ){ ?> selected="selected" <?php } ?>>19</option><option value="20" <?php if( $tuesday[3] == "20" ){ ?> selected="selected" <?php } ?>>20</option><option value="21" <?php if( $tuesday[3] == "21" ){ ?> selected="selected" <?php } ?>>21</option><option value="22" <?php if( $tuesday[3] == "22" ){ ?> selected="selected" <?php } ?>>22</option><option value="23" <?php if( $tuesday[3] == "23" ){ ?> selected="selected" <?php } ?>>23</option><?php } ?></select><select id="lcn_opening_hours_closes_at_minutes_tuesday" name="lcn_opening_hours_closes_at_minutes_tuesday"><option value="" <?php if( $tuesday[4] == "" ){ ?> selected="selected" <?php } ?>></option><option value="00" <?php if( $tuesday[4] == "00" ){ ?> selected="selected" <?php } ?>>00</option><option value="15" <?php if( $tuesday[4] == "15" ){ ?> selected="selected" <?php } ?>>15</option><option value="30" <?php if( $tuesday[4] == "30" ){ ?> selected="selected" <?php } ?>>30</option><option value="45" <?php if( $tuesday[4] == "45" ){ ?> selected="selected" <?php } ?>>45</option></select><?php if($openinghoursformat == "12"){ ?><select id="lcn_opening_hours_closes_at_am_pm_tuesday" name="lcn_opening_hours_closes_at_am_pm_tuesday"><option value="" <?php if( $tuesday[5] == "" ){ ?> selected="selected" <?php } ?>></option><option value="AM" <?php if( $tuesday[5] == "AM" ){ ?> selected="selected" <?php } ?>>AM</option><option value="PM" <?php if( $tuesday[5] == "PM" ){ ?> selected="selected" <?php } ?>>PM</option></select><? } ?></td>
									</tr>
									<tr>
										<td><label>Wednesday</label></td>
										<td><select id="lcn_opening_hours_opens_at_hour_wednesday" name="lcn_opening_hours_opens_at_hour_wednesday"><?php if($openinghoursformat == "12"){ ?><option value="" <?php if( $wednesday[0] == "" ){ ?> selected="selected" <?php } ?>></option><option value="01" <?php if( $wednesday[0] == "01" ){ ?> selected="selected" <?php } ?>>01</option><option value="02" <?php if( $wednesday[0] == "02" ){ ?> selected="selected" <?php } ?>>02</option><option value="03" <?php if( $wednesday[0] == "03" ){ ?> selected="selected" <?php } ?>>03</option><option value="04" <?php if( $wednesday[0] == "04" ){ ?> selected="selected" <?php } ?>>04</option><option value="05" <?php if( $wednesday[0] == "05" ){ ?> selected="selected" <?php } ?>>05</option><option value="06" <?php if( $wednesday[0] == "06" ){ ?> selected="selected" <?php } ?>>06</option><option value="07" <?php if( $wednesday[0] == "07" ){ ?> selected="selected" <?php } ?>>07</option><option value="08" <?php if( $wednesday[0] == "08" ){ ?> selected="selected" <?php } ?>>08</option><option value="09" <?php if( $wednesday[0] == "09" ){ ?> selected="selected" <?php } ?>>09</option><option value="10" <?php if( $wednesday[0] == "10" ){ ?> selected="selected" <?php } ?>>10</option><option value="11" <?php if( $wednesday[0] == "11" ){ ?> selected="selected" <?php } ?>>11</option><option value="12" <?php if( $wednesday[0] == "12" ){ ?> selected="selected" <?php } ?>>12</option><? }else { ?><option value="" <?php if( $wednesday[0] == "" ){ ?> selected="selected" <?php } ?>></option><option value="00" <?php if( $wednesday[0] == "00" ){ ?> selected="selected" <?php } ?>>00</option><option value="01" <?php if( $wednesday[0] == "01" ){ ?> selected="selected" <?php } ?>>01</option><option value="02" <?php if( $wednesday[0] == "02" ){ ?> selected="selected" <?php } ?>>02</option><option value="03" <?php if( $wednesday[0] == "03" ){ ?> selected="selected" <?php } ?>>03</option><option value="04" <?php if( $wednesday[0] == "04" ){ ?> selected="selected" <?php } ?>>04</option><option value="05" <?php if( $wednesday[0] == "05" ){ ?> selected="selected" <?php } ?>>05</option><option value="06" <?php if( $wednesday[0] == "06" ){ ?> selected="selected" <?php } ?>>06</option><option value="07" <?php if( $wednesday[0] == "07" ){ ?> selected="selected" <?php } ?>>07</option><option value="08" <?php if( $wednesday[0] == "08" ){ ?> selected="selected" <?php } ?>>08</option><option value="09" <?php if( $wednesday[0] == "09" ){ ?> selected="selected" <?php } ?>>09</option><option value="10" <?php if( $wednesday[0] == "10" ){ ?> selected="selected" <?php } ?>>10</option><option value="11" <?php if( $wednesday[0] == "11" ){ ?> selected="selected" <?php } ?>>11</option><option value="12" <?php if( $wednesday[0] == "12" ){ ?> selected="selected" <?php } ?>>12</option><option value="13" <?php if( $wednesday[0] == "13" ){ ?> selected="selected" <?php } ?>>13</option><option value="14" <?php if( $wednesday[0] == "14" ){ ?> selected="selected" <?php } ?>>14</option><option value="15" <?php if( $wednesday[0] == "15" ){ ?> selected="selected" <?php } ?>>15</option><option value="16" <?php if( $wednesday[0] == "16" ){ ?> selected="selected" <?php } ?>>16</option><option value="17" <?php if( $wednesday[0] == "17" ){ ?> selected="selected" <?php } ?>>17</option><option value="18" <?php if( $wednesday[0] == "18" ){ ?> selected="selected" <?php } ?>>18</option><option value="19" <?php if( $wednesday[0] == "19" ){ ?> selected="selected" <?php } ?>>19</option><option value="20" <?php if( $wednesday[0] == "20" ){ ?> selected="selected" <?php } ?>>20</option><option value="21" <?php if( $wednesday[0] == "21" ){ ?> selected="selected" <?php } ?>>21</option><option value="22" <?php if( $wednesday[0] == "22" ){ ?> selected="selected" <?php } ?>>22</option><option value="23" <?php if( $wednesday[0] == "23" ){ ?> selected="selected" <?php } ?>>23</option><?php } ?></select><select id="lcn_opening_hours_opens_at_minutes_wednesday" name="lcn_opening_hours_opens_at_minutes_wednesday"><option value="" <?php if( $wednesday[1] == "" ){ ?> selected="selected" <?php } ?>></option><option value="00" <?php if( $wednesday[1] == "00" ){ ?> selected="selected" <?php } ?>>00</option><option value="15" <?php if( $wednesday[1] == "15" ){ ?> selected="selected" <?php } ?>>15</option><option value="30" <?php if( $wednesday[1] == "30" ){ ?> selected="selected" <?php } ?>>30</option><option value="45" <?php if( $wednesday[1] == "45" ){ ?> selected="selected" <?php } ?>>45</option></select><?php if($openinghoursformat == "12"){ ?><select id="lcn_opening_hours_opens_at_am_pm_wednesday" name="lcn_opening_hours_opens_at_am_pm_wednesday"><option value="" <?php if( $wednesday[2] == "" ){ ?> selected="selected" <?php } ?>></option><option value="AM" <?php if( $wednesday[2] == "AM" ){ ?> selected="selected" <?php } ?>>AM</option><option value="PM" <?php if( $wednesday[2] == "PM" ){ ?> selected="selected" <?php } ?>>PM</option></select><? } ?></td>
										<td><select id="lcn_opening_hours_closes_at_hour_wednesday" name="lcn_opening_closes_opens_at_hour_wednesday"><?php if($openinghoursformat == "12"){ ?><option value="" <?php if( $wednesday[3] == "" ){ ?> selected="selected" <?php } ?>></option><option value="01" <?php if( $wednesday[3] == "01" ){ ?> selected="selected" <?php } ?>>01</option><option value="02" <?php if( $wednesday[3] == "02" ){ ?> selected="selected" <?php } ?>>02</option><option value="03" <?php if( $wednesday[3] == "03" ){ ?> selected="selected" <?php } ?>>03</option><option value="04" <?php if( $wednesday[3] == "04" ){ ?> selected="selected" <?php } ?>>04</option><option value="05" <?php if( $wednesday[3] == "05" ){ ?> selected="selected" <?php } ?>>05</option><option value="06" <?php if( $wednesday[3] == "06" ){ ?> selected="selected" <?php } ?>>06</option><option value="07" <?php if( $wednesday[3] == "07" ){ ?> selected="selected" <?php } ?>>07</option><option value="08" <?php if( $wednesday[3] == "08" ){ ?> selected="selected" <?php } ?>>08</option><option value="09" <?php if( $wednesday[3] == "09" ){ ?> selected="selected" <?php } ?>>09</option><option value="10" <?php if( $wednesday[3] == "10" ){ ?> selected="selected" <?php } ?>>10</option><option value="11" <?php if( $wednesday[3] == "11" ){ ?> selected="selected" <?php } ?>>11</option><option value="12" <?php if( $wednesday[3] == "12" ){ ?> selected="selected" <?php } ?>>12</option><? }else { ?><option value="" <?php if( $wednesday[3] == "" ){ ?> selected="selected" <?php } ?>></option><option value="00" <?php if( $wednesday[3] == "00" ){ ?> selected="selected" <?php } ?>>00</option><option value="01" <?php if( $wednesday[3] == "01" ){ ?> selected="selected" <?php } ?>>01</option><option value="02" <?php if( $wednesday[3] == "02" ){ ?> selected="selected" <?php } ?>>02</option><option value="03" <?php if( $wednesday[3] == "03" ){ ?> selected="selected" <?php } ?>>03</option><option value="04" <?php if( $wednesday[3] == "04" ){ ?> selected="selected" <?php } ?>>04</option><option value="05" <?php if( $wednesday[3] == "05" ){ ?> selected="selected" <?php } ?>>05</option><option value="06" <?php if( $wednesday[3] == "06" ){ ?> selected="selected" <?php } ?>>06</option><option value="07" <?php if( $wednesday[3] == "07" ){ ?> selected="selected" <?php } ?>>07</option><option value="08" <?php if( $wednesday[3] == "08" ){ ?> selected="selected" <?php } ?>>08</option><option value="09" <?php if( $wednesday[3] == "09" ){ ?> selected="selected" <?php } ?>>09</option><option value="10" <?php if( $wednesday[3] == "10" ){ ?> selected="selected" <?php } ?>>10</option><option value="11" <?php if( $wednesday[3] == "11" ){ ?> selected="selected" <?php } ?>>11</option><option value="12" <?php if( $wednesday[3] == "12" ){ ?> selected="selected" <?php } ?>>12</option><option value="13" <?php if( $wednesday[3] == "13" ){ ?> selected="selected" <?php } ?>>13</option><option value="14" <?php if( $wednesday[3] == "14" ){ ?> selected="selected" <?php } ?>>14</option><option value="15" <?php if( $wednesday[3] == "15" ){ ?> selected="selected" <?php } ?>>15</option><option value="16" <?php if( $wednesday[3] == "16" ){ ?> selected="selected" <?php } ?>>16</option><option value="17" <?php if( $wednesday[3] == "17" ){ ?> selected="selected" <?php } ?>>17</option><option value="18" <?php if( $wednesday[3] == "18" ){ ?> selected="selected" <?php } ?>>18</option><option value="19" <?php if( $wednesday[3] == "19" ){ ?> selected="selected" <?php } ?>>19</option><option value="20" <?php if( $wednesday[3] == "20" ){ ?> selected="selected" <?php } ?>>20</option><option value="21" <?php if( $wednesday[3] == "21" ){ ?> selected="selected" <?php } ?>>21</option><option value="22" <?php if( $wednesday[3] == "22" ){ ?> selected="selected" <?php } ?>>22</option><option value="23" <?php if( $wednesday[3] == "23" ){ ?> selected="selected" <?php } ?>>23</option><?php } ?></select><select id="lcn_opening_hours_closes_at_minutes_wednesday" name="lcn_opening_hours_closes_at_minutes_wednesday"><option value="" <?php if( $wednesday[4] == "" ){ ?> selected="selected" <?php } ?>></option><option value="00" <?php if( $wednesday[4] == "00" ){ ?> selected="selected" <?php } ?>>00</option><option value="15" <?php if( $wednesday[4] == "15" ){ ?> selected="selected" <?php } ?>>15</option><option value="30" <?php if( $wednesday[4] == "30" ){ ?> selected="selected" <?php } ?>>30</option><option value="45" <?php if( $wednesday[4] == "45" ){ ?> selected="selected" <?php } ?>>45</option></select><?php if($openinghoursformat == "12"){ ?><select id="lcn_opening_hours_closes_at_am_pm_wednesday" name="lcn_opening_hours_closes_at_am_pm_wednesday"><option value="" <?php if( $wednesday[5] == "" ){ ?> selected="selected" <?php } ?>></option><option value="AM" <?php if( $wednesday[5] == "AM" ){ ?> selected="selected" <?php } ?>>AM</option><option value="PM" <?php if( $wednesday[5] == "PM" ){ ?> selected="selected" <?php } ?>>PM</option></select><? } ?></td>
									</tr>
									<tr>
										<td><label>Thursday</label></td>
										<td><select id="lcn_opening_hours_opens_at_hour_thursday" name="lcn_opening_hours_opens_at_hour_thursday"><?php if($openinghoursformat == "12"){ ?><option value="" <?php if( $thursday[0] == "" ){ ?> selected="selected" <?php } ?>></option><option value="01" <?php if( $thursday[0] == "01" ){ ?> selected="selected" <?php } ?>>01</option><option value="02" <?php if( $thursday[0] == "02" ){ ?> selected="selected" <?php } ?>>02</option><option value="03" <?php if( $thursday[0] == "03" ){ ?> selected="selected" <?php } ?>>03</option><option value="04" <?php if( $thursday[0] == "04" ){ ?> selected="selected" <?php } ?>>04</option><option value="05" <?php if( $thursday[0] == "05" ){ ?> selected="selected" <?php } ?>>05</option><option value="06" <?php if( $thursday[0] == "06" ){ ?> selected="selected" <?php } ?>>06</option><option value="07" <?php if( $thursday[0] == "07" ){ ?> selected="selected" <?php } ?>>07</option><option value="08" <?php if( $thursday[0] == "08" ){ ?> selected="selected" <?php } ?>>08</option><option value="09" <?php if( $thursday[0] == "09" ){ ?> selected="selected" <?php } ?>>09</option><option value="10" <?php if( $thursday[0] == "10" ){ ?> selected="selected" <?php } ?>>10</option><option value="11" <?php if( $thursday[0] == "11" ){ ?> selected="selected" <?php } ?>>11</option><option value="12" <?php if( $thursday[0] == "12" ){ ?> selected="selected" <?php } ?>>12</option><? }else { ?><option value="" <?php if( $thursday[0] == "" ){ ?> selected="selected" <?php } ?>></option><option value="00" <?php if( $thursday[0] == "00" ){ ?> selected="selected" <?php } ?>>00</option><option value="01" <?php if( $thursday[0] == "01" ){ ?> selected="selected" <?php } ?>>01</option><option value="02" <?php if( $thursday[0] == "02" ){ ?> selected="selected" <?php } ?>>02</option><option value="03" <?php if( $thursday[0] == "03" ){ ?> selected="selected" <?php } ?>>03</option><option value="04" <?php if( $thursday[0] == "04" ){ ?> selected="selected" <?php } ?>>04</option><option value="05" <?php if( $thursday[0] == "05" ){ ?> selected="selected" <?php } ?>>05</option><option value="06" <?php if( $thursday[0] == "06" ){ ?> selected="selected" <?php } ?>>06</option><option value="07" <?php if( $thursday[0] == "07" ){ ?> selected="selected" <?php } ?>>07</option><option value="08" <?php if( $thursday[0] == "08" ){ ?> selected="selected" <?php } ?>>08</option><option value="09" <?php if( $thursday[0] == "09" ){ ?> selected="selected" <?php } ?>>09</option><option value="10" <?php if( $thursday[0] == "10" ){ ?> selected="selected" <?php } ?>>10</option><option value="11" <?php if( $thursday[0] == "11" ){ ?> selected="selected" <?php } ?>>11</option><option value="12" <?php if( $thursday[0] == "12" ){ ?> selected="selected" <?php } ?>>12</option><option value="13" <?php if( $thursday[0] == "13" ){ ?> selected="selected" <?php } ?>>13</option><option value="14" <?php if( $thursday[0] == "14" ){ ?> selected="selected" <?php } ?>>14</option><option value="15" <?php if( $thursday[0] == "15" ){ ?> selected="selected" <?php } ?>>15</option><option value="16" <?php if( $thursday[0] == "16" ){ ?> selected="selected" <?php } ?>>16</option><option value="17" <?php if( $thursday[0] == "17" ){ ?> selected="selected" <?php } ?>>17</option><option value="18" <?php if( $thursday[0] == "18" ){ ?> selected="selected" <?php } ?>>18</option><option value="19" <?php if( $thursday[0] == "19" ){ ?> selected="selected" <?php } ?>>19</option><option value="20" <?php if( $thursday[0] == "20" ){ ?> selected="selected" <?php } ?>>20</option><option value="21" <?php if( $thursday[0] == "21" ){ ?> selected="selected" <?php } ?>>21</option><option value="22" <?php if( $thursday[0] == "22" ){ ?> selected="selected" <?php } ?>>22</option><option value="23" <?php if( $thursday[0] == "23" ){ ?> selected="selected" <?php } ?>>23</option><?php } ?></select><select id="lcn_opening_hours_opens_at_minutes_thursday" name="lcn_opening_hours_opens_at_minutes_thursday"><option value="" <?php if( $thursday[1] == "" ){ ?> selected="selected" <?php } ?>></option><option value="00" <?php if( $thursday[1] == "00" ){ ?> selected="selected" <?php } ?>>00</option><option value="15" <?php if( $thursday[1] == "15" ){ ?> selected="selected" <?php } ?>>15</option><option value="30" <?php if( $thursday[1] == "30" ){ ?> selected="selected" <?php } ?>>30</option><option value="45" <?php if( $thursday[1] == "45" ){ ?> selected="selected" <?php } ?>>45</option></select><?php if($openinghoursformat == "12"){ ?><select id="lcn_opening_hours_opens_at_am_pm_thursday" name="lcn_opening_hours_opens_at_am_pm_thursday"><option value="" <?php if( $thursday[2] == "" ){ ?> selected="selected" <?php } ?>></option><option value="AM" <?php if( $thursday[2] == "AM" ){ ?> selected="selected" <?php } ?>>AM</option><option value="PM" <?php if( $thursday[2] == "PM" ){ ?> selected="selected" <?php } ?>>PM</option></select><? } ?></td>
										<td><select id="lcn_opening_hours_closes_at_hour_thursday" name="lcn_opening_closes_opens_at_hour_thursday"><?php if($openinghoursformat == "12"){ ?><option value="" <?php if( $thursday[3] == "" ){ ?> selected="selected" <?php } ?>></option><option value="01" <?php if( $thursday[3] == "01" ){ ?> selected="selected" <?php } ?>>01</option><option value="02" <?php if( $thursday[3] == "02" ){ ?> selected="selected" <?php } ?>>02</option><option value="03" <?php if( $thursday[3] == "03" ){ ?> selected="selected" <?php } ?>>03</option><option value="04" <?php if( $thursday[3] == "04" ){ ?> selected="selected" <?php } ?>>04</option><option value="05" <?php if( $thursday[3] == "05" ){ ?> selected="selected" <?php } ?>>05</option><option value="06" <?php if( $thursday[3] == "06" ){ ?> selected="selected" <?php } ?>>06</option><option value="07" <?php if( $thursday[3] == "07" ){ ?> selected="selected" <?php } ?>>07</option><option value="08" <?php if( $thursday[3] == "08" ){ ?> selected="selected" <?php } ?>>08</option><option value="09" <?php if( $thursday[3] == "09" ){ ?> selected="selected" <?php } ?>>09</option><option value="10" <?php if( $thursday[3] == "10" ){ ?> selected="selected" <?php } ?>>10</option><option value="11" <?php if( $thursday[3] == "11" ){ ?> selected="selected" <?php } ?>>11</option><option value="12" <?php if( $thursday[3] == "12" ){ ?> selected="selected" <?php } ?>>12</option><? }else { ?><option value="" <?php if( $thursday[3] == "" ){ ?> selected="selected" <?php } ?>></option><option value="00" <?php if( $thursday[3] == "00" ){ ?> selected="selected" <?php } ?>>00</option><option value="01" <?php if( $thursday[3] == "01" ){ ?> selected="selected" <?php } ?>>01</option><option value="02" <?php if( $thursday[3] == "02" ){ ?> selected="selected" <?php } ?>>02</option><option value="03" <?php if( $thursday[3] == "03" ){ ?> selected="selected" <?php } ?>>03</option><option value="04" <?php if( $thursday[3] == "04" ){ ?> selected="selected" <?php } ?>>04</option><option value="05" <?php if( $thursday[3] == "05" ){ ?> selected="selected" <?php } ?>>05</option><option value="06" <?php if( $thursday[3] == "06" ){ ?> selected="selected" <?php } ?>>06</option><option value="07" <?php if( $thursday[3] == "07" ){ ?> selected="selected" <?php } ?>>07</option><option value="08" <?php if( $thursday[3] == "08" ){ ?> selected="selected" <?php } ?>>08</option><option value="09" <?php if( $thursday[3] == "09" ){ ?> selected="selected" <?php } ?>>09</option><option value="10" <?php if( $thursday[3] == "10" ){ ?> selected="selected" <?php } ?>>10</option><option value="11" <?php if( $thursday[3] == "11" ){ ?> selected="selected" <?php } ?>>11</option><option value="12" <?php if( $thursday[3] == "12" ){ ?> selected="selected" <?php } ?>>12</option><option value="13" <?php if( $thursday[3] == "13" ){ ?> selected="selected" <?php } ?>>13</option><option value="14" <?php if( $thursday[3] == "14" ){ ?> selected="selected" <?php } ?>>14</option><option value="15" <?php if( $thursday[3] == "15" ){ ?> selected="selected" <?php } ?>>15</option><option value="16" <?php if( $thursday[3] == "16" ){ ?> selected="selected" <?php } ?>>16</option><option value="17" <?php if( $thursday[3] == "17" ){ ?> selected="selected" <?php } ?>>17</option><option value="18" <?php if( $thursday[3] == "18" ){ ?> selected="selected" <?php } ?>>18</option><option value="19" <?php if( $thursday[3] == "19" ){ ?> selected="selected" <?php } ?>>19</option><option value="20" <?php if( $thursday[3] == "20" ){ ?> selected="selected" <?php } ?>>20</option><option value="21" <?php if( $thursday[3] == "21" ){ ?> selected="selected" <?php } ?>>21</option><option value="22" <?php if( $thursday[3] == "22" ){ ?> selected="selected" <?php } ?>>22</option><option value="23" <?php if( $thursday[3] == "23" ){ ?> selected="selected" <?php } ?>>23</option><?php } ?></select><select id="lcn_opening_hours_closes_at_minutes_thursday" name="lcn_opening_hours_closes_at_minutes_thursday"><option value="" <?php if( $thursday[4] == "" ){ ?> selected="selected" <?php } ?>></option><option value="00" <?php if( $thursday[4] == "00" ){ ?> selected="selected" <?php } ?>>00</option><option value="15" <?php if( $thursday[4] == "15" ){ ?> selected="selected" <?php } ?>>15</option><option value="30" <?php if( $thursday[4] == "30" ){ ?> selected="selected" <?php } ?>>30</option><option value="45" <?php if( $thursday[4] == "45" ){ ?> selected="selected" <?php } ?>>45</option></select><?php if($openinghoursformat == "12"){ ?><select id="lcn_opening_hours_closes_at_am_pm_thursday" name="lcn_opening_hours_closes_at_am_pm_thursday"><option value="" <?php if( $thursday[5] == "" ){ ?> selected="selected" <?php } ?>></option><option value="AM" <?php if( $thursday[5] == "AM" ){ ?> selected="selected" <?php } ?>>AM</option><option value="PM" <?php if( $thursday[5] == "PM" ){ ?> selected="selected" <?php } ?>>PM</option></select><? } ?></td>
									</tr>
									<tr>
										<td><label>Friday</label></td>
										<td><select id="lcn_opening_hours_opens_at_hour_friday" name="lcn_opening_hours_opens_at_hour_friday"><?php if($openinghoursformat == "12"){ ?><option value="" <?php if( $friday[0] == "" ){ ?> selected="selected" <?php } ?>></option><option value="01" <?php if( $friday[0] == "01" ){ ?> selected="selected" <?php } ?>>01</option><option value="02" <?php if( $friday[0] == "02" ){ ?> selected="selected" <?php } ?>>02</option><option value="03" <?php if( $friday[0] == "03" ){ ?> selected="selected" <?php } ?>>03</option><option value="04" <?php if( $friday[0] == "04" ){ ?> selected="selected" <?php } ?>>04</option><option value="05" <?php if( $friday[0] == "05" ){ ?> selected="selected" <?php } ?>>05</option><option value="06" <?php if( $friday[0] == "06" ){ ?> selected="selected" <?php } ?>>06</option><option value="07" <?php if( $friday[0] == "07" ){ ?> selected="selected" <?php } ?>>07</option><option value="08" <?php if( $friday[0] == "08" ){ ?> selected="selected" <?php } ?>>08</option><option value="09" <?php if( $friday[0] == "09" ){ ?> selected="selected" <?php } ?>>09</option><option value="10" <?php if( $friday[0] == "10" ){ ?> selected="selected" <?php } ?>>10</option><option value="11" <?php if( $friday[0] == "11" ){ ?> selected="selected" <?php } ?>>11</option><option value="12" <?php if( $friday[0] == "12" ){ ?> selected="selected" <?php } ?>>12</option><? }else { ?><option value="" <?php if( $friday[0] == "" ){ ?> selected="selected" <?php } ?>></option><option value="00" <?php if( $friday[0] == "00" ){ ?> selected="selected" <?php } ?>>00</option><option value="01" <?php if( $friday[0] == "01" ){ ?> selected="selected" <?php } ?>>01</option><option value="02" <?php if( $friday[0] == "02" ){ ?> selected="selected" <?php } ?>>02</option><option value="03" <?php if( $friday[0] == "03" ){ ?> selected="selected" <?php } ?>>03</option><option value="04" <?php if( $friday[0] == "04" ){ ?> selected="selected" <?php } ?>>04</option><option value="05" <?php if( $friday[0] == "05" ){ ?> selected="selected" <?php } ?>>05</option><option value="06" <?php if( $friday[0] == "06" ){ ?> selected="selected" <?php } ?>>06</option><option value="07" <?php if( $friday[0] == "07" ){ ?> selected="selected" <?php } ?>>07</option><option value="08" <?php if( $friday[0] == "08" ){ ?> selected="selected" <?php } ?>>08</option><option value="09" <?php if( $friday[0] == "09" ){ ?> selected="selected" <?php } ?>>09</option><option value="10" <?php if( $friday[0] == "10" ){ ?> selected="selected" <?php } ?>>10</option><option value="11" <?php if( $friday[0] == "11" ){ ?> selected="selected" <?php } ?>>11</option><option value="12" <?php if( $friday[0] == "12" ){ ?> selected="selected" <?php } ?>>12</option><option value="13" <?php if( $friday[0] == "13" ){ ?> selected="selected" <?php } ?>>13</option><option value="14" <?php if( $friday[0] == "14" ){ ?> selected="selected" <?php } ?>>14</option><option value="15" <?php if( $friday[0] == "15" ){ ?> selected="selected" <?php } ?>>15</option><option value="16" <?php if( $friday[0] == "16" ){ ?> selected="selected" <?php } ?>>16</option><option value="17" <?php if( $friday[0] == "17" ){ ?> selected="selected" <?php } ?>>17</option><option value="18" <?php if( $friday[0] == "18" ){ ?> selected="selected" <?php } ?>>18</option><option value="19" <?php if( $friday[0] == "19" ){ ?> selected="selected" <?php } ?>>19</option><option value="20" <?php if( $friday[0] == "20" ){ ?> selected="selected" <?php } ?>>20</option><option value="21" <?php if( $friday[0] == "21" ){ ?> selected="selected" <?php } ?>>21</option><option value="22" <?php if( $friday[0] == "22" ){ ?> selected="selected" <?php } ?>>22</option><option value="23" <?php if( $friday[0] == "23" ){ ?> selected="selected" <?php } ?>>23</option><?php } ?></select><select id="lcn_opening_hours_opens_at_minutes_friday" name="lcn_opening_hours_opens_at_minutes_friday"><option value="" <?php if( $friday[1] == "" ){ ?> selected="selected" <?php } ?>></option><option value="00" <?php if( $friday[1] == "00" ){ ?> selected="selected" <?php } ?>>00</option><option value="15" <?php if( $friday[1] == "15" ){ ?> selected="selected" <?php } ?>>15</option><option value="30" <?php if( $friday[1] == "30" ){ ?> selected="selected" <?php } ?>>30</option><option value="45" <?php if( $friday[1] == "45" ){ ?> selected="selected" <?php } ?>>45</option></select><?php if($openinghoursformat == "12"){ ?><select id="lcn_opening_hours_opens_at_am_pm_friday" name="lcn_opening_hours_opens_at_am_pm_friday"><option value="" <?php if( $friday[2] == "" ){ ?> selected="selected" <?php } ?>></option><option value="AM" <?php if( $friday[2] == "AM" ){ ?> selected="selected" <?php } ?>>AM</option><option value="PM" <?php if( $friday[2] == "PM" ){ ?> selected="selected" <?php } ?>>PM</option></select><? } ?></td>
										<td><select id="lcn_opening_hours_closes_at_hour_friday" name="lcn_opening_closes_opens_at_hour_friday"><?php if($openinghoursformat == "12"){ ?><option value="" <?php if( $friday[3] == "" ){ ?> selected="selected" <?php } ?>></option><option value="01" <?php if( $friday[3] == "01" ){ ?> selected="selected" <?php } ?>>01</option><option value="02" <?php if( $friday[3] == "02" ){ ?> selected="selected" <?php } ?>>02</option><option value="03" <?php if( $friday[3] == "03" ){ ?> selected="selected" <?php } ?>>03</option><option value="04" <?php if( $friday[3] == "04" ){ ?> selected="selected" <?php } ?>>04</option><option value="05" <?php if( $friday[3] == "05" ){ ?> selected="selected" <?php } ?>>05</option><option value="06" <?php if( $friday[3] == "06" ){ ?> selected="selected" <?php } ?>>06</option><option value="07" <?php if( $friday[3] == "07" ){ ?> selected="selected" <?php } ?>>07</option><option value="08" <?php if( $friday[3] == "08" ){ ?> selected="selected" <?php } ?>>08</option><option value="09" <?php if( $friday[3] == "09" ){ ?> selected="selected" <?php } ?>>09</option><option value="10" <?php if( $friday[3] == "10" ){ ?> selected="selected" <?php } ?>>10</option><option value="11" <?php if( $friday[3] == "11" ){ ?> selected="selected" <?php } ?>>11</option><option value="12" <?php if( $friday[3] == "12" ){ ?> selected="selected" <?php } ?>>12</option><? }else { ?><option value="" <?php if( $friday[3] == "" ){ ?> selected="selected" <?php } ?>></option><option value="00" <?php if( $friday[3] == "00" ){ ?> selected="selected" <?php } ?>>00</option><option value="01" <?php if( $friday[3] == "01" ){ ?> selected="selected" <?php } ?>>01</option><option value="02" <?php if( $friday[3] == "02" ){ ?> selected="selected" <?php } ?>>02</option><option value="03" <?php if( $friday[3] == "03" ){ ?> selected="selected" <?php } ?>>03</option><option value="04" <?php if( $friday[3] == "04" ){ ?> selected="selected" <?php } ?>>04</option><option value="05" <?php if( $friday[3] == "05" ){ ?> selected="selected" <?php } ?>>05</option><option value="06" <?php if( $friday[3] == "06" ){ ?> selected="selected" <?php } ?>>06</option><option value="07" <?php if( $friday[3] == "07" ){ ?> selected="selected" <?php } ?>>07</option><option value="08" <?php if( $friday[3] == "08" ){ ?> selected="selected" <?php } ?>>08</option><option value="09" <?php if( $friday[3] == "09" ){ ?> selected="selected" <?php } ?>>09</option><option value="10" <?php if( $friday[3] == "10" ){ ?> selected="selected" <?php } ?>>10</option><option value="11" <?php if( $friday[3] == "11" ){ ?> selected="selected" <?php } ?>>11</option><option value="12" <?php if( $friday[3] == "12" ){ ?> selected="selected" <?php } ?>>12</option><option value="13" <?php if( $friday[3] == "13" ){ ?> selected="selected" <?php } ?>>13</option><option value="14" <?php if( $friday[3] == "14" ){ ?> selected="selected" <?php } ?>>14</option><option value="15" <?php if( $friday[3] == "15" ){ ?> selected="selected" <?php } ?>>15</option><option value="16" <?php if( $friday[3] == "16" ){ ?> selected="selected" <?php } ?>>16</option><option value="17" <?php if( $friday[3] == "17" ){ ?> selected="selected" <?php } ?>>17</option><option value="18" <?php if( $friday[3] == "18" ){ ?> selected="selected" <?php } ?>>18</option><option value="19" <?php if( $friday[3] == "19" ){ ?> selected="selected" <?php } ?>>19</option><option value="20" <?php if( $friday[3] == "20" ){ ?> selected="selected" <?php } ?>>20</option><option value="21" <?php if( $friday[3] == "21" ){ ?> selected="selected" <?php } ?>>21</option><option value="22" <?php if( $friday[3] == "22" ){ ?> selected="selected" <?php } ?>>22</option><option value="23" <?php if( $friday[3] == "23" ){ ?> selected="selected" <?php } ?>>23</option><?php } ?></select><select id="lcn_opening_hours_closes_at_minutes_friday" name="lcn_opening_hours_closes_at_minutes_friday"><option value="" <?php if( $friday[4] == "" ){ ?> selected="selected" <?php } ?>></option><option value="00" <?php if( $friday[4] == "00" ){ ?> selected="selected" <?php } ?>>00</option><option value="15" <?php if( $friday[4] == "15" ){ ?> selected="selected" <?php } ?>>15</option><option value="30" <?php if( $friday[4] == "30" ){ ?> selected="selected" <?php } ?>>30</option><option value="45" <?php if( $friday[4] == "45" ){ ?> selected="selected" <?php } ?>>45</option></select><?php if($openinghoursformat == "12"){ ?><select id="lcn_opening_hours_closes_at_am_pm_friday" name="lcn_opening_hours_closes_at_am_pm_friday"><option value="" <?php if( $friday[5] == "" ){ ?> selected="selected" <?php } ?>></option><option value="AM" <?php if( $friday[5] == "AM" ){ ?> selected="selected" <?php } ?>>AM</option><option value="PM" <?php if( $friday[5] == "PM" ){ ?> selected="selected" <?php } ?>>PM</option></select><? } ?></td>
									</tr>
									<tr>
										<td><label>Saturday</label></td>
										<td><select id="lcn_opening_hours_opens_at_hour_saturday" name="lcn_opening_hours_opens_at_hour_saturday"><?php if($openinghoursformat == "12"){ ?><option value="" <?php if( $saturday[0] == "" ){ ?> selected="selected" <?php } ?>></option><option value="01" <?php if( $saturday[0] == "01" ){ ?> selected="selected" <?php } ?>>01</option><option value="02" <?php if( $saturday[0] == "02" ){ ?> selected="selected" <?php } ?>>02</option><option value="03" <?php if( $saturday[0] == "03" ){ ?> selected="selected" <?php } ?>>03</option><option value="04" <?php if( $saturday[0] == "04" ){ ?> selected="selected" <?php } ?>>04</option><option value="05" <?php if( $saturday[0] == "05" ){ ?> selected="selected" <?php } ?>>05</option><option value="06" <?php if( $saturday[0] == "06" ){ ?> selected="selected" <?php } ?>>06</option><option value="07" <?php if( $saturday[0] == "07" ){ ?> selected="selected" <?php } ?>>07</option><option value="08" <?php if( $saturday[0] == "08" ){ ?> selected="selected" <?php } ?>>08</option><option value="09" <?php if( $saturday[0] == "09" ){ ?> selected="selected" <?php } ?>>09</option><option value="10" <?php if( $saturday[0] == "10" ){ ?> selected="selected" <?php } ?>>10</option><option value="11" <?php if( $saturday[0] == "11" ){ ?> selected="selected" <?php } ?>>11</option><option value="12" <?php if( $saturday[0] == "12" ){ ?> selected="selected" <?php } ?>>12</option><? }else { ?><option value="" <?php if( $saturday[0] == "" ){ ?> selected="selected" <?php } ?>></option><option value="00" <?php if( $saturday[0] == "00" ){ ?> selected="selected" <?php } ?>>00</option><option value="01" <?php if( $saturday[0] == "01" ){ ?> selected="selected" <?php } ?>>01</option><option value="02" <?php if( $saturday[0] == "02" ){ ?> selected="selected" <?php } ?>>02</option><option value="03" <?php if( $saturday[0] == "03" ){ ?> selected="selected" <?php } ?>>03</option><option value="04" <?php if( $saturday[0] == "04" ){ ?> selected="selected" <?php } ?>>04</option><option value="05" <?php if( $saturday[0] == "05" ){ ?> selected="selected" <?php } ?>>05</option><option value="06" <?php if( $saturday[0] == "06" ){ ?> selected="selected" <?php } ?>>06</option><option value="07" <?php if( $saturday[0] == "07" ){ ?> selected="selected" <?php } ?>>07</option><option value="08" <?php if( $saturday[0] == "08" ){ ?> selected="selected" <?php } ?>>08</option><option value="09" <?php if( $saturday[0] == "09" ){ ?> selected="selected" <?php } ?>>09</option><option value="10" <?php if( $saturday[0] == "10" ){ ?> selected="selected" <?php } ?>>10</option><option value="11" <?php if( $saturday[0] == "11" ){ ?> selected="selected" <?php } ?>>11</option><option value="12" <?php if( $saturday[0] == "12" ){ ?> selected="selected" <?php } ?>>12</option><option value="13" <?php if( $saturday[0] == "13" ){ ?> selected="selected" <?php } ?>>13</option><option value="14" <?php if( $saturday[0] == "14" ){ ?> selected="selected" <?php } ?>>14</option><option value="15" <?php if( $saturday[0] == "15" ){ ?> selected="selected" <?php } ?>>15</option><option value="16" <?php if( $saturday[0] == "16" ){ ?> selected="selected" <?php } ?>>16</option><option value="17" <?php if( $saturday[0] == "17" ){ ?> selected="selected" <?php } ?>>17</option><option value="18" <?php if( $saturday[0] == "18" ){ ?> selected="selected" <?php } ?>>18</option><option value="19" <?php if( $saturday[0] == "19" ){ ?> selected="selected" <?php } ?>>19</option><option value="20" <?php if( $saturday[0] == "20" ){ ?> selected="selected" <?php } ?>>20</option><option value="21" <?php if( $saturday[0] == "21" ){ ?> selected="selected" <?php } ?>>21</option><option value="22" <?php if( $saturday[0] == "22" ){ ?> selected="selected" <?php } ?>>22</option><option value="23" <?php if( $saturday[0] == "23" ){ ?> selected="selected" <?php } ?>>23</option><?php } ?></select><select id="lcn_opening_hours_opens_at_minutes_saturday" name="lcn_opening_hours_opens_at_minutes_saturday"><option value="" <?php if( $saturday[1] == "" ){ ?> selected="selected" <?php } ?>></option><option value="00" <?php if( $saturday[1] == "00" ){ ?> selected="selected" <?php } ?>>00</option><option value="15" <?php if( $saturday[1] == "15" ){ ?> selected="selected" <?php } ?>>15</option><option value="30" <?php if( $saturday[1] == "30" ){ ?> selected="selected" <?php } ?>>30</option><option value="45" <?php if( $saturday[1] == "45" ){ ?> selected="selected" <?php } ?>>45</option></select><?php if($openinghoursformat == "12"){ ?><select id="lcn_opening_hours_opens_at_am_pm_saturday" name="lcn_opening_hours_opens_at_am_pm_saturday"><option value="" <?php if( $saturday[2] == "" ){ ?> selected="selected" <?php } ?>></option><option value="AM" <?php if( $saturday[2] == "AM" ){ ?> selected="selected" <?php } ?>>AM</option><option value="PM" <?php if( $saturday[2] == "PM" ){ ?> selected="selected" <?php } ?>>PM</option></select><? } ?></td>
										<td><select id="lcn_opening_hours_closes_at_hour_saturday" name="lcn_opening_closes_opens_at_hour_saturday"><?php if($openinghoursformat == "12"){ ?><option value="" <?php if( $saturday[3] == "" ){ ?> selected="selected" <?php } ?>></option><option value="01" <?php if( $saturday[3] == "01" ){ ?> selected="selected" <?php } ?>>01</option><option value="02" <?php if( $saturday[3] == "02" ){ ?> selected="selected" <?php } ?>>02</option><option value="03" <?php if( $saturday[3] == "03" ){ ?> selected="selected" <?php } ?>>03</option><option value="04" <?php if( $saturday[3] == "04" ){ ?> selected="selected" <?php } ?>>04</option><option value="05" <?php if( $saturday[3] == "05" ){ ?> selected="selected" <?php } ?>>05</option><option value="06" <?php if( $saturday[3] == "06" ){ ?> selected="selected" <?php } ?>>06</option><option value="07" <?php if( $saturday[3] == "07" ){ ?> selected="selected" <?php } ?>>07</option><option value="08" <?php if( $saturday[3] == "08" ){ ?> selected="selected" <?php } ?>>08</option><option value="09" <?php if( $saturday[3] == "09" ){ ?> selected="selected" <?php } ?>>09</option><option value="10" <?php if( $saturday[3] == "10" ){ ?> selected="selected" <?php } ?>>10</option><option value="11" <?php if( $saturday[3] == "11" ){ ?> selected="selected" <?php } ?>>11</option><option value="12" <?php if( $saturday[3] == "12" ){ ?> selected="selected" <?php } ?>>12</option><? }else { ?><option value="" <?php if( $saturday[3] == "" ){ ?> selected="selected" <?php } ?>></option><option value="00" <?php if( $saturday[3] == "00" ){ ?> selected="selected" <?php } ?>>00</option><option value="01" <?php if( $saturday[3] == "01" ){ ?> selected="selected" <?php } ?>>01</option><option value="02" <?php if( $saturday[3] == "02" ){ ?> selected="selected" <?php } ?>>02</option><option value="03" <?php if( $saturday[3] == "03" ){ ?> selected="selected" <?php } ?>>03</option><option value="04" <?php if( $saturday[3] == "04" ){ ?> selected="selected" <?php } ?>>04</option><option value="05" <?php if( $saturday[3] == "05" ){ ?> selected="selected" <?php } ?>>05</option><option value="06" <?php if( $saturday[3] == "06" ){ ?> selected="selected" <?php } ?>>06</option><option value="07" <?php if( $saturday[3] == "07" ){ ?> selected="selected" <?php } ?>>07</option><option value="08" <?php if( $saturday[3] == "08" ){ ?> selected="selected" <?php } ?>>08</option><option value="09" <?php if( $saturday[3] == "09" ){ ?> selected="selected" <?php } ?>>09</option><option value="10" <?php if( $saturday[3] == "10" ){ ?> selected="selected" <?php } ?>>10</option><option value="11" <?php if( $saturday[3] == "11" ){ ?> selected="selected" <?php } ?>>11</option><option value="12" <?php if( $saturday[3] == "12" ){ ?> selected="selected" <?php } ?>>12</option><option value="13" <?php if( $saturday[3] == "13" ){ ?> selected="selected" <?php } ?>>13</option><option value="14" <?php if( $saturday[3] == "14" ){ ?> selected="selected" <?php } ?>>14</option><option value="15" <?php if( $saturday[3] == "16" ){ ?> selected="selected" <?php } ?>>15</option><option value="16" <?php if( $saturday[3] == "16" ){ ?> selected="selected" <?php } ?>>16</option><option value="17" <?php if( $saturday[3] == "17" ){ ?> selected="selected" <?php } ?>>17</option><option value="18" <?php if( $saturday[3] == "18" ){ ?> selected="selected" <?php } ?>>18</option><option value="19" <?php if( $saturday[3] == "19" ){ ?> selected="selected" <?php } ?>>19</option><option value="20" <?php if( $saturday[3] == "20" ){ ?> selected="selected" <?php } ?>>20</option><option value="21" <?php if( $saturday[3] == "21" ){ ?> selected="selected" <?php } ?>>21</option><option value="22" <?php if( $saturday[3] == "22" ){ ?> selected="selected" <?php } ?>>22</option><option value="23" <?php if( $saturday[3] == "23" ){ ?> selected="selected" <?php } ?>>23</option><?php } ?></select><select id="lcn_opening_hours_closes_at_minutes_saturday" name="lcn_opening_hours_closes_at_minutes_saturday"><option value="" <?php if( $saturday[4] == "" ){ ?> selected="selected" <?php } ?>></option><option value="00" <?php if( $saturday[4] == "00" ){ ?> selected="selected" <?php } ?>>00</option><option value="15" <?php if( $saturday[4] == "15" ){ ?> selected="selected" <?php } ?>>15</option><option value="30" <?php if( $saturday[4] == "30" ){ ?> selected="selected" <?php } ?>>30</option><option value="45" <?php if( $saturday[4] == "45" ){ ?> selected="selected" <?php } ?>>45</option></select><?php if($openinghoursformat == "12"){ ?><select id="lcn_opening_hours_closes_at_am_pm_saturday" name="lcn_opening_hours_closes_at_am_pm_saturday"><option value="" <?php if( $saturday[5] == "" ){ ?> selected="selected" <?php } ?>></option><option value="AM" <?php if( $saturday[5] == "AM" ){ ?> selected="selected" <?php } ?>>AM</option><option value="PM" <?php if( $saturday[5] == "PM" ){ ?> selected="selected" <?php } ?>>PM</option></select><? } ?></td>
									</tr>
									<tr>
										<td><label>Sunday</label></td>
										<td><select id="lcn_opening_hours_opens_at_hour_sunday" name="lcn_opening_hours_opens_at_hour_sunday"><?php if($openinghoursformat == "12"){ ?><option value="" <?php if( $sunday[0] == "" ){ ?> selected="selected" <?php } ?>></option><option value="01" <?php if( $sunday[0] == "01" ){ ?> selected="selected" <?php } ?>>01</option><option value="02" <?php if( $sunday[0] == "02" ){ ?> selected="selected" <?php } ?>>02</option><option value="03" <?php if( $sunday[0] == "03" ){ ?> selected="selected" <?php } ?>>03</option><option value="04" <?php if( $sunday[0] == "04" ){ ?> selected="selected" <?php } ?>>04</option><option value="05" <?php if( $sunday[0] == "05" ){ ?> selected="selected" <?php } ?>>05</option><option value="06" <?php if( $sunday[0] == "06" ){ ?> selected="selected" <?php } ?>>06</option><option value="07" <?php if( $sunday[0] == "07" ){ ?> selected="selected" <?php } ?>>07</option><option value="08" <?php if( $sunday[0] == "08" ){ ?> selected="selected" <?php } ?>>08</option><option value="09" <?php if( $sunday[0] == "09" ){ ?> selected="selected" <?php } ?>>09</option><option value="10" <?php if( $sunday[0] == "10" ){ ?> selected="selected" <?php } ?>>10</option><option value="11" <?php if( $sunday[0] == "11" ){ ?> selected="selected" <?php } ?>>11</option><option value="12" <?php if( $sunday[0] == "12" ){ ?> selected="selected" <?php } ?>>12</option><? }else { ?><option value="" <?php if( $sunday[0] == "" ){ ?> selected="selected" <?php } ?>></option><option value="00" <?php if( $sunday[0] == "00" ){ ?> selected="selected" <?php } ?>>00</option><option value="01" <?php if( $sunday[0] == "01" ){ ?> selected="selected" <?php } ?>>01</option><option value="02" <?php if( $sunday[0] == "02" ){ ?> selected="selected" <?php } ?>>02</option><option value="03" <?php if( $sunday[0] == "03" ){ ?> selected="selected" <?php } ?>>03</option><option value="04" <?php if( $sunday[0] == "04" ){ ?> selected="selected" <?php } ?>>04</option><option value="05" <?php if( $sunday[0] == "05" ){ ?> selected="selected" <?php } ?>>05</option><option value="06" <?php if( $sunday[0] == "06" ){ ?> selected="selected" <?php } ?>>06</option><option value="07" <?php if( $sunday[0] == "07" ){ ?> selected="selected" <?php } ?>>07</option><option value="08" <?php if( $sunday[0] == "08" ){ ?> selected="selected" <?php } ?>>08</option><option value="09" <?php if( $sunday[0] == "09" ){ ?> selected="selected" <?php } ?>>09</option><option value="10" <?php if( $sunday[0] == "10" ){ ?> selected="selected" <?php } ?>>10</option><option value="11" <?php if( $sunday[0] == "11" ){ ?> selected="selected" <?php } ?>>11</option><option value="12" <?php if( $sunday[0] == "12" ){ ?> selected="selected" <?php } ?>>12</option><option value="13" <?php if( $sunday[0] == "13" ){ ?> selected="selected" <?php } ?>>13</option><option value="14" <?php if( $sunday[0] == "14" ){ ?> selected="selected" <?php } ?>>14</option><option value="15" <?php if( $sunday[0] == "15" ){ ?> selected="selected" <?php } ?>>15</option><option value="16" <?php if( $sunday[0] == "16" ){ ?> selected="selected" <?php } ?>>16</option><option value="17" <?php if( $sunday[0] == "17" ){ ?> selected="selected" <?php } ?>>17</option><option value="18" <?php if( $sunday[0] == "18" ){ ?> selected="selected" <?php } ?>>18</option><option value="19" <?php if( $sunday[0] == "19" ){ ?> selected="selected" <?php } ?>>19</option><option value="20" <?php if( $sunday[0] == "20" ){ ?> selected="selected" <?php } ?>>20</option><option value="21" <?php if( $sunday[0] == "21" ){ ?> selected="selected" <?php } ?>>21</option><option value="22" <?php if( $sunday[0] == "22" ){ ?> selected="selected" <?php } ?>>22</option><option value="23" <?php if( $sunday[0] == "23" ){ ?> selected="selected" <?php } ?>>23</option><?php } ?></select><select id="lcn_opening_hours_opens_at_minutes_sunday" name="lcn_opening_hours_opens_at_minutes_sunday"><option value="" <?php if( $sunday[1] == "" ){ ?> selected="selected" <?php } ?>></option><option value="00" <?php if( $sunday[1] == "00" ){ ?> selected="selected" <?php } ?>>00</option><option value="15" <?php if( $sunday[1] == "15" ){ ?> selected="selected" <?php } ?>>15</option><option value="30" <?php if( $sunday[1] == "30" ){ ?> selected="selected" <?php } ?>>30</option><option value="45" <?php if( $sunday[1] == "45" ){ ?> selected="selected" <?php } ?>>45</option></select><?php if($openinghoursformat == "12"){ ?><select id="lcn_opening_hours_opens_at_am_pm_sunday" name="lcn_opening_hours_opens_at_am_pm_sunday"><option value="" <?php if( $sunday[2] == "" ){ ?> selected="selected" <?php } ?>></option><option value="AM" <?php if( $sunday[2] == "AM" ){ ?> selected="selected" <?php } ?>>AM</option><option value="PM" <?php if( $sunday[2] == "PM" ){ ?> selected="selected" <?php } ?>>PM</option></select><? } ?></td>
										<td><select id="lcn_opening_hours_closes_at_hour_sunday" name="lcn_opening_closes_opens_at_hour_sunday"><?php if($openinghoursformat == "12"){ ?><option value="" <?php if( $sunday[3] == "" ){ ?> selected="selected" <?php } ?>></option><option value="01" <?php if( $sunday[3] == "01" ){ ?> selected="selected" <?php } ?>>01</option><option value="02" <?php if( $sunday[3] == "02" ){ ?> selected="selected" <?php } ?>>02</option><option value="03" <?php if( $sunday[3] == "03" ){ ?> selected="selected" <?php } ?>>03</option><option value="04" <?php if( $sunday[3] == "04" ){ ?> selected="selected" <?php } ?>>04</option><option value="05" <?php if( $sunday[3] == "05" ){ ?> selected="selected" <?php } ?>>05</option><option value="06" <?php if( $sunday[3] == "06" ){ ?> selected="selected" <?php } ?>>06</option><option value="07" <?php if( $sunday[3] == "07" ){ ?> selected="selected" <?php } ?>>07</option><option value="08" <?php if( $sunday[3] == "08" ){ ?> selected="selected" <?php } ?>>08</option><option value="09" <?php if( $sunday[3] == "09" ){ ?> selected="selected" <?php } ?>>09</option><option value="10" <?php if( $sunday[3] == "10" ){ ?> selected="selected" <?php } ?>>10</option><option value="11" <?php if( $sunday[3] == "11" ){ ?> selected="selected" <?php } ?>>11</option><option value="12" <?php if( $sunday[3] == "12" ){ ?> selected="selected" <?php } ?>>12</option><? }else { ?><option value="" <?php if( $sunday[3] == "" ){ ?> selected="selected" <?php } ?>></option><option value="00" <?php if( $sunday[3] == "00" ){ ?> selected="selected" <?php } ?>>00</option><option value="01" <?php if( $sunday[3] == "01" ){ ?> selected="selected" <?php } ?>>01</option><option value="02" <?php if( $sunday[3] == "02" ){ ?> selected="selected" <?php } ?>>02</option><option value="03" <?php if( $sunday[3] == "03" ){ ?> selected="selected" <?php } ?>>03</option><option value="04" <?php if( $sunday[3] == "04" ){ ?> selected="selected" <?php } ?>>04</option><option value="05" <?php if( $sunday[3] == "05" ){ ?> selected="selected" <?php } ?>>05</option><option value="06" <?php if( $sunday[3] == "06" ){ ?> selected="selected" <?php } ?>>06</option><option value="07" <?php if( $sunday[3] == "07" ){ ?> selected="selected" <?php } ?>>07</option><option value="08" <?php if( $sunday[3] == "08" ){ ?> selected="selected" <?php } ?>>08</option><option value="09" <?php if( $sunday[3] == "09" ){ ?> selected="selected" <?php } ?>>09</option><option value="10" <?php if( $sunday[3] == "10" ){ ?> selected="selected" <?php } ?>>10</option><option value="11" <?php if( $sunday[3] == "11" ){ ?> selected="selected" <?php } ?>>11</option><option value="12" <?php if( $sunday[3] == "12" ){ ?> selected="selected" <?php } ?>>12</option><option value="13" <?php if( $sunday[3] == "13" ){ ?> selected="selected" <?php } ?>>13</option><option value="14" <?php if( $sunday[3] == "14" ){ ?> selected="selected" <?php } ?>>14</option><option value="15" <?php if( $sunday[3] == "15" ){ ?> selected="selected" <?php } ?>>15</option><option value="16" <?php if( $sunday[3] == "16" ){ ?> selected="selected" <?php } ?>>16</option><option value="17" <?php if( $sunday[3] == "17" ){ ?> selected="selected" <?php } ?>>17</option><option value="18" <?php if( $sunday[3] == "18" ){ ?> selected="selected" <?php } ?>>18</option><option value="19" <?php if( $sunday[3] == "19" ){ ?> selected="selected" <?php } ?>>19</option><option value="20" <?php if( $sunday[3] == "20" ){ ?> selected="selected" <?php } ?>>20</option><option value="21" <?php if( $sunday[3] == "21" ){ ?> selected="selected" <?php } ?>>21</option><option value="22" <?php if( $sunday[3] == "22" ){ ?> selected="selected" <?php } ?>>22</option><option value="23" <?php if( $sunday[3] == "23" ){ ?> selected="selected" <?php } ?>>23</option><?php } ?></select><select id="lcn_opening_hours_closes_at_minutes_sunday" name="lcn_opening_hours_closes_at_minutes_sunday"><option value="" <?php if( $sunday[4] == "" ){ ?> selected="selected" <?php } ?>></option><option value="00" <?php if( $sunday[4] == "00" ){ ?> selected="selected" <?php } ?>>00</option><option value="15" <?php if( $sunday[4] == "15" ){ ?> selected="selected" <?php } ?>>15</option><option value="30" <?php if( $sunday[4] == "30" ){ ?> selected="selected" <?php } ?>>30</option><option value="45" <?php if( $sunday[4] == "45" ){ ?> selected="selected" <?php } ?>>45</option></select><?php if($openinghoursformat == "12"){ ?><select id="lcn_opening_hours_closes_at_am_pm_sunday" name="lcn_opening_hours_closes_at_am_pm_sunday"><option value="" <?php if( $sunday[5] == "" ){ ?> selected="selected" <?php } ?>></option><option value="AM" <?php if( $sunday[5] == "AM" ){ ?> selected="selected" <?php } ?>>AM</option><option value="PM" <?php if( $sunday[5] == "PM" ){ ?> selected="selected" <?php } ?>>PM</option></select><? } ?></td>
									</tr>
								</tbody>
							</table>
							<p><input type="button" value="Save Location" name="locsett" class="button button-primary lcn-save-hours-settings-button"><span class="successmessagenew">Settings updated successfully</span></p>
						</div>
					</form>
				</div>
				<div class="createdby"><p>Developed by: <a href="http://www.lokalyze.com/" target="_blank">LOKALYZE</a></p></div>
			</div>
		</div>
	</div>
<?php
	}
}
/***************** End Working Hours Content *****************/

/***************** Working Hours Update *****************/
add_action( 'wp_ajax_saveopeninghours', 'lcn_ajax_add_openinghours' );
function lcn_ajax_add_openinghours(){
	$user = wp_get_current_user();
	$allowed_roles = array('administrator');
	if( array_intersect($allowed_roles, $user->roles ) ){
		if( current_user_can('manage_options') ){
			global $wpdb;
	        $table_name = $wpdb->prefix.'callmenow';
			
			$open_hour_format = sanitize_text_field( $_POST['openfrmt'] );
			$mondayopenhour = sanitize_text_field( $_POST['moh'] );
			$mondayopenmins = sanitize_text_field( $_POST['mom'] );
			$mondayopenampm = sanitize_text_field( $_POST['moap'] );
			$mondayclosehour = sanitize_text_field( $_POST['mch'] );
			$mondayclosemins = sanitize_text_field( $_POST['mcm'] );
			$mondaycloseampm = sanitize_text_field( $_POST['mcap'] );
			$mondaydetail = $mondayopenhour."-|#|-".$mondayopenmins."-|#|-".$mondayopenampm."-|#|-".$mondayclosehour."-|#|-".$mondayclosemins."-|#|-".$mondaycloseampm;
			$tuesdayopenhour = sanitize_text_field( $_POST['tuoh'] );
			$tuesdayopenmins = sanitize_text_field( $_POST['tuom'] );
			$tuesdayopenampm = sanitize_text_field( $_POST['tuoap'] );
			$tuesdayclosehour = sanitize_text_field( $_POST['tuch'] );
			$tuesdayclosemins = sanitize_text_field( $_POST['tucm'] );
			$tuesdaycloseampm = sanitize_text_field( $_POST['tucap'] );
			$tuesdaydetail = $tuesdayopenhour."-|#|-".$tuesdayopenmins."-|#|-".$tuesdayopenampm."-|#|-".$tuesdayclosehour."-|#|-".$tuesdayclosemins."-|#|-".$tuesdaycloseampm;
			$wednesdayopenhour = sanitize_text_field( $_POST['woh'] );
			$wednesdayopenmins = sanitize_text_field( $_POST['wom'] );
			$wednesdayopenampm = sanitize_text_field( $_POST['woap'] );
			$wednesdayclosehour = sanitize_text_field( $_POST['wch'] );
			$wednesdayclosemins = sanitize_text_field( $_POST['wcm'] );
			$wednesdayclosampm = sanitize_text_field( $_POST['wcap'] );
			$wednesdaydetail = $wednesdayopenhour."-|#|-".$wednesdayopenmins."-|#|-".$wednesdayopenampm."-|#|-".$wednesdayclosehour."-|#|-".$wednesdayclosemins."-|#|-".$wednesdayclosampm;
			$thursdayopenhour = sanitize_text_field( $_POST['thoh'] );
			$thursdayopenmins = sanitize_text_field( $_POST['thom'] );
			$thursdayopenampm = sanitize_text_field( $_POST['thoap'] );
			$thursdayclosehour = sanitize_text_field( $_POST['thch'] );
			$thursdayclosemins = sanitize_text_field( $_POST['thcm'] );
			$thursdaycloseampm = sanitize_text_field( $_POST['thcap'] );
			$thursdaydetail = $thursdayopenhour."-|#|-".$thursdayopenmins."-|#|-".$thursdayopenampm."-|#|-".$thursdayclosehour."-|#|-".$thursdayclosemins."-|#|-".$thursdaycloseampm;
			$fridayopenhour = sanitize_text_field( $_POST['foh'] );
			$fridayopenmins = sanitize_text_field( $_POST['fom'] );
			$fridayopenampm = sanitize_text_field( $_POST['foap'] );
			$fridayclosehour = sanitize_text_field( $_POST['fch'] );
			$fridayclosemins = sanitize_text_field( $_POST['fcm'] );
			$fridaycloseampm = sanitize_text_field( $_POST['fcap'] );
			$fridaydetail = $fridayopenhour."-|#|-".$fridayopenmins."-|#|-".$fridayopenampm."-|#|-".$fridayclosehour."-|#|-".$fridayclosemins."-|#|-".$fridaycloseampm;
			$saturdayopenhour = sanitize_text_field( $_POST['saoh'] );
			$saturdayopenmins = sanitize_text_field( $_POST['saom'] );
			$saturdayopenampm = sanitize_text_field( $_POST['saoap'] );
			$saturdayclosehour = sanitize_text_field( $_POST['sach'] );
			$saturdayclosemins = sanitize_text_field( $_POST['sacm'] );
			$saturdaycloseampm = sanitize_text_field( $_POST['sacap'] );
			$saturdaydetail = $saturdayopenhour."-|#|-".$saturdayopenmins."-|#|-".$saturdayopenampm."-|#|-".$saturdayclosehour."-|#|-".$saturdayclosemins."-|#|-".$saturdaycloseampm;
			$sundayopenhour = sanitize_text_field( $_POST['suoh'] );
			$sundayopenmins = sanitize_text_field( $_POST['suom'] );
			$sundayopenampm = sanitize_text_field( $_POST['suoap'] );
			$sundayclosehour = sanitize_text_field( $_POST['such'] );
			$sundayclosemins = sanitize_text_field( $_POST['sucm'] );
			$sundaycloseampm = sanitize_text_field( $_POST['sucap'] );
			$sundaydetail = $sundayopenhour."-|#|-".$sundayopenmins."-|#|-".$sundayopenampm."-|#|-".$sundayclosehour."-|#|-".$sundayclosemins."-|#|-".$sundaycloseampm;
			$openinghours = $mondaydetail."-|$|-".$tuesdaydetail."-|$|-".$wednesdaydetail."-|$|-".$thursdaydetail."-|$|-".$fridaydetail."-|$|-".$saturdaydetail."-|$|-".$sundaydetail."-|$|-".$open_hour_format;
			if($open_hour_format == 12){
				$openinghoursdet = $wpdb->get_results( "SELECT * FROM $table_name" );
				foreach($openinghoursdet as $openhour){
					$openinghourscon = $openhour->openinghours;
					if($openinghourscon != ""){
						$openinghourdet = explode("-|$|-", $openinghourscon);
						$mondaydetails = $openinghourdet[0];
						$monday = explode("-|#|-", $mondaydetails);
						$tuesdaydetails = $openinghourdet[1];
						$tuesday = explode("-|#|-", $tuesdaydetails);
						$wednesdaydetails = $openinghourdet[2];
						$wednesday = explode("-|#|-", $wednesdaydetails);
						$thursdaydetails = $openinghourdet[3];
						$thursday = explode("-|#|-", $thursdaydetails);
						$fridaydetails = $openinghourdet[4];
						$friday = explode("-|#|-", $fridaydetails);
						$saturdaydetails = $openinghourdet[5];
						$saturday = explode("-|#|-", $saturdaydetails);
						$sundaydetails = $openinghourdet[6];
						$sunday = explode("-|#|-", $sundaydetails);
						$openhourformat = $openinghourdet[7];
						if($openhourformat == "12"){
                            $wpdb->query( "UPDATE $table_name SET openinghours = '$openinghours', openinghourformat = '$open_hour_format' where id = 1" );
						}else {
							$mon_open_hour = $monday[0];
							$mon_open_mins = $monday[1];
							$mon_open_ampm = $monday[2];
							$mon_close_hour = $monday[3];
							$mon_close_mins = $monday[4];
							$mon_close_ampm = $monday[5];
							if($mon_open_hour != ""){
								if($mon_open_hour > 11){
									if($mon_open_hour == 12){ $mon_open_hour = $mon_open_hour; }else { $mon_open_hour = $mon_open_hour - 12; }
									$mon_open_ampm = "PM";
								}else {
									if($mon_open_hour == 0){ $mon_open_hour = $mon_open_hour + 12; }else { $mon_open_hour = $mon_open_hour; }
									$mon_open_ampm = "AM";
								}
							}
							if($mon_close_hour != ""){
								if($mon_close_hour > 11){
									if($mon_close_hour == 12){ $mon_close_hour = $mon_close_hour; }else { $mon_close_hour = $mon_close_hour - 12; }
									$mon_close_ampm = "PM";
								}else {
									if($mon_close_hour == 0){ $mon_close_hour = $mon_close_hour + 12; }else { $mon_close_hour = $mon_close_hour; }
									$mon_close_ampm = "AM";
								}
							}
							$mondayh = $mon_open_hour."-|#|-".$mon_open_mins."-|#|-".$mon_open_ampm."-|#|-".$mon_close_hour."-|#|-".$mon_close_mins."-|#|-".$mon_close_ampm;
							$tue_open_hour = $tuesday[0];
							$tue_open_mins = $tuesday[1];
							$tue_open_ampm = $tuesday[2];
							$tue_close_hour = $tuesday[3];
							$tue_close_mins = $tuesday[4];
							$tue_close_ampm = $tuesday[5];
							if($tue_open_hour != ""){
								if($tue_open_hour > 11){
									if($tue_open_hour == 12){ $tue_open_hour = $tue_open_hour; }else { $tue_open_hour = $tue_open_hour - 12; }
									$tue_open_ampm = "PM";
								}else {
									if($tue_open_hour == 0){ $tue_open_hour = $tue_open_hour + 12; }else { $tue_open_hour = $tue_open_hour; }
									$tue_open_ampm = "AM";
								}
							}
							if($tue_close_hour != ""){
								if($tue_close_hour > 11){
									if($tue_close_hour == 12){ $tue_close_hour = $tue_close_hour; }else { $tue_close_hour = $tue_close_hour - 12; }
									$tue_close_ampm = "PM";
								}else {
									if($tue_close_hour == 0){ $tue_close_hour = $tue_close_hour + 12; }else { $tue_close_hour = $tue_close_hour; }
									$tue_close_ampm = "AM";
								}
							}
							$tuesdayh = $tue_open_hour."-|#|-".$tue_open_mins."-|#|-".$tue_open_ampm."-|#|-".$tue_close_hour."-|#|-".$tue_close_mins."-|#|-".$tue_close_ampm;
							$wed_open_hour = $wednesday[0];
							$wed_open_mins = $wednesday[1];
							$wed_open_ampm = $wednesday[2];
							$wed_close_hour = $wednesday[3];
							$wed_close_mins = $wednesday[4];
							$wed_close_ampm = $wednesday[5];
							if($wed_open_hour != ""){
								if($wed_open_hour > 11){
									if($wed_open_hour == 12){ $wed_open_hour = $wed_open_hour; }else { $wed_open_hour = $wed_open_hour - 12; }
									$wed_open_ampm = "PM";
								}else {
									if($wed_open_hour == 0){ $wed_open_hour = $wed_open_hour + 12; }else { $wed_open_hour = $wed_open_hour; }
									$wed_open_ampm = "AM";
								}
							}
							if($wed_close_hour != ""){
								if($wed_close_hour > 11){
									if($wed_close_hour == 12){ $wed_close_hour = $wed_close_hour; }else { $wed_close_hour = $wed_close_hour - 12; }
									$wed_close_ampm = "PM";
								}else {
									if($wed_close_hour == 0){ $wed_close_hour = $wed_close_hour + 12; }else { $wed_close_hour = $wed_close_hour; }
									$wed_close_ampm = "AM";
								}
							}
							$wednesdayh = $wed_open_hour."-|#|-".$wed_open_mins."-|#|-".$wed_open_ampm."-|#|-".$wed_close_hour."-|#|-".$wed_close_mins."-|#|-".$wed_close_ampm;
							$thu_open_hour = $thursday[0];
							$thu_open_mins = $thursday[1];
							$thu_open_ampm = $thursday[2];
							$thu_close_hour = $thursday[3];
							$thu_close_mins = $thursday[4];
							$thu_close_ampm = $thursday[5];
							if($thu_open_hour != ""){
								if($thu_open_hour > 11){
									if($thu_open_hour == 12){ $thu_open_hour = $thu_open_hour; }else { $thu_open_hour = $thu_open_hour - 12; }
									$thu_open_ampm = "PM";
								}else {
									if($thu_open_hour == 0){ $thu_open_hour = $thu_open_hour + 12; }else { $thu_open_hour = $thu_open_hour; }
									$thu_open_ampm = "AM";
								}
							}
							if($thu_close_hour != ""){
								if($thu_close_hour > 11){
									if($thu_close_hour == 12){ $thu_close_hour = $thu_close_hour; }else { $thu_close_hour = $thu_close_hour - 12; }
									$thu_close_ampm = "PM";
								}else {
									if($thu_close_hour == 0){ $thu_close_hour = $thu_close_hour + 12; }else { $thu_close_hour = $thu_close_hour; }
									$thu_close_ampm = "AM";
								}
							}
							$thursdayh = $thu_open_hour."-|#|-".$thu_open_mins."-|#|-".$thu_open_ampm."-|#|-".$thu_close_hour."-|#|-".$thu_close_mins."-|#|-".$thu_close_ampm;
							$fri_open_hour = $friday[0];
							$fri_open_mins = $friday[1];
							$fri_open_ampm = $friday[2];
							$fri_close_hour = $friday[3];
							$fri_close_mins = $friday[4];
							$fri_close_ampm = $friday[5];
							if($fri_open_hour != ""){
								if($fri_open_hour > 11){
									if($fri_open_hour == 12){ $fri_open_hour = $fri_open_hour; }else { $fri_open_hour = $fri_open_hour - 12; }
									$fri_open_ampm = "PM";
								}else {
									if($fri_open_hour == 0){ $fri_open_hour = $fri_open_hour + 12; }else { $fri_open_hour = $fri_open_hour; }
									$fri_open_ampm = "AM";
								}
							}
							if($fri_close_hour != ""){
								if($fri_close_hour > 11){
									if($fri_close_hour == 12){ $fri_close_hour = $fri_close_hour; }else { $fri_close_hour = $fri_close_hour - 12; }
									$fri_close_ampm = "PM";
								}else {
									if($fri_close_hour == 0){ $fri_close_hour = $fri_close_hour + 12; }else { $fri_close_hour = $fri_close_hour; }
									$fri_close_ampm = "AM";
								}
							}
							$fridayh = $fri_open_hour."-|#|-".$fri_open_mins."-|#|-".$fri_open_ampm."-|#|-".$fri_close_hour."-|#|-".$fri_close_mins."-|#|-".$fri_close_ampm;
							$sat_open_hour = $saturday[0];
							$sat_open_mins = $saturday[1];
							$sat_open_ampm = $saturday[2];
							$sat_close_hour = $saturday[3];
							$sat_close_mins = $saturday[4];
							$sat_close_ampm = $saturday[5];
							if($sat_open_hour != ""){
								if($sat_open_hour > 11){
									if($sat_open_hour == 12){ $sat_open_hour = $sat_open_hour; }else { $sat_open_hour = $sat_open_hour - 12; }
									$sat_open_ampm = "PM";
								}else {
									if($sat_open_hour == 0){ $sat_open_hour = $sat_open_hour + 12; }else { $sat_open_hour = $sat_open_hour; }
									$sat_open_ampm = "AM";
								}
							}
							if($sat_close_hour != ""){
								if($sat_close_hour > 11){
									if($sat_close_hour == 12){ $sat_close_hour = $sat_close_hour; }else { $sat_close_hour = $sat_close_hour - 12; }
									$sat_close_ampm = "PM";
								}else {
									if($sat_close_hour == 0){ $sat_close_hour = $sat_close_hour + 12; }else { $sat_close_hour = $sat_close_hour; }
									$sat_close_ampm = "AM";
								}
							}
							$saturdayh = $sat_open_hour."-|#|-".$sat_open_mins."-|#|-".$sat_open_ampm."-|#|-".$sat_close_hour."-|#|-".$sat_close_mins."-|#|-".$sat_close_ampm;
							$sun_open_hour = $sunday[0];
							$sun_open_mins = $sunday[1];
							$sun_open_ampm = $sunday[2];
							$sun_close_hour = $sunday[3];
							$sun_close_mins = $sunday[4];
							$sun_close_ampm = $sunday[5];
							if($sun_open_hour != ""){
								if($sun_open_hour > 11){
									if($sun_open_hour == 12){ $sun_open_hour = $sun_open_hour; }else { $sun_open_hour = $sun_open_hour - 12; }
									$sun_open_ampm = "PM";
								}else {
									if($sun_open_hour == 0){ $sun_open_hour = $sun_open_hour + 12; }else { $sun_open_hour = $sun_open_hour; }
									$sun_open_ampm = "AM";
								}
							}
							if($sun_close_hour != ""){
								if($sun_close_hour > 11){
									if($sun_close_hour == 12){ $sun_close_hour = $sun_close_hour; }else { $sun_close_hour = $sun_close_hour - 12; }
									$sun_close_ampm = "PM";
								}else {
									if($sun_close_hour == 0){ $sun_close_hour = $sun_close_hour + 12; }else { $sun_close_hour = $sun_close_hour; }
									$sun_close_ampm = "AM";
								}
							}
							$sundayh = $sun_open_hour."-|#|-".$sun_open_mins."-|#|-".$sun_open_ampm."-|#|-".$sun_close_hour."-|#|-".$sun_close_mins."-|#|-".$sun_close_ampm;
							$hrsformat = "12";
							$openinghours = $mondayh."-|$|-".$tuesdayh."-|$|-".$wednesdayh."-|$|-".$thursdayh."-|$|-".$fridayh."-|$|-".$saturdayh."-|$|-".$sundayh."-|$|-".$hrsformat;
							$wpdb->query( "UPDATE $table_name SET openinghourformat = '$hrsformat', openinghours = '$openinghours' where id = 1" );
						}
					}else {
					    $sql1 = "UPDATE $table_name SET openinghours = '$openinghours', openinghourformat = '$open_hour_format' where id = 1";
					    $wpdb->query( "UPDATE $table_name SET openinghours = '$openinghours', openinghourformat = '$open_hour_format' where id = 1" );
					}
				}
			}else {
				$openinghoursdet = $wpdb->get_results( "SELECT * FROM $table_name" );
				foreach($openinghoursdet as $openhour){
					$openinghourscon = $openhour->openinghours;
					if($openinghourscon != ""){
						$openinghourdet = explode("-|$|-", $openinghourscon);
						$mondaydetails = $openinghourdet[0];
						$monday = explode("-|#|-", $mondaydetails);
						$tuesdaydetails = $openinghourdet[1];
						$tuesday = explode("-|#|-", $tuesdaydetails);
						$wednesdaydetails = $openinghourdet[2];
						$wednesday = explode("-|#|-", $wednesdaydetails);
						$thursdaydetails = $openinghourdet[3];
						$thursday = explode("-|#|-", $thursdaydetails);
						$fridaydetails = $openinghourdet[4];
						$friday = explode("-|#|-", $fridaydetails);
						$saturdaydetails = $openinghourdet[5];
						$saturday = explode("-|#|-", $saturdaydetails);
						$sundaydetails = $openinghourdet[6];
						$sunday = explode("-|#|-", $sundaydetails);
						$openhourformat = $openinghourdet[7];
						if($openhourformat == "24"){
						    $wpdb->query( "UPDATE $table_name SET openinghours = '$openinghours', openinghourformat = '$open_hour_format' where id = 1" );
						}else {
							$mon_open_hour = $monday[0];
							$mon_open_mins = $monday[1];
							$mon_open_ampm = $monday[2];
							$mon_close_hour = $monday[3];
							$mon_close_mins = $monday[4];
							$mon_close_ampm = $monday[5];
							if(($mon_open_hour == 12) && ($mon_open_ampm == "AM")){ $mon_open_hour = $mon_open_hour - 12; }
							if(($mon_open_hour == 12) && ($mon_open_ampm == "PM")){ $mon_open_hour = $mon_open_hour; }
							if(($mon_open_hour < 12) && ($mon_open_ampm == "AM")){ $mon_open_hour = $mon_open_hour;	}
							if(($mon_open_hour < 12) && ($mon_open_ampm == "PM")){ $mon_open_hour = $mon_open_hour + 12; }
							if(($mon_open_hour == "") && ($mon_open_ampm == "")){ $mon_open_hour = $mon_open_hour; }
							$mon_open_ampm = "";
							if(($mon_close_hour == 12) && ($mon_close_ampm == "AM")){ $mon_close_hour = $mon_close_hour - 12; }
							if(($mon_close_hour == 12) && ($mon_close_ampm == "PM")){ $mon_close_hour = $mon_close_hour; }
							if(($mon_close_hour < 12) && ($mon_close_ampm == "AM")){ $mon_close_hour = $mon_close_hour; }
							if(($mon_close_hour < 12) && ($mon_close_ampm == "PM")){ $mon_close_hour = $mon_close_hour + 12; }
							if(($mon_close_hour == "") && ($mon_close_hour == "")){ $mon_close_hour = $mon_close_hour; }
							$mon_close_ampm = "";
							$mondayh = $mon_open_hour."-|#|-".$mon_open_mins."-|#|-".$mon_open_ampm."-|#|-".$mon_close_hour."-|#|-".$mon_close_mins."-|#|-".$mon_close_ampm;
							$tue_open_hour = $tuesday[0];
							$tue_open_mins = $tuesday[1];
							$tue_open_ampm = $tuesday[2];
							$tue_close_hour = $tuesday[3];
							$tue_close_mins = $tuesday[4];
							$tue_close_ampm = $tuesday[5];
							if(($tue_open_hour == 12) && ($tue_open_ampm == "AM")){ $tue_open_hour = $tue_open_hour - 12; }
							if(($tue_open_hour == 12) && ($tue_open_ampm == "PM")){ $tue_open_hour = $tue_open_hour; }
							if(($tue_open_hour < 12) && ($tue_open_ampm == "AM")){ $tue_open_hour = $tue_open_hour; }
							if(($tue_open_hour < 12) && ($tue_open_ampm == "PM")){ $tue_open_hour = $tue_open_hour + 12; }
							if(($tue_open_hour == "") && ($tue_open_ampm == "")){ $tue_open_hour = $tue_open_hour; }
							$tue_open_ampm = "";
							if(($tue_close_hour == 12) && ($tue_close_ampm == "AM")){ $tue_close_hour = $tue_close_hour - 12; }
							if(($tue_close_hour == 12) && ($tue_close_ampm == "PM")){ $tue_close_hour = $tue_close_hour; }
							if(($tue_close_hour < 12) && ($tue_close_ampm == "AM")){ $tue_close_hour = $tue_close_hour; }
							if(($tue_close_hour < 12) && ($tue_close_ampm == "PM")){ $tue_close_hour = $tue_close_hour + 12; }
							if(($tue_close_hour == "") && ($tue_close_hour == "")){ $tue_close_hour = $tue_close_hour; }
							$tue_close_ampm = "";
							$tuesdayh = $tue_open_hour."-|#|-".$tue_open_mins."-|#|-".$tue_open_ampm."-|#|-".$tue_close_hour."-|#|-".$tue_close_mins."-|#|-".$tue_close_ampm;
							$wed_open_hour = $wednesday[0];
							$wed_open_mins = $wednesday[1];
							$wed_open_ampm = $wednesday[2];
							$wed_close_hour = $wednesday[3];
							$wed_close_mins = $wednesday[4];
							$wed_close_ampm = $wednesday[5];
							if(($wed_open_hour == 12) && ($wed_open_ampm == "AM")){ $wed_open_hour = $wed_open_hour - 12; }
							if(($wed_open_hour == 12) && ($wed_open_ampm == "PM")){ $wed_open_hour = $wed_open_hour; }
							if(($wed_open_hour < 12) && ($wed_open_ampm == "AM")){ $wed_open_hour = $wed_open_hour; }
							if(($wed_open_hour < 12) && ($wed_open_ampm == "PM")){ $wed_open_hour = $wed_open_hour + 12; }
							if(($wed_open_hour == "") && ($wed_open_hour == "")){ $wed_open_hour = $wed_open_hour; }
							$wed_open_ampm = "";
							if(($wed_close_hour == 12) && ($wed_close_ampm == "AM")){ $wed_close_hour = $wed_close_hour - 12; }
							if(($wed_close_hour == 12) && ($wed_close_ampm == "PM")){ $wed_close_hour = $wed_close_hour; }
							if(($wed_close_hour < 12) && ($wed_close_ampm == "AM")){ $wed_close_hour = $wed_close_hour;	}
							if(($wed_close_hour < 12) && ($wed_close_ampm == "PM")){ $wed_close_hour = $wed_close_hour + 12; }
							if(($wed_close_hour == "") && ($wed_close_hour == "")){ $wed_close_hour = $wed_close_hour; }
							$wed_close_ampm = "";
							$wednesdayh = $wed_open_hour."-|#|-".$wed_open_mins."-|#|-".$wed_open_ampm."-|#|-".$wed_close_hour."-|#|-".$wed_close_mins."-|#|-".$wed_close_ampm;
							$thu_open_hour = $thursday[0];
							$thu_open_mins = $thursday[1];
							$thu_open_ampm = $thursday[2];
							$thu_close_hour = $thursday[3];
							$thu_close_mins = $thursday[4];
							$thu_close_ampm = $thursday[5];
							if(($thu_open_hour == 12) && ($thu_open_ampm == "AM")){ $thu_open_hour = $thu_open_hour - 12; }
							if(($thu_open_hour == 12) && ($thu_open_ampm == "PM")){ $thu_open_hour = $thu_open_hour; }
							if(($thu_open_hour < 12) && ($thu_open_ampm == "AM")){ $thu_open_hour = $thu_open_hour; }
							if(($thu_open_hour < 12) && ($thu_open_ampm == "PM")){ $thu_open_hour = $thu_open_hour + 12; }
							if(($thu_open_hour == "") && ($thu_open_hour == "")){ $thu_open_hour = $thu_open_hour; }
							$thu_open_ampm = "";
							if(($thu_close_hour == 12) && ($thu_close_ampm == "AM")){ $thu_close_hour = $thu_close_hour - 12; }
							if(($thu_close_hour == 12) && ($thu_close_ampm == "PM")){ $thu_close_hour = $thu_close_hour; }
							if(($thu_close_hour < 12) && ($thu_close_ampm == "AM")){ $thu_close_hour = $thu_close_hour; }
							if(($thu_close_hour < 12) && ($thu_close_ampm == "PM")){ $thu_close_hour = $thu_close_hour + 12; }
							if(($thu_close_hour == "") && ($thu_close_hour == "")){ $thu_close_hour = $thu_close_hour; }
							$thu_close_ampm = "";
							$thursdayh = $thu_open_hour."-|#|-".$thu_open_mins."-|#|-".$thu_open_ampm."-|#|-".$thu_close_hour."-|#|-".$thu_close_mins."-|#|-".$thu_close_ampm;					
							$fri_open_hour = $friday[0];
							$fri_open_mins = $friday[1];
							$fri_open_ampm = $friday[2];
							$fri_close_hour = $friday[3];
							$fri_close_mins = $friday[4];
							$fri_close_ampm = $friday[5];
							if(($fri_open_hour == 12) && ($fri_open_ampm == "AM")){ $fri_open_hour = $fri_open_hour - 12; }
							if(($fri_open_hour == 12) && ($fri_open_ampm == "PM")){ $fri_open_hour = $fri_open_hour; }
							if(($fri_open_hour < 12) && ($fri_open_ampm == "AM")){ $fri_open_hour = $fri_open_hour; }
							if(($fri_open_hour < 12) && ($fri_open_ampm == "PM")){ $fri_open_hour = $fri_open_hour + 12; }
							if(($fri_open_hour == "") && ($fri_open_hour == "")){ $fri_open_hour = $fri_open_hour; }
							$fri_open_ampm = "";
							if(($fri_close_hour == 12) && ($fri_close_ampm == "AM")){ $fri_close_hour = $fri_close_hour - 12; }
							if(($fri_close_hour == 12) && ($fri_close_ampm == "PM")){ $fri_close_hour = $fri_close_hour; }
							if(($fri_close_hour < 12) && ($fri_close_ampm == "AM")){ $fri_close_hour = $fri_close_hour;	}
							if(($fri_close_hour < 12) && ($fri_close_ampm == "PM")){ $fri_close_hour = $fri_close_hour + 12; }
							if(($fri_close_hour == "") && ($fri_close_hour == "")){ $fri_close_hour = $fri_close_hour; }
							$fri_close_ampm = "";
							$fridayh = $fri_open_hour."-|#|-".$fri_open_mins."-|#|-".$fri_open_ampm."-|#|-".$fri_close_hour."-|#|-".$fri_close_mins."-|#|-".$fri_close_ampm;
							$sat_open_hour = $saturday[0];
							$sat_open_mins = $saturday[1];
							$sat_open_ampm = $saturday[2];
							$sat_close_hour = $saturday[3];
							$sat_close_mins = $saturday[4];
							$sat_close_ampm = $saturday[5];
							if(($sat_open_hour == 12) && ($sat_open_ampm == "AM")){ $sat_open_hour = $sat_open_hour - 12; }
							if(($sat_open_hour == 12) && ($sat_open_ampm == "PM")){ $sat_open_hour = $sat_open_hour; }
							if(($sat_open_hour < 12) && ($sat_open_ampm == "AM")){ $sat_open_hour = $sat_open_hour; }
							if(($sat_open_hour < 12) && ($sat_open_ampm == "PM")){ $sat_open_hour = $sat_open_hour + 12; }
							if(($sat_open_hour == "") && ($sat_open_hour == "")){ $sat_open_hour = $sat_open_hour; }
							$sat_open_ampm = "";
							if(($sat_close_hour == 12) && ($sat_close_ampm == "AM")){ $sat_close_hour = $sat_close_hour - 12; }
							if(($sat_close_hour == 12) && ($sat_close_ampm == "PM")){ $sat_close_hour = $sat_close_hour; } 
							if(($sat_close_hour < 12) && ($sat_close_ampm == "AM")){ $sat_close_hour = $sat_close_hour; }
							if(($sat_close_hour < 12) && ($sat_close_ampm == "PM")){ $sat_close_hour = $sat_close_hour + 12; }
							if(($sat_close_hour == "") && ($sat_close_hour == "")){ $sat_close_hour = $sat_close_hour; }
							$sat_close_ampm = "";
							$saturdayh = $sat_open_hour."-|#|-".$sat_open_mins."-|#|-".$sat_open_ampm."-|#|-".$sat_close_hour."-|#|-".$sat_close_mins."-|#|-".$sat_close_ampm;
							$sun_open_hour = $sunday[0];
							$sun_open_mins = $sunday[1];
							$sun_open_ampm = $sunday[2];
							$sun_close_hour = $sunday[3];
							$sun_close_mins = $sunday[4];
							$sun_close_ampm = $sunday[5];
							if(($sun_open_hour == 12) && ($sun_open_ampm == "AM")){ $sun_open_hour = $sun_open_hour - 12; }
							if(($sun_open_hour == 12) && ($sun_open_ampm == "PM")){ $sun_open_hour = $sun_open_hour; }
							if(($sun_open_hour < 12) && ($sun_open_ampm == "AM")){ $sun_open_hour = $sun_open_hour;	}
							if(($sun_open_hour < 12) && ($sun_open_ampm == "PM")){ $sun_open_hour = $sun_open_hour + 12; }
							if(($sun_open_hour == "") && ($sun_open_hour == "")){ $sun_open_hour = $sun_open_hour; }
							$sun_open_ampm = "";
							if(($sun_close_hour == 12) && ($sun_close_ampm == "AM")){ $sun_close_hour = $sun_close_hour - 12; }
							if(($sun_close_hour == 12) && ($sun_close_ampm == "PM")){ $sun_close_hour = $sun_close_hour; }
							if(($sun_close_hour < 12) && ($sun_close_ampm == "AM")){ $sun_close_hour = $sun_close_hour; }
							if(($sun_close_hour < 12) && ($sun_close_ampm == "PM")){ $sun_close_hour = $sun_close_hour + 12; }
							if(($sun_close_hour == "") && ($sun_close_hour == "")){ $sun_close_hour = $sun_close_hour; }
							$sun_close_ampm = "";
							$sundayh = $sun_open_hour."-|#|-".$sun_open_mins."-|#|-".$sun_open_ampm."-|#|-".$sun_close_hour."-|#|-".$sun_close_mins."-|#|-".$sun_close_ampm;
							$hrsformat = "24";
							$openinghours = $mondayh."-|$|-".$tuesdayh."-|$|-".$wednesdayh."-|$|-".$thursdayh."-|$|-".$fridayh."-|$|-".$saturdayh."-|$|-".$sundayh."-|$|-".$hrsformat;
							
							$wpdb->query( "UPDATE $table_name SET openinghourformat = '$hrsformat', openinghours = '$openinghours' where id = 1" );
						}
					}else {
					    $wpdb->query( "UPDATE $table_name SET openinghours = '$openinghours', openinghourformat = '$open_hour_format' where id = 1" );
					}
				}
			}
		}
	}
	echo "Opening Hours Settings Updated Successfully";
	exit();
}
/***************** End Working Hours Update *****************/

/***************** Call Me Now - User Guide *****************/
function lcn_user_guide_function(){
?>
	<div class="maincontainer">
		<div class="mainwrap">
			<div class="maincontent">
				<h1>CALL ME NOW BUTTON - USER GUIDE</h1>
				<div class="formcont">
					<h3>Call Now Button</h3>
					<p>This page enables you to configure the 'Call Now Button' that will be added to your website when it is viewed in mobile devices. It will not show when your website is viewed on desktop devices.</p>
					<p>The Call Now button enables a site visitor from your site to quickly and easily click a phone icon on their mobile device to call the phone number entered in this section. You can define the phone number so you can enter where you would like the call forwarded to.</p>
					<p>The phone button that appears when your site is viewed on a mobile device does not move when a user scrolls through the site. This enables the user to be able to easily contact your business whenever they are on your website.</p>
				    <table class="lsform_table">
						<tbody>
							<tr>
								<td>
									<h3>Call Now Button</h3>
									<p>You can define whether or not you would like to use the Call Now Button on your website.</p>
									<h3>Phone number</h3>
									<p>You will enter the phone number that site visitors will be directed to from your site.</p>
									<h3>Icon color</h3>
									<p>You can define the color of the Call Now button that is shown on your site. There are two ways to define your color. You can enter the six digit color code or choose the color from the color panel.</p>
									<h3>Appearance</h3>
									<p>You can define where you would like the icon to be displayed on the user's mobile device. The options are the right corner, left corner, and full bottom.</p>
									<h3>Click tracking</h3>
									<p>If the Google Analytics universal tracking script has been added to your site, the Call Now functionality has been integrated into Google Analytics. If have Google Analytics tracking your website using a plugin, you will need to add tracking code to enable Call Now tracking.<br />The Google Analytics tracking code is the default code used to set up Google Analytics. The script does not need to be modified to work.<br />When enabling Google Analytics tracking, select Google Universal Analytics.  If you do not want to enable Google Analytics tracking, select 'disabled'.</p>
									<h3>Tracking script</h3>
									<p>This is the field where you add the tracking script if you want to enable Google Analytics tracking of the Call Now Button.</p>
									<p>An example of the code is provided below:</p>
									<p><?php $scval = "&lt;script&gt;<br/>(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){<br/>(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),<br/>m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)<br/>})(window,document,'script','https://www.google-analytics.com/analytics.js','ga');<br/><br/>ga('create', 'UA-xxxxxxxxx-1', 'auto');<br/>ga('send', 'pageview');<br/><br/>&lt;/script&gt;"; echo $scval; ?></p>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				<div class="createdby"><p>Developed by: <a href="http://www.lokalyze.com/" target="_blank">LOKALYZE</a></p></div>
			</div>
		</div>
	</div>
<?php 
}
/***************** End Call Me Now - User Guide *****************/