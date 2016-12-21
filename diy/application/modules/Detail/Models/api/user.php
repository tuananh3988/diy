<?PHP
	class UserModel extends Model {
    	
    	public function loginUser()
    	{
        	//メールログイン
            if($this->_getParam('type') == 1){
                $SQL = "SELECT
                            id
                        FROM mtb_user
                        WHERE mail     = ?
                        AND   password = MD5(?)
                        AND   deleted  = 0";
                $param = array($this->_getParam('mail'),$this->_getParam('password'));
                $result = $this -> getRow($SQL,$param);
            }
            else{
                $SQL = "SELECT
                            id
                        FROM mtb_user
                        WHERE login_id = ?
                        AND   deleted  = 0";
                $param = array($this->_getParam('loginID'));
                $result = $this -> getRow($SQL,$param);
            }
            $loginID = -1;
            if(!isset($result['id'])){
        		$this->errorEnd(1,"ログイン情報が間違っています。");
        		return false;
            }
            $loginID = $result['id'];
            $SQL = "UPDATE mtb_user_uuid
                        SET mtb_user_id = ?
                    WHERE uuid = ?";
            $param = array($loginID,$this->_getParam('uuid'));
            $this -> execute($SQL,$param);
            
            return true;
        }
	
    	public function editUser()
    	{
            $name = $this->_getParam('userName');
            $hitokoto = $this->_getParam('userHitokoto');
            $image = $this->_getParam('userImage');
            $back_ground_image = $this->_getParam('userBackGroundImage');
            $mail = $this->_getParam('userMail');
            $tel = $this->_getParam('userTel');
            $sex = $this->_getParam('userSex');
            $password = $this->_getParam('userPassword');

        	$SQL = "UPDATE mtb_user
        	            SET ";
            if(isset($name)) $SQL .= " name = ?"; 
            if(isset($hitokoto)) $SQL .= ", hitokoto = ?"; 
            if(isset($image)) $SQL .= ", image = ?"; 
            if(isset($back_ground_image)) $SQL .= ", back_ground_image = ?"; 
            if(isset($mail)) $SQL .= ", mail = ?"; 
            if(isset($tel)) $SQL .= ", tel = ?"; 
            if(isset($sex)) $SQL .= ", sex = ?"; 
            if(isset($password)) $SQL .= ", password = MD5(?)";                            
        	$SQL .= " WHERE id = ?";

            $param = array();
            if(isset($name)) array_push($param, $name);
            if(isset($hitokoto)) array_push($param, $hitokoto);
            if(isset($image)) array_push($param, $image);
            if(isset($back_ground_image)) array_push($param, $back_ground_image);
            if(isset($mail)) array_push($param, $mail);
            if(isset($tel)) array_push($param, $tel);
            if(isset($sex)) array_push($param, $sex);
            if(isset($password)) array_push($param, $password);
            array_push($param, $this -> _userData['id']);
            $st= $this -> execute($SQL,$param);
            return $st;
            
    	}
    	
    	public function userDetail()
    	{
            $param          = array($this->_getParam('userID'));
        	$followCount    = $this->getCount('dtb_follow',' mtb_user_id    = ? AND deleted = 0 ',$param);

        	$followerCount  = $this->getCount('dtb_follow',' follow_user_id = ? AND deleted = 0 ',$param);
        	
        	$tagCount       = $this->getCount('dtb_user_tag',' mtb_user_id  = ? AND deleted = 0 ',$param);

        	$listCount      = $this->getCount('dtb_list',' mtb_user_id = ? AND deleted = 0 ',$param);

        	$favoriteCount  = $this->getCount('dtb_list_favorite INNER JOIN dtb_list ON ( dtb_list.id = dtb_list_favorite.dtb_list_id AND dtb_list.deleted =0) ','   dtb_list_favorite.mtb_user_id  = ? AND dtb_list_favorite.deleted = 0  ',$param);
        	
        	$SQL = "SELECT
        	            ".SQL_USER_DATA_MY."
        	        FROM mtb_user
        	        WHERE id = ?";
            $param  = array($this->_getParam('userID'));
            $result = $this -> getRow($SQL,$param);
            $this -> addFollowStatus($result);
            $result['count']['followCount']   = $followCount;
            $result['count']['followerCount'] = $followerCount;
            $result['count']['tagCount']      = $tagCount;
            $result['count']['listCount']     = $listCount;
            $result['count']['favoriteCount'] = $favoriteCount;
            
            
        	return $result;
        }
    	
    	public function myData()
    	{
            $param          = array($this -> _userData['id']);
        	$followCount    = $this->getCount('dtb_follow',' mtb_user_id    = ? AND deleted = 0 ',$param);
        	
        	$followerCount  = $this->getCount('dtb_follow',' follow_user_id = ? AND deleted = 0 ',$param);
        	
        	$tagCount       = $this->getCount('dtb_user_tag',' mtb_user_id  = ? AND deleted = 0 ',$param);
        	
        	$listCount      = $this->getCount('dtb_list',' mtb_user_id = ? AND deleted = 0 ',$param);
        	
        	$favoriteCount  = $this->getCount('dtb_list_favorite INNER JOIN dtb_list ON ( dtb_list.id = dtb_list_favorite.dtb_list_id AND dtb_list.deleted =0) ','   dtb_list_favorite.mtb_user_id  = ? AND dtb_list_favorite.deleted = 0 ',$param);
        	
        	
        	$SQL = "SELECT
        	            ".SQL_USER_DATA_MY."
        	        FROM mtb_user
        	        WHERE id = ?";
            $param = array($this -> _userData['id']);
            $result = $this -> getRow($SQL,$param);
            $result['count']['followCount']   = $followCount;
            $result['count']['followerCount'] = $followerCount;
            $result['count']['tagCount']      = $tagCount;
            $result['count']['listCount']     = $listCount;
            $result['count']['favoriteCount'] = $favoriteCount;
            
        	return $result;
        }
        
        public function myToko()
        {
            $SQL = "SELECT
                        ".SQL_LIST_DATA."
                    FROM dtb_list
                    WHERE mtb_user_id = ? AND deleted = 0";
            $param = array($this -> _userData['id']);
            $result = $this -> getRows($SQL,$param);
            return $result;
        }
        
        public function myFavorite()
        {
            $SQL = "SELECT
                        ".SQL_LIST_DATA."
                    FROM dtb_list_favorite
                    LEFT JOIN dtb_list ON (dtb_list.id = dtb_list_favorite.dtb_list_id)
                    WHERE dtb_list_favorite.deleted = 0 AND dtb_list_favorite.mtb_user_id = ?";
            $param = array($this -> _userData['id']);
            $result = $this -> getRows($SQL,$param);
            return $result;
        }
        
        public function createUser()
        {
            $loginID = "";
            if($this->_getParam('mail') != "" && $this->_getParam('loginID') == ""){
                $param     = array($this->_getParam('mail'));
            	$mailCheck = $this->getCount('mtb_user',' mail = ? AND deleted = 0',$param);
            	$loginID   = $this->_getParam('mail');
            	if($mailCheck>0){
            		$this->errorEnd(1,"すでに登録されています。下のログインボタンからログインしてください。");
            		return false;
                }
            }
            else{
            	$loginID   = $this->_getParam('loginID');
                $param     = array($loginID);
                $idCheck = $this->getCount('mtb_user',' login_id = ? AND deleted = 0',$param);
                if($idCheck>0){
                    $this->errorEnd(2,"すでに登録されています。下のログインボタンからログインしてください。");
                    return false;
                }
            }
            
            
            $password = $this->_getParam('password');
            if($password == ""){
                $password = $this -> _createRandam();
            }
            
            $type = 1;
            if (!empty($this->_getParam('device_type')) && in_array($this->_getParam('device_type'), array(1, 2))) {
                $type = $this->_getParam('device_type');
            }

            $SQL = "INSERT INTO mtb_user
                        SET mail       = ?,
                            login_id   = ?,
                            password   = MD5(?),
                            login_type = ?,
                            name       = ?,
                            image      = ?,
                            created    = ?,
                            type   = ?";
            $param = array($this->_getParam('mail'),$loginID,$password,$this->_getParam('type'),$this->_getParam('name'),$this->_getParam('image',''),time(),$type);
            $result = $this -> execute($SQL,$param);
            $userID = $this->getLastID("mtb_user");
            $SQL = "UPDATE mtb_user_uuid
                        SET mtb_user_id = ?
                    WHERE uuid = ?";
            $param = array($userID,$this->_getParam('uuid'));
            $this -> execute($SQL,$param);
            return $result;
        }
        
        public function followList()
        {
            $userID = 0;
            if($this->_getParam('userID') > 0){
                $userID = $this->_getParam('userID');
            }
            else{
                $userID = $this -> _userData['id'];
            }
            $SQL = "SELECT
        	            ".SQL_USER_DATA_MY."
                    FROM dtb_follow
                    LEFT JOIN mtb_user ON (mtb_user.id = dtb_follow.follow_user_id)
                    WHERE dtb_follow.mtb_user_id = ?
                    AND   dtb_follow.deleted = 0";
            $param = array($userID);
            $result = $this -> getRows($SQL,$param);
            for($i=0;$i<count($result);$i++){
                $this -> addFollowStatus($result[$i]);
            }
            return $result;
        }
        public function followerList()
        {
            $userID = 0;
            if($this->_getParam('userID') > 0){
                $userID = $this->_getParam('userID');
            }
            else{
                $userID = $this -> _userData['id'];
            }
            $SQL = "SELECT
        	            ".SQL_USER_DATA_MY."
                    FROM dtb_follow
                    LEFT JOIN mtb_user ON (mtb_user.id = dtb_follow.mtb_user_id)
                    WHERE dtb_follow.follow_user_id = ?
                    AND   dtb_follow.deleted = 0";
            $param = array($userID);
            $result = $this -> getRows($SQL,$param);
            for($i=0;$i<count($result);$i++){
                $this -> addFollowStatus($result[$i]);
            }
            return $result;
        }
        
        public function tagsList(){
            $userID = 0;
            if($this->_getParam('userID') > 0){
                $userID = $this->_getParam('userID');
            }
            else{
                $userID = $this -> _userData['id'];
            }
            
            $SQL = "SELECT
                        ".SQL_TAG_LIST."
                    FROM dtb_user_tag
                    LEFT JOIN mtb_tag ON (mtb_tag.id = dtb_user_tag.mtb_tag_id)
                    WHERE dtb_user_tag.mtb_user_id = ?
                    AND   dtb_user_tag.deleted     = 0";
            $param = array($userID);
            $result = $this -> getRows($SQL,$param);
            for($i=0;$i<count($result);$i++){
                $this -> addTagFollowStatus($result[$i]);
            }
            return $result;
        }
        
        public function logout(){
            
            $SQL = "DELETE FROM mtb_user_uuid
                    WHERE mtb_user_id = ? AND uuid = ?";
            $param = array($this -> _userData['id'],$this->_getParam('uuid') );
            $this->execute($SQL,$param);
        
            if($this -> _userData['mail'] == ""){
                $SQL = "UPDATE dtb_user_tag
                            SET deleted = 1
                        WHERE mtb_user_id = ?";
                $param = array($this -> _userData['id']);
                $this -> execute($SQL,$param);
            }
            return true;
        }
        public function setUnfollow(){
            $param = array($this -> _userData['id'],$this->_getParam('userID'));
            $SQL = "UPDATE dtb_follow
                                SET deleted = 1
                            WHERE mtb_user_id = ?
                            AND   follow_user_id  = ?";
            $param = array($this -> _userData['id'],$this->_getParam('userID'));
            return $this -> execute($SQL,$param);
        }
        public function setFollow()
        {
            $param          = array($this -> _userData['id'],$this->_getParam('userID'));
        	$followCheck    = $this->getCount('dtb_follow',' mtb_user_id    = ? AND follow_user_id = ? AND deleted = 0 ',$param);
        	
        	//フォロー解除
        	if((int)$followCheck > 0){
            	$SQL = "UPDATE dtb_follow
            	            SET deleted = 1
                        WHERE mtb_user_id    = ?
                        AND   follow_user_id = ?";
                $param = array($this -> _userData['id'],$this->_getParam('userID'));
                $st    = $this -> execute($SQL,$param);
                
                $SQL = "UPDATE dtb_infomation
                            SET deleted = 1
                        WHERE type = 3
                        AND   mtb_user_id = ?
                        AND   target_user_id = ?";
                $param = array($this->_getParam('userID'),$this -> _userData['id']);
                $this -> execute($SQL,$param);
                
                return array('status'=>0);
            }
            //フォロー再開
            else{
                $param          = array($this -> _userData['id'],$this->_getParam('userID'));
            	$followInsCheck = $this->getCount('dtb_follow',' mtb_user_id    = ? AND follow_user_id = ? ',$param);
            	
            	if($followInsCheck == 0){
                    $SQL = "INSERT INTO dtb_follow(mtb_user_id,follow_user_id,created)";
                    $param = array($this -> _userData['id'],$this->_getParam('userID'),time());
                    $st =  $this -> executeIns($SQL,$param);
                    
                    $SQL = "INSERT INTO dtb_infomation(type,mtb_user_id,target_user_id,created)";
                    $param = array(3,$this->_getParam('userID'),$this -> _userData['id'],time());
                    $st =  $this -> executeIns($SQL,$param);

                    $this -> _sendPush($this->_getParam('userID'),$this -> _userData['name'] ."さんに、フォローされました！",1,false);
                    return array('status'=>1);
                }
                else{
                	$SQL = "UPDATE dtb_follow
                	            SET deleted = 0
                            WHERE mtb_user_id    = ?
                            AND   follow_user_id = ?";
                    $param = array($this -> _userData['id'],$this->_getParam('userID'));
                    $st =  $this -> execute($SQL,$param);
                    
                    $SQL = "UPDATE dtb_infomation
                                SET deleted = 0,
                                    created = ?
                            WHERE type = 3
                            AND   mtb_user_id = ?
                            AND   target_user_id = ?";
                    $param = array(time(),$this->_getParam('userID'),$this -> _userData['id']);
                    $this -> execute($SQL,$param);
                    $this -> _sendPush($this->_getParam('userID'),$this -> _userData['name'] ."さんに、フォローされました！",1,false);
                    return array('status'=>1);
                }
            }
        }

        public function userDelete(){            
            	$SQL = "DELETE FROM mtb_user_uuid
                    WHERE mtb_user_id = ? AND uuid = ?";
            	$param = array($this -> _userData['id'],$this->_getParam('uuid') );
            	$this->execute($SQL,$param);
            	
                //deleter user
                $SQL = "UPDATE mtb_user
                            SET deleted = 1,
                            uuid = NULL
                        WHERE id = ?";
                $param = array($this -> _userData['id']);
                $this -> execute($SQL,$param);

                //delete follow
                $SQL = "UPDATE dtb_follow
                            SET deleted = 1
                        WHERE mtb_user_id = ?";
                $param = array($this -> _userData['id']);
                $this -> execute($SQL,$param);

                //delete information
                $SQL = "UPDATE dtb_infomation
                            SET deleted = 1
                        WHERE mtb_user_id = ?";
                $param = array($this -> _userData['id']);
                $this -> execute($SQL,$param);   

                //delete list
                $SQL = "UPDATE dtb_list
                            SET deleted = 1
                        WHERE mtb_user_id = ?";
                $param = array($this -> _userData['id']);
                $this -> execute($SQL,$param);                             

                //delete favorite
                $SQL = "UPDATE dtb_list_favorite
                            SET deleted = 1
                        WHERE mtb_user_id = ?";
                $param = array($this -> _userData['id']);
                $this -> execute($SQL,$param);  

                //delete user tag                             
                $SQL = "UPDATE dtb_user_tag
                            SET deleted = 1
                        WHERE mtb_user_id = ?";
                $param = array($this -> _userData['id']);
                $this -> execute($SQL,$param);    
            
            return true;
        }

        public function reportSpam()
        {   
            header('Content-Type: text/html; charset=utf-8');
            $SQL = "SELECT name,mail FROM mtb_user WHERE id=?"; 
            $userID = $this->_getParam('userID');                   
            $param = array($userID);
            $result = $this -> getRows($SQL,$param);
            $type = $this->_getParam('type');
            $reportUserName  = $this->_getParam('reportUserName');
            if(!empty($result)){
                $name = $result[0]['name'] ;
                $email_address = $result[0]['mail'] ;

                if($type == 1) $subject = '「HANDIY報告」レシピの表記に問題がある';
                else if($type == 2) $subject = '「HANDIY報告」画像が不適切　';
                else if($type == 3)$subject = '「HANDIY報告」マナー違反';
                else $subject = 'No Subject';

                $body = '<p> ■通報した人 </p>
                        <p>・名前： '.$name.' </p>
                        <p>・メール: '.$email_address.' </p>
                        <p>■通報された人</p>
                        <p>・名前：'.$reportUserName.' </p>';

                //$to      = 'info@theappbase.com'; 
                $to      = 'nhatnh47@gmail.com';                                

// Always set content-type when sending HTML email
$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

// More headers
$headers .= 'From: <webmaster@example.com>' . "\r\n";        

                mail($to, $subject, $body, $headers);
                return true;
            }
            return false;
        }
    	
	}

?>