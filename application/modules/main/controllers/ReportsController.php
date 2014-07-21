<?php

class main_ReportsController extends My_Controller_Action
{	
    public function init()
    {
		$this->view->layout()->setLayout('layout_blank');
		
		$sessions = new My_Controller_Auth();
        if($sessions->validateSession()){
	        $this->view->dataUser   = $sessions->getContentSession();   		
		}
    }
    
    public function indexAction(){
    	
    }
}