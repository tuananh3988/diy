<?PHP
	require_once dirname(__FILE__).'/loader.php';
	require_once dirname(__FILE__).'/../../modules/Base/function.php';
	require_once dirname(__FILE__).'/../../modules/Base/Actions/Action.php';
	require_once dirname(__FILE__).'/../../modules/Base/Models/Model.php';
	$thisURL = $_SERVER["REQUEST_URI"];
	$sepaleteParameters = explode('?',$thisURL);
	
	ini_set( 'display_errors', TEST );
	
	//アプリ以外の物は遮断
	if(strpos($_SERVER['HTTP_USER_AGENT'], "Android") === FALSE){
    	if(TEST===0 && !strpos($_SERVER["REQUEST_URI"],"ad")){
    		if((strpos($_SERVER['HTTP_USER_AGENT'], APP_UA) === FALSE)){
    			if((strpos($_SERVER["REQUEST_URI"], "favicon") === FALSE)){
    				$data = array('url'     => $_SERVER["REQUEST_URI"],
    							  'host'    => $_SERVER['REMOTE_HOST'],
    							  'REFERER' => $_SERVER["HTTP_REFERER"],
    							  'ip1'     => $_SERVER['HTTP_X_FORWARDED_FOR'],
    							  'ip2'     => $_SERVER["REMOTE_ADDR"],
    							  'time'    => time(),
    							  'ua'      => $_SERVER['HTTP_USER_AGENT']
    							  );
    				$saveData = json_encode($data);
    				$saveFileName = dirname(__FILE__)."/../../logs/fuckingConnect/".time().'.json';
    				file_put_contents( $saveFileName ,$saveData);
    				chmod( $saveFileName, 0777 );
    			}
    			header("HTTP/1.0 404 Not Found");
    			die();
    		}
    	}
    }
	
	$sepaleteURLs       = explode('/',$sepaleteParameters[0]);
	
	unset($sepaleteURLs[0]);
	$sepaleteURLs       = array_merge($sepaleteURLs);
	
	$loader    = "";
	$read      = "";
	$runName   = "";
	$directory = "";
	$post	   = false;
	
	if($sepaleteURLs[0]==""){
		
		$fileName = LOGFILE;
		$fileName .= "accsess/error/";
		$day       = getdate();
		$fileName .= $day['year'].$day['mon'].$day['mday'].".log";
		$data      = "[".$day['hours'].":".mb_substr("0".$day['minutes'],-2,2,"UTF-8").":".mb_substr("0".$day['seconds'],-2,2,"UTF-8")."] ip:". $_SERVER["REMOTE_ADDR"]." url:".$_SERVER["REQUEST_URI"];
    	$data      .= " REQUEST:".json_encode($_REQUEST);
		
		file_put_contents($fileName, $data. PHP_EOL,FILE_APPEND);
		
		header("HTTP/1.0 403 Forbidden");
		echo "bye";
		die();
	}
	else{
		
		$directory = $sepaleteURLs[0];
		
		if(isset($sepaleteURLs[1])){
			$loader = $sepaleteURLs[1];
		}
		else{
			$loader = "";
		}
		
		for($i=0;$i<strlen($loader);$i++){
			$ins = $loader[$i];
			if($i==0){
				$ins = strtoupper($ins);
			}
			$runName .= $ins;
		}
		
		$read = "";
		for($i=2;$i<count($sepaleteURLs);$i++){
			$read .= $sepaleteURLs[$i];
		}
		if($read==""){
			$read = "index";
		}
	}
	if($runName=="Post"){
		$post = true;
	}
	else{
		$post = false;
	}
	
	$runModelName  = $runName."Model";
	$runName      .= "Controller";
	$read         .= "Action";
	//実行
	if (!(file_exists(dirname(__FILE__).'/../../modules/Detail/Models/' .$directory."/".$loader.'.php') && dirname(__FILE__).'/../../modules/Detail/Actions/'.$directory."/".$loader.'.php')) {
		
		$fileName = LOGFILE;
		$fileName .= "accsess/error/";
		$day       = getdate();
		$fileName .= $day['year'].$day['mon'].$day['mday'].".log";
		$data      = "[".$day['hours'].":".mb_substr("0".$day['minutes'],-2,2,"UTF-8").":".mb_substr("0".$day['seconds'],-2,2,"UTF-8")."] ip:". $_SERVER["REMOTE_ADDR"]." url:".$_SERVER["REQUEST_URI"];
    	$data      .= " REQUEST:".json_encode($_REQUEST);
		file_put_contents($fileName, $data. PHP_EOL,FILE_APPEND);
		
		header("HTTP/1.0 403 Forbidden");
		echo "bye";
		die();
	}
	require_once dirname(__FILE__).'/../../modules/Detail/Models/' .$directory."/".$loader.'.php';
	require_once dirname(__FILE__).'/../../modules/Detail/Actions/'.$directory."/".$loader.'.php';
	
	try {
		if(method_exists($runName,$read)){
			$run = new $runName($runModelName,$post);
			$run -> $read();
		}
		else{
			$error = array('error_code'=>300,'message'=>'nothing');
			echo json_encode($error);
			die();
		}
	} catch (Exception $e) {
		$error = array('error_code'=>300,'message'=>'nothing');
		echo json_encode($error);
		die();
	}
	
	$fileName = LOGFILE;
	$fileName .= "accsess/";
	$day       = getdate();
	$fileName .= $day['year'].$day['mon'].$day['mday'].".log";
	$data      = "[".$day['hours'].":".mb_substr("0".$day['minutes'],-2,2,"UTF-8").":".mb_substr("0".$day['seconds'],-2,2,"UTF-8")."] ip:". $_SERVER["REMOTE_ADDR"]." url:".$_SERVER["REQUEST_URI"];
	$data      .= " REQUEST:".json_encode($_REQUEST);
	
	file_put_contents($fileName, $data. PHP_EOL,FILE_APPEND);
?>