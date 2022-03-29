jQuery(function() {
    var date = (new Date()).yyyymmdd(); //오늘 날짜

    jQuery('input[cal=date]').datepicker({
        // minDate : plusDate(date, -1)
    });

    jQuery('input[cal=sdate]').datepicker({
        minDate: new Date("2020-04-01"),
        beforeShow: function(date) {
            var date = jQuery(this).next().datepicker('getDate');

            date.setDate(date.getDate()); // Add 7 days
            jQuery(this).datepicker("option", "maxDate", date);
        },
        onClose: function(selectedDate) {
            // 시작일(fromDate) datepicker가 닫힐때
            // 종료일(toDate)의 선택할수있는 최소 날짜(minDate)를 선택한 시작일로 지정 
            var date = jQuery(this).datepicker('getDate');

            date.setDate(date.getDate()); // Add 7 days
            jQuery(this).next().datepicker("option", "minDate", date);
        }
    });


    jQuery('input[cal=edate]').datepicker({
        minDate: new Date("2020-05-01"),
        beforeShow: function(date) {
            var date = jQuery(this).prev().datepicker('getDate');

            date.setDate(date.getDate()); // Add 7 days
            jQuery(this).datepicker("option", "minDate", date);
        },
        onClose: function(selectedDate) {

            // 시작일(fromDate) datepicker가 닫힐때
            // 종료일(toDate)의 선택할수있는 최소 날짜(minDate)를 선택한 시작일로 지정 
            var date = jQuery(this).datepicker('getDate');

            date.setDate(date.getDate()); // Add 7 days
            jQuery(this).prev().datepicker("option", "maxDate", date);
        }
    });
});

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