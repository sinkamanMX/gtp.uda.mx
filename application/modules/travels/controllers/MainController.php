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
				//$this->_aErrors['status'] = 'no-info';
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
	    	$this->view->aViajesNoPay = $cViajes->getViajesnoPay($this->_dataUser['ID_EMPRESA']);
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
			$aClientes 			= Array();
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
									
			$aNamespace = new Zend_Session_Namespace("solTravel");
		
			if($this->_dataOp=='registerTravel'){
				if(isset($aNamespace->dataGral)){
					unset($aNamespace->dataGral);					
				}
				$aNamespace->dataGral = $this->_dataIn;
				$this->_redirect("/travels/main/paytravel");
			}
						
			if(isset($aNamespace->dataGral)){
				$this->_dataIn = $aNamespace->dataGral;
				
				$aClients     	 = new My_Model_Clientes();
				$cboValues       = $aClients->getCbo($this->_dataIn['inputSucursal'],$this->_dataUser['ID_EMPRESA']);
				$aClientes       = $functions->selectDb($cboValues,$this->_dataIn['inputCliente']);
	
				$cOperadores  	 = new My_Model_Operadores();
				$IdTransportistas= $cOperadores->getData($this->_dataIn['inputOperadores']);	
				
				$cUnidades		 = new My_Model_Unidades();
				$aUnidades		 = $cUnidades->getCbo($IdTransportistas['ID_TRANSPORTISTA'],$this->_dataUser['ID_EMPRESA']);			
				$aOperadores	 = $cOperadores->getCbo($IdTransportistas['ID_TRANSPORTISTA'],$this->_dataUser['ID_EMPRESA']);
				$sTipoViaje		 = $this->_dataIn['inputTviaje'];
				$sRuta			 = $this->_dataIn['inputRuta'];					
			}				
			
			$this->view->clientes 	= $aClientes; 
			$this->view->idTransportista = $IdTransportistas['ID_TRANSPORTISTA'];
			$this->view->operadores = $aOperadores;
			$this->view->unidades	= $aUnidades;
			$this->view->incidencias= $aIncidencias;
			$this->view->data 		= $this->_dataIn; 
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
    
    public function paytravelAction(){
		try{
			$aTravelInfo  = Array();
			$aMoreInfo 	  = Array();
			$aTipoViaje	  = Array();
			$cViajes	  = new My_Model_Viajes(); 
			$cIncidencias = new My_Model_Incidencias();
			$cFunciones	  = new My_Controller_Functions();
			$cRutas		  = new My_Model_Rutas();
			$cTipoViajes  = new My_Model_TipoViajes();
			$cExtras	  = new My_Model_Extras();
			$aIncidencias = $cIncidencias->getIncidenciasCosto();
			
			$aNamespace = new Zend_Session_Namespace("solTravel");
			if(isset($aNamespace->dataGral)){
				$aTravelInfo = $aNamespace->dataGral;					
				$aMoreInfo	 = $cRutas->getData($aTravelInfo['inputRuta']);
				$aTipoViaje	 = $cTipoViajes->getData($aTravelInfo['inputTviaje']);
				
				if($this->_dataOp=="registerTravel"){	
					$aTravelInfo['userRegister'] = $this->_dataUser['ID_USUARIO'];				
					$insertViaje = $cViajes->insertTravel($aTravelInfo);
					if($insertViaje['status']){
						$idViaje = $insertViaje['id'];
						
						foreach($aIncidencias as $key => $items){
							if(isset($this->_dataIn['cboInc_'.$items['ID_INCIDENCIA']])){
								if($this->_dataIn['cboInc_'.$items['ID_INCIDENCIA']]>0){
									$iCountInc	= $this->_dataIn['cboInc_'.$items['ID_INCIDENCIA']];
									$aInfoInc   = $cIncidencias->getData($items['ID_INCIDENCIA']);
									
									$dataInsert   = Array();
									$dataInsert['inputIdViaje'] 	= $idViaje;
									$dataInsert['inputIncidencia'] 	= $items['ID_INCIDENCIA'];
									$dataInsert['inputTotal'] 		= $aInfoInc['PRECIO_EXTRA'] * $iCountInc;
									$dataInsert['inputCantidad']    = $iCountInc;  
									$insertRelInc = $cExtras->insertTravel($dataInsert);	
									if(!$insertRelInc){
										$this->_aErrors['no-insert'] = 1;
									}
								}
							}
						}
						
						if(count($this->_aErrors)==0){
							unset($aNamespace->dataGral);
							$this->_redirect("/travels/main/resume?catId=".$idViaje);
						}else{
							$this->_aErrors['no-insert'] = 1;		
						}
					}else{
						$this->_aErrors['no-insert'] = 1;
					}
				}				
			}else{
				$this->_redirect("/travels/main/newtravel");
			}
			
			$this->view->aIncidencias = $aIncidencias;
			$this->view->aNumbers	  = $cFunciones->cbo_number(26);
			$this->view->dataTravel   = $aTravelInfo;
			$this->view->aMoreInfo	  = $aMoreInfo;
			$this->view->aTipoViaje	  = $aTipoViaje;
			$this->view->aErrors	  = $this->_aErrors;
		}catch(Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }  	
    }
    
    public function getpreciosAction(){
    	try{   			
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender();    
	                
	        $answer = Array('answer' => 'no-data');	        
			$data = $this->_request->getParams();
	        if($this->_idUpdate > -1){
	        	$cTipoViajes = new My_Model_TipoViajes();
	        	
	        	$aDataViaje	 = $cTipoViajes->getData($this->_idUpdate);
	        	$answer = Array('answer' => $aDataViaje);
	        }else{
	            $answer = Array('answer' => 'problem');	
	        }
	        echo Zend_Json::encode($answer);   
		} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }
    }
    
    public function resumeAction(){
		try{
			$aTravelInfo  = Array();
			$aMoreInfo 	  = Array();
			$aTipoViaje	  = Array();
			$cViajes	  = new My_Model_Viajes(); 
			$cIncidencias = new My_Model_Incidencias();
			$cFunciones	  = new My_Controller_Functions();
			$cRutas		  = new My_Model_Rutas();
			$cTipoViajes  = new My_Model_TipoViajes();
			$cExtras	  = new My_Model_Extras();
			$aTravelInfo  = $cViajes->getData($this->_idUpdate);
								
			$aMoreInfo	  = $cRutas->getData($aTravelInfo['ID_RUTA']);
			$aTipoViaje	  = $cTipoViajes->getData($aTravelInfo['ID_TIPO_VIAJE']);			
			
			$this->view->aIncidencias = $cExtras->getIncidenciasByTravel($this->_idUpdate);
			$this->view->aNumbers	  = $cFunciones->cbo_number(26);
			$this->view->dataTravel   = $aTravelInfo;
			$this->view->aMoreInfo	  = $aMoreInfo;
			$this->view->aTipoViaje	  = $aTipoViaje;
			$this->view->aErrors	  = $this->_aErrors;			
			
		}catch(Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        } 	
    }
	
}