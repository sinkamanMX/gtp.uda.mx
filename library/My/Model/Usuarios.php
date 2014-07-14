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
    protected $_schema 	= 'gtp_bd';
	protected $_name 	= 'USUARIOS';
	protected $_primary = 'ID_USUARIO';
	
	public function validateUser($datauser){
		$result= Array();		
		$this->query("SET NAMES utf8",false);
    	$sql ="SELECT $this->_primary
	    		FROM USUARIOS U
				WHERE U.USUARIO  = '".$datauser['usuario']."'
                 AND  U.PASSWORD = SHA1('".$datauser['contrasena']."')";
		$query   = $this->query($sql);
		if(count($query)>0){
			$result	 = $query[0];
		}
        
		return $result;			
	} 
    
    public function getDataUser($idObject){
      	$this->query("SET NAMES utf8",false); 
        
		$result= Array();
    	$sql ="SELECT U.* ,P.*, S.*, E.* ,E.NOMBRE AS N_EMPRESA
				FROM USUARIOS U
				INNER JOIN PERFILES    P  ON U.ID_PERFIL     = P.ID_PERFIL
				INNER JOIN USR_EMPRESA UE ON U.ID_USUARIO    = UE.ID_USUARIO
				INNER JOIN SUCURSALES  S  ON UE.ID_SUCURSAL  = S.ID_SUCURSAL
				INNER JOIN EMPRESAS    E  ON S.ID_EMPRESA    = E.ID_EMPRESA
                WHERE U.ID_USUARIO = $idObject";			         	
		$query   = $this->query($sql);
		if(count($query)>0){
			$result	 = $query[0];			
		}	
        
		return $result;	        
    }  
    
    public function validatePassword($datauser){
		$result= Array();		
		$this->query("SET NAMES utf8",false);
    	$sql ="SELECT $this->_primary
	    		FROM USUARIOS U
				WHERE U.PASSWORD   = SHA1('".$datauser['VPASSWORD']."')
				  AND U.ID_USUARIO = ".$datauser['ID_USUARIO'];
		$query   = $this->query($sql);
		if(count($query)>0){
			$result	 = $query[0];
		}
        
		return $result;		    	
    }
    
    public function changePass($datauser){
        $result     = Array();
        $result['status']  = false;

        $sql="UPDATE  $this->_name
				SET  PASSWORD 	=  SHA1('".$datauser['NPASSWORD']."')					 
					 WHERE $this->_primary =".$datauser['ID_USUARIO']." LIMIT 1";
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