<?PHP
ini_set( 'display_errors', TEST );
	class Model extends Functions {
		public $DB;
		public $_tableName;
		public $_userData;
		public $_test;
		public $_errorCode;
		public $_lastID;
		public $_tokenCheck;
        /*
         * コンストラクタ
         */
        public function __construct() {
        	parent::__construct();
        	$this -> DB = new DB();
        }
        
        /* 共通処理*/
        /*
         * 接続切り替え
         */
        public function connectDevelopment($release){
        	if(!$release){
//        		$this -> DB ->connectDevelopment();
        	}
        }
                
        /*
         * データ返却
         */
        public function retrunData($SQL,$data){
	        
	        if($data['error']==1){
	        	$write = "SQL:".$SQL." error:".$data['data'];
		        $this->_log($write,1);
		        if($this -> _test){
			        var_dump($data['data']);
		        }
		        $data['data'] = array();
	        }
	        else{
	        	$write = "SQL:".$SQL;
		        $this->_log($write,0);
	        }
			if(!$data['data'] || !isset($data['data'])){
				$data['data'] = array();
			}
	        return $data['data'];
        }
        
        
        /*
         * 複数データ取得
         */
        public function getRows($SQL,$param=array()){
	        return $this->retrunData($SQL,$this ->DB -> getRows($SQL,$param));
        }
        /*
         * 単一データ取得
         */
        public function getRow($SQL,$param=array()){
	        return $this->retrunData($SQL,$this ->DB -> getRow($SQL,$param));
        }
        /*
         * インサート
         */
        public function executeIns($SQL,$param=array()){
        	$insertCount = (int)substr_count($SQL,",");
        	$SQL .= " VALUES ( ".str_repeat("?,",$insertCount)."? ".")";
        	
	        return $this->execute($SQL,$param);
        }
        /*
         * 実行
         */
        public function execute($SQL,$param=array()){
        	$data = $this->retrunData($SQL,$this ->DB -> insData($SQL,$param));
        	if(!$data && count($data) != 0){
	        	$this->_errorCode['code']    = 予期せぬエラー;
	        	$this->_errorCode['message'] = '予期せぬエラー';
        	}
	        return $data;
        }
        /*
         * ユーザーID単品返却
         */
        public function getUserID($uuid){
        	$this->changeTokentoUserData($uuid);
        	return $this->_userData['id'];
//	        return $this->_userData;
        }
        
        /*
         * 件数取得
         */
        public function getCount($table,$where,$whereArray=array())
        {        	
			$SQL = "SELECT
						COUNT(1) cnt
					FROM ".$table."
					WHERE ".$where;
					
			$res=$this->getRow($SQL,$whereArray);
			return (int)$res['cnt'];
        }
        /*
         * SUM取得
         */
        public function getSum($table,$param,$where,$whereArray=array())
        {        	
			$SQL = "SELECT
						SUM(".$param.") cnt
					FROM ".$table."
					WHERE ".$where;
					
			$res=$this->getRow($SQL,$whereArray);
			return (int)$res['cnt'];
        }
        /*
         * 最終ID取得
         */
        public function getLastID($tableName){
	        $SQL = "SELECT
	        			id
	        		FROM $tableName
	        		ORDER BY id DESC
	        		LIMIT 1";
	        $lastID = $this ->DB -> getRow($SQL);
	        $lastID = $lastID['data'];
	        if(isset($lastID['id'])){
		        return $lastID['id'];
	        }
	        else{
		        return -1;
	        }
        }
        
        /*
         * テーブル取得
         */
        public function setTables($release){
        	$SQL 				= "SHOW TABLES;";
        	$result 			= $this->getRows($SQL);
        	$this -> _tableName = array();
        	
        	for($i=0;$i<count($result);$i++){
	        	foreach ($result[$i] as $k => $v){
	        		if($v=="config"){
		        		continue;
	        		}
	        		if(strpos($v,'dev_') === false){
	        			if($release){
			        		$this -> _tableName[$v] = $v;
	        			}
	        			else{
			        		$this -> _tableName[$v] = "dev_".$v;
	        			}
	        		}
	        	}
        	}
        }
        
        /*
         * update更新変更
         * 
         */
        public function notUpdate($sql,$param,$change){
	        if($change){
	        	$splitSQL = explode("\n",$sql);
	        	$returnSQL = "";
				
				for($i=0;$i<count($splitSQL);$i++){
					if(strpos($splitSQL[$i],"	".$param) !== false || strpos($splitSQL[$i]," ".$param) !== false ){
						$returnSQL.= "/*".$splitSQL[$i]."*/";
					}
					else{
						$returnSQL.= $splitSQL[$i];
					}
					$returnSQL.="
					";
				}
	        }
	        else{
		        $returnSQL = $sql;
	        }
	        return $returnSQL;
        }
        
        /*
         * push送信
         * $data:jsonData
         */
        public function _sendPush($userID,$message,$mail=false,$badge=0,$test=false){
        	$param = array($this->_getParam('userID'));
//            $badge = $this -> getCount('dtb_talk' ,' get_user_id  = ? AND midoku = 1 AND deleted = 0 ',$param) + $this -> getCount('dtb_ashi' ,' ashi_user_id = ? AND midoku = 1 AND deleted = 0 ',$param);
            parent::_sendPush($userID,$message,$mail,$badge,$test);
			//ここでの一斉送信は無し。
			if($userID==0){
				return ;
			}
			else{
				$sendAray = array('userid'  => $userID,
								  'message' => $message,
								  'mail'    => $mail,
								  'badge'   => (int)$badge,
								  'test'    => $test);
			    system("php ".dirname(__FILE__)."/../../lib/ios/push.php '" . serialize($sendAray) . "' > /dev/null &");
	        }
        }        
	}

?>