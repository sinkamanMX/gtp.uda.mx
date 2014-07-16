<?php
/**
 * Archivo de definici—n de perfiles
 * 
 * @author epena
 * @package library.My.Models
 */
class My_Model_Clientes extends My_Db_Table
{
    protected $_schema 	= 'gtp_bd';
	protected $_name 	= 'GTP_CLIENTES';
	protected $_primary = 'ID_CLIENTE';
	
	public function getRowsEmp($idObject){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT * 
				FROM $this->_name
				WHERE ID_EMPRESA = $idObject
				ORDER BY NOMBRE ASC";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;			
	}

	public function getCbo($idObject,$idEmpresa){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT ID_CLIENTE AS ID, NOMBRE AS NAME
				FROM $this->_name
				WHERE ID_SUCURSAL = $idObject
				  AND ID_EMPRESA  = $idEmpresa  
				ORDER BY NOMBRE ASC";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;			
	}
}