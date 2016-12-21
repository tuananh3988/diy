<?PHP
$url = "http://api.monster-strike.com/matching/list";
//$url = "http://lmst.kari.pw/tmp/special/jj.php";
    $postString ="..matching_mask..latitude.@$.......longitude.@$.......pass_code.378963683.try_cnt..history_members.....";
    
    $header = array(
        "POST:/matching/list HTTP/1.1",
        "Host:api.monster-strike.com",
        "Accept:*/*",
        "Accept-Encoding:gzip",
        "Content-Type:application/x-msg-pack",
        "User-Agent:ms/5.5.0 (iPhone7,1; iOS 9.2.1)",
        "X-DS-NOTIFY:1",
        "X-DS-SESSION:d2655ea7ad5dc6a8aa184aefadf4e90a6c00ec58a5b89c2bd591ee45325a7ec3",
        "X-DS-RequestHash-Ver:3",
        "X-DS-RequestHash:9021d3efd4d29282021dcfe55da0ee94d3fbb77dc0242fa76426133e4645908a",
        "Content-Length:103"
    );

    $options = array(
      'http' => array(
        'method' => 'POST',
        'header' => implode("\r\n", $header),
        "content" => $postString,
      ),
    );
    
    $context  = stream_context_create($options);
    $jsonData = file_get_contents($url, false, $context);
die(var_dump($http_response_header));
    
?>