<?php
	require_once dirname(__FILE__).'/sqlString.php';
   /*************************************************************
    * allDBs
    * Mysql処理
    * 継承元:nothing
    *
    * 20121013
    * kpp
    *************************************************************/
    class DB{
        public $db;
        
       /*
        * コンストラクタ
        * 接続のみ
        */
        public function __construct() {
            try{
            	$connections = parse_ini_file(dirname(__FILE__).'/../../configs/config.ini');
                $DBNAME      = $connections['DB.name'];
			    $user        = $connections['DB.username'];
			    $pass        = $connections['DB.password'];
			    $host        = $connections['DB.host'];
                $this -> db = new PDO("mysql:host=".$host.";dbname=".$DBNAME, $user, $pass);
                $this -> db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this -> db->query("SET NAMES utf8");
                
            }catch(PDOException $e) {
                $this -> db = null;
            }
        }
        
       /*
        * dev環境接続
        */
        public function connectDevelopment(){
        	unset($this -> db);
            try{
            	$connections = parse_ini_file(dirname(__FILE__).'/../../configs/config.ini');
                $DBNAME      = $connections['DB.name']."_dev";
			    $user        = $connections['DB.username'];
			    $pass        = $connections['DB.password'];
			    $host        = $connections['DB.host'];
                $this -> db = new PDO("mysql:host=".$host.";dbname=".$DBNAME, $user, $pass);
                $this -> db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this -> db->query("SET NAMES utf8");
                
            }catch(PDOException $e) {
                $this -> db = null;
            }
        }
   
       /*
        * 全行取得
        * $sql    :SQL
        * $where  :where句 配列渡し array('_id' => xxxxxx)
        * 
        * return  :結果[][]
        */
        public function getRows($sql,$where=array()){
            $res = null;
            $i=0;
            $returnArray = array();

			try {
	            $stmt = $this -> db -> prepare($sql);
				$stmt -> execute($where);
				while($row = $stmt->fetch(PDO::FETCH_NAMED)){
                	$res[$i] = $row;
					$i++;
				}
				$returnArray = array('data'  => $res,
									 'error' => 0);
	            return $returnArray;
			} catch (Exception $e) {
				$returnArray = array('data'  => $e->getMessage(),
									 'error' => 1);
	            return $returnArray;
			}
        }
        
        /*
        * 全行取得
        * $sql    :SQL
        * $where  :where句 配列渡し array('_id' => xxxxxx)
        * 
        * return  :結果[][]
        */
        public function getRow($sql,$where=array()){
            $res = null;
            $i=0;
            
			try{
            	$stmt = $this -> db -> prepare($sql);
				$stmt -> execute($where);
				$res = $stmt->fetch(PDO::FETCH_NAMED);
            
				$returnArray = array('data'  => $res,
									 'error' => 0);
				return $returnArray;
			}catch (Exception $e) {
				$returnArray = array('data'  => $e->getMessage(),
									 'error' => 1);
				return $returnArray;
			}
        }
        
       /*
        * insert
        * $sql    :SQL
        * 
        * return  :結果[][]
        */
        public function insData($sql,$data=array()){
        
			try{
            	$res = $this -> db -> prepare($sql);
				$res = $res->execute($data);

				$returnArray = array('data'  => true,
								     'error' => 0);
				return $returnArray;
			}catch (Exception $e) {
				$returnArray = array('data'  => $e->getMessage(),
									 'error' => 1);
				return $returnArray;
			}
        }
        
    }