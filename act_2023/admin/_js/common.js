$j(function() {
    $j("ul.tabs li").not("#click").click(function() {
        $j("ul.tabs li").not("#click").removeClass("active").css("color", "#333");
        $j(this).addClass("active").css("color", "darkred");
        $j("div[class=tab_content]").css('display', 'none');
        var activeTab = $j(this).attr("rel");

        $j("#" + activeTab).css('display', 'block');
    });
});

function fnBlockClose() {
    $j.unblockUI();
}