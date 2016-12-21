<?PHP
date_default_timezone_set('Asia/Tokyo');
require_once dirname(__FILE__).'/../kappa/DB.php';
require_once dirname(__FILE__).'/ApnsPHP/Autoload.php';
	$DB = new DB();
    //並列処理
    if(!$argc>0){
        return;
    }
	$data = unserialize($argv[1]);

	$sendUserid  = $data['userid'];
	$sendMessage = $data['message'];
	$sendBadge   = $data['badge'];
	$test        = $data['test'];
//	$mail        = $data['mail'];

	$certificate = array('file' => '',
						 'type' => 1);
	$test=false;
RESTART:
	if($test){
		$certificate['file'] = "dev";
		$certificate['type'] = 1;
		
		//db切り替え
//		$DB->connectDevelopment();
	}
	else{
		$certificate['file'] = "dis";
		$certificate['type'] = 0;
	}
	
	$where = "";
	//一斉送信
	if(!is_array($sendUserid) && $sendUserid==0){
		$param = array();
	}
	//複数発射
	else if(is_array($sendUserid)){
		$sendUsers = array();
		for($i=0;$i<count($sendUserid);$i++){
			$sendUsers[] = $sendUserid[$i]['userID'];
		}

		$SQL = "SELECT
					uuid
				FROM mtb_user
				WHERE id in (".str_repeat("?,",count($sendUserid)-1)."?)";
		$uuid  = $DB -> getRows($SQL,$sendUsers);
		
		$where = " AND ( 1=2 ";
		for($i=0;$i<count($uuid['data']);$i++){
			$where .= " OR uuid = '".$uuid['data'][$i]['uuid']."' ";
		}
		$where .= " )";
		$param = array();
	}
	//単体送信
	else{
		$SQL = "SELECT
					uuid
				FROM mtb_user
				WHERE id = ?";
		$param = array($sendUserid);
		$uuid  = $DB -> getRow($SQL,$param);
		$uuid  = $uuid['data']['uuid'];
		
		
		$param = array($uuid);
		
		$where = " AND uuid = ?";
	}
	
	$SQL = "SELECT
				token
			FROM mtb_token
			WHERE send_error = 0 ".$where."
			ORDER BY id DESC ";
	$pushToken = $DB -> getRows($SQL,$param);
	$pushToken = $pushToken['data'];
	
	//送信データ無し
	if(count($pushToken) == 0){
		die('miss Connect');
	}
	
	//多重なので、股並列
	if(count($pushToken) > 500){

		$pushTokens = array_chunk($pushToken, 500);
		for($i=0;$i<count($pushTokens);$i++){		
			$sendAray = array('tokens'  => $pushTokens[$i],
							  'message' => $sendMessage,
							  'badge'   => (int)$sendBadge,
							  'test'    => $test);
//die(var_dump("php ".dirname(__FILE__)."/push_next.php '" . serialize($sendAray) . "' > /dev/null &"));
		    system("php ".dirname(__FILE__)."/push_next.php '" . serialize($sendAray) . "' > /dev/null &");
		}
		die();
	}

	$pushSuper = new ApnsPHP_Push($certificate['type'], dirname(__FILE__)."/../../configs/ios/".$certificate['file'].".pem");
	$pushSuper->setRootCertificationAuthority(dirname(__FILE__).'/../../configs/ios/entrust_root_certification_authority.pem');
	$pushSuper->connect();
	
	
	try{
		for($i=0;$i<count($pushToken);$i++){
			$token   = $pushToken[$i]['token'];	
			$message = new ApnsPHP_Message($token);//device token
			$message->setText($sendMessage);
//			$message->setBadge((int)$sendBadge);
//			$message->setExpiry(30);
			$message->setCustomProperty('best',1);
//			$message->setSound("bingbong.aiff");
			
			$pushSuper->add($message);
			$pushSuper->send();	
		}

	}catch (ApnsPHP_Message_Exception $e){
		$e->getMessage();
	}
	$pushSuper->disconnect();

	$aErrorQueue = $pushSuper->getErrors();
	if (!empty($aErrorQueue)) {
		if(!$test){
    		if(!$test){
        		$test = true;
        		GOTO RESTART;
    		}
			$missToken = $aErrorQueue[1]['MESSAGE']->_aDeviceTokens;
			$SQL = "UPDATE mtb_token
						SET send_error = 1
					WHERE token = ?";
			for($i=0;$i<count($missToken);$i++){
				$param = array($missToken[$i]);
				$DB -> insData($SQL,$param);
			}
		}
	}
?>
