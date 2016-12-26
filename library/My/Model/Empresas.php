<?php
/**
 * Modelo de tabla: usuarios
 *
 * @package library.My.Models
 * @author EPENA
 */
class My_Model_Empresas extends My_Db_Table
{
    protected $_schema 	= 'taccsi';
	protected $_name 	= 'EMPRESAS';
	protected $_primary = 'ID_EMPRESA';
	
    public function insertRow($data){
        $result     = Array();
        $result['status']  = false;

        $idEstado    = (isset($data['inputEstadoF'])     && $data['inputEstadoF']    > 0) ? $data['inputEstadoF']      : 0;
        $idMunicipio = (isset($data['inputMunicipioF'])  && $data['inputMunicipioF'] > 0) ? $data['inputMunicipioF']   : 0;        
        $sUserUda 	 = (isset($data['inputUserUda'])     && $data['inputUserUda']  !="")     ? $data['inputUserUda']    : '';
        $sPassUda 	 = (isset($data['inputPasswordUda']) && $data['inputPasswordUda'] !="") ? $data['inputPasswordUda']: '';
        $idCentrom   = (isset($data['idcentro']) && $data['idcentro'] !="") ? $data['idcentro']: 1;
        $sRfcUda	 = (isset($data['inputRFC']) && $data['inputRFC'] !="") ? $data['inputRFC']: '';
        $iClienteUda = (isset($data['inputClienteUDA'])  && $data['inputClienteUDA'] !="") ? $data['inputClienteUDA']: 0;
        //$idMonitoreo = (isset($data['idMonitoreo'])      && $data['inputClienteUDA'] !="") ? $data['idMonitoreo'] : 1;
        
        $sql="INSERT INTO $this->_name	
        		SET	NOMBRE 			= '".$data['inputDescripcion']."',
        			RFC				= '".$sRfcUda."',
        		 	RAZON_SOCIAL	= '".$data['inputRazonSocial']."',
					ESTATUS			=  ".$data['inputEstatus'].",
					CLIENTE_UDA		=  ".$iClienteUda.",
					USUARIO_UDA  	= '".$sUserUda."' ,
					PASSWORD_UDA	= '".$sPassUda."' ,
					ID_MONITOREO	=  ".$idCentrom." ,
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
    
  	public function validateExist($sRfc,$idMonitoreo=1){
		$result= Array();
    	$sql ="SELECT  *
                FROM ".$this->_name." 
                WHERE RFC = '".$sRfc."'
                  AND ID_MONITOREO = ".$idMonitoreo." LIMIT 1";		         	
		$query   = $this->query($sql);
		if(count($query)>0){
			$result	 = $query[0];			
		}	
        
		return $result;			
	}    

    public function setActivate($idEmpresa){
        $result  = false;

        $sql="UPDATE $this->_name
				SET ESTATUS         = 1					 
			  WHERE $this->_primary = ".$idEmpresa." LIMIT 1";
        try{            
    		$query   = $this->query($sql,false);
			if($query){
				$result = true;					
			}	
        }catch(Exception $e) {
            echo $e->getMessage();
            echo $e->getErrorMessage();
        }
		return $result;	      	
    }	

	public function getDataTables($idCdm=-1,$idCompany=-1){
		$result= Array();		
		$this->query("SET NAMES utf8",false);
		
		$sCdm	 = ($idCdm==-1) ? '' : 'WHERE ID_MONITOREO = '.$idCdm.' ';
		$sFilter = ($idCompany!=-1) ? ' AND ': '';  
		$sFilter.= ($idCompany!=-1) ? 'ID_EMPRESA NOT IN ('.$idCompany.')': ''; 
		 		
    	$sql ="SELECT *
				FROM $this->_name
				$sCdm
				$sFilter
				ORDER BY RAZON_SOCIAL ASC";
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
    
    
    public function updateRow($data,$idObject){
        $result     = Array();
        $result['status']  = false;
                
        $sUserUda 	 = (isset($data['inputUserUda'])    && $data['inputUserUda']  !="")     ? $data['inputUserUda']    : '';
        $sPassUda 	 = (isset($data['inputPasswordUda']) && $data['inputPasswordUda'] !="") ? $data['inputPasswordUda']: '';
        
        $sql="UPDATE  $this->_name
        		SET	NOMBRE 			= '".$data['inputDescripcion']."',
        			RFC				= '".$data['inputRFC']."',
        		 	RAZON_SOCIAL	= '".$data['inputRazonSocial']."',
					ESTATUS			=  ".$data['inputEstatus'].",
					CLIENTE_UDA		=  ".$data['inputClienteUDA'].",
					USUARIO_UDA  	= '".$sUserUda."',
					PASSWORD_UDA	= '".$sPassUda."',
					COBRAR_VIAJES	=  ".$data['inputCobro']."
				WHERE $this->_primary   = ".$idObject;
        try{            
    		$query   = $this->query($sql,false);
			if($query){
				$result['status']  = true;					
			}	
        }catch(Exception $e) {
            echo $e->getMessage();
            echo $e->getErrorMessage();
            echo $sql;
        }
		return $result;	      	
    } 

	public function getCbo($idMonitoreo=1){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT ID_EMPRESA AS ID, NOMBRE AS NAME
				FROM $this->_name
				WHERE ID_MONITOREO = ".$idMonitoreo."
				ORDER BY NAME ASC";
		$query   = $this->query($sql);
		if(count($query)>0){
			$result = $query;			
		}
		return $result;
	}       
}