$j(document).ready(function() {
    $j("#slide1").click(function() {
        $j(".vip-tabnavi li").removeClass("on");
        $j(".vip-tabnavi li").eq(3).addClass("on");

        $j("#view_tab1").css("display", "none");
        $j("#view_tab3").css("display", "block");

        if ($j("#view_tab2").length > 0) {
            $j("#view_tab2").css("display", "none");
        }

        fnMapView("#view_tab3", 90);

        $j(".con_footer").css("display", "none");
    });

    var swiper = new Swiper('.swiper-container', {
        loop: true,
        autoplay: {
            delay: 2000,
            disableOnInteraction: false,
        },
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
    });

    var topBar = $j(".vip-tabwrap").offset();

    $j(window).scroll(function() {
        var docScrollY = $j(document).scrollTop();

        //$j("#test").html(scrollBottom + '/' + bottomBar + '/' + topBar.top + '/' + $j(window).scrollTop());
        if ((docScrollY + 47) > (topBar.top + 0)) {
            $j("#tabnavi").addClass("vip-tabwrap-fixed");
            $j(".vip-tabwrap").addClass("vip-tabwrap-top");
        } else {
            $j("#tabnavi").removeClass("vip-tabwrap-fixed");
            $j(".vip-tabwrap").removeClass("vip-tabwrap-top");
        }
        if ($j('.contentimg').length > 0) {
            if (checkVisible($j('.contentimg')) && !isVisible) {
                $j(".vip-tabnavi li").removeClass("on");
                $j(".vip-tabnavi li").eq(0).addClass("on");
            }
        }

        if ($j('#shopmap').length > 0) {
            if (checkVisible($j('#shopmap')) && !isVisible) {
                $j(".vip-tabnavi li").removeClass("on");
                $j(".vip-tabnavi li").eq(1).addClass("on");
            }
        }
        if ($j('#cancelinfo').length > 0) {
            if (checkVisible($j('#cancelinfo')) && !isVisible) {
                $j(".vip-tabnavi li").removeClass("on");
                $j(".vip-tabnavi li").eq(2).addClass("on");
            }
        }
    });

    $j('#coupon').bind("keyup", function() {
        //var regexp = /[^a-z0-9]/gi;
        //$j(this).val($j(this).val().toUpperCase().replace(regexp,''));
        $j(this).val($j(this).val().toUpperCase());
    });
});

var isVisible = false;

function fnCoupon(type, gubun, coupon) {
    if (coupon == "") {
        alert("쿠폰코드를 입력하세요.")
        return 0;
    }

    var params = "type=" + type + "&gubun=" + gubun + "&coupon=" + coupon;
    var rtn = $j.ajax({
        type: "POST",
        url: "/act/coupon/coupon_load.php",
        data: params,
        success: function(data) {
            return data;
        }
    }).responseText;

    if (rtn == "yes") {
        alert("이미 사용 된 쿠폰입니다.");
        return 0;
    } else if (rtn == "no") {
        alert("사용가능한 쿠폰이 없습니다.");
        return 0;
    } else {
        return rtn;
    }
}

function checkVisible(elm, eval) {
    eval = eval || "object visible";
    var viewportHeight = $j(window).height(), // Viewport Height
        scrolltop = $j(window).scrollTop(), // Scroll Top
        y = $j(elm).offset().top,
        elementHeight = $j(elm).height();
    if (eval == "object visible") return ((y < (viewportHeight + scrolltop)) && (y > (scrolltop - elementHeight)));
    if (eval == "above") return ((y < (viewportHeight + scrolltop)));
}

function fnResViewBus(bool, objid, topCnt, obj) {
    $j(".vip-tabnavi li").removeClass("on");
    $j(obj).addClass("on");

    $j(".con_footer").css("display", "block");
    if (bool) {
        $j("#view_tab1").css("display", "block");
        $j("#view_tab2").css("display", "none");
        $j("#view_tab3").css("display", "none");
    } else {
        $j("#view_tab1").css("display", "none");

        if (objid == "#view_tab2") {
            $j("#view_tab2").css("display", "block");
            $j("#view_tab3").css("display", "none");
        } else {
            $j("#view_tab2").css("display", "none");
            $j("#view_tab3").css("display", "block");

            if (objid == "#view_tab3") {
                $j(".con_footer").css("display", "none");
            }
        }
    }

    fnMapView(objid, topCnt);
}

function fnResView(bool, objid, topCnt, obj) {
    $j(".vip-tabnavi li").removeClass("on");
    $j(obj).addClass("on");

    $j(".con_footer").css("display", "block");
    if (bool) {
        $j("#view_tab1").css("display", "block");
        $j("#view_tab3").css("display", "none");
    } else {
        $j("#view_tab1").css("display", "none");
        $j("#view_tab3").css("display", "block");

        if (objid == "#view_tab3") {
            $j(".con_footer").css("display", "none");
        }
    }

    fnMapView(objid, topCnt);
}

function fnResViewSol(bool, objid, topCnt, obj) {
    $j(".vip-tabnavi li").removeClass("on");
    $j(obj).addClass("on");

    if (bool) {

    } else {
        $j("div[tabid='viewtab']").css("display", "none");
        $j(objid).css("display", "");
    }

    fnMapView(objid, topCnt);
}

function fnMapView(objid, topCnt) {
    var divLoc = $j(objid).offset();
    $j('html, body').animate({
        scrollTop: divLoc.top - topCnt
    }, "slow");
}

//달력 월 이동
function fnCalMove(selDate, seq) {
    var nowDate = new Date();
    $j("#tour_calendar").load("/act/surf/surfview_calendar.php?selDate=" + selDate + "&seq=" + seq + "&t=" + nowDate.getTime());

    $j("#initText").css("display", "");
    $j("#lessonarea").css("display", "none");

    $j(".fixed_wrap3 li").removeClass("on3");
    $j("div[area=shopListArea]").css("display", "none");

}

// 강습/렌탈 클릭시 바인딩
function fnSurfList(obj, num) {
    $j(".fixed_wrap3 li").removeClass("on3");
    $j(obj).parent().addClass("on3");

    $j("div[area=shopListArea]").css("display", "none");
    $j("div[area=shopListArea]").eq(num).css("display", "block");

    //var lesson_price =  parseInt($j("calbox[value='" + selDate + "']").attr("lesson_price"), 10);
}

//날짜 클릭후 표시
function fnPassenger(obj) {
    var selDate = obj.attributes.value.value;
    $j("#tour_calendar calBox").css("background", "white");
    $j(obj).css("background", "#efefef");

    $j("#initText").css("display", "none");
    $j("#lessonarea").css("display", "");
    $j("#resselDate").val(selDate);
    $j("#strStayDate").val(selDate);

    if (!$j(".fixed_wrap3 li").hasClass("on3")) {
        $j(".fixed_wrap3 li:eq(0)").addClass("on3");
        $j("div[area=shopListArea]").eq($j(".fixed_wrap3 li:eq(0)").attr("id")).css("display", "block");
    }

    soldoutchk(selDate, obj);
}

function plusDate(date, count) {
    var dateArr = date.split("-");
    var changeDay = new Date(dateArr[0], (dateArr[1] - 1), dateArr[2]);

    // count만큼의 미래 날짜 계산
    changeDay.setDate(changeDay.getDate() + count);
    return dateToYYYYMMDD(changeDay);
}

function dateToYYYYMMDD(date) {
    function pad(num) {
        num = num + '';
        return num.length < 2 ? '0' + num : num;
    }
    return date.getFullYear() + '-' + pad(date.getMonth() + 1) + '-' + pad(date.getDate());
}

Date.prototype.yyyymmdd = function() {
    var yyyy = this.getFullYear().toString();
    var mm = (this.getMonth() + 1).toString();
    var dd = this.getDate().toString();
    return yyyy + "-" + (mm[1] ? mm : "0" + mm[0]) + "-" + (dd[1] ? dd : "0" + dd[0]);
}

function fnResListInit(date) {
    var lesson_price = parseInt($j("calbox[value='" + date + "']").attr("lesson_price"), 10);
    var rent_price = parseInt($j("calbox[value='" + date + "']").attr("rent_price"), 10);
    var stay_price = parseInt($j("calbox[value='" + date + "']").attr("stay_price"), 10);
    var bbq_price = parseInt($j("calbox[value='" + date + "']").attr("bbq_price"), 10);

    if ($j("#sellesson").length > 0 && $j("#sellesson option").length > 0) {
        var priceText = " (" + commify(parseInt($j("#sellesson").val().split('|')[2], 10)) + "원)";
        var infoText = $j("#sellesson option:selected").attr("opt_info");
        if (infoText == "") {
            infoText = $j("#sellesson option:selected").text();
        }
        $j("#stayText").text(infoText + (priceText));

        var opttime = $j("#sellesson option:selected").attr("opttime");
        if (opttime == "") {
            $j("#sellessonTime").html("<option value=''>-</option>");
        } else {
            var opttimeHtml = "";
            opttime.split('|').forEach(function(el) {
                if (el != "") {
                    opttimeHtml += "<option value='" + el + "'>" + el + "</option>";
                }
            });
            $j("#sellessonTime").html(opttimeHtml);
        }
    }

    if ($j("#selRent").length > 0 && $j("#selRent option").length > 0) {
        priceText = " (" + commify(parseInt($j("#selRent").val().split('|')[2], 10) + rent_price) + "원)";
        infoText = $j("#selRent option:selected").text();
        $j("#rentText").text(infoText + priceText);
    }

    if ($j("#selPkg").length > 0 && $j("#selPkg option").length > 0) {
        priceText = " (" + commify(parseInt($j("#selPkg").val().split('|')[2], 10) + stay_price) + "원)";
        infoText = $j("#selPkg option:selected").attr("opt_info");
        $j("#pkgText").text(infoText + priceText);

        var opttime = $j("#selPkg option:selected").attr("opttime");
        if (opttime == "") {
            $j("#selPkgTime").html("<option value=''>-</option>");
            $j("#selPkgTime").css("display", "none");
        } else {
            $j("#selPkgTime").css("display", "");
            var opttimeHtml = "";
            opttime.split('|').forEach(function(el) {
                if (el != "") {
                    opttimeHtml += "<option value='" + el + "'>" + el + "</option>";
                }
            });
            $j("#selPkgTime").html(opttimeHtml);
        }
    }

    if ($j("#selBBQ").length > 0 && $j("#selBBQ option").length > 0) {
        priceText = " (" + commify(parseInt($j("#selBBQ").val().split('|')[2], 10) + bbq_price) + "원)";
        infoText = $j("#selBBQ option:selected").attr("opt_info");
        $j("#bbqText").text(infoText + priceText);
    }
}

// 달력 날짜 클릭 후 매진여부 체크
function soldoutchk(date, obj) {
    $j("#sellesson").html($j("#hidsellesson").html());
    $j("#selRent").html($j("#hidselRent").html());
    $j("#selPkg").html($j("#hidselPkg").html());
    $j("#selBBQ").html($j("#hidselBBQ").html());

    fnResListInit(date);

    $j("#tbsellesson").css("display", "");
    $j("#divsellesson").css("display", "none");
    $j("#tbselRent").css("display", "");
    $j("#divselRent").css("display", "none");
    $j("#tbselPkg").css("display", "");
    $j("#divselPkg").css("display", "none");
    $j("#tbselBBQ").css("display", "");
    $j("#divselBBQ").css("display", "none");

    $j("#sellessonM").parent().css("display", "");
    $j("#sellessonW").parent().css("display", "");
    $j("#selRentM").parent().css("display", "");
    $j("#selRentW").parent().css("display", "");
    $j("#selPkgM").parent().css("display", "");
    $j("#selPkgW").parent().css("display", "");
    $j("#tbselBBQ").css("display", "");
    $j("#divselBBQ").css("display", "none");

    var nowDate = (new Date()).yyyymmdd(); //오늘 날짜
    if ($j(obj).attr("day_type") == 3) {
        var resDate = plusDate(date, -6); //숙소 예약가능한 날짜

        if ($j(obj).attr("weeknum") == 6 && nowDate > resDate) {
            $j("#sellesson option[stay_day=0]").remove();
            $j("#sellesson option[stay_day=1]").remove();
            $j("#sellesson option[stay_day=2]").remove();
        }
    }

    if (plusDate(nowDate, 1) == date || $j("calbox[value='" + plusDate(date, -1) + "']").length == 0) {
        $j("#sellesson option[stay_day=1]").remove();
        $j("#sellesson option[stay_day=2]").remove();

        $j("#selPkg option[stay_day=1]").remove();
        $j("#selPkg option[stay_day=2]").remove();
    }

    if (main[date] != null) {
        for (key in main[date]) {
            if (main[date][key].type == "lesson") {
                delObjid = "#sellesson";
            } else if (main[date][key].type == "rent") {
                delObjid = "#selRent";
            } else if (main[date][key].type == "pkg") {
                delObjid = "#selPkg";
            } else if (main[date][key].type == "bbq") {
                delObjid = "#selBBQ";
            }

            if (main[date][key].opt_sexM == "Y" && main[date][key].opt_sexW == "Y") {
                $j(delObjid + " option[soldout=" + key + "]").remove();
            } else {
                $j(delObjid + " option[soldout=" + key + "]").attr("opt_sexM", main[date][key].opt_sexM);
                $j(delObjid + " option[soldout=" + key + "]").attr("opt_sexW", main[date][key].opt_sexW);
            }
        }

        if ($j("#sellesson option").length == 0) {
            $j("#tbsellesson").css("display", "none");
            $j("#divsellesson").css("display", "");
        } else {
            fnResChange('sellesson');
        }

        if ($j("#selRent option").length == 0) {
            $j("#tbselRent").css("display", "none");
            $j("#divselRent").css("display", "");
        } else {
            fnResChange('selRent');
        }

        if ($j("#selPkg option").length == 0) {
            $j("#tbselPkg").css("display", "none");
            $j("#divselPkg").css("display", "");
        } else {
            fnResChange('selPkg');
        }

        if ($j("#selBBQ option").length == 0) {
            $j("#tbselBBQ").css("display", "none");
            $j("#divselBBQ").css("display", "");
        } else {
            fnResChange('selBBQ');
        }
    }
}

function fnResChange(key) {
    $j("#" + key + "M").val('0');
    $j("#" + key + "W").val('0');
    $j("#" + key + "M").prev().css("display", "none");
    $j("#" + key + "W").prev().css("display", "none");

    if ($j("#" + key + " option:selected").attr("opt_sexM") == "Y") {
        $j("#" + key + "M").css("display", "none");
        $j("#" + key + "M").prev().css("display", "");
    } else {
        $j("#" + key + "M").css("display", "");
        $j("#" + key + "M").prev().css("display", "none");
    }

    if ($j("#" + key + " option:selected").attr("opt_sexW") == "Y") {
        $j("#" + key + "W").css("display", "none");
        $j("#" + key + "W").prev().css("display", "");
    } else {
        $j("#" + key + "W").css("display", "");
        $j("#" + key + "W").prev().css("display", "none");
    }

    var resselDate = $j("#resselDate").val();
    if (key == "sellesson") {
        var lesson_price = parseInt($j("calbox[value='" + resselDate + "']").attr("lesson_price"), 10);
        var stay_price = parseInt($j("calbox[value='" + resselDate + "']").attr("stay_price"), 10);

        var stayPlus = $j("#sellesson option:selected").attr("stay_day");
        var stayText = "";
        if (stayPlus == 0) {
            stayText = "숙박일 : " + resselDate + "(1박)";
        } else if (stayPlus == 1) {
            stay_price = parseInt($j("calbox[value='" + plusDate(resselDate, -1) + "']").attr("stay_price"), 10);
            stayText = "숙박일 : " + plusDate(resselDate, -1) + "(1박)";
        } else if (stayPlus == 2) {
            var stay_price1 = parseInt($j("calbox[value='" + plusDate(resselDate, -1) + "']").attr("stay_price"), 10);
            var stay_price2 = parseInt($j("calbox[value='" + resselDate + "']").attr("stay_price"), 10);
            stayText = "숙박일 : " + plusDate(resselDate, -1) + "(2박)";
            stay_price = stay_price1 + stay_price2;
        } else {
            // stayText = $j("#sellesson option:selected").attr("opt_info");
            stayText = $j("#sellesson option:selected").text();
            stay_price = 0;
        }

        var priceText = " (" + commify(parseInt($j("#sellesson").val().split('|')[2], 10) + stay_price) + "원)";
        $j("#stayText").text(stayText + priceText);

        var opttime = $j("#sellesson option:selected").attr("opttime");
        if (opttime == "") {
            $j("#sellessonTime").html("<option value=''>-</option>");
        } else {
            var opttimeHtml = "";
            opttime.split('|').forEach(function(el) {
                if (el != "") {
                    opttimeHtml += "<option value='" + el + "'>" + el + "</option>";
                }
            });
            $j("#sellessonTime").html(opttimeHtml);
        }
    } else if (key == "selBBQ") {
        var bbq_price = parseInt($j("calbox[value='" + resselDate + "']").attr("bbq_price"), 10);
        var bbqText = $j("#selBBQ option:selected").attr("opt_info");
        var priceText = " (" + commify(parseInt($j("#selBBQ").val().split('|')[2], 10) + bbq_price) + "원)";

        $j("#bbqText").html(bbqText + priceText);
    } else if (key == "selPkg") {
        var stay_price = parseInt($j("calbox[value='" + resselDate + "']").attr("stay_price"), 10);
        var pkgText = $j("#selPkg option:selected").attr("opt_info");

        var stayPlus = $j("#selPkg option:selected").attr("stay_day");
        var stayText = "";
        if (stayPlus == 0) {
            // stayText = "숙박일 : " + resselDate + "(1박)";
        } else if (stayPlus == 1) {
            stay_price = parseInt($j("calbox[value='" + plusDate(resselDate, -1) + "']").attr("stay_price"), 10);
            // stayText = "숙박일 : " + plusDate(resselDate, -1) + "(1박)";
        } else if (stayPlus == 2) {
            var stay_price1 = parseInt($j("calbox[value='" + plusDate(resselDate, -1) + "']").attr("stay_price"), 10);
            var stay_price2 = parseInt($j("calbox[value='" + resselDate + "']").attr("stay_price"), 10);
            // stayText = "숙박일 : " + plusDate(resselDate, -1) + "(2박)";
            stay_price = stay_price1 + stay_price2;
        } else {
            // stayText = $j("#sellesson option:selected").attr("opt_info");
            // stayText = $j("#sellesson option:selected").text();
            stay_price = 0;
        }

        var priceText = " (" + commify(parseInt($j("#selPkg").val().split('|')[2], 10) + stay_price) + "원)";
        $j("#pkgText").html(pkgText + priceText);

        var opttime = $j("#selPkg option:selected").attr("opttime");
        if (opttime == "") {
            $j("#selPkgTime").html("<option value=''>-</option>");
            $j("#selPkgTime").css("display", "none");
        } else {
            $j("#selPkgTime").css("display", "");
            var opttimeHtml = "";
            opttime.split('|').forEach(function(el) {
                if (el != "") {
                    opttimeHtml += "<option value='" + el + "'>" + el + "</option>";
                }
            });
            $j("#selPkgTime").html(opttimeHtml);
        }
    } else if (key == "selRent") {
        var rent_price = parseInt($j("calbox[value='" + resselDate + "']").attr("rent_price"), 10);
        //var rentText = $j("#selRent option:selected").attr("opt_info");
        var rentText = $j("#selRent option:selected").text();
        var priceText = " (" + commify(parseInt($j("#selRent").val().split('|')[2], 10) + rent_price) + "원)";

        $j("#rentText").html(rentText + priceText);
    }
}

//서핑샵 예약
function fnSurfSave() {
    var chkVlu = $j("input[id=resSeq]").map(function() { return $j(this).val(); }).get();
    if (chkVlu == "") {
        alert("예약 내역이 없습니다.\n\n날짜 선택 후 진행해주세요.");
        return;
    }
    $j("#resNumAll").val(chkVlu);

    if ($j("#userName").val() == "") {
        alert("이름을 입력하세요.");
        return;
    }

    if ($j("#userPhone1").val() == "" || $j("#userPhone2").val() == "" || $j("#userPhone3").val() == "") {
        alert("연락처를 입력하세요.");
        return;
    }

    if (!$j("#chk8").is(':checked')) {
        alert("이용안내 및 취소/환불 규정에 대한 동의를 해주세요.");
        return;
    }

    if (!$j("#chk9").is(':checked')) {
        alert("개인정보 취급방침에 동의를 해주세요.");
        return;
    }

    if (!confirm("신청하신 항목으로 예약하시겠습니까?")) {
        return;
    }

    $j('#divConfirm').block({ message: "신청하신 예약건 진행 중입니다." });

    setTimeout('$j("#frmRes").attr("action", "/act/surf/surf_save.php").submit();', 500);
    //$j("#frmRes").attr("action", "/act/surf/surf_save.php").submit();
}

// 서핑옵션 신청버튼
function fnSurfAdd(num, obj) {
    var selDate = $j("#resselDate").val();

    if (num == "lesson") {
        if ($j("#sellessonM").val() == 0 && $j("#sellessonW").val() == 0) {
            alert("예약 인원을 선택해주세요.");
            return;
        }

        gubun = $j("#sellesson").val();
        mNum = $j("#sellessonM").val();
        wNum = $j("#sellessonW").val();
    } else if (num == "rent") {
        if ($j("#selRentM").val() == 0 && $j("#selRentW").val() == 0) {
            alert("예약 인원을 선택해주세요.");
            return;
        }

        gubun = $j("#selRent").val();
        mNum = $j("#selRentM").val();
        wNum = $j("#selRentW").val();
    } else if (num == "pkg") {
        if ($j("#selPkgM").val() == 0 && $j("#selPkgW").val() == 0) {
            alert("예약 인원을 선택해주세요.");
            return;
        }

        gubun = $j("#selPkg").val();
        mNum = $j("#selPkgM").val();
        wNum = $j("#selPkgW").val();
    } else if (num == "bbq") {
        if ($j("#selBBQ").val() == '') {
            alert("바베큐 이용날짜를 선택해주세요.");
            return;
        }

        if ($j("#selBBQM").val() == 0 && $j("#selBBQW").val() == 0) {
            alert("예약 인원을 선택해주세요.");
            return;
        }

        gubun = $j("#selBBQ").val();
        mNum = $j("#selBBQM").val();
        wNum = $j("#selBBQW").val();
    }

    fnSurfAppend(num, obj, selDate, gubun);
}

function fnSurfAppend(num, obj, selDate, gubun) {
    $j("#frmRes").css("display", "");


    var selSeq = gubun.split('|')[0];
    var selName = gubun.split('|')[1];
    var selPrice = parseInt(gubun.split('|')[2], 10);
    var selTime = "",
        selDay = "";

    if (num == "lesson") { //서핑강습
        var lesson_price = parseInt($j("calbox[value='" + selDate + "']").attr("lesson_price"), 10);
        var stay_price = parseInt($j("calbox[value='" + selDate + "']").attr("stay_price"), 10);
        addSurfType = selDate + " / " + $j("#sellessonTime").val();

        selM = $j("#sellessonM").val();
        selW = $j("#sellessonW").val();
        selTime = $j("#sellessonTime").val();

        var stayPlus = $j("#sellesson option:selected").attr("stay_day");
        if (stayPlus == 0) {
            var opt_info = "숙박일 : " + selDate + "(1박)";
            selPrice = selPrice + stay_price;
        } else if (stayPlus == 1) {
            stay_price = parseInt($j("calbox[value='" + plusDate(selDate, -1) + "']").attr("stay_price"), 10);
            var opt_info = "숙박일 : " + plusDate(selDate, -1) + "(1박)";
            selPrice = selPrice + stay_price;
        } else if (stayPlus == 2) {
            var stay_price1 = parseInt($j("calbox[value='" + plusDate(selDate, -1) + "']").attr("stay_price"), 10);
            var stay_price2 = parseInt($j("calbox[value='" + selDate + "']").attr("stay_price"), 10);
            var opt_info = "숙박일 : " + plusDate(selDate, -1) + "(2박)";
            selPrice = selPrice + stay_price1 + stay_price2;;
        } else {
            var opt_info = $j("#sellesson option:selected").attr("opt_info");
            selPrice = selPrice + lesson_price;
        }
    } else if (num == "rent") { //렌탈
        var rent_price = parseInt($j("calbox[value='" + selDate + "']").attr("rent_price"), 10);
        var opt_info = "";
        addSurfType = selDate;

        selM = $j("#selRentM").val();
        selW = $j("#selRentW").val();

        selPrice = selPrice + rent_price;
    } else if (num == "pkg") { //패키지
        var pkg_price = parseInt($j("calbox[value='" + selDate + "']").attr("pkg_price"), 10);
        var stay_price = parseInt($j("calbox[value='" + selDate + "']").attr("stay_price"), 10);

        if ($j("#selPkgTime").val() == "") {
            addSurfType = selDate;
        } else {
            addSurfType = selDate + " / " + $j("#selPkgTime").val();
        }

        selM = $j("#selPkgM").val();
        selW = $j("#selPkgW").val();
        selTime = $j("#selPkgTime").val();

        var stayPlus = $j("#selPkg option:selected").attr("stay_day");
        if (stayPlus == 0) {
            // var opt_info = "숙박일 : " + selDate + "(1박)";
            selPrice = selPrice + stay_price;
        } else if (stayPlus == 1) {
            stay_price = parseInt($j("calbox[value='" + plusDate(selDate, -1) + "']").attr("stay_price"), 10);
            // var opt_info = "숙박일 : " + plusDate(selDate, -1) + "(1박)";
            selPrice = selPrice + stay_price;
        } else if (stayPlus == 2) {
            var stay_price1 = parseInt($j("calbox[value='" + plusDate(selDate, -1) + "']").attr("stay_price"), 10);
            var stay_price2 = parseInt($j("calbox[value='" + selDate + "']").attr("stay_price"), 10);
            // var opt_info = "숙박일 : " + plusDate(selDate, -1) + "(2박)";
            selPrice = selPrice + stay_price1 + stay_price2;;
        } else {
            // var opt_info = "";
            selPrice = selPrice;
        }
        var opt_info = $j("#selPkg option:selected").attr("opt_info");

    } else if (num == "bbq") { //바베큐
        var bbq_price = parseInt($j("calbox[value='" + selDate + "']").attr("bbq_price"), 10);
        var opt_info = $j("#selBBQ option:selected").attr("opt_info");
        addSurfType = selDate;

        selM = $j("#selBBQM").val();
        selW = $j("#selBBQW").val();

        selPrice = selPrice + bbq_price;
    }
    selPrice = (selPrice * selM) + (selPrice * selW);

    var addText_mem = "";
    if (selM > 0) {
        addText_mem += "남" + selM + "명";
    }

    if (selM > 0 && selW > 0) {
        addText_mem += ",";
    }

    if (selW > 0) {
        addText_mem += "여" + selW + "명";
    }

    var addText = "";
    addText = '<tr>' +
        '	<td>' +
        "		<input type='hidden' id='selPriceAdd' name='selPriceAdd[]' value='" + selPrice + "' >" +
        "		<input type='hidden' id='resSeq' name='resSeq[]' value='" + selSeq + "' >" +
        "		<input type='hidden' id='resDate' name='resDate[]' value='" + selDate + "' >" +
        "		<input type='hidden' id='resTime' name='resTime[]' value='" + selTime + "' >" +
        "		<input type='hidden' id='resDay' name='resDay[]' value='" + selDay + "' >" +
        "		<input type='hidden' id='resGubun' name='resGubun[]' value='" + num + "' >" +
        "		<input type='hidden' id='resM' name='resM[]' value='" + selM + "' >" +
        "		<input type='hidden' id='resW' name='resW[]' value='" + selW + "' >" +
        '		<strong>' + selName + '</strong>' +
        '		<span class="resoption">예약일 : ' + addSurfType + ' (' + addText_mem + ')</span>' +
        '		<span class="resoption">' + opt_info + '</span>' +
        '	</td>' +
        '	<td style="text-align:right;">' + commify(selPrice) + '원</td>' +
        '	<td style="text-align:center;cursor: pointer;" onclick="fnSurfShopDel(this, \'' + num + '\');"><img src="/act/images/button/close.png" style="width:18px;vertical-align:middle;"></td>' +
        '</tr>';

    $j("#surfAdd").append(addText);

    $j("#frmResList")[0].reset();

    // $j("#strStayDate").val(selDate);
    // $j("#strBBQDate").val(selDate);
    fnResListInit(selDate);
    fnTotalPrice();

    $j("ul.tabs li").eq(2).click();

    //fnResView(true, '#reslist', 30);
}

function fnTotalPrice() {
    var sum = 0;
    $j("input[id='selPriceAdd']").each(function() {
        sum += parseInt($j(this).val(), 10);
    });

    $j("#lastcouponprice").html("");
    if ($j("#couponcode").val() == "" || $j("#couponprice").val() == 0) {
        $j("#lastPrice").text(commify(sum) + '원');
    } else {
        var cp = $j("#couponprice").val();
        if (cp <= 100) { //퍼센트 할인			
            cp = (1 - (cp / 100));
            $j("#lastPrice").html(commify(sum * cp) + "원");
            $j("#lastcouponprice").html(" (" + commify(sum) + "원 - 할인쿠폰:" + commify(sum - (sum * cp)) + "원)");
        } else { //금액할인
            $j("#lastPrice").html(commify(sum - cp) + "원");
            $j("#lastcouponprice").html(" (" + commify(sum) + "원 - 할인쿠폰:" + commify(cp) + "원)");
        }
    }


}

//선택 삭제
function fnSurfShopDel(obj, num) {
    if (confirm("해당 항목을 삭제하시겠습니까?")) {
        if (num == 2) {
            // $j(obj).parents('tr').next().remove();
        }

        $j(obj).parents('tr').remove();

        fnTotalPrice();
    }
}

function fnStaySearch(resseq) {
    var params = "resparam=solstay&resseq=" + resseq;
    $j.ajax({
        type: "POST",
        url: "/act/admin/sol/res_sollist_info.php",
        data: params,
        success: function(data) {
            if (data == 1) {
                alert("오후 2시 이후에 객실 조회가 가능합니다.");
            } else if (data == 2) {
                alert("예약하신 이용 당일에 조회가 가능합니다.");
            } else {
                var rtnVlu = "";
                for (let i = 0; i < data.length; i++) {
                    $j("tr[trid='stay']").remove();
                    var arrData = data[i].split("|");

                    rtnVlu += "<tr trid='stay'><td>" + arrData[0] + "</td><td>" + arrData[1] + " ~ <br>" + arrData[2] + "</td><td>" + arrData[3] + "호</td><td>" + arrData[4] + "번 침대</td><td>" + arrData[5] + "</td></tr>";
                }

                $j("#tbStay").append(rtnVlu);
                alert("객실조회가 완료되었습니다.\n\n호실,침대번호,도어락 비밀번호를 확인 후 입실해주세요~");
                $j(".SolLayer").css("display", "none");
            }
        }
    });
}