<?php
$content = trim(file_get_contents("php://input"));
$decoded = json_decode($content, true);

$id = $decoded['id'];
$cdkey = $decoded['cdkey'];
$nick = $decoded['nick'];
$ip = $_SERVER['REMOTE_ADDR'];

if (empty($id) || empty($cdkey)) {
    echo json_encode(array('access' => "denided"));
    return;
}

$conn = new mysqli("localhost", "root", "XedeX160!", "igi2");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM user WHERE cdkey LIKE '$cdkey' AND id LIKE '$id'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $sql = "INSERT INTO ipUsers (id_user, ip, date, nick) "
            . "VALUES ('$id', '$ip', '" . date("Y-m-d H:i:s") . "','$nick' );";
    $result = $conn->query($sql);

    $sql = "UPDATE user SET status='2', ip='$ip' WHERE id LIKE '$id' AND cdkey LIKE '$cdkey'";
    $result = $conn->query($sql);
    echo json_encode(array('access' => 'granted'));
} else {
    echo json_encode(array('access' => 'denided'));
}
return;

