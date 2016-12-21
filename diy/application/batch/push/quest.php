<?PHP
	require_once dirname(__FILE__).'/../../lib/kappa/loader.php';
	date_default_timezone_set('Asia/Tokyo');
	$DB = new DB();
	
	$checkDate  = (int)date("i");
	$halfStage  = false;
	$showDate   = "";
	//30分ステージ
	if($checkDate>30){
    	$targetDate = date("Y-m-d H:00:00",strtotime("1 hours"));
    	$showDate   = (int)date("H",strtotime("1 hours"))."時";
    	$halfStage  = false;
    }
    //ぴったりステージ
    else{
    	$targetDate = date("Y-m-d H:30:00");
    	$showDate   = (int)date("H")."時30分";
    	$halfStage  = true;
    }
	$targetDate = strtotime($targetDate);
	$SQL = "SELECT
	            *
	        FROM mtb_quest
	        WHERE mtb_quest.start_time = ?
	        AND   mtb_quest.type       LIKE '%降臨%'
	        AND   mtb_quest.deleted    = 0";
    $param = array($targetDate);
    $result = $DB -> getRows($SQL,$param);
    
    if(!isset($result['data'])){
        die('nothing');
    }
    
    $sendMessage = "";
    for($i=0;$i<count($result['data']);$i++){
        if($result['data'][$i]['monster_name'] == "レッドリドラ" && $result['data'][$i]['monster_id'] == "131"){
            continue;
        }
        if($result['data'][$i]['monster_name'] == "レッドリドラ"){
            continue;
        }
        $sendMessage .= "「".$result['data'][$i]['monster_name']."」"; 
    }
    if($sendMessage!=""){
        
    	$sendAray = array('userid'  => 0,
    					  'message' => $showDate."から".$sendMessage."が降臨!!\nBESTを使ってマルチで攻略!!",
    					  'badge'   => (int)0,
    					  'test'    => false);
    //die(var_dump("php ".dirname(__FILE__)."/../../lib/ios/push.php '" . serialize($sendAray) . "' > /dev/null &"));
        system("php ".dirname(__FILE__)."/../../lib/ios/push.php '" . serialize($sendAray) . "' > /dev/null &");
    }
?>