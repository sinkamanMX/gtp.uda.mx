<?php
/**
 * Archivo de definici—n 
 * 
 * @author epena
 * @package library.My.Models
 */
class My_Model_Operadores extends My_Db_Table
{
    protected $_schema 	= 'gtp_bd';
	protected $_name 	= 'GTP_OPERADORES';
	protected $_primary = 'ID_OPERADOR';
    
	public function getCbo($idObject,$idEmpresa){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT ID_OPERADOR AS ID, CONCAT(NOMBRE,'-',APELLIDOS) AS NAME
				FROM $this->_name
				WHERE ID_TRANSPORTISTA = $idObject  
				ORDER BY NOMBRE ASC";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;			
	}  	
}