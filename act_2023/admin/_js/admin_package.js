function fnSearchPack() {



    if ($j("#selPack").val() == "") {
        alert("패키지를 선택해 주세요.");
        return;
    }
    else if ($j("#packDate").val() == "") {
        alert("패키지 일자를 선택해 주세요.");
        return;
    }

    alert("패키지 조회");



}