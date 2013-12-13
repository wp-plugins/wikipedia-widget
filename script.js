jQuery(document).ready(function($) {

	var options = wikipedia_widget_script;
	var ajaxurl = options['ajaxurl'];

	$.wikipedia_call = function(search_term) {		
		if (search_term == "") return;
		var thisData = {
				action: 'wikipedia_request',				
				search: search_term,
				url: options['wikipedia_url'],
				limit: options['limit'],
			};
		$.ajax({
		    url: ajaxurl,
		    type: 'POST',
		    data: thisData,
		    beforeSend: function () {
		    	$(".wikipedia_widget-loader").show("slow");
		    },
		    success: function (response) {
		    	$(".wikipedia_widget-loader").hide();
		        if (response) {
	 				$(".wikipedia_widget-result").html( response );
	 			}
		    }
		});
	}

	$('.wikipedia_widget-search_form').submit(function () {
		return false;
	});	

	if ( $('.wikipedia_widget-default_search').length > 0) {
		$.wikipedia_call($.trim($('.wikipedia_widget-default_search').val()));
	}

	var search_fields = Array('.wikipedia_widget-search');
	var search_field_alt = options['search_field_alt'];	
	if (search_field_alt) {
		search_field_alt = search_field_alt.search(/^[^#|^.]/) != -1 ? '#' + search_field_alt : search_field_alt;
		search_fields.push( search_field_alt );
	}	
	$.each(search_fields, function(i, value) {
		var search_term = new Array(2);
		$(value).keyup(function() {				
			if ( search_term[i] != $.trim($(this).val())) {
				search_term[i] = $.trim($(this).val());
				$(search_fields[0]).val( search_term[i] );
				if ( search_term[i].length > 2 ) {					
					delay(function(){						
			    		$.wikipedia_call(search_term[i]);
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