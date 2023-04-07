var busPoint = {}
busPoint.sPointYS = [];
busPoint.sPointYS.push({ "code": "N", "codename": "출발" });
busPoint.sPointYS.push({ "code": "신도림", "codename": "신도림" });
busPoint.sPointYS.push({ "code": "대림역", "codename": "대림역" });
busPoint.sPointYS.push({ "code": "사당역", "codename": "사당역" });
busPoint.sPointYS.push({ "code": "강남역", "codename": "강남역" });
busPoint.sPointYS.push({ "code": "종합운동장역", "codename": "종합운동장역" });

busPoint.sPointYJ = [];
busPoint.sPointYJ.push({ "code": "N", "codename": "출발" });
busPoint.sPointYJ.push({ "code": "합정역", "codename": "합정역" });
busPoint.sPointYJ.push({ "code": "종로3가역", "codename": "종로3가역" });
busPoint.sPointYJ.push({ "code": "건대입구", "codename": "건대입구" });
busPoint.sPointYJ.push({ "code": "종합운동장역", "codename": "종합운동장역" });

busPoint.sPointSY = [];
busPoint.sPointSY.push({ "code": "N", "codename": "출발" });
busPoint.sPointSY.push({ "code": "남애3리", "codename": "남애3리" });
busPoint.sPointSY.push({ "code": "인구해변", "codename": "인구해변" });
busPoint.sPointSY.push({ "code": "죽도해변", "codename": "죽도해변" });
busPoint.sPointSY.push({ "code": "기사문해변", "codename": "기사문해변" });
busPoint.sPointSY.push({ "code": "서피비치", "codename": "서피비치" });

//서울 > 양양 도착
busPoint.ePointYend = [];
busPoint.ePointYend.push({ "code": "N", "codename": "도착" });
busPoint.ePointYend.push({ "code": "서피비치", "codename": "서피비치" });
busPoint.ePointYend.push({ "code": "기사문해변", "codename": "기사문해변" });
busPoint.ePointYend.push({ "code": "죽도해변", "codename": "죽도해변" });
busPoint.ePointYend.push({ "code": "인구해변", "codename": "인구해변" });
busPoint.ePointYend.push({ "code": "남애3리", "codename": "남애3리" });

busPoint.sPointES = busPoint.sPointYS;
busPoint.sPointEJ = busPoint.sPointYJ;

busPoint.sPointAE = [];
busPoint.sPointAE.push({ "code": "N", "codename": "출발" });
busPoint.sPointAE.push({ "code": "금진해변", "codename": "금진해변" });
busPoint.sPointAE.push({ "code": "대진해변", "codename": "대진해변" });
busPoint.sPointAE.push({ "code": "솔.동해점", "codename": "솔.동해점" });

//서울 > 동해 도착
busPoint.ePointEend = [];
busPoint.ePointEend.push({ "code": "N", "codename": "도착" });
busPoint.ePointEend.push({ "code": "금진해변", "codename": "금진해변" });
busPoint.ePointEend.push({ "code": "대진해변", "codename": "대진해변" });
busPoint.ePointEend.push({ "code": "솔.동해점", "codename": "솔.동해점" });

//양양,동해 > 서울 도착
busPoint.ePointSend = [];
busPoint.ePointSend.push({ "code": "N", "codename": "도착" });
busPoint.ePointSend.push({ "code": "잠실역", "codename": "잠실역" });
busPoint.ePointSend.push({ "code": "강남역", "codename": "강남역" });
busPoint.ePointSend.push({ "code": "사당역", "codename": "사당역" });

var busPoint_1 = "신도림 &gt; 대림역 &gt; 사당역 &gt; 강남역 &gt; 종합운동장역";
var busPoint_2 = "합정역 &gt; 종로3가역 &gt; 건대입구 &gt; 종합운동장역";
var busPoint_3 = "남애3리 &gt; 인구해변 &gt; 죽도해변 &gt; 기사문해변 &gt; 서피비치";
var busPoint_4 = "금진해변 &gt; 대진해변 &gt; 솔.동해점";
var busPointList = {
    "YS": { li: busPoint_1 }, //사당 > 양양
    "YJ": { li: busPoint_2 }, //종로 > 양양
    "SY": { li: busPoint_3 }, //양양 > 서울

    "ES": { li: busPoint_1 }, //사당 > 동해
    "EJ": { li: busPoint_2 }, //종로 > 동해
    "AE": { li: busPoint_4 } //동해 > 서울
};

var MARKER_SPRITE_X_OFFSET = 29,
    MARKER_SPRITE_Y_OFFSET = 50;
var busPointListY1 = {
    "신도림": [0, MARKER_SPRITE_Y_OFFSET * 3, '37.5095592', '126.8885712', '홈플러스 신도림점 앞', '탑승시간 : <font color="red">06시 00분</font>', 0],
    "대림역": [MARKER_SPRITE_X_OFFSET * 1, MARKER_SPRITE_Y_OFFSET * 3, '37.4928008', '126.8947074', '대림역 2번출구 앞', '탑승시간 : <font color="red">06시 07분</font>', 1],
    "사당역": [MARKER_SPRITE_X_OFFSET * 3, MARKER_SPRITE_Y_OFFSET * 3, '37.4764763', '126.977734', '사당역 6번출구 방향 참약사 장수약국 앞', '탑승시간 : <font color="red">06시 20분</font>', 3],
    "강남역": [MARKER_SPRITE_X_OFFSET * 4, MARKER_SPRITE_Y_OFFSET * 3, '37.4982078', '127.0290928', '강남역 1번출구 버스정류장', '탑승시간 : <font color="red">06시 35분</font>', 4],
    "종합운동장역": [MARKER_SPRITE_X_OFFSET * 5, MARKER_SPRITE_Y_OFFSET * 3, '37.5104765', '127.0722925', '종합운동장역 4번출구 방향 버스정류장 뒤쪽', '탑승시간 : <font color="red">06시 50분</font>', 5]
};
var busPointListY2 = {
    "합정역": [MARKER_SPRITE_X_OFFSET * 1, MARKER_SPRITE_Y_OFFSET * 3, '37.5507926', '126.9159159', '합정역 3번출구 앞', '탑승시간 : <font color="red">05시 50분</font>', 1],
    "종로3가역": [MARKER_SPRITE_X_OFFSET * 2, MARKER_SPRITE_Y_OFFSET * 3, '37.5703347', '126.99317687', '종로3가역 12번출구 방향 새마을금고 앞', '탑승시간 : <font color="red">06시 10분</font>', 2],
    "건대입구": [MARKER_SPRITE_X_OFFSET * 4, MARKER_SPRITE_Y_OFFSET * 3, '37.5393413', '127.0716672', '건대입구역 롯데백화점 스타시티점 입구', '탑승시간 : <font color="red">06시 35분</font>', 4],
    "종합운동장역": [MARKER_SPRITE_X_OFFSET * 5, MARKER_SPRITE_Y_OFFSET * 3, '37.5104765', '127.0722925', '종합운동장역 4번출구 방향 버스정류장 뒤쪽', '탑승시간 : <font color="red">06시 50분</font>', 5]
};
var busPointListS1 = {
    "남애해변": [MARKER_SPRITE_X_OFFSET * 1, 0, '37.9452543', '128.7814356', '남애3리 입구', '탑승시간 : <font color="red">14시 30분 / 17시 30분</font>', 1],
    "인구해변": [MARKER_SPRITE_X_OFFSET * 2, 0, '37.9689758', '128.7599915', '현남면사무소 맞은편', '탑승시간 : <font color="red">14시 35분 / 17시 35분</font>', 2],
    "죽도해변": [MARKER_SPRITE_X_OFFSET * 3, 0, '37.9720003', '128.7595433', 'GS25 죽도비치점 맞은편', '탑승시간 : <font color="red">14시 42분 / 17시 42분</font>', 3],
    "기사문해변": [MARKER_SPRITE_X_OFFSET * 5, 0, '38.0053627', '128.7306342', '기사문 해변주차장 입구', '탑승시간 : <font color="red">14시 50분 / 17시 50분</font>', 5],
    "서피비치": [MARKER_SPRITE_X_OFFSET * 6, 0, '38.0268271', '128.7169575', '서피비치 회전교차로 횡단보도 앞', '탑승시간 : <font color="red">15시 00분 / 18시 00분</font>', 6]
}
var busPointListA1 = {
    "솔.동해점": [0, 0, '37.5782382', '129.1156248', '솔.동해점 입구', '탑승시간 : <font color="red">14시 00분 / 18시 00분</font>', 0],
    "대진해변": [MARKER_SPRITE_X_OFFSET * 1, 0, '37.5807657', '129.111344', '대진항 공영주차장 입구', '탑승시간 : <font color="red">13시 55분 / 17시 55분</font>', 1],
    "금진해변": [MARKER_SPRITE_X_OFFSET * 2, 0, '37.6347202', '129.0450586', '금진해변 공영주차장 입구', '탑승시간 : <font color="red">13시 35분 / 17시 35분</font>', 2]
}

function fnBusTime(obj, busnum, num) {
    if (num == -1) {
        var objStop = $j(obj).parent().find("#stopLocation");
    } else {
        var objStop = $j("td[id=stopLocation]").eq(num);
    }
    if (obj.value == "N") {
        objStop.text('');
        return;
    }
    var params = "res_spointname=" + obj.value + "&res_bus=" + busnum.substring(0, 2);
    $j.ajax({
        type: "POST",
        url: "/act_2023/front/bus/view_bus_point.php",
        data: params,
        success: function(data) {
            objStop.text(data);
        }
    })
}