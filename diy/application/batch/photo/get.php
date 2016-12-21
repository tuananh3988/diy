<?PHP
	require_once dirname(__FILE__).'/../../lib/kappa/loader.php';
	
	$DB = new DB();
	$SQL = "SELECT 
				mtb_shop_id 
			FROM dtb_review_photo
			WHERE id > 37
			GROUP BY mtb_shop_id
			ORDER BY mtb_shop_id ASC";
	$param = array();
	$shopData = $DB -> getRows($SQL,$param);
	$shopData = $shopData['data'];
	
	for($i=0;$i<count($shopData);$i++){
		var_dump($i."/".count($shopData));
		$SQL = "SELECT
					photo_url
				FROM dtb_review_photo
				WHERE mtb_shop_id = ?
				ORDER BY id DESC
				LIMIT 1";
		$param  = array($shopData[$i]['mtb_shop_id']);
		$result = $DB -> getRow($SQL,$param);
		$result = $result['data'];
		
		if(isset($result['photo_url'])){
			$SQL = "UPDATE mtb_shop
						SET image = ?
					WHERE id = ?";	
			$param = array($result['photo_url'],$shopData[$i]['mtb_shop_id']);
			$j = $DB -> insData($SQL,$param);
		}
	}
?>