<?PHP
	class SuperController extends Action {
		
	public function donAction(){
    	$showFlg = "";

    	$this -> _model -> updateLoginSetting();
        $data = array('review_title' => "レビュータイトル",
                      'review_msg'   => "レビューメッセージ"
                      );
		$this -> _retrunData = $data;
	}
	
}

?>