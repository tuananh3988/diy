<?PHP

	require_once dirname(__FILE__).'/../../lib/kappa/monst.php';
	$MONST = new MONST();
	
	$key = "412053582";
	$data = $MONST->monst_multi_encode($key);
	die(var_dump($data));
?>