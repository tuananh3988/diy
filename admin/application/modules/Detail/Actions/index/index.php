<?PHP
	class IndexController extends Action {
	    public function indexAction(){
	        $this -> _settingSmarty();
	    }
	    
	    public function allAction(){
	        $this -> _settingSmarty();
            
            $data = $this -> _model -> getAllData();
            $dataMonth = $this -> _model -> getAllDataMonth();
            $datas = array();
            foreach ($data as $k => $d) {
                if ($k == 0) {
                    $first = $d;
                }
                $date = explode('-', $d['keydate']);
                if ($date[2] == '01' && isset($data[$k - 1])) {
                    $date = explode('-', $data[$k - 1]['keydate']);
                    $month = $date[0] . '-' . $date[1];
                    foreach ($dataMonth as $km => $m) {
                        if ($month == $m['keydate']) {
                            $m['month'] = 1;
                            $datas[] = $m;
                        }
                    }
                }
                
                $d['month'] = 0;
                $datas[] = $d;
            }
            
            $dataFirst = $this -> _model -> getFirstData($first['keydate']);
            
            var_dump($first);
            
    	    $this -> setViewParam('date1',$this->_getParam('date1',""));
    	    $this -> setViewParam('date2',$this->_getParam('date2',""));
            $ids = "";
            
    	    $this -> setViewParam('data',$data);
            $this -> setViewParam('datas',$datas);
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
            $csv_data = "日付,iOSインストール,Androidインストール,iOS会員登録者（男女）,AOS会員登録者（男女）,iOS退会数（男女）,AOS退会数（男女）,累計会員数（退会者は引く） ,iOS会員登録率,AOS会員登録率 ,トータル会員登録率,iOS DAU,AOS DAU,トータル DAU ,iOS　投稿数,AOS投稿数,トータル投稿数,iOSレシピ投稿数,AOS レシピ投稿数,トータルレシピ投稿数,お気に入り数→新規,累計お気に入り数,コメント数 ,累計コメント数,タグ数→新規 ,累計タグ数"."\n";
            $keyArr = array('keydate', 'installCnt', 'installCntAos', 'installCnt2', 'installCntAos2', 'deactiveIosCnt',
                'deactiveAosCnt', 'total7', 'r8', 'r9', 'r10',
                'loginCnt', 'loginCntAos', 'r13', 'listCnt', 'listCntAos',
                'r16', 'listCnt2', 'listCntAos2', 'r19', 'listFavorite',
                'total21', 'listComment', 'total23', 'listTag', 'total25');
            $total7 = $total21 = $total23 = $total25 = 0;
            for ( $i = 0 ; $i < count ( $data ) ; $i ++ ) {
                $total7 += $data[$i]['totalLK'];
                $total21 += $data[$i]['listFavorite'];
                $total23 += $data[$i]['listComment'];
                $total25 += $data[$i]['listTag'];
                $data[$i]['total7'] = $total7;
                $data[$i]['total21'] = $total21;
                $data[$i]['total23'] = $total23;
                $data[$i]['total25'] = $total25;
                $data[$i]['r8'] = round($data[$i]['installCnt2'] != 0 ? $data[$i]['installCnt'] / $data[$i]['installCnt2'] : 0 , 2);
                $data[$i]['r9'] = round($data[$i]['installCntAos2'] != 0 ? $data[$i]['installCntAos'] / $data[$i]['installCntAos2'] : 0, 2);
                $data[$i]['r10'] = round(($data[$i]['installCnt2'] + $data[$i]['installCntAos2']) != 0 ? ($data[$i]['installCnt'] + $data[$i]['installCntAos']) / ($data[$i]['installCnt2'] + $data[$i]['installCntAos2']) : 0, 2);
                $data[$i]['r13'] = $data[$i]['loginCnt'] + $data[$i]['loginCntAos'];
                $data[$i]['r16'] = $data[$i]['listCnt'] + $data[$i]['listCntAos'];
                $data[$i]['r19'] = $data[$i]['listCnt2'] + $data[$i]['listCntAos2'];
                foreach ($keyArr as $k) {
                    $csv_data .= $data[$i][$k];
                    if ($k == 'total25') {
                        $csv_data .= "\n";
                    }
                    else {
                        $csv_data .= ',';
                    }
                }

               // $csv_data.= $data[$i]['keydate'].','.$data[$i]['installCnt'].','.$data[$i]['installCntAos'].','.$data[$i]['installCnt2'].','.$data[$i]['installCntAos2'].','.$data[$i]['deactiveIosCnt'].','.$data[$i]['deactiveAosCnt'].','.$data[$i]['installCntAos2'].','.$data[$i]['installCntAos2'].','.$data[$i]['installCntAos2'].','.$data[$i]['installCntAos2'].','.$data[$i]['installCntAos2'].','.$data[$i]['installCntAos2'].','.$data[$i]['installCntAos2'].','.$data[$i]['installCntAos2'].','.$data[$i]['installCntAos2'].','.$data[$i]['installCntAos2'].','.$data[$i]['installCntAos2'].','.$data[$i]['installCntAos2'].','.$data[$i]['installCntAos2'].','.$data[$i]['installCntAos2'].','.$data[$i]['installCntAos2'].','.$data[$i]['installCntAos2']."\n";
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
        
        public function processDataForCsv($data, $key) {
            switch ($key) {
                case '':


                    break;

                default:
                    break;
            }
        }
    }

?>