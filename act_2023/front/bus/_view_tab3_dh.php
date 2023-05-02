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
                <col style="width:33%;">
                <col style="width:33%;">
                <col style="width:34%;">
            </colgroup>
            <tbody>
                <tr>
                    <th colspan="2" style="text-align: center;">
                        <strong style="line-height:2;">
                            ★ 서울 → 동해
                        </strong>
                    </th>
                    <th style="text-align: center;">
                        <strong style="line-height:2;">
                            ★ 동해 → 서울
                        </strong>
                    </th>
                </tr>
                <tr>
                    <td style="text-align:center;"><input type="button" class="bd_btn" btnpoint="point" style="padding-top:4px;background:#1973e1;color:#fff;" value="사당선" onclick="fnBusPoint(this);"></td>
                    <td style="text-align:center;"><input type="button" class="bd_btn" btnpoint="point" style="padding-top:4px;" value="종로선" onclick="fnBusPoint(this);"></td>
                    <td style="text-align:center;"><input type="button" class="bd_btn" btnpoint="point" style="padding-top:4px;" value="동해 서울행" onclick="fnBusPoint(this);"></td>
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
                    <td colspan="3" height="28"><b>★ [동해행] 사당선 출발 셔틀버스</b></td>
                </tr>
                <tr>
                    <th style="text-align:center;"></th>
                    <th style="text-align:center;">탑승장소 및 시간</th>
                    <th style="text-align:center;">위치</th>
                </tr>
                <tr>
                    <th>신도림</th>
                    <td><?=fnBusPointArr("YSa_신도림", 0)?><br>
                        <font color="red"><?=fnBusPointArr("YSa_신도림", 1)?></font>
                    </td>
                    <td><input type="button" class="bd_btn mapviewid" style="padding-top:4px;" value="지도" onclick="fnBusMap('Y', 1, 1, '신도림', this);"></td>
                </tr>
                <tr>
                    <th>대림역</th>
                    <td><?=fnBusPointArr("YSa_대림역", 0)?><br>
                        <font color="red"><?=fnBusPointArr("YSa_대림역", 1)?></font>
                    </td>
                    <td><input type="button" class="bd_btn mapviewid" style="padding-top:4px;" value="지도" onclick="fnBusMap('Y', 2, 1, '대림역', this);"></td>
                </tr>
                <tr>
                    <th>사당역</th>
                    <td><?=fnBusPointArr("YSa_사당역", 0)?><br>
                        <font color="red"><?=fnBusPointArr("YSa_사당역", 1)?></font>
                    </td>
                    <td><input type="button" class="bd_btn mapviewid" style="padding-top:4px;" value="지도" onclick="fnBusMap('Y', 4, 1, '사당역', this);"></td>
                </tr>
                <tr>
                    <th>강남역</th>
                    <td><?=fnBusPointArr("YSa_강남역", 0)?><br>
                        <font color="red"><?=fnBusPointArr("YSa_강남역", 1)?></font>
                    </td>
                    <td><input type="button" class="bd_btn mapviewid" style="padding-top:4px;" value="지도" onclick="fnBusMap('Y', 5, 1, '강남역', this);"></td>
                </tr>
                <tr>
                    <th>종합운동장역</th>
                    <td><?=fnBusPointArr("YSa_종합운동장역", 0)?><br>
                        <font color="red"><?=fnBusPointArr("YSa_종합운동장역", 1)?></font>
                    </td>
                    <td><input type="button" class="bd_btn mapviewid" style="padding-top:4px;" value="지도" onclick="fnBusMap('Y', 6, 1, '종합운동장역', this);"></td>
                </tr>
            </tbody>
        </table>

        <table view="tbBus1" class="et_vars">
            <tbody>
                <tr>
                    <td height="28" style="border: 0px solid #DDD;"><b>★ 도착정류장<br><span style="padding-left:30px;">금진해변 &gt; 대진항 &gt; 솔.동해점</span></b></td>
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
                    <td colspan="3" height="28"><b>★ [동해행] 종로선 출발 셔틀버스</b></td>
                </tr>
                <tr>
                    <th style="text-align:center;"></th>
                    <th style="text-align:center;">탑승장소 및 시간</th>
                    <th style="text-align:center;">위치</th>
                </tr>
                <tr>
                    <th>합정역</th>
                    <td><?=fnBusPointArr("YJo_합정역", 0)?><br>
                        <font color="red"><?=fnBusPointArr("YJo_합정역", 1)?></font>
                    </td>
                    <td><input type="button" class="bd_btn mapviewid" style="padding-top:4px;" value="지도" onclick="fnBusMap('Y', 2, 2, '합정역', this);"></td>
                </tr>
                <tr>
                    <th>종로3가역</th>
                    <td><?=fnBusPointArr("YJo_종로3가역", 0)?><br>
                        <font color="red"><?=fnBusPointArr("YJo_종로3가역", 1)?></font>
                    </td>
                    <td><input type="button" class="bd_btn mapviewid" style="padding-top:4px;" value="지도" onclick="fnBusMap('Y', 3, 2, '종로3가역', this);"></td>
                </tr>
                <tr>
                    <th>건대입구</th>
                    <td><?=fnBusPointArr("YJo_건대입구", 0)?><br>
                        <font color="red"><?=fnBusPointArr("YJo_건대입구", 1)?></font>
                    </td>
                    <td><input type="button" class="bd_btn mapviewid" style="padding-top:4px;" value="지도" onclick="fnBusMap('Y', 5, 2, '건대입구', this);"></td>
                </tr>
                <tr>
                    <th>종합운동장역</th>
                    <td><?=fnBusPointArr("YJo_종합운동장역", 0)?><br>
                        <font color="red"><?=fnBusPointArr("YJo_종합운동장역", 1)?></font>
                    </td>
                    <td><input type="button" class="bd_btn mapviewid" style="padding-top:4px;" value="지도" onclick="fnBusMap('Y', 6, 1, '종합운동장역', this);"></td>
                </tr>
            </tbody>
        </table>

        <table view="tbBus2" class="et_vars" style="display: none;">
            <tbody>
                <tr>
                    <td height="28" style="border: 0px solid #DDD;"><b>★ 도착정류장<br><span style="padding-left:30px;">금진해변 &gt; 대진항 &gt; 솔.동해점</span></b></td>
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
                    <td colspan="3" height="28"><b>★ [서울행] 서울행 출발 셔틀버스</b></td>
                </tr>
                <tr>
                    <th style="text-align:center;"></th>
                    <th style="text-align:center;">탑승장소 및 시간</th>
                    <th style="text-align:center;">위치</th>
                </tr>
                <tr>
                    <th>솔.동해점</th>
                    <td><?=fnBusPointArr("AE2_솔.동해점", 0)?><br>
                        <font color="red"><?=fnBusPointArr("AE2_솔.동해점", 2)?></font>
                    </td>
                    <td><input type="button" class="bd_btn mapviewid" style="padding-top:4px;" value="지도" onclick="fnBusMap('A', 1, 1, '솔.동해점', this);"></td>
                </tr>
                <tr>
                    <th>대진해변</th>
                    <td><?=fnBusPointArr("AE2_대진해변", 0)?><br>
                        <font color="red"><?=fnBusPointArr("AE2_대진해변", 2)?></font>
                    </td>
                    <td><input type="button" class="bd_btn mapviewid" style="padding-top:4px;" value="지도" onclick="fnBusMap('A', 2, 1, '대진해변', this);"></td>
                </tr>
                <tr>
                    <th>금진해변</th>
                    <td><?=fnBusPointArr("AE2_금진해변", 0)?><br>
                        <font color="red"><?=fnBusPointArr("AE2_금진해변", 2)?></font>
                    </td>
                    <td><input type="button" class="bd_btn mapviewid" style="padding-top:4px;" value="지도" onclick="fnBusMap('A', 3, 1, '금진해변', this);"></td>
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
    </div>

    <img style="max-width:100%;display:none;padding-bottom:10px;" id="mapimg" src="https://actrip.cdn1.cafe24.com/act_bus/Y1_1.JPG">

    <iframe scrolling="no" frameborder="0" id="ifrmBusMap" name="ifrmBusMap" style="width:100%;height:450px;display:none;"></iframe>
</div>