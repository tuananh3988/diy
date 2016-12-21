<?PHP
	require_once dirname(__FILE__).'/../../../lib/smarty/Smarty.class.php';
	class Action extends Functions {
		public  $_retrunData;
		public  $_model;
		public  $_html;
		public  $_view;
		public  $_displayName;
		
        /*
         * コンストラクタ
         */
        public function __construct($model) {
        	parent::__construct();

        	//iniファイル読み込み
        	$this -> _model      = new $model();
        	$this -> _html       = true;
        	
        	$this -> _view       = new Smarty;
        	$this -> _displayName = "";
        }
        
        
        /*
         * デストラクタ
         */
        public function __destruct() {
        	if(!$this -> _html){
//	        	$this->createJson($this->_retrunData);
	        }
	        else{
	        	$this -> createHtml();
	        }
        	unset($this->_retrunData);
        }
        
        /*
         * smarty設定
         */
        public function _settingSmarty($viewName=""){
            $this -> _view -> debugging      = false;
            $this -> _view -> caching        = false;
            $this -> _view -> cache_lifetime = 60;

            $this -> _view -> template_dir = '/www/admin/application/tmp/smarty/templates/';
            $this -> _view -> compile_dir  = '/www/admin/application/tmp/smarty/templates_c/';
            $this -> _view -> config_dir   = '/www/admin/application/tmp/smarty/configs/';
            $this -> _view -> cache_dir    = '/www/admin/application/tmp/smarty/cache/';
            
            $nowFunction = debug_backtrace();
            $nowFunction = $nowFunction[1];
            $direcry     = "";
            $fileName    = "";
            
            $direcry  = $nowFunction['class'];
            $direcry  = str_replace("Controller", "", $direcry);
            $direcry  = mb_strtolower($direcry);
            if($viewName == ""){
                $fileName = $nowFunction['function'];
                $fileName = str_replace("Action", "", $fileName);
                $fileName = mb_strtolower($fileName);
            }
            else{
                $fileName = $viewName;
            }
            $this -> _displayName = dirname(__FILE__).'/../../Detail/View/'.$direcry."/".$fileName.".tpl";
            $ref = "";
            if(isset($_SERVER["HTTP_REFERER"])){
                $ref = $_SERVER["HTTP_REFERER"];
            }
            $this -> _view -> assign("ref",$ref);
        }
        
        /*
         * smarty私
         */
        public function setViewParam($param,$value){
            $this -> _view -> assign($param,$value);
        }
        
                
        /*
         * json作成
         */
        public function createJson($data){
        	$status = 0;
        	if($data===TRUE){
	        	$data = array();
        	}
	        $jsonData = array('response'  => array('data'=>$data),
	        				  'error'     => array('code'    => $this -> _model -> _errorCode['code'],
	        									   'message' => $this -> _model -> _errorCode['message']),
	        				  'timestamp'      => (int)time());
	        $this->_errorCode = $this -> _model -> _errorCode;
	        $jsonData = json_encode($jsonData);
	        echo $jsonData;
	        die();
        }
        
        /*
         * HTML作成
         */
        public function createHtml(){
            $this -> _view -> display($this -> _displayName);
        }
	}

?>