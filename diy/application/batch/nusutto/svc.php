<?PHP
    exec("ps aux | grep ".basename($_SERVER['PHP_SELF']), $output, $result);
    
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
    system("php ".dirname(__FILE__)."/svc2.php > /dev/null &");

	require_once dirname(__FILE__).'/../../lib/kappa/loader.php';
	require_once dirname(__FILE__).'/../../lib/Goutte//vendor/autoload.php';
	require_once dirname(__FILE__).'/../../lib/kappa/monst.php';
	date_default_timezone_set('Asia/Tokyo');
	$GLOBALS['DB'] = new DB();
	$GLOBALS['MONST'] = new MONST();
	$client  = new Goutte\Client();
	$client->setHeader('User-Agent', 'Mozilla/5.0 (iPhone; CPU iPhone OS 8_0_2 like Mac OS X) AppleWebKit/600.1.4 (KHTML, like Gecko) Version/8.0 Mobile/12A405 Safari/600.1.4');
	$GLOBALS['insert']  = true;
	
    $SQL = "SELECT
                mtb_mokuteki_id,
                text
            FROM mtb_quest_change";
    $data = $GLOBALS['DB'] -> getRows($SQL);
    $data = $data['data'];
    $GLOBALS['mokuteki'] = $data;
    
    
    $SQL    = "SELECT
                    *
               FROM mtb_quest
               WHERE deleted = 0
               AND   mtb_quest.start_time - 7200 < UNIX_TIMESTAMP()
			   AND   mtb_quest.end_time          > UNIX_TIMESTAMP() 
               ORDER BY id DESC";
    $param = array();
    $questData = $GLOBALS['DB'] -> getRows($SQL,$param);
    $GLOBALS['questResult'] = $questData['data'];

    
    unset($data);
	for(;;){
var_dump("next!");
        $GLOBALS['loopCount'] = 0;
        if(!$GLOBALS['insert']){
            sleep(1);
            $GLOBALS['loopCount'] ++;
        }
        else{
//            sleep(0);
        }
        $GLOBALS['getTime'] = time();
        $GLOBALS['insert']   = false;
        $base = $client->request('GET', 'http://xn--zckqu2gra1duc2140ehfh3i0c.com/');
        $targetSelector = '.list_01 li';
        
        $base->filter($targetSelector)->each(function ($node) {
            $data = trim($node->text());
            $data = explode("\n", $data);
            
            if(count($data) != 4){
                GOTO END;
            }
            /*
                URL分解
            */
            preg_match_all('/(「.+?」)/', $data[0], $questName);
            if(isset($questName[0][0])){
                $questName      = $questName[0][0];
                $delete         = array("「","」");
                $questName      = str_replace($delete, "", $questName);                
            }
            else{
                $questName      = "不明なクエスト";
            }
            
            preg_match_all('(https?://[-_.!~*\'()a-zA-Z0-9;/?:@&=+$,%#]+)', $data[0], $onlyURL);
            if(!isset($onlyURL[0][0])){
                GOTO END;
            }
            $onlyURL        = $onlyURL[0][0];
            $keys           = parse_url($onlyURL);
            if(!isset($keys['query'])){
                GOTO END;
            }
            $keys           = $keys['query'];
            if($keys ==""){
                continue;
            }
            $keys           = str_replace("pass_code=", "", $keys);
            
            /*
                text抽出
            */
            $text          = $data[1];
            
            /*
                日付抽出
            */
            $defaultData   = $data[3];
            $defaultData   = explode("　", $defaultData);
            $defaultData   = $defaultData[1];
            
            $date          = "2016/".$defaultData;
            
            $date = strtotime($date);
            
            if($questName == "" || $onlyURL == "" || $keys == "" || !((int)$date > 0) ){
                GOTO END;
            }
            
            $SQL = "SELECT
                        count(1) cnt
                    FROM dtb_list
                    WHERE scheme = ? 
                    AND   deleted = 0 ";
            $param = array($keys);
            $check = $GLOBALS['DB'] -> getRow($SQL,$param);
            $check = $check['data'];
            $mokuteki = 1;
            for($x=0;$x<count($GLOBALS['mokuteki']);$x++){
                if(strpos($questName, $GLOBALS['mokuteki'][$x]['text']) !== FALSE){
                    $mokuteki = $GLOBALS['mokuteki'][$x]['mtb_mokuteki_id'];
                    GOTO END;
                }
            }
            
            $mtb_quest_id = -1;
            $mtb_quest_image = "";
            $mtb_quest_big_image = "";
            $endQuest     = 0;
            for($p=0;$p<count($GLOBALS['questResult']);$p++){
                if($GLOBALS['questResult'][$p]['default'] == 1){
                    $endQuest = $GLOBALS['questResult'][$p]['id'];
                    $mtb_quest_image = $GLOBALS['questResult'][$p]['icon'];
                    continue;
                }
                $searchData = $GLOBALS['questResult'][$p]['search'];
                $searchData = json_decode($searchData,true);
                for($j=0;$j<count($searchData);$j++){
                    if(strpos($questName,$searchData[$j]['name'])!== false ){
                        $mtb_quest_id = $GLOBALS['questResult'][$p]['id'];
                        if(isset($searchData[$j]['icon']) && $searchData[$j]['icon'] !="" && $GLOBALS['questResult'][$p]['icon'] == ""){
                            $mtb_quest_image = $searchData[$j]['icon'];
                        }
                        else{
                            $mtb_quest_image = $GLOBALS['questResult'][$p]['icon'];
                        }
                   
                        if(isset($searchData[$j]['big_icon']) && $searchData[$j]['big_icon']!="" && $GLOBALS['questResult'][$p]['big_icon'] == ""){
                            $mtb_quest_big_image = $searchData[$j]['big_icon'];
                        }
                        else{
                            $mtb_quest_big_image = $GLOBALS['questResult'][$p]['big_icon'];
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

            
            if((int)time() < (int)($date + 60) ){
                $created = $GLOBALS['getTime']-$GLOBALS['loopCount'];
            }
            else{
                $created = $date;
            }
            
            
            
            $monstID = $GLOBALS['MONST']->monst_multi_decode($keys);
            //不正
            if($monstID==0){
                var_dump("ID不正",$monstID);
                GOTO END;
            }
            
            $param = array($monstID);
            $SQL = "SELECT
                        COUNT(1) cnt
                    FROM dtb_list
                    WHERE monst_id = ?";
            $mainasuPoint = $GLOBALS['DB'] -> getRow($SQL,$param);
            $mainasuPoint = $mainasuPoint['data'];
            $mainasuPoint = (int)$mainasuPoint['cnt'];
            
            
            $param = array($monstID,$questName);
            $SQL = "SELECT
                        COUNT(1) cnt
                    FROM dtb_list_other
                    WHERE monst_id = ?
                    AND   quest_name = ?";
            $mainasuPoint2 = $GLOBALS['DB'] -> getRow($SQL,$param);
            $mainasuPoint2 = $mainasuPoint['data'];
            $mainasuPoint2 = (int)$mainasuPoint['cnt'];
            $mainasuPoint  = $mainasuPoint + $mainasuPoint2;
            
            
            if($check['cnt'] > 0){
var_dump("continue ");
                GOTO END;
            }
            $param = array($monstID);
            $SQL = "UPDATE dtb_list
                        SET deleted = 1
                    WHERE monst_id = ?";
            $param = array($monstID);
            $status = $GLOBALS['DB'] -> insData($SQL,$param);


            $point = (85-$GLOBALS['loopCount']-$mainasuPoint)-(time()-$created);
var_dump($point);
            $SQL = "INSERT INTO dtb_list (mtb_user_id,quest_name,text,url,scheme,monst_id,mtb_mokuteki_id,mtb_quest_id,icon,big_icon,mtb_site_id,point,end_time,click,original_key,created)
                        VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
            $param = array(-1,$questName,$text,$onlyURL,$keys,$monstID,$mokuteki,$mtb_quest_id,$mtb_quest_image,$mtb_quest_image,3,$point,$created,0,$keys .$created,$created);
            $status = $GLOBALS['DB'] -> insData($SQL,$param);
            if($status['error']==1){
                $GLOBALS['DB'] = new DB();
                $status = $GLOBALS['DB'] -> insData($SQL,$param);
            }
            $GLOBALS['insert'] = true;
var_dump("insert");

            $SQL = "SELECT
                        id
                    FROM dtb_monst_user_list_count
                    WHERE monst_id = ?
                    AND   mtb_site_id = ?";
            $monstCheckParam = array($monstID,3);
            $monstListCount = $GLOBALS['DB'] -> getRow($SQL,$monstCheckParam);
            $monstListCount = $monstListCount['data'];
            //既存
            if(isset($monstListCount['id'])){
                $SQL = "UPDATE dtb_monst_user_list_count
                            SET count = count +1,
                                last_date = ?
                        WHERE id = ?";
                $monstCheckParam = array(time(),$monstListCount['id']);
                $GLOBALS['DB'] -> insData($SQL,$monstCheckParam);
            }
            else{
                $SQL = "INSERT INTO dtb_monst_user_list_count(monst_id,mtb_site_id,count,last_date)
                            VALUES(?,?,?,?)";
                $param = array($monstID,3,1,time());
                $status = $GLOBALS['DB'] -> insData($SQL,$param);
            }
            
            
            $SQL = "SELECT
                        id
                    FROM lst_site
                    WHERE mtb_site_id = ?
                    AND   date        = ?";
            $superData = date("Y-m-d 00:00:00");
            $param = array(3,$superData);
            $checkResult = $GLOBALS['DB'] -> getRow($SQL,$param);
            $checkResult = $checkResult['data'];
            if(isset($checkResult['id'])){
                $SQL = "UPDATE lst_site
                            SET list_count = list_count + 1
                        WHERE id = ?";
                $param = array($checkResult['id']);
                $GLOBALS['DB'] -> insData($SQL,$param);
            }
            //新規
            else{
                $SQL = "INSERT INTO lst_site(mtb_site_id,date,list_count)
                            VALUES(?,?,?)";
                $param = array(3,$superData,1);
                $status = $GLOBALS['DB'] -> insData($SQL,$param);
            }
            
END:
 $GLOBALS['loopCount'] ++;

        });
    }
?>