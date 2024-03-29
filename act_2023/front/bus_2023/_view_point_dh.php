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
                    <td colspan="3" height="28"><b>★ [동해행] 서울출발 셔틀버스</b></td>
                </tr>
                <tr>
                    <th style="text-align:center;"></th>
                    <th style="text-align:center;">탑승장소 및 시간</th>
                    <th style="text-align:center;">위치</th>
                </tr>

                <?if($arrSa["신도림역"]){?>
                <tr>
                    <th>신도림역</th>
                    <td><?=fnBusPointArr2023("동해_신도림역", $shopseq, 0)?><br>
                        <font color="red"><?=fnBusPointArr2023("동해_신도림역", $shopseq, 1)?></font>
                    </td>
                    <td><input type="button" class="bd_btn mapviewid" style="padding-top:4px;" value="지도" onclick="fnBusMap('S', 1, 1, '신도림역', this);"></td>
                </tr>
                <?}?>

                <?if($arrSa["사당역"]){?>
                <tr>
                    <th>사당역</th>
                    <td><?=fnBusPointArr2023("동해_사당역", $shopseq, 0)?><br>
                        <font color="red"><?=fnBusPointArr2023("동해_사당역", $shopseq, 1)?></font>
                    </td>
                    <td><input type="button" class="bd_btn mapviewid" style="padding-top:4px;" value="지도" onclick="fnBusMap('S', 2, 1, '사당역', this);"></td>
                </tr>
                <?}?>

                <?if($arrSa["올림픽공원역"]){?>
                <tr>
                    <th>올림픽공원역</th>
                    <td><?=fnBusPointArr2023("동해_올림픽공원역", $shopseq, 0)?><br>
                        <font color="red"><?=fnBusPointArr2023("동해_올림픽공원역", $shopseq, 1)?></font>
                    </td>
                    <td><input type="button" class="bd_btn mapviewid" style="padding-top:4px;" value="지도" onclick="fnBusMap('S', 3, 1, '올림픽공원역', this);"></td>
                </tr>
                <?}?>
            </tbody>
        </table>
    <?
    }
    if(count($arrSa) > 0){
    ?>
        <table view="tbBus2" class="et_vars">
            <tbody>
                <tr>
                    <td height="28" style="border: 0px solid #DDD;"><b>★ 도착정류장<br><span style="padding-left:30px;"><?=fnBusPointList('busPoint_End_dh')?></span></b></td>
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
                    <td colspan="3" height="28"><b>★ [서울행] 서울복귀 셔틀버스</b></td>
                </tr>
                <tr>
                    <th style="text-align:center;"></th>
                    <th style="text-align:center;">탑승장소 및 시간</th>
                    <th style="text-align:center;">위치</th>
                </tr>

            <?if(count($arrS2) > 0){?>

                <?if($arrS2["솔.동해점"]){?>
                <tr>
                    <th>솔.동해점</th>
                    <td><?=fnBusPointArr2023("오후_솔.동해점", $shopseq, 0)?><br>
                        <font color="red"><?=fnBusPointArr2023("오후_솔.동해점", $shopseq, 1)?></font>
                    </td>
                    <td><input type="button" class="bd_btn mapviewid" style="padding-top:4px;" value="지도" onclick="fnBusMap('E', 1, 1, '솔.동해점', this);"></td>
                </tr>
                <?}?>

                <?if($arrS2["대진해변"]){?>
                <tr>
                    <th>대진해변</th>
                    <td><?=fnBusPointArr2023("오후_대진해변", $shopseq, 0)?><br>
                        <font color="red"><?=fnBusPointArr2023("오후_대진해변", $shopseq, 1)?></font>
                    </td>
                    <td><input type="button" class="bd_btn mapviewid" style="padding-top:4px;" value="지도" onclick="fnBusMap('E', 2, 1, '대진해변', this);"></td>
                </tr>
                <?}?>

                <?if($arrS2["나인비치"]){?>
                <tr>
                    <th>망상 나인비치</th>
                    <td><?=fnBusPointArr2023("오후_나인비치", $shopseq, 0)?><br>
                        <font color="red"><?=fnBusPointArr2023("오후_나인비치", $shopseq, 1)?></font>
                    </td>
                    <td><input type="button" class="bd_btn mapviewid" style="padding-top:4px;" value="지도" onclick="fnBusMap('E', 3, 1, '나인비치', this);"></td>
                </tr>
                <?}?>

                <?if($arrS2["금진해변"]){?>
                <tr>
                    <th>금진해변</th>
                    <td><?=fnBusPointArr2023("오후_금진해변", $shopseq, 0)?><br>
                        <font color="red"><?=fnBusPointArr2023("오후_금진해변", $shopseq, 1)?></font>
                    </td>
                    <td><input type="button" class="bd_btn mapviewid" style="padding-top:4px;" value="지도" onclick="fnBusMap('E', 4, 1, '금진해변', this);"></td>
                </tr>
                <?}?>

                <?if($arrS2["서프홀릭"]){?>
                <tr>
                    <th>금진 서프홀릭</th>
                    <td><?=fnBusPointArr2023("오후_서프홀릭", $shopseq, 0)?><br>
                        <font color="red"><?=fnBusPointArr2023("오후_서프홀릭", $shopseq, 1)?></font>
                    </td>
                    <td><input type="button" class="bd_btn mapviewid" style="padding-top:4px;" value="지도" onclick="fnBusMap('E', 5, 1, '서프홀릭', this);"></td>
                </tr>
                <?}?>

                <?if($arrS2["브라보서프"]){?>
                <tr>
                    <th>금진 브라보서프</th>
                    <td><?=fnBusPointArr2023("오후_브라보서프", $shopseq, 0)?><br>
                        <font color="red"><?=fnBusPointArr2023("오후_브라보서프", $shopseq, 1)?></font>
                    </td>
                    <td><input type="button" class="bd_btn mapviewid" style="padding-top:4px;" value="지도" onclick="fnBusMap('E', 6, 1, '브라보서프', this);"></td>
                </tr>
                <?}?>

            <?}else{?>

                <?if($arrS5["솔.동해점"]){?>
                <tr>
                    <th>솔.동해점</th>
                    <td><?=fnBusPointArr("AE5_솔.동해점", 0)?><br>
                        <font color="red"><?=fnBusPointArr("AE5_솔.동해점", 3)?></font>
                    </td>
                    <td><input type="button" class="bd_btn mapviewid" style="padding-top:4px;" value="지도" onclick="fnBusMap('A', 1, 1, '솔.동해점', this);"></td>
                </tr>
                <?}?>

                <?if($arrS5["대진해변"]){?>
                <tr>
                    <th>대진해변</th>
                    <td><?=fnBusPointArr("AE5_대진해변", 0)?><br>
                        <font color="red"><?=fnBusPointArr("AE5_대진해변", 3)?></font>
                    </td>
                    <td><input type="button" class="bd_btn mapviewid" style="padding-top:4px;" value="지도" onclick="fnBusMap('A', 2, 1, '대진해변', this);"></td>
                </tr>
                <?}?>

                <?if($arrS5["금진해변"]){?>
                <tr>
                    <th>금진해변</th>
                    <td><?=fnBusPointArr("AE5_금진해변", 0)?><br>
                        <font color="red"><?=fnBusPointArr("AE5_금진해변", 3)?></font>
                    </td>
                    <td><input type="button" class="bd_btn mapviewid" style="padding-top:4px;" value="지도" onclick="fnBusMap('A', 3, 1, '금진해변', this);"></td>
                </tr>
                <?}?>

            <?}?>

            </tbody>
        </table>

        <table view="tbBus3" class="et_vars">
            <tbody>
                <tr>
                    <td height="28" style="border: 0px solid #DDD;"><b>★ 도착정류장<br><span style="padding-left:30px;"><?=fnBusPointList('busPoint_End2')?></span></b></td>
                </tr>
            </tbody>
        </table>
    <?}?>
    </div>

    <img style="max-width:100%;display:none;padding-bottom:10px;" id="mapimg" src="https://actrip.cdn1.cafe24.com/act_bus/Y1_1.JPG">

    <iframe scrolling="no" frameborder="0" id="ifrmBusMap" name="ifrmBusMap" style="width:100%;height:450px;display:none;"></iframe>
</div>