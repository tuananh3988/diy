<?PHP
    $jsons = file_get_contents("http://monstdata.gamdom.biz/GetGuerrillaTime/get/");
	$saveFileName = dirname(__FILE__)."/../../../htdocs/tmp/alerm/file.json";
	$st = file_put_contents( $saveFileName ,$jsons);
var_dump($st);
?>