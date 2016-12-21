<?PHP
	require_once dirname(__FILE__).'/loader.php';
	require_once dirname(__FILE__).'/../../modules/Base/function.php';
	require_once dirname(__FILE__).'/../../modules/Base/Actions/Action.php';
	require_once dirname(__FILE__).'/../../modules/Base/Models/Model.php';
	$thisURL = $_SERVER["REQUEST_URI"];
	$sepaleteParameters = explode('?',$thisURL);
	
	ini_set( 'display_errors', 1 );
	
	//アプリ以外の物は遮断
	if(TEST === 0){
		if((strpos($_SERVER['HTTP_USER_AGENT'], APP_UA) === FALSE)){
			if((strpos($_SERVER["REQUEST_URI"], "favicon") === FALSE)){
				$data = array('url'     => isset($_SERVER["REQUEST_URI"])?$_SERVER["REQUEST_URI"]:"",
							  'host'    => isset($_SERVER['REMOTE_HOST'])?$_SERVER["REMOTE_HOST"]:"",
							  'REFERER' => isset($_SERVER["HTTP_REFERER"])?$_SERVER["HTTP_REFERER"]:"",
							  'ip1'     => isset($_SERVER['HTTP_X_FORWARDED_FOR'])?$_SERVER["HTTP_X_FORWARDED_FOR"]:"",
							  'ip2'     => isset($_SERVER["REMOTE_ADDR"])?$_SERVER["REMOTE_ADDR"]:"",
							  'time'    => time(),
							  'ua'      => isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER["HTTP_USER_AGENT"]:""
							  );
				$saveData = json_encode($data);
				$saveFileName = dirname(__FILE__)."/../../logs/fuckingConnect/".time().'.json';
				file_put_contents( $saveFileName ,$saveData);
				chmod( $saveFileName, 0777 );
			}
    		header("HTTP/1.0 403 Forbidden");
            $jsonData = array('response'  => array('data'=>array()),
            				  'error'     => array('code'    => "1111",
            									   'message' => "root1"),
            				  'timestamp'      => (int)time());
            $jsonData = json_encode($jsonData);
            echo $jsonData;
    		die();
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
	
	if($sepaleteURLs[0]=="" || count($sepaleteURLs)<2){
    	if($sepaleteURLs[0]==""){
    		$sepaleteURLs[0] == "index";
    		$read            =  "index";
        }
        else{
//    		$sepaleteURLs[0] == "index";
    		$read            =  $sepaleteURLs[0];
        }
		$loader          =  "index";
		$runName         =  "Index";
		$directory       =  "index";
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
        $jsonData = array('response'  => array('data'=>array()),
        				  'error'     => array('code'    => "1111",
        									   'message' => "root3"),
        				  'timestamp'      => (int)time());
        $jsonData = json_encode($jsonData);
        echo $jsonData;
		die();
	}
	
	require_once dirname(__FILE__).'/../../modules/Detail/Models/' .$directory."/".$loader.'.php';
	require_once dirname(__FILE__).'/../../modules/Detail/Actions/'.$directory."/".$loader.'.php';
	
	try {
		if(method_exists($runName,$read)){
    		header("HTTP/1.1 200 OK");
			$run = new $runName($runModelName);
			$run -> $read();
		}
		else{
    		header("HTTP/1.0 403 Forbidden");
            $jsonData = array('response'  => array('data'=>array()),
            				  'error'     => array('code'    => "1111",
            									   'message' => "root2"),
            				  'timestamp'      => (int)time());
            $jsonData = json_encode($jsonData);
            echo $jsonData;
    		die();
		}
	} catch (Exception $e) {
		header("HTTP/1.0 403 Forbidden");
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