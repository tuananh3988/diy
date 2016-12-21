<?PHP
	class UserController extends Action {
    	public function editAction(){
        	$this -> _special    = true;
        	$this -> _retrunData = $this -> _model -> editUser();
    	}
    	
    	public function loginAction(){
        	$this -> _special    = true;
        	$this -> _retrunData = $this -> _model -> loginUser();
        }
    	
    	
    	public function mypageAction(){
        	$this -> _retrunData = $this -> _model -> myData();
        }
        
        public function detailAction(){
        	$this -> _retrunData = $this -> _model -> userDetail();
        }
        
        public function myTokoAction(){
        	$this -> _retrunData = $this -> _model -> myToko();
        }
        
        public function favoriteAction(){
        	$this -> _retrunData = $this -> _model -> myFavorite();
        }
        
        public function torokuAction(){
            //type1 メール
            //type2 fb
            //type3 twi
            //type4 google
    		if(!$this->_itemCheck('type') || !$this->_itemCheck('loginID') ){
        		$this->errorEnd(パラメーター不足,"パラメーター不足");
            }
        	$this -> _special    = true;
        	$this -> _retrunData = $this -> _model -> createUser();
        }
        
        public function followAction(){
    		if(!$this->_itemCheck('userID')){
        		$this->errorEnd(パラメーター不足,"パラメーター不足");
            }
        	$this -> _special    = true;
        	$this -> _retrunData = $this -> _model -> setFollow();
        }
        
        public function followlistAction(){
    		if(!$this->_itemCheck('userID')){
        		$this->errorEnd(パラメーター不足,"パラメーター不足");
            }
        	$this -> _retrunData = $this -> _model -> followList();
        }
        public function followerlistAction(){
    		if(!$this->_itemCheck('userID')){
        		$this->errorEnd(パラメーター不足,"パラメーター不足");
            }
        	$this -> _retrunData = $this -> _model -> followerList();
        }
        public function tagslistAction(){
    		if(!$this->_itemCheck('userID')){
        		$this->errorEnd(パラメーター不足,"パラメーター不足");
            }
        	$this -> _retrunData = $this -> _model -> tagsList();
        }
        
        public function logoutAction(){
        	$this -> _retrunData = $this -> _model -> logout();
        	$this -> _special    = true;
        }
    }

?>