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

	$tokens		 = $data['tokens'];
	$sendMessage = $data['message'];
	$sendBadge   = $data['badge'];
	$test        = $data['test'];
	
//	sleep(10);

	$certificate = array('file' => '',
						 'type' => 1);
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
	$pushSuper = new ApnsPHP_Push($certificate['type'], dirname(__FILE__)."/../../configs/ios/".$certificate['file'].".pem");
	$pushSuper->setRootCertificationAuthority(dirname(__FILE__).'/../../configs/ios/entrust_root_certification_authority.pem');
	$pushSuper->connect();
	
	
	try{
		for($i=0;$i<count($tokens);$i++){
			$token   = $tokens[$i]['token'];
		
			$message = new ApnsPHP_Message($token);//device token
			$message->setText($sendMessage);
			$message->setBadge((int)0);
			$message->setExpiry(10);
			$message->setSound("bingbong.aiff");
			
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
	
	/*

	$feedback = new ApnsPHP_Feedback($certificate['type'], dirname(__FILE__)."/../../configs/ios/".$certificate['file'].".pem");
// Connect to the Apple Push Notification Feedback Service
$feedback->connect();
$aDeviceTokens = $feedback->receive();
die(var_dump($aDeviceTokens));
if (!empty($aDeviceTokens)) {
	var_dump($aDeviceTokens);
}
// Disconnect from the Apple Push Notification Feedback Service
$feedback->disconnect();
	
*/
?>
