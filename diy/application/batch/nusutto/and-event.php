<?PHP
	require_once dirname(__FILE__).'/../../lib/kappa/loader.php';
	date_default_timezone_set('Asia/Tokyo');
	$DB = new DB();
	$url = "http://monstdata.gamdom.biz/HomeInfo/getHomeInfoWithPickup";
    $insert = true;
    $iconURL    = "http://gamewith.jp.edgesuite.net/article_tools/monst/gacha/[ICON].jpg";
    $bugIconURL = "http://img.monst.appbank.net/images/monster/big/[ICON].png";
    
    $postString = array("info" => '{"os":"ios","bundle_id":"com.ana.quizmaru"}');
    $postString = http_build_query($postString, "", "&");
    
    $header = array(
        "Content-Type: application/x-www-form-urlencoded",
        "Content-Length: ".strlen($postString),
        "User-Agent: Mozilla/5.0 (iPhone; CPU iPhone OS 8_0_2 like Mac OS X) AppleWebKit/600.1.4 (KHTML, like Gecko) Version/8.0 Mobile/12A405 Safari/600.1.4\n"
    );

    $options = array(
      'http' => array(
        'method' => 'POST',
        'header' => $header,
        "content" => $postString,
      ),
    );
    
    $lastWhere  = "''";
    
    $context  = stream_context_create($options);
    $jsonData = file_get_contents($url, false, $context);
    $jsonData = json_decode($jsonData,true);
    
    $jsonData = $jsonData['data_event_info'];
    
    if(count($jsonData)>0){
        $SQL = "TRUNCATE TABLE dtb_quest ";
        $DB -> insData($SQL,array());
        
//        $SQL = "TRUNCATE TABLE mtb_quest ";
        if(date("H")>0){
            $SQL = "UPDATE mtb_quest
                        SET deleted = 1
                    WHERE sort < 2";
            $DB -> insData($SQL,array());
        }
        else{
            $SQL = "TRUNCATE TABLE mtb_quest ";
            $DB -> insData($SQL,array());
        }
    }
    for($i=0;$i<count($jsonData);$i++){
        $sort = -1;
        
        $data = $jsonData[$i];
        if(strpos($data['quest_category'], "超絶") !== FALSE){
            $sort= 0;
        }
        if(strpos($data['quest_category'], "爆絶") !== FALSE){
            $sort= 0;
        }
        
        if($data['quest_category'] == "降臨"){
            $sort= 1;
        }
        if($sort < 0){
            $sort = 2;
        }
        
        
        $date = $data['date_string'];
        $date = str_replace("(ｽﾄﾗｲｶｰ別)", "", $date);
        $date = explode("〜", $date);
        
        $startData = $date[0];
        $endData   = $date[1];


        
        $startData = str_replace("時",":",$startData);
        $endData   = str_replace("時",":",$endData);
        
        $check     = explode("/", $startData);
        if(strlen($check[0])==1){
            $startData = "0".$startData;
        }
        $check     = explode(" ", $check[1]);
        if(strlen($check[0])==1){
            $startData = str_replace("/","/0",$startData);
        }
        $check    = explode(":", $check[1]);
        if(strlen($check[0])==1){
            $startData = str_replace(" "," 0",$startData);
        }
        
        if(strlen($endData) < 5){
            $super = explode(" ", $startData);
            $endData = $super[0]." ".$endData;
        }
        
        $check     = explode("/", $endData);
        if(strlen($check[0])==1){
            $endData = "0".$endData;
        }
        if(isset($check[1])){
            $check     = explode(" ", $check[1]);
            if(strlen($check[0])==1){
                $endData = str_replace("/","/0",$endData);
            }
        }
        if(isset($check[1])){
            $check    = explode(":", $check[1]);
            if(strlen($check[0])==1){
                $endData = str_replace(" "," 0",$endData);
            }
        }
        
        if($startData[strlen($startData)-1] == ":"){
            $startData .= "00:00:00";
        }
        else{
            $startData .= ":00:00:00";
        }
        
        if($endData[strlen($endData)-1] == ":"){
            $endData .= "00:00:00";
        }
        else{
            $endData .= ":00:00:00";
        }

        $startData = "2016/".substr($startData, 0,14);
        $endData   = "2016/".substr($endData, 0,14);

        $startData = strtotime($startData);
        $endData   = strtotime($endData);
        if($startData>$endData){
            $endData = $startData + 7200;
        }
        
        $questName = $data['quest_name'];
var_dump($questName);

        
        $SQL = "SELECT
                    *
                FROM mtb_quest_save
                WHERE value = ?";
        $param = array($questName);
        $saveData  = $DB -> getRow($SQL,$param);
        $saveData = $saveData['data'];
        
        $SQL = "SELECT
                    id
                FROM mtb_quest
                WHERE value      = ?
                AND   start_time = ?";
        $param = array($data['quest_name'],$startData);
        $check = $DB -> getRow($SQL,$param);
        $check = $check['data'];

//unset($check['id']);
        if(!isset($check['id'])){
            if(!isset($saveData['id'])){
                
                $detailUrl  = "http://monstdata.gamdom.biz/GetEventInfo/get?quest_name=".$data['quest_name'];
                $context    = stream_context_create($options);
                $detailData = file_get_contents($detailUrl, false, $context);
                $detailData = json_decode($detailData,true);
                $detailData = $detailData['quest_detail'];
                
                $serachKey  = explode("･", $data['quest_name']);
                $serachKey  = $serachKey[0];
                $searchStr  = array(array('name'=>$serachKey)
                                         );
                $searchStr = json_encode($searchStr);
                if($detailData['monster_id']!="131"){
                    $smallIcon = str_replace("[ICON]", $detailData['monster_id'], $iconURL);
                    $bigIcon   = str_replace("[ICON]", $detailData['monster_id'], $bugIconURL);
                }
                else{
                    $smallIcon = "http://lmst.kari.pw/tmp/special/0.png";
    //                $smallIcon = "http://lmst.kari.pw/tmp/special/12782190_903241836441374_443049364_n.jpg";
                    $bigIcon   = "";
                }
                
                if($data['quest_name'] == "千代に舞う光遁の折紙絵巻"){
                    $smallIcon = "http://lmst.kari.pw/tmp/special/12782190_903241836441374_443049364_n.jpg";
                    $bigIcon   = "http://pbs.twimg.com/media/CRqgDqGUwAAyQkj.jpg";
                }
                $questName      = $data['quest_name'];
                $questType      = $detailData['quest_type'];
                $monsterID      = $detailData['monster_id'];
                $monsterName    = $detailData['monster_name'];
                
                $SQL = "INSERT INTO mtb_quest_save (sort,search_icon,icon,big_icon,value,type,monster_id,monster_name,start_time,end_time,created,search)
                            VALUES(?,?,?,?,?,?,?,?,?,?,?,?)";
                $param = array($sort,$smallIcon,$smallIcon,$bigIcon,$questName,$questType,$monsterID,$monsterName,$startData,$endData,time(),$searchStr);
                $status = $DB -> insData($SQL,$param);
            }
            else{
                $sort           = $saveData['sort'];
                $smallIcon      = $saveData['search_icon'];
                $bigIcon        = $saveData['big_icon'];
                $questName      = $saveData['value'];
                $questType      = $saveData['type'];
                $monsterID      = $saveData['monster_id'];
                $monsterName    = $saveData['monster_name'];
                $searchStr      = $saveData['search'];
                
                
                if((int)$saveData['start_time'] != (int)$startData|| (int)$saveData['end_time'] != (int)$endData){
                    $SQL            = "UPDATE mtb_quest_save 
                                        SET start_time = ?,
                                            end_time   = ?
                                       WHERE value = ?";
                    $param          = array($startData,$endData,$questName);
                    $DB -> insData($SQL,$param);
                }
            }
            $SQL = "SELECT
                        id
                    FROM mtb_quest
                    WHERE value      = ?";
            $param = array($data['quest_name']);
            $insertCheck = $DB -> getRow($SQL,$param);
            $insertCheck = $insertCheck['data'];
            //ある
            if(isset($insertCheck['id'])){
                $SQL = "UPDATE mtb_quest
                            SET sort            = ?,
                                search_icon     = ?,
                                icon            = ?,
                                big_icon        = ?,
                                value           = ?,
                                type            = ?,
                                monster_id      = ?,
                                monster_name    = ?,
                                start_time      = ?,
                                end_time        = ?,
                                search          = ?
                        WHERE id = ?";
                $param = array($sort,$smallIcon,$smallIcon,$bigIcon,$questName,$questType,$monsterID,$monsterName,$startData,$endData,$searchStr,$insertCheck['id']);
                $DB -> insData($SQL,$param);
                $check = $insertCheck;
            }
            //無し
            else{
                $SQL = "INSERT INTO mtb_quest (sort,search_icon,icon,big_icon,value,type,monster_id,monster_name,start_time,end_time,created,search)
                            VALUES(?,?,?,?,?,?,?,?,?,?,?,?)";
                $param = array($sort,$smallIcon,$smallIcon,$bigIcon,$questName,$questType,$monsterID,$monsterName,$startData,$endData,time(),$searchStr);
                $status = $DB -> insData($SQL,$param);
                $lastWhere .= ",'".$questName."'";
                
                $SQL = "SELECT
                            id
                        FROM mtb_quest
                        ORDER BY id DESC
                        LIMIT 1";
                $check = $DB -> getRow($SQL);
                $check = $check['data'];
            }
            
        
        }
        else{
            $SQL = "SELECT
                        *
                    FROM mtb_quest_save
                    WHERE value = ?";
            $param = array($data['quest_name']);
            $superSaveData = $DB -> getRow($SQL,$param);
            $superSaveData = $superSaveData['data'];
            
            
            $lastWhere .= ",'".$data['quest_name']."'";
            $SQL = "UPDATE mtb_quest
                        SET deleted     = 0,
                            search_icon = ?,
                            icon        = ?,
                            big_icon    = ?,
                            start_time  = ?,
                            end_time    = ?,
                            search      = ?
                    WHERE id = ?";
            $param = array($superSaveData['search_icon'],$superSaveData['icon'],$superSaveData['big_icon'],$superSaveData['start_time'],$superSaveData['end_time'],$superSaveData['search'],$check['id']);
            $status = $DB -> insData($SQL,$param);
        }
    }
    $SQL = "SELECT
                *
            FROM mtb_quest_save
            WHERE value not IN (".$lastWhere.")
            AND   mtb_quest_save.start_time - 7200 < UNIX_TIMESTAMP()
		    AND   mtb_quest_save.end_time          > UNIX_TIMESTAMP()";
    $check = $DB -> getRows($SQL);
    $check = $check['data'];

    for($i=0;$i<count($check);$i++){
        $SQL = "SELECT
                    *
                FROM mtb_quest
                WHERE value = ?";
        $param = array($check[$i]['value']);
        $check2 = $DB -> getRow($SQL,$param);
        $check2 = $check2['data'];
        if(isset($check2['id'])){
            $SQL = "UPDATE mtb_quest
                        SET deleted = 0
                    WHERE value = ?";
            $param = array($check2['value']);
            $DB -> insData($SQL,$param);
        }
        else{
            $sort           = $check[$i]['sort'];
            $smallIcon      = $check[$i]['search_icon'];
            $bigIcon        = $check[$i]['big_icon'];
            $questName      = $check[$i]['value'];
            $questType      = $check[$i]['type'];
            $monsterID      = $check[$i]['monster_id'];
            $monsterName    = $check[$i]['monster_name'];
            $searchStr      = $check[$i]['search'];
            $startData      = $check[$i]['start_time'];
            $endData        = $check[$i]['end_time'];
                
            $SQL = "INSERT INTO mtb_quest (sort,search_icon,icon,big_icon,value,type,monster_id,monster_name,start_time,end_time,created,search)
                        VALUES(?,?,?,?,?,?,?,?,?,?,?,?)";
            if(isset($questName)){            
                $param = array($sort,$smallIcon,$smallIcon,$bigIcon,$questName,$questType,$monsterID,$monsterName,$startData,$endData,time(),$searchStr);
                $status = $DB -> insData($SQL,$param);
            }
        }
    }
    
    
    $SQL = "SELECT
                value,
                type,
                sort,
                CASE WHEN search_icon = '' THEN icon ELSE search_icon END sicon,
                icon,
                big_icon,
                IFNULL(search,'') search 
            FROM mtb_default_quest
            WHERE (type = ?
            OR    type = -1)
            AND   deleted = 0 
            ORDER BY sort ASC,super_sort DESC";
    $param = array(date("w"));
    $defaultData = $DB -> getRows($SQL,$param);
    $defaultData = $defaultData['data'];
    $startTime   = strtotime(date("Y/m/d 00:00:00"));
    $endTime     = strtotime(date("Y/m/d 23:59:59")); 
    
    $SQL = "SELECT
                COUNT(1) cnt
            FROM mtb_quest
            WHERE id > 1000";
    $check = $DB -> getRow($SQL);
    $check = $check['data'];
    if($check['cnt'] == 0){
        for($i=0;$i<count($defaultData);$i++){
            $oneData = $defaultData[$i];
            $df      = 0;
            if($oneData['search']==""){
                $df = 1;
            }
            if($oneData['value']==""){
                continue;
            }
            $SQL = "INSERT INTO mtb_quest (id,sort,search_icon,icon,big_icon,value,type,monster_id,monster_name,start_time,end_time,created,search,`default`)
                        VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
            $param = array(1000+$i,$oneData['sort'],$oneData['sicon'],$oneData['icon'],$oneData['big_icon'],$oneData['value'],'df',0,"",$startTime,$endTime,time(),$oneData['search'],$df);
            $status = $DB -> insData($SQL,$param);
        }
    }
    var_dump("ok");
    
?>