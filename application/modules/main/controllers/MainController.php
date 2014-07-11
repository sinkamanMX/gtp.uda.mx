<?php

class main_MainController extends My_Controller_Action
{
	public $menu	= 'index';
	public $mOption = 'mapa';
	
    public function init()
    {
		$this->view->layout()->setLayout('layout_blank');
		$this->view->mOption = $this->mOption;
		$this->view->menu	 = $this->menu;	
    }

    public function indexAction()
    {
		try{
			$sessions = new My_Controller_Auth();
	        if($sessions->validateSession()){
	            $this->_redirect('/main/dashboard/index');		
			}				
			
			/*
            $sessions = new My_Controller_Auth();
            $promos = new My_Model_Promociones();
            $usuarios = new My_Model_Usuarios();
            //$distribuidores = new My_Model_Distribuidores();
            
            $fc = Zend_controller_front::getInstance();            
            $dataUserSession = $sessions->getContentSession();            
            
            $logged   = ($sessions->validateSession()) ? true: false;  
            
            if($logged){
            	$dataUser = $usuarios->getDataUser($dataUserSession['IdUsuario']);	
            }
            
            $this->view->logged   = $logged;  
            $this->view->UserName = $dataUserSession['nombreUsuario'];
            $this->view->promos   = $promos->getPromociones();
        	$this->view->baseUrl  = $fc->getBaseUrl();
        	//$this->view->shops	  = $distribuidores->getDistribuidores();   
        	*/       	         
        } catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }
    }
    
    public function loginAction(){
		try{   			
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender();    
	                
	        $answer = Array('answer' => 'no-data');
			$data = $this->_request->getParams();
	        if(isset($data['usuario']) && isset($data['contrasena'])){
	            $usuarios = new My_Model_Usuarios();
				$validate = $usuarios->validateUser($data); //recogemos los valores y mandamos            
				if($validate){
				     $sessions = new My_Controller_Auth();
	                 $sessions->setContentSession($validate);
	                 $sessions->startSession();
				    $answer = Array('answer' => 'logged'); 
				}else{ 
				    $answer = Array('answer' => 'no-perm'); 
				}
	        }else{
	            $answer = Array('answer' => 'problem');	
	        }
	        echo Zend_Json::encode($answer);   
		} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }
    }
    
    public function logoutAction(){
		$mysession= new Zend_Session_Namespace('gtpSession');
		$mysession->unsetAll();
		
		Zend_Session::namespaceUnset('gtpSession');
		Zend_Session::destroy();
		
		$this->_redirect('/');
    }  
}
