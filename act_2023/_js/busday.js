var busPoint = {}
busPoint.sPointY1 = [];
busPoint.sPointY1.push({ "code": "N", "codename": "출발" });
busPoint.sPointY1.push({ "code": "신도림", "codename": "신도림" });
busPoint.sPointY1.push({ "code": "대림역", "codename": "대림역" });
busPoint.sPointY1.push({ "code": "사당역", "codename": "사당역" });
busPoint.sPointY1.push({ "code": "강남역", "codename": "강남역" });
busPoint.sPointY1.push({ "code": "종합운동장역", "codename": "종합운동장역" });

busPoint.sPointY3 = busPoint.sPointY1;
busPoint.sPointY5 = busPoint.sPointY1;

busPoint.sPointY2 = [];
busPoint.sPointY2.push({ "code": "N", "codename": "출발" });
busPoint.sPointY2.push({ "code": "합정역", "codename": "합정역" });
busPoint.sPointY2.push({ "code": "종로3가역", "codename": "종로3가역" });
busPoint.sPointY2.push({ "code": "건대입구", "codename": "건대입구" });
busPoint.sPointY2.push({ "code": "종합운동장역", "codename": "종합운동장역" });

busPoint.sPointY4 = busPoint.sPointY2;
busPoint.sPointY6 = busPoint.sPointY2;

busPoint.ePointY = [];
busPoint.ePointY.push({ "code": "N", "codename": "도착" });
busPoint.ePointY.push({ "code": "서피비치", "codename": "서피비치" });
busPoint.ePointY.push({ "code": "기사문해변", "codename": "기사문해변" });
busPoint.ePointY.push({ "code": "죽도해변", "codename": "죽도해변" });
busPoint.ePointY.push({ "code": "인구해변", "codename": "인구해변" });
busPoint.ePointY.push({ "code": "남애3리", "codename": "남애3리" });

busPoint.sPointE1 = busPoint.sPointY1;
busPoint.sPointE2 = busPoint.sPointY2;
busPoint.sPointE3 = busPoint.sPointY1;
busPoint.sPointE4 = busPoint.sPointY2;
busPoint.sPointE5 = busPoint.sPointY1;
busPoint.sPointE6 = busPoint.sPointY2;

busPoint.ePointE = [];
busPoint.ePointE.push({ "code": "N", "codename": "도착" });
busPoint.ePointE.push({ "code": "금진해변", "codename": "금진해변" });
busPoint.ePointE.push({ "code": "대진해변", "codename": "대진해변" });
busPoint.ePointE.push({ "code": "솔.동해점", "codename": "솔.동해점" });

busPoint.sPointS21 = [];
busPoint.sPointS21.push({ "code": "N", "codename": "출발" });
busPoint.sPointS21.push({ "code": "남애3리", "codename": "남애3리" });
busPoint.sPointS21.push({ "code": "인구해변", "codename": "인구해변" });
busPoint.sPointS21.push({ "code": "죽도해변", "codename": "죽도해변" });
busPoint.sPointS21.push({ "code": "기사문해변", "codename": "기사문해변" });
busPoint.sPointS21.push({ "code": "서피비치", "codename": "서피비치" });

busPoint.sPointS22 = busPoint.sPointS21;
busPoint.sPointS23 = busPoint.sPointS21;
busPoint.sPointS51 = busPoint.sPointS21;
busPoint.sPointS52 = busPoint.sPointS21;
busPoint.sPointS53 = busPoint.sPointS21;

busPoint.ePointS = [];
busPoint.ePointS.push({ "code": "N", "codename": "도착" });
busPoint.ePointS.push({ "code": "잠실역", "codename": "잠실역" });
busPoint.ePointS.push({ "code": "강남역", "codename": "강남역" });
busPoint.ePointS.push({ "code": "사당역", "codename": "사당역" });

busPoint.ePointA = busPoint.ePointS;

busPoint.sPointA21 = [];
busPoint.sPointA21.push({ "code": "N", "codename": "출발" });
busPoint.sPointA21.push({ "code": "솔.동해점", "codename": "솔.동해점" });
busPoint.sPointA21.push({ "code": "대진해변", "codename": "대진해변" });
busPoint.sPointA21.push({ "code": "금진해변", "codename": "금진해변" });

busPoint.sPointA22 = busPoint.sPointA21;
busPoint.sPointA23 = busPoint.sPointA21;
busPoint.sPointA51 = busPoint.sPointA21;
busPoint.sPointA52 = busPoint.sPointA21;
busPoint.sPointA53 = busPoint.sPointA21;

var busPoint_1 = "신도림 &gt; 대림역 &gt; 사당역 &gt; 강남역 &gt; 종합운동장역";
var busPoint_2 = "합정역 &gt; 종로3가역 &gt; 건대입구 &gt; 종합운동장역";
var busPoint_3 = "남애3리 &gt; 인구해변 &gt; 죽도해변 &gt; 기사문해변 &gt; 서피비치";
var busPoint_4 = "금진해변 &gt; 대진해변 &gt; 솔.동해점";
var busPointList = {
    "Y1": { li: busPoint_1 },
    "Y2": { li: busPoint_2 },
    "Y3": { li: busPoint_1 },
    "Y4": { li: busPoint_2 },
    "Y5": { li: busPoint_1 },
    "Y6": { li: busPoint_2 },
    "E1": { li: busPoint_1 },
    "E2": { li: busPoint_2 },
    "E3": { li: busPoint_1 },
    "E4": { li: busPoint_2 },
    "E5": { li: busPoint_1 },
    "E6": { li: busPoint_2 },
    "S21": { li: busPoint_3 },
    "S22": { li: busPoint_3 },
    "S23": { li: busPoint_3 },
    "S51": { li: busPoint_3 },
    "S52": { li: busPoint_3 },
    "S53": { li: busPoint_3 },
    "A21": { li: busPoint_4 },
    "A22": { li: busPoint_4 },
    "A23": { li: busPoint_4 },
    "A51": { li: busPoint_4 },
    "A52": { li: busPoint_4 },
    "A53": { li: busPoint_4 }
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
    var params = "res_spointname=" + obj.value + "&res_bus=" + busnum;
    $j.ajax({
        type: "POST",
        url: "/act/surf/surfbus_point.php",
        data: params,
        success: function(data) {
            objStop.text(data);
        }
    })
}