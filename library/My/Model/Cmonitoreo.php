<?php
/**
 * Modelo de tabla: usuarios
 *
 * @package library.My.Models
 * @author EPENA
 */
class My_Model_Cmonitoreo extends My_Db_Table
{
    protected $_schema 	= 'gtp';
	protected $_name 	= 'CENTRO_MONITOREO';
	protected $_primary = 'ID_MONITOREO';
	
	public function getDataTable(){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT * 
				FROM $this->_name
				GROUP BY NOMBRE ASC";
		$query   = $this->query($sql);
		if(count($query)>0){
			$result = $query;
		}
        
		return $result;			
	}  
	
    public function getData($idObject){
		$result= Array();
		$this->query("SET NAMES utf8",false); 
    	$sql ="SELECT  *
                FROM $this->_name
                WHERE $this->_primary = $idObject LIMIT 1";	
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query[0];			
		}	
        
		return $result;	    	
    }

  	public function validateExist($sName){
		$result= Array();
    	$sql ="SELECT  *
                FROM ".$this->_name." 
                WHERE UPPER(NOMBRE) = UPPER('".$sName."') LIMIT 1";		         	
		$query   = $this->query($sql);
		if(count($query)>0){
			$result	 = $query[0];			
		}	
        
		return $result;			
	}
	
    public function insertRow($data){
        $result     = Array();
        $result['status']  = false;

        
        $sql="INSERT INTO $this->_name	
        		SET	NOMBRE 			= '".$data['inputDescripcion']."',        			
        		 	RAZON_SOCIAL	= '".$data['inputRazonSocial']."',
					ESTATUS			=  ".$data['inputEstatus'].",
					EMAIL_CONTACTO  = '".$data['inputEmail']."',
					TELEFONO		= '".$data['inputTelFijo']."',
					DIRECCION		= '".$data['inputDireccion']."',					
        			CREADO     	    = CURRENT_TIMESTAMP";       			  
        try{            
    		$query   = $this->query($sql,false);
    		$sql_id ="SELECT LAST_INSERT_ID() AS ID_LAST;";
			$query_id   = $this->query($sql_id);
			if(count($query_id)>0){
				$result['id']  = $query_id[0]['ID_LAST'];  			 	
				$result['status']  = true;	
			}	
        }catch(Exception $e) {
            echo $e->getMessage();
            echo $e->getErrorMessage();
        }
		return $result;	
    } 	
}