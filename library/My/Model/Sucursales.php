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
	
	public function getFilterSucursales($description,$idEmpresa){
		$result= Array();
		$this->query("SET NAMES utf8",false); 
    	$sql ="SELECT ID_CLIENTE AS ID
				FROM GTP_CLIENTES
				WHERE ID_SUCURSAL 
				IN (
					SELECT ID_SUCURSAL
					FROM SUCURSALES 
					WHERE DESCRIPCION LIKE '%".$description."%' AND ID_EMPRESA = ".$idEmpresa."
				)";    
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}
        
		return $result;   		
	}	
	
    public function insertRowRegister($data){
        $result     = Array();
        $result['status']  = false;    


        $sql="INSERT INTO $this->_name	
        		SET	ID_EMPRESA 		=  ".$data['inputIdEmpresa'].",
        			DESCRIPCION		= '".$data['inputDescripcion']."',
        			FECHA_REGISTRO	= CURRENT_TIMESTAMP";       			  
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