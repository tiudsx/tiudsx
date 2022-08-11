<!DOCTYPE html>
<html>
<head>
    <title>Document</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>

        $(function(){

        });

        function fnMakeTable() {
            
            $("#html_2").val($("#html_1").val().replace(/<!---->/gi,""));
            $("#divCopy").html($("#html_2").val());

            var addHtml = "";
            
            addHtml += '<table width="100%" border="1" id="td_select">';
            $("#divCopy .el-table__row").each(function(){
                
                if ($(this).find("button").length > 0) {

                    addHtml += '<tr name="' + $(this).find("td").eq(3).find(".cell").text() + '">';
                    addHtml += '<td style="mso-data-placement:same-cell;">';
                    addHtml += $(this).find("td").eq(1).find(".cell").text(); //이름
                    addHtml += '</td>';
                    addHtml += '<td style="mso-data-placement:same-cell;">';
                    addHtml += $(this).find("td").eq(3).find(".cell").text(); //연락처
                    addHtml += '</td>';
                    addHtml += '<td style="mso-data-placement:same-cell;">';
                    addHtml += '1';
                    addHtml += '</td>';
                    addHtml += '</tr>';
                }
            });
            addHtml += '</table>';
            $("#divSet").html(addHtml);
            fnChk();
        }

        function fnChk() {
            
            var aryInfo = [];
            var chkCnt = 0;
            $("#divSet tr").each(function(){
                
                //하나 이상일때
                if ($("#divSet tr[name='" + $(this).attr("name") + "']").length > 1) {

                    if (aryInfo.length <= 0) {
                        aryInfo.push($(this).attr("name"));
                    }
                    else{
                        chkCnt = 0;
                        for (var i = 0; i < aryInfo.length; i++) {
                            if (aryInfo[i] == $(this).attr("name")) {
                                chkCnt++;
                            } 
                        }

                        if (chkCnt <= 0) {
                            aryInfo.push($(this).attr("name"));
                        }

                    }
                }
                

            });

            var itemCnt = 0;

            $.each(aryInfo,function(index, item){

                itemCnt = $("#divSet tr[name='" + item + "']").length;

                $("#divSet tr[name='" + item + "']").eq(0).find("td").eq(2).text(itemCnt);

                for (var i = itemCnt; i < 1; i--) {
                    $("#divSet tr[name='" + item + "']").eq(i).remove();
                }

            });

        }


    </script>

</head>
<body>
    <div style="width: 800px;height: 100px;">
        <div><label>el-table__body</label></div>
        <div>
            <textarea id="html_1"></textarea>            
        </div>
        <div style="display: none;">
            <textarea id="html_2"></textarea>
        </div>
        
        <button onclick="fnMakeTable()">생성</button>
    </div>

    <div id="divCopy" style="display: none;"></div>
    <div style="width: 500px;" id="divSet"></div>

</body>
</html>