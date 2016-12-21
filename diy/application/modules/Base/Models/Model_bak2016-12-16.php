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
        	
        	$this -> _lastID      = 0;
        	$this -> _usePointKey = "";
        	$this -> _usePoint    = 0;
        	$this -> _ngWords     = array();
        	$this -> _errorCode   = array('code'    => 0,
        								  'message' => "");
        	
        	if($this->_itemCheck('uuid')){
        		//ユーザー確認
				$this -> _tokenCheck = $this->changeTokentoUserData($this->_getParam('uuid'));
        	}
        	else{
	        	$this -> _tokenCheck = false;
        	}
			
        }
        
        public function errorEnd($id,$message){
            
			$this -> _errorCode['code']    = $id;
			$this -> _errorCode['message'] = $message;
			return false;
        }
        
        /* 共通処理*/
        /*
         * 接続切り替え
         */
        public function connectDevelopment($release){
        	if(!$release){
        		$this -> DB ->connectDevelopment();
        	}
        }
        
        /*
         * バージョンチェック
         */
        public function versionCehck(){
	        $SQL = "SELECT
	        			`release`,
	        			test
	        		FROM config
	        		WHERE app_version = ?";
	        $params = array($this->_getParam('v','1.0'));
	        $result = $this ->DB -> getRow($SQL,$params);
	        $result = $result['data'];
	        
	        $release = false;
	        if($this->_cleanValue($result,'release',0) == 0 &&
	           $this->_cleanValue($result,'test',0)    == 0){
				$release = false;
	        }
	        else{
				$release = true;
	        }
	        $testFlg = false;
	        if($result['test']==1){
		        $testFlg = true;
	        }
	        $return = array('release' => $release,
	        				'test'	  => $testFlg);
	        $this -> _test = $testFlg;
	        return $return;
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
        	$SQL = $this->chnageSQLUser($SQL);
	        return $this->retrunData($SQL,$this ->DB -> getRows($SQL,$param));
        }
        /*
         * 単一データ取得
         */
        public function getRow($SQL,$param=array()){
        	$SQL = $this->chnageSQLUser($SQL);
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
         * ユーザーtoken更新
         * ログ無し
         */
         public function updateUserToken($userID){
         	$userToken = $this -> _createUUID();
	        $SQL = "UPDATE mtb_user
	         			SET user_token = ?
	         		WHERE id = ?";
	        $param = array($userToken,$userID);
	        $status = $this->execute($SQL,$param);
	        if($status){
		        $userToken = $userToken;
	        }
	        else{
	        	$SQL = "SELECT
	        				user_token
	        			FROM mtb_user
	        			WHERE id = ?";
		        $param = array($userID);
	        	$result = $this->getRow($SQL,$param);
		        $userToken = $result['user_token'];
	        }
	        return $userToken;
         }
         
        /*
         * ユーザーtoken削除
         * ログ無し
         */
         public function deleteUserToken($userID){
	        $SQL = "UPDATE mtb_user
	         			SET user_token = ?
	         		WHERE id = ?";
	        $param = array('',$userID);
	        $status = $this->execute($SQL,$param);
	        
	        return $status;
         }
         
        /*
         * ユーザーデータ取得
         * ログ無し
         */
        public function changeTokentoUserData($uuid){
	        $SQL = "SELECT
	        			*
	        		FROM mtb_user
	        		WHERE uuid = ?";
			$param = array($uuid);
	        $this->_userData = $this ->DB -> getRow($SQL,$param);
	        $this->_userData = $this->_userData['data'];
	        if(!$this->_userData || !isset($this->_userData)){
	        	$this->_userData = $this -> insertStartUserData();
	        	if(!$this->_userData || !isset($this->_userData)){
			        return false;
	        	}
	        }
	        return true;
        }
        
        
        /*
         * ユーザーID返却
         * ログ無し
         */
        public function changeUserIDtoUserDataReturn($id){
	        $SQL = "SELECT
	        			*
	        		FROM mtb_user
	        		WHERE id = ?";
	        $param = array($id);
	        $data = $this ->DB -> getRow($SQL,$param);
	        $data = $data['data'];
	        if(!$data || !isset($data)){
		       $data = array('uuid'=>'',
		        			 'id'  =>0);
	        }
	        return $data;
        }
        /*
         * ユーザーデータ取得
         * ログ無し
         */
        public function changeUserIDtoUserData($id){
	        $SQL = "SELECT
	        			*
	        		FROM mtb_user
	        		WHERE id = ?";
	        $param = array($id);
	        $this->_userData = $this ->DB -> getRow($SQL,$param);
	        $this->_userData = $this->_userData['data'];
	        if(!$this->_userData || !isset($this->_userData)){
		        $this->_userData = array('uuid'=>'',
		        						 'id'  =>0);
		        $this->_errorCode = ユーザー登録されていない;

				return false;
	        }
	        return true;
        }
        
        /*
         * アプリ固有処理
         */
        private function insertStartUserData(){
	        $SQL 			= "INSERT INTO mtb_user(uuid,login,created)";
			$insertParam 	= array($this->_getParam('uuid'),time(),time());
			$status 		= $this -> executeIns($SQL,$insertParam);
/*
$installCount = $this -> getCount('mtb_user' ,' blocked=0 ',array()) ;
$this -> _sendPush(1,$installCount."インストール目",$installCount,false);
$this -> _sendPush(2,$installCount."インストール目",$installCount,false);
*/
			return $this->changeUserIDtoUserDataReturn($this->getLastID("mtb_user"));
        }
        
        /*
         * ログイン
         */
        public function updateLoginSetting(){
	        $SQL 			= "UPDATE mtb_user
	        				   		SET login   = ?,
	        				   		    login_count = login_count + 1,
	        				   		    ua      = ?,
	        				   			last_ip = ?,
	        				   			version = ? 
	        				   WHERE id = ?";
			$insertParam 	= array(time(),$_SERVER['HTTP_USER_AGENT'],$_SERVER["REMOTE_ADDR"],$this -> _params['v'],$this->_userData['id']);
			$status 		= $this -> execute($SQL,$insertParam);
			
			
			$param = array($this->_userData['id'],time());
            $check = $this -> getCount('dtb_login_history'," mtb_user_id = ? AND created = ? ",$param);
			if($check == 0){
    			$SQL = "INSERT INTO dtb_login_history(mtb_user_id,created)";
    			$this -> executeIns($SQL,$param);
            }

			return $status;
        }
        
        /*
         * push送信
         * $data:jsonData
         */
        public function _sendPush($userID,$message,$badge=0,$test=false){
        	$param = array($this->_getParam('userID'));
//            $badge = $this -> getCount('dtb_talk' ,' get_user_id  = ? AND midoku = 1 AND deleted = 0 ',$param) + $this -> getCount('dtb_ashi' ,' ashi_user_id = ? AND midoku = 1 AND deleted = 0 ',$param);
            parent::_sendPush($userID,$message,$badge,$test);
        }
        //ブロック
        public function blockCheck(){
	        $SQL = "SELECT
	        			id
	        		FROM dtb_block
	        		WHERE mtb_user_id = ?
	        		AND   block_user_id = ?";
	        $param = array($this->_userData['id'],$this->_getParam('userID',-1));
	        $result = $this->getRow($SQL,$param);
	        if(isset($result['id'])){
	        	$this->_errorCode = ユーザーブロック;
		        return true;
	        }
	        else{
		        $param = array($this->_getParam('userID',-1),$this->_userData['id']);
		        $result = $this->getRow($SQL,$param);
		        if(isset($result['id'])){
		        	$this->_errorCode = ユーザーブロック;
		        	return true;
				}
		        return false;
	        }
        }
        
        private function chnageSQLUser($SQL){
	        return str_replace("MYUSERID",$this->_userData['id'],$SQL);
        }
        
        //投稿ユーザーID返却
        public function getListUserData($id){
	        $SQL = "SELECT
	        			mtb_user_id id
	        		FROM dtb_list
	        		WHERE id = ? ";
	        $param = array($id);
	        
	        $result = $this->getRow($SQL,$param);
	        if(isset($result['id'])){
		        return $result['id'];
	        }
	        else{
		        return 0;
	        }
        }
        
        public function getNgUserList(){
            $SQL = "SELECT
                        block_user_id
                    FROM dtb_block
                    WHERE mtb_user_id = ?";
            $param = array($this->_userData['id']);
            $result = $this -> getRows($SQL,$param);
            return $result;
        }
        
        public function getNgUserCheck($checkID){
            $param = array($checkID,$this->_userData['id']);
            $check = $this -> getCount('dtb_block'," mtb_user_id = ? AND block_user_id = ? ",$param);
            if($check>0){
                return true;
            }
            else{
                return false;
            }
        }
        
        private function getNgWords(){
	        $SQL = "SELECT
	        			*
	        		FROM mtb_ng_word
					WHERE deleted = 0";
			$data = $this->getRows($SQL);
			return $data;
        }
        
        public function checkNgWords($fullWord){
	        if($this -> _ngWords == array()){
		        $this -> _ngWords = $this->getNgWords();
	        }
	        mb_regex_encoding("UTF-8");
	        $addSearchParam = array("C","h","c","k","H","K","n","N");
	        for($i=0;$i<count($this -> _ngWords);$i++){
//	        	$serachText = preg_quote($this -> _ngWords[$i]['word'], '/');
	        	$serachText = $this -> _ngWords[$i]['word'];
	        	
		        if (preg_match("/".$serachText."/i", $fullWord)) {
					return false;
				}
				//ほかも変換
				if($this -> _ngWords[$i]['all']==1){	
					for($l=0;$l<count($addSearchParam);$l++){
			    		//ひらがな変換
				        if (preg_match("/".mb_convert_kana($serachText, $addSearchParam[$l])."/i", $fullWord)) {
							return false;
						}
					}
				}
	        }
	        return true;
        }
        
        public function addFollowStatus(&$data){

            $param       = array($this->_userData['id'],$data['userID']);
            $followCheck = $this -> getCount('dtb_follow' ,' mtb_user_id = ? AND follow_user_id = ? AND deleted = 0',$param);
            
            if($followCheck  == 0){
                $data['follow_status'] = 0;
            }
            else{
                $data['follow_status'] = 1;
            }
            
            return $data;
            
        }
        public function addTagFollowStatus(&$data){

            $param       = array($this->_userData['id'],$data['tagID']);
            $followCheck = $this -> getCount('dtb_user_tag' ,' mtb_user_id = ? AND mtb_tag_id = ? ',$param);
            
            if($followCheck  == 0){
                $data['tag_follow_status'] = 0;
            }
            else{
                $data['tag_follow_status'] = 1;
            }
            
            return $data;
            
        }
        
        public function listJibunCheck(&$data){
            if($data['userID'] == $this->_userData['id']){
                $data['jibun'] = 1;
            }
            else{
                $data['jibun'] = 0;
            }
        }
        
        public function listFavoriteCheck(&$data){
            
            $param       = array($this->_userData['id'],$data['listID']);
            $followCheck = $this -> getCount('dtb_list_favorite' ,' mtb_user_id = ? AND dtb_list_id = ? AND deleted = 0 ',$param);
            
            if($followCheck  == 0){
                $data['listFavoriteStatus'] = 0;
            }
            else{
                $data['listFavoriteStatus'] = 1;
            }
            
            return $data;
        }
        
        
        
        public function getNendListURL(){
            $nendArray = array();
            $nendURL = "https://lona.nend.net/nafeed.php?api_key=787f2d73ca6e6a7fc6f9724b99326a79b8a2dd75&adspot_id=664347";
//                      https://lona.nend.net/nafeed.php?api_key=d57cbe4327ec424e656eee039c738633431b2eb0&adspot_id=624410&ad_num=5
            $options = array(
              'http' => array(
                'method' => 'GET',
                'header' => 'User-Agent: Mozilla/5.0 (iPhone; CPU iPhone OS 9_3_2 like Mac OS X) AppleWebKit/601.1.46 (KHTML, like Gecko) Version/9.0 Mobile/13F69 Safari/601.1',
              ),
            );
            $context = stream_context_create($options);
            $contents = @file_get_contents($nendURL, false, $context);
            $contents = @str_replace("nendNativeCallback(", "", $contents);
            $contents = @str_replace(")", "", $contents);
            $contents = @json_decode($contents,true);
            if(!isset($contents) || !$contents){
                return array();
            }
            
            $ad_define = $contents['ad_define']['ad_define_texts'];
            $prPass    = array();
            
            for($i=1;$i<count($ad_define)+1;$i++){
                $prPass[] = $ad_define[$i];
            }
            
            $nendArray = array('click_url'              => $contents['default_ads'][0]['click_url'],
                               'impression_count_url'   => $contents['default_ads'][0]['impression_count_url'],
                               'short_text'             => $contents['default_ads'][0]['short_text'],
                               'long_text'              => $contents['default_ads'][0]['long_text'],
                               'image_url'              => $contents['default_ads'][0]['ad_image']['image_url'],
                               'promotion_name'         => $contents['default_ads'][0]['promotion_name'],
                               'action_button_text'     => $contents['default_ads'][0]['action_button_text'],
                               'ad_define_texts'        => $prPass);
            
            //インプ付け
            $context = stream_context_create($options);
            $contents = @file_get_contents($contents['default_ads'][0]['impression_count_url'], false, $context);
            return $nendArray;
        }
        public function getNendListURLTest(){
            $nendArray = array('click_url'              => "https://c1.nend.net/click.php?a=3502&c=76430&m=1712112&i=1669797&d=1258&s=185311&w=605116&v=1&p=0&q=0&k=5",
                               'impression_count_url'   => "https://impression.nend.net/impression.php?d=1258&s=185311&w=605116&u=4340842944cba08310c2dbe91c3392db&o=iphone&e=6.0&n=iphone&l=ja-jp&k=5&vu=1&mid[]=1712112",
                               'short_text'             => "死ぬほどハマる！スマホゲームランキングTOP10",
                               'long_text'              => "死ぬほどハマる！本当に面白いゲームアプリランキングTOP10",
                               'image_url'              => "https://img1.nend.net/img/banner/3502/76430/1669797.png",
                               'promotion_name'         => "ゲーム紹介メディア .Games",
                               'action_button_text'     => "サイトへ行く",
                               'ad_define_texts'        => array("PR","Sponsored","広告","プロモーション"));
            return $nendArray;
        }
	}

?>