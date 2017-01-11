<?PHP
	class SearchModel extends Model {
	    public function diySearch()
	    {
    	    
    	    if($this->_getParam('word','') == ""){
        	    $SQL = "SELECT
        	                ".SQL_TAG_LIST."
        	            FROM mtb_tag
        	            WHERE id = ?";
                $param = array($this->_getParam('tagID'));
                $result = $this -> getRows($SQL,$param);
            }
            else{
        	    $SQL = "SELECT
        	                ".SQL_TAG_LIST."
        	            FROM mtb_tag
        	            WHERE tag like ?";
                $param = array("%".$this->_getParam('word')."%");
                $result = $this -> getRows($SQL,$param);
            }
            
            
            $param = array($this -> _userData['id'],$this->_getParam('word'));
        	$count = $this->getCount('dtb_history',' mtb_user_id = ? AND type = 1 AND word = ? ',$param);
        	
        	if($count==0){
                $SQL = "INSERT INTO dtb_history(mtb_user_id,type,word,created,updated)";
                $param = array($this -> _userData['id'],1,$this->_getParam('word'),time(),time());
                $this -> executeIns($SQL,$param);
            }
            else{
                $SQL = "UPDATE dtb_history
                            SET updated = ?
                        WHERE mtb_user_id = ?
                        AND   type        = ?
                        AND   word        = ?";
                $param = array(time(),$this -> _userData['id'],1,$this->_getParam('word'));
                $st = $this -> execute($SQL,$param);
            }
            
            return $result;
        }
	    public function userSearch()
	    {
    	    $SQL = "SELECT
    	                ".SQL_USER_DATA."
    	            FROM mtb_user
    	            WHERE name like ?";
            $param = array("%".$this->_getParam('word')."%");
            $result = $this -> getRows($SQL,$param);
            for($i=0;$i<count($result);$i++)
                $this -> addFollowStatus($result[$i]);
            
            $param = array($this -> _userData['id'],$this->_getParam('word'));
        	$count = $this->getCount('dtb_history',' mtb_user_id = ? AND type = 2 AND word = ? ',$param);
        	
        	if($count==0){
                $SQL = "INSERT INTO dtb_history(mtb_user_id,type,word,created,updated)";
                $param = array($this -> _userData['id'],2,$this->_getParam('word'),time(),time());
                $this -> executeIns($SQL,$param);
            }
            else{
                $SQL = "UPDATE dtb_history
                            SET updated = ?
                        WHERE mtb_user_id = ?
                        AND   type        = ?
                        AND   word        = ?";
                $param = array(time(),$this -> _userData['id'],2,$this->_getParam('word'));
                $st = $this -> execute($SQL,$param);
            }
            return $result;
        }
        
        public function osusumeUser()
        {
            $SQL = "SELECT DISTINCT
                        ".SQL_USER_DATA."
                    FROM mtb_user
                    LEFT JOIN dtb_list ON mtb_user.id  = dtb_list.mtb_user_id
                    WHERE mtb_user.deleted     = 0 AND dtb_list.deleted = 0 AND mtb_user.image != '' AND mtb_user.id != 1 AND mtb_user.id != 12 AND mtb_user.id != ?
                    GROUP BY mtb_user.id
                    ORDER BY count(dtb_list.image) DESC
                    LIMIT 28";
            $param = array($this -> _userData['id']);
            $result1 = $this -> getRows($SQL,$param);
            
            $SQL = "SELECT
                        ".SQL_USER_DATA."
                    FROM mtb_user
                    WHERE id in (1,12)";
            $param = array($this -> _userData['id']);
            $result2 = $this -> getRows($SQL,$param);
            $result = array_merge($result2, $result1);

            for($i=0;$i<count($result);$i++){
                $this -> addFollowStatus($result[$i]);
                
                $SQL = "SELECT
                             dtb_list.id             listID,
                             dtb_list.image          listImage
                        FROM dtb_list
                        WHERE mtb_user_id = ? AND image != '' AND image IS NOT NULL
                        AND   deleted     = 0
                        ORDER BY id DESC
                        LIMIT 3";
                $param = array($result[$i]['userID']);
                $result[$i]['listData'] = $this -> getRows($SQL,$param);
            }

            $arr0 = array();
            $arr1 = array();
            $arr2 = array();
            foreach($result as $row){
                //echo $row['listImage'];
                if ($row['userID'] == '1' || $row['userID'] == '12') {
                    $arr0[] = $row;
                }else if(isset($row['listData'][0]['listImage']))
                    $arr1[] = $row;
                else 
                    $arr2[] = $row;                 
            }             
            $output = array_merge($arr0, array_reverse($arr1),array_reverse($arr2));
            return $output;       
            //return $result;
        }

        public function sksort(&$array, $subkey="id", $sort_ascending=false) {

    if (count($array))
        $temp_array[key($array)] = array_shift($array);

    foreach($array as $key => $val){
        $offset = 0;
        $found = false;
        foreach($temp_array as $tmp_key => $tmp_val)
        {
            if(!$found and strtolower($val[$subkey]) > strtolower($tmp_val[$subkey]))
            {
                $temp_array = array_merge(    (array)array_slice($temp_array,0,$offset),
                                            array($key => $val),
                                            array_slice($temp_array,$offset)
                                          );
                $found = true;
            }
            $offset++;
        }
        if(!$found) $temp_array = array_merge($temp_array, array($key => $val));
    }

    if ($sort_ascending) $array = array_reverse($temp_array);

    else $array = $temp_array;
}
        
        public function diyHistory()
        {
            $SQL = "SELECT
                        dtb_history.word 
                    FROM dtb_history
                    WHERE mtb_user_id = ?
                    AND   type        = 1
                    ORDER BY id DESC
                    LIMIT 50";
            $param = array($this -> _userData['id']);
            $result = $this -> getRows($SQL,$param);
            return $result;
        }
        
        public function userHistory()
        {
            $SQL = "SELECT
                        dtb_history.word 
                    FROM dtb_history
                    WHERE mtb_user_id = ?
                    AND   type        = 2
                    ORDER BY id DESC
                    LIMIT 50";
            $param = array($this -> _userData['id']);
            $result = $this -> getRows($SQL,$param);
            return $result;
        }
        
        public function diyKekka()
        {
            if($this->_itemCheck('word')){
            		
        	    $SQL = "SELECT
        	                ".SQL_TAG_LIST."
        	            FROM mtb_tag
        	            WHERE tag like ?";
                $param = array("%".$this->_getParam('word')."%");
                $result = $this -> getRows($SQL,$param);
                
                $tagIDs = "";
                if(isset($result[0]['tagID'])){
                    $tagIDs = $result[0]['tagID'];
                    for($i=1;$i<count($result);$i++){
                        $tagIDs .= "," . $result[$i]['tagID'];
                    }
                }
                else{
                    return array();
                }
                
                $SQL = "SELECT
                            DISTINCT(dtb_list_id) listID
                        FROM dtb_list_tag
                        WHERE mtb_tag_id IN (".$tagIDs.")";
                $param = array();
                $tagIDs = $this -> getRows($SQL,$param);
                
            }
            else{
                $SQL = "SELECT
                            DISTINCT(dtb_list_id) listID
                        FROM dtb_list_tag
                        WHERE mtb_tag_id = ?";
                $param = array($this->_getParam('tagID'));
                $tagIDs = $this -> getRows($SQL,$param);
            }
            $tagString = "";
            $tagString = $tagIDs[0]['listID'];
            for($i=1;$i<count($tagIDs);$i++){
                $tagString .= ",". $tagIDs[$i]['listID'];
            }
            
            $limit = 20;
			$page = $this->_getParam('page');
			if($page<0){
				$page = 0;
			}
			$limit = $limit*$page . ", $limit ";
			
            $SQL = "SELECT
                        ".SQL_LIST_DATA."
                    FROM dtb_list
                    WHERE dtb_list.id IN (".$tagString.")
                    AND   deleted = 0
                    ORDER BY id DESC
                    LIMIT ".$limit;
            $param = array();
            $result = $this -> getRows($SQL,$param);
            return $result;
        }
        
        
	}

?>