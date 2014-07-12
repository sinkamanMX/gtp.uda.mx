<?php

class main_AdminController extends My_Controller_Action
{
	public $menu	= 'catalogos';
	public $validateNumbers;
	public $validateAlpha;
	public $dataIn;
	public $idToUpdate=-1;
	public $errors = Array();
	public $operation='init';
	public $resultop=null;	
	
    public function init()
    {
		$sessions = new My_Controller_Auth();
        if(!$sessions->validateSession()){
            $this->_redirect('/');		
		}				
		$this->view->menu	 = $this->menu;	
		$this->dataIn = $this->_request->getParams();
		$this->validateNumbers = new Zend_Validate_Digits();
				
		if(isset($this->dataIn['optReg'])){
			$this->operation = $this->dataIn['optReg'];
			
			if($this->operation=='update'){
				$this->operation = $this->dataIn['optReg'];

				$this->validateAlpha   = new Zend_Validate_Alnum(array('allowWhiteSpace' => true));				
			}	
		}
		
		if(isset($this->dataIn['catId']) && $this->validateNumbers->isValid($this->dataIn['catId'])){
			$this->idToUpdate 	   = $this->dataIn['catId'];	
		}else{
			$this->idToUpdate 	   = -1;
			$this->errors['status'] = 'no-info';
		}			
    }
    
    public function indexAction(){
    	$this->view->mOption = 'units';
		$classObject = new My_Model_Unidades(); 
		$this->view->datatTable = $classObject->getUnidades(1);   	
    }
    
    public function getinfounitAction(){
		$dataInfo = Array();
		$classObject = new My_Model_Unidades();
		$functions = new My_Controller_Functions();
		$transports= new My_Model_Transportistas();
		
		if($this->idToUpdate >-1){
			$dataInfo    = $classObject->getData($this->idToUpdate);
		}
		
		if($this->operation=='update'){			
			if($this->idToUpdate>-1){
				 $updated = $classObject->updateRow($this->dataIn);
				 if($updated['status']){
				 	$dataInfo    = $classObject->getData($this->idToUpdate);
				 	$this->resultop = 'okRegister';	
				 }
			}else{
				$this->errors['status'] = 'no-info';
			}	
		}else if($this->operation=='new'){
			$insert = $classObject->insertRow($this->dataIn);
			if($insert['status']){
				$this->idToUpdate	= $insert['id'];
				$this->resultop = 'okRegister';	
				$dataInfo    = $classObject->getData($this->idToUpdate);
			}else{
				$this->errors['status'] = 'no-insert';
			}
		}else if($this->operation=='delete'){
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender();
			$answer = Array('answer' => 'no-data');
			    
			$this->dataIn['idEmpresa'] = 1; /*Aqui va la variable que venga de la session*/
			$delete = $classObject->deleteRow($this->dataIn);
			if($delete['status']){
				$answer = Array('answer' => 'deleted'); 
			}	

	        echo Zend_Json::encode($answer);
	        die();   			
		}
		
		$this->view->status     = $functions->cboStatus(@$dataInfo['ACTIVO']);
		$this->view->transportistas = $transports->getRowsEmp(1);
		$this->view->data 		= $dataInfo; 
		$this->view->error 		= $this->errors;	
    	$this->view->mOption 	= 'units';
		$this->view->resultOp   = $this->resultop;
		$this->view->catId		= $this->idToUpdate;
		$this->view->idToUpdate = $this->idToUpdate;		
    }
    
    public function clientsAction(){
    	$this->view->mOption = 'clients';
    	
    }    
    
    public function profilesAction(){
    	$this->view->mOption = 'profiles';
    	
    } 

    public function usersAction(){
    	$this->view->mOption = 'users';
    	
    }     
}    