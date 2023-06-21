var holidays = {
    "0101": { type: 0, title: "신정", year: "" },
    "0301": { type: 0, title: "삼일절", year: "" },
    "0505": { type: 0, title: "어린이날", year: "" },
    "0606": { type: 0, title: "현충일", year: "" },
    "0815": { type: 0, title: "광복절", year: "" },
    "1003": { type: 0, title: "개천절", year: "" },
    "1009": { type: 0, title: "한글날", year: "" },
    "1225": { type: 0, title: "크리스마스", year: "" },

    "0519": { type: 0, title: "석가탄신일", year: "2021" },

    "0920": { type: 0, title: "추석", year: "2021" },
    "0921": { type: 0, title: "추석", year: "2021" },
    "0922": { type: 0, title: "추석", year: "2021" },

    "0211": { type: 0, title: "설날", year: "2021" },
    "0212": { type: 0, title: "설날", year: "2021" },
    "0213": { type: 0, title: "설날", year: "2021" }
};

var rtnBusDate = function(day, getDay, json, bus) {
    var holiday = holidays[$j.datepicker.formatDate("mmdd", day)];
    var thisYear = $j.datepicker.formatDate("yy", day);

    if (json != "init") {
        var onoffDay = json[bus + ((day.getMonth() + 1) + 100).toString().substring(1, 3) + (day.getDate() + 100).toString().toString().substring(1, 3)];
    }

    var cssRes = "";
    if (holiday) {
        if (thisYear == holiday.year || holiday.year == "") {
            cssRes = "date-sunday";
        }
    }

    var result;
    if (getDay == 0) {
        cssRes = "date-sunday";
    } else if (getDay == 6) {
        cssRes = "date-saturday";
    } else {
        cssRes = "";
    }

    if (json == "init") {
        result = [true, cssRes];
    } else {
        if (onoffDay) {
            result = [true, cssRes];
        } else {
            result = [false, cssRes];
        }
    }

    return result;
}


var selDate;
var busNum;
var busNumName;
var busType;



//셔틀버스 상세탭
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