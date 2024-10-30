jQuery(document).ready(function($){
	var ajaxurl = ajax_object.ajax_url;
	$( "#lokalyze_lcn_iconcolor" ).wpColorPicker();  
	$( ".lokalyze-save-callnowbutton" ).click(function(){
		var isValid = true;
		var call_but_status = $( "input[name=lokalyze_lcn_cnbutton]:checked" ).val();
		var call_but_number = $( "#lokalyze_lcn_callnowphone" ).val(); 
		if (call_but_number == null || call_but_number == "") {
			alert("Phone number cannot be empty");
			return false;
		}
		var call_but_color = $( "#lokalyze_lcn_iconcolor" ).val();
		var call_but_appear = $( "input[name=lokalyze_lcn_position]:checked" ).val();
		var call_but_cltrck = $( "input[name=lokalyze_lcn_tracking]:checked" ).val();
		var call_but_trkcode = $( "#lokalyze_lcn_tracking_code" ).val();
		var call_but_fulltxt = $( "#lokalyze_lcn_flltxt" ).val();
		var data = { 'action': 'savecallnowbutton', 'clbtstus': call_but_status, 'clbtnum': call_but_number, 'clbtclr': call_but_color, 'clbtapp': call_but_appear, 'clbtcltrk': call_but_cltrck, 'clbttrkcod': call_but_trkcode, 'clbtfulltxt': call_but_fulltxt };
		$.post(ajaxurl, data,function(response){
			$( ".successmessagenew" ).show().delay(1000).fadeOut();
			location.reload(true);
		});   
	}); 

	$( ".lcn-save-hours-settings-button" ).click(function(){ 
		var openhrformat = $( "#lcn_opening_hours_format" ).val();
		var mon_open_hour = $( "#lcn_opening_hours_opens_at_hour_monday" ).val();
		var mon_open_mins = $( "#lcn_opening_hours_opens_at_minutes_monday" ).val();
		var mon_open_ampm = $( "#lcn_opening_hours_opens_at_am_pm_monday" ).val();
		var mon_close_hour = $( "#lcn_opening_hours_closes_at_hour_monday" ).val();
		var mon_close_mins = $( "#lcn_opening_hours_closes_at_minutes_monday" ).val();
		var mon_close_ampm = $( "#lcn_opening_hours_closes_at_am_pm_monday" ).val();
		var tue_open_hour = $( "#lcn_opening_hours_opens_at_hour_tuesday" ).val();
		var tue_open_mins = $( "#lcn_opening_hours_opens_at_minutes_tuesday" ).val();
		var tue_open_ampm = $( "#lcn_opening_hours_opens_at_am_pm_tuesday" ).val();
		var tue_close_hour = $( "#lcn_opening_hours_closes_at_hour_tuesday" ).val();
		var tue_close_mins = $( "#lcn_opening_hours_closes_at_minutes_tuesday" ).val();
		var tue_close_ampm = $( "#lcn_opening_hours_closes_at_am_pm_tuesday" ).val();
		var wed_open_hour = $( "#lcn_opening_hours_opens_at_hour_wednesday" ).val();
		var wed_open_mins = $( "#lcn_opening_hours_opens_at_minutes_wednesday" ).val();
		var wed_open_ampm = $( "#lcn_opening_hours_opens_at_am_pm_wednesday" ).val();
		var wed_close_hour = $( "#lcn_opening_hours_closes_at_hour_wednesday" ).val();
		var wed_close_mins = $( "#lcn_opening_hours_closes_at_minutes_wednesday" ).val();
		var wed_close_ampm = $( "#lcn_opening_hours_closes_at_am_pm_wednesday" ).val();
		var thu_open_hour = $( "#lcn_opening_hours_opens_at_hour_thursday" ).val();
		var thu_open_mins = $( "#lcn_opening_hours_opens_at_minutes_thursday" ).val();
		var thu_open_ampm = $( "#lcn_opening_hours_opens_at_am_pm_thursday" ).val();
		var thu_close_hour = $( "#lcn_opening_hours_closes_at_hour_thursday" ).val();
		var thu_close_mins = $( "#lcn_opening_hours_closes_at_minutes_thursday" ).val();
		var thu_close_ampm = $( "#lcn_opening_hours_closes_at_am_pm_thursday" ).val();
		var fri_open_hour = $( "#lcn_opening_hours_opens_at_hour_friday" ).val();
		var fri_open_mins = $( "#lcn_opening_hours_opens_at_minutes_friday" ).val();
		var fri_open_ampm = $( "#lcn_opening_hours_opens_at_am_pm_friday" ).val();
		var fri_close_hour = $( "#lcn_opening_hours_closes_at_hour_friday" ).val();
		var fri_close_mins = $( "#lcn_opening_hours_closes_at_minutes_friday" ).val();
		var fri_close_ampm = $( "#lcn_opening_hours_closes_at_am_pm_friday" ).val();
		var sat_open_hour = $( "#lcn_opening_hours_opens_at_hour_saturday" ).val();
		var sat_open_mins = $( "#lcn_opening_hours_opens_at_minutes_saturday" ).val();
		var sat_open_ampm = $( "#lcn_opening_hours_opens_at_am_pm_saturday" ).val();
		var sat_close_hour = $( "#lcn_opening_hours_closes_at_hour_saturday" ).val();
		var sat_close_mins = $( "#lcn_opening_hours_closes_at_minutes_saturday" ).val();
		var sat_close_ampm = $( "#lcn_opening_hours_closes_at_am_pm_saturday" ).val();
		var sun_open_hour = $( "#lcn_opening_hours_opens_at_hour_sunday" ).val();
		var sun_open_mins = $( "#lcn_opening_hours_opens_at_minutes_sunday" ).val();
		var sun_open_ampm = $( "#lcn_opening_hours_opens_at_am_pm_sunday" ).val();
		var sun_close_hour = $( "#lcn_opening_hours_closes_at_hour_sunday" ).val();
		var sun_close_mins = $( "#lcn_opening_hours_closes_at_minutes_sunday" ).val();
		var sun_close_ampm = $( "#lcn_opening_hours_closes_at_am_pm_sunday" ).val();		
		var data = { 'action': 'saveopeninghours', 'openfrmt': openhrformat, 'moh': mon_open_hour, 'mom': mon_open_mins, 'moap': mon_open_ampm, 'mch': mon_close_hour, 'mcm': mon_close_mins, 'mcap': mon_close_ampm, 'tuoh': tue_open_hour, 'tuom': tue_open_mins, 'tuoap': tue_open_ampm, 'tuch': tue_close_hour, 'tucm': tue_close_mins, 'tucap': tue_close_ampm, 'woh': wed_open_hour, 'wom': wed_open_mins, 'woap': wed_open_ampm, 'wch': wed_close_hour, 'wcm': wed_close_mins, 'wcap': wed_close_ampm, 'thoh': thu_open_hour, 'thom': thu_open_mins, 'thoap': thu_open_ampm, 'thch': thu_close_hour, 'thcm': thu_close_mins, 'thcap': thu_close_ampm, 'foh': fri_open_hour, 'fom': fri_open_mins, 'foap': fri_open_ampm, 'fch': fri_close_hour, 'fcm': fri_close_mins, 'fcap': fri_close_ampm, 'saoh': sat_open_hour, 'saom': sat_open_mins, 'saoap': sat_open_ampm, 'sach': sat_close_hour, 'sacm': sat_close_mins, 'sacap': sat_close_ampm, 'suoh': sun_open_hour, 'suom': sun_open_mins, 'suoap': sun_open_ampm, 'such': sun_close_hour, 'sucm': sun_close_mins, 'sucap': sun_close_ampm };
		$.post(ajaxurl, data,function(response){
			$( ".successmessagenew" ).show().delay(1000).fadeOut();
			location.reload(true);
		});
	});
});