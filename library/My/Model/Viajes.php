<?php
/**
 * Archivo de definición de Unidades
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
				LEFT JOIN SUCURSALES S ON V.ID_SUCURSAL = S.ID_SUCURSAL
				LEFT JOIN GTP_ESTATUS_VIAJE ST ON V.ID_ESTATUS = ST.ID_ESTATUS
				LEFT JOIN GTP_UNIDADES U ON V.ID_UNIDAD = U.ID_UNIDAD
				LEFT JOIN GTP_CLIENTES C ON V.`ID_CLIENTE` = C.ID_CLIENTE 
				WHERE /*S.ID_EMPRESA = $idObject
				  AND */ V.ID_ESTATUS IN (1,2,3)
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
        $sNoTravel 	= (isset($data['inputNoTravel']))    ? "CLAVE				= '".$data['inputNoTravel']."',": "";
        $sDesc 		= (isset($data['inputDescripcion'])) ? " DESCRIPCION		= '".$data['inputDescripcion']."',": "";

        $sql="UPDATE  $this->_name SET
					  ID_CLIENTE 		=  ".$data['inputCliente'].",
					  ID_SUCURSAL 		=  ".$data['inputSucursal'].",
					  ID_UNIDAD			=  ".$data['inputUnidades'].",
					  ID_OPERADOR		=  ".$data['inputOperadores'].",
					  USUARIO_REGISTRO	=  ".$data['userRegister'].",
					  $sNoTravel
					  $sDesc
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
    
    public function getDataExport($idObject){
		$result= Array();
		$this->query("SET NAMES utf8",false); 
    	$sql ="SELECT V.ID_VIAJE, V.CLAVE, V.INICIO, V.FIN, S.DESCRIPCION AS SUCURSAL, U.ECONOMICO,C.NOMBRE AS CLIENTE,  
    			O.NOMBRE, T.DESCRIPCION AS TRANSPORTISTA
				, (SELECT COUNT(ID_VIAJE) FROM GTP_INCIDENCIAS_VIAJE WHERE ID_VIAJE = V.ID_VIAJE) AS INCIDENCIAS, V.DESCRIPCION AS NDESC, E.DESCRIPCION AS DESC_E
				FROM GTP_VIAJES V
				INNER JOIN SUCURSALES S ON V.ID_SUCURSAL = S.ID_SUCURSAL
				INNER JOIN GTP_ESTATUS_VIAJE ST ON V.ID_ESTATUS = ST.ID_ESTATUS
				INNER JOIN GTP_UNIDADES U ON V.ID_UNIDAD = U.ID_UNIDAD
				INNER JOIN GTP_CLIENTES C ON V.`ID_CLIENTE` = C.ID_CLIENTE 
				INNER JOIN GTP_OPERADORES O ON V.`ID_OPERADOR` = O.ID_OPERADOR
				INNER JOIN GTP_TRANSPORTISTA T ON O.ID_TRANSPORTISTA = T.ID_TRANSPORTISTA
				INNER JOIN GTP_ESTATUS_VIAJE E ON V.ID_ESTATUS = E.ID_ESTATUS				
				WHERE V.ID_VIAJE = $idObject LIMIT 1";
		$query   = $this->query($sql);
		if(count($query)>0){
			$result = $query[0];			
		}	
        
		return $result;	    	
    }    
        
    public function getDataReport($idObject){
		$result= Array();
		$this->query("SET NAMES utf8",false); 
    	$sql ="SELECT V.ID_VIAJE, V.CLAVE, V.INICIO, V.FIN, S.DESCRIPCION AS SUCURSAL, U.ECONOMICO,C.NOMBRE AS CLIENTE,  
    			O.NOMBRE, T.DESCRIPCION AS TRANSPORTISTA
				, (SELECT COUNT(ID_VIAJE) FROM GTP_INCIDENCIAS_VIAJE WHERE ID_VIAJE = V.ID_VIAJE) AS INCIDENCIAS
				FROM GTP_VIAJES V
				INNER JOIN SUCURSALES S ON V.ID_SUCURSAL = S.ID_SUCURSAL
				INNER JOIN GTP_ESTATUS_VIAJE ST ON V.ID_ESTATUS = ST.ID_ESTATUS
				INNER JOIN GTP_UNIDADES U ON V.ID_UNIDAD = U.ID_UNIDAD
				INNER JOIN GTP_CLIENTES C ON V.`ID_CLIENTE` = C.ID_CLIENTE 
				INNER JOIN GTP_OPERADORES O ON V.`ID_OPERADOR` = O.ID_OPERADOR
				INNER JOIN GTP_TRANSPORTISTA T ON O.ID_TRANSPORTISTA = T.ID_TRANSPORTISTA
				WHERE V.CLAVE = '$idObject' LIMIT 1";
		$query   = $this->query($sql);
		if(count($query)>0){
			$result = $query;			
		}	
        
		return $result;	    	
    }
    
    public function getDataFromSucursal($idObject){
		$result= Array();
		$this->query("SET NAMES utf8",false); 
    	$sql ="SELECT V.ID_VIAJE, V.CLAVE, V.INICIO, V.FIN, S.DESCRIPCION AS SUCURSAL, U.ECONOMICO,C.NOMBRE AS CLIENTE,  
    			O.NOMBRE, T.DESCRIPCION AS TRANSPORTISTA
				, (SELECT COUNT(ID_VIAJE) FROM GTP_INCIDENCIAS_VIAJE WHERE ID_VIAJE = V.ID_VIAJE) AS INCIDENCIAS
				FROM GTP_VIAJES V
				INNER JOIN SUCURSALES S ON V.ID_SUCURSAL = S.ID_SUCURSAL
				INNER JOIN GTP_ESTATUS_VIAJE ST ON V.ID_ESTATUS = ST.ID_ESTATUS
				INNER JOIN GTP_UNIDADES U ON V.ID_UNIDAD = U.ID_UNIDAD
				INNER JOIN GTP_CLIENTES C ON V.`ID_CLIENTE` = C.ID_CLIENTE 
				INNER JOIN GTP_OPERADORES O ON V.`ID_OPERADOR` = O.ID_OPERADOR
				INNER JOIN GTP_TRANSPORTISTA T ON O.ID_TRANSPORTISTA = T.ID_TRANSPORTISTA
				WHERE V.ID_OPERADOR IN ($idObject)";	
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;    	
    }
    
    public function getDataFromCliente($idObject){
		$result= Array();
		$this->query("SET NAMES utf8",false); 
    	$sql ="SELECT V.ID_VIAJE, V.CLAVE, V.INICIO, V.FIN, S.DESCRIPCION AS SUCURSAL, U.ECONOMICO,C.NOMBRE AS CLIENTE,  
    			O.NOMBRE, T.DESCRIPCION AS TRANSPORTISTA
				, (SELECT COUNT(ID_VIAJE) FROM GTP_INCIDENCIAS_VIAJE WHERE ID_VIAJE = V.ID_VIAJE) AS INCIDENCIAS
				FROM GTP_VIAJES V
				INNER JOIN SUCURSALES S ON V.ID_SUCURSAL = S.ID_SUCURSAL
				INNER JOIN GTP_ESTATUS_VIAJE ST ON V.ID_ESTATUS = ST.ID_ESTATUS
				INNER JOIN GTP_UNIDADES U ON V.ID_UNIDAD = U.ID_UNIDAD
				INNER JOIN GTP_CLIENTES C ON V.`ID_CLIENTE` = C.ID_CLIENTE 
				INNER JOIN GTP_OPERADORES O ON V.`ID_OPERADOR` = O.ID_OPERADOR
				INNER JOIN GTP_TRANSPORTISTA T ON O.ID_TRANSPORTISTA = T.ID_TRANSPORTISTA
				WHERE V.ID_CLIENTE IN ($idObject)";	
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;    	
    }    

    public function getReportViajes($dateInicio,$dateFin){
		$result= Array();
		$this->query("SET NAMES utf8",false); 
    	$sql ="SELECT V.ID_VIAJE, V.CLAVE, V.INICIO, V.FIN, S.DESCRIPCION AS SUCURSAL, U.ECONOMICO,C.NOMBRE AS CLIENTE,  
    			O.NOMBRE, T.DESCRIPCION AS TRANSPORTISTA
				, (SELECT COUNT(ID_VIAJE) FROM GTP_INCIDENCIAS_VIAJE WHERE ID_VIAJE = V.ID_VIAJE) AS INCIDENCIAS
				FROM GTP_VIAJES V
				INNER JOIN SUCURSALES S ON V.ID_SUCURSAL = S.ID_SUCURSAL
				INNER JOIN GTP_ESTATUS_VIAJE ST ON V.ID_ESTATUS = ST.ID_ESTATUS
				INNER JOIN GTP_UNIDADES U ON V.ID_UNIDAD = U.ID_UNIDAD
				INNER JOIN GTP_CLIENTES C ON V.`ID_CLIENTE` = C.ID_CLIENTE 
				INNER JOIN GTP_OPERADORES O ON V.`ID_OPERADOR` = O.ID_OPERADOR
				INNER JOIN GTP_TRANSPORTISTA T ON O.ID_TRANSPORTISTA = T.ID_TRANSPORTISTA
				WHERE V.INICIO BETWEEN '".$dateInicio."' AND '".$dateFin."'
				  OR  V.FIN    BETWEEN '".$dateInicio."' AND '".$dateFin."'
				  GROUP BY V.ID_VIAJE";	
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;	     	
    } 

    public function getViajesByDate($idEmpresa,$idStatus=4,$date=-1){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
		
		$sFilter = ($date==-1) ? ' AND (CURRENT_DATE BETWEEN CAST(V.INICIO AS DATE) AND CAST(V.FIN AS DATE)) ': ''; 
		
    	$sql ="SELECT V.ID_VIAJE, V.CLAVE, V.INICIO, V.FIN, V.RETRASO, S.DESCRIPCION AS SUCURSAL, U.ECONOMICO, ST.ICONO,C.NOMBRE AS CLIENTE,				
				ST.ID_ESTATUS, P.DESCRIPCION AS E_PAGO, E.CLIENTE_UDA, E.USUARIO_UDA, E.PASSWORD_UDA,V.ID_UNIDAD, T.DESCRIPCION AS N_TRANS
				, CONCAT(O.`NOMBRE`,' ',O.`APELLIDOS`) AS N_OPERADOR, A.DESCRIPCION AS TIPO, R.DESCRIPCION AS DES_RUTA, ST.DESCRIPCION AS STATUS_VIAJE
				FROM GTP_VIAJES V
				LEFT JOIN TIPO_VIAJES A ON V.ID_TIPO_VIAJE = A.ID_TIPO_VIAJE
				LEFT JOIN SUCURSALES S ON V.ID_SUCURSAL = S.ID_SUCURSAL
				LEFT JOIN EMPRESAS   E ON S.ID_EMPRESA  = E.ID_EMPRESA
				LEFT JOIN GTP_ESTATUS_VIAJE ST ON V.ID_ESTATUS = ST.ID_ESTATUS
				LEFT JOIN GTP_UNIDADES U ON V.ID_UNIDAD = U.ID_UNIDAD
				LEFT JOIN GTP_CLIENTES C ON V.`ID_CLIENTE` = C.ID_CLIENTE 
				LEFT JOIN GTP_ESTATUS_PAGO P ON V.ID_ESTATUS_PAGO = P.ID_ESTATUS_PAGO	
				LEFT JOIN GTP_OPERADORES    O ON V.ID_OPERADOR   = O.ID_OPERADOR			
				LEFT JOIN GTP_TRANSPORTISTA T ON U.`ID_TRANSPORTISTA` = T.ID_TRANSPORTISTA
				LEFT JOIN RUTAS R ON V.ID_RUTA = R.ID_RUTA	
				WHERE S.ID_EMPRESA = $idEmpresa
					$sFilter
				  AND V.ID_ESTATUS IN ($idStatus)
				ORDER BY V.ID_VIAJE DESC";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;
		}	
        
		return $result;
    }
    
    public function getIncidenciasTravels($idEmpresa){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT V.ID_VIAJE, V.CLAVE, I.DESCRIPCION AS DESC_INCIDENCIA,R.COMENTARIO, I.PRIORIDAD
				FROM GTP_INCIDENCIAS_VIAJE	R	
				INNER JOIN GTP_VIAJES V ON R.ID_VIAJE = V.ID_VIAJE
				INNER JOIN GTP_INCIDENCIAS I ON R.ID_INCIDENCIA = I.ID_INCIDENCIA
				WHERE R.ID_VIAJE IN (
					SELECT V.ID_VIAJE
					FROM GTP_VIAJES V
					INNER JOIN SUCURSALES S ON V.ID_SUCURSAL = S.ID_SUCURSAL
					WHERE S.ID_EMPRESA = $idEmpresa
								  AND (CURRENT_DATE BETWEEN CAST(V.INICIO AS DATE) AND CAST(V.FIN AS DATE))
								  /*AND V.ID_ESTATUS NOT IN (4)*/
								  AND V.ID_ESTATUS IN (4))";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;
		}	
        
		return $result;	    	
    }
    
    public function insertTravel($data,$statusPago=1,$status=0){
        $result     = Array();
        $result['status']  = false;
        
        $fechaFin 	= (isset($data['inputFechaFin'])) ? $data['inputFechaFin']: '';        
        
        $sql="INSERT INTO GTP_VIAJES SET
				ID_ESTATUS_PAGO =  ".$statusPago.",
        		ID_RUTA			=  ".$data['inputRuta'].",
        		ID_TIPO_VIAJE	=  ".$data['inputTviaje'].",			        		
				ID_CLIENTE 		=  ".$data['inputCliente'].",
				ID_SUCURSAL 	=  ".$data['inputSucursal'].",
				ID_UNIDAD		=  ".$data['inputUnidades'].",
				ID_OPERADOR		=  ".$data['inputOperadores'].",
				ID_ESTATUS		=  ".$status.",
				USUARIO_REGISTRO=  ".$data['userRegister'].",
				CLAVE			= '".$data['inputNoTravel']."',
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

    public function getViajesnoPay($idEmpresa){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT V.ID_VIAJE, V.CLAVE, V.INICIO, V.FIN, V.RETRASO, S.DESCRIPCION AS SUCURSAL, U.ECONOMICO, ST.ICONO,C.NOMBRE AS CLIENTE,				
				ST.ID_ESTATUS, P.DESCRIPCION AS E_PAGO
				FROM GTP_VIAJES V
				INNER JOIN SUCURSALES S ON V.ID_SUCURSAL = S.ID_SUCURSAL
				INNER JOIN GTP_ESTATUS_VIAJE ST ON V.ID_ESTATUS = ST.ID_ESTATUS
				INNER JOIN GTP_UNIDADES U ON V.ID_UNIDAD = U.ID_UNIDAD
				INNER JOIN GTP_CLIENTES C ON V.`ID_CLIENTE` = C.ID_CLIENTE 
				INNER JOIN GTP_ESTATUS_PAGO P ON V.ID_ESTATUS_PAGO = P.ID_ESTATUS_PAGO
				WHERE S.ID_EMPRESA = $idEmpresa
				  AND V.ID_ESTATUS_PAGO = 1
				ORDER BY V.ID_VIAJE DESC;";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;
		}	
        
		return $result;
    }    
    
    public function getDataComplete($idObject){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT V.ID_VIAJE, V.CLAVE, V.INICIO, V.FIN, V.RETRASO, S.DESCRIPCION AS SUCURSAL, U.ECONOMICO, ST.ICONO,C.NOMBRE AS CLIENTE,				
				ST.ID_ESTATUS, P.DESCRIPCION AS E_PAGO, E.CLIENTE_UDA, E.USUARIO_UDA, E.PASSWORD_UDA,V.ID_UNIDAD, T.DESCRIPCION AS N_TRANS
				, CONCAT(O.`NOMBRE`,' ',O.`APELLIDOS`) AS N_OPERADOR, A.DESCRIPCION AS TIPO, R.DESCRIPCION AS N_RUTA, ST.DESCRIPCION AS N_STATUS
				FROM GTP_VIAJES V
				INNER JOIN TIPO_VIAJES A ON V.ID_TIPO_VIAJE = A.ID_TIPO_VIAJE
				LEFT JOIN SUCURSALES S ON V.ID_SUCURSAL = S.ID_SUCURSAL
				LEFT JOIN EMPRESAS   E ON S.ID_EMPRESA  = E.ID_EMPRESA
				LEFT JOIN GTP_ESTATUS_VIAJE ST ON V.ID_ESTATUS = ST.ID_ESTATUS
				LEFT JOIN GTP_UNIDADES U ON V.ID_UNIDAD = U.ID_UNIDAD
				LEFT JOIN GTP_CLIENTES C ON V.`ID_CLIENTE` = C.ID_CLIENTE 
				LEFT JOIN GTP_ESTATUS_PAGO P ON V.ID_ESTATUS_PAGO = P.ID_ESTATUS_PAGO	
				LEFT JOIN GTP_OPERADORES    O ON V.ID_OPERADOR   = O.ID_OPERADOR			
				LEFT JOIN GTP_TRANSPORTISTA T ON U.`ID_TRANSPORTISTA` = T.ID_TRANSPORTISTA
				INNER JOIN RUTAS R ON V.ID_RUTA = R.ID_RUTA		
				WHERE V.ID_VIAJE = $idObject
				LIMIT 1";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query[0];
		}	
        
		return $result;
    }     

    public function updatePaymentStatus($sStatus,$idObject){
        $result     = Array();
        $result['status']  = false;        
        
        $idStatusPay = 1;
        switch ($sStatus) {
        	case "Denied":
        		$idStatusPay = 3;
        	break;
        	case "Completed":
        		$idStatusPay = 2;
        	break;        	        	        	
        	default:
        		$idStatusPay = 1; 
        	break;
        }

        $sql="UPDATE  $this->_name SET
				ID_ESTATUS_PAGO 	  = ".$idStatusPay."				
				WHERE $this->_primary = ".$idObject." LIMIT 1";
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
/*
    public function getDataComplete($idObject){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT V.ID_VIAJE, V.CLAVE, V.INICIO, V.FIN, V.RETRASO, S.DESCRIPCION AS SUCURSAL, U.ECONOMICO, ST.ICONO,C.NOMBRE AS CLIENTE,				
				ST.ID_ESTATUS, P.DESCRIPCION AS E_PAGO
				FROM GTP_VIAJES V
				INNER JOIN SUCURSALES S ON V.ID_SUCURSAL = S.ID_SUCURSAL
				INNER JOIN GTP_ESTATUS_VIAJE ST ON V.ID_ESTATUS = ST.ID_ESTATUS
				INNER JOIN GTP_UNIDADES U ON V.ID_UNIDAD  = U.ID_UNIDAD
				INNER JOIN GTP_CLIENTES C ON V.ID_CLIENTE = C.ID_CLIENTE 
				INNER JOIN GTP_ESTATUS_PAGO P ON V.ID_ESTATUS_PAGO = P.ID_ESTATUS_PAGO
				WHERE V.ID_VIAJE = $idObject
				ORDER BY V.ID_VIAJE DESC";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;
		}	
        
		return $result;    	
    }*/
}	