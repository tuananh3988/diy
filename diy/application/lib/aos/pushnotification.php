<?php

date_default_timezone_set('Asia/Tokyo');
require_once __DIR__ . '/push.php'; //=======Include File of push because we pass value it return the json
$push = new Push();
require_once dirname(__FILE__) . '/../kappa/DB.php';
$DB = new DB();
if (!$argc > 0) {
    return;
}
$data = unserialize($argv[1]);

$sendUserid = $data['userid'];
$sendMessage = $data['message'];

$SQL = "SELECT
                uuid
        FROM mtb_user_uuid
        WHERE mtb_user_id = ?";
$param = array($sendUserid);
$uuid = $DB->getRow($SQL, $param);
$uuid = $uuid['data']['uuid'];

$SQL = "SELECT
                token
        FROM mtb_token
        WHERE send_error = 0 AND type = 2 AND uuid = ?
        ORDER BY id DESC ";
$pushToken = $DB->getRows($SQL, array($uuid));
$pushToken = $pushToken['data'];
if(count($pushToken) == 0){
        die('miss Connect');
}

$registrationIds = array();
for($i=0;$i<count($pushToken);$i++){
    $registrationIds[] = $pushToken[$i]['token'];
}
//=======
// optional payload
$payload = array();
$payload['team'] = 'India';
$payload['score'] = '5.6';
//========

$push->setTitle("Diy");  //.... set title for show on notification

$push->setMessage($sendMessage); //.... set message for show on notification

$push->setImage('');
$push->setIsBackground(FALSE);
$push->setPayload($payload);
$json = $push->getPush();
//========== this function put device token and json parmeters

sendMultiple($registrationIds, $json);


function send($to, $message) {

    $fields = array(
        'to' => $to,
        'data' => $message,
    );

    //====call function to send notification
    sendPushNotification($fields);
}

function sendMultiple($registration_ids, $message) {
    $fields = array(
        'registration_ids' => $registration_ids,
        'data' => $message,
    );

    return sendPushNotification($fields);
}

function sendPushNotification($fields) {



    // Set POST variables
    $url = 'https://fcm.googleapis.com/fcm/send';

    $headers = array(
        'Authorization: key=AIzaSyAS9qZXwjk3AnmFIpf1EYuuQ5lKlEaA8w4', //=== there you 
        'Content-Type: application/json'
    );
    // Open connection
    $ch = curl_init();

    // Set the url, number of POST vars, POST data
    curl_setopt($ch, CURLOPT_URL, $url);

    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Disabling SSL Certificate support temporarly
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

    // Execute post
    $result = curl_exec($ch);
    if ($result === FALSE) {
        die('Curl failed: ' . curl_error($ch));
    }

    // Close connection
    curl_close($ch);
    echo json_encode($result);
}

?>