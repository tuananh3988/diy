<?PHP
	class ListController extends Action {
    	public function searchAction(){
        	$this -> _retrunData = $this -> _model -> listSerch();
    	}
    	public function detailAction(){
        	$this -> _retrunData = $this -> _model -> listDetail();
    	}
    	
    	public function favoriteAction(){
    		if(!$this->_itemCheck('listID')){
        		$this->errorEnd(パラメーター不足,"パラメーター不足");
            }
        	$this -> _special    = true;
        	$this -> _retrunData = $this -> _model -> setFavorite();
        }
        public function unfavoriteAction(){
            if(!$this->_itemCheck('listID')){
                $this->errorEnd(パラメーター不足,"パラメーター不足");
            }
            $this -> _special    = true;
            $this -> _retrunData = $this -> _model -> setUnfavorite();
        }
        
        public function favoritelistAction(){
    		if(!$this->_itemCheck('listID')){
        		$this->errorEnd(パラメーター不足,"パラメーター不足");
            }
        	$this -> _retrunData = $this -> _model -> favoriteList();
        }
        
    	public function commentAction(){
    		if(!$this->_itemCheck('listCommentText') || !$this->_itemCheck('listID')  || !$this->_itemCheck('returnUserID')){
        		$this->errorEnd(パラメーター不足,"パラメーター不足");
            }
        	$this -> _special    = true;
        	$this -> _retrunData = $this -> _model -> setComment();
        }
        
        public function commentListAction(){
    		if(!$this->_itemCheck('listID')){
        		$this->errorEnd(パラメーター不足,"パラメーター不足");
            }
        	$this -> _special    = true;
        	$this -> _retrunData = $this -> _model -> commentList();
        }
        
    	public function addAction(){
    		if(!$this->_itemCheck('listText') || !$this->_itemCheck('tags')||
    		   !$this->_itemCheck('reshipis')  || !$this->_itemCheck('zairyos')  || !$this->_itemCheck('listType') || !$this->_itemCheck('listImage') ){
        		$this->errorEnd(パラメーター不足,"パラメーター不足");
            }
        	$this -> _special    = true;
        	$this -> _retrunData = $this -> _model -> setAdd();
        }
        
        public function userAction(){
    		if(!$this->_itemCheck('userID')){
        		$this->errorEnd(パラメーター不足,"パラメーター不足");
            }
        	$this -> _retrunData = $this -> _model -> userList();
        }
        
        public function userfavoriteAction(){
    		if(!$this->_itemCheck('userID')){
        		$this->errorEnd(パラメーター不足,"パラメーター不足");
            }
        	$this -> _retrunData = $this -> _model -> userFavoriteList();
        }
        
        public function deleteAction(){
    		if(!$this->_itemCheck('listID')){
        		$this->errorEnd(パラメーター不足,"パラメーター不足");
            }
        	$this -> _special    = true;
        	$this -> _retrunData = $this -> _model -> listDelete();
        }
        public function updateAction(){
            
    		if(!$this->_itemCheck('listID') || !$this->_itemCheck('listText') || !$this->_itemCheck('tags')||
    		   !$this->_itemCheck('reshipis')  || !$this->_itemCheck('zairyos')   || !$this->_itemCheck('listType') || !$this->_itemCheck('listImage') ){
        		$this->errorEnd(パラメーター不足,"パラメーター不足");
            }
        	$this -> _special    = true;
        	$this -> _retrunData = $this -> _model -> listUpdate();
        }
        
        public function tuhoAction(){
    		if(!$this->_itemCheck('listID')){
        		$this->errorEnd(パラメーター不足,"パラメーター不足");
            }
        	$this -> _special    = true;
        	$this -> _retrunData = $this -> _model -> tuho();
        }
    }

?>