var busPoint = {}
busPoint.sPointY1 = [];
busPoint.sPointY1.push({ "code": "N", "codename": "출발" });
busPoint.sPointY1.push({ "code": "공덕역", "codename": "공덕역" });
busPoint.sPointY1.push({ "code": "건대입구역", "codename": "건대입구역" });


busPoint.sPointY2 = [];
busPoint.sPointY2.push({ "code": "N", "codename": "출발" });
busPoint.sPointY2.push({ "code": "공덕역", "codename": "공덕역" });
busPoint.sPointY2.push({ "code": "건대입구역", "codename": "건대입구역" });

busPoint.sPointY3 = busPoint.sPointY1;
busPoint.sPointY4 = busPoint.sPointY1;
busPoint.sPointY5 = busPoint.sPointY1;
busPoint.sPointY6 = busPoint.sPointY1;

busPoint.ePointY = [];
busPoint.ePointY.push({ "code": "N", "codename": "도착" });
busPoint.ePointY.push({ "code": "니지모리", "codename": "니지모리" });

busPoint.sPointE1 = busPoint.sPointY1;
busPoint.sPointE2 = busPoint.sPointY2;
busPoint.sPointE3 = busPoint.sPointY1;
busPoint.sPointE4 = busPoint.sPointY2;
busPoint.sPointE5 = busPoint.sPointY1;
busPoint.sPointE6 = busPoint.sPointY2;

busPoint.ePointE = [];
busPoint.ePointE.push({ "code": "N", "codename": "도착" });
busPoint.ePointE.push({ "code": "솔.동해점", "codename": "솔.동해점" });
busPoint.ePointE.push({ "code": "대진항", "codename": "대진항" });
busPoint.ePointE.push({ "code": "금진해변", "codename": "금진해변" });

busPoint.sPointS21 = [];
busPoint.sPointS21.push({ "code": "N", "codename": "출발" });
busPoint.sPointS21.push({ "code": "니지모리", "codename": "니지모리" });

busPoint.sPointS22 = busPoint.sPointS21;
busPoint.sPointS23 = busPoint.sPointS21;
busPoint.sPointS24 = busPoint.sPointS21;
busPoint.sPointS25 = busPoint.sPointS21;
busPoint.sPointS26 = busPoint.sPointS21;

busPoint.ePointS = [];
busPoint.ePointS.push({ "code": "N", "codename": "도착" });
busPoint.ePointS.push({ "code": "건대입구역", "codename": "건대입구역" });
busPoint.ePointS.push({ "code": "공덕역", "codename": "공덕역" });

busPoint.ePointA = busPoint.ePointS;

busPoint.sPointA21 = [];
busPoint.sPointA21.push({ "code": "N", "codename": "출발" });
busPoint.sPointA21.push({ "code": "솔.동해점", "codename": "솔.동해점" });
busPoint.sPointA21.push({ "code": "대진항", "codename": "대진항" });
busPoint.sPointA21.push({ "code": "금진해변", "codename": "금진해변" });

busPoint.sPointA22 = busPoint.sPointA21;
busPoint.sPointA23 = busPoint.sPointA21;
busPoint.sPointA51 = busPoint.sPointA21;
busPoint.sPointA52 = busPoint.sPointA21;
busPoint.sPointA53 = busPoint.sPointA21;

var busPoint_1 = "공덕역 3번출구 &gt; 건대입구역 5번출구";
var busPoint_2 = "공덕역 3번출구 &gt; 건대입구역 5번출구";
var busPoint_3 = "건대입구역 5번출구  &gt; 공덕역 3번출구";
var busPoint_4 = "솔.동해점 &gt; 대진항 &gt; 금진해변";
var busPointList = {
    "Y1": { li: busPoint_1 },
    "Y2": { li: busPoint_1 },
    "Y3": { li: busPoint_1 },
    "Y4": { li: busPoint_1 },
    "Y5": { li: busPoint_1 },
    "Y6": { li: busPoint_1 },
    "E1": { li: busPoint_1 },
    "E2": { li: busPoint_1 },
    "E3": { li: busPoint_1 },
    "E4": { li: busPoint_2 },
    "E5": { li: busPoint_1 },
    "E6": { li: busPoint_2 },
    "S21": { li: busPoint_3 },
    "S22": { li: busPoint_3 },
    "S23": { li: busPoint_3 },
    "S24": { li: busPoint_3 },
    "S25": { li: busPoint_3 },
    "S26": { li: busPoint_3 },
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
    "공덕역": [0, MARKER_SPRITE_Y_OFFSET * 3, '37.5453585', '126.9514437', '공덕역 3번출구 앞', '탑승시간 : <font color="red">10시 30분</font>', 0],
    "건대입구": [MARKER_SPRITE_X_OFFSET * 1, MARKER_SPRITE_Y_OFFSET * 3, '37.5393413', '127.0716672', '건대입구역 롯데백화점 스타시티점 입구', '탑승시간 : <font color="red">11시 10분</font>', 1],
    "봉천역": [MARKER_SPRITE_X_OFFSET * 2, MARKER_SPRITE_Y_OFFSET * 3, '37.4821436', '126.9426997', '봉천역 1번출구 앞', '탑승시간 : <font color="red">06시 40분</font>', 2],
    "사당역": [MARKER_SPRITE_X_OFFSET * 3, MARKER_SPRITE_Y_OFFSET * 3, '37.4764763', '126.977734', '사당역 6번출구 방향 참약사 장수약국 앞', '탑승시간 : <font color="red">06시 50분</font>', 3],
    "강남역": [MARKER_SPRITE_X_OFFSET * 4, MARKER_SPRITE_Y_OFFSET * 3, '37.4982078', '127.0290928', '강남역 1번출구 버스정류장', '탑승시간 : <font color="red">07시 05분</font>', 4],
    "종합운동장역": [MARKER_SPRITE_X_OFFSET * 5, MARKER_SPRITE_Y_OFFSET * 3, '37.5104765', '127.0722925', '종합운동장역 4번출구 방향 버스정류장 뒤쪽', '탑승시간 : <font color="red">07시 20분</font>', 5]
};
var busPointListY2 = {
    "니지모리": [0, MARKER_SPRITE_Y_OFFSET * 3, '37.8792025', '127.0920022', '니지모리 스튜디오', '탑승시간 : <font color="red">21시 00분</font>', 0],
    "합정역": [MARKER_SPRITE_X_OFFSET * 1, MARKER_SPRITE_Y_OFFSET * 3, '37.5507926', '126.9159159', '합정역 3번출구 앞', '탑승시간 : <font color="red">06시 10분</font>', 1],
    "종로3가역": [MARKER_SPRITE_X_OFFSET * 2, MARKER_SPRITE_Y_OFFSET * 3, '37.5703347', '126.99317687', '종로3가역 12번출구 방향 새마을금고 앞', '탑승시간 : <font color="red">06시 35분</font>', 2],
    "왕십리역": [MARKER_SPRITE_X_OFFSET * 3, MARKER_SPRITE_Y_OFFSET * 3, '37.5615557', '127.0348018', '왕십리역 11번출구 방향 우리은행 앞', '탑승시간 : <font color="red">06시 50분</font>', 3],
    "건대입구": [MARKER_SPRITE_X_OFFSET * 4, MARKER_SPRITE_Y_OFFSET * 3, '37.5393413', '127.0716672', '건대입구역 롯데백화점 스타시티점 입구', '탑승시간 : <font color="red">07시 05분</font>', 4],
    "종합운동장역": [MARKER_SPRITE_X_OFFSET * 5, MARKER_SPRITE_Y_OFFSET * 3, '37.5104765', '127.0722925', '종합운동장역 4번출구 방향 버스정류장 뒤쪽', '탑승시간 : <font color="red">07시 20분</font>', 5]
};
var busPointListS1 = {
    "니지모리": [0, 0, '37.8792025', '127.0920022', '니지모리 스튜디오 주차장', '탑승시간 : <font color="red">21시 00분</font>', 0],
    "남애해변": [MARKER_SPRITE_X_OFFSET * 1, 0, '37.9452543', '128.7814356', '남애3리 입구', '탑승시간 : <font color="red">14시 30분 / 17시 30분</font>', 1],
    "인구해변": [MARKER_SPRITE_X_OFFSET * 2, 0, '37.9689758', '128.7599915', '현남면사무소 맞은편', '탑승시간 : <font color="red">14시 35분 / 17시 35분</font>', 2],
    "죽도해변": [MARKER_SPRITE_X_OFFSET * 3, 0, '37.9720003', '128.7595433', 'GS25 죽도비치점 맞은편', '탑승시간 : <font color="red">14시 40분 / 17시 40분</font>', 3],
    "동산항해변": [MARKER_SPRITE_X_OFFSET * 4, 0, '37.9763045', '128.7586692', '동산카센타 맞은편', '탑승시간 : <font color="red">14시 45분 / 17시 45분</font>', 4],
    "기사문해변": [MARKER_SPRITE_X_OFFSET * 5, 0, '38.0053627', '128.7306342', '기사문 해변주차장 입구', '탑승시간 : <font color="red">14시 50분 / 17시 50분</font>', 5],
    "서피비치": [MARKER_SPRITE_X_OFFSET * 6, 0, '38.0268271', '128.7169575', '서피비치 회전교차로 횡단보도 앞', '탑승시간 : <font color="red">15시 00분 / 18시 00분</font>', 6]
}
var busPointListA1 = {
    "솔.동해점": [0, 0, '37.5782382', '129.1156248', '솔.동해점 입구', '탑승시간 : <font color="red">14시 00분 / 17시 00분</font>', 0],
    "대진항": [MARKER_SPRITE_X_OFFSET * 1, 0, '37.5807657', '129.111344', '대진항 공영주차장 입구', '탑승시간 : <font color="red">14시 05분 / 17시 05분</font>', 1],
    "금진해변": [MARKER_SPRITE_X_OFFSET * 2, 0, '37.6347202', '129.0450586', '금진해변 공영주차장 입구', '탑승시간 : <font color="red">14시 20분 / 17시 20분</font>', 2]
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
        url: "/act/frip/inc_surfbus_point.php",
        data: params,
        success: function(data) {
            objStop.text(data);
        }
    })
}