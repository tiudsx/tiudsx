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
                <col style="width:50%;">
                <col style="width:50%;">
            </colgroup>
            <tbody>
                <tr>
                    <th style="text-align: center;">
                        <strong style="line-height:2;">
                            ★ 서울 → 금진,동해
                        </strong>
                    </th>
                    <th style="text-align: center;">
                        <strong style="line-height:2;">
                            ★ 금진,동해 → 서울
                        </strong>
                    </th>
                </tr>
                <tr>
                    <td style="text-align:center;"><input type="button" class="bd_btn" btnpoint="point" style="padding-top:4px;background:#1973e1;color:#fff;" value="서울 출발" onclick="fnBusPoint(this);"></td>
                    <td style="text-align:center;"><input type="button" class="bd_btn" btnpoint="point" style="padding-top:4px;" value="서울 복귀" onclick="fnBusPoint(this);"></td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="bd" style="padding:10px 0 0px 0;">
    <?
    $arrPoint = fnBusPoint2023("", "", $shopseq);

    $sPoint = array();
    $ePoint = array();
    foreach($arrPoint as $key=>$value){
        if(strpos($key, "동해_") !== false){
            $sPoint[$key] = $value;
        }else if(strpos($key, "AM_") !== false){
            $ePoint[$key] = $value;
        }
    }
    ?>
        <table view="tbBus1" class="et_vars">
            <colgroup>
                <col style="width:90px;">
                <col style="width:auto;">
                <col style="width:58px;">
            </colgroup>
            <tbody>
                <tr>
                    <td colspan="3" height="28"><b>★ [금진, 동해] 서울출발 셔틀버스</b></td>
                </tr>
                <tr>
                    <th style="text-align:center;"></th>
                    <th style="text-align:center;">탑승장소 및 시간</th>
                    <th style="text-align:center;">위치</th>
                </tr>
            <?
            $i = 0;
            foreach($sPoint as $key=>$value){

            $pointName = explode("_",$key)[1];
            $pointInfo = explode("|",$value);

            $pointInfoTime = explode(":", $pointInfo[0]);
            $i++;
            ?>
                <tr>
                    <th><?=$pointName?></th>
                    <td><?=$pointInfo[1]?><br>
                        <font color="red"><?=$pointInfoTime[0]."시 ".$pointInfoTime[1]. "분"?></font>
                    </td>
                    <td><input type="button" class="bd_btn mapviewid" style="padding-top:4px;" value="지도" onclick="fnBusMap('S', <?=$i?>, 1, '<?=$pointName?>', this);"></td>
                </tr>
            <?}?>
            </tbody>
        </table>

        <table view="tbBus1" class="et_vars">
            <tbody>
                <tr>
                    <td height="28" style="border: 0px solid #DDD;"><b>★ 도착정류장<br><span style="padding-left:30px;"><?=fnBusPointList('busPoint_End_dh')?></span></b></td>
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
                    <td colspan="3" height="28"><b>★ [금진, 동해] 서울복귀 셔틀버스</b></td>
                </tr>
                <tr>
                    <th style="text-align:center;"></th>
                    <th style="text-align:center;">탑승장소 및 시간</th>
                    <th style="text-align:center;">위치</th>
                </tr>
            <?
            $i = 0;
            foreach($ePoint as $key=>$value){

            $pointName = explode("_",$key)[1];
            $pointInfo = explode("|",$value);

            $pointInfoTime = explode(":", $pointInfo[0]);
            $i++;
            ?>
                <tr>
                    <th><?=$pointName?></th>
                    <td><?=$pointInfo[1]?><br>
                        <font color="red"><?=$pointInfoTime[0]."시 ".$pointInfoTime[1]. "분"?></font>
                    </td>
                    <td><input type="button" class="bd_btn mapviewid" style="padding-top:4px;" value="지도" onclick="fnBusMap('E', <?=$i?>, 1, '<?=$pointName?>', this);"></td>
                </tr>
            <?}?>
            </tbody>
        </table>

        <table view="tbBus2" class="et_vars" style="display: none;">
            <tbody>
                <tr>
                    <td height="28" style="border: 0px solid #DDD;"><b>★ 도착정류장<br><span style="padding-left:30px;"><?=fnBusPointList('busPoint_End2')?></span></b></td>
                </tr>
            </tbody>
        </table>
    </div>

    <img style="max-width:100%;display:none;padding-bottom:10px;" id="mapimg" src="https://actrip.cdn1.cafe24.com/act_bus/Y1_1.JPG">

    <iframe scrolling="no" frameborder="0" id="ifrmBusMap" name="ifrmBusMap" style="width:100%;height:450px;display:none;"></iframe>
</div>