<?php

class main_DashboardController extends My_Controller_Action
{
	protected $_clase = 'dashboard';
	public    $dataIn = Array();
	protected $idEmpresa = -1;
	public    $aDbTables = Array (  'clients' => Array('nameClass'=>'Clientes'),
								    'units'  => Array('nameClass'=>'Unidades'),
									'operators'  => Array('nameClass'=>'Operadores')
	
						/*Array('class' => 'clients'   ,'nameClass'=>'Clientes'),
						Array('class' => 'units'     ,'nameClass'=>'Unidades'),
						Array('class' => 'operators' ,'nameClass'=>'Operadores')*/
						);
	
    public function init()
    {
		$sessions = new My_Controller_Auth();
		$perfiles = new My_Model_Perfiles();
        if(!$sessions->validateSession()){
            $this->_redirect('/');		
		}
		$this->view->dataUser   = $sessions->getContentSession();
		$this->view->modules    = $perfiles->getModules($this->view->dataUser['ID_PERFIL']);
		$this->idEmpresa		= $this->view->dataUser['ID_EMPRESA'];
		$this->view->moduleInfo = $perfiles->getDataMenu($this->_clase);
    }
    
    public function indexAction(){
    	
    }
    
    public function getselectAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();    
		    	
    	$result = 'no-info';
		$this->dataIn = $this->_request->getParams();
		$functions = new My_Controller_Functions();
		
		$validateNumbers = new Zend_Validate_Digits();
		$validateAlpha   = new Zend_Validate_Alnum(array('allowWhiteSpace' => true));		

		if($validateNumbers->isValid($this->dataIn['catId']) && $validateAlpha->isValid($this->dataIn['oprDb'])){
			Zend_Debug::dump($this->aDbTables);
			if(isset($this->aDbTables[$this->dataIn['class']['oprDb']])){
				$classObject = eval(" new My_Model_".$this->aDbTables['class']['oprDb']."()");
				$cboValues   = $classObject->getCbo($this->dataIn['catId'],$this->idEmpresa);
				$result      = $functions->selectDb($cboValues);		
			}
		}
		
		echo $result;
    }
}