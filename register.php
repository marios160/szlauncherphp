<?php
$content = trim(file_get_contents("php://input"));
$decoded = json_decode($content, true);
$token = $decoded['token'];

if (empty($token)) {
    echo json_encode(array('access' => "denided"));
    return;
}
if (!checkToken()) {
    echo json_encode(array('access' => "denided"));
    return;
}

$conn = new mysqli("localhost", "root", "XedeX160!", "igi2");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$rawcdk = generateCDKey($conn);
$cdkey =  strtoupper(md5($rawcdk));

$sql = "INSERT INTO user (email, cdkey, raw_cdkey, register_data, password, nick, activated, status, ip) "
        . "VALUES ('jones@igi2.pl', '$cdkey','$rawcdk', '"
        . date("Y-m-d H:i:s") . "', 'jonespass','SZJones', '1', '0','"
        . $_SERVER['REMOTE_ADDR'] . "' );";
$result = $conn->query($sql);

if (empty($conn->error)) {
    $userId = mysqli_insert_id($conn);
    $response = array(
        'id' => $userId,
        'cdkey' => $rawcdk
    );
    echo json_encode($response);
} else {
    echo json_encode(array('error' => $conn->error));
}


$conn->close();
return;

function generateCDKey($conn) {
    $characters = '123456789ABCDEFGHJKMNPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $var = true;
    $cdkey = '';
    do {
        for ($i = 0; $i < 4; $i++) {
            $cdkey .= $characters[rand(0, $charactersLength - 1)];
        }
        $cdkey .= "-";
        for ($i = 0; $i < 4; $i++) {
            $cdkey .= $characters[rand(0, $charactersLength - 1)];
        }
        $cdkey .= "-";
        for ($i = 0; $i < 4; $i++) {
            $cdkey .= $characters[rand(0, $charactersLength - 1)];
        }
        $cdkey .= "-";
        for ($i = 0; $i < 4; $i++) {
            $cdkey .= $characters[rand(0, $charactersLength - 1)];
        }
        $sql = "SELECT * FROM user WHERE cdkey LIKE '" . md5($cdkey) . "'";
        $result = $conn->query($sql);

        if ($result->num_rows == 0) {
            $var = false;
        } else {
            $cdkey = '';
        }
    } while ($var);
    return $cdkey;
}

function checkToken() {

    return true;
}
