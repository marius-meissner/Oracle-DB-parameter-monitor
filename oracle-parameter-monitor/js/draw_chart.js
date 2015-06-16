function draw_chart (parameter_id, labels, data_values, boolean_value)
{
    var ctx = $("#chart_" + parameter_id).get(0).getContext("2d");
    
    var data = {
    	    labels: labels,
    	    datasets: [
    	        {
    	            label: "dataset for 14 days",
    	            fillColor: "rgba(151,187,205,0.2)",
    	            strokeColor: "rgba(151,187,205,1)",
    	            pointColor: "rgba(151,187,205,1)",
    	            pointStrokeColor: "#fff",
    	            pointHighlightFill: "#fff",
    	            pointHighlightStroke: "rgba(151,187,205,1)",
    	            data: data_values
    	        }
    	    ]
    	};
    
    if (boolean_value == true)
    	{
    	var options = {
        	scaleOverride: true,
        	// Number - The number of steps in a hard coded scale
            scaleSteps: 2,
            // Number - The value jump in the hard coded scale
            scaleStepWidth: 1,
            // Number - The scale starting value
            scaleStartValue: -1,
        	scaleIntegersOnly: true,
        	scaleFontSize: 12
        	};
    	}
    else
    	{var options = {scaleFontSize: 12};}
    
    var myLineChart = new Chart(ctx).Line(data, options);
}

