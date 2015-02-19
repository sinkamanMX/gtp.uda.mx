<?php
/**
 * Archivo de definici—n de Unidades
 * 
 * @author epena
 * @package library.My.Models
 */
class My_Model_Extras extends My_Db_Table
{
    protected $_schema 	= 'gtp_bd';
	protected $_name 	= 'GTP_VIAJE_EXTRAS';
	protected $_primary = 'ID_VIAJE_EXTRAS';

    public function insertTravel($data){
        $result     = false;
                
        $sql="INSERT INTO $this->_name 
        		SET ID_VIAJE	= ".$data['inputIdViaje'].",
        		ID_INCIDENCIA	= ".$data['inputIncidencia'].",
        		TOTAL			= ".$data['inputTotal'].",
        		CANTIDAD		= ".$data['inputCantidad'].",
        		FECHA_CREADO 	= CURRENT_TIMESTAMP";
        try{            
    		$query   = $this->query($sql,false);
			if($query){
				$result	  = true;
			}	
        }catch(Exception $e) {
            echo $e->getMessage();
            echo $e->getErrorMessage();
        }
		return $result;	      	
    } 

    public function getIncidenciasByTravel($idObject){
		$result= Array();
		$this->query("SET NAMES utf8",false); 
    	$sql ="SELECT  *
				FROM GTP_VIAJE_EXTRAS E
				INNER JOIN GTP_INCIDENCIAS I ON I.ID_INCIDENCIA = E.ID_INCIDENCIA
				WHERE E.ID_VIAJE = $idObject";	
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;
		}	
        
		return $result;	    	
    }    
}	