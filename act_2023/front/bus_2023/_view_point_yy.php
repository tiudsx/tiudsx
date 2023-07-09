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
                        <font style="font-size:12px;"><strong>사전 신청하지 않는 정류장은 정차 및 하차하지 않습니다.</strong></font>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="bd" style="padding:10px 0 0px 0;">
    
    <?if(count($arrSa) > 0){?>
        <table view="tbBus1" class="et_vars">
            <colgroup>
                <col style="width:90px;">
                <col style="width:auto;">
                <col style="width:58px;">
            </colgroup>
            <tbody>
                <tr>
                    <td colspan="3" height="28"><b>★ [양양행] 사당선 출발 셔틀버스</b></td>
                </tr>
                <tr>
                    <th style="text-align:center;"></th>
                    <th style="text-align:center;">탑승장소 및 시간</th>
                    <th style="text-align:center;">위치</th>
                </tr>

                <?if($arrSa["신도림"]){?>
                <tr>
                    <th>신도림</th>
                    <td><?=fnBusPointArr("YSa_신도림", 0)?><br>
                        <font color="red"><?=fnBusPointArr("YSa_신도림", 1)?></font>
                    </td>
                    <td><input type="button" class="bd_btn mapviewid" style="padding-top:4px;" value="지도" onclick="fnBusMap('Y', 1, 1, '신도림', this);"></td>
                </tr>
                <?}?>

                <?if($arrSa["대림역"]){?>
                <tr>
                    <th>대림역</th>
                    <td><?=fnBusPointArr("YSa_대림역", 0)?><br>
                        <font color="red"><?=fnBusPointArr("YSa_대림역", 1)?></font>
                    </td>
                    <td><input type="button" class="bd_btn mapviewid" style="padding-top:4px;" value="지도" onclick="fnBusMap('Y', 2, 1, '대림역', this);"></td>
                </tr>
                <?}?>

                <?if($arrSa["사당역"]){?>
                <tr>
                    <th>사당역</th>
                    <td><?=fnBusPointArr("YSa_사당역", 0)?><br>
                        <font color="red"><?=fnBusPointArr("YSa_사당역", 1)?></font>
                    </td>
                    <td><input type="button" class="bd_btn mapviewid" style="padding-top:4px;" value="지도" onclick="fnBusMap('Y', 4, 1, '사당역', this);"></td>
                </tr>
                <?}?>

                <?if($arrSa["강남역"]){?>
                <tr>
                    <th>강남역</th>
                    <td><?=fnBusPointArr("YSa_강남역", 0)?><br>
                        <font color="red"><?=fnBusPointArr("YSa_강남역", 1)?></font>
                    </td>
                    <td><input type="button" class="bd_btn mapviewid" style="padding-top:4px;" value="지도" onclick="fnBusMap('Y', 5, 1, '강남역', this);"></td>
                </tr>
                <?}?>

                <?if($arrSa["종합운동장역"]){?>
                <tr>
                    <th>종합운동장역</th>
                    <td><?=fnBusPointArr("YSa_종합운동장역", 0)?><br>
                        <font color="red"><?=fnBusPointArr("YSa_종합운동장역", 1)?></font>
                    </td>
                    <td><input type="button" class="bd_btn mapviewid" style="padding-top:4px;" value="지도" onclick="fnBusMap('Y', 6, 1, '종합운동장역', this);"></td>
                </tr>
                <?}?>
            </tbody>
        </table>
    <?
    }
    
    if(count($arrJo) > 0){
    ?>
        <table view="tbBus2" class="et_vars">
            <colgroup>
                <col style="width:90px;">
                <col style="width:auto;">
                <col style="width:58px;">
            </colgroup>
            <tbody>
                <tr>
                    <td colspan="3" height="28"><b>★ [양양행] 종로선 출발 셔틀버스</b></td>
                </tr>
                <tr>
                    <th style="text-align:center;"></th>
                    <th style="text-align:center;">탑승장소 및 시간</th>
                    <th style="text-align:center;">위치</th>
                </tr>
                
                <?if($arrJo["합정역"]){?>
                <tr>
                    <th>합정역</th>
                    <td><?=fnBusPointArr("YJo_합정역", 0)?><br>
                        <font color="red"><?=fnBusPointArr("YJo_합정역", 1)?></font>
                    </td>
                    <td><input type="button" class="bd_btn mapviewid" style="padding-top:4px;" value="지도" onclick="fnBusMap('Y', 2, 2, '합정역', this);"></td>
                </tr>
                <?}?>

                <?if($arrJo["종로3가역"]){?>
                <tr>
                    <th>종로3가역</th>
                    <td><?=fnBusPointArr("YJo_종로3가역", 0)?><br>
                        <font color="red"><?=fnBusPointArr("YJo_종로3가역", 1)?></font>
                    </td>
                    <td><input type="button" class="bd_btn mapviewid" style="padding-top:4px;" value="지도" onclick="fnBusMap('Y', 3, 2, '종로3가역', this);"></td>
                </tr>
                <?}?>

                <?if($arrJo["건대입구"]){?>
                <tr>
                    <th>건대입구</th>
                    <td><?=fnBusPointArr("YJo_건대입구", 0)?><br>
                        <font color="red"><?=fnBusPointArr("YJo_건대입구", 1)?></font>
                    </td>
                    <td><input type="button" class="bd_btn mapviewid" style="padding-top:4px;" value="지도" onclick="fnBusMap('Y', 5, 2, '건대입구', this);"></td>
                </tr>
                <?}?>

                <?if($arrJo["종합운동장역"]){?>
                <tr>
                    <th>종합운동장역</th>
                    <td><?=fnBusPointArr("YJo_종합운동장역", 0)?><br>
                        <font color="red"><?=fnBusPointArr("YJo_종합운동장역", 1)?></font>
                    </td>
                    <td><input type="button" class="bd_btn mapviewid" style="padding-top:4px;" value="지도" onclick="fnBusMap('Y', 6, 1, '종합운동장역', this);"></td>
                </tr>
                <?}?>
            </tbody>
        </table>

    <?
    }

    if(count($arrSa) > 0 || count($arrJo) > 0){
    ?>

        <table view="tbBus2" class="et_vars">
            <tbody>
                <tr>
                    <td height="28" style="border: 0px solid #DDD;"><b>★ 도착정류장<br><span style="padding-left:30px;"><?=fnBusPointList('busPoint_End_yy')?></span></b></td>
                </tr>
            </tbody>
        </table>
        <br>
    <?}
    
    if(count($arrS2) > 0 || count($arrS5) > 0){
    ?>
        <table view="tbBus3" class="et_vars">
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

                
            <?if(count($arrS2) > 0){?>

                <?if($arrS2["남애3리"]){?>
                <tr>
                    <th>남애3리</th>
                    <td><?=fnBusPointArr("SY2_남애3리", 0)?><br>
                        <font color="red"><?=fnBusPointArr("SY2_남애3리", 3)?></font>
                    </td>
                    <td><input type="button" class="bd_btn mapviewid" style="padding-top:4px;" value="지도" onclick="fnBusMap('S', 2, 1, '남애3리', this);"></td>
                </tr>
                <?}?>

                <?if($arrS2["인구해변"]){?>
                <tr>
                    <th>인구해변</th>
                    <td><?=fnBusPointArr("SY2_인구해변", 0)?><br>
                        <font color="red"><?=fnBusPointArr("SY2_인구해변", 3)?></font>
                    </td>
                    <td><input type="button" class="bd_btn mapviewid" style="padding-top:4px;" value="지도" onclick="fnBusMap('S', 3, 1, '인구해변', this);"></td>
                </tr>
                <?}?>

                <?if($arrS2["죽도해변"]){?>
                <tr>
                    <th>죽도해변</th>
                    <td><?=fnBusPointArr("SY2_죽도해변", 0)?><br>
                        <font color="red"><?=fnBusPointArr("SY2_죽도해변", 3)?></font>
                    </td>
                    <td><input type="button" class="bd_btn mapviewid" style="padding-top:4px;" value="지도" onclick="fnBusMap('S', 4, 1, '죽도해변', this);"></td>
                </tr>
                <?}?>

                <?if($arrS2["기사문해변"]){?>
                <tr>
                    <th>기사문해변</th>
                    <td><?=fnBusPointArr("SY2_기사문해변", 0)?><br>
                        <font color="red"><?=fnBusPointArr("SY2_기사문해변", 3)?></font>
                    </td>
                    <td><input type="button" class="bd_btn mapviewid" style="padding-top:4px;" value="지도" onclick="fnBusMap('S', 6, 1, '기사문해변', this);"></td>
                </tr>
                <?}?>

                <?if($arrS2["서피비치"]){?>
                <tr>
                    <th>서피비치</th>
                    <td><?=fnBusPointArr("SY2_서피비치", 0)?><br>
                        <font color="red"><?=fnBusPointArr("SY2_서피비치", 3)?></font>
                    </td>
                    <td><input type="button" class="bd_btn mapviewid" style="padding-top:4px;" value="지도" onclick="fnBusMap('S', 7, 1, '서피비치', this);"></td>
                </tr>
                <?}?>

            <?}else{?>

                <?if($arrS5["남애3리"]){?>
                <tr>
                    <th>남애3리</th>
                    <td><?=fnBusPointArr("SY5_남애3리", 0)?><br>
                        <font color="red"><?=fnBusPointArr("SY5_남애3리", 3)?></font>
                    </td>
                    <td><input type="button" class="bd_btn mapviewid" style="padding-top:4px;" value="지도" onclick="fnBusMap('S', 2, 1, '남애3리', this);"></td>
                </tr>
                <?}?>

                <?if($arrS5["인구해변"]){?>
                <tr>
                    <th>인구해변</th>
                    <td><?=fnBusPointArr("SY5_인구해변", 0)?><br>
                        <font color="red"><?=fnBusPointArr("SY5_인구해변", 3)?></font>
                    </td>
                    <td><input type="button" class="bd_btn mapviewid" style="padding-top:4px;" value="지도" onclick="fnBusMap('S', 3, 1, '인구해변', this);"></td>
                </tr>
                <?}?>

                <?if($arrS5["죽도해변"]){?>
                <tr>
                    <th>죽도해변</th>
                    <td><?=fnBusPointArr("SY5_죽도해변", 0)?><br>
                        <font color="red"><?=fnBusPointArr("SY5_죽도해변", 3)?></font>
                    </td>
                    <td><input type="button" class="bd_btn mapviewid" style="padding-top:4px;" value="지도" onclick="fnBusMap('S', 4, 1, '죽도해변', this);"></td>
                </tr>
                <?}?>

                <?if($arrS5["기사문해변"]){?>
                <tr>
                    <th>기사문해변</th>
                    <td><?=fnBusPointArr("SY5_기사문해변", 0)?><br>
                        <font color="red"><?=fnBusPointArr("SY5_기사문해변", 3)?></font>
                    </td>
                    <td><input type="button" class="bd_btn mapviewid" style="padding-top:4px;" value="지도" onclick="fnBusMap('S', 6, 1, '기사문해변', this);"></td>
                </tr>
                <?}?>

                <?if($arrS5["서피비치"]){?>
                <tr>
                    <th>서피비치</th>
                    <td><?=fnBusPointArr("SY5_서피비치", 0)?><br>
                        <font color="red"><?=fnBusPointArr("SY5_서피비치", 3)?></font>
                    </td>
                    <td><input type="button" class="bd_btn mapviewid" style="padding-top:4px;" value="지도" onclick="fnBusMap('S', 7, 1, '서피비치', this);"></td>
                </tr>
                <?}?>


            <?}?>
            </tbody>
        </table>

        <table view="tbBus3" class="et_vars">
            <tbody>
                <tr>
                    <td height="28" style="border: 0px solid #DDD;"><b>★ 도착정류장<br><span style="padding-left:30px;"><?=fnBusPointList('busPoint_End')?></span></b></td>
                </tr>
            </tbody>
        </table>
    <?}?>
    </div>

    <img style="max-width:100%;display:none;padding-bottom:10px;" id="mapimg" src="https://actrip.cdn1.cafe24.com/act_bus/Y1_1.JPG">

    <iframe scrolling="no" frameborder="0" id="ifrmBusMap" name="ifrmBusMap" style="width:100%;height:450px;display:none;"></iframe>
</div>