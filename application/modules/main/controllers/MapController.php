<?php

class main_MapController extends My_Controller_Action
{
	protected $_clase = 'map';
	public $dataIn;
	public $idToUpdate=-1;
	public $errors = Array();
	public $operation='init';
	public $resultop=null;	
	public $userUpdate=-1;
	
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
		$this->dataIn['userRegister']	= $this->view->dataUser['ID_USUARIO'];
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
    	$this->view->layout()->setLayout('layout_blank');
		$dataInfo 			= Array();
		$clientes 			= '';
		$IdTransportistas 	= -1;		
		$aUnidades			= Array();
		$aOperadores		= Array();
		$aIncidencias		= Array();
		
		$classObject 	= new My_Model_Viajes();
		$functions   	= new My_Controller_Functions();
		$sucursales 	= new My_Model_Sucursales();
		$transportistas = new My_Model_Transportistas();
		
		$this->view->sucursales     = $sucursales->getRowsEmp($this->view->dataUser['ID_EMPRESA']);
		$this->view->transportistas = $transportistas->getRowsEmp($this->view->dataUser['ID_EMPRESA']);
		
    	if($this->idToUpdate >-1){
			$dataInfo    = $classObject->getData($this->idToUpdate);
			
			$clients     	 = new My_Model_Clientes();
			$cboValues       = $clients->getCbo($this->idToUpdate,$this->view->dataUser['ID_EMPRESA']);
			$clientes        = $functions->selectDb($cboValues,$dataInfo['ID_CLIENTE']);

			$operadores  	 = new My_Model_Operadores();
			$IdTransportistas= $operadores->getData($dataInfo['ID_OPERADOR']);	
			
			$unidades		 = new My_Model_Unidades();
			$aUnidades		 = $unidades->getCbo($IdTransportistas['ID_TRANSPORTISTA'],$this->view->dataUser['ID_EMPRESA']);
			
			$aOperadores	 = $operadores->getCbo($IdTransportistas['ID_TRANSPORTISTA'],$this->view->dataUser['ID_EMPRESA']);
			
			$aIncidencias	 = $classObject->getIncidencias($this->idToUpdate);
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
		}				
		
		$this->view->clientes = $clientes; 
		$this->view->idTransportista = $IdTransportistas['ID_TRANSPORTISTA'];
		$this->view->operadores = $aOperadores;
		$this->view->unidades	= $aUnidades;
		$this->view->incidencias= $aIncidencias;
		$this->view->data 		= $dataInfo; 
		$this->view->error 		= $this->errors;	
		$this->view->resultOp   = $this->resultop;
		$this->view->catId		= $this->idToUpdate;
		$this->view->idToUpdate = $this->idToUpdate;		
    }
    
    public function chagestatusAction(){
    		try{   			
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender();    
	                
	        $answer = Array('answer' => 'no-data');
			$data = $this->_request->getParams();
			
			$validateNumbers = new Zend_Validate_Digits();
			$validateString  = new Zend_Validate_Alnum();		
		
			if($validateNumbers->isValid($data['catId'])  && 
				$validateString->isValid($data['option'])){
			
				$idUpdated    = $data['catId'];
				$optionUpdate = $data['option'];
				$statusChange = 0;

				$classObject = new My_Model_Viajes();
				$infoData   = $classObject->getData($idUpdated);
					
				if($optionUpdate=='start'){
					$statusChange = ($infoData['ID_ESTATUS']==1) ? 2 : $infoData['ID_ESTATUS'];
				}else if($optionUpdate=='stop'){
					$statusChange = ($infoData['ID_ESTATUS']==2) ? 4 : $infoData['ID_ESTATUS'];
				}
				
				$updated = $classObject->changeStatus($statusChange,$idUpdated);	
				if($updated){
					$infoData   = $classObject->getData($idUpdated);
					if($infoData['ID_ESTATUS']==2){
						$answer = Array('answer' => 'started');
					}elseif($infoData['ID_ESTATUS']==4){
						$answer = Array('answer' => 'stoped');	
					} 
				}else{
					$answer = Array('answer' => 'problem'); 
				}							
			}
		
		echo Zend_Json::encode($answer);   		
		} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }
    }
    
    public function setincidenciaAction(){
    	$this->view->layout()->setLayout('layout_blank');    	
		$data = $this->_request->getParams();
		$result = false;
		$validateNumbers = new Zend_Validate_Digits();
		$validateString  = new Zend_Validate_Alnum();		
		$travels 		 = new My_Model_Viajes();
		
		if(isset($data['option'])){
			if($validateNumbers->isValid($data['catId'])  && 
				$validateString->isValid($data['option'])){
				
				if($data['option']=='insert'){					
					$data['userRegister']	= $this->view->dataUser['ID_USUARIO'];
					$insert  = $travels->setIncidencia($data);
					if($insert){
						$result =true;
					}
				}
			}			
		} 
		
		$this->view->incidencias = $travels->getTipoIncidencias($this->view->dataUser['ID_EMPRESA']);
		$this->view->insert = $result;
		$this->view->catId = $data['catId'];
    }
}