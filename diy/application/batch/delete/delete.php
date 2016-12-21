<?PHP
	require_once dirname(__FILE__).'/../../lib/kappa/loader.php';
	$DB = new DB();
	$SQL = "TRUNCATE TABLE dtb_list";
    $param = array();
    $DB -> insData($SQL,$param);
    
	$SQL = "TRUNCATE TABLE dtb_show_best";
    $param = array();
    $DB -> insData($SQL,$param);
    
    $SQL = "UPDATE mtb_user
                SET jumped = 0";
    $DB -> insData($SQL,$param);
?>