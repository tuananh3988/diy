<?PHP
	require_once dirname(__FILE__).'/../../lib/kappa/loader.php';
	$DB = new DB();
	$SQL = "UPDATE dtb_list
	            SET deleted = 1
	        WHERE dtb_list.point - (? - dtb_list.created ) <0
	        AND deleted = 0
	        AND mtb_user_id = -1";
    $param = array(time());
    $DB -> insData($SQL,$param);
    
	$SQL = "UPDATE dtb_list
	            SET deleted = 1
	        WHERE dtb_list.created + 180 < ? 
	        AND deleted = 0
	        AND mtb_user_id > 0";
    $param = array(time());
    $DB -> insData($SQL,$param);
    
	$SQL = "UPDATE dtb_list
	            SET deleted = 1
	        WHERE dtb_list.click > 4 
	        AND deleted = 0
	        AND mtb_user_id > 0";
    $param = array(time());
    $DB -> insData($SQL,$param);
    
    $SQL = "INSERT INTO dtb_list_best(moto_id,now_click,now_seika_ng_count,mtb_user_id,text,monst_id,scheme,quest_name,url,mtb_mokuteki_id,mtb_quest_id,mtb_site_id,point,end_time,click,original_key,created,deleted,seika_ng_count)
            SELECT 
                dtb_list.id,now_click,now_seika_ng_count,mtb_user_id,text,monst_id,scheme,quest_name,url,mtb_mokuteki_id,mtb_quest_id,mtb_site_id,point,end_time,click,original_key,created,deleted,seika_ng_count
            FROM dtb_list
            WHERE deleted != 0
            AND   mtb_user_id != -1";
    $DB -> insData($SQL,array());
    
    $SQL = "INSERT INTO dtb_list_other(moto_id,mtb_user_id,text,monst_id,scheme,quest_name,url,mtb_mokuteki_id,mtb_quest_id,mtb_site_id,point,end_time,click,original_key,created,deleted,seika_ng_count)
            SELECT 
                dtb_list.id,mtb_user_id,text,monst_id,scheme,quest_name,url,mtb_mokuteki_id,mtb_quest_id,mtb_site_id,point,end_time,click,original_key,created,deleted,seika_ng_count
            FROM dtb_list
            WHERE deleted != 0
            AND   mtb_user_id = -1";
    $st = $DB -> insData($SQL,array());
    
	$SQL = "DELETE FROM dtb_list
	        WHERE deleted = 1
	        AND   mtb_user_id = -1";
    $param = array();
    $DB -> insData($SQL,$param);
    
  
	$SQL = "DELETE FROM dtb_list
	        WHERE deleted != 0
	        AND   mtb_user_id != -1";
    $param = array();
    $DB -> insData($SQL,$param);  
?>