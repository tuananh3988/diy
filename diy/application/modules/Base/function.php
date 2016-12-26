<?PHP
ini_set( 'display_errors', TEST );
	if(LOCAL == 0){
		require_once dirname(__FILE__).'/../../lib/S3/S3.php';
	}
	class Functions {
		public $_config;
		public $_params;
		public $_uuid;
        /*
         * コンストラクタ
         */
        public function __construct() {
        	$this -> _config   = parse_ini_file(INIFILE);
        	$this->_getParams();
        }
        /*
         * デストラクタ
         */
        public function __destruct() {
        	unset($this->_params);
        }
        
        /*
         * パラメーター取得
         */
        public function _getParams(){
        	$this -> _params = $_REQUEST;
        	if(isset($this -> _params['request'])){
	        	$this -> _params = json_decode(urldecode( $this -> _params['request']),true);
        	}
        	else{
        		$this -> _params = $_REQUEST;
//	        	$this -> _params = array();
        	}
        }
        
        /*
         * ランダム文字列作成
         */
        public function _createRandam($keta=10){
		    $str = array_merge(range('a', 'z'), range('0', '9'), range('A', 'Z'));
		    $r_str = null;
		    for ($i = 0; $i < $keta; $i++) {
		        @$r_str .= $str[rand(0, count($str))];
		    }
		    return $r_str;
        }
        
        /*
         * uuid生成
         */
         public function _createUUID(){
	         $this -> _uuid = sha1(uniqid(mt_rand() , true));
	         return $this -> _uuid;
         }
         
        /*
         * 項目チェック
         */
        public function _itemCheck($key){
	        if($this->_getParam($key) == -1){
		        return false;
	        }
	        return true;
        }
        
        /*
         * iPhone課金判定
         */
        public function _iPhoneReceipt($receipt,$month=false,$test=true){
        	$connection = "";
        	$return     = array('id' 	 => 0,
        						'status' => false);
        	//本番
        	if(!$test){
	        	$connection = RECEIPT_DIS;
        	}
        	//テスト
        	else{
	        	$connection = RECEIPT_DEV;
        	}

			$postData = json_encode(
			    array('receipt-data' => $receipt)
/* 			    	  "password" => 'a1bc2c564fc74afcb207fdd85eb001bf') */
			);
			
		    $ch = curl_init($connection);
		    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		    curl_setopt($ch, CURLOPT_POST, true);
		    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
		    $response = json_decode(curl_exec($ch));
		    curl_close($ch);
			if ($response->status == 21007) {
			    $ch = curl_init(RECEIPT_DEV);
			    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			    curl_setopt($ch, CURLOPT_POST, true);
			    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
			    $response = json_decode(curl_exec($ch));
			    curl_close($ch);
			}
			
			
			// 検証成功の場合
			if ($response->status == 0) {
				$return['id']     = $response->receipt->transaction_id;
				$return['status'] = true;
				$return['error']  = 0;
				$return['full']   = json_encode($response);
			}
			// 月額じゃない場合はこれも正解？
			else if ($response->status == 21006 && !$month) {
				$return['id']     = $response->receipt->transaction_id;
				$return['status'] = true;
				$return['error']  = 0;
				$return['full']   = json_encode($response);
			}
			else{
				$return['id']     = 0;
				$return['status'] = false;
				$return['error']  = $response->status;
				$return['full']   = json_encode($response);
			}
			return $return;
        }
        
        /*
         * get,post受け取り
         */
        public function _getParam($value,$default=-1){
        	
	        if(!isset($this -> _params[$value])){
		        return $default;
	        }
	        if($this -> _params[$value] == "(null)" ){
		        return $default;
	        }
	        if(isset($this -> _params[$value])){
		        return $this -> _params[$value];
	        }
        }
        /*
         * データ綺麗にする。
         */
        public function _cleanValue($array,$value,$default=0){
	        if(isset($array[$value])){
		        return $array[$value];
	        }
	        else{
		        return $default;
	        }
        }
        /*
         * データチェック
         */
        public function _checkValue($value,$ng){
	        if($value == $ng){
		        return false;
	        }
	        else{
		        return true;
	        }
        }
        
        public function _changeTime($data,$param){
	        for($i=0;$i<count($data);$i++){
		        if(isset($data[$i][$param])){
			        $data[$i][$param] = $this->_niceTime($data[$i][$param],time());
		        }
	        }
	        return $data;
        }
        
        
        
        /*
         * メール送信
         */
        public function _sendMail($address,$title,$text){
            require_once "Mail.php";
            
            // メールの文字セット
            define( "MAIL_CHARSET", "UTF-8" );
            
            
            // smtpサーバ接続情報
            $param = array(
            	  "host" => "smtp.gmail.com"
            	, "port" => 587
            	, "auth" => true
            	, "username" => "diylife.info@gmail.com"
            	, "password" => "Diyappbase79"
            	, "timeout" => 20
            );
            
            // 送信先情報
            $to = array( $address );
            
            // 送信元＆件名＆本文を用意し、エンコード
            $from = "【HANDIY】";
            $from = mb_encode_mimeheader( $from, MAIL_CHARSET );
            $subject = $title;
            $subject = mb_encode_mimeheader( $subject, MAIL_CHARSET );
            $body = $text;
            $body = mb_convert_encoding( $body, MAIL_CHARSET, "UTF-8" );
            
            // メールヘッダ
            $header = array(
            	"From" => $from
            	, "To" => implode( ",", $to )
            	, "Subject" => $subject
            	, "Content-Type" => "text/plain; charset=" . MAIL_CHARSET
            );
            
            // PEAR::Mailオブジェクト取得
            $obj =& Mail::factory( "smtp", $param );
            
            // メール送信
            $recipients = array_merge( $to);
            $ret = $obj->send( $recipients, $header, $body );
            if ( PEAR::isError( $ret ) ) {
//            	echo "Code[" . $ret->getCode() . "], Msg[" . $ret->getMessage() . "]\n";
            } else {
//            	echo "メールを送信しました。\n";
            }
        }
        
		/**
		 * タイムオブジェクトを「何分前」で表示する
		 * @param	int $dest 比較する時刻（UNIX TIME）
		 * @param	int $sour 比較基準となる時刻（UNIX TIME）※省略可能
		 * @return	string 何秒前／何分前／何時間前‥‥
		*/
		public function _niceTime($dest,$sour) {
		/* 	$sour = (func_num_args() == 1) ? time() : func_get_arg(1); */
		
			$tt = $dest - $sour;
			
			if ($tt / 31536000  < -1)	return abs(round($tt / 31536000))    . '年前';
			if ($tt / 2592000 	< -1)	return abs(round($tt / 2592000))   	 . 'ヶ月前';
			if ($tt / 604800  	< -1)	return abs(round($tt / 604800))    	 . '週間前';
			if ($tt / 86400   	< -1)	return abs(round($tt / 86400))     	 . '日前';
			if ($tt / 3600  	< -1)	return abs(round($tt / 3600))    	 . '時間前';
			if ($tt / 60 		< -1)	return abs(round($tt / 60)) 		 . '分前';
			if ($tt < 0	)				return abs(round($tt)) 				 . '秒前';
			if ($tt / 31536000  > +1)	return abs(round($tt / 31536000))    . '年後';
			if ($tt / 2592000	> +1)	return abs(round($tt / 2592000))   	 . 'ヶ月後';
			if ($tt / 604800  	> +1)	return abs(round($tt / 604800))    	 . '週間後';
			if ($tt / 86400   	> +1)	return abs(round($tt / 86400))     	 . '日後';
			if ($tt / 3600  	> +1)	return abs(round($tt / 3600))    	 . '時間後';
			if ($tt / 60 		> +1)	return abs(round($tt / 60)) 		 . '分後';
			if ($tt > 0)				return abs(round($tt)) 				 . '秒後';
			return '現在';
		}
        
        
        /*
         * push送信
         * $data:jsonData
         */
        public function _sendPush($userID,$message,$badge=0,$test=false){
			//ここでの一斉送信は無し。
			if($userID==0){
				return ;
			}
			else{
				$sendAray = array('userid'  => $userID,
								  'message' => $message,
								  'badge'   => (int)$badge,
								  'test'    => $test);
			    system("php ".dirname(__FILE__)."/../../lib/ios/push.php '" . serialize($sendAray) . "' > /dev/null &");
                            
                            system("php ".dirname(__FILE__)."/../../lib/aos/pushnotification.php '" . serialize($sendAray) . "' > /dev/null &");
	        }
        }
        
        
        
        /*
         * ログ作成
         * $w:内容
         * $t:0 :app
         *    1 :db
         * $e:0:正常
         *   :1:エラー
         */
        public function _log($w,$e=0){
			$w        = str_replace(array("\r\n","\r","\n","\t"), '', $w);
			$w        = str_replace(array("  "), ' ', $w);
			$w        = str_replace(array("  "), ' ', $w);
        	$fileName = LOGFILE."sql/";
        	if($e==1){
	        	$fileName .= "error/";
        	}
        	$day       = getdate();
        	$fileName .= $day['year'].$day['mon'].$day['mday'].".log";
			$data      = "[".$day['hours'].":".mb_substr("0".$day['minutes'],-2,2,"UTF-8").":".mb_substr("0".$day['seconds'],-2,2,"UTF-8")."] url:".$_SERVER["REQUEST_URI"]." write:".$w." parameter:".serialize($this->_params);
			
			file_put_contents($fileName, $data. PHP_EOL,FILE_APPEND);

        }

    }
?>