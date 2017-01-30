jQuery(document).ready(function($) {
	
	// Set up empty array
	var imagelyImages = [];
	
	// Add each image from Bacstretch object to array
	$.each( imagelyBackstretchImages, function( key, value ) {
		imagelyImages.push(value);
	});

	// Pass the image array to Backstretch
	$(imagelyBackstretchDiv).backstretch( imagelyImages ,{duration:3000,fade:750});

});