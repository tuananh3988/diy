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

	require_once dirname(__FILE__).'/../../lib/kappa/loader.php';
	require_once dirname(__FILE__).'/../../lib/Goutte//vendor/autoload.php';
	require_once dirname(__FILE__).'/../../lib/kappa/monst.php';
	date_default_timezone_set('Asia/Tokyo');
	$GLOBALS['DB'] = new DB();
	$GLOBALS['MONST'] = new MONST();
	$client  = new Goutte\Client();
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
               WHERE deleted = 0";
    $param = array();
    $questData = $GLOBALS['DB'] -> getRows($SQL,$param);
    $GLOBALS['questResult'] = $questData['data'];
    
    
    unset($data);
	for(;;){
        if(!$GLOBALS['insert']){
            sleep(10);
        }
        else{
            sleep(5);
        }
        $GLOBALS['insert']   = false;
        $base = $client->request('GET', 'http://monst-multi.net/');
        $targetSelector = '.bbs-list';
        $base->filter($targetSelector)->each(function ($node) {
            $date = $node->text();
            $date = explode("モンストマルチ掲示板｜投稿時間：", $date);
            $date = $date[1];
            $date = trim($date);
            $date = str_replace("時", ":", $date);
            $date = str_replace("分", ":", $date);
            $date = date("Y/m/d ").$date."00";
            $GLOBALS['date'] = $date;
            
            
            $detailURL = $node->attr('onclick');
            $detailURL = str_replace("location.href='", "", $detailURL);
            $detailURL = str_replace("'", "", $detailURL);
            $GLOBALS['insertData'] = array();
            $client2  = new Goutte\Client();
            $detail = $client2->request('GET', 'http://monst-multi.net'.$detailURL);
            $detail->filter('td')->each(function ($detailNode) {
                if(($detailNode->attr('title')) 
                    && !strpos($detailNode->attr('title'), "クエスト")
                    && !strpos($detailNode->attr('title'), "募集内容")
                    && !strpos($detailNode->attr('title'), "投稿日時")){
    
                        $GLOBALS['insertData'][] = trim($detailNode->text());
/*
                        
                        if(count($GLOBALS['insertData'])==3){
                            $GLOBALS['insertData'][2] = str_replace("/ ", " ", $GLOBALS['insertData'][2]);
                        }
*/
                }
            });
            $url = "";
            $detail->filter('a')->each(function ($detailNode) {
                if(($detailNode->attr('target')) ){
                    $GLOBALS['url'] = 'http://monst-multi.net'.trim($detailNode->attr('href'));
                    $default_opts = array(
                        'http' => array(
                            'method'=>"GET",
                            'header'=>"User-Agent: Mozilla/5.0 (iPhone; CPU iPhone OS 8_0_2 like Mac OS X) AppleWebKit/600.1.4 (KHTML, like Gecko) Version/8.0 Mobile/12A405 Safari/600.1.4\n",
                        )
                    );
                     
                    stream_context_get_default($default_opts);
                    $header_data = get_headers($GLOBALS['url'], true);
                    if(isset($header_data['Location'])){
                        $original_url = $header_data['Location'];
                        if(is_array($original_url)){
                            $original_url = end($original_url);
                        }
                    }else{
                        $original_url = '';
                    }
                    $GLOBALS['url'] = $original_url;
                }
            });
            
            if($GLOBALS['url']!=""){
                $created = strtotime(($GLOBALS['date']))-50;
                
                $keys = parse_url($GLOBALS['url'] );
                $keys = $keys['query'];
                $keys = str_replace("pass_code=", "", $keys);
//                $keys = $keys . $created;
                $SQL = "SELECT
                            COUNT(1) cnt
                        FROM dtb_list
                        WHERE original_key = ?";
                $param = array($keys);
                $check = $GLOBALS['DB'] -> getRow($SQL,$param);
                $check = $check['data'];
                
                if($check['cnt']==0){
                    $mokuteki = 1;
                    for($x=0;$x<count($GLOBALS['mokuteki']);$x++){
                        if(strpos($GLOBALS['insertData'][1], $GLOBALS['mokuteki'][$x]['text']) !== FALSE){
                            $mokuteki = $GLOBALS['mokuteki'][$x]['mtb_mokuteki_id'];
                            break;
                        }
                    }
                    
                    $mtb_quest_id = -1;
                    $mtb_quest_image = "";
                    $endQuest     = 0;
                    for($i=0;$i<count($GLOBALS['questResult']);$i++){
                        if($GLOBALS['questResult'][$i]['default'] == 1){
                            $endQuest = $GLOBALS['questResult'][$i]['id'];
                            $mtb_quest_image = $GLOBALS['questResult'][$i]['big_icon'];
                            continue;
                        }
                        $searchData = $GLOBALS['questResult'][$i]['search'];
                        $searchData = json_decode($searchData,true);
                        for($j=0;$j<count($searchData);$j++){
                            if(strpos($GLOBALS['insertData'][1],$searchData[$j]['name'])!== false ){
                                $mtb_quest_id = $GLOBALS['questResult'][$i]['id'];
                                if($GLOBALS['questResult'][$i]['big_icon']==""){
                                    $mtb_quest_image = $searchData[$j]['icon'];
                                }
                                else{
                                    $mtb_quest_image = $GLOBALS['questResult'][$i]['big_icon'];
                                }
                                break 2;
                            }
                        }
                    }
                    
                    if($mtb_quest_id <0 ){
                        $mtb_quest_id = $endQuest;
                    }
                    
                    $monstID = $GLOBALS['MONST']->monst_multi_decode($keys);
                    //不正
                    if($monstID==0){
                        var_dump("ID不正",$monstID);
                        continue;
                    }
                    
                    $SQL = "UPDATE dtb_list
                                SET deleted = 1
                            WHERE monst_id = ?";
                    $param = array($monstID);
                    $status = $GLOBALS['DB'] -> insData($SQL,$param);
                    
                    $SQL = "INSERT INTO dtb_list (mtb_user_id,quest_name,text,url,scheme,monst_id,mtb_mokuteki_id,mtb_quest_id,icon,mtb_site_id,point,end_time,click,original_key,created)
                                VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
                    $param = array(-1,$GLOBALS['insertData'][1],$GLOBALS['insertData'][0],$GLOBALS['url'],$keys,$monstID,$mokuteki,$mtb_quest_id,$mtb_quest_image,2,90-(time()-$created),$created+280,0,$keys . $created,$created);
                    $status = $GLOBALS['DB'] -> insData($SQL,$param);
                            
                    $SQL = "SELECT
                                id
                            FROM dtb_monst_user_list_count
                            WHERE monst_id = ?
                            AND   mtb_site_id = ?";
                    $monstCheckParam = array($monstID,2);
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
                        $param = array($monstID,2,1,time());
                        $status = $GLOBALS['DB'] -> insData($SQL,$param);
                    }
                    

                    
                    $SQL = "SELECT
                                id
                            FROM lst_site
                            WHERE mtb_site_id = ?
                            AND   date        = ?";
                    $superData = date("Y-m-d 00:00:00");
                    $param = array(2,$superData);
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
                        $param = array(2,$superData,1);
                        $status = $GLOBALS['DB'] -> insData($SQL,$param);
                    }
                    
                    if($status){
                        $GLOBALS['insert'] = true;
                        var_dump("insert");
                    }
                }
            }
        });
    }
?>