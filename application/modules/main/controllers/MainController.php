<?php

class main_MainController extends My_Controller_Action
{	
    public function init()
    {
		$this->view->layout()->setLayout('layout_blank');
    }

    public function indexAction()
    {
		try{
			$sessions = new My_Controller_Auth();
	        if($sessions->validateSession()){
	            $this->_redirect('/main/main/inicio');		
			}
			
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
				$validate = $usuarios->validateUser($data);            
				if($validate){
					 $dataUser = $usuarios->getDataUser($validate['ID_USUARIO']);
				     $sessions = new My_Controller_Auth();
	                 $sessions->setContentSession($dataUser);
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
    
    public function inicioAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();    
		
		$sessions = new My_Controller_Auth();
        if($sessions->validateSession()){
            $profile = new My_Model_Perfiles();
            $dataUser = $sessions->getContentSession();
            $default = $profile->getModuleDefault($dataUser['ID_PERFIL']);	
            if(count($default)>0){
            	$this->_redirect($default['SCRIPT']);
            }else{
            	$this->_redirect('/main/dashboard/index');	
            }
		}		   	
    }
}
