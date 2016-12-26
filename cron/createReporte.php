<?php
	error_reporting(E_ALL);
	require 'PHPMailerAutoload.php';
	require_once 'PHPExcel.php';
	include 'PHPExcel/Writer/Excel2007.php';				

	$conexion = new mysqli('localhost','dba','t3cnod8A!','gtp_db') or die("Some error occurred during connection " . mysqli_error($conexion));
	/** PHPExcel */ 
	
	$realPath   = '/var/www/vhosts/gtp/htdocs';
	$pathReport = '/var/www/vhosts/gtp/htdocs/cron/reportes/';

	$mail = new PHPMailer();
	$mail->IsSMTP();
	$mail->SMTPDebug  = 0;
	/*$mail->Host       = 'smtp.tecnologiza.me';
	$mail->Port       = 587;
	$mail->SMTPSecure = 'tls';
	$mail->SMTPAuth   = true;
	$mail->Username   = "no-reply@tecnologiza.me";
	$mail->Password   = "nOr3plym41l3r_";*/
	$mail->Host    = "mail2.grupouda.com.mx"; // specify main and backup server
    $mail->SMTPAuth  = true; // turn on SMTP authentication
    $mail->Username  = "avl.4togo"; // SMTP username
    $mail->Password  = "qazwsx"; // SMTP password
	$mail->Sender 	 ='no-reply@grupouda.com.mx';
	$mail->SetFrom("no-reply@grupouda.com.mx", "no-reply@grupouda.com.mx",FALSE);	

	$sql = "SELECT *
			FROM GTP_REPORTE_MAIL
			WHERE ENVIADO = 0 LIMIT 5"; 
	$query = mysqli_query($conexion, $sql);

	while($result = mysqli_fetch_array($query)){
    	$aDataSearch = Array();			
		$aDataSearch['fecIncio'] 		= $result['FECHA_INICIO'];
		$aDataSearch['fecFin'] 			= $result['FECHA_FIN'];	
		$aDataSearch['inputUserAssign'] = $result['ID_USUARIO'];
		$aDataSearch['inputCliente'] 	= $result['ID_CLIENTE'];				
		$aDataSearch['inputStatus'] 	= $result['ID_ESTATUS'];
		
		//$aDataTable  = getReportViajes($aDataSearch);

				
		$sFilter = '';
		
		if(isset($aDataSearch['inputUserAssign']) && $aDataSearch['inputUserAssign']!="0"){			
			$sFilter .= ' V.ID_USUARIO_ASIGNADO  ='.$aDataSearch['inputUserAssign'].' AND';
		}			
		
    	if(isset($aDataSearch['inputCliente']) && $aDataSearch['inputCliente']!="0"){
			$sFilter .=  ' U.ID_EMPRESA  ='.$aDataSearch['inputCliente'].' AND';	
		}

    	if(isset($aDataSearch['inputStatus']) &&  $aDataSearch['inputStatus']!="0"){
			$sFilter .= ' V.ID_ESTATUS  ='.$aDataSearch['inputStatus'].' AND';	
		}
			 
    	$sql2 ="SELECT V.ID_VIAJE, V.CLAVE, V.INICIO, V.FIN, S.DESCRIPCION AS SUCURSAL, U.ECONOMICO,C.NOMBRE AS CLIENTE,  
    			CONCAT(O.NOMBRE,' ',O.APELLIDOS) AS N_OPERADOR , T.DESCRIPCION AS TRANSPORTISTA
				, (SELECT COUNT(ID_VIAJE) FROM GTP_INCIDENCIAS_VIAJE WHERE ID_VIAJE = V.ID_VIAJE) AS INCIDENCIAS
				, CONCAT(A.NOMBRE,' ',A.APELLIDOS ) AS MONITOR, ST.DESCRIPCION AS DES_STATUS, E.NOMBRE AS DESC_EMPRESA, R.DESCRIPCION AS N_RUTA,
				U.IDENTIFICADOR,
				V.INICIO_REAL,V.FIN_REAL, V.ESTADIA_DESTINO, SEC_TO_TIME(TIMESTAMPDIFF(SECOND , V.INICIO, V.INICIO_REAL )) AS DIF_INICIO,
				SEC_TO_TIME(TIMESTAMPDIFF(SECOND , V.ESTADIA_DESTINO, V.FIN_REAL )) AS DIF_FIN
				FROM GTP_VIAJES V
				INNER JOIN SUCURSALES S ON V.ID_SUCURSAL = S.ID_SUCURSAL
				INNER JOIN USUARIOS   A ON V.ID_USUARIO_ASIGNADO = A.ID_USUARIO
				INNER JOIN GTP_ESTATUS_VIAJE ST ON V.ID_ESTATUS = ST.ID_ESTATUS
				INNER JOIN GTP_UNIDADES U ON V.ID_UNIDAD  = U.ID_UNIDAD
				INNER JOIN EMPRESAS     E ON U.ID_EMPRESA = E.ID_EMPRESA
				INNER JOIN RUTAS        R ON V.ID_RUTA    = R.ID_RUTA
				LEFT JOIN GTP_CLIENTES C ON V.`ID_CLIENTE` = C.ID_CLIENTE 
				LEFT JOIN GTP_OPERADORES O ON V.`ID_OPERADOR` = O.ID_OPERADOR
				LEFT JOIN GTP_TRANSPORTISTA T ON O.ID_TRANSPORTISTA = T.ID_TRANSPORTISTA
				WHERE   ".$sFilter." 
						 (V.INICIO BETWEEN '".$aDataSearch['fecIncio']."' AND '".$aDataSearch['fecFin']."'
				  		OR  V.FIN    BETWEEN '".$aDataSearch['fecIncio']."' AND '".$aDataSearch['fecFin']."')
				  GROUP BY V.ID_VIAJE";		
		$query2 = mysqli_query($conexion, $sql2);
		$totalRows = mysqli_num_rows($query2);

		if($totalRows>0){		
			$dateCreate = date("d-m-Y H:i");
		
			$objPHPExcel = new PHPExcel();
			$objPHPExcel->getProperties()->setCreator("UDA")
									 ->setLastModifiedBy("UDA")
									 ->setTitle("Office 2007 XLSX")
									 ->setSubject("Office 2007 XLSX")
									 ->setDescription("Reporte del Viaje")
									 ->setKeywords("office 2007 openxml php")
									 ->setCategory("Reporte del Viaje");
			
			$styleHeader = new PHPExcel_Style();
			$stylezebraTable = new PHPExcel_Style();  

			$styleHeader->applyFromArray(array(
				'fill' => array(
					'type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('argb' => '459ce6')
				)
			));

			$stylezebraTable->applyFromArray(array(
				'fill' => array(
					'type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('argb' => 'e7f3fc')
				)
			));	

			$zebraTable = array(
				'fill' => array(
					'type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('argb' => 'e7f3fc')
				)
			);
			
			/**
			 * Header del Reporte
			 **/
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', 'Viajes Grupo UDA');
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A2', 'Reporte de Viaje');
			$objPHPExcel->getActiveSheet()->getStyle("A2")->getFont()->setSize(20);
			$objPHPExcel->getActiveSheet()->getStyle("A2")->getFont()->setBold(true);
			$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A3', 'Reporte Creado por sistema');	
			$objPHPExcel->getActiveSheet()->mergeCells('A1:H1');
			$objPHPExcel->getActiveSheet()->getStyle('A1:H1')
					->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->mergeCells('A2:H2');
			$objPHPExcel->getActiveSheet()->getStyle('A2:H2')
					->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);	
			$objPHPExcel->getActiveSheet()->mergeCells('A3:H3');	
			$objPHPExcel->getActiveSheet()->getStyle('A3:H3')
					->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			/**
			 * Detalle del Viaje
			 * */
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A5', 'Id Viaje');
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B5', 'Cliente');
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C5', 'Transportista');
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D5', 'Unidad');
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E5', 'Operador');
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('F5', 'Fecha Inicio (Programada)');
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G5', 'Fecha Inicio (Real)');
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('H5', 'Diferencia Inicio Viaje');
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I5', 'Estadia en Destino');
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('J5', 'Fecha Fin de Viaje');
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('K5', 'Tiempo Estadia en Destino');
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('L5', 'Monitorista');
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('M5', 'Estatus');
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('N5', 'Incidencias');
			$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($styleHeader, 'A5:N5');	

			$objPHPExcel->setActiveSheetIndex(0)->getStyle("A5:N5")->getFont()->setSize(12);
			$objPHPExcel->setActiveSheetIndex(0)->getStyle("A5:N5")->getFont()->setBold(true);

			
			$rowControlHist=6;
			$zebraControl=0;				
			while($reporte = mysqli_fetch_array($query2)){
				$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0,  ($rowControlHist), $reporte['CLAVE']);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,  ($rowControlHist), $reporte['CLIENTE']);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2,  ($rowControlHist), $reporte['TRANSPORTISTA']);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(3,  ($rowControlHist), $reporte['ECONOMICO']);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(4,  ($rowControlHist), $reporte['N_OPERADOR']);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(5,  ($rowControlHist), $reporte['INICIO']);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(6,  ($rowControlHist), $reporte['INICIO_REAL']);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(7,  ($rowControlHist), $reporte['DIF_INICIO']);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(8,  ($rowControlHist), $reporte['ESTADIA_DESTINO']);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(9,  ($rowControlHist), $reporte['FIN_REAL']);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(10, ($rowControlHist), $reporte['DIF_FIN']);
				
				$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(11,  ($rowControlHist), $reporte['MONITOR']);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(12,  ($rowControlHist), $reporte['DES_STATUS']);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(13,  ($rowControlHist), $reporte['INCIDENCIAS']);
			
				
				if($zebraControl++%2==1){
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($stylezebraTable, 'A'.$rowControlHist.':N'.$rowControlHist);
				}
						
				$objPHPExcel->getActiveSheet()->getStyle('A'.$rowControlHist.':D'.$rowControlHist)
						->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);				
				$objPHPExcel->getActiveSheet()->getStyle('H'.$rowControlHist)
						->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('F'.$rowControlHist.':K'.$rowControlHist)
						->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);																
				$rowControlHist++;					
			}	
			

			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('A')->setAutoSize(true);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('B')->setAutoSize(true);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('C')->setAutoSize(true);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('D')->setAutoSize(true);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('E')->setAutoSize(true);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('F')->setAutoSize(true);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('G')->setAutoSize(true);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('H')->setAutoSize(true);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('I')->setAutoSize(true);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('J')->setAutoSize(true);
							
			$filename  = "Viajes_".date("Y_m_d").".xlsx";

			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
			$objWriter->save($pathReport.$filename);

			if(file_exists($pathReport.$filename)){
				setMarkReporte($result['ID_REPORTE_MAIL']);

				$sMensaje = 'Se ha generado un reporte de viajes.';

				$mail->AddAddress($result['DESTINATARIO'], $result['DESTINATARIO']);				
				$mail->Subject = "Reporte de viajes";
				$mail->MsgHTML(utf8_decode($sMensaje));
				$mail->AltBody = utf8_decode($sMensaje);
				$mail->addAttachment($pathReport.$filename,$filename);
				
				//Enviamos el correo
				if(!$mail->Send()) {
				  	echo 'Message could not be sent.';
					echo 'Mailer Error: ' . $mail->ErrorInfo;
				}else{
					unlink($pathReport.$filename);
				}

				$mail->ClearAllRecipients();
				$mail->clearAttachments();				
			}		
		}
	}

    function getReportViajes($aDataFilter){
    	global $conexion;    	

		$query   = mysqli_query($conexion, $sql);
		$result  = mysqli_fetch_array($query);
		
		return $result;	     	
    } 	

	function setMarkReporte($idOject){
		global $conexion;
		$result = false;
    	$sql ="UPDATE GTP_REPORTE_MAIL 
				  SET   ENVIADO 		= 1,
				  		FECHA_ENVIADO	= CURRENT_TIMESTAMP
				WHERE ID_REPORTE_MAIL = $idOject";
		$query  = mysqli_query($conexion, $sql);
		if($query){
			$result= true;
		}
		return $result;		
	}    