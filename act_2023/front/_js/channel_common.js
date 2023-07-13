$j.ajaxSetup({
	async: false
});

var isVisible = false;
jQuery(function() {
    var date = (new Date()).yyyymmdd(); //오늘 날짜

    jQuery('input[cal=date]').datepicker({
        // minDate : plusDate(date, -1)
    });

    jQuery('input[cal=sdate]').datepicker({
        minDate: new Date("2020-04-01"),
        beforeShow: function(date) {
            var calObj = jQuery(this).parents("tr").find("[cal=edate]");
            if(calObj.val() == ""){

            }else{
                var date = calObj.datepicker('getDate');

                date.setDate(date.getDate()); // Add 7 days
                jQuery(this).datepicker("option", "maxDate", date);
            }
        },
        onClose: function(selectedDate) {
            // 시작일(fromDate) datepicker가 닫힐때
            // 종료일(toDate)의 선택할수있는 최소 날짜(minDate)를 선택한 시작일로 지정 
            var calObj = jQuery(this).parents("tr").find("[cal=edate]");
            if(jQuery(this).val() == ""){

            }else{
                var date = jQuery(this).datepicker('getDate');

                date.setDate(date.getDate()); // Add 7 days
                jQuery(this).next().datepicker("option", "minDate", date);

                //버스 구분
                if($j("#busgubun").length > 0){
                    if($j("#busgubun").val() == 1){ //1박 왕복
                        calObj.val(plusDate(date.yyyymmdd(), 1));
                    }else if($j("#busgubun").val() == 2){ //당일 왕복
                        calObj.val(plusDate(date.yyyymmdd(), 0));
                    }
                }
            }
            
        }
    });


    jQuery('input[cal=edate]').datepicker({
        minDate: new Date("2020-05-01"),
        beforeShow: function(date) {
            if(jQuery(this).parents("tr").find("[cal=sdate]").val() == ""){

            }else{
                var date = jQuery(this).parents("tr").find("[cal=sdate]").datepicker('getDate');

                date.setDate(date.getDate()); // Add 7 days
                jQuery(this).datepicker("option", "minDate", date);
            }
        },
        onClose: function(selectedDate) {

            // 시작일(fromDate) datepicker가 닫힐때
            // 종료일(toDate)의 선택할수있는 최소 날짜(minDate)를 선택한 시작일로 지정 
            if(jQuery(this).val() == ""){

            }else{
                var date = jQuery(this).datepicker('getDate');

                date.setDate(date.getDate()); // Add 7 days
                jQuery(this).prev().datepicker("option", "maxDate", date);
            }
        }
    });

    var topBar = $j(".vip-tabwrap").offset();
    $j(window).scroll(function() {
        var docScrollY = $j(document).scrollTop();

        if(topBar != null){
            if ((docScrollY + 47) > (topBar.top + 0)) {
                $j("#tabnavi").addClass("vip-tabwrap-fixed");
                $j(".vip-tabwrap").addClass("vip-tabwrap-top");
            } else {
                $j("#tabnavi").removeClass("vip-tabwrap-fixed");
                $j(".vip-tabwrap").removeClass("vip-tabwrap-top");
            }
        }
        // if ($j('.contentimg').length > 0) {
        //     if (checkVisible($j('.contentimg')) && !isVisible) {
        //         $j(".vip-tabnavi li").removeClass("on");
        //         $j(".vip-tabnavi li").eq(0).addClass("on");
        //     }
        // }

        // if ($j('#shopmap').length > 0) {
        //     if (checkVisible($j('#shopmap')) && !isVisible) {
        //         $j(".vip-tabnavi li").removeClass("on");
        //         $j(".vip-tabnavi li").eq(1).addClass("on");
        //     }
        // }
        // if ($j('#cancelinfo').length > 0) {
        //     if (checkVisible($j('#cancelinfo')) && !isVisible) {
        //         $j(".vip-tabnavi li").removeClass("on");
        //         $j(".vip-tabnavi li").eq(2).addClass("on");
        //     }
        // }
    });

    $j('#coupon').bind("keyup", function() {
        //var regexp = /[^a-z0-9]/gi;
        //$j(this).val($j(this).val().toUpperCase().replace(regexp,''));
        $j(this).val($j(this).val().toUpperCase());
    });
});

function checkVisible(elm, eval) {
    eval = eval || "object visible";
    var viewportHeight = $j(window).height(), // Viewport Height
        scrolltop = $j(window).scrollTop(), // Scroll Top
        y = $j(elm).offset().top,
        elementHeight = $j(elm).height();
    if (eval == "object visible") return ((y < (viewportHeight + scrolltop)) && (y > (scrolltop - elementHeight)));
    if (eval == "above") return ((y < (viewportHeight + scrolltop)));
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

//form input 공백제거
function fnFormTrim(formData) {
    for (let i = 0; i < $j(formData + " input[type=text]").length; i++) {
        $j(formData + " input[type=text]").eq(i).val($j.trim($j(formData + " input[type=text]").eq(i).val()));
    }
}

// 천단위마다 쉼표 넣기
function commify(n) {
    var reg = /(^[+-]?\d+)(\d{3})/; // 정규식
    n += ''; // 숫자를 문자열로 변환

    while (reg.test(n)) {
        n = n.replace(reg, '$1' + ',' + '$2');
    }

    return n;
}

//스크롤 이동
function fnMapView(objid, topCnt) {
    var divLoc = $j(objid).offset();
    $j('html, body').animate({
        scrollTop: divLoc.top - topCnt
    }, "slow");
}

//쿠폰 조회
function fnCoupon(type, gubun, coupon) {
    if (coupon == "") {
        alert("쿠폰코드를 입력하세요.")
        return 0;
    }

    var params = "type=" + type + "&gubun=" + gubun + "&coupon=" + coupon;
    var rtn = $j.ajax({
        type: "POST",
        url: "/act_2023/front/coupon/coupon_load.php",
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

function fnRtnText(data, type){
	if(data == "0"){
		alert("정상적으로 처리되었습니다.");
		return true;
	}else{
		alert("처리 중 에러가 발생하였습니다.\n\n관리자에게 문의하세요.");	   
		return false;
	}
 }