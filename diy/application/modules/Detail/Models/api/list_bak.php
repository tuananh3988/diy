<?PHP
	class ListModel extends Model {
    	public function listDetail()
    	{
        	$SQL = "SELECT
    	                ".SQL_LIST_DATA.",
    	                ".SQL_USER_DATA."
    	            FROM dtb_list
    	            LEFT JOIN mtb_user          ON (mtb_user.id                   = dtb_list.mtb_user_id)
    	            WHERE dtb_list.id = ?
    	            AND   dtb_list.deleted = 0";
    	    $param  = array($this->_getParam('listID'));
    	    $result = $this -> getRow($SQL,$param);
    	    
    	    $this -> listJibunCheck($result);
    	    
            $param = array($this->_getParam('listID'));
    	    $SQL = "SELECT
    	                ".SQL_TAG_LIST."
    	            FROM dtb_list_tag
    	            INNER JOIN mtb_tag ON (mtb_tag.id = dtb_list_tag.mtb_tag_id)
    	            WHERE dtb_list_id = ?";
            $result['tags'] = $this -> getRows($SQL,$param);
            
            //レシピ投稿
            if($result['listType'] == 2){
                $SQL = "SELECT
                            ".SQL_LIST_RESP_DATA."
                        FROM dtb_list_detail
                        WHERE dtb_list_id = ?";
                $result['reshipi'] = $this -> getRows($SQL,$param);
                
                $SQL = "SELECT
                            ".SQL_LIST_ZAIRYO_DATA."
                        FROM dtb_list_detail2
                        WHERE dtb_list_id = ?";
                $result['zairyo'] = $this -> getRows($SQL,$param);
            }
            
            $SQL = "SELECT
    	                ".SQL_TAG_LIST."
                    FROM mtb_tag
                    WHERE sort > -1
                    ORDER BY sort ASC 
                    LIMIT 10";
            $param = array();
            $result['kanren'] = $this -> getRows($SQL,$param);
            
            $this -> addFollowStatus($result);
            $this -> listFavoriteCheck($result);
            
    	    return $result;
        	
        }
    	
	    public function listSerch()
	    {
    	    $addWhere = "";
    		if($this->_itemCheck('word')){
        		$SQL = "SELECT
        		            id
        		        FROM mtb_tag
        		        WHERE tag LIKE ?";
                $param = array("%".$this->_getParam('word')."%");
                $result = $this -> getRows($SQL,$param);
                
                $tagIDs = "";
                if(isset($result[0]['id'])){
                    for($i=0;$i<count($result);$i++){
                        $tagIDs .= $result[$i]['id'].",";
                    }
                    $tagIDs .= "0";
                }
                else{
                    $tagIDs = "";
                }
                $listwhere = "";
                if($tagIDs != ""){
                    $SQL = "SELECT
                                DISTINCT(dtb_list_id) listID
                            FROM dtb_list_tag
                            WHERE mtb_tag_id IN (".$tagIDs.")";
                    $whereAdd = $this -> getRows($SQL,$param);
                    
                    if(isset($whereAdd[0]['listID'])){
                        for($i=0;$i<count($whereAdd);$i++){
                            $listwhere .= $whereAdd[$i]['listID'].",";
                        }
                        $listwhere .= "0";
                    }
                    else{
                        $listwhere = "";
                    }
                }
                
                if($listwhere!=""){
                    $addWhere = " AND dtb_list.id IN (".$listwhere.")";
                }
                else{
                    $addWhere = " AND 1 = 2 ";
                }
                
            }
    		else{
        		$SQL = "SELECT
        		            DISTINCT(mtb_tag_id) tagID
        		        FROM dtb_user_tag
        		        WHERE mtb_user_id = ?
        		        AND   deleted     = 0";
                $param = array($this -> _userData['id']);
                $result = $this -> getRows($SQL,$param);
                $addTag = "";
                $listID = "";
                if(isset($result[0]['tagID'])){
                    $addTag = $result[0]['tagID'];
                    for($i=1;$i<count($result);$i++){
                        $addTag .= " , " . $result[$i]['tagID'];
                    }
                    
                    $SQL = "SELECT
                                DISTINCT(dtb_list_id) listID
                            FROM dtb_list_tag
                            WHERE mtb_tag_id IN (".$addTag.")";
                    $result = $this -> getRows($SQL);
                    if(isset($result[0]['listID'])){
                        $listID = $result[0]['listID'];
                        for($i=1;$i<count($listID);$i++){
                            $listID .= "," . $listID[$i]['listID'];
                        }
                    }
                    
                }
                
        		$SQL = "SELECT
        		            follow_user_id userID
        		        FROM dtb_follow
        		        WHERE mtb_user_id = ?
        		        AND   deleted     = 0";
                $param = array($this -> _userData['id']);
                $result = $this -> getRows($SQL,$param);
                $addUser = "";
                if(isset($result[0]['userID'])){
                    $addUser = $result[0]['userID'];
                    for($i=1;$i<count($result);$i++){
                        $addUser .= " , " . $result[$i]['userID'];
                    }
                }
                
                if($listID != "" || $addUser != ""){
                     $addWhere = " AND ( ";
                    if($listID!=""){
                        $addWhere .= " dtb_list.id IN (".$listID.")";
                    }
                    if($addUser == ""){
                        $addWhere .=  ")";
                    }
                    else{
                        if($listID != ""){
                            $addWhere .= " OR ";
                        }
                        $addWhere .= " dtb_list.mtb_user_id IN (".$addUser.") )";
                    }
                }
                else{
                    $addWhere = "";
                }
            }
            $limit = 20;
			$page = $this->_getParam('page');
			if($page<0){
				$page = 0;
			}
			$limit = $limit*$page . ", $limit ";
    	    $SQL = "SELECT
    	                ".SQL_LIST_DATA.",
    	                ".SQL_USER_DATA."
    	            FROM dtb_list
    	            LEFT JOIN mtb_user          ON (mtb_user.id                   = dtb_list.mtb_user_id)
    	            WHERE dtb_list.deleted = 0 ".$addWhere ."
    	            ORDER BY dtb_list.id DESC
    	            LIMIT ".$limit;
    	    $param  = array();
    	    $result = $this -> getRows($SQL,$param);
    	    if(!isset($result[0]['id'])){
        	    $SQL = "SELECT
        	                ".SQL_LIST_DATA.",
        	                ".SQL_USER_DATA."
        	            FROM dtb_list
        	            LEFT JOIN mtb_user          ON (mtb_user.id                   = dtb_list.mtb_user_id)
        	            WHERE dtb_list.deleted = 0 
        	            ORDER BY dtb_list.id DESC
        	            LIMIT ".$limit;
        	    $param  = array();
        	    $result = $this -> getRows($SQL,$param);
            }
    	    
    	    for($i=0;$i<count($result);$i++){
        	    $this -> listJibunCheck($result[$i]);
        	    $this -> listFavoriteCheck($result[$i]);
                $param = array($result[$i]['listID']);
        	    $SQL = "SELECT
        	                ".SQL_TAG_LIST."
        	            FROM dtb_list_tag
        	            INNER JOIN mtb_tag ON (mtb_tag.id = dtb_list_tag.mtb_tag_id)
        	            WHERE dtb_list_id = ?";
                $result[$i]['tags'] = $this -> getRows($SQL,$param);
            }
            
            
            $returnArray = array();
            $counting = 0;
            $returnArray = array();
            $adRand = 10;
            for($i=0;$i<count($result)+1;$i++){
                if($i % $adRand == 0 && $i > 0){
                    $nendGet = @$this -> getNendListURLTest();
                    $returnArray[] = array('listID'              => (string)-1,
                                           'listType'            => (string)99,
                                           'listImage'           => $nendGet['image_url'],
                                           'listTitle'           => $nendGet['promotion_name'],
                                           'listText'            => $nendGet['long_text'],
                                           'listIineCount'       => (string)rand(1,100),
                                           'listCommentCount'    => (string)rand(1,100),
                                           'listCreated'         => $nendGet['ad_define_texts'][0],
                                           'adURL'               => $nendGet['click_url'],
                                           'userID'              => -1,
                                           'userName'            => "",
                                           'userImage'           => "",
                                           'userBackGroundImage' => "",
                                           'listFavoriteStatus'  => 0,
                                           'jibun'               => 0,
                                           'tags'                => array(),
                                           );
                }
                else{
                    if(!isset($result[$counting]['listID'])){
                        break;
                    }
                    $returnArray[] = $result[$counting];
                    $counting++;
                }
            }
    	    return $returnArray;
        }
        public function setUnfavorite(){
            $param = array($this -> _userData['id'],$this->_getParam('listID'));
            $SQL = "UPDATE dtb_list_favorite
                                SET deleted = 1,
                                    updated = ?
                            WHERE mtb_user_id = ?
                            AND   dtb_list_id  = ?";
            $param = array(time(),$this -> _userData['id'],$this->_getParam('listID'));
            $this -> execute($SQL,$param);
            $favoriteStatus = 0;  
              
            $SQL   = "UPDATE dtb_list SET iine = iine -1 where id=?";
            $param = array($this->_getParam('listID'));
            $this -> execute($SQL,$param);
            
            $param = array($this->_getParam('listID'));        
            $SQL = "SELECT iine from dtb_list where id=?";
            $favoriteCount = $this->getRow($SQL,$param);
            return array('favoriteCount' => $favoriteCount['iine'],'favoriteStatus'=>$favoriteStatus);
        }
        
        public function setFavorite()
        {
            $param = array($this -> _userData['id'],$this->_getParam('listID'));
        	$count = $this->getCount('dtb_list_favorite',' mtb_user_id = ? AND dtb_list_id = ? AND deleted = 0 ',$param);
        	
        	$favoriteStatus = 0;
        	//登録
        	if($count==0){
                $param = array($this -> _userData['id'],$this->_getParam('listID'));
            	$count = $this->getCount('dtb_list_favorite',' mtb_user_id = ? AND dtb_list_id = ? AND deleted = 1 ',$param);
            	
            	//完全新規
            	if($count == 0){
                    $SQL   = "INSERT INTO dtb_list_favorite(mtb_user_id,dtb_list_id,created)";
                    $param = array($this -> _userData['id'],$this->_getParam('listID'),time());
                    $this -> executeIns($SQL,$param);
                    
                    $SQL = "UPDATE dtb_list
                                SET iine = iine + 1
                            WHERE id = ?";
                    $param = array($this->_getParam('listID'));
                    $this -> execute($SQL,$param);
                    
                    $SQL = "SELECT
                                mtb_user_id 
                            FROM dtb_list
                            WHERE id = ?";
                    $param = array($this->_getParam('listID'));
                    $userID = $this -> getRow($SQL,$param);
                    
                    if(isset($userID['mtb_user_id'])){
                        $SQL = "INSERT INTO dtb_infomation(type,mtb_user_id,target_user_id,dtb_list_id,created)";
                        $param = array(1,$userID['mtb_user_id'],$this -> _userData['id'],$this->_getParam('listID'),time());
                        $st =  $this -> executeIns($SQL,$param);
                        $this -> _sendPush($userID['mtb_user_id'],$this -> _userData['name'] ."さんが、あなたのレシピをお気に入り登録しました！",1,false);
                    }
                }
                //復活
                else{
                    $SQL = "UPDATE dtb_list_favorite
                                SET deleted = 0,
                                    updated = ?
                            WHERE mtb_user_id = ?
                            AND   dtb_list_id  = ?";
                    $param = array(time(),$this -> _userData['id'],$this->_getParam('listID'));
                    $this -> execute($SQL,$param);
                    
                    $SQL = "UPDATE dtb_list
                                SET iine = iine + 1
                            WHERE id = ?";
                    $param = array($this->_getParam('listID'));
                    $this -> execute($SQL,$param);
                    
                    $SQL = "SELECT
                                mtb_user_id 
                            FROM dtb_list
                            WHERE id = ?";
                    $param = array($this->_getParam('listID'));
                    $userID = $this -> getRow($SQL,$param);
                    
                    if(isset($userID['mtb_user_id'])){
                        $SQL = "UPDATE dtb_infomation
                                    SET deleted = 0
                                WHERE type           = 1
                                AND   mtb_user_id    = ?
                                AND   target_user_id = ?";
                        $param = array(1,$userID['mtb_user_id'],$this -> _userData['id'],time());
                        $st =  $this -> execute($SQL,$param);
                        $this -> _sendPush($userID['mtb_user_id'],$this -> _userData['name'] ."さんが、あなたのレシピをお気に入り登録しました！",1,false);
                    }
                }
                $favoriteStatus = 1;
            }
            //削除
            else{
                $SQL = "UPDATE dtb_list_favorite
                            SET deleted = 1
                        WHERE mtb_user_id = ?
                        AND   dtb_list_id  = ?";
                $param = array($this -> _userData['id'],$this->_getParam('listID'));
                $this -> execute($SQL,$param);
                
                $SQL = "UPDATE dtb_list
                            SET iine = iine - 1
                        WHERE id = ?";
                $param = array($this->_getParam('listID'));
                $this -> execute($SQL,$param);
                
                $SQL = "SELECT
                            mtb_user_id 
                        FROM dtb_list
                        WHERE id = ?";
                $param = array($this->_getParam('listID'));
                $userID = $this -> getRow($SQL,$param);
                if(isset($userID['mtb_user_id'])){
                    $SQL = "UPDATE dtb_infomation
                                SET deleted = 1
                            WHERE type           = 1
                            AND   mtb_user_id    = ?
                            AND   target_user_id = ?";
                    $param = array(1,$userID['mtb_user_id'],$this -> _userData['id'],time());
                    $st =  $this -> execute($SQL,$param);
                }
                $favoriteStatus = 0;
            }
            $param = array($this->_getParam('listID'));
        	$favoriteCount = $this->getCount('dtb_list_favorite',' dtb_list_id = ? AND deleted = 0 ',$param);
        	return array('favoriteCount' => $favoriteCount,'favoriteStatus'=>$favoriteStatus);
        }
        
        public function setComment()
        {
            $SQL = "INSERT INTO dtb_list_comment(dtb_list_id,mtb_user_id,return_user_id,text,created)";
            $param = array($this->_getParam('listID'),$this -> _userData['id'],$this->_getParam('returnUserID'),$this->_getParam('listCommentText'),time());
            $st =  $this -> executeIns($SQL,$param);
            
            $SQL = "SELECT
                        mtb_user_id
                    FROM dtb_list
                    WHERE id = ?";
            $param = array($this->_getParam('listID'));
            $userID = $this -> getRow($SQL,$param);
            
            if(!isset($userID['mtb_user_id'])){
                return $st;
            }
            
            $SQL = "UPDATE dtb_list
                        SET comment_count = comment_count + 1
                    WHERE id = ?";
            $param = array($this->_getParam('listID'));
            $this -> execute($SQL,$param);
            
            $SQL = "INSERT INTO dtb_infomation(type,mtb_user_id,target_user_id,dtb_list_id,created)";
            $param = array(2,$userID['mtb_user_id'],$this -> _userData['id'],$this->_getParam('listID'),time());
            $st =  $this -> executeIns($SQL,$param);
            $this -> _sendPush($userID['mtb_user_id'],$this -> _userData['name'] ."さんが、あなたのレシピにコメントしました！",1,false);
            return $st;
        }
        
        public function setAdd()
        {
            $SQL = "INSERT INTO dtb_list(mtb_user_id,image,type,title,text,created)";
            $param = array($this -> _userData['id'],$this->_getParam('listImage'),$this->_getParam('listType'),$this->_getParam('listTitle'),$this->_getParam('listText'),time());
            $this -> executeIns($SQL,$param);
            
            $listID = $this -> getLastID('dtb_list');
            
            $tags = $this->_getParam('tags');
            for($i=0;$i<count($tags);$i++){
                $tag = $tags[$i];
                if(!isset($tag['tagID']) || $tag['sentaku'] == 0){
                    continue;
                }
                //既存
                $insertTagId = 0;
                if($tag['tagID'] == 0 ){
                    $SQL = "SELECT
                                id
                            FROM mtb_tag
                            WHERE tag = ?";
                    $param = array($tag['tagName']);
                    $check = $this -> getRow($SQL,$param);
                    
                    if(isset($check['id'])){
                        $insertTagId = $check['id'];
                    }
                    else{
                        $SQL = "INSERT INTO mtb_tag(mtb_user_id,tag,sort,created)";
                        $param = array($this -> _userData['id'],$tag['tagName'],0,time());
                        $this -> executeIns($SQL,$param);
                        $insertTagId = $this -> getLastID('mtb_tag');
                    }
                }
                else{
                    $insertTagId = $tag['tagID'];
                }
                
                $SQL = "INSERT INTO dtb_list_tag (dtb_list_id,mtb_tag_id,created)";
                $param = array($listID,$insertTagId,time());
                $this -> executeIns($SQL,$param);
            }
            
            $tags = $this->_getParam('newtags');
            for($i=0;$i<count($tags);$i++){
                
                $SQL = "SELECT
                            id
                        FROM mtb_tag
                        WHERE tag = ?";
                $param = array($tags[$i]);
                $check = $this -> getRow($SQL,$param);
                
                if(isset($check['id'])){
                    $insertTagId = $check['id'];
                }
                else{
                    $SQL = "INSERT INTO mtb_tag(mtb_user_id,tag,sort,created)";
                    $param = array($this -> _userData['id'],$tags[$i],0,time());
                    $this -> executeIns($SQL,$param);
                    
                    $insertTagId = $this -> getLastID('mtb_tag');
                }
                $SQL = "SELECT
                            id
                        FROM dtb_list_tag
                        WHERE dtb_list_id = ? AND mtb_tag_id = ?";
                $param = array($listID,$insertTagId);
                $checkTag = $this -> getRow($SQL,$param);
                if(!isset($checkTag['id'])){
                    $SQL = "INSERT INTO dtb_list_tag (dtb_list_id,mtb_tag_id,created)";
                    $param = array($listID,$insertTagId,time());
                    $this -> executeIns($SQL,$param);
                }
            }
            
            
            
            $reshipis = $this->_getParam('reshipis');
            for($i=0;$i<count($reshipis);$i++){
                $reshipi = $reshipis[$i];
                if(!isset($reshipi['listResipiImage'])){
                    continue;
                }
                $SQL = "INSERT INTO dtb_list_detail(dtb_list_id,text,image,created)";
                $param = array($listID,$reshipi['listResipiText'],$reshipi['listResipiImage'],time());
                $this -> executeIns($SQL,$param);
            }
            
            $zairyos = $this->_getParam('zairyos');
            for($i=0;$i<count($zairyos);$i++){
                $zairyo = $zairyos[$i];
                if(!isset($zairyo['listZairyoTitle'])){
                    continue;
                }
                $SQL = "INSERT INTO dtb_list_detail2(dtb_list_id,title,cnt,created)";
                $param = array($listID,$zairyo['listZairyoTitle'],$zairyo['listZairyoCount'],time());
                $this -> executeIns($SQL,$param);
            }
            
            
            return true;
        }
        
        public function commentList()
        {
    	    $param  = array($this->_getParam('listID'));
            $SQL = "SELECT a.* , IFNULL(user2.name,'') returnUserName
                FROM (SELECT
                     ".SQL_LIST_CMT_DATA.",
                     ".SQL_USER_DATA.",
                     dtb_list_comment.return_user_id
                    FROM dtb_list_comment
                    LEFT JOIN mtb_user       ON (mtb_user.id = dtb_list_comment.mtb_user_id)
                    WHERE dtb_list_id = ?) as a
                LEFT JOIN mtb_user user2 ON (user2.id = a.return_user_id)";
            $result = $this -> getRows($SQL,$param);
            return $result;
        }
        
        public function favoriteList()
        {
            $SQL = "SELECT
                        ".SQL_USER_DATA."
                    FROM dtb_list_favorite
                    LEFT JOIN mtb_user ON (mtb_user.id = dtb_list_favorite.mtb_user_id)
                    WHERE dtb_list_favorite.dtb_list_id = ?
                    AND   dtb_list_favorite.deleted     = 0";
            $param = array($this->_getParam('listID'));
            $result = $this -> getRows($SQL,$param);
            
            for($i=0;$i<count($result);$i++){
                $this -> addFollowStatus($result[$i]);
            }
            return $result;
        }
        
        public function userList()
        {
            $SQL = "SELECT
                        ".SQL_LIST_DATA.",
                        ".SQL_USER_DATA."
                    FROM dtb_list
                    LEFT JOIN mtb_user          ON (mtb_user.id                   = dtb_list.mtb_user_id)
                    WHERE dtb_list.mtb_user_id = ?
                    AND   dtb_list.deleted     = 0
                    ORDER BY dtb_list.id DESC";
            $param = array($this->_getParam('userID'));
            return $this -> getRows($SQL,$param);
        }
        
        
        public function userFavoriteList(){
            $SQL = "SELECT
    	                ".SQL_LIST_DATA.",
    	                ".SQL_USER_DATA.",
        	            CASE WHEN dtb_list_favorite.id IS NULL THEN 0 ELSE 1 END listFavoriteStatus
                    FROM dtb_list_favorite
                    INNER JOIN dtb_list ON (dtb_list.id = dtb_list_favorite.dtb_list_id AND dtb_list.deleted = 0)
    	            LEFT JOIN mtb_user ON (mtb_user.id = dtb_list.mtb_user_id)
                    WHERE dtb_list_favorite.mtb_user_id = ?
                    AND   dtb_list_favorite.deleted     = 0";
            $param = array($this->_getParam('userID'));
            return $this -> getRows($SQL,$param);
        }
        
        public function listDelete()
        {
            $SQL = "UPDATE dtb_list
                        SET deleted = 1
                    WHERE mtb_user_id = ?
                    AND   id          = ?";
            $param = array($this -> _userData['id'],$this->_getParam('listID'));
            return $this -> execute($SQL,$param);
        }
        
        public function listUpdate()
        {
            $param = array($this -> _userData['id'],$this->_getParam('listID'));
        	$count = $this->getCount('dtb_list',' mtb_user_id = ? AND id = ? AND deleted = 0 ',$param);
        	if($count == 0){
            	return false;
            }
        	
            $SQL = "UPDATE dtb_list
                        SET image   = ?,
                            type    = ?,
                            title   = ?,
                            text    = ?,
                            updated = ?
                    WHERE id = ?
                    AND   mtb_user_id = ?";
            $param = array($this->_getParam('listImage'),$this->_getParam('listType'),$this->_getParam('listTitle'),$this->_getParam('listText'),time(),$this->_getParam('listID'),$this -> _userData['id']);
            $this -> execute($SQL,$param);
            
            $listID = $this->_getParam('listID');
            
            
            $SQL = "DELETE FROM dtb_list_tag
                        WHERE dtb_list_id = ?";
            $param = array($this->_getParam('listID'));
            $this -> execute($SQL,$param);
            
            $tags = $this->_getParam('tags');
            for($i=0;$i<count($tags);$i++){
                $tag = $tags[$i];
                if(!isset($tag['tagID']) || $tag['sentaku'] == 0){
                    continue;
                }
                //既存
                $insertTagId = 0;
                if($tag['tagID'] == 0 ){
                    $SQL = "SELECT
                                id
                            FROM mtb_tag
                            WHERE tag = ?";
                    $param = array($tag['tagName']);
                    $check = $this -> getRow($SQL,$param);
                    
                    if(isset($check['id'])){
                        $insertTagId = $check['id'];
                    }
                    else{
                        $SQL = "INSERT INTO mtb_tag(mtb_user_id,tag,sort,created)";
                        $param = array($this -> _userData['id'],$tag['tagName'],0,time());
                        $this -> executeIns($SQL,$param);
                        $insertTagId = $this -> getLastID('mtb_tag');
                    }
                }
                else{
                    $insertTagId = $tag['tagID'];
                }
                
                $SQL = "INSERT INTO dtb_list_tag (dtb_list_id,mtb_tag_id,created)";
                $param = array($listID,$insertTagId,time());
                $this -> executeIns($SQL,$param);
            }
            
            $tags = $this->_getParam('newtags');
            for($i=0;$i<count($tags);$i++){
                
                $SQL = "SELECT
                            id
                        FROM mtb_tag
                        WHERE tag = ?";
                $param = array($tags[$i]);
                $check = $this -> getRow($SQL,$param);
                
                if(isset($check['id'])){
                    $insertTagId = $check['id'];
                }
                else{
                    $SQL = "INSERT INTO mtb_tag(mtb_user_id,tag,sort,created)";
                    $param = array($this -> _userData['id'],$tags[$i],0,time());
                    $this -> executeIns($SQL,$param);
                    
                    $insertTagId = $this -> getLastID('mtb_tag');
                }
                $SQL = "SELECT
                            id
                        FROM dtb_list_tag
                        WHERE dtb_list_id = ? AND mtb_tag_id = ?";
                $param = array($listID,$insertTagId);
                $checkTag = $this -> getRow($SQL,$param);
                if(!isset($checkTag['id'])){
                    $SQL = "INSERT INTO dtb_list_tag (dtb_list_id,mtb_tag_id,created)";
                    $param = array($listID,$insertTagId,time());
                    $this -> executeIns($SQL,$param);
                }
            }
            
            
            $SQL = "DELETE FROM dtb_list_detail
                        WHERE dtb_list_id = ?";
            $param = array($this->_getParam('listID'));
            $this -> execute($SQL,$param);
            
            $reshipis = $this->_getParam('reshipis');
            for($i=0;$i<count($reshipis);$i++){
                $reshipi = $reshipis[$i];
                if(!isset($reshipi['listResipiImage'])){
                    continue;
                }
                $SQL = "INSERT INTO dtb_list_detail(dtb_list_id,text,image,created)";
                $param = array($listID,$reshipi['listResipiText'],$reshipi['listResipiImage'],time());
                $this -> executeIns($SQL,$param);
            }
                  
            $SQL = "DELETE FROM dtb_list_detail2
                        WHERE dtb_list_id = ?";
            $param = array($this->_getParam('listID'));
            $this -> execute($SQL,$param);
            
            $zairyos = $this->_getParam('zairyos');
            for($i=0;$i<count($zairyos);$i++){
                $zairyo = $zairyos[$i];
                if(!isset($zairyo['listZairyoTitle'])){
                    continue;
                }
                $SQL = "INSERT INTO dtb_list_detail2(dtb_list_id,title,cnt,created)";
                $param = array($listID,$zairyo['listZairyoTitle'],$zairyo['listZairyoCount'],time());
                $this -> executeIns($SQL,$param);
            }
            
            
            return true;
        }
        
        public function tuho()
        {
            $param = array($this -> _userData['id'],$this->_getParam('listID'));
        	$check = $this->getCount('dtb_list_tuho',' mtb_user_id = ? AND dtb_list_id = ? ',$param);
        	if($check> 0){
            	return true;
            }
        	
        	$SQL = "INSERT INTO dtb_list_tuho(mtb_user_id,dtb_list_id,created)";
        	$param = array($this -> _userData['id'],$this->_getParam('listID'),time()); 
        	return $this -> executeIns($SQL,$param);
        }
        
    	
	}

?>