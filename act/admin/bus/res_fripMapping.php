<!DOCTYPE html>
<html>
<head>
    <title>Document</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>

        $(function(){

        });

        function fnMakeTable() {
            
            //복사된 html을 가공 table[class='el-table__body']
            $("#html_2").val($("#html_1").val().replace(/<!---->/gi,""));
            $("#divCopy").html($("#html_2").val());
            $("#divCopy").html($("#divCopy").find("table[class='el-table__body']").html());

            //Json 인스턴스
            var objList = new Array();
            var objValue = new Object();

            //html 생성
            var addHtml = '<table width="100%" border="1" id="td_select">';
            $("#divCopy .el-table__row").each(function(){

                objValue = new Object();

                addHtml += '<tr name="' + $(this).find("td").eq(3).find(".cell").text() + '">';
                
                addHtml += '<td style="mso-data-placement:same-cell;">';
                addHtml += $(this).find("td").eq(1).find(".cell").text(); //이름
                addHtml += '</td>';
                objValue.name = $(this).find("td").eq(1).find(".cell").text(); //이름

                addHtml += '<td style="mso-data-placement:same-cell;">';
                addHtml += $(this).find("td").eq(2).find(".cell").text(); //성별
                addHtml += '</td>';
                objValue.genser = $(this).find("td").eq(2).find(".cell").text(); //성별

                addHtml += '<td style="mso-data-placement:same-cell;">';
                addHtml += $(this).find("td").eq(3).find(".cell").text(); //연락처
                addHtml += '</td>';
                objValue.tel = $(this).find("td").eq(3).find(".cell").text(); //연락처

                addHtml += '<td style="mso-data-placement:same-cell;">';
                addHtml += $(this).find("td").eq(4).find(".cell").text(); //아이템명
                addHtml += '</td>';
                objValue.item = $(this).find("td").eq(4).find(".cell").text(); //아이템명

                addHtml += '<td style="mso-data-placement:same-cell;">';
                addHtml += $(this).find("td").eq(5).find(".cell").text(); //추가정보
                addHtml += '</td>';
                objValue.addinfo = $(this).find("td").eq(5).find(".cell").text(); //추가정보

                addHtml += '<td style="mso-data-placement:same-cell;">';
                addHtml += $(this).find("td").eq(6).find(".cell").text(); //예약상태
                addHtml += '</td>';
                objValue.state = $(this).find("td").eq(6).find(".cell").text(); //예약상태

                addHtml += '<td style="mso-data-placement:same-cell;">';
                
                if ($(this).find("button").length > 0) {
                    addHtml += $(this).find("button").text(); //액션
                    objValue.btn = $(this).find("button").text(); //액션
                }
                else{
                    addHtml += 'none'; //액션
                    objValue.btn = 'none'; //액션
                }
                
                addHtml += '</td>';

                addHtml += '<td style="mso-data-placement:same-cell;">';
                addHtml += '1';
                addHtml += '</td>';
                addHtml += '</tr>';

                objList.push(objValue);
            });
            addHtml += '</table>';
            
            $("#divSet").html(addHtml);

            alert(JSON.stringify(objList));

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
    <br>
    <div style="width: 80%;" id="divSet"></div>
</body>
</html>