function show_prod_db_details (container_id)
{
	var spoiler = document.getElementById('details_container_' + container_id);
	var spoiler_table = document.getElementById('schemas_' + container_id);
	spoiler.disabled = true;
	
	if (spoiler.style.display == 'none')
	{
		// If there any open containers, we will close them before opening the new one
		$('.prod_db_details').transition({ display: 'none', delay: 0, duration: 0 });
		
		// This max_height will be reset first and then slowly increased
		$('#details_container_' + container_id).transition({ 'max-height': '0px', opacity: '0', delay: 0, duration: 0 }); $('#details_container_' + container_id).transition({ display: 'block',  delay: 0, duration: 0}); 
		$('#details_container_' + container_id).transition({ 'max-height': '2000px', opacity: '1', delay: 0, duration: 1000 });
		$('#arrow_img_' + container_id).transition({ rotate: '180deg' });
	}
	else
	{
		// We will to the same, but the other way arround
		$('#details_container_' + container_id).transition({ 'max-height': '0px', opacity: '0', delay: 0 }); $('#details_container_' + container_id).transition({ display: 'none', delay: 0, duration: 0}); 
		$('#arrow_img_' + container_id).transition({ rotate: '360deg' });
	}
}


/////////////////////////////////////////////////////////////////////////////////////
//Here we search throuch all instances, only matching parameter will be displayed
//- Multible words are possible
//- Search for customer name or telephone nr
//---------------------------------------------------------------------------------
//search_string 	string which user provided
/////////////////////////////////////////////////////////////////////////////////////
function instant_search_instances (search_string)
{
	// Case of Chars should not count
	search_string 	= search_string.toLowerCase();
	// Splitting words of the string
	search_words 		= search_string.split(" ");
	
	// Going through all customers
	for (var key in instance_search_objs) {
 	element			 =  document.getElementById("prod-db-item_" + key);
 	element2			=  document.getElementById("details_container_" + key);
 	found_all_words  = 1
 	
 	// Test if all words are matching
	    $.each(search_words, function(index, word) {
	        if (word != "") {
	            if (instance_search_objs[key].info_string.indexOf(word) == -1) {
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
     	if (element2 != null)
     	{
     		element2.style.display = "none";
     	}
     }
	    
	}
}
