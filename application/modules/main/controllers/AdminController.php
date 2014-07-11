<?php

class main_AdminController extends My_Controller_Action
{
	public $menu	= 'catalogos';
	
    public function init()
    {
		$sessions = new My_Controller_Auth();
        if(!$sessions->validateSession()){
            $this->_redirect('/');		
		}				
		$this->view->menu	 = $this->menu;	
    }
    
    public function indexAction(){
    	$this->view->mOption = 'units';
    }
    
    public function getinfounitAction(){
    	
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