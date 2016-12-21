<?PHP
	class InfomationModel extends Model {
    	
    	public function infomationList()
    	{
        	$SQL = "SELECT
        	            ".SQL_INFOMATION.",
        	            ".SQL_USER_DATA.",
        	            IFNULL(dtb_list.image,'')          listImage
        	        FROM dtb_infomation
        	        LEFT JOIN mtb_user ON (mtb_user.id = dtb_infomation.target_user_id)
        	        LEFT JOIN dtb_list ON (dtb_list.id = dtb_infomation.dtb_list_id)
        	        WHERE dtb_infomation.mtb_user_id = ?
        	        AND   dtb_infomation.deleted = 0
        	        ORDER BY dtb_infomation.id desc";
            $param = array($this -> _userData['id']);
            $result = $this -> getRows($SQL,$param);
            for($i=0;$i<count($result);$i++){
                $this -> addFollowStatus($result[$i]);
            }
            if(count($result) ==0){
                $result = array(array('infomationType'         => -1,
                                      'infomationListID'       => 0,
                                      'infomationTargetUserID' => 0,
                                      'infomationCreated'      => time(),
                                      'userID'                 => 0,
                                      'userImage'              => "",
                                      'userBackGroundImage'    => ""
                                      )
                                );
            }
            
            for($i=0;$i<count($result);$i++){
                $result[$i]['infomationCreated'] = $this -> _niceTime($result[$i]['infomationCreated'],time());
            }
            return $result;
        }
	}

?>