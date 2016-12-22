<?PHP
	class IndexModel extends Model {
    	
    	public function getAllData()
    	{
        	
			$date1  = $this->_getParam('date1',"");
			$date2  = $this->_getParam('date2',"");
			
			$dateWhere = "";
			if($date1 != ""){
    			$dateWhere = " DATE_FORMAT(date_add(CURDATE(), interval tmp.generate_series - 30 day), '%Y/%m/%d') >= '".$date1."' ";
            }
            else{
    			$dateWhere = " DATE_FORMAT(date_add(CURDATE(), interval tmp.generate_series - 30 day), '%Y/%m/%d') >= '2016/09/27' ";
            }
			
			if($date2 != ""){
    			$dateWhere .= " AND DATE_FORMAT(date_add(CURDATE(), interval tmp.generate_series - 30 day), '%Y/%m/%d') <= '".$date2."' ";
            }
			
        	
        	$SQL = "SELECT
                        tmpDate.keydate2                 keydate,
                        IFNULL(tmpInstall.installCnt,0)  installCnt,
                        IFNULL(tmpInstall2.installCnt,0) installCnt2,
                        IFNULL(tmpAosInstall.installAosCnt,0)  installCntAos,
                        IFNULL(tmpAosInstall2.installAosCnt,0) installCntAos2,
                        IFNULL(tmpIosDeactive.deactiveIosCnt,0) deactiveIosCnt,
                        IFNULL(tmpAosDeactive.deactiveAosCnt,0) deactiveAosCnt,
                        IFNULL(tmpLogin.loginCnt,0)      loginCnt,
                        IFNULL(tmpLoginAos.loginCntAos,0)      loginCntAos,

                        IFNULL(tmpList.listCnt,0)        listCnt,
                        IFNULL(tmpListAos.listCnt,0)        listCntAos,
                        IFNULL(tmpList2.listCnt,0)       listCnt2,
                        IFNULL(tmpListAos2.listCnt,0)       listCntAos2,
                        IFNULL(tmpListFavorite.listCnt,0)       listFavorite,
                        IFNULL(tmpListComment.listCnt,0)       listComment,
                        IFNULL(tmpListTag.listCnt,0)       listTag,
                        
                        IFNULL(tmpListUser.listCnt,0)    listUserCnt,
                        IFNULL(tmpList2User.listCnt,0)   listUserCnt2,
                        
                        REPLACE(IFNULL(admin_memo.memo,''),'\n',' ')       memoGraph,
                        IFNULL(admin_memo.memo,'')       memo
                    FROM (SELECT DATE_FORMAT(date_add(CURDATE(), interval tmp.generate_series - 30 day), '%Y/%m/%d') keydate,DATE_FORMAT(date_add(CURDATE(), interval tmp.generate_series - 30 day), '%Y-%m-%d') keydate2 FROM (SELECT 0 generate_series FROM DUAL WHERE (@num:=1-1)*0 UNION ALL SELECT @num:=@num+1 FROM `information_schema`.COLUMNS LIMIT 100) tmp WHERE ".$dateWhere.") tmpDate
                    LEFT JOIN (SELECT DATE_FORMAT(from_unixtime(created),'%Y/%m/%d') date,COUNT(1) installCnt FROM mtb_user WHERE type = 1 GROUP BY DATE_FORMAT(from_unixtime(created),'%Y/%m/%d') ) tmpInstall ON (tmpInstall.date = tmpDate.keydate)
                    LEFT JOIN (SELECT DATE_FORMAT(from_unixtime(created),'%Y/%m/%d') date,COUNT(1) installCnt FROM mtb_user WHERE name != '' AND type = 1 GROUP BY DATE_FORMAT(from_unixtime(created),'%Y/%m/%d') ) tmpInstall2 ON (tmpInstall2.date = tmpDate.keydate)
                    LEFT JOIN (SELECT DATE_FORMAT(from_unixtime(created),'%Y/%m/%d') date,COUNT(1) installAosCnt FROM mtb_user WHERE type = 2 GROUP BY DATE_FORMAT(from_unixtime(created),'%Y/%m/%d') ) tmpAosInstall ON (tmpAosInstall.date = tmpDate.keydate)
                    LEFT JOIN (SELECT DATE_FORMAT(from_unixtime(created),'%Y/%m/%d') date,COUNT(1) installAosCnt FROM mtb_user WHERE name != '' AND type = 2 GROUP BY DATE_FORMAT(from_unixtime(created),'%Y/%m/%d') ) tmpAosInstall2 ON (tmpAosInstall2.date = tmpDate.keydate)
                    LEFT JOIN (SELECT DATE_FORMAT(from_unixtime(deleted_date),'%Y/%m/%d') date,COUNT(1) deactiveIosCnt FROM mtb_user WHERE deleted = 1 AND type = 1 GROUP BY DATE_FORMAT(from_unixtime(deleted_date),'%Y/%m/%d') ) tmpIosDeactive ON (tmpIosDeactive.date = tmpDate.keydate)
                    LEFT JOIN (SELECT DATE_FORMAT(from_unixtime(deleted_date),'%Y/%m/%d') date,COUNT(1) deactiveAosCnt FROM mtb_user WHERE deleted = 1 AND type = 2 GROUP BY DATE_FORMAT(from_unixtime(deleted_date),'%Y/%m/%d') ) tmpAosDeactive ON (tmpAosDeactive.date = tmpDate.keydate)
                    LEFT JOIN (SELECT DATE_FORMAT(from_unixtime(dtb_login_history.created),'%Y/%m/%d') date,COUNT(DISTINCT(dtb_login_history.mtb_user_id)) loginCnt FROM dtb_login_history INNER JOIN mtb_user ON dtb_login_history.mtb_user_id = mtb_user.id WHERE mtb_user.type = 1 GROUP BY DATE_FORMAT(from_unixtime(dtb_login_history.created),'%Y/%m/%d')) tmpLogin ON (tmpLogin.date = tmpDate.keydate)
                    LEFT JOIN (SELECT DATE_FORMAT(from_unixtime(dtb_login_history.created),'%Y/%m/%d') date,COUNT(DISTINCT(dtb_login_history.mtb_user_id)) loginCntAos FROM dtb_login_history INNER JOIN mtb_user ON dtb_login_history.mtb_user_id = mtb_user.id WHERE mtb_user.type = 2 GROUP BY DATE_FORMAT(from_unixtime(dtb_login_history.created),'%Y/%m/%d')) tmpLoginAos ON (tmpLoginAos.date = tmpDate.keydate)

                    LEFT JOIN (SELECT DATE_FORMAT(from_unixtime(dtb_list.created),'%Y/%m/%d') date,COUNT(1) listCnt FROM dtb_list INNER JOIN mtb_user ON dtb_list.mtb_user_id = mtb_user.id WHERE mtb_user.type = 1 AND dtb_list.deleted = 0 GROUP BY DATE_FORMAT(from_unixtime(dtb_list.created),'%Y/%m/%d')) tmpList ON (tmpList.date = tmpDate.keydate)
                    LEFT JOIN (SELECT DATE_FORMAT(from_unixtime(dtb_list.created),'%Y/%m/%d') date,COUNT(1) listCnt FROM dtb_list INNER JOIN mtb_user ON dtb_list.mtb_user_id = mtb_user.id WHERE mtb_user.type = 2 AND dtb_list.deleted = 0 GROUP BY DATE_FORMAT(from_unixtime(dtb_list.created),'%Y/%m/%d')) tmpListAos ON (tmpListAos.date = tmpDate.keydate)
                    LEFT JOIN (SELECT DATE_FORMAT(from_unixtime(dtb_list.created),'%Y/%m/%d') date,COUNT(1) listCnt FROM dtb_list INNER JOIN mtb_user ON dtb_list.mtb_user_id = mtb_user.id WHERE dtb_list.deleted = 0 AND dtb_list.type=2 AND mtb_user.type = 1 GROUP BY DATE_FORMAT(from_unixtime(dtb_list.created),'%Y/%m/%d')) tmpList2 ON (tmpList2.date = tmpDate.keydate)
                    LEFT JOIN (SELECT DATE_FORMAT(from_unixtime(dtb_list.created),'%Y/%m/%d') date,COUNT(1) listCnt FROM dtb_list INNER JOIN mtb_user ON dtb_list.mtb_user_id = mtb_user.id WHERE dtb_list.deleted = 0 AND dtb_list.type=2 AND mtb_user.type = 2 GROUP BY DATE_FORMAT(from_unixtime(dtb_list.created),'%Y/%m/%d')) tmpListAos2 ON (tmpListAos2.date = tmpDate.keydate)

                    LEFT JOIN (SELECT DATE_FORMAT(from_unixtime(created),'%Y/%m/%d') date,COUNT(1) listCnt FROM dtb_list_favorite WHERE dtb_list_favorite.deleted = 0 GROUP BY DATE_FORMAT(from_unixtime(created),'%Y/%m/%d')) tmpListFavorite ON (tmpListFavorite.date = tmpDate.keydate)
                    LEFT JOIN (SELECT DATE_FORMAT(from_unixtime(created),'%Y/%m/%d') date,COUNT(1) listCnt FROM dtb_list_comment GROUP BY DATE_FORMAT(from_unixtime(created),'%Y/%m/%d')) tmpListComment ON (tmpListComment.date = tmpDate.keydate)
                    LEFT JOIN (SELECT DATE_FORMAT(from_unixtime(created),'%Y/%m/%d') date,COUNT(1) listCnt FROM dtb_list_tag GROUP BY DATE_FORMAT(from_unixtime(created),'%Y/%m/%d')) tmpListTag ON (tmpListTag.date = tmpDate.keydate)
                    
                    LEFT JOIN (SELECT DATE_FORMAT(from_unixtime(created),'%Y/%m/%d') date,COUNT(DISTINCT(mtb_user_id)) listCnt FROM dtb_list WHERE dtb_list.deleted = 0 GROUP BY DATE_FORMAT(from_unixtime(created),'%Y/%m/%d')) tmpListUser ON (tmpListUser.date = tmpDate.keydate)
                    LEFT JOIN (SELECT DATE_FORMAT(from_unixtime(created),'%Y/%m/%d') date,COUNT(DISTINCT(mtb_user_id)) listCnt FROM dtb_list WHERE dtb_list.deleted = 0 AND dtb_list.type=2  GROUP BY DATE_FORMAT(from_unixtime(created),'%Y/%m/%d')) tmpList2User ON (tmpList2User.date = tmpDate.keydate)
                    
                    LEFT JOIN admin_memo ON (admin_memo.date = tmpDate.keydate2)
                    ORDER BY tmpDate.keydate ASC";
            $param  = array();
            $result = $this -> getRows($SQL,$param);
            
            return $result;
        }
        
        public function editText()
        {
            $param = array($this->_getParam('date'));
    		$count = $this -> getCount('admin_memo' ,' date = ? ',$param);
    		if($count == 0){
        		$SQL 			= "INSERT INTO admin_memo (memo,date)";
        		$insertParam 	= array($this->_getParam('text'),$this->_getParam('date'));
        		$status 		= $this -> executeIns($SQL,$insertParam);
            }
            else{
                $SQL = "UPDATE admin_memo
                            SET memo = ?
                        WHERE date = ?";
        		$insertParam 	= array($this->_getParam('text'),$this->_getParam('date'));
        		$status = $this -> execute($SQL,$insertParam);
            }
            return $status;
        }
        
        public function deleteText(){
            $SQL = "DELETE FROM admin_memo
                    WHERE date = ?";
    		$insertParam 	= array($this->_getParam('date'));
    		$status = $this -> execute($SQL,$insertParam);
        }
        
        public function getUsersData()
        {
			$page  = $this->_getParam('page',0);
			$limit = 300;
			$limit = $limit*$page . ", $limit ";
            $SQL = "SELECT
                        id,
                        uuid,
                        name,
                        FROM_UNIXTIME(login)   lastLogin,
                        FROM_UNIXTIME(created) install
                    FROM mtb_user
                    ORDER BY id DESC
                    LIMIT ".$limit;
            $param = array();
            $result = $this -> getRows($SQL,$param);
            return $result;
            
        }
        public function getListData()
        {
			$page  = $this->_getParam('page',0);
			$limit = 300;
			$limit = $limit*$page . ", $limit ";
            $SQL = "SELECT
                        dtb_list.id,
                        dtb_list.mtb_user_id,
                        dtb_list.image,
                        dtb_list.type,
                        dtb_list.title,
                        dtb_list.text,
                        dtb_list.iine,
                        dtb_list.comment_count,
                        FROM_UNIXTIME(dtb_list.created) created
                    FROM dtb_list
                    WHERE deleted = 0
                    ORDER BY id DESC
                    LIMIT ".$limit;
            $param = array();
            $result = $this -> getRows($SQL,$param);
            return $result;
            
        }
        
        public function deleteList()
        {
            $SQL = "UPDATE dtb_list
                        SET deleted = 1
                    WHERE id = ?";
            $param = array($this->_getParam('listID',0));
            return $this -> execute($SQL,$param);
        }
        
        public function deleteTag()
        {
            $SQL = "DELETE FROM mtb_tag
                    WHERE id = ?";
            $param = array($this->_getParam('tagID',0));
            $this -> execute($SQL,$param);
            
            $SQL = "DELETE FROM dtb_list_tag
                    WHERE mtb_tag_id = ?";
            $param = array($this->_getParam('tagID',0));
            $this -> execute($SQL,$param);
            
            $SQL = "UPDATE dtb_user_tag
                        SET deleted = 1
                    WHERE mtb_tag_id = ?";
            $param = array($this->_getParam('tagID',0));
            $this -> execute($SQL,$param);
            
            return true;
        }
        
        public function getUserDetail()
        {
            $SQL = "SELECT
                        *,
                        FROM_UNIXTIME(login)   lastLogin,
                        FROM_UNIXTIME(created) install
                    FROM mtb_user
                    WHERE id = ?";
            $param = array($this->_getParam('id',0));
            $result = $this -> getRow($SQL,$param);
            return $result;
        }
        
        public function getTagData()
        {
			$page  = $this->_getParam('page',0);
			$limit = 300;
			$limit = $limit*$page . ", $limit ";
            $SQL = "SELECT
                        mtb_tag.id,
                        mtb_tag.tag
                    FROM mtb_tag
                    ORDER BY id DESC
                    LIMIT ".$limit;
            $param = array();
            $result = $this -> getRows($SQL,$param);
            return $result;
        }

    }

?>