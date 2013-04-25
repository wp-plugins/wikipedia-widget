jQuery(document).ready(function($) {

	var options = wikipedia_widget_script;
	var ajaxurl = options['ajaxurl'];

	jQuery.wikipedia_call = function(search_term) {
		var thisData = {
				action: 'wikipedia_request',				
				search: search_term,
				url: options['wikipedia_url'],
				limit: options['limit'],
			};
		jQuery.ajax({
		    url: ajaxurl,
		    type: 'POST',
		    data: thisData,
		    beforeSend: function () {
		    	jQuery(".wikipedia_widget-loader").css('display', 'inline');
		    },
		    success: function (response) {
		    	jQuery(".wikipedia_widget-loader").css('display', 'none');
		        if (response) {
	 				jQuery(".wikipedia_widget-result").html( response );
	 			}
		    }
		});
	}

	jQuery('.wikipedia_widget-search_form').submit(function () {
		return false;
	});	

	if ( jQuery('.wikipedia_widget-default_search').length > 0) {
		jQuery.wikipedia_call(jQuery.trim(jQuery('.wikipedia_widget-default_search').val()));
	}

	var search_fields = Array('.wikipedia_widget-search');
	var search_field_alt = options['search_field_alt'];	
	if (search_field_alt) {
		search_field_alt = search_field_alt.search(/^[^#|^.]/) != -1 ? '#' + search_field_alt : search_field_alt;
		search_fields.push( search_field_alt );
	}	
	jQuery.each(search_fields, function(i, value) {
		var search_term = Array();
		jQuery(value).keyup(function() {
			if ( search_term[i] != jQuery.trim(jQuery(value).val()) ) {				
				search_term[i] = jQuery.trim(jQuery(value).val());
				jQuery(search_fields[0]).val( search_term[i] );
				if ( search_term[i].length > 2 ) {
					delay(function(){
			    		jQuery.wikipedia_call(search_term[i]);
			    	}, 500 );
				}
			}
		});
	});
	
	/* delay function by Christian C. from http://stackoverflow.com/questions/1909441/jquery-keyup-delay */
	var delay = (function(){
		var timer = 0;
		return function(callback, ms){
			clearTimeout (timer);
			timer = setTimeout(callback, ms);
		};
	})();
	

});