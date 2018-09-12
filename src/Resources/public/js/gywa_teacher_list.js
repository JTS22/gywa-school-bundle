function changeColor(color, amount) {
    const split = color.split(", ");

    let r = parseInt(split[0].substr(4)) + amount;
	if(r > 255) r = 255;
	else if(r < 0) r = 0;

    let g = parseInt(split[1]) + amount;
	if(g > 255) g = 255;
	else if(g < 0) g = 0;

    let b = parseInt(split[2].substr(0, split[2].length - 1)) + amount;
	if(b > 255) b = 255;
	else if(b < 0) b = 0;
	
	return "rgb(" + r + ", " + g + ", " + b + ")";
}

$(document).ready(function() {	
	// add click listener to expand tiles on click
	$(".tile-layout.folding-details ul li").click(function(e) {
		if($(e.target).prop("tagName") != "A") $(this).toggleClass("expanded"); // don't fold when link is clicked

        const el = $(this).find(".details a");
        const href = el.attr("href");
		
		if(href.indexOf("@") === -1) { // email address has not yet been substituted
			el.append(href + "@");
			el.attr("href", "mailto:" + href + "@gy-waldstrasse.de");
			
			if(href.length < 13) el.addClass("collapse-extension");
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