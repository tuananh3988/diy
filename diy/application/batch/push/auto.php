<?PHP
    
    
	require_once dirname(__FILE__).'/../../lib/kappa/loader.php';
	date_default_timezone_set('Asia/Tokyo');
	$DB = new DB();

    $SQL = "SELECT
                id
            FROM mtb_user
            WHERE id in (56910,56774,56935,57693,58814,56862,57259,51113,58333,48784,56504,52677,57780,29139,56378,56450,57995,40042,59135,56456,1)";
//    $data = $DB->getRows($SQL,array());
//    $data = $data['data'];

//for($i=0;$i<count($data);$i++){
//for($i=0;$i<1;$i++){
//"お問い合わせ頂いた内容にメールにてご回答させていただきました",
    	$sendAray = array('userid'  => 0,//$data[$i]['id'],//1,
    					  'message' => "BESTを使ってさくっとマルチに参加しよう！！
クシナダ零もベガも今行くしか無い！！",
    					  'badge'   => (int)0,
    					  'test'    => false);
//die(var_dump("php ".dirname(__FILE__)."/../../lib/ios/push.php '" . serialize($sendAray) . "' > /dev/null &"));
        system("php ".dirname(__FILE__)."/../../lib/ios/push.php '" . serialize($sendAray) . "' > /dev/null &");
//    }
?>