<?PHP
	class TagModel extends Model {
	
    	public function taglist()
    	{
            $param = array($this -> _userData['id']);
        	$SQL = "SELECT
        	            ".SQL_TAG_LIST.",
        	            CASE WHEN dtb_user_tag.id IS NULL THEN 0 ELSE 1 END sentaku
        	        FROM mtb_tag
        	        LEFT JOIN dtb_user_tag ON (dtb_user_tag.mtb_tag_id = mtb_tag.id AND dtb_user_tag.mtb_user_id = ? AND dtb_user_tag.deleted = 0)
        	        WHERE mtb_tag.sort != -1
        	        ORDER BY mtb_tag.sort DESC
        	        LIMit 300";
            return $this -> getRows($SQL,$param);
        }
        
        public function hotTag()
        {
            $param = array($this -> _userData['id']);
        	$SQL = "SELECT
        	            ".SQL_TAG_LIST.",
        	            CASE WHEN dtb_user_tag.id IS NULL THEN 0 ELSE 1 END sentaku
        	        FROM mtb_tag
        	        LEFT JOIN dtb_user_tag ON (dtb_user_tag.mtb_tag_id = mtb_tag.id AND dtb_user_tag.mtb_user_id = ? AND dtb_user_tag.deleted = 0)
        	        WHERE mtb_tag.sort != -1
        	        ORDER BY mtb_tag.sort DESC
        	        LIMit 100";
            return $this -> getRows($SQL,$param);
        }
        
        public function setsTag()
        {
            $tags = $this->_getParam('tagIDs');
            $tags = explode( ",",$tags);
            for($i=0;$i<count($tags);$i++){
                $this -> _params['tagID'] = $tags[$i];
                $this -> setTag();
            }
            return true;
            
        }
        
        public function setTag(){
            $param = array($this -> _userData['id'],$this->_getParam('tagID'));
        	$count = $this->getCount('dtb_user_tag',' mtb_user_id = ? AND mtb_tag_id = ? AND deleted = 0 ',$param);
        	//登録
        	if($count==0){
                $param = array($this -> _userData['id'],$this->_getParam('tagID'));
            	$count = $this->getCount('dtb_user_tag',' mtb_user_id = ? AND mtb_tag_id = ? AND deleted = 1 ',$param);
            	
            	//完全新規
            	if($count == 0){
                    $SQL   = "INSERT INTO dtb_user_tag(mtb_user_id,mtb_tag_id,created)";
                    $param = array($this -> _userData['id'],$this->_getParam('tagID'),time());
                    $this -> executeIns($SQL,$param);
                    return array('status' => 1);
                }
                //復活
                else{
                    $SQL = "UPDATE dtb_user_tag
                                SET deleted = 0,
                                    updated = ?
                            WHERE mtb_user_id = ?
                            AND   mtb_tag_id  = ?";
                    $param = array(time(),$this -> _userData['id'],$this->_getParam('tagID'));
                    $this -> execute($SQL,$param);
                    
                    return array('status' => 1);
                }
            }
            //削除
            else{
                $SQL = "UPDATE dtb_user_tag
                            SET deleted = 1
                        WHERE mtb_user_id = ?
                        AND   mtb_tag_id  = ?";
                $param = array($this -> _userData['id'],$this->_getParam('tagID'));
                $this -> execute($SQL,$param);
                return array('status' => 0);
            }
        }
    	
	}

?>