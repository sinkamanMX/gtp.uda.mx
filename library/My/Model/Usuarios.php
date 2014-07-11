<?php
/**
 * Archivo de definición de usuarios
 * 
 * @author EPENA
 * @package library.My.Models
 */

/**
 * Modelo de tabla: usuarios
 *
 * @package library.My.Models
 * @author EPENA
 */
class My_Model_Usuarios extends My_Db_Table
{
    protected $_schema 	= 'petlocator';
	protected $_name 	= 'usuario_cliente';
	protected $_primary = 'clt_id';
	
	public function validateUser($datauser){
		$result= Array('clt_id' => 1,
					   'name'   => 'Enrique',
						'pass'	=> 'ferfefdesd');
		
		/*$this->query("SET NAMES utf8",false); 
    	$sql ="SELECT  clt_id,CONCAT(clt_nombre,' ',clt_apellidos) AS name , clt_clave AS pass
                FROM usuario_cliente 
                WHERE clt_email = '".$datauser['usuario']."'
                 AND clt_clave  = '".$datauser['contrasena']."'";			         	
		$query   = $this->query($sql);
		if(count($query)>0){
			$result	 = $query[0];			
		}	*/
        
		return $result;			
	} 
	
	public function userExist($datauser){
		$result= Array();
    	$sql ="SELECT  clt_id,CONCAT(clt_nombre,' ',clt_apellidos) AS name , clt_clave AS pass
                FROM usuario_cliente 
                WHERE clt_email = '".$datauser['usuario']."' LIMIT 1";			         	
		$query   = $this->query($sql);
		if(count($query)>0){
			$result	 = $query[0];			
		}	
        
		return $result;			
	}
    
    public function getDataUser($datauser){
      	$this->query("SET NAMES utf8",false); 
        
		$result= Array();
    	$sql ="SELECT * 
                FROM usuario_cliente  
                WHERE clt_id = $datauser";			         	
		$query   = $this->query($sql);
		if(count($query)>0){
			$result	 = $query[0];			
		}	
        
		return $result;	        
    }  

    public function setDataUser($data,$iduser){
        $this->query("SET NAMES utf8",false);     	
    	$result=false;
		$sql="UPDATE usuario_cliente SET clt_nombre 	= '".$data['inputNombre']."',
			   clt_apellidos			= '".$data['inputApps']."' ,
			   clt_telefono				= '".$data['inputTel']."' ,
			   clt_celular				= '".$data['inputCel']."' ,
			   clt_calle 				= '".$data['appCalle']."',
			   clt_numero_exterior 		= '".$data['appnoExt']."',
			   clt_numero_interior 		= '".$data['appNoint']."',
			   clt_colonia 				= '".$data['inputCol']."' ,
			   clt_municipio 			=  ".$data['inputMun']."  ,
			   clt_cp 					= '".$data['inputCp']."'  ,
			   clt_estado 				= '".$data['inputEdo']."' ,
			   clt_referencias 			= '".$data['nameRefss']."',
			   clt_requiere_factura 	=  ".$data['inputAsq']." ,
			   clt_domicilio_fiscal_diferente= ".$data['inputDir'].",
			   clt_razon_social 		= '".$data['inputRazon']."',
			   clt_rfc 					= '".$data['inputRfc']."' ,
			   clt_domfiscal_calle 		= '".$data['inputCalleDf']."',
			   clt_domfiscal_numero_exterior= '".$data['inputNoextDf']."',
			   clt_domfiscal_numero_interior= '".$data['inputNointDf']."',
			   clt_domfiscal_colonia 	= '".$data['inputColDf']."',
			   clt_domfiscal_cp 		= '".$data['inputCpDf']."',
			   clt_domfiscal_estado 	= '".$data['inputEdoDf']."',
			   clt_domfiscal_municipio 	=  ".$data['inputMunDf'].",
			   clt_email 				= '".$data['inputEmail']."',
			   clt_clave 				= '".$data['inputpassword']."'
			where clt_id = ".$iduser;
        try{            
    		$query   = $this->query($sql,false);
    		$result  = true;	
        }catch(Exception $e) {
            echo $e->getMessage();
            echo $e->getErrorMessage();
        }
		return $result;			
    }   

    public function registroUser($data){
        $this->query("SET NAMES utf8",false);     	
    	$result=false;
		$sql="INSERT INTO usuario_cliente SET clt_nombre 	= '".$data['inputNombre']."',
			   clt_apellidos			= '".$data['inputApps']."' ,
			   clt_telefono				= '".$data['inputTel']."' ,
			   clt_celular				= '".$data['inputCel']."' ,
			   clt_calle 				= '".$data['appCalle']."',
			   clt_numero_exterior 		= '".$data['appnoExt']."',
			   clt_numero_interior 		= '".$data['appNoint']."',
			   clt_colonia 				= '".$data['inputCol']."' ,
			   clt_municipio 			=  ".$data['inputMun']."  ,
			   clt_cp 					= '".$data['inputCp']."'  ,
			   clt_estado 				= '".$data['inputEdo']."' ,
			   clt_referencias 			= '".$data['nameRefss']."',
			   clt_requiere_factura 	=  ".$data['inputAsq']." ,
			   clt_domicilio_fiscal_diferente= ".$data['inputDir'].",
			   clt_razon_social 		= '".$data['inputRazon']."',
			   clt_rfc 					= '".$data['inputRfc']."' ,
			   clt_domfiscal_calle 		= '".$data['inputCalleDf']."',
			   clt_domfiscal_numero_exterior= '".$data['inputNoextDf']."',
			   clt_domfiscal_numero_interior= '".$data['inputNointDf']."',
			   clt_domfiscal_colonia 	= '".$data['inputColDf']."',
			   clt_domfiscal_cp 		= '".$data['inputCpDf']."',
			   clt_domfiscal_estado 	= '".$data['inputEdoDf']."',
			   clt_domfiscal_municipio 	=  ".$data['inputMunDf'].",
			   clt_email 				= '".$data['inputEmail']."',
			   clt_clave 				= '".$data['inputpassword']."'";		
        try{            
    		$query   = $this->query($sql,false);
    		$result  = true;	
        }catch(Exception $e) {
            echo $e->getMessage();
            echo $e->getErrorMessage();
        }
		return $result;	    	
    }
    
    public function getAllUsuarios(){
      	$this->query("SET NAMES utf8",false); 
        
		$result= Array();
    	$sql ="SELECT usuario_cliente.* , COUNT(masc_id) AS no_mascotas
                FROM usuario_cliente
                LEFT JOIN mascota ON usuario_cliente.`clt_id` = mascota.`clt_id`                
                GROUP BY usuario_cliente.`clt_id`";			         	
		$query   = $this->query($sql);
		if(count($query)>0){
			$result	 = $query;			
		}	
        
		return $result;	        	
    }
    
    public function changeStatus($Status,$idItem){
        $result     = false;
      	$this->query("SET NAMES utf8",false); 
        $sql="UPDATE usuario_cliente SET
                clt_permiso  = $Status
                WHERE clt_id = $idItem limit 1";
        try{            
    		$query   = $this->query($sql,false);
    		$result  = true;	
        }catch(Exception $e) {
            echo $e->getMessage();
            echo $e->getErrorMessage();
        }
		return $result;	    	
    }

    public function getInfo($idObject){
      	$this->query("SET NAMES utf8",false);       	
		$result= Array();
    	$sql ="SELECT  *
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
        
		$status	= (isset($data['inputEstatus']) && $data['inputEstatus']=='1') ? 1 : 0;
      	$this->query("SET NAMES utf8",false);     	
		$sql="INSERT INTO usuario_cliente 
			SET clt_nombre 	= '".$data['inputNombre']."',
			   clt_apellidos			= '".$data['inputApps']."' ,
			   clt_telefono				= '".$data['inputTel']."' ,
			   clt_empresa				= '".$data['inputEmpresa']."' ,
			   clt_telefono				= '".$data['inputTel']."' ,
			   clt_celular				= '".$data['inputCel']."' ,
			   clt_calle				= '".$data['inputCalle']."' ,
			   clt_numero_exterior		= '".$data['inputNoExt']."' ,
			   clt_numero_interior		= '".$data['inputNoint']."' ,
			   clt_colonia				= '".$data['inputColonia']."' ,
			   clt_municipio			=  ".$data['inputMunicipio']." ,
			   clt_cp					= '".$data['inputCp']."' ,
			   clt_estado				= '".$data['inputEstado']."' ,
			   clt_referencias			= '".$data['inputRefs']."' ,
			   clt_requiere_factura		= '".$data['inputComprobante']."' ,
			   clt_razon_social			= '".$data['inputRazon']."' ,
			   clt_numero_referencia    = '".$data['inputRefp']."' ,
			   clt_rfc					= '".$data['inputRfc']."' ,
			   clt_email 				= '".$data['inputEmail']."',
			   clt_clave 				= '".$data['inputpassword']."',
			   clt_permiso	 			".$status;
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
        
		$status	= (isset($data['inputEstatus']) && $data['inputEstatus']=='on') ? 1 : 0;
      	$this->query("SET NAMES utf8",false); 
		$sql="UPDATE usuario_cliente 
					SET clt_nombre 	= '".$data['inputNombre']."',
			   clt_apellidos			= '".$data['inputApps']."' ,
			   clt_telefono				= '".$data['inputTel']."' ,
			   clt_empresa				= '".$data['inputEmpresa']."' ,
			   clt_telefono				= '".$data['inputTel']."' ,
			   clt_celular				= '".$data['inputCel']."' ,
			   clt_calle				= '".$data['inputCalle']."' ,
			   clt_numero_exterior		= '".$data['inputNoExt']."' ,
			   clt_numero_interior		= '".$data['inputNoint']."' ,
			   clt_colonia				= '".$data['inputColonia']."' ,
			   clt_municipio			=  ".$data['inputMunicipio']." ,
			   clt_cp					= '".$data['inputCp']."' ,
			   clt_estado				= '".$data['inputEstado']."' ,
			   clt_referencias			= '".$data['inputRefs']."' ,
			   clt_requiere_factura		= ".$data['inputComprobante']." ,
			   clt_razon_social			= '".$data['inputRazon']."' ,
			   clt_numero_referencia    = '".$data['inputRefp']."' ,
			   clt_rfc					= '".$data['inputRfc']."' ,
			   clt_email 				= '".$data['inputEmail']."',
			   clt_clave 				= '".$data['inputpassword']."',
			   clt_permiso	 			= ".$status."
			   WHERE clt_id = ".$data['catId'];		
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
}