<?PHP
	class SettingController extends Action {
	
    	public function toiawaseAction(){
    		$this -> _retrunData = $this -> _model -> toiawase();
    	}
    		
    	public function pushAction(){
    		if(!$this->_itemCheck('token')){
        		$this->errorEnd(パラメーター不足,"パラメーター不足");
            }
        	$this -> _retrunData = $this -> _model -> insertToken();
    	}
    	
    	public function getImageURLAction(){
        	if(!isset($_FILES["image"])){
            	$imageURL = "";
            }
            else{
        		$imageURL = $this -> _getPhotos($_FILES["image"]);
            }
    		$return   = array('url'=>$imageURL);
    		$this -> _retrunData = $return;
        }
        
        public function passwordAction(){
    		if(!$this->_itemCheck('mail')){
        		$this->errorEnd(パラメーター不足,"パラメーター不足");
            }
        	$this -> _retrunData = $this -> _model -> resetpassword();
        	$this -> _special    = true;
        }
}
?>