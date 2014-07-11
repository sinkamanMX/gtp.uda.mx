<?php

class main_DashboardController extends My_Controller_Action
{
	public $menu	= 'index';
	
    public function init()
    {
		$sessions = new My_Controller_Auth();
        if(!$sessions->validateSession()){
            $this->_redirect('/');		
		}				
		$this->view->menu	 = $this->menu;	
    }
    
    public function indexAction(){
    	$this->view->mOption = 'mapa';
    }
    
    public function reportsAction(){
    	$this->view->mOption = 'reports';
    	
    }
    
    public function productivityAction(){
    	$this->view->mOption = 'productividad';
    	
    }
}