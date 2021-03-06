<?php

class reports_AtencionController extends My_Controller_Action
{	
	protected $_clase = 'ratencion';
	public $validateNumbers;
	public $validateAlpha;
	public $dataIn;
	public $idToUpdate=-1;
	public $errors = Array();
	public $operation='init';
	public $resultop=null;	
		
    public function init()
    {
    	try{
		$sessions = new My_Controller_Auth();
		$perfiles = new My_Model_Perfiles();
        if(!$sessions->validateSession()){
            $this->_redirect('/');		
		}
		$this->view->dataUser   = $sessions->getContentSession();
		$this->view->modules    = $perfiles->getModules($this->view->dataUser['ID_PERFIL']);
		$this->view->moduleInfo = $perfiles->getDataModule($this->_clase);
		$this->view->idEmpresa  = $this->view->dataUser['ID_EMPRESA'];
		
		$this->dataIn = $this->_request->getParams();

		} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }  		
    }
    
    public function indexAction(){
		try{   	
	    	$aDataTable  = Array();
	    	$aDataSearch['fecFin'] = Array();
			$aDataSearch['idEmpresa'] 	= $this->view->dataUser['ID_EMPRESA'];
			$aDataSearch['idUsuario'] 	= $this->view->dataUser['ID_USUARIO'];
			$aDataSearch['idPerfil'] 	= $this->view->dataUser['ID_PERFIL'];
			$aDataSearch['idMonitoreo'] = $this->view->dataUser['ID_MONITOREO'];						    	
			$aDataSearch['fecIncio'] 	= date("Y-m-d ")."00:00:00";
			$aDataSearch['fecFin'] 		= date("Y-m-d ")."23:59:59";    
			$aDataSearch['inputUserAssign'] = '';
			$aDataSearch['inputCliente'] 	= '';	
			$aDataSearch['inputStatus'] 	= '';
			
			$viajes 	 = new My_Model_Viajes();
			$cFunctions  = new My_Controller_Functions();
			$cEmpresas   = new My_Model_Empresas();
			
			$aStatus     = $viajes->getCboStatus();	
			
		
			$aEmpresas   = $cEmpresas->getCbo($this->view->dataUser['ID_MONITOREO']);
			$sStatus     = '';
			$sUsuario    = '';
			$sCliente	 = '';
			$sEmpresa	 = '';
			
			if(@$this->dataIn['option']=='getReport' && isset($this->dataIn['option'])){					
				$aDataSearch['inputCliente'] 	= $this->dataIn['inputCliente'];
				$aDataSearch['fecIncio'] 		= $this->dataIn['inputFechaIn'];
				$aDataSearch['fecFin'] 			= $this->dataIn['inputFechaFin'];
				$aDataSearch['inputStatus'] 	= $this->dataIn['inputStatus'];	
				
				$sStatus 	= $this->dataIn['inputStatus'];				
				$sEmpresa	= $this->dataIn['inputCliente'];
			}
			
			$this->dataIn['inputFechaIn']  = $aDataSearch['fecIncio'];
			$this->dataIn['inputFechaFin'] = $aDataSearch['fecFin'];
			
			$aDataTable  = $viajes->getReportAtencion($aDataSearch);
			
			$this->view->datatTable = $aDataTable;
			$this->view->data		= $this->dataIn;
			$this->view->aStatus 	= $cFunctions->selectDb($aStatus,$sStatus);
			$this->view->aEmpresas 	= $cFunctions->selectDb($aEmpresas,$sEmpresa);
			
    	} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        } 			
    }
    
    public function exportdatatravelAction(){
		try{   			
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender();

			$validateNumbers = new Zend_Validate_Digits();
			$classObject 	= new My_Model_Viajes();
			$functions   	= new My_Controller_Functions();
			$sucursales 	= new My_Model_Sucursales();
			$transportistas = new My_Model_Transportistas();
						
			if($validateNumbers->isValid(@$this->dataIn['travelID'])){
				$dataInfo    = $classObject->getDataExport($this->dataIn['travelID']);						
				$aRecorrido  = $classObject->getRecorrido($this->dataIn['travelID'],true);				

				$nameClient = $this->view->dataUser['N_EMPRESA']; 
				$dateCreate = date("d-m-Y H:i");
				$createdBy	= $this->view->dataUser['USUARIO']; 
					
					/** PHPExcel */
					include 'PHPExcel.php';
					
					/** PHPExcel_Writer_Excel2007 */
					include 'PHPExcel/Writer/Excel2007.php';			
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
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', $nameClient);
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A2', 'Reporte de Viaje');
					$objPHPExcel->getActiveSheet()->getStyle("A2")->getFont()->setSize(20);
					$objPHPExcel->getActiveSheet()->getStyle("A2")->getFont()->setBold(true);
					$objPHPExcel->setActiveSheetIndex(0)
						->setCellValue('A3', 'Reporte Creado '.$dateCreate.' por '.$createdBy);	
					$objPHPExcel->getActiveSheet()->mergeCells('A1:H1');
					$objPHPExcel->getActiveSheet()->getStyle('A1:H1')
							->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->mergeCells('A2:H2');
					$objPHPExcel->getActiveSheet()->getStyle('A2:H2')
							->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);	
					$objPHPExcel->getActiveSheet()->mergeCells('A3:H3');	
					$objPHPExcel->getActiveSheet()->getStyle('A3:H3')
							->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
								
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A5', 'Clave Viaje');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B5', $dataInfo['CLAVE']);
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C5', 'Unidad');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D5', $dataInfo['ECONOMICO']);

					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A6', 'Ruta');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B6', $dataInfo['N_RUTA']);
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C6', 'Eco');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D6', $dataInfo['ECONOMICO']);	

					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A7', html_entity_decode('Descripci&oacute;n'));
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B7', $dataInfo['NDESC']); 
					$objPHPExcel->getActiveSheet()->mergeCells('B7:D7');	

					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A8', 'Fecha Inicio');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B8', $dataInfo['INICIO']);
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C8', 'Fecha Fin');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D8', $dataInfo['FIN']);						
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A9', 'Sucursal');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B9', $dataInfo['SUCURSAL']);
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C9', 'Cliente');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D9', $dataInfo['CLIENTE']);

					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A10', 'Transportista');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B10', $dataInfo['TRANSPORTISTA']);
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C10', 'Operador');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D10', $dataInfo['CONDUCTOR']);						
							
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A11', 'Estatus');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B11', $dataInfo['DESC_E']);
		
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C11', 'Total incidencias');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D11', $dataInfo['INCIDENCIAS']);	
		
					/**
					 * Detalle del Viaje
					 * */
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A13', 'Fecha GPS');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B13', 'Latitud');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C13', 'Longitud');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D13', 'Velocidad');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E13', 'Angulo');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('F13', 'Tipo');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G13', 'Incidencia');
					
					$slastColumn = 'H';
					if($this->view->dataUser['ID_PERFIL']==1){
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue($slastColumn.'13', 'Comentario Incidencia');
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I13', 'Usuario Comento');
						$slastColumn= 'J';
					}

					$objPHPExcel->setActiveSheetIndex(0)->setCellValue($slastColumn.'13', html_entity_decode('Ubicacion'));					
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($styleHeader, 'A13:'.$slastColumn.'13');
					$objPHPExcel->setActiveSheetIndex(0)->getStyle("A13:".$slastColumn."13")->getFont()->setSize(12);
					$objPHPExcel->setActiveSheetIndex(0)->getStyle("A13:".$slastColumn."13")->getFont()->setBold(true);						
					
					$rowControlHist=14;
					$zebraControl=0;				
					foreach($aRecorrido as $reporte){
						$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0,  ($rowControlHist), $reporte['FECHA']);
						$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,  ($rowControlHist), $reporte['LATITUD']);
						$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2,  ($rowControlHist), $reporte['LONGITUD']);
						$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(3,  ($rowControlHist), round($reporte['VELOCIDAD'],2)." km/h.");
						$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(4,  ($rowControlHist), $reporte['ANGULO']);
						$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(5,  ($rowControlHist), $reporte['MODO']);
						$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(6,  ($rowControlHist), $reporte['INCIDENCIA']);						

						$ilastColumn = 7;
						if($this->view->dataUser['ID_PERFIL']==1){
							$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($ilastColumn,  ($rowControlHist), $reporte['INC_COMENTARIOS']);
							$ilastColumn++;
							$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($ilastColumn,  ($rowControlHist), $reporte['INC_USER']);
							$ilastColumn++;
						}					
						
						$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($ilastColumn,  ($rowControlHist), $reporte['UBICACION']);

						
						if($zebraControl++%2==1){
							$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($stylezebraTable, 'A'.$rowControlHist.':H'.$rowControlHist);			
						}		
								
						$objPHPExcel->getActiveSheet()->getStyle('A'.$rowControlHist.':C'.$rowControlHist)
								->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);				
						$objPHPExcel->getActiveSheet()->getStyle('G'.$rowControlHist)
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
					if($this->view->dataUser['ID_PERFIL']==1){
						$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('I')->setAutoSize(true);		
					}	
		
					$objPHPExcel->setActiveSheetIndex(0);
					$filename  = "Viaje_".$dataInfo['ID_VIAJE']."_".date("Y_m_d").".xlsx";
					header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
					header('Content-Disposition: attachment;filename="'.$filename.'"');
					header('Cache-Control: max-age=0');			
					$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
					$objWriter->save('php://output');
			}
		} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }    	
    }
    
    public function exportsearchAction(){
    	$aDataTable = Array();
    	try{
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender();
			
	    	$aDataTable  = Array();
	    	$aDataSearch = Array();
			$aDataSearch['idEmpresa'] 	= $this->view->dataUser['ID_EMPRESA'];
			$aDataSearch['idUsuario'] 	= $this->view->dataUser['ID_USUARIO'];
			$aDataSearch['idPerfil'] 	= $this->view->dataUser['ID_PERFIL'];	
			$aDataSearch['idMonitoreo'] = $this->view->dataUser['ID_MONITOREO'];		    	
			$aDataSearch['fecIncio'] 	= date("Y-m-d h:i:s");
			$aDataSearch['fecFin'] 		= date("Y-m-d h:i:s");    
			$aDataSearch['inputCliente'] 	= '';	
			$aDataSearch['inputStatus'] 	= '';
			
			$viajes 	 = new My_Model_Viajes();
			
			if(@$this->dataIn['option']=='getReport' && isset($this->dataIn['option'])){				
				$aDataSearch['fecIncio'] 	= $this->dataIn['inputFechaIn'];
				$aDataSearch['fecFin'] 		= $this->dataIn['inputFechaFin'];	
				$aDataSearch['inputCliente'] 	= $this->dataIn['inputCliente'];				
				$aDataSearch['inputStatus'] 	= $this->dataIn['inputStatus'];	
			}
			
			$aDataTable  = $viajes->getReportAtencion($aDataSearch);

			if(count($aDataTable)>0){
				$dateCreate = date("d-m-Y H:i");
				$createdBy	= $this->view->dataUser['USUARIO']; 

				//** PHPExcel *
				include 'PHPExcel.php';
				
				//** PHPExcel_Writer_Excel2007 *
				include 'PHPExcel/Writer/Excel2007.php';			
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
					->setCellValue('A3', 'Reporte Creado '.$dateCreate.' por '.$createdBy);	
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
						
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A5', 'Fecha Creaci�n');
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B5', 'Fecha Inicio (Programada)');
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C5', 'Fecha Inicio Antecion');
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D5', 'Cliente');				
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E5', 'Unidad');				
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('F5', 'Descripcion Viaje');
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G5', 'Estatus');
				$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($styleHeader, 'A5:G5');

				$objPHPExcel->setActiveSheetIndex(0)->getStyle("A5:N5")->getFont()->setSize(12);
				$objPHPExcel->setActiveSheetIndex(0)->getStyle("A5:N5")->getFont()->setBold(true);
				
				$rowControlHist=6;
				$zebraControl=0;				
				foreach($aDataTable as $reporte){
					$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0,  ($rowControlHist), $reporte['CREADO']);
					$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,  ($rowControlHist), $reporte['INICIO']);
					$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2,  ($rowControlHist), $reporte['INICIO_REAL']);
					$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(3,  ($rowControlHist), $reporte['DESC_EMPRESA']);
					$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(4,  ($rowControlHist), $reporte['IDENTIFICADOR']);
					$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(5,  ($rowControlHist), $reporte['N_RUTA']);
					$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(6,  ($rowControlHist), $reporte['DES_STATUS']);
										
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($stylezebraTable, 'A'.$rowControlHist.':G'.$rowControlHist);
							
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
								
				$filename  = "Atencion_Viajes_".date("Y_m_d").".xlsx";
				header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
				header('Content-Disposition: attachment;filename="'.$filename.'"');
				header('Cache-Control: max-age=0');							
				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
				$objWriter->save('php://output');				
			}else{
				echo "No hay informaci�n para exportar";
			}
    	} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        } 	
    }
}