<?PHP
	class TagController extends Action {
    	public function listAction(){
        	$this -> _retrunData = $this -> _model -> taglist();
    	}
    	
    	public function setAction(){
    		if(!$this->_itemCheck('tagID')){
        		$this->errorEnd(パラメーター不足,"パラメーター不足");
            }
        	$this -> _special    = true;
        	$this -> _retrunData = $this -> _model -> setTag();
        }
    	public function setsAction(){
    		if(!$this->_itemCheck('tagIDs')){
        		$this->errorEnd(パラメーター不足,"パラメーター不足");
            }
        	$this -> _special    = true;
        	$this -> _retrunData = $this -> _model -> setsTag();
        }
        
        public function hotAction(){
        	$this -> _retrunData = $this -> _model -> hotTag();
        }
        
    	
    }

?>