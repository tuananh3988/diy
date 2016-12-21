<?PHP
	class SearchController extends Action {
    	public function diyAction(){
        	$this -> _retrunData = $this -> _model -> diySearch();
    	}
    	
    	
    	public function userAction(){
    		if(!$this->_itemCheck('word')){
        		$this->errorEnd(パラメーター不足,"パラメーター不足");
            }
        	$this -> _retrunData = $this -> _model -> userSearch();
        }
        
    	public function osusumeUserAction(){
        	$this -> _retrunData = $this -> _model -> osusumeUser();
        }
        
        public function diyHistoryAction(){
        	$this -> _retrunData = $this -> _model -> diyHistory();
        }
        public function userHistoryAction(){
        	$this -> _retrunData = $this -> _model -> userHistory();
        }
        
        public function diyKekkaAction(){
        	$this -> _retrunData = $this -> _model -> diyKekka();
        }
            	
    }

?>