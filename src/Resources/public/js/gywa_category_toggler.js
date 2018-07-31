$(document).ready(function() {
    window.category = window.location.hash.substr(1);
    history.replaceState("", document.title, window.location.pathname + window.location.search);

	function updateActive() {
		$("#category-toggler a").each(function() {
            const href = $(this).attr("href");
            const ownCategory = (href.indexOf("#") != -1 ? href.substring(href.indexOf("#") + 1) : "all");
			
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

        const url = $(this).attr("href");

        const clicked_category = (url.indexOf("#") != -1 ? url.substring(url.indexOf("#") + 1) : "all");

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