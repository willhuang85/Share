jQuery(document).ready(function($){
	$("#publish").click(function() {
		share_publish_post(this);
	});
	
	function share_publish_post(obj) {
		var scoopit_array = new Array()

		scoopit_array.push($(".share_select_topic :selected").val());
		$(".share_sharers_list input:checked").each(function() {
			var temp = new Array()
			temp.push(this.name);
			temp.push(this.value);
			
			scoopit_array.push(temp);
		});
		
		var data = {
			action: 'share_publish_post',
			share_sharers: scoopit_array
		};
		jQuery.post(ajaxurl, data);
	}
});

