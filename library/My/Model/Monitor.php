<?php
/**
 * Archivo de definici—n de Unidades
 * 
 * @author epena
 * @package library.My.Models
 */
class My_Model_Monitor extends My_Db_Table
{
    protected $_schema 	= 'gtp_bd';
	protected $_name 	= 'GTP_VIAJES';
	protected $_primary = 'ID_VIAJE';

	public function getNoAssing(){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT V.ID_VIAJE, V.CLAVE, V.INICIO, V.FIN, V.RETRASO, S.DESCRIPCION AS SUCURSAL, U.ECONOMICO, ST.ICONO,E.`NOMBRE`  AS CLIENTE,
				ST.ID_ESTATUS, ST.DESCRIPCION AS STATUS_VIAJE, R.DESCRIPCION AS DES_RUTA
				FROM GTP_VIAJES V
				INNER JOIN SUCURSALES S ON V.ID_SUCURSAL = S.ID_SUCURSAL
				INNER JOIN EMPRESAS E ON S.ID_EMPRESA = E.ID_EMPRESA
				LEFT JOIN GTP_ESTATUS_VIAJE ST ON V.ID_ESTATUS = ST.ID_ESTATUS
				LEFT JOIN GTP_UNIDADES U ON V.ID_UNIDAD = U.ID_UNIDAD
				INNER JOIN RUTAS       R ON V.ID_RUTA      = R.ID_RUTA 
				WHERE V.ID_USUARIO_ASIGNADO IS NULL
				ORDER BY V.ID_VIAJE ASC";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;
		}	
        
		return $result;			
	}  

	public function getAssingUser($idUser,$iStatus=1,$dateEnd=0){
		$result= Array();
		$this->query("SET NAMES utf8",false);
		$sFilter = ($dateEnd != 0 ) ? 'AND CAST(V.FIN AS DATE )  = CURRENT_DATE': ''; 		
    	$sql ="SELECT V.ID_VIAJE, V.CLAVE, V.INICIO, V.FIN, V.RETRASO, S.DESCRIPCION AS SUCURSAL, U.ECONOMICO, ST.ICONO,E.`NOMBRE`  AS CLIENTE,
				ST.ID_ESTATUS, ST.DESCRIPCION AS STATUS_VIAJE, R.DESCRIPCION AS DES_RUTA
				FROM GTP_VIAJES V
				INNER JOIN SUCURSALES S ON V.ID_SUCURSAL = S.ID_SUCURSAL
				INNER JOIN EMPRESAS E ON S.ID_EMPRESA = E.ID_EMPRESA
				LEFT JOIN GTP_ESTATUS_VIAJE ST ON V.ID_ESTATUS = ST.ID_ESTATUS
				LEFT JOIN GTP_UNIDADES U ON V.ID_UNIDAD = U.ID_UNIDAD
				INNER JOIN RUTAS       R ON V.ID_RUTA      = R.ID_RUTA 
				WHERE V.ID_USUARIO_ASIGNADO = $idUser
				  AND V.ID_ESTATUS          = $iStatus
				  $sFilter
				ORDER BY V.ID_VIAJE ASC";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;
		}	
        
		return $result;			
	}  	

    public function assignTravel($idTravel,$idUser){
        $result     = Array();
        $result['status']  = false;
        
        $sql="UPDATE GTP_VIAJES 
				SET ID_USUARIO_ASIGNADO = $idUser
				WHERE ID_VIAJE 		    = $idTravel
				LIMIT 1";
        try{            
    		$query   = $this->query($sql,false);
			if($query){
				$result['status']  = true;					
			}	
        }catch(Exception $e) {
            echo $e->getMessage();
            echo $e->getErrorMessage();
        }
		return $result['status'];	      		
    }	
}