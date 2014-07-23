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

	public function lastPosition($idObject){
		$result= Array();
		$this->query("SET NAMES utf8",false); 
    	$sql ="SELECT DT.`LATITUD`,DT.`LONGITUD`,DT.`FECHA`,DT.`UBICACION`,DT.`VELOCIDAD`,DT.`ANGULO`,DT.`MODO`, DT.CREADO, IF(C.DESCRIPCION IS NULL, '--',C.DESCRIPCION) AS INCIDENCIA
				  FROM GTP_DETALLE_VIAJE DT
				  LEFT JOIN GTP_INCIDENCIAS_VIAJE I ON DT.`ID_DETALLE` = I.`ID_HISTORICO`
				  LEFT JOIN GTP_INCIDENCIAS      C ON I.`ID_INCIDENCIA` = C.`ID_INCIDENCIA`
				  WHERE DT.ID_VIAJE = $idObject
				  ORDER BY DT.ID_DETALLE  DESC
				  LIMIT 1";	
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query[0];			
		}	
        
		return $result;	 		
	}
	
	public function getIncidencias($idObject){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT U.USUARIO, I.PRIORIDAD, GI.CREADO, GI.`COMENTARIO`, I.`DESCRIPCION`
				FROM GTP_INCIDENCIAS_VIAJE GI
				INNER JOIN USUARIOS U 		 ON GI.ID_USUARIO = U.ID_USUARIO 
				INNER JOIN GTP_INCIDENCIAS I ON GI.ID_INCIDENCIA = I.ID_INCIDENCIA
				WHERE GI.ID_VIAJE = $idObject	
				ORDER BY GI.CREADO DESC";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;
		}	
        
		return $result;			
	}
	

    public function getData($idObject){
		$result= Array();
		$this->query("SET NAMES utf8",false); 
    	$sql ="SELECT  $this->_name.*,INICIO, FIN
                FROM $this->_name
                WHERE $this->_primary = $idObject LIMIT 1";	
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query[0];			
		}	
        
		return $result;	    	
    }

    public function insertRow($data){
        $result     = Array();
        $result['status']  = false;
        
        $fechaFin 	= (isset($data['inputFechaFin'])) ? $data['inputFechaFin']: '';        
        
        $sql="INSERT INTO GTP_VIAJES SET
			  ID_CLIENTE 		=  ".$data['inputCliente'].",
			  ID_SUCURSAL 		=  ".$data['inputSucursal'].",
			  ID_UNIDAD			=  ".$data['inputUnidades'].",
			  ID_OPERADOR		=  ".$data['inputOperadores'].",
			  ID_ESTATUS		= 1,
			  USUARIO_REGISTRO	=  ".$data['userRegister'].",
			  CLAVE				= '".$data['inputNoTravel']."',
			  DESCRIPCION		= '".$data['inputDescripcion']."',
			  INICIO			= '".$data['inputFechaIn']."',
			  FIN				= '".$fechaFin."',
			  CREADO			= CURRENT_TIMESTAMP";
        try{            
    		$query   = $this->query($sql,false);
    		$sql_id ="SELECT LAST_INSERT_ID() AS ID_LAST;";
			$query_id   = $this->query($sql_id);
			if(count($query_id)>0){
				$result['id']	   = $query_id[0]['ID_LAST'];
				$result['status']  = true;					
			}	
        }catch(Exception $e) {
            echo $e->getMessage();
            echo $e->getErrorMessage();
        }
		return $result;	      	
    }
    
    public function updateRow($data){
        $result     = Array();
        $result['status']  = false;
        
        $fechaFin 	= (isset($data['inputFechaFin'])) ? $data['inputFechaFin']: '';

        $sql="UPDATE  $this->_name SET
					  ID_CLIENTE 		=  ".$data['inputCliente'].",
					  ID_SUCURSAL 		=  ".$data['inputSucursal'].",
					  ID_UNIDAD			=  ".$data['inputUnidades'].",
					  ID_OPERADOR		=  ".$data['inputOperadores'].",
					  USUARIO_REGISTRO	=  ".$data['userRegister'].",
					  CLAVE				= '".$data['inputNoTravel']."',
					  DESCRIPCION		= '".$data['inputDescripcion']."',
					  INICIO			= '".$data['inputFechaIn']."',
					  FIN				= '".$fechaFin."'
					 WHERE $this->_primary =".$data['catId']." LIMIT 1";
        try{            
    		$query   = $this->query($sql,false);
			if($query){
				$result['status']  = true;					
			}	
        }catch(Exception $e) {
            echo $e->getMessage();
            echo $e->getErrorMessage();
        }
		return $result;	      	
    } 
	
    public function changeStatus($Status,$idItem){
        $result     = false;
		$this->query("SET NAMES utf8",false);
		$options = ($Status==1) ? ' INICIO = CURRENT_TIMESTAMP ': ' FIN = CURRENT_TIMESTAMP '; 
        $sql="UPDATE $this->_name SET
                ID_ESTATUS  = $Status,
                $options
               	WHERE $this->_primary = $idItem limit 1";        
        try{
    		$query   = $this->query($sql,false);
    		$result  = true;	
        }catch(Exception $e) {
            echo $e->getMessage();
            echo $e->getErrorMessage();
        }
		return $result;	    	
    }  
    
    public function setIncidencia($data,$idHistorico=-1){
        $result     = Array();
        $result['status']  = false;
        
        $varHist = ($idHistorico>0) ? ' ID_HISTORICO= '.$idHistorico.', ': '';

        $sql="INSERT INTO  GTP_INCIDENCIAS_VIAJE SET
			        ID_USUARIO		= ".$data['userRegister'].",
					ID_INCIDENCIA	= ".$data['inputIncidencia'].",
					ID_VIAJE		= ".$data['catId'].",
					COMENTARIO		= '".$data['inputComentario']."',
					$varHist
					CREADO			= CURRENT_TIMESTAMP";
        try{            
    		$query   = $this->query($sql,false);
			if($query){
				$result['status']  = true;					
			}	
        }catch(Exception $e) {
            echo $e->getMessage();
            echo $e->getErrorMessage();
        }
		return $result;	       	
    }
    
    public function getTipoIncidencias($idObject){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT *
				FROM GTP_INCIDENCIAS 
				WHERE ID_EMPRESA = $idObject	
				ORDER BY DESCRIPCION DESC";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;
		}	
        
		return $result;	    	
    }
    
    public function setManualPosition($data){
        $result     = Array();
        $result['status']  = false;

        $sql="INSERT INTO GTP_DETALLE_VIAJE SET
			        ID_VIAJE		=  ".$data['catId'].",
					ID_USUARIO		=  ".$data['userRegister'].",
					FECHA			= '".$data['inputFecha']."',
					LATITUD			=  ".$data['inputLatitud'].",
					LONGITUD		=  ".$data['inputLongitud'].",
					ANGULO			=  ".$data['inputAngulo'].",
					VELOCIDAD		=  ".$data['inputVelocidad'].",
					UBICACION		= '".$data['inputDir']."',
					OBSERVACION		= '".$data['inputObservaciones']."',
					CREADO			= CURRENT_TIMESTAMP,
					MODO			= 'A'";
        try{            
            $query   = $this->query($sql,false);
    		$sql_id ="SELECT LAST_INSERT_ID() AS ID_LAST;";
			$query_id   = $this->query($sql_id);
			if(count($query_id)>0){
				$result['id']	   = $query_id[0]['ID_LAST'];
				$result['status']  = true;
			}
        }catch(Exception $e) {
            echo $e->getMessage();
            echo $e->getErrorMessage();
        }
		return $result;	    	
    }
    
    public function getRecorrido($idObject){
		$result= Array();
		$this->query("SET NAMES utf8",false); 
    	$sql ="SELECT DT.`LATITUD`,DT.`LONGITUD`,DT.`FECHA`,DT.`UBICACION`,DT.`VELOCIDAD`,DT.`ANGULO`,DT.`MODO`, DT.CREADO, IF(C.DESCRIPCION IS NULL, '--',C.DESCRIPCION) AS INCIDENCIA
				  FROM GTP_DETALLE_VIAJE DT
				  LEFT JOIN GTP_INCIDENCIAS_VIAJE I ON DT.`ID_DETALLE` = I.`ID_HISTORICO`
				  LEFT JOIN GTP_INCIDENCIAS      C ON I.`ID_INCIDENCIA` = C.`ID_INCIDENCIA`
				  WHERE DT.ID_VIAJE = $idObject
				  ORDER BY DT.ID_DETALLE  DESC";	
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;	     	
    }
}	