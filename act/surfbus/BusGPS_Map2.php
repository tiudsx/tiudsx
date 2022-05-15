<script src="/common/js/jquery.min.js?20180801170322"></script>

<div class="bd" style="padding:0px;" id="s1">
	클릭 위도,경도 : <span id="mapnum"></span>
	<div>
	<br>
	<input type="text" id="schText" size="60" > <input type="button" value="위치" onclick="fnsch();"><br><br>
	lat : <input type="text" id="lat" size="30" ><br>
	lng : <input type="text" id="lng" size="30" ><br><br>
	<table border=1>
		<tr>
			<td id="lat1"></td>
			<td id="lng1"></td>
		</tr>
	</table>
	<br>
	</div>
	<div id="map" style="width:50%;height:50%;">
	</div>
</div>

<script type="text/javascript" src="https://openapi.map.naver.com/openapi/v3/maps.js?ncpClientId=zhh3svia3i&submodules=geocoder"></script>
<script>
function fnsch(){
	searchAddressToCoordinate($("#schText").val());
}

function searchAddressToCoordinate(address) {
    naver.maps.Service.geocode({
        address: address
    }, function(status, response) {
        if (status === naver.maps.Service.Status.ERROR) {
            return alert('Something Wrong!');
        }

        var item = response.result.items[0],
            addrType = item.isRoadAddress ? '[도로명 주소]' : '[지번 주소]',
            point = new naver.maps.Point(item.point.x, item.point.y);

		$("#lat").val(item.point.y);
		$("#lng").val(item.point.x);
		$("#lat1").html(item.point.y);
		$("#lng1").html(item.point.x);
		//alert(item.point.x + '/' + item.point.y + '\n\n' + item.isRoadAddress + ' : ' + response.result.userquery);

        infoWindow.setContent([
                '<div style="padding:10px;min-width:200px;line-height:150%;">',
                '<h4 style="margin-top:5px;">검색 주소 : '+ response.result.userquery +'</h4><br />',
                addrType +' '+ item.address +'<br />',
                '&nbsp&nbsp&nbsp -> '+ point.x +','+ point.y,
                '</div>'
            ].join('\n'));


        map.setCenter(point);
        infoWindow.open(map, point);
      
    });
}



function initGeocoder() {
    var latlng = map.getCenter();
    var utmk = naver.maps.TransCoord.fromLatLngToUTMK(latlng); // 위/경도 -> UTMK
    var tm128 = naver.maps.TransCoord.fromUTMKToTM128(utmk);   // UTMK -> TM128
    var naverCoord = naver.maps.TransCoord.fromTM128ToNaver(tm128); // TM128 -> NAVER

    infoWindow = new naver.maps.InfoWindow({
        content: ''
    });

    map.addListener('click', function(e) {
        var latlng = e.coord,
            utmk = naver.maps.TransCoord.fromLatLngToUTMK(latlng),
            tm128 = naver.maps.TransCoord.fromUTMKToTM128(utmk),
            naverCoord = naver.maps.TransCoord.fromTM128ToNaver(tm128),
			ch = naver.maps.TransCoord.fromNaverToLatLng("291971,562552");

		//임시
		document.getElementById("mapnum").innerText = latlng;

        utmk.x = parseFloat(utmk.x.toFixed(1));
        utmk.y = parseFloat(utmk.y.toFixed(1));


        /*infoWindow.setContent([
            '<div style="padding:10px;width:350px;font-size:14px;line-height:20px;">',
            '<strong>LatLng</strong> : '+ latlng +'<br />',
            '<strong>UTMK</strong> : '+ utmk +'<br />',
            '<strong>TM128</strong> : '+ tm128 +'<br />',
            '<strong>NAVER</strong> : '+ naverCoord +'<br />',
            '</div>'
        ].join(''));*/

        infoWindow.open(map, latlng);
    });
}
//searchAddressToCoordinate("제주특별자치도 서귀포시 색달동 3039");
naver.maps.onJSContentLoaded = initGeocoder;
</script>


<script>
var lat = 37.9731043;
var lng = 128.7591667;

var MARKER_SPRITE_X_OFFSET = 29,
    MARKER_SPRITE_Y_OFFSET = 50,
    MARKER_SPRITE_POSITION = {
        "망고서프"		: [0, MARKER_SPRITE_Y_OFFSET*3, lat, lng, '강원 양양군 현남면 인구중앙길 97', '#서핑강습 #게스트하우스 #렌탈', 0],
        "모쿠서프"		: [MARKER_SPRITE_X_OFFSET*1, MARKER_SPRITE_Y_OFFSET*3, '37.9731043', '128.7591667', '강원 양양군 현남면 인구중앙길 95-1', '#서핑강습 #게스트하우스 #렌탈', 1],
        "조아서프": [MARKER_SPRITE_X_OFFSET*2, MARKER_SPRITE_Y_OFFSET*3, '37.9715175', '128.7594324', '강원 양양군 현남면 인구중앙길 79', '#서핑강습 #게스트하우스 #렌탈', 2],
        "서퍼911"		: [MARKER_SPRITE_X_OFFSET*3, MARKER_SPRITE_Y_OFFSET*3, '37.9725146', '128.7591486', '강원 양양군 현남면 인구중앙길 89', '#서핑강습 #게스트하우스 #렌탈', 3]
    };


var mapOptions = {
	zoomControl: true,
	zoomControlOptions: {
		style: naver.maps.ZoomControlStyle.SMALL,
		position: naver.maps.Position.RIGHT_CENTER
	},
	center: new naver.maps.LatLng(lat, lng),
    zoom: 13
};

var map = new naver.maps.Map('map', mapOptions);

var bounds = map.getBounds(),
    southWest = bounds.getSW(),
    northEast = bounds.getNE(),
    lngSpan = northEast.lng() - southWest.lng(),
    latSpan = northEast.lat() - southWest.lat();

var markers = [],
    infoWindows = [];

for (var key in MARKER_SPRITE_POSITION) {

    /*
	var position = new naver.maps.LatLng(
        southWest.lat() + latSpan * Math.random(),
        southWest.lng() + lngSpan * Math.random());
	*/
    var position = new naver.maps.LatLng(MARKER_SPRITE_POSITION[key][2],MARKER_SPRITE_POSITION[key][3]);

    var marker = new naver.maps.Marker({
        map: map,
        position: position,
        title: key,
		icon: {
			url: 'https://navermaps.github.io/maps.js.ncp/docs/img/example/ico_pin.jpg', //50, 68 크기의 원본 이미지
			//url: 'http://static.naver.com/maps2/icons/marker-default.png',
			size: new naver.maps.Size(18, 25),
			scaledSize: new naver.maps.Size(18, 25),
			origin: new naver.maps.Point(0, 0),
			anchor: new naver.maps.Point(12, 34)
		},

        /*icon: {
            url: 'http://static.naver.com/maps2/icons/pin_spot.png'
        },*/
        zIndex: 100
    });

    var infoWindow = new naver.maps.InfoWindow({
        content: '<div style="width:230px;text-align:left;padding:10px;line-height:1.5;"><b>'+ key +'</b><br/>&nbsp;&nbsp;&nbsp;' + MARKER_SPRITE_POSITION[key][4] + '<br/>&nbsp;&nbsp;&nbsp;' + MARKER_SPRITE_POSITION[key][5] + "</div>"
    });
	marker.set('seq', MARKER_SPRITE_POSITION[key][0]);
	marker.set('seq2', MARKER_SPRITE_POSITION[key][1]);
	marker.set('seq3', key);
    markers.push(marker);
    infoWindows.push(infoWindow);

    //marker.addListener('mouseover', onMouseOver);
    //marker.addListener('mouseout', onMouseOut);

/*
	if(MARKER_SPRITE_POSITION[key][6] == 0){
		infoWindow.open(map, marker)
	}
*/
};

naver.maps.Event.addListener(map, 'idle', function() {
    updateMarkers(map, markers);
});

function updateMarkers(map, markers) {

    var mapBounds = map.getBounds();
    var marker, position;

    for (var i = 0; i < markers.length; i++) {

        marker = markers[i]
        position = marker.getPosition();

        if (mapBounds.hasLatLng(position)) {
            showMarker(map, marker);
        } else {
            hideMarker(map, marker);
        }
    }
}

function showMarker(map, marker) {
    if (marker.setMap()) return;
    marker.setMap(map);
}

function hideMarker(map, marker) {
    if (!marker.setMap()) return;
    marker.setMap(null);
}

// 해당 마커의 인덱스를 seq라는 클로저 변수로 저장하는 이벤트 핸들러를 반환합니다.
function getClickHandler(seq) {
    return function(e) {
        var marker = markers[seq],
            infoWindow = infoWindows[seq];
        if (infoWindow.getMap()) {
            infoWindow.close();
        } else {
            infoWindow.open(map, marker);

			var position = new naver.maps.LatLng(marker["position"]["y"],marker["position"]["x"]);
			map.panTo(position);
        }
    }
}

for (var i=0, ii=markers.length; i<ii; i++) {
    naver.maps.Event.addListener(markers[i], 'click', getClickHandler(i));
}

function onMouseOver(e) {
    var marker = e.overlay,
        seq = marker.get('seq'),
        seq2 = marker.get('seq2');

    marker.setIcon({
        url: 'http://static.naver.com/maps2/icons/pin_spot.png'
    });
}
function onMouseOut(e) {
    var marker = e.overlay,
        seq = marker.get('seq'),
        seq2 = marker.get('seq2');

    marker.setIcon({
        url: 'http://static.naver.com/maps2/icons/marker-default.png'
    });
}

function mapMove(vlu){
	var position = new naver.maps.LatLng(MARKER_SPRITE_POSITION[vlu][2],MARKER_SPRITE_POSITION[vlu][3]);
	map.panTo(position);

	var num = MARKER_SPRITE_POSITION[vlu][6];
	var marker = markers[num],
		infoWindow = infoWindows[num];
        infoWindow.open(map, marker);



	var mobile = fnMobileType();
	var mapAppHtml = "";
	var mapNameText = encodeURIComponent(vlu);
}

function fnMobileType(){
	if (/android|iphone|ipad|ipod/i.test(navigator.userAgent))
	{
		if(/android/i.test(navigator.userAgent)){
			return 1;
		}else{
			return 2;
		}
	}else{
		return 0;
	}
}
</script>
