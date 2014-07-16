<?php
/**
 * Archivo de definici—n de perfiles
 * 
 * @author epena
 * @package library.My.Models
 */
class My_Model_Sucursales extends My_Db_Table
{
    protected $_schema 	= 'gtp_bd';
	protected $_name 	= 'SUCURSALES';
	protected $_primary = 'ID_SUCURSAL';
	
	public function getRowsEmp($idObject){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT * 
				FROM $this->_name
				WHERE ID_EMPRESA = $idObject
				GROUP BY $this->_primary";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;			
	}   	
}