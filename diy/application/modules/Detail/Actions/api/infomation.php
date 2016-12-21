<?PHP
	class InfomationController extends Action {
    	public function listAction(){
        	$this -> _retrunData = $this -> _model -> infomationList();
    	}
    	
    		
    }

?>