<?PHP
$context = stream_context_create(
  array(
    "https" => array(
      "proxy" => "tcp://127.0.01:9050",
      "request_fulluri" => TRUE,
    )
  ));
$data = file_get_contents('https://www.cman.jp/network/support/go_access.cgi', FALSE, $context); 
die(var_dump($data));

?>