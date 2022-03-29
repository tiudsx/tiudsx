<?
include __DIR__.'/../../db.php';
$selDate = $_REQUEST["selDate"];
$busgubun = $_REQUEST["bus"];

$select_query = "SELECT * FROM `AT_PROD_BUS` WHERE use_yn = 'Y' AND busgubun = '$busgubun' AND busdate = '$selDate'";
$result = mysqli_query($conn, $select_query);
$rowMain = mysqli_fetch_array($result);

echo $rowMain["busseat"];
?>