<?PHP
ini_set( 'display_errors', TEST );
	class Action extends Functions {
		public  $_retrunData;
		public  $_model;
		public  $_release;
		public  $_test;
		public  $_allCount;
		private $_post;
		private $_badAccsess;
		public  $_special;
		public  $_echoMode;
		
        /*
         * コンストラクタ
         */
        public function __construct($model,$post) {
        	parent::__construct();
        	
			$this -> _special    = false;
			$this -> _echoMode   = false;
			
        	//PC排除
        	/*

        	if(hash('sha256', $this->_getParam('uuid')."+juventus", false) != $this->_getParam('p') || !$this->_checkValue($this->_getParam('uuid'),'')){
 	       		if(strpos($_SERVER["REQUEST_URI"],"pointLP") === FALSE){
					$error = array('error_code'=>400,'message'=>'Not Found juventus');
		        	$this -> _badAccsess = true;
					echo json_encode($error);
					die();	 	       		
 	       		}
        	}
			*/
			
        	//iniファイル読み込み
        	$this -> _model      = new $model();
        	$this -> _post       = $post;
        	$this -> _badAccsess = false;
        	//バージョン管理
        	$releaseData         = $this -> _model -> versionCehck();
        	$this -> _release    = $releaseData['release'];
        	$this -> _test       = $releaseData['test'];
        	//接続先判定
        	$this -> _model -> connectDevelopment($this -> _release);
        	$this -> _allCount   = 0;
        	
        	//必須チェック
        	if(!$this->superCheck()){
	        	if(!$this -> _test){
	        		$this->errorEnd(パラメーター不足,"パラメーター不足");
				}
        	}
			
        	if(!$this -> _model -> _tokenCheck || $this -> _model -> _userData['id'] < 0){
        		$this -> errorEnd(トークン不正,"不正です");
        	}
        	else{
        	}
        	
        }
        
        /*
         * デストラクタ
         */
        public function __destruct() {
        	if(!$this -> _badAccsess){
            	if($this -> _echoMode){
                	$this->echoEnd  ($this->_retrunData);
            	}
	        	if(!$this -> _special){
		        	$this->createReadJson  ($this->_retrunData);
	        	}
	        	else{
		        	$this->createUploadJson($this->_retrunData);
	        	}
	        }
        	unset($this->_retrunData);
        }
        
        /*
         * 必須項目チェック
         */
        public function superCheck(){
	        if(!$this->_itemCheck('uuid') || !$this->_itemCheck('suuid') || !$this->_itemCheck('v')){
		        return false;
	        }
	        return true;
        }
        
        /*
         * パラメータ不足エラー終了
         */
        public function errorEnd($errorCode,$errorMessage){
    		$this -> _model -> _errorCode['code'] 	 = $errorCode;
			$this -> _model -> _errorCode['message'] = $errorMessage;
			$this -> _retrunData                     = array();
			$this -> _special    					 = true;
			$this -> _model -> _lastID    			 = 0;
			die();
        	
        }
        
        public function echoEnd($data){
            $echo = '<html>
                        <head>
                            <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
                            <meta name="viewport" content="width=device-width, height=50, user-scalable=no, initial-scale=1, minimum-scale=1, maximum-scale=1" />
                            <meta http-equiv="Pragma" content="no-cache">
                            <meta http-equiv="Cache-Control" content="no-cache">
                            <meta http-equiv="Expires" content="Thu, 01 Dec 1994 16:00:00 GMT">
                            <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
                        </head>
                        <body style="width:100%;height:330px;margin:0px;padding:0px;background-color: transparent ">
                            '.$data['tag'].'
                        </body>
                    </html>';
            echo $echo;
            die();
        }
        
        /*
         * 読み込みjson作成
         */
        public function createReadJson($data){
        	$status = 0;
        	if($data===TRUE){
	        	$data = array();
        	}
	        $jsonData = array('response'  => array('data'=>$data,'allCount'=>$this->_allCount),
	        				  'error'     => array('code'    => $this -> _model -> _errorCode['code'],
	        									   'message' => $this -> _model -> _errorCode['message']),
	        				  'timestamp'      => (int)time());
	        $this->_errorCode = $this -> _model -> _errorCode;
	        $jsonData = json_encode($jsonData);
	        echo $jsonData;
	        die();
        }
        
        /*
         * 更新json作成
         */
        public function createUploadJson($data){
        	$status = 0;
	        $jsonData = array('response'  =>  array('data'=>$data),
	        				  'error'     => array('code'    => $this -> _model -> _errorCode['code'],
	        									   'message' => $this -> _model -> _errorCode['message']),
	        				  'timestamp'      => (int)time());
	        $this->_errorCode = $this -> _model -> _errorCode;
	        $jsonData = json_encode($jsonData);
	        echo $jsonData;
	        die();
        }
        
        /*
         * json一時保存
         * $data:jsonData
         * $time:保持時間
         */
        public function createTempJsons($data,$time=5){
			
			$saveFileName = dirname(__FILE__)."/../../tmp/jsons/".time().'.json';
			file_put_contents( $saveFileName ,$saveData);
			chmod( $saveFileName, 0777 );
        }
        
        /*
         * 写真取得
         */
        public function _getPhotos($param,$directory=PHOTO_PATH,$urlDirectory=PHOTO_URL_PATH){
	        
	        $fileName = "";
			if(isset($param)&&$param){
				$fileTime = time();
				$fileRand = rand();
				$fileName = $fileTime.$fileRand;
				$sepaleteName = explode(".",$param["name"]);
				$fileName = $fileName.".".$sepaleteName[count($sepaleteName)-1];
		
				//ローカル保存
				if(LOCAL==1){
					if (move_uploaded_file($param["tmp_name"], $directory . $fileName)) {
//						chmod($directory . $_FILES["files"]["name"], 0644);
					} else {
						$fileName = "";
					}
					$fileName = $this -> _config['URL'].$urlDirectory.$fileName;
				}
				else{
					$remote_file = $fileName;
			
					// 接続を確立する
					$conn_id = ftp_connect("27.133.139.189");
					
					// ユーザー名とパスワードでログインする
					$login_result = ftp_login($conn_id, "files", "TNUvVyjdqcHkfv2y6KEkwRXzDwotK");
					$status       = ftp_chdir( $conn_id , "casual/photo/" );
					ftp_pasv($conn_id, true);
					$status       = ftp_put($conn_id, $remote_file, $param["tmp_name"], FTP_BINARY);
					// ファイルをアップロードする
					if ($status) {
			 		} else {
						die();
			 		}
					// 接続を閉じる
					ftp_close($conn_id);
					$fileName='http://files.apwtalk.me/casual/photo/'.$fileName;				
				}
			}
			else{
				//noimage
				$fileName = "";
			}
			return $fileName;
        }

        /*
         * 写真取得
         */
        public function _getPhotos_new($param,$directory=PHOTO_PATH,$urlDirectory=PHOTO_URL_PATH){
	        
	        $fileName  = "";
			$data = base64_decode($param);
	        $imageData = @imagecreatefromstring($data);
			if($imageData !== FALSE){
				$fileTime = time();
				$fileRand = rand();
				$fileName = $fileTime.$fileRand;
//				$sepaleteName = explode(".",$_FILES["files"]["name"]);
				$fileName = $fileName.".png";
		
				//ローカル保存
				if(LOCAL==1){
					if (imagepng($imageData ,$directory . $fileName)){
//						chmod($directory . $_FILES["files"]["name"], 0644);
					} else {
						$fileName = "";
					}
					$fileName = $this -> _config['URL'].$urlDirectory.$fileName;
				}
				else{
				    $S3 = new S3(ACCSESSKEY,SECRETKEY);
				    $S3->setEndpoint('s3-ap-northeast-1.amazonaws.com');
					$res = $S3->putObjectFile($imageData, BUCKETNAME, $directory."/".$fileName, S3::ACL_PUBLIC_READ);
					$fileName='https://s3-ap-northeast-1.amazonaws.com/'.BUCKETNAME.'/'.$directory.'/'.$fileName;					
				}
			}
			else{
				$this -> errorEnd(トークン不正,"画像ぶっ壊れです。");
				//noimage
				$fileName = "";
			}
			return $fileName;
        }
        
	}

?>