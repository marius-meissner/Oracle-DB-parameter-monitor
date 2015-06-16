/////////////////////////////////////////////////////////////////////////////////////
// Here we search throuch all parameters, only matching parameter will be displayed
// - Multible words are possible
// - Search for customer name or telephone nr
//---------------------------------------------------------------------------------
// search_string 	string which user provided
/////////////////////////////////////////////////////////////////////////////////////
function dialog_search_parameter (search_string)
{
	// Case of Chars should not count
	search_string 	= search_string.toLowerCase();
	// Splitting words of the string
	search_words 		= search_string.split(" ");
	
	// Going through all customers
	for (var key in parameter_search_objs) {
    	element			 =  document.getElementById("ora_parameter_item_" + key);
    	found_all_words  = 1
    	
    	// Test if all words are matching
	    $.each(search_words, function(index, word) {
	        if (word != "") {
	            if (parameter_search_objs[key].info_string.indexOf(word) == -1) {
	            	found_all_words = 0;
	            }
	        }
	    });
	    
    	// Hide customer if one of the words did not match
    	if (found_all_words == 1)
    	{
        	element.style.display = "block";
        } 
    	else 
    	{
        	element.style.display = "none";
        }
	    
	}
}

////// Adding parameter widget to database details //////////////////////////////////
// AJAX rquest with database and parameter ID is send to PHP
// PHP will generate widget content, this content will be
// then added to our parameter_widgets container
// Dialog Box will closed when everything is finished
/////////////////////////////////////////////////////////////////////////////////////
function load_parameter_widget (db_id, parameter_id, parameter_type)
{
	$.ajax({
	    type: 'POST',
	    url: 'includes/windows/parameter_widget.php',
	    data: {'parameter_id': parameter_id, 'instance_id': db_id},
	    success: function(content) {
	    	$( '#add_parameter_dialog' ).dialog( 'close' );
			$( '#parameter_widgets_'+db_id ).append(content);
	    }
	  });
	
	load_parameter_chart (db_id, parameter_id, "14_days", parameter_type);
}

function load_parameter_chart (db_id, parameter_id, type, parameter_type)
{
	if (parameter_type == 1) {boolean_type = true;} else {boolean_type = false;}
	
	switch (type) {
    case '24_hours':
    	max_time = 24;
    	step = 2;
        break;
    case '14_days':
    	max_time = 13 * 24;
    	step = 1 * 24;
        break;
    case '30_days':
    	max_time = 29 * 24;
    	step = 2 * 24;
        break;
    case '90_days':
    	max_time = 89 * 24;
    	step = 6 * 24;
        break;
    case '365_days':
    	max_time = 359 * 24;
    	step = 30 * 24;
        break;
	}
	
	
	$.ajax({
	    type: 'POST',
	    url: 'includes/windows/get_chart_data.php',
	    data: {'parameter_id': parameter_id, 'instance_id': db_id, 'max_time': max_time, 'step': step},
	    success: function(content) {
	    	data = $.parseJSON(content);
	    	draw_chart (parameter_id, data['label'], data['data'], boolean_type);
	    }
	  });
}

function load_select_parameter_dialog (instance_id)
{
	$.ajax({
	    type: 'POST',
	    url: 'includes/windows/parameter_select_dialog.php',
	    data: {'instance_id': instance_id},
	    success: function(content) {
	    	$( '#add_parameter_dialog' ).html(content);
	    	$( '#add_parameter_dialog' ).dialog( 'open' );
	    }
	  });
}

function save_parameter_change_notice (value_id, key)
{
	var dba_notice = $('#dba_notice_' +  key).val();
	var publish_range = 'none'
	
	
	if ($('#publish_range_dg_' + key).prop('checked'))
	{
		publish_range = 'dataguard';
	}
	if ($('#publish_range_server_' + key).prop('checked'))
	{
		publish_range = 'server';
	}
	if ($('#publish_range_global_' + key).prop('checked'))
	{
		publish_range = 'global';
	}

	
	$.ajax({
	    type: 'POST',
	    url: 'includes/windows/save_parameter_change_notice.php',
	    data: {'value_id': value_id, 'publish_range': publish_range, 'dba_notice': dba_notice},
	    success: function(content) {
	    	$('#save_failed_' +  key).transition({ display: 'none', delay: 0, duration: 10 });
	    	$('#save_successfully_' +  key).transition({ opacity: 0, display: 'block', delay: 0, duration: 10 });
	    	$('#save_successfully_' +  key).transition({ opacity: 100, delay: 0 });
	    },
	    error: function (xhr, ajaxOptions, thrownError) {
	    	$('#save_successfully_' +  key).transition({ display: 'none', delay: 0, duration: 10 });
	    	$('#save_failed_' +  key).transition({ opacity: 0, display: 'block', delay: 0, duration: 10 });
	    	$('#save_failed_' +  key).transition({ opacity: 100, delay: 0 });
	    }
	  });
}