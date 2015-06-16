function load_fra_monitor_dialog (server_id, server_hostname)
{
	$.ajax({
	    type: 'POST',
	    url: 'includes/windows/fra_monitor_dialog.php',
	    data: {'hostname': server_hostname},
	    success: function(content) {
	    	$( '#dialog_fra_monitor_'+server_id ).html(content);
	    	$( '#dialog_fra_monitor_'+server_id ).dialog( 'open' );
	    }
	  });
}