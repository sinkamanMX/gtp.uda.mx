<?php
/**
 * Archivo de definici—n de Unidades
 * 
 * @author epena
 * @package library.My.Models
 */
class My_Model_Viajes extends My_Db_Table
{
    protected $_schema 	= 'gtp_bd';
	protected $_name 	= 'GTP_VIAJES';
	protected $_primary = 'ID_VIAJE';
	
	public function getRowsEmp($idObject){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT V.ID_VIAJE, V.CLAVE, V.INICIO, V.FIN, V.RETRASO, S.DESCRIPCION AS SUCURSAL, U.ECONOMICO, ST.ICONO,C.NOMBRE AS CLIENTE,
				ST.ID_ESTATUS
				FROM GTP_VIAJES V
				INNER JOIN SUCURSALES S ON V.ID_SUCURSAL = S.ID_SUCURSAL
				INNER JOIN GTP_ESTATUS_VIAJE ST ON V.ID_ESTATUS = ST.ID_ESTATUS
				INNER JOIN GTP_UNIDADES U ON V.ID_UNIDAD = U.ID_UNIDAD
				INNER JOIN GTP_CLIENTES C ON V.`ID_CLIENTE` = C.ID_CLIENTE 
				WHERE S.ID_EMPRESA = $idObject	
				  AND V.ID_ESTATUS IN (1,2,3)
				ORDER BY V.ID_VIAJE DESC";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;
		}	
        
		return $result;			
	}  

	public function lastPosition($idViaje){
		$result= Array();
		$this->query("SET NAMES utf8",false); 
    	$sql ="SELECT DT.`LATITUD`,DT.`LONGITUD`,DT.`FECHA`,DT.`UBICACION`,DT.`VELOCIDAD`,DT.`ANGULO`,DT.`MODO`
				  FROM GTP_DETALLE_VIAJE DT
				  WHERE DT.ID_VIAJE = ".$idViaje."
				  ORDER BY DT.ID_DETALLE
				  LIMIT 1";	
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query[0];			
		}	
        
		return $result;	 		
	}
}	