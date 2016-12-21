<?php
   /*************************************************************
    * モンストまとめ
    * 継承元:nothing
    *
    * 20160212
    * kpp
    *************************************************************/
    class MONST{
        public $db;
        private $_changeArray;
       /*
        * コンストラクタ
        * 接続のみ
        */
        public function __construct() {
            $this -> _changeArray = array(  array('MD','MT','Mj','Mz','ND','NT','Nj','Nz','OD','OT'),
                                            array('A','E','I','M','Q','U','Y','c','g','k'),
                                            array('w','x','y','z','0','1','2','3','4','5')
                                         );
        }
        
       /*
        * マルチスキーム作成
        * error:空文字
        */
        public function monst_multi_encode($value){
            $multiID = "";
            for($i=0;$i<strlen($value);$i++){
                if(!isset($value[$i])){
                    return "";
                }
                $multiID .= $this -> _changeArray[$i % 3][$value[$i]];
            }
            //12
            return $multiID;
            
        }
        
       /*
        * マルチスキーム⇒ID
        */
        public function monst_multi_decode($value){
            $searchValue = array();
            for($i=0;$i<12;$i++){
                if(!isset($value[$i])){
                    return 0;
                }
                if($i%4==0){
                    $searchValue[] = $value[$i].$value[$i+1];
                    $i++;
                }
                else{
                    $searchValue[] = $value[$i];
                }
            }
            if(count($searchValue)!==9){
                return 0;
            }
            
            $monstNo = "";
            for($i=0;$i<count($searchValue);$i++){
                $monstNo .= array_search($searchValue[$i],$this -> _changeArray[$i%3]);
            }
            
            //不正
            if(strlen($monstNo) != 9){
                return 0;
            }
            //9
            return $monstNo;
        }
    }