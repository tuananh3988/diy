<?PHP
    sleep(3);
    $super = "ps aux | grep ".basename($_SERVER['PHP_SELF']);
    exec($super, $output, $result);
    
    //2個以上見つかれば中止
    if(count($output) > 1){
        for($i=0;$i<4;$i++){
            $mailProsess = $output[$i];
            for(;;){
                $mailProsess = str_replace("  "," ",$mailProsess,$count);
                if($count==0){
                    break;
                }
            }
            $mailProsess = explode(" ",$mailProsess);
            if($mailProsess[1]!=""){
                $killID      = (int)$mailProsess[1];
                if(getmypid()!=$killID)
                    exec("kill -9 ".$killID, $output, $result);
            }
        }
    }
    system("php ".dirname(__FILE__)."/and4.php > /dev/null &");
    
	require_once dirname(__FILE__).'/../../lib/kappa/loader.php';
	require_once dirname(__FILE__).'/../../lib/kappa/monst.php';
	date_default_timezone_set('Asia/Tokyo');
	$DB = new DB();
	$MONST = new MONST();
	$url = "http://rss.gamdom.biz/monsto/list_new_api.php?line_cacao=-1&from=0&to=80&purpose=&quest_name=";
    $insert = true;
    
    $SQL    = "SELECT
                    *
               FROM mtb_quest
               WHERE deleted = 0
               AND   mtb_quest.start_time - 7200 < UNIX_TIMESTAMP()
			   AND   mtb_quest.end_time          > UNIX_TIMESTAMP() 
               ORDER BY id DESC";
    $param = array();
    $questResult = $DB -> getRows($SQL,$param);
    $questResult = $questResult['data'];
    
    
    for(;;){
var_dump("start");
        $loopCount = 0;
        if(!$insert){
var_dump("sleep");
            sleep(1);
            $loopCount++;
        }
        else{
            sleep(0);
        }
        
        $header = array(
            "Accept:*/*",
            "Accept-Language:ja-jp",
            "Accept-Encoding:gzip, deflate",
            "User-Agent:%E3%83%A2%E3%83%B3%E3%82%B9%E3%83%88%E6%94%BB%E7%95%A5/6.0.4 CFNetwork/758.2.8 Darwin/15.0.0"
        );
    
        $options = array(
          'http' => array(
            'method' => 'GET',
            'header' => implode("\r\n", $header)
          ),
        );
        
        $context  = stream_context_create($options);
        $jsonData = file_get_contents($url, false, $context);
        
        $options = array(
          'http' => array(
            'method' => 'GET',
            'header' => 'User-Agent: %E3%83%A2%E3%83%B3%E3%82%B9%E3%83%88%E6%94%BB%E7%95%A5/6.0.4 CFNetwork/758.2.8 Darwin/15.0.0',
          ),
        );
        $context  = stream_context_create($options);
        $jsonData = file_get_contents($url, false, $context);
        $jsonData = json_decode($jsonData,true);
        $insert   = false;
        
        $loopCount = 5;
        if(count($jsonData)<$loopCount){
            $loopCount = count($jsonData);
        }
        for($pppp=0;$pppp<$loopCount;$pppp++){
var_dump("no:".$pppp);
            if(!isset($jsonData[$pppp])){
var_dump("break");
                break;
            }
            $thisData = $jsonData[$pppp];
            $mokuteki = 0;
            //目的変換
            switch ((int)$thisData['purpose']) {
                case 1:
                case 2:
                case 3:
                case 4:
                case 5:
                    $mokuteki = 10;
                    break;
                case 6:
                    $mokuteki = 2;
                    break;
                case 7:
                    $mokuteki =  2;
                    break;
                case 8:
                    $mokuteki = 2;
                    break;
                case 9:
                    $mokuteki = 7;
                    break;
                case 10:
                    $mokuteki = 9;
                    break;
                case 11:
                    $mokuteki = 8;
                    break;
                case 12:
                    $mokuteki = 8;
                    break;
                case 13:
                    $mokuteki = 6;
                    break;
                case 14:
                    $mokuteki = 2;
                    break;
                case 15:
                    $mokuteki = 11;
                    break;
                case 16:
                case 17:
                case 18:
                case 19:
                case 20:
                    $mokuteki = 10;
                    break;
                case 21:
                case 22:
                case 23:
                    $mokuteki = 12;
                    break;
                case 24:
                    $mokuteki = 9;
                    break;
                default:
                    $mokuteki = 1;
                    break;
            }
            $thisData['detail'] = trim($thisData['detail']);
            if($thisData['detail'] == "" || $thisData['multi_url'] == "" || $thisData['quest_name'] == ""){
//var_dump($thisData['detail'] ,$thisData['multi_url'],$thisData['quest_name']);
//var_dump("足りない");
                GOTO NEXT_LOOP;
            }
            
            //original_key
            
            $keys = parse_url($thisData['multi_url']);
            $keys = $keys['query'];
            $keys = str_replace("pass_code=", "", $keys);
            if($keys[0]=="!"){
                GOTO NEXT_LOOP;
            }
            
            if((int)strlen($keys)>12){
                GOTO NEXT_LOOP;
            }
            $cehckKeys = $keys . $thisData['id'];
            $SQL = "SELECT
                        COUNT(1) cnt
                    FROM dtb_list
                    WHERE original_key = ?";
            $param = array($cehckKeys);
            $check = $DB -> getRow($SQL,$param);
            $check = $check['data'];
            
            $created = strtotime($thisData['created']);
            $created = $created;
            
            $mtb_quest_id = -1;
            $mtb_quest_image = "";
            $mtb_quest_big_image = "";
            $endQuest     = 0;
            for($p=0;$p<count($questResult);$p++){
                if($questResult[$p]['default'] == 1){
                    $endQuest = $questResult[$p]['id'];
                    $mtb_quest_image = $questResult[$p]['icon'];
                    continue;
                }
                $searchData = $questResult[$p]['search'];
                $searchData = json_decode($searchData,true);
                for($j=0;$j<count($searchData);$j++){
                    if(strpos($thisData['quest_name'],$searchData[$j]['name'])!== false ){
                        $mtb_quest_id = $questResult[$p]['id'];
                        if(isset($searchData[$j]['icon']) && $searchData[$j]['icon'] != "" && $questResult[$p]['icon'] == ""){
                            $mtb_quest_image = $searchData[$j]['icon'];
                        }
                        else{
                            $mtb_quest_image = $questResult[$p]['icon'];
                        }
                        if(isset($searchData[$j]['big_icon']) && $searchData[$j]['big_icon'] != "" && $questResult[$p]['big_icon'] == ""){
                            $mtb_quest_big_image = $searchData[$j]['big_icon'];
                        }
                        else{
                            $mtb_quest_big_image = $questResult[$p]['big_icon'];
                        }
                        break 2;
                    }
                }
            }
            
            if($mtb_quest_id < 1 ){
                $mtb_quest_id = $endQuest;
            }
            if($mtb_quest_image==""){
                $mtb_quest_image = "http://lmst.kari.pw/tmp/special/0.png";
            }
            
            $monstID = $MONST->monst_multi_decode($keys);
            //不正
            if($monstID==0){
                var_dump("ID不正",$monstID);
                GOTO NEXT_LOOP;
            }
            
            $param = array($monstID);
            $SQL = "SELECT
                        COUNT(1) cnt
                    FROM dtb_list
                    WHERE monst_id = ?";
            $mainasuPoint = $DB -> getRow($SQL,$param);
            $mainasuPoint = $mainasuPoint['data'];
            $mainasuPoint = (int)$mainasuPoint['cnt'];
            
            $param = array($monstID,$thisData['quest_name']);
            $SQL = "SELECT
                        COUNT(1) cnt
                    FROM dtb_list_other
                    WHERE monst_id = ?
                    AND   quest_name = ?";
            $mainasuPoint2 = $DB -> getRow($SQL,$param);
            $mainasuPoint2 = $mainasuPoint['data'];
            $mainasuPoint2 = (int)$mainasuPoint['cnt'];
            $mainasuPoint  = $mainasuPoint + $mainasuPoint2;
                        
            $param = array($monstID);
            $SQL = "UPDATE dtb_list
                        SET deleted = 1
                    WHERE monst_id = ?";
            $status = $DB -> insData($SQL,$param);
            
            $point = (85-$mainasuPoint)-(time()-$created);
            $point = $point - $pppp;
            if($point<0){
                $point = 0;
            }
            if($check['cnt']>0){
var_dump("continue");
GOTO NEXT_LOOP;
            }
            $SQL = "INSERT INTO dtb_list (mtb_user_id,quest_name,text,url,scheme,monst_id,mtb_mokuteki_id,mtb_quest_id,icon,big_icon,mtb_site_id,point,end_time,click,original_key,created)
                        VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
            $param = array(-1,$thisData['quest_name'],$thisData['detail'],$thisData['multi_url'],$keys,$monstID,$mokuteki,$mtb_quest_id,$mtb_quest_image,$mtb_quest_big_image,1,$point,$created+280,0,$keys . $thisData['id'],$created);
            $status = $DB -> insData($SQL,$param);
            if($status['error']==1){
                $DB = new DB();
                $status = $DB -> insData($SQL,$param);
            }
            
            $SQL = "SELECT
                        id
                    FROM dtb_monst_user_list_count
                    WHERE monst_id = ?
                    AND   mtb_site_id = ?";
            $monstCheckParam = array($monstID,1);
            $monstListCount = $DB -> getRow($SQL,$monstCheckParam);
            $monstListCount = $monstListCount['data'];
            //既存
            if(isset($monstListCount['id'])){
                $SQL = "UPDATE dtb_monst_user_list_count
                            SET count = count +1,
                                last_date = ?
                        WHERE id = ?";
                $monstCheckParam = array(time(),$monstListCount['id']);
                $DB -> insData($SQL,$monstCheckParam);
            }
            else{
                $SQL = "INSERT INTO dtb_monst_user_list_count(monst_id,mtb_site_id,count,last_date)
                            VALUES(?,?,?,?)";
                $param = array($monstID,1,1,time());
                $status = $DB -> insData($SQL,$param);
            }
            $SQL = "SELECT
                        id
                    FROM lst_site
                    WHERE mtb_site_id = ?
                    AND   date        = ?";
            $superData = date("Y-m-d 00:00:00");
            $param = array(1,$superData);
            $checkResult = $DB -> getRow($SQL,$param);
            $checkResult = $checkResult['data'];
            if(isset($checkResult['id'])){
                $SQL = "UPDATE lst_site
                            SET list_count = list_count + 1
                        WHERE id = ?";
                $param = array($checkResult['id']);
                $DB -> insData($SQL,$param);
            }
            //新規
            else{
                $SQL = "INSERT INTO lst_site(mtb_site_id,date,list_count)
                            VALUES(?,?,?)";
                $param = array(1,$superData,1);
                $status = $DB -> insData($SQL,$param);
            }
            
            if($status['data']){
                var_dump("insert!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!");
                $insert = true;
            }
            else{
                var_dump("miss");
            }
        
NEXT_LOOP:
        }
        
    }
?>