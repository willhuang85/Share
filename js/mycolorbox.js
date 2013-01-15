jQuery(document).ready(function($){
	$(".scoopit_iframe").colorbox({iframe:true, width:"80%", height:"80%"});
	
	$(".scoopit_iframe").colorbox({
		onClosedSuccess:function(){
			share_ajaxforsuccess(this);
		},
		onClosedFail:function(){
			share_ajaxforfail(this);
		}
	});
	

	function share_ajaxforsuccess(obj) {
		var data = {
			action: 'share_store_access_token'
		};
		jQuery.post(ajaxurl, data, function(response) {
			$(".scoopit_iframe").hide();
		});
	}
		
	function share_ajaxforfail(obj) {
		var data = {
			action: 'share_access_token_failed'
		};
		jQuery.post(ajaxurl, data);
	}
});

