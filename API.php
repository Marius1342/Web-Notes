<?php
header('Content-type: application/json');
ob_start();
require_once("db.php");

ob_get_clean();
if (REFRESH == false) {
    echo '{"Status":"403"}';
    exit();
}

function SendResponse($str)
{
    echo json_encode($str);
    exit();
}


if (isset($_GET["nd"]) && isset($_GET["id"])) {
    $conn = new DB();
    $SQL = "SELECT * FROM tasks WHERE idofpr=:idofpr AND id=:id";
    $stmt = $conn->conn->prepare($SQL);
    $stmt->bindParam(":idofpr", $_GET["id"]);
    $stmt->bindParam(":id", $_GET["nd"]);
    try {
        $stmt->execute();
    } catch (PDOException $e) {
        SendResponse('{"Status":"404"}');
    }

    $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (count($row) != 1) {
        SendResponse('{"Status":"404"}');
    }
    $row = $row[0];
    $str = "<tr class='colum'><th><p class='notes' onclick='del_note(" . $row["id"] . ");'/>" . $row["name"] . "</th><th><p id='" . $row["id"] . "' class='item_id' >" . $row["contents"] . "</p> <a class='edit' onclick='edit(" . $row["id"] . ", this);' >Edit</a></th></tr>";
    $arr = ["Status" => "202", "tr" => $str];
    SendResponse($arr);
}
//Get Project nodes
if (isset($_GET["id"])) {
    $conn = new DB();
    $SQL = "SELECT id FROM tasks WHERE idofpr=:idofpr";
    $stmt = $conn->conn->prepare($SQL);
    $stmt->bindParam(":idofpr", $_GET["id"]);
    try {
        $stmt->execute();
    } catch (PDOException $e) {
        SendResponse('{"Status":"404"}');
    }
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $ids = array();
    foreach ($result as $row) {
        array_push($ids, $row["id"]);
    }
    $arr = ["Status" => "202", "Length" => count($ids), "id" => $ids];
    SendResponse($arr);
}
?>
<!---!>