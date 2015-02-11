<?php
class travels_MainController extends My_Controller_Action
{		
	protected $_clase = 'mtravels';	
	public $validateNumbers;
	public $validateAlpha;
		
    public function init()
    {
    	try{
	    		
			$sessions = new My_Controller_Auth();
			$perfiles = new My_Model_Perfiles();
	        if(!$sessions->validateSession()){
	            $this->_redirect('/');		
			}
			
			$this->_dataUser        = $sessions->getContentSession();
			$this->view->modules    = $perfiles->getModules($this->_dataUser['ID_PERFIL']);
			$this->view->moduleInfo = $perfiles->getDataModule($this->_clase);
			$this->view->idEmpresa  = $this->_dataUser['ID_EMPRESA'];		
			$this->_dataIn			= $this->_request->getParams();
			$this->validateNumbers = new Zend_Validate_Digits();		
					
			if(isset($this->_dataIn['optReg'])){
				$this->_dataOp	 = $this->_dataIn['optReg'];
				
				if($this->_dataOp=='update'){
					$this->_dataOp = $this->_dataIn['optReg'];
	
					$this->validateAlpha   = new Zend_Validate_Alnum(array('allowWhiteSpace' => true));				
				}	
			}
			
			if(isset($this->_dataIn['catId']) && $this->validateNumbers->isValid($this->_dataIn['catId'])){
				$this->_idUpdate	   = $this->_dataIn['catId'];	
			}else{
				$this->_idUpdate 	   = -1;
				$this->_aErrors['status'] = 'no-info';
			}

			$this->view->dataUser = $this->_dataUser;

		} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        } 		
    }
    
    public function indexAction(){
	    try{
	    	$cViajes 	 = new My_Model_Viajes();

	    	$this->view->aViajes 	  = $cViajes->getViajesByDate($this->_dataUser['ID_EMPRESA']);
	    	$this->view->aIncidencias =	$cViajes->getIncidenciasTravels($this->_dataUser['ID_EMPRESA']);
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
		$aRecorrido			= Array();
		$aTipoViajes		= Array();
		$aRutas				= Array();
		$sTipoViaje			= '';
		$sRuta				= '';
		
		$classObject 	= new My_Model_Viajes();
		$functions   	= new My_Controller_Functions();
		$sucursales 	= new My_Model_Sucursales();
		$transportistas = new My_Model_Transportistas();
		$cTipoViajes	= new My_Model_TipoViajes();
		$cRutas			= new My_Model_Rutas();
		
		$this->view->sucursales     = $sucursales->getRowsEmp($this->_dataUser['ID_EMPRESA']);
		$this->view->transportistas = $transportistas->getRowsEmp($this->_dataUser['ID_EMPRESA']);
		$aTipoViajes				= $cTipoViajes->getCbo($this->_dataUser['ID_EMPRESA']);
		$aRutas						= $cRutas->getCbo($this->_dataUser['ID_EMPRESA']);
		
    	if($this->_idUpdate >-1){
			$dataInfo    = $classObject->getData($this->_idUpdate);
			
			$clients     	 = new My_Model_Clientes();
			$cboValues       = $clients->getCbo($dataInfo['ID_SUCURSAL'],$this->_dataUser['ID_EMPRESA']);
			$clientes        = $functions->selectDb($cboValues,$dataInfo['ID_CLIENTE']);

			$operadores  	 = new My_Model_Operadores();
			$IdTransportistas= $operadores->getData($dataInfo['ID_OPERADOR']);	
			
			$unidades		 = new My_Model_Unidades();
			$aUnidades		 = $unidades->getCbo($IdTransportistas['ID_TRANSPORTISTA'],$this->_dataUser['ID_EMPRESA']);			
			$aOperadores	 = $operadores->getCbo($IdTransportistas['ID_TRANSPORTISTA'],$this->_dataUser['ID_EMPRESA']);			
			$aIncidencias	 = $classObject->getIncidencias($this->_idUpdate);			
			$aRecorrido      = $classObject->getRecorrido($this->_idUpdate);
			$sTipoViaje		 = $dataInfo['ID_TIPO_VIAJE'];
			$sRuta			 = $dataInfo['ID_RUTA'];
		}	
		
		if($this->_dataOp=='update'){			
			if($this->_idUpdate>-1){
				 $updated = $classObject->updateRow($this->_dataIn);
				 if($updated['status']){
				 	$dataInfo    = $classObject->getData($this->_idUpdate);
				 	$this->_resultOp = 'okRegister';	
				 }
			}else{
				$this->_aErrors['status'] = 'no-info';
			}	
		}else if($this->_dataOp=='new'){
			$insert = $classObject->insertRow($this->_dataIn);
			if($insert['status']){
				$this->_idUpdate	= $insert['id'];
				$this->_resultOp    = 'okRegister';	
				$dataInfo    = $classObject->getData($this->idToUpdate);
			}else{
				$this->_aErrors['status'] = 'no-insert';
			}
		}				
		
		$this->view->clientes 	= $clientes; 
		$this->view->idTransportista = $IdTransportistas['ID_TRANSPORTISTA'];
		$this->view->operadores = $aOperadores;
		$this->view->unidades	= $aUnidades;
		$this->view->incidencias= $aIncidencias;
		$this->view->data 		= $dataInfo; 
		$this->view->recorrido  = $aRecorrido;
		$this->view->aRutas 	= $functions->selectDb($aRutas,$sRuta);
		$this->view->aTipoViaje	= $functions->selectDb($aTipoViajes,$sTipoViaje);
		$this->view->error 		= $this->_aErrors;	
		$this->view->resultOp   = $this->_resultOp;
		$this->view->catId		= $this->_idUpdate;
		$this->view->idToUpdate = $this->_idUpdate;		
    }    
    
    
    public function newtravelAction(){
    	try{
    		$this->view->layout()->setLayout('layout_blank');  
	
			$dataInfo 			= Array();
			$clientes 			= '';
			$IdTransportistas 	= -1;		
			$aUnidades			= Array();
			$aOperadores		= Array();
			$aIncidencias		= Array();
			$aRecorrido			= Array();
			$aTipoViajes		= Array();
			$aRutas				= Array();
			$sTipoViaje			= '';
			$sRuta				= '';
			
			$classObject 	= new My_Model_Viajes();
			$functions   	= new My_Controller_Functions();
			$sucursales 	= new My_Model_Sucursales();
			$transportistas = new My_Model_Transportistas();
			$cTipoViajes	= new My_Model_TipoViajes();
			$cRutas			= new My_Model_Rutas();
			
			$this->view->sucursales     = $sucursales->getRowsEmp($this->_dataUser['ID_EMPRESA']);
			$this->view->transportistas = $transportistas->getRowsEmp($this->_dataUser['ID_EMPRESA']);
			$aTipoViajes				= $cTipoViajes->getCbo($this->_dataUser['ID_EMPRESA']);
			$aRutas						= $cRutas->getCbo($this->_dataUser['ID_EMPRESA']);
			
	    	if($this->_idUpdate >-1){
				$dataInfo    = $classObject->getData($this->_idUpdate);
				
				$clients     	 = new My_Model_Clientes();
				$cboValues       = $clients->getCbo($dataInfo['ID_SUCURSAL'],$this->_dataUser['ID_EMPRESA']);
				$clientes        = $functions->selectDb($cboValues,$dataInfo['ID_CLIENTE']);
	
				$operadores  	 = new My_Model_Operadores();
				$IdTransportistas= $operadores->getData($dataInfo['ID_OPERADOR']);	
				
				$unidades		 = new My_Model_Unidades();
				$aUnidades		 = $unidades->getCbo($IdTransportistas['ID_TRANSPORTISTA'],$this->_dataUser['ID_EMPRESA']);			
				$aOperadores	 = $operadores->getCbo($IdTransportistas['ID_TRANSPORTISTA'],$this->_dataUser['ID_EMPRESA']);			
				$aIncidencias	 = $classObject->getIncidencias($this->_idUpdate);			
				$aRecorrido      = $classObject->getRecorrido($this->_idUpdate);
				$sTipoViaje		 = $dataInfo['ID_TIPO_VIAJE'];
				$sRuta			 = $dataInfo['ID_RUTA'];
			}	
			
			if($this->_dataOp=='update'){			
				if($this->_idUpdate>-1){
					 $updated = $classObject->updateRow($this->_dataIn);
					 if($updated['status']){
					 	$dataInfo    = $classObject->getData($this->_idUpdate);
					 	$this->_resultOp = 'okRegister';	
					 }
				}else{
					$this->_aErrors['status'] = 'no-info';
				}	
			}else if($this->_dataOp=='new'){
				$insert = $classObject->insertRow($this->_dataIn);
				if($insert['status']){
					$this->_idUpdate	= $insert['id'];
					$this->_resultOp    = 'okRegister';	
					$dataInfo    = $classObject->getData($this->idToUpdate);
				}else{
					$this->_aErrors['status'] = 'no-insert';
				}
			}	    		
    		
    		
    		
    		
			$this->view->clientes 	= $clientes; 
			$this->view->idTransportista = $IdTransportistas['ID_TRANSPORTISTA'];
			$this->view->operadores = $aOperadores;
			$this->view->unidades	= $aUnidades;
			$this->view->incidencias= $aIncidencias;
			$this->view->data 		= $dataInfo; 
			$this->view->recorrido  = $aRecorrido;
			$this->view->aRutas 	= $functions->selectDb($aRutas,$sRuta);
			$this->view->aTipoViaje	= $functions->selectDb($aTipoViajes,$sTipoViaje);
			$this->view->error 		= $this->_aErrors;	
			$this->view->resultOp   = $this->_resultOp;
			$this->view->catId		= $this->_idUpdate;
			$this->view->idToUpdate = $this->_idUpdate;		    		
		} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        } 
    }
	
}