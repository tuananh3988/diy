<?PHP
	class IndexController extends Action {
	    public function indexAction(){
	        $this -> _settingSmarty();
	    }
	    
	    public function allAction(){
	        $this -> _settingSmarty();
            
            $data = $this -> _model -> getAllData();
            
            $ids = "";
            
    	    $this -> setViewParam('data',$data);
    	    $this -> setViewParam('date1',$this->_getParam('date1',""));
    	    $this -> setViewParam('date2',$this->_getParam('date2',""));
        }
        
        public function edittextAction(){
            
    		if(!$this->_itemCheck('text','')){
                $this -> _retrunData = $this -> _model -> deleteText();
            }
            else{
                $this -> _retrunData = $this -> _model -> editText();
            }
        }
        
        public function deletelistAction(){
            $this -> _retrunData = $this -> _model -> deleteList();
        }
        
        public function deleteTagAction(){
            $this -> _retrunData = $this -> _model -> deleteTag();
        }
        
        
        public function createCsvAction(){
            $this -> _html = false;
            $data = $this -> _model -> getAllData();
            $csv_data = "日付,インストール数,ログイン数,投稿数"."\n";
            for ( $i = 0 ; $i < count ( $data ) ; $i ++ ) {
                $csv_data.= $data[$i]['keydate'].','.$data[$i]['installCnt'].','.$data[$i]['listCnt'].','.$data[$i]['loginCnt'].','.$data[$i]['memo']."\n";
            }
            //出力ファイル名の作成
            $csv_file = "DIY一括データ_". mt_rand() .'.csv';
          
            //文字化けを防ぐ
            $csv_data = mb_convert_encoding ( $csv_data , "sjis-win" , 'utf-8' );
              
            //MIMEタイプの設定
            header("Content-Type: application/octet-stream");
            //名前を付けて保存のダイアログボックスのファイル名の初期値
            header("Content-Disposition: attachment; filename=".$csv_file);
          
            // データの出力
            echo($csv_data);
            exit();
        }
	    public function usersAction(){
	        $this -> _settingSmarty();
            
            $data = $this -> _model -> getUsersData();
            
            $ids = "";
    	    $this -> setViewParam('data',$data);
    	    $this -> setViewParam('next',(int)$this->_getParam('page',0)+1);
        }
	    public function tokoAction(){
	        $this -> _settingSmarty();
            
            $data = $this -> _model -> getListData();
            
            $ids = "";
    	    $this -> setViewParam('data',$data);
    	    $this -> setViewParam('next',(int)$this->_getParam('page',0)+1);
        }
	    public function tagsAction(){
	        $this -> _settingSmarty();
            
            $data = $this -> _model -> getTagData();
            
            $ids = "";
    	    $this -> setViewParam('data',$data);
    	    $this -> setViewParam('next',(int)$this->_getParam('page',0)+1);
        }
        
        public function userdetailAction(){
	        $this -> _settingSmarty();
            
            $data = $this -> _model -> getUserDetail();
            
            $ids = "";
    	    $this -> setViewParam('data',$data);
        }
	    
	    public function questAction(){
	        $this -> _settingSmarty();
            
            $data = $this -> _model -> getQuest();
            
            $ids = "";
    	    $this -> setViewParam('data',$data);
        }
        
        public function questchangeAction(){
	        $this -> _settingSmarty();
            
            $data = $this -> _model -> setQuest();
            
            
        }
        public function questdeleteAction(){
	        $this -> _settingSmarty();
            
            $data = $this -> _model -> deleteQuest();
            
        }
        
        public function questaddAction(){
	        $this -> _settingSmarty();
            
            $data  = $this -> _model -> oldData();
            $data2 = $this -> _model -> bestData();
            
            $ids = "";
    	    $this -> setViewParam('data',$data);
    	    $this -> setViewParam('data2',$data2);
        }
        
        public function getolddataAction(){
            $this -> _retrunData  = $this -> _model -> getOneOld();
            $this -> _html        = false;
        }
        public function getolddatanameAction(){
            $this -> _retrunData  = $this -> _model -> getOneOldName();
            $this -> _html        = false;
        }
        public function getbestdataAction(){
            $this -> _retrunData  = $this -> _model -> getOne();
            $this -> _html        = false;
        }
        public function searchOldAction(){
            $this -> _retrunData  = $this -> _model -> searchOld();
            $this -> _html        = false;
        }
        
        public function questinsertAction(){
	        $this -> _settingSmarty();
    		if(!$this->_itemCheck('stime') || !$this->_itemCheck('etime') ){
        		echo "開始時間と終了時間は必須だよーん";
        		die();
            }
    		if($this->_getParam('etime') == "" || $this->_getParam('stime') == ""){
        		echo "開始時間と終了時間は必須だよーん";
        		die();
            }
            
            $data = $this -> _model -> addQuest();
        }
    }

?>