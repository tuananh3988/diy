<?PHP
	class SettingModel extends Model {
	
	public function install(){
            $SQL = "INSERT INTO mtb_install(uuid, created)";
            $insertParam = array($this->_getParam('uuid'),time());
            return $this->executeIns($SQL,$insertParam);
        }
            
		//push登録
        public function insertToken(){
                $return = false;
        	$count = $this->getCount('mtb_token',' uuid = ?',array($this->_getParam('uuid','')));
        	if($count==0){
        		$count = $this->getCount('mtb_token',' token = ?',array($this->_getParam('token','')));
        		if($count>0){
	        		$SQL = "DELETE FROM mtb_token
	        				WHERE token = ?";
	        		$param = array($this->_getParam('token'));
	        		$this->execute($SQL,$param);
        		}
		        $SQL = "INSERT INTO mtb_token(uuid,token,type,created)";
		        $insertParam = array($this->_getParam('uuid'),$this->_getParam('token'),$this->_getParam('device_type', 1),time());
		        $return = $this->executeIns($SQL,$insertParam);
        	}
        	else{
		        $SQL = "UPDATE mtb_token
		        				SET token   = ?,
                                                        type = ?,
		        					created = ?
						WHERE uuid = '".$this->_getParam('uuid')."'";
				$where = array($this->_getParam('token'),$this->_getParam('device_type', 1),time());
		        $return = $this->execute($SQL,$where);
        	}
	        return $return;
        }
        
        public function resetpassword()
        {
            $newPassword = $this -> _createRandam();
            
            $SQL = "UPDATE mtb_user
                        SET password = ?
                    WHERE id = ?";
            $param = array($newPassword,$this -> _userData['id']);
            $st = $this -> execute($SQL,$param);
            $mailText = "こんにちは、".$this -> _userData['name']."さん,

HANDIYアカウントのパスワードリセットのリクエストを受け付けました。以下のパスワードからログインしてください。

パスワードをリセット:".$newPassword."

パスワードの変更はログイン後、マイページにて変更可能です。

パスワードリセットをリクエストしていない場合は、このメッセージは無視していただいて結構です。パスワードは変更されず、そのままとなります。

- HANDIYチーム";
            $this -> _sendMail($this->_getParam('mail'),"HANDIYパスワード",$mailText);
            return $st;
        }
				
	}

?>