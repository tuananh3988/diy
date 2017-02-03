<?PHP
	class IndexModel extends Model {
    	
    	public function getAllData()
    	{
                
            if ($this->_getParam('date1') == -1 && $this->_getParam('date2') == -1) {
                $days = 30;
                $date2 = date("Y-m-d");
                $date1 = "2016/09/27";
            }
            else {
                $date1  = $this->_getParam('date1', "2016/09/27");
                $date1 = empty($date1) ? "2016/09/27" : $date1;
                $date2  = $this->_getParam('date2', date("Y-m-d"));
                $date2 = empty($date2) ? date("Y-m-d") : $date2;
                $datetime1 = date_create($date1);
                $datetime2 = date_create($date2);
                $interval = date_diff($datetime1, $datetime2);
                $days = $interval->format("%a") + 1;
            }
            
            $select = "(SELECT DATE_FORMAT(date_add('$date2', interval tmp.generate_series - $days day), '%Y/%m/%d') keydate,DATE_FORMAT(date_add('$date2', interval tmp.generate_series - $days day), '%Y-%m-%d') keydate2 FROM (SELECT 0 generate_series FROM DUAL WHERE (@num:=1-1)*0 UNION ALL SELECT @num:=@num+1 FROM `information_schema`.COLUMNS LIMIT $days) tmp)";

        	$SQL = "SELECT
                        tmpDate.keydate2                 keydate,
                        IFNULL(tmpInstall.installCnt,0)  installCnt,
                        IFNULL(tmpInstall2.installCnt,0) installCnt2,
                        IFNULL(tmpInstall2Women.installCnt,0) installCnt2Women,
                        IFNULL(tmpInstall2Men.installCnt,0) installCnt2Men,
                        IFNULL(tmpAosInstall.installAosCnt,0)  installCntAos,
                        IFNULL(tmpAosInstall2.installAosCnt,0) installCntAos2,
                        IFNULL(tmpAosInstall2Men.installAosCnt,0) installCntAos2Men,
                        IFNULL(tmpAosInstall2Women.installAosCnt,0) installCntAos2Women,
                        IFNULL(tmpIosDeactive.deactiveIosCnt,0) deactiveIosCnt,
                        IFNULL(tmpIosDeactiveMen.deactiveIosCnt,0) deactiveIosCntMen,
                        IFNULL(tmpIosDeactiveWomen.deactiveIosCnt,0) deactiveIosCntWomen,
                        IFNULL(tmpAosDeactive.deactiveAosCnt,0) deactiveAosCnt,
                        IFNULL(tmpAosDeactiveMen.deactiveAosCnt,0) deactiveAosCntMen,
                        IFNULL(tmpAosDeactiveWomen.deactiveAosCnt,0) deactiveAosCntWomen,
                        (IFNULL(tmpInstall2.installCnt,0) + IFNULL(tmpAosInstall2.installAosCnt,0) - IFNULL(tmpIosDeactive.deactiveIosCnt,0) - IFNULL(tmpAosDeactive.deactiveAosCnt,0)) totalLK,
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
                        IFNULL(tmpList2User.listCnt,0)   listUserCnt2

                    FROM $select tmpDate

                    LEFT JOIN (SELECT DATE_FORMAT(from_unixtime(created),'%Y/%m/%d') date,COUNT(1) installCnt FROM mtb_install WHERE device_type = 1 GROUP BY DATE_FORMAT(from_unixtime(created),'%Y/%m/%d') ) tmpInstall ON (tmpInstall.date = tmpDate.keydate)
                    LEFT JOIN (SELECT DATE_FORMAT(from_unixtime(created),'%Y/%m/%d') date,COUNT(1) installCnt FROM mtb_user WHERE  type = 1 GROUP BY DATE_FORMAT(from_unixtime(created),'%Y/%m/%d') ) tmpInstall2 ON (tmpInstall2.date = tmpDate.keydate)
                    LEFT JOIN (SELECT DATE_FORMAT(from_unixtime(created),'%Y/%m/%d') date,COUNT(1) installCnt FROM mtb_user WHERE  type = 1 AND sex = 0 GROUP BY DATE_FORMAT(from_unixtime(created),'%Y/%m/%d') ) tmpInstall2Men ON (tmpInstall2Men.date = tmpDate.keydate)
                    LEFT JOIN (SELECT DATE_FORMAT(from_unixtime(created),'%Y/%m/%d') date,COUNT(1) installCnt FROM mtb_user WHERE  type = 1 AND sex = 1 GROUP BY DATE_FORMAT(from_unixtime(created),'%Y/%m/%d') ) tmpInstall2Women ON (tmpInstall2Women.date = tmpDate.keydate)
                    LEFT JOIN (SELECT DATE_FORMAT(from_unixtime(created),'%Y/%m/%d') date,COUNT(1) installAosCnt FROM mtb_install WHERE device_type = 2 GROUP BY DATE_FORMAT(from_unixtime(created),'%Y/%m/%d') ) tmpAosInstall ON (tmpAosInstall.date = tmpDate.keydate)
                    LEFT JOIN (SELECT DATE_FORMAT(from_unixtime(created),'%Y/%m/%d') date,COUNT(1) installAosCnt FROM mtb_user WHERE  type = 2 GROUP BY DATE_FORMAT(from_unixtime(created),'%Y/%m/%d') ) tmpAosInstall2 ON (tmpAosInstall2.date = tmpDate.keydate)
                    LEFT JOIN (SELECT DATE_FORMAT(from_unixtime(created),'%Y/%m/%d') date,COUNT(1) installAosCnt FROM mtb_user WHERE  type = 2 AND sex = 0 GROUP BY DATE_FORMAT(from_unixtime(created),'%Y/%m/%d') ) tmpAosInstall2Men ON (tmpAosInstall2Men.date = tmpDate.keydate)
                    LEFT JOIN (SELECT DATE_FORMAT(from_unixtime(created),'%Y/%m/%d') date,COUNT(1) installAosCnt FROM mtb_user WHERE  type = 2 AND sex = 1 GROUP BY DATE_FORMAT(from_unixtime(created),'%Y/%m/%d') ) tmpAosInstall2Women ON (tmpAosInstall2Women.date = tmpDate.keydate)
                    LEFT JOIN (SELECT DATE_FORMAT(from_unixtime(deleted_date),'%Y/%m/%d') date,COUNT(1) deactiveIosCnt FROM mtb_user WHERE deleted = 1 AND type = 1 GROUP BY DATE_FORMAT(from_unixtime(deleted_date),'%Y/%m/%d') ) tmpIosDeactive ON (tmpIosDeactive.date = tmpDate.keydate)
                    LEFT JOIN (SELECT DATE_FORMAT(from_unixtime(deleted_date),'%Y/%m/%d') date,COUNT(1) deactiveIosCnt FROM mtb_user WHERE deleted = 1 AND type = 1 AND sex = 0 GROUP BY DATE_FORMAT(from_unixtime(deleted_date),'%Y/%m/%d') ) tmpIosDeactiveMen ON (tmpIosDeactiveMen.date = tmpDate.keydate)
                    LEFT JOIN (SELECT DATE_FORMAT(from_unixtime(deleted_date),'%Y/%m/%d') date,COUNT(1) deactiveIosCnt FROM mtb_user WHERE deleted = 1 AND type = 1 AND sex = 1 GROUP BY DATE_FORMAT(from_unixtime(deleted_date),'%Y/%m/%d') ) tmpIosDeactiveWomen ON (tmpIosDeactiveWomen.date = tmpDate.keydate)
                    LEFT JOIN (SELECT DATE_FORMAT(from_unixtime(deleted_date),'%Y/%m/%d') date,COUNT(1) deactiveAosCnt FROM mtb_user WHERE deleted = 1 AND type = 2 GROUP BY DATE_FORMAT(from_unixtime(deleted_date),'%Y/%m/%d') ) tmpAosDeactive ON (tmpAosDeactive.date = tmpDate.keydate)
                    LEFT JOIN (SELECT DATE_FORMAT(from_unixtime(deleted_date),'%Y/%m/%d') date,COUNT(1) deactiveAosCnt FROM mtb_user WHERE deleted = 1 AND type = 2 AND sex = 0 GROUP BY DATE_FORMAT(from_unixtime(deleted_date),'%Y/%m/%d') ) tmpAosDeactiveMen ON (tmpAosDeactiveMen.date = tmpDate.keydate)
                    LEFT JOIN (SELECT DATE_FORMAT(from_unixtime(deleted_date),'%Y/%m/%d') date,COUNT(1) deactiveAosCnt FROM mtb_user WHERE deleted = 1 AND type = 2 AND sex = 1 GROUP BY DATE_FORMAT(from_unixtime(deleted_date),'%Y/%m/%d') ) tmpAosDeactiveWomen ON (tmpAosDeactiveWomen.date = tmpDate.keydate)
                    LEFT JOIN (SELECT DATE_FORMAT(from_unixtime(dtb_login_history.created),'%Y/%m/%d') date,COUNT(DISTINCT(dtb_login_history.mtb_user_id)) loginCnt FROM dtb_login_history INNER JOIN mtb_user ON dtb_login_history.mtb_user_id = mtb_user.id WHERE mtb_user.type = 1 GROUP BY DATE_FORMAT(from_unixtime(dtb_login_history.created),'%Y/%m/%d')) tmpLogin ON (tmpLogin.date = tmpDate.keydate)
                    LEFT JOIN (SELECT DATE_FORMAT(from_unixtime(dtb_login_history.created),'%Y/%m/%d') date,COUNT(DISTINCT(dtb_login_history.mtb_user_id)) loginCntAos FROM dtb_login_history INNER JOIN mtb_user ON dtb_login_history.mtb_user_id = mtb_user.id WHERE mtb_user.type = 2 GROUP BY DATE_FORMAT(from_unixtime(dtb_login_history.created),'%Y/%m/%d')) tmpLoginAos ON (tmpLoginAos.date = tmpDate.keydate)

                    LEFT JOIN (SELECT DATE_FORMAT(from_unixtime(dtb_list.created),'%Y/%m/%d') date,COUNT(1) listCnt FROM dtb_list INNER JOIN mtb_user ON dtb_list.mtb_user_id = mtb_user.id WHERE mtb_user.type = 1 AND dtb_list.deleted = 0 GROUP BY DATE_FORMAT(from_unixtime(dtb_list.created),'%Y/%m/%d')) tmpList ON (tmpList.date = tmpDate.keydate)
                    LEFT JOIN (SELECT DATE_FORMAT(from_unixtime(dtb_list.created),'%Y/%m/%d') date,COUNT(1) listCnt FROM dtb_list INNER JOIN mtb_user ON dtb_list.mtb_user_id = mtb_user.id WHERE mtb_user.type = 2 AND dtb_list.deleted = 0 GROUP BY DATE_FORMAT(from_unixtime(dtb_list.created),'%Y/%m/%d')) tmpListAos ON (tmpListAos.date = tmpDate.keydate)
                    LEFT JOIN (SELECT DATE_FORMAT(from_unixtime(dtb_list.created),'%Y/%m/%d') date,COUNT(1) listCnt FROM dtb_list INNER JOIN mtb_user ON dtb_list.mtb_user_id = mtb_user.id WHERE dtb_list.deleted = 0 AND dtb_list.type=2 AND mtb_user.type = 1 GROUP BY DATE_FORMAT(from_unixtime(dtb_list.created),'%Y/%m/%d')) tmpList2 ON (tmpList2.date = tmpDate.keydate)
                    LEFT JOIN (SELECT DATE_FORMAT(from_unixtime(dtb_list.created),'%Y/%m/%d') date,COUNT(1) listCnt FROM dtb_list INNER JOIN mtb_user ON dtb_list.mtb_user_id = mtb_user.id WHERE dtb_list.deleted = 0 AND dtb_list.type=2 AND mtb_user.type = 2 GROUP BY DATE_FORMAT(from_unixtime(dtb_list.created),'%Y/%m/%d')) tmpListAos2 ON (tmpListAos2.date = tmpDate.keydate)

                    LEFT JOIN (SELECT DATE_FORMAT(from_unixtime(created),'%Y/%m/%d') date,COUNT(1) listCnt FROM dtb_list_favorite WHERE dtb_list_favorite.deleted = 0 GROUP BY DATE_FORMAT(from_unixtime(created),'%Y/%m/%d')) tmpListFavorite ON (tmpListFavorite.date = tmpDate.keydate)
                    LEFT JOIN (SELECT DATE_FORMAT(from_unixtime(created),'%Y/%m/%d') date,COUNT(1) listCnt FROM dtb_list_comment GROUP BY DATE_FORMAT(from_unixtime(created),'%Y/%m/%d')) tmpListComment ON (tmpListComment.date = tmpDate.keydate)
                    LEFT JOIN (SELECT DATE_FORMAT(from_unixtime(created),'%Y/%m/%d') date,COUNT(1) listCnt FROM mtb_tag GROUP BY DATE_FORMAT(from_unixtime(created),'%Y/%m/%d')) tmpListTag ON (tmpListTag.date = tmpDate.keydate)
                    
                    LEFT JOIN (SELECT DATE_FORMAT(from_unixtime(created),'%Y/%m/%d') date,COUNT(DISTINCT(mtb_user_id)) listCnt FROM dtb_list WHERE dtb_list.deleted = 0 GROUP BY DATE_FORMAT(from_unixtime(created),'%Y/%m/%d')) tmpListUser ON (tmpListUser.date = tmpDate.keydate)
                    LEFT JOIN (SELECT DATE_FORMAT(from_unixtime(created),'%Y/%m/%d') date,COUNT(DISTINCT(mtb_user_id)) listCnt FROM dtb_list WHERE dtb_list.deleted = 0 AND dtb_list.type=2  GROUP BY DATE_FORMAT(from_unixtime(created),'%Y/%m/%d')) tmpList2User ON (tmpList2User.date = tmpDate.keydate)
                    
                    ORDER BY tmpDate.keydate ASC";
            $param  = array();
            $result = $this -> getRows($SQL,$param);
            
            return $result;
        }
        
        public function getFirstData($date)
        {
            $install = $this->getInstall($date);
            $deactive = $this->getDeactive($date);
            $favorite = $this->getFavorite($date);
            $comment = $this->getComment($date);
            $tag = $this->getTag($date);
            
            $first = array_merge($install, $deactive, $favorite, $comment, $tag);
            return $first;
             
        }
        
        public function getInstall($date) {
            $sql = "SELECT count(*) install
                    FROM  mtb_user 
                    WHERE `name` != '' 
                    AND DATE_FORMAT(from_unixtime(created),'%Y-%m-%d') < '$date'";
            
            $param  = array();
            $result = $this -> getRow($sql, $param);
            return $result;
        }
        
        public function getDeactive($date) {
            $sql = "SELECT count(*) deactive
                    FROM  mtb_user 
                    WHERE deleted = 1 
                    AND DATE_FORMAT(from_unixtime(deleted_date),'%Y-%m-%d') < '$date'";
            
            $param  = array();
            $result = $this -> getRow($sql, $param);
            return $result;
        }
        
        public function getFavorite($date) {
            $sql = "SELECT count(*) favorite
                    FROM dtb_list_favorite 
                    WHERE dtb_list_favorite.deleted = 0  
                    AND DATE_FORMAT(from_unixtime(created),'%Y-%m-%d') < '$date'";
            
            $param  = array();
            $result = $this -> getRow($sql, $param);
            return $result;
        }
        
        public function getComment($date) {
            $sql = "SELECT count(*) comment
                    FROM dtb_list_comment 
                    WHERE DATE_FORMAT(from_unixtime(created),'%Y-%m-%d') < '$date'";
            
            $param  = array();
            $result = $this -> getRow($sql, $param);
            return $result;
        }
        
        public function getTag($date) {
            $sql = "SELECT count(*) tag
                    FROM mtb_tag 
                    WHERE DATE_FORMAT(from_unixtime(created),'%Y-%m-%d') < '$date'";
            
            $param  = array();
            $result = $this -> getRow($sql, $param);
            return $result;
        }

        public function getAllDataMonth()
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
                        IFNULL(tmpInstall2Women.installCnt,0) installCnt2Women,
                        IFNULL(tmpInstall2Men.installCnt,0) installCnt2Men,
                        IFNULL(tmpAosInstall.installAosCnt,0)  installCntAos,
                        IFNULL(tmpAosInstall2.installAosCnt,0) installCntAos2,
                        IFNULL(tmpAosInstall2Men.installAosCnt,0) installCntAos2Men,
                        IFNULL(tmpAosInstall2Women.installAosCnt,0) installCntAos2Women,
                        IFNULL(tmpIosDeactive.deactiveIosCnt,0) deactiveIosCnt,
                        IFNULL(tmpIosDeactiveMen.deactiveIosCnt,0) deactiveIosCntMen,
                        IFNULL(tmpIosDeactiveWomen.deactiveIosCnt,0) deactiveIosCntWomen,
                        IFNULL(tmpAosDeactive.deactiveAosCnt,0) deactiveAosCnt,
                        IFNULL(tmpAosDeactiveMen.deactiveAosCnt,0) deactiveAosCntMen,
                        IFNULL(tmpAosDeactiveWomen.deactiveAosCnt,0) deactiveAosCntWomen,
                        (IFNULL(tmpInstall2.installCnt,0) + IFNULL(tmpAosInstall2.installAosCnt,0) - IFNULL(tmpIosDeactive.deactiveIosCnt,0) - IFNULL(tmpAosDeactive.deactiveAosCnt,0)) totalLK,
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
                    FROM (SELECT DISTINCT DATE_FORMAT(date_add(CURDATE(), interval tmp.generate_series - 30 day), '%Y/%m') keydate,DATE_FORMAT(date_add(CURDATE(), interval tmp.generate_series - 30 day), '%Y-%m') keydate2 FROM (SELECT 0 generate_series FROM DUAL WHERE (@num:=1-1)*0 UNION ALL SELECT @num:=@num+1 FROM `information_schema`.COLUMNS LIMIT 30) tmp WHERE ".$dateWhere.") tmpDate
                    LEFT JOIN (SELECT DATE_FORMAT(from_unixtime(created),'%Y/%m') date,COUNT(1) installCnt FROM mtb_install WHERE device_type = 1 GROUP BY DATE_FORMAT(from_unixtime(created),'%Y/%m') ) tmpInstall ON (tmpInstall.date = tmpDate.keydate)
                    LEFT JOIN (SELECT DATE_FORMAT(from_unixtime(created),'%Y/%m') date,COUNT(1) installCnt FROM mtb_user WHERE type = 1 GROUP BY DATE_FORMAT(from_unixtime(created),'%Y/%m') ) tmpInstall2 ON (tmpInstall2.date = tmpDate.keydate)
                    LEFT JOIN (SELECT DATE_FORMAT(from_unixtime(created),'%Y/%m') date,COUNT(1) installCnt FROM mtb_user WHERE type = 1 AND sex = 0 GROUP BY DATE_FORMAT(from_unixtime(created),'%Y/%m') ) tmpInstall2Men ON (tmpInstall2Men.date = tmpDate.keydate)
                    LEFT JOIN (SELECT DATE_FORMAT(from_unixtime(created),'%Y/%m') date,COUNT(1) installCnt FROM mtb_user WHERE type = 1 AND sex = 1 GROUP BY DATE_FORMAT(from_unixtime(created),'%Y/%m') ) tmpInstall2Women ON (tmpInstall2Women.date = tmpDate.keydate)
                    LEFT JOIN (SELECT DATE_FORMAT(from_unixtime(created),'%Y/%m') date,COUNT(1) installAosCnt FROM mtb_install WHERE device_type = 2 GROUP BY DATE_FORMAT(from_unixtime(created),'%Y/%m') ) tmpAosInstall ON (tmpAosInstall.date = tmpDate.keydate)
                    LEFT JOIN (SELECT DATE_FORMAT(from_unixtime(created),'%Y/%m') date,COUNT(1) installAosCnt FROM mtb_user WHERE type = 2 GROUP BY DATE_FORMAT(from_unixtime(created),'%Y/%m') ) tmpAosInstall2 ON (tmpAosInstall2.date = tmpDate.keydate)
                    LEFT JOIN (SELECT DATE_FORMAT(from_unixtime(created),'%Y/%m') date,COUNT(1) installAosCnt FROM mtb_user WHERE type = 2 AND sex = 0 GROUP BY DATE_FORMAT(from_unixtime(created),'%Y/%m') ) tmpAosInstall2Men ON (tmpAosInstall2Men.date = tmpDate.keydate)
                    LEFT JOIN (SELECT DATE_FORMAT(from_unixtime(created),'%Y/%m') date,COUNT(1) installAosCnt FROM mtb_user WHERE type = 2 AND sex = 1 GROUP BY DATE_FORMAT(from_unixtime(created),'%Y/%m') ) tmpAosInstall2Women ON (tmpAosInstall2Women.date = tmpDate.keydate)
                    LEFT JOIN (SELECT DATE_FORMAT(from_unixtime(deleted_date),'%Y/%m') date,COUNT(1) deactiveIosCnt FROM mtb_user WHERE deleted = 1 AND type = 1 GROUP BY DATE_FORMAT(from_unixtime(deleted_date),'%Y/%m') ) tmpIosDeactive ON (tmpIosDeactive.date = tmpDate.keydate)
                    LEFT JOIN (SELECT DATE_FORMAT(from_unixtime(deleted_date),'%Y/%m') date,COUNT(1) deactiveIosCnt FROM mtb_user WHERE deleted = 1 AND type = 1 AND sex = 0 GROUP BY DATE_FORMAT(from_unixtime(deleted_date),'%Y/%m') ) tmpIosDeactiveMen ON (tmpIosDeactiveMen.date = tmpDate.keydate)
                    LEFT JOIN (SELECT DATE_FORMAT(from_unixtime(deleted_date),'%Y/%m') date,COUNT(1) deactiveIosCnt FROM mtb_user WHERE deleted = 1 AND type = 1 AND sex = 1 GROUP BY DATE_FORMAT(from_unixtime(deleted_date),'%Y/%m') ) tmpIosDeactiveWomen ON (tmpIosDeactiveWomen.date = tmpDate.keydate)
                    LEFT JOIN (SELECT DATE_FORMAT(from_unixtime(deleted_date),'%Y/%m') date,COUNT(1) deactiveAosCnt FROM mtb_user WHERE deleted = 1 AND type = 2 GROUP BY DATE_FORMAT(from_unixtime(deleted_date),'%Y/%m') ) tmpAosDeactive ON (tmpAosDeactive.date = tmpDate.keydate)
                    LEFT JOIN (SELECT DATE_FORMAT(from_unixtime(deleted_date),'%Y/%m') date,COUNT(1) deactiveAosCnt FROM mtb_user WHERE deleted = 1 AND type = 2 AND sex = 0 GROUP BY DATE_FORMAT(from_unixtime(deleted_date),'%Y/%m') ) tmpAosDeactiveMen ON (tmpAosDeactiveMen.date = tmpDate.keydate)
                    LEFT JOIN (SELECT DATE_FORMAT(from_unixtime(deleted_date),'%Y/%m') date,COUNT(1) deactiveAosCnt FROM mtb_user WHERE deleted = 1 AND type = 2 AND sex = 1 GROUP BY DATE_FORMAT(from_unixtime(deleted_date),'%Y/%m') ) tmpAosDeactiveWomen ON (tmpAosDeactiveWomen.date = tmpDate.keydate)
                    LEFT JOIN (SELECT DATE_FORMAT(from_unixtime(dtb_login_history.created),'%Y/%m') date,COUNT(DISTINCT(dtb_login_history.mtb_user_id)) loginCnt FROM dtb_login_history INNER JOIN mtb_user ON dtb_login_history.mtb_user_id = mtb_user.id WHERE mtb_user.type = 1 GROUP BY DATE_FORMAT(from_unixtime(dtb_login_history.created),'%Y/%m')) tmpLogin ON (tmpLogin.date = tmpDate.keydate)
                    LEFT JOIN (SELECT DATE_FORMAT(from_unixtime(dtb_login_history.created),'%Y/%m') date,COUNT(DISTINCT(dtb_login_history.mtb_user_id)) loginCntAos FROM dtb_login_history INNER JOIN mtb_user ON dtb_login_history.mtb_user_id = mtb_user.id WHERE mtb_user.type = 2 GROUP BY DATE_FORMAT(from_unixtime(dtb_login_history.created),'%Y/%m')) tmpLoginAos ON (tmpLoginAos.date = tmpDate.keydate)

                    LEFT JOIN (SELECT DATE_FORMAT(from_unixtime(dtb_list.created),'%Y/%m') date,COUNT(1) listCnt FROM dtb_list INNER JOIN mtb_user ON dtb_list.mtb_user_id = mtb_user.id WHERE mtb_user.type = 1 AND dtb_list.deleted = 0 GROUP BY DATE_FORMAT(from_unixtime(dtb_list.created),'%Y/%m')) tmpList ON (tmpList.date = tmpDate.keydate)
                    LEFT JOIN (SELECT DATE_FORMAT(from_unixtime(dtb_list.created),'%Y/%m') date,COUNT(1) listCnt FROM dtb_list INNER JOIN mtb_user ON dtb_list.mtb_user_id = mtb_user.id WHERE mtb_user.type = 2 AND dtb_list.deleted = 0 GROUP BY DATE_FORMAT(from_unixtime(dtb_list.created),'%Y/%m')) tmpListAos ON (tmpListAos.date = tmpDate.keydate)
                    LEFT JOIN (SELECT DATE_FORMAT(from_unixtime(dtb_list.created),'%Y/%m') date,COUNT(1) listCnt FROM dtb_list INNER JOIN mtb_user ON dtb_list.mtb_user_id = mtb_user.id WHERE dtb_list.deleted = 0 AND dtb_list.type=2 AND mtb_user.type = 1 GROUP BY DATE_FORMAT(from_unixtime(dtb_list.created),'%Y/%m')) tmpList2 ON (tmpList2.date = tmpDate.keydate)
                    LEFT JOIN (SELECT DATE_FORMAT(from_unixtime(dtb_list.created),'%Y/%m') date,COUNT(1) listCnt FROM dtb_list INNER JOIN mtb_user ON dtb_list.mtb_user_id = mtb_user.id WHERE dtb_list.deleted = 0 AND dtb_list.type=2 AND mtb_user.type = 2 GROUP BY DATE_FORMAT(from_unixtime(dtb_list.created),'%Y/%m')) tmpListAos2 ON (tmpListAos2.date = tmpDate.keydate)

                    LEFT JOIN (SELECT DATE_FORMAT(from_unixtime(created),'%Y/%m') date,COUNT(1) listCnt FROM dtb_list_favorite WHERE dtb_list_favorite.deleted = 0 GROUP BY DATE_FORMAT(from_unixtime(created),'%Y/%m')) tmpListFavorite ON (tmpListFavorite.date = tmpDate.keydate)
                    LEFT JOIN (SELECT DATE_FORMAT(from_unixtime(created),'%Y/%m') date,COUNT(1) listCnt FROM dtb_list_comment GROUP BY DATE_FORMAT(from_unixtime(created),'%Y/%m')) tmpListComment ON (tmpListComment.date = tmpDate.keydate)
                    LEFT JOIN (SELECT DATE_FORMAT(from_unixtime(created),'%Y/%m') date,COUNT(1) listCnt FROM mtb_tag GROUP BY DATE_FORMAT(from_unixtime(created),'%Y/%m')) tmpListTag ON (tmpListTag.date = tmpDate.keydate)
                    
                    LEFT JOIN (SELECT DATE_FORMAT(from_unixtime(created),'%Y/%m') date,COUNT(DISTINCT(mtb_user_id)) listCnt FROM dtb_list WHERE dtb_list.deleted = 0 GROUP BY DATE_FORMAT(from_unixtime(created),'%Y/%m')) tmpListUser ON (tmpListUser.date = tmpDate.keydate)
                    LEFT JOIN (SELECT DATE_FORMAT(from_unixtime(created),'%Y/%m') date,COUNT(DISTINCT(mtb_user_id)) listCnt FROM dtb_list WHERE dtb_list.deleted = 0 AND dtb_list.type=2  GROUP BY DATE_FORMAT(from_unixtime(created),'%Y/%m')) tmpList2User ON (tmpList2User.date = tmpDate.keydate)
                    
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
                        mtb_user.name,
                        FROM_UNIXTIME(dtb_list.created) created
                    FROM dtb_list
                    INNER JOIN mtb_user ON dtb_list.mtb_user_id = mtb_user.id
                    WHERE dtb_list.deleted = 0
                    ORDER BY dtb_list.id DESC
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
