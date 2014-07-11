<?php
/**
 * Archivo de definición de clase 
 * 
 * @package library.My.Controller
 * @author epena
 */

/**
 * Definición de clase de controlador genérico
 *
 * @package library.My.Controller
 * @author epena
 */
class My_Controller_Auth
{
	/**
	 * Nombre de la session
	 * 
	 * @var mixed
	 */
	public $nameSession;
	
	/**
	 * Flag que tiene el estatus del logeo
	 * 
	 * @var mixed
	 */
	public $logged=false;
	
	/**
	 * Contenido de la Session
	 *
	 * @var $contexts: arreglo de contextos
	 */
	public $contentSession = array( );
	
   /**
    * Inicializa el contexto, el formato de respuesta a un action
    *
    * @return void
    */
    public function __construct(){		
    	$this->nameSession = "gtpSession";
    }
    
   /**
    * Asigna el nomnbre de la session
    *
    * @return void
    * @param string $name Nombre de la session
    */
    public function setNameSession($name="gtpSession") {		
    	$this->nameSession = $name;
    }    
    
   /**
    * Asigna los datos del usuario para la session
    *
    * @return void
    * @param Array $dataUser Informacion del usuario
    */
    public function setContentSession($dataUser=Array()) {		
    	$this->contentSession = $dataUser;
    }    
    
   /**
    * Asigna los datos del usuario para la session
    *
    * @return Array
    * @param Array $dataUser Informacion del usuario
    */
    public function getContentSession() {
    	Zend_Session::start();
    	$aNamespace = new Zend_Session_Namespace($this->nameSession);        
        $content = Array(
            'IdUsuario'         => $aNamespace->IdUser,
            'nombreUsuario'     => $aNamespace->NameUser,
            'passwordUsuario'   => $aNamespace->PassUser,
        );
        return $content;
    }       
    
    /**
     * Método que valida si la session existe
     *
     * @return boolean
     */
    public function validateSession()
    {
    	Zend_Session::start();
    	$aNamespace = new Zend_Session_Namespace($this->nameSession);
    	if(isset($aNamespace->IdUser)){    	
            $this->getContentSession($aNamespace);
			$this->logged = true;			
    	}else{
    		$this->logged = false;
    	}
		return $this->logged;
    }

    /**
     * Crea la Session del usuario
     *
     * void
     */
    public function startSession(){
    	Zend_Session::start();
		if(!$this->validateSession()){
			$aNamespace = new Zend_Session_Namespace($this->nameSession);			
			//die();    
			$aNamespace->IdUser   = $this->contentSession['clt_id'];
            $aNamespace->NameUser = $this->contentSession['name'];
            $aNamespace->PassUser = $this->contentSession['pass'];
			$this->logged = true;
		}
    }

    /**
     * Crea la Session del usuario
     *
     * void
     */
    public function endSession(){
    	Zend_Session::start();
        $aNamespace = new Zend_Session_Namespace($this->nameSession);
		if($this->validateSession()){
			$aNamespace->unsetAll();
            $this->logged = false;
		}else{
    		$this->logged = true;
    	}
		return $this->logged;
    }
}
