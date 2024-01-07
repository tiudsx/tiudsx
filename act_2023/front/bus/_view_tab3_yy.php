<div style="display: block;padding-top:5px;">
    <div class="bd">
        <table>
            <tbody>
                <tr>
                    <td style="border:0px none;line-height:1.5;">
                        <strong><font color="#ff0000">※ </font></strong>
                        <span style="color: rgb(255, 0, 0);font-size:12px;"><strong>정류장 [지도]를 클릭하시면 네이버 지도 및 정류장 위치 사진을 볼 수 있습니다.</strong></span><br>
                        <font color="#ff0000" style="font-size:12px;"><strong>&nbsp;&nbsp;&nbsp;교통상황으로 인해 셔틀버스가 지연 도착할 수 있으니 양해부탁드립니다.</strong></font><br><br>
                        <strong>※ </strong>
                        <font style="font-size:12px;"><strong>사전 신청하지 않는 정류장은 정차 및 하차하지 않습니다.</strong></font><br><br>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="bd">
        <table class="et_vars">
            <colgroup>
                <col style="width:25%;">
                <col style="width:25%;">
                <col style="width:25%;">
                <col style="width:25%;">
            </colgroup>
            <tbody>
                <tr>
                    <th colspan="2" style="text-align: center;">
                        <strong style="line-height:2;">
                            ★ 서울 출발
                        </strong>
                    </th>
                    <th colspan="2" style="text-align: center;">
                        <strong style="line-height:2;">
                            ★ 서울 복귀
                        </strong>
                    </th>
                </tr>
                <tr>
                    <td style="text-align:center;"><input type="button" class="bd_btn" btnpoint="point" style="padding-top:4px;background:#1973e1;color:#fff;" value="사당선" onclick="fnBusPoint(this, 1);"></td>
                    <td style="text-align:center;"><input type="button" class="bd_btn" btnpoint="point" style="padding-top:4px;" value="종로선" onclick="fnBusPoint(this, 2);"></td>
                    <td style="text-align:center;"><input type="button" class="bd_btn" btnpoint="point" style="padding-top:4px;" value="오후 출발" onclick="fnBusPoint(this, 3);"></td>
                    <td style="text-align:center;"><input type="button" class="bd_btn" btnpoint="point" style="padding-top:4px;" value="저녁 출발" onclick="fnBusPoint(this, 4);"></td>

                </tr>
            </tbody>
        </table>
    </div>
    <div class="bd" style="padding:10px 0 0px 0;">
        <table view="tbBus1" class="et_vars">
            <colgroup>
                <col style="width:90px;">
                <col style="width:auto;">
                <col style="width:58px;">
            </colgroup>
            <tbody>
                <tr>
                    <td colspan="3" height="28"><b>★ [양양행] 사당선 셔틀버스</b></td>
                </tr>
                <tr>
                    <th style="text-align:center;"></th>
                    <th style="text-align:center;">탑승장소 및 시간</th>
                    <th style="text-align:center;">위치</th>
                </tr>
                <tr>
                    <th>신도림</th>
                    <td><?=fnBusPointArr2023("사당_신도림",$shopseq, 0)?><br>
                        <font color="red"><?=fnBusPointArr2023("사당_신도림", $shopseq, 1)?></font>
                    </td>
                    <td><input type="button" class="bd_btn mapviewid" style="padding-top:4px;" value="지도" onclick="fnBusMap('사당', 'Y1_1', '신도림', this);"></td>
                </tr>
                <tr>
                    <th>대림역</th>
                    <td><?=fnBusPointArr2023("사당_대림역",$shopseq, 0)?><br>
                        <font color="red"><?=fnBusPointArr2023("사당_대림역", $shopseq, 1)?></font>
                    </td>
                    <td><input type="button" class="bd_btn mapviewid" style="padding-top:4px;" value="지도" onclick="fnBusMap('사당', 'Y1_2', '대림역', this);"></td>
                </tr>
                <tr>
                    <th>사당역</th>
                    <td><?=fnBusPointArr2023("사당_사당역",$shopseq, 0)?><br>
                        <font color="red"><?=fnBusPointArr2023("사당_사당역", $shopseq, 1)?></font>
                    </td>
                    <td><input type="button" class="bd_btn mapviewid" style="padding-top:4px;" value="지도" onclick="fnBusMap('사당', 'Y1_4', '사당역', this);"></td>
                </tr>
                <tr>
                    <th>강남역</th>
                    <td><?=fnBusPointArr2023("사당_강남역",$shopseq, 0)?><br>
                        <font color="red"><?=fnBusPointArr2023("사당_강남역", $shopseq, 1)?></font>
                    </td>
                    <td><input type="button" class="bd_btn mapviewid" style="padding-top:4px;" value="지도" onclick="fnBusMap('사당', 'Y1_5', '강남역', this);"></td>
                </tr>
                <tr>
                    <th>종합운동장역</th>
                    <td><?=fnBusPointArr2023("사당_종합운동장역",$shopseq, 0)?><br>
                        <font color="red"><?=fnBusPointArr2023("사당_종합운동장역", $shopseq, 1)?></font>
                    </td>
                    <td><input type="button" class="bd_btn mapviewid" style="padding-top:4px;" value="지도" onclick="fnBusMap('사당', 'Y1_6', '종합운동장역', this);"></td>
                </tr>
            </tbody>
        </table>

        <table view="tbBus1" class="et_vars">
            <tbody>
                <tr>
                    <td height="28" style="border: 0px solid #DDD;"><b>★ 도착정류장
                        <br><span style="padding-left:30px;"><?=fnBusPointList('busPoint_End_yy')?></span></b></td>
                </tr>
            </tbody>
        </table>

        <table view="tbBus2" class="et_vars" style="display: none;">
            <colgroup>
                <col style="width:90px;">
                <col style="width:auto;">
                <col style="width:58px;">
            </colgroup>
            <tbody>
                <tr>
                    <td colspan="3" height="28"><b>★ [양양행] 종로선 셔틀버스</b></td>
                </tr>
                <tr>
                    <th style="text-align:center;"></th>
                    <th style="text-align:center;">탑승장소 및 시간</th>
                    <th style="text-align:center;">위치</th>
                </tr>
                <tr>
                    <th>합정역</th>
                    <td><?=fnBusPointArr2023("종로_합정역",$shopseq, 0)?><br>
                        <font color="red"><?=fnBusPointArr2023("종로_합정역", $shopseq, 1)?></font>
                    </td>
                    <td><input type="button" class="bd_btn mapviewid" style="padding-top:4px;" value="지도" onclick="fnBusMap('종로', 'Y2_2', '합정역', this);"></td>
                </tr>
                <tr>
                    <th>종로3가역</th>
                    <td><?=fnBusPointArr2023("종로_종로3가역",$shopseq, 0)?><br>
                        <font color="red"><?=fnBusPointArr2023("종로_종로3가역", $shopseq, 1)?></font>
                    </td>
                    <td><input type="button" class="bd_btn mapviewid" style="padding-top:4px;" value="지도" onclick="fnBusMap('종로', 'Y2_3', '종로3가역', this);"></td>
                </tr>
                <tr>
                    <th>건대입구</th>
                    <td><?=fnBusPointArr2023("종로_건대입구",$shopseq, 0)?><br>
                        <font color="red"><?=fnBusPointArr2023("종로_건대입구", $shopseq, 1)?></font>
                    </td>
                    <td><input type="button" class="bd_btn mapviewid" style="padding-top:4px;" value="지도" onclick="fnBusMap('종로', 'Y2_5', '건대입구', this);"></td>
                </tr>
                <tr>
                    <th>종합운동장역</th>
                    <td><?=fnBusPointArr2023("종로_종합운동장역",$shopseq, 0)?><br>
                        <font color="red"><?=fnBusPointArr2023("종로_종합운동장역", $shopseq, 1)?></font>
                    </td>
                    <td><input type="button" class="bd_btn mapviewid" style="padding-top:4px;" value="지도" onclick="fnBusMap('종로', 'Y2_6', '종합운동장역', this);"></td>
                </tr>
            </tbody>
        </table>

        <table view="tbBus2" class="et_vars" style="display: none;">
            <tbody>
                <tr>
                    <td height="28" style="border: 0px solid #DDD;"><b>★ 도착정류장<br><span style="padding-left:30px;"><?=fnBusPointList('busPoint_End_yy')?></span></b></td>
                </tr>
            </tbody>
        </table>

        <table view="tbBus3" class="et_vars" style="display: none;">
            <colgroup>
                <col style="width:90px;">
                <col style="width:auto;">
                <col style="width:58px;">
            </colgroup>
            <tbody>
                <tr>
                    <td colspan="3" height="28"><b>★ [서울행] 오후 출발 셔틀버스</b></td>
                </tr>
                <tr>
                    <th style="text-align:center;"></th>
                    <th style="text-align:center;">탑승장소 및 시간</th>
                    <th style="text-align:center;">위치</th>
                </tr>
                <tr>
                    <th>남애3리</th>
                    <td><?=fnBusPointArr2023("오후_남애3리", $shopseq, 0)?><br>
                        <font color="red"><?=fnBusPointArr2023("오후_남애3리", $shopseq, 1)?></font>
                    </td>
                    <td><input type="button" class="bd_btn mapviewid" style="padding-top:4px;" value="지도" onclick="fnBusMap('오후', 'S1_2', '남애3리', this);"></td>
                </tr>
                <tr>
                    <th>인구해변</th>
                    <td><?=fnBusPointArr2023("오후_인구해변", $shopseq, 0)?><br>
                        <font color="red"><?=fnBusPointArr2023("오후_인구해변", $shopseq, 1)?></font>
                    </td>
                    <td><input type="button" class="bd_btn mapviewid" style="padding-top:4px;" value="지도" onclick="fnBusMap('오후', 'S1_3', '인구해변', this);"></td>
                </tr>
                <tr>
                    <th>죽도해변</th>
                    <td><?=fnBusPointArr2023("오후_죽도해변", $shopseq, 0)?><br>
                        <font color="red"><?=fnBusPointArr2023("오후_죽도해변", $shopseq, 1)?></font>
                    </td>
                    <td><input type="button" class="bd_btn mapviewid" style="padding-top:4px;" value="지도" onclick="fnBusMap('오후', 'S1_4', '죽도해변', this);"></td>
                </tr>
                <tr>
                    <th>기사문해변</th>
                    <td><?=fnBusPointArr2023("오후_기사문해변", $shopseq, 0)?><br>
                        <font color="red"><?=fnBusPointArr2023("오후_기사문해변", $shopseq, 1)?></font>
                    </td>
                    <td><input type="button" class="bd_btn mapviewid" style="padding-top:4px;" value="지도" onclick="fnBusMap('오후', 'S1_6', '기사문해변', this);"></td>
                </tr>
                <tr>
                    <th>서피비치</th>
                    <td><?=fnBusPointArr2023("오후_서피비치", $shopseq, 0)?><br>
                        <font color="red"><?=fnBusPointArr2023("오후_서피비치", $shopseq, 1)?></font>
                    </td>
                    <td><input type="button" class="bd_btn mapviewid" style="padding-top:4px;" value="지도" onclick="fnBusMap('오후', 'S1_7', '서피비치', this);"></td>
                </tr>
            </tbody>
        </table>

        <table view="tbBus3" class="et_vars" style="display: none;">
            <tbody>
                <tr>
                    <td height="28" style="border: 0px solid #DDD;"><b>★ 도착정류장<br><span style="padding-left:30px;"><?=fnBusPointList('busPoint_End')?></span></b></td>
                </tr>
            </tbody>
        </table>
        
        <table view="tbBus4" class="et_vars" style="display: none;">
            <colgroup>
                <col style="width:90px;">
                <col style="width:auto;">
                <col style="width:58px;">
            </colgroup>
            <tbody>
                <tr>
                    <td colspan="3" height="28"><b>★ [서울행] 저녁 출발 셔틀버스</b></td>
                </tr>
                <tr>
                    <th style="text-align:center;"></th>
                    <th style="text-align:center;">탑승장소 및 시간</th>
                    <th style="text-align:center;">위치</th>
                </tr>
                <tr>
                    <th>남애3리</th>
                    <td><?=fnBusPointArr2023("저녁_남애3리", $shopseq, 0)?><br>
                        <font color="red"><?=fnBusPointArr2023("저녁_남애3리", $shopseq, 1)?></font>
                    </td>
                    <td><input type="button" class="bd_btn mapviewid" style="padding-top:4px;" value="지도" onclick="fnBusMap('저녁', 'S1_2', '남애3리', this);"></td>
                </tr>
                <tr>
                    <th>인구해변</th>
                    <td><?=fnBusPointArr2023("저녁_인구해변", $shopseq, 0)?><br>
                        <font color="red"><?=fnBusPointArr2023("저녁_인구해변", $shopseq, 1)?></font>
                    </td>
                    <td><input type="button" class="bd_btn mapviewid" style="padding-top:4px;" value="지도" onclick="fnBusMap('저녁', 'S1_3', '인구해변', this);"></td>
                </tr>
                <tr>
                    <th>죽도해변</th>
                    <td><?=fnBusPointArr2023("저녁_죽도해변", $shopseq, 0)?><br>
                        <font color="red"><?=fnBusPointArr2023("저녁_죽도해변", $shopseq, 1)?></font>
                    </td>
                    <td><input type="button" class="bd_btn mapviewid" style="padding-top:4px;" value="지도" onclick="fnBusMap('저녁', 'S1_4', '죽도해변', this);"></td>
                </tr>
                <tr>
                    <th>기사문해변</th>
                    <td><?=fnBusPointArr2023("저녁_기사문해변", $shopseq, 0)?><br>
                        <font color="red"><?=fnBusPointArr2023("저녁_기사문해변", $shopseq, 1)?></font>
                    </td>
                    <td><input type="button" class="bd_btn mapviewid" style="padding-top:4px;" value="지도" onclick="fnBusMap('저녁', 'S1_6', '기사문해변', this);"></td>
                </tr>
                <tr>
                    <th>서피비치</th>
                    <td><?=fnBusPointArr2023("저녁_서피비치", $shopseq, 0)?><br>
                        <font color="red"><?=fnBusPointArr2023("저녁_서피비치", $shopseq, 1)?></font>
                    </td>
                    <td><input type="button" class="bd_btn mapviewid" style="padding-top:4px;" value="지도" onclick="fnBusMap('저녁', 'S1_7', '서피비치', this);"></td>
                </tr>
            </tbody>
        </table>

        <table view="tbBus4" class="et_vars" style="display: none;">
            <tbody>
                <tr>
                    <td height="28" style="border: 0px solid #DDD;"><b>★ 도착정류장<br><span style="padding-left:30px;"><?=fnBusPointList('busPoint_End')?></span></b></td>
                </tr>
            </tbody>
        </table>
    </div>

    <img style="max-width:100%;display:none;padding-bottom:10px;" id="mapimg" src="https://actrip.cdn1.cafe24.com/act_bus/Y1_1.JPG">

    <iframe scrolling="no" frameborder="0" id="ifrmBusMap" name="ifrmBusMap" style="width:100%;height:450px;display:none;"></iframe>
</div>