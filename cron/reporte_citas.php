<?php
	error_reporting(E_ALL);
	$conexion = new mysqli('192.168.6.23','dba','t3cnod8A!','SIMA') or die("Some error occurred during connection " . mysqli_error($conexion));

	$realPath='/var/www/vhosts/sima/htdocs/public';
	$sql = "SELECT *
			FROM PROD_CITAS
			WHERE ENVIO_SAP = -1
			  AND FOLIO     IS NOT NULL
			  AND ID_ESTATUS= 4"; 
	$query = mysqli_query($conexion, $sql);
	while($result = mysqli_fetch_array($query)){
		$validate_photos=0;
		$bValRespuestas =0;
		$aForms = getFormsCita($result['ID_CITA']);
		foreach($aForms as $key => $itemsForm){
			$aDataForms = getDataSendbyForms($result['ID_CITA'],$itemsForm['ID_FORMULARIO']);
			if(count($aDataForms)>0){
				foreach($aDataForms as $items){					
					if($items['T_ELEMENTO']=='9' || $items['T_ELEMENTO']=='10'){	
						if (!file_exists($realPath.$items['CONTESTACION'])) {
							$validate_photos++;
						}
					}
				}			
			}else{
				$bValRespuestas++;
			}
		}		
		if($validate_photos==0 && ($bValRespuestas==0 || count($aForms) > $bValRespuestas)){
			setToReportar($result['ID_CITA']);
		}
	}	

	function getFormsCita($idCita){
		global $conexion;
		$result = Array();
    	$sql ="SELECT B.ID_FORMULARIO,			
	               B.TITULO,		
			       B.FOTOS_EXTRAS,
			       B.QRS_EXTRAS,
			       B.FIRMAS_EXTRAS,
			       B.LOCALIZACION
				FROM PROD_CITA_FORMULARIO    A
				  INNER JOIN PROD_FORMULARIO B ON A.ID_FORMULARIO = B.ID_FORMULARIO
				WHERE A.ID_CITA = ".$idCita;
		$query  = mysqli_query($conexion, $sql);
		if($query){
			while($resultSql = mysqli_fetch_array($query)){
				$result[] = $resultSql;
			}
		}
		return $result;
	}

	function getDataSendbyForms($idOject,$idForm){
		global $conexion;
		$result = Array();
    	$sql ="SELECT E.ID_TIPO,			
				       IF (E.ID_TIPO = 8, 'ENCABEZADO','RESPUESTA') AS TIPO,			
				       E.DESCIPCION AS DESCRIPCION,			
				       B.CONTESTACION,			
				       A.FECHA_CAPTURA_EQUIPO,
				       L.ID_TIPO AS T_ELEMENTO		
				FROM PROD_FORM_RESULTADO A			
				  INNER JOIN PROD_FORM_DETALLE_RESULTADO B ON A.ID_RESULTADO = B.ID_RESULTADO			
				  INNER JOIN PROD_FORMULARIO_ELEMENTOS C ON C.ID_ELEMENTO = B.ID_ELEMENTO			
				  INNER JOIN PROD_CITA_FORMULARIO D ON D.ID_RESULTADO = A.ID_RESULTADO			
				  INNER JOIN PROD_ELEMENTOS E ON E.ID_ELEMENTO = C.ID_ELEMENTO
				  INNER JOIN PROD_TPO_ELEMENTO L ON E.ID_TIPO = L.ID_TIPO		
				WHERE A.ID_FORMULARIO = $idForm AND			
				      D.ID_CITA 	  = $idOject			
				ORDER BY C.ORDEN ASC";			
		$query  = mysqli_query($conexion, $sql);
		if($query){
			while($resultSql = mysqli_fetch_array($query)){
				$result[] = $resultSql;
			}
		}
		return $result;		
	}

	function setToReportar($idOject){
		global $conexion;
		$result = false;
    	$sql ="UPDATE PROD_CITAS 
				SET ENVIO_SAP = 1
				WHERE ID_CITA = $idOject";
		$query  = mysqli_query($conexion, $sql);
		if($query){
			$result= true;
		}
		return $result;		
	}	