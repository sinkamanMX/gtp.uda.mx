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

        $idEstado    = (isset($data['inputEstadoF'])    && $data['inputEstadoF']    > 0) ? $data['inputEstadoF']   : 0;
        $idMunicipio = (isset($data['inputMunicipioF']) && $data['inputMunicipioF'] > 0) ? $data['inputMunicipioF']: 0;        
        
        $sql="INSERT INTO $this->_name	
        		SET	NOMBRE 			= '".$data['inputDescripcion']."',
        			RFC				= '".$data['inputRFC']."',
        		 	RAZON_SOCIAL	= '".$data['inputRazonSocial']."',
					ESTATUS			=  ".$data['inputEstatus'].",
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
    
  	public function validateExist($sRfc){
		$result= Array();
    	$sql ="SELECT  *
                FROM ".$this->_name." 
                WHERE RFC = '".$sRfc."' LIMIT 1";		         	
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
	
	/*
	public function getDataTables(){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT *, IF(TIPO_RAZON='M','Moral','Fisica') AS TIPO
				FROM $this->_name
				ORDER BY RAZON_SOCIAL ASC";
		$query   = $this->query($sql);
		if(count($query)>0){
			$result = $query;			
		}
		return $result;
	}   
	
    public function getData($idObject){
		try{
			$result= Array();
	    	$sql ="SELECT *, IF(TIPO_RAZON='M','Moral','Fisica') AS TIPO
					FROM $this->_name
	                WHERE ID_EMPRESA = $idObject";
			$query   = $this->query($sql);
			if(count($query)>0){
				$result	 = $query[0];			
			}	
	        
			return $result;	   			
			
        } catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }                    
    }	
    
   
    
    public function updateRow($data){
       	$result     = Array();
        $result['status']  = false;
        
        $idEstado    = (isset($data['inputEstadoF'])    && $data['inputEstadoF']    > 0) ? $data['inputEstadoF']   : 0;
        $idMunicipio = (isset($data['inputMunicipioF']) && $data['inputMunicipioF'] > 0) ? $data['inputMunicipioF']: 0;  

        $sql="UPDATE $this->_name	
        		SET	  TIPO_RAZON		='".$data['inputTipoRazon']."',
				  	  NOMBRE_EMPRESA	='".$data['inputNameEmpresa']."', 
					  RAZON_SOCIAL		='".$data['inputRazon']."',
					  RFC				='".$data['inputRFC']."',
					  REPRESENTANTE_LEGAL='".$data['inputRep']."',
					  CALLE				='".$data['inputCalle']."',
					  NOEXT				='".$data['inputNoext']."',
					  NOINT				='".$data['inputNoint']."',
					  COLONIA			='".$data['inputColonia']."',
					  ID_MUNICIPIO		= ".$data['inputMunicipio'].",
					  ID_ESTADO			= ".$data['inputEstado'].",
					  CP				='".$data['inputCp']."',
					  DIR_DIFERENTE		= ".$data['inputDirDif'].",
					  FIS_CALLE			='".$data['inputCalleF']."',
					  FIS_NOEXT			='".$data['inputNoextF']."',
					  FIS_NOINT			='".$data['inputNointF']."',
					  FIS_COLONIA		='".$data['inputColoniaF']."',
					  FIS_ID_MUNICIPIO	= ".$idMunicipio.",
					  FIS_ID_ESTADO		= ".$idEstado.",
					  FIS_CP			='".$data['inputCpF']."',
					  ESTATUS			= ".$data['inputEstatus']."
			  WHERE $this->_primary = ".$data['catId']." LIMIT 1";
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
    
    public function deleteRow($data){
		$result = false;    	
    	try{    	
			$sqlInsert="DELETE FROM $this->_name
        			WHERE $this->_primary = ".$data['catId']." LIMIT 1";
    		$queryInsert   = $this->query($sqlInsert,false);				
			if($queryInsert){
				$result = true;	
			}					
        }catch(Exception $e) {
            echo $e->getMessage();
            echo $e->getErrorMessage();
        }
		return $result;    	
    }   

	public function getGlobalViajes(){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT V.*, E.ESTATUS AS N_ESTATUS, CONCAT(U.NOMBRE,' ',U.APATERNO,' ',U.AMATERNO) AS N_TAXISTA, 
				IF(M.NOMBRE_EMPRESA IS NULL,'Sin Asignar',M.NOMBRE_EMPRESA) AS N_EMPRESA,    P.DESCRIPCION AS FPAGO,
				IF(M.ID_EMPRESA IS NULL,'-1',M.ID_EMPRESA) AS IDEMPRESA ,
				ROUND(V.MONTO_TOTAL,2)  AS PRECIO
				FROM ADMIN_VIAJES V
				INNER JOIN SRV_ESTATUS E ON V.ID_SRV_ESTATUS = E.ID_ADMIN_ESTATUS			
				INNER JOIN ADMIN_FORMA_PAGO P ON V.ID_FORMA_PAGO = P.ID_FORMA_PAGO
				 LEFT JOIN ADMIN_USUARIOS U ON V.ID_TAXISTA 	= U.ID_USUARIO
				 LEFT JOIN ADMIN_EMPRESAS M ON U.ID_EMPRESA     = M.ID_EMPRESA
				WHERE V.FECHA_VIAJE > date_sub(NOW(), INTERVAL 30 DAY)";
		$query   = $this->query($sql);
		if(count($query)>0){
			$result = $query;			
		}
		return $result;
	}    

	public function getViajesByEmpresa($idEmpresa){			
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT COUNT(V.ID_VIAJES) AS TOTAL, E.ESTATUS , E.COLOR
				FROM ADMIN_VIAJES V
				INNER JOIN SRV_ESTATUS E ON V.ID_SRV_ESTATUS = E.ID_ADMIN_ESTATUS			
				INNER JOIN ADMIN_FORMA_PAGO P ON V.ID_FORMA_PAGO = P.ID_FORMA_PAGO
				 LEFT JOIN ADMIN_USUARIOS U ON V.ID_TAXISTA 	= U.ID_USUARIO
				 LEFT JOIN ADMIN_EMPRESAS M ON U.ID_EMPRESA     = M.ID_EMPRESA
				WHERE V.FECHA_VIAJE > date_sub(NOW(), INTERVAL 30 DAY)
				 AND  U.ID_EMPRESA = $idEmpresa
				 GROUP BY V.ID_SRV_ESTATUS";
		$query   = $this->query($sql);
		if(count($query)>0){
			$result = $query;
		}
		return $result;
	}
	
	public function getTotalViajes($idEmpresa){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT COUNT(V.ID_VIAJES) AS TOTAL
				FROM ADMIN_VIAJES V
				INNER JOIN SRV_ESTATUS E ON V.ID_SRV_ESTATUS = E.ID_ADMIN_ESTATUS			
				INNER JOIN ADMIN_FORMA_PAGO P ON V.ID_FORMA_PAGO = P.ID_FORMA_PAGO
				 LEFT JOIN ADMIN_USUARIOS U ON V.ID_TAXISTA 	= U.ID_USUARIO
				 LEFT JOIN ADMIN_EMPRESAS M ON U.ID_EMPRESA     = M.ID_EMPRESA
				WHERE V.FECHA_VIAJE > date_sub(NOW(), INTERVAL 30 DAY)
				 AND  U.ID_EMPRESA = $idEmpresa";
		$query   = $this->query($sql);
		if(count($query)>0){
			$result = $query[0]['TOTAL'];			
		}
		return $result;
	}
	
	public function getViajesByTaxis($idEmpresa){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT V.*, CONCAT(U.NOMBRE,' ',U.APATERNO,' ',U.AMATERNO) AS N_TAXISTA, CONCAT(T.PLACAS,', ECO: ',T.`ECO`) AS N_TAXI,
    			ROUND(V.MONTO_TOTAL,2)  AS PRECIO
				FROM ADMIN_VIAJES V
				INNER JOIN SRV_ESTATUS E ON V.ID_SRV_ESTATUS = E.ID_ADMIN_ESTATUS			
				INNER JOIN ADMIN_FORMA_PAGO P ON V.ID_FORMA_PAGO = P.ID_FORMA_PAGO
				INNER JOIN ADMIN_USUARIOS U ON V.ID_TAXISTA 	= U.ID_USUARIO
				INNER JOIN ADMIN_EMPRESAS M ON U.ID_EMPRESA     = M.ID_EMPRESA
				INNER JOIN ADMIN_TAXIS    T ON V.ID_TAXISTA     = T.ADMIN_USUARIOS_ID_USUARIO
				WHERE V.FECHA_VIAJE > date_sub(NOW(), INTERVAL 30 DAY)
				 AND  U.ID_EMPRESA 		= $idEmpresa
				 ORDER BY V.ID_TAXISTA";
		$query   = $this->query($sql);
		if(count($query)>0){
			$result = $query;			
		}
		return $result;		
	}
	
	public function getTaxisDes($aData){
        try{
    		$moreTravels  = Array();
    		$moreMoney    = Array();
    		$moreRecha    = Array();
    		
    		$controlTRavle = 0;
    		$controlMoney  = 0;
    		$controlRec	   = 0;
    		
    		foreach($aData AS $key => $items){
    			if($items['VIAJES'] > @$moreTravels['VIAJES']){
    				$moreTravels = $items;
    			}
    			if(@$items['RECHAZADO']!=""){
	    		    if(@$items['RECHAZADO'] > @$moreRecha['RECHAZADO']){
	    				$moreRecha = $items;
	    			}    		    				
    			}

    		    if($items['PRECIO'] > @$moreMoney['PRECIO']){
    				$moreMoney = $items;
    			}      
    		}
			return Array(
				'aViajes'	=> $moreTravels,
				'aRechazado'=> $moreRecha,
				'aPrecio'	=> $moreMoney
			);			
		} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }      	  			
	}
	
    public function getCodeEmp($inputRfc){
		try{
			$codeRandom = $this->getRandomCode($inputRfc);
			$sResult = '';
	    	$sql ="SELECT COUNT(*) AS TOTAL
					FROM $this->_name
	                WHERE CODIGO_EMPRESA = '$codeRandom'";
			$query   = $this->query($sql);
			if(count($query)>0){
				$result	 = $query[0];
				if($result['TOTAL']==0){
					$sResult = $codeRandom;
				}else{
					$codeRandom = $this->getRandomCode($inputRfc);
					$sResult = '';
			    	$sql ="SELECT COUNT(*) AS TOTAL
							FROM $this->_name
			                WHERE CODIGO_EMPRESA = '$codeRandom'";
					$query   = $this->query($sql);					
					if(count($query)>0){
						$result	 = $query[0];
						if($result['TOTAL']==0){
							$sResult = $codeRandom;
						}
					}		
				}
			}
	        
			return $sResult;	   			
			
        } catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }                    
    }

	function getRandomCode($sRandomPrev){
		$an = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	    $su = strlen($an) - 1;
	    return  substr($sRandomPrev, 0, 3).
	    		substr($an, rand(0, $su), 1) .
	            substr($an, rand(0, $su), 1) .
	            substr($an, rand(0, $su), 1);
	}   

	function validateCodeEmp($codeEmpresa){
		$result=-1;		
		$this->query("SET NAMES utf8",false);
    	$sql ="SELECT $this->_primary
	    		FROM $this->_name
				WHERE CODIGO_EMPRESA = '$codeEmpresa' ";
		$query   = $this->query($sql);
		if(count($query)>0){
			$result	 = $query[0][$this->_primary];
		}
        
		return $result;
	}
	
	
	public function getCbo(){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT $this->_primary AS ID, NOMBRE_EMPRESA AS NAME 
    			FROM $this->_name 
    			ORDER BY NOMBRE_EMPRESA ASC";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;			
	}	
	*/
}