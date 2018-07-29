$(document).ready(function() {
    window.category = window.location.hash.substr(1);
    history.replaceState("", document.title, window.location.pathname + window.location.search);

	function updateActive() {
		$("#category-toggler a").each(function() {
			var href = $(this).attr("href");
			var ownCategory = (href.indexOf("#") != -1 ? href.substring(href.indexOf("#") + 1) : "all");
			
			if(ownCategory == window.category) $(this).addClass("active");
			else $(this).removeClass("active");
		});
	}
	
    if(window.category && $('#' + window.category).length) {
        $(".category").not($("#" + category)).hide();

        updateActive();
    }

    $("#category-toggler a").click(function(e) {
        e.preventDefault();

        var url = $(this).attr("href");

        var clicked_category = (url.indexOf("#") != -1 ? url.substring(url.indexOf("#") + 1) : "all");

        if(clicked_category != window.category) {
            window.category = clicked_category;
			
			updateActive();

            $(".category:visible").fadeOut(function() {
                if(window.category == "all") $(".category").fadeIn();
                else $("#" + category).fadeIn();
            });
        }
    });
});