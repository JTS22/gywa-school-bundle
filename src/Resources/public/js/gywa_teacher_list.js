function changeColor(color, amount) {
	const split = color.split(", ");

	let r = parseInt(split[0].substr(4)) + amount;
	if(r > 255)
		r = 255;
	else if(r < 0)
		r = 0;

	let g = parseInt(split[1]) + amount;
	if(g > 255)
		g = 255;
	else if(g < 0)
		g = 0;

	let b = parseInt(split[2].substr(0, split[2].length - 1)) + amount;
	if(b > 255)
		b = 255;
	else if(b < 0)
		b = 0;
	
	return "rgb(" + r + ", " + g + ", " + b + ")";
}

$(document).ready(function() {	
	// add click listener to expand tiles on click and complete the email address (incomplete to prevent robots from grabbing them)
	$(".tile-layout.folding-details ul li").click(function(e) {
		// don't fold when link is clicked
		if($(e.target).prop("tagName") != "A")
			$(this).toggleClass("expanded");

		let el = $(this).find(".details a");
		let email_address = el.data("email-address"); // email address is previously inserted into data-email-address attribute in the template
		
		if(email_address !== "") { // email address has not yet been substituted
			el.append(email_address + "@");
			el.attr("href", "mailto:" + email_address + "@gy-waldstrasse.de");
			
			if(email_address.length < 13)
				el.addClass("collapse-extension");

			el.data("email-address", ""); // clear data attribute
		}
	});
	
	// darken background color of tags on hover
	$(".tile-layout.folding-details .subjects a").each(function(i, el) {
		const bg_color = $(this).css("background-color");
		
		$(this).hover(
			function() {
				$(this).css("background-color", changeColor(bg_color, -25));
			},
			function() {
				$(this).css("background-color", bg_color);
			}
		);
	});
});