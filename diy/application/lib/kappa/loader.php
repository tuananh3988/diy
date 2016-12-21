<?PHP
	date_default_timezone_set('UTC');
	#define
	//設定ファイル
	define('INIFILE'  , dirname(__FILE__).'/../../configs/config.ini');
	//errorファイルログ
	define('LOGFILE'  , dirname(__FILE__).'/../../logs/');
	
	//モンストスキーム
	define('SCHEME',"monsterstrike-app://joingame/?join=");
	
	//アプリUA
	define('APP_UA','');
	
	//ページカウント
	define('LIMIT_COUNT','20');
	
	//ファイル保存場所
	//1:ローカル
	//0:S3
	define('LOCAL',1);
	define('PHOTO_PATH',dirname(__FILE__).'/../../../htdocs/tmp/photos/');
	define('PHOTO_URL_PATH','tmp/photos/');
	
	//S3key
	define('ACCSESSKEY',0);
	//S3key
	define('SECRETKEY',0);
	//bucketName
	define('BUCKETNAME','');
	
	//APPLE接続情報
	define('RECEIPT_DIS','https://buy.itunes.apple.com/verifyReceipt');
	define('RECEIPT_DEV','https://sandbox.itunes.apple.com/verifyReceipt');
		
	#エラーコード
	define('パラメーター不足'		,100);
	define('トークン不正'			,101);
	define('禁止ワード'			    ,201);
	define('登録済み'				,288);
	define('予期せぬエラー'			,299);
	define('ブロック'   			,9999);
	
	#require
	//DB
	require_once dirname(__FILE__).'/DB.php';
	
?>