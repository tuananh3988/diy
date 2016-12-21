<?PHP
	//テストフラグ
	define('TEST',1);
    if(!isset($_REQUEST['request']) && !isset($_REQUEST['uuid'])){
		header("HTTP/1.0 403 Forbidden");
		die();
    }
    else{
        if(isset($_REQUEST['request'])){
            $data = $_REQUEST['request'];
            $data = json_decode(urldecode( $data),true);
        }
        else{
            $data = $_REQUEST;
        }
    }
	require_once dirname(__FILE__).'/../application/lib/kappa/rooter.php';  
?>