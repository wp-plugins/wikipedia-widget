jQuery(document).ready(function () {

	//var widget_id = '#widget-wikipedia_widget-2';

	jQuery('.wikipedia_widget-search_form').submit(function () {
		return false;
	});

	//_OLD_ for mulit-widget:
	/* jQuery('.wikipedia_widget-show_on_startpage').click(function () {
		//var thisId = jQuery(this).attr('wid');
		//jQuery('#widget-wikipedia_widget-' + thisId + '-startpage_term').toggle( );
		jQuery('.wikipedia_widget-startpage_term').toggle( );
		
	}); */

	jQuery.wikipedia_call = function(data) {
		var thisUrl = jQuery('.wikipedia_widget-url').attr('value');
		var thisLimit = jQuery('.wikipedia_widget-limit').attr('value');
		var thisData = {
				action: 'ww_request',
				url: thisUrl,
				data: data,
				limit: thisLimit
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
	
	if (jQuery('.wikipedia_widget-search_now').length) {
		var data = jQuery('.wikipedia_widget-search_now').attr('value');
		jQuery.wikipedia_call(data);
	}
	
	jQuery('.wikipedia_widget-search').keyup(function() {
		var data = jQuery(this).attr('value');    
    	delay(function(){
    		jQuery.wikipedia_call(data);
    	}, 300 );
	});
	
	/* amazin delay function by Christian C. from http://stackoverflow.com/questions/1909441/jquery-keyup-delay */
	var delay = (function(){
		var timer = 0;
		return function(callback, ms){
			clearTimeout (timer);
			timer = setTimeout(callback, ms);
		};
	})();
	

});