<?php

class main_MapController extends My_Controller_Action
{
	protected $_clase = 'map';
	public $dataIn;
	public $idToUpdate=-1;
	public $errors = Array();	
	
    public function init()
    {
		$sessions = new My_Controller_Auth();
		$perfiles = new My_Model_Perfiles();
        if(!$sessions->validateSession()){
            $this->_redirect('/');		
		}
		$this->view->dataUser   = $sessions->getContentSession();
		$this->view->modules    = $perfiles->getModules($this->view->dataUser['ID_PERFIL']);
		$this->view->moduleInfo = $perfiles->getDataModule($this->_clase);

	
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
    	try{

    		
		} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        } 
    }
    
    public function getravelsAction(){
    	$result = '';
		try{  
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender();

    		$travels = new My_Model_Viajes();
    		$travelsOn = $travels->getRowsEmp($this->view->dataUser['ID_EMPRESA']);
    		
    		foreach ($travelsOn as $value => $items){
    			$infoLastP = $travels->lastPosition($items['ID_VIAJE']);
    			$dataposition = '';
    			if(count($infoLastP)>0){    				
    				$dataposition = $infoLastP['LATITUD']."|".$infoLastP['LONGITUD']."|".$infoLastP['FECHA']."|".
    								$infoLastP['UBICACION']."|".round($infoLastP['VELOCIDAD'],2)."|".$infoLastP['ANGULO']."|".$infoLastP['MODO'];
    			}else{
    				$dataposition = "null|null|null|".
    								"null|null|null|null";
    			}
				$result .=  ($result=="") ? "" : "!";
				$result .=  $items['ID_VIAJE']."|".
							$items['CLAVE']."|".
							$items['INICIO']."|".
							$items['FIN']."|".
							$items['RETRASO']."|".
							$items['CLIENTE']."|".
							$items['ECONOMICO']."|".
							$items['ICONO']."|".
							$items['SUCURSAL']."|".
							$items['ID_ESTATUS']."|".
							$dataposition;	
    		}
			echo $result;
		} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }    	
    }
    
    public function infotravelAction(){
		$dataInfo = Array();
		$this->view->layout()->setLayout('layout_blank');
		
		$classObject = new My_Model_Viajes();
		$functions   = new My_Controller_Functions();
		
		$sucursales = new My_Model_Sucursales();
		
		$this->view->sucursales = $sucursales->getRowsEmp($this->view->dataUser['ID_EMPRESA']);

		
		/*
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
			    
			$this->dataIn['idEmpresa'] = 1; 
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

		*/
    }
}