	/* Nofication Close Buttons */
	$('.notification a.close').click(function(e){
		e.preventDefault();

		$(this).parent('.notification').fadeOut();
	});
