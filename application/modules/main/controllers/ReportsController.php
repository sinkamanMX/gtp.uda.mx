<?php

class main_reportsController extends My_Controller_Action
{	
	protected $_clase = 'reports';
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
    	$aDataTable = Array();
    	
		if(@$this->dataIn['option']=='getReport' && isset($this->dataIn['option'])){	
			$viajes 	 = new My_Model_Viajes();
			$transportista= new My_Model_Transportistas();
			$sucursales	 = new My_Model_Sucursales();				
			$functions   = new My_Controller_Functions();
			
			$fechaInicio = $this->dataIn['inputFechaIn'];
			$fechaFin	 = $this->dataIn['inputFechaFin'];
			$option		 = $this->dataIn['inputOption'];
			$optionValue = $this->dataIn['inputNoTravel']; 
			if($option=='0'){
				$aDataTable  = $viajes->getReportViajes($fechaInicio,$fechaFin);	
			}elseif($option=='1'){
				$aDataTable = $viajes->getDataReport($optionValue);
			}elseif($option=='2'){
				$aClientes  = $sucursales->getFilterSucursales($optionValue,$this->view->dataUser['ID_EMPRESA']);
				$stringData = $functions->arrayToStringDb($aClientes);	
				if($stringData!=""){
					$aDataTable = $viajes->getDataFromCliente($stringData);	
				}
			}elseif($option=='3'){
				$operadores = new My_Model_Operadores();
				$aOperadores= $operadores->getFilterOperadores($optionValue,$this->view->dataUser['ID_EMPRESA']);
				$stringData = $functions->arrayToStringDb($aOperadores);	
				if($stringData!=""){
					$aDataTable = $viajes->getDataFromSucursal($stringData);	
				}
			}
		}
		$this->view->datatTable = $aDataTable;
		$this->view->data		= $this->dataIn;
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
				$aRecorrido  = $classObject->getRecorrido($this->dataIn['travelID']);				

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
								
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A6', 'Descripcion');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B6', $dataInfo['NDESC']); 
					$objPHPExcel->getActiveSheet()->mergeCells('B6:D6');	
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A7', 'Fecha Inicio');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B7', $dataInfo['INICIO']);
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C7', 'Fecha Fin');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D7', $dataInfo['FIN']);			
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A8', 'Sucursal');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B8', $dataInfo['SUCURSAL']);
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C8', 'Cliente');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D8', $dataInfo['CLIENTE']);
		
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A8', 'Transportista');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B8', $dataInfo['TRANSPORTISTA']);
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C8', 'Operador');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D8', $dataInfo['NOMBRE']);	
		
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A9', 'Estatus');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B9', $dataInfo['DESC_E']);
		
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C9', 'Total incidencias');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D9', $dataInfo['INCIDENCIAS']);	
		
					/**
					 * Detalle del Viaje
					 * */
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A11', 'Fecha GPS');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B11', 'Latitud');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C11', 'Longitud');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D11', 'Velocidad');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E11', 'Angulo');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('F11', 'Tipo');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G11', 'Ubicaci—n');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('H11', 'Incidencia');
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($styleHeader, 'A11:H11');
					$objPHPExcel->setActiveSheetIndex(0)->getStyle("A11:H11")->getFont()->setSize(12);
					$objPHPExcel->setActiveSheetIndex(0)->getStyle("A11:H11")->getFont()->setBold(true);
					
					$rowControlHist=12;
					$zebraControl=0;				
					foreach($aRecorrido as $reporte){
						$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0,  ($rowControlHist), $reporte['FECHA']);
						$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,  ($rowControlHist), $reporte['LATITUD']);
						$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2,  ($rowControlHist), $reporte['LONGITUD']);
						$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(3,  ($rowControlHist), $reporte['VELOCIDAD']);
						$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(4,  ($rowControlHist), $reporte['ANGULO']);
						$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(5,  ($rowControlHist), $reporte['MODO']);
						$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(6,  ($rowControlHist), $reporte['UBICACION']);
						$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(7,  ($rowControlHist), $reporte['INCIDENCIA']);

						if($zebraControl++%2==1){
							$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($stylezebraTable, 'A'.$rowControlHist.':H'.$rowControlHist);			
						}				
						$objPHPExcel->getActiveSheet()->getStyle('A'.$rowControlHist.':C'.$rowControlHist)
								->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);				
						$objPHPExcel->getActiveSheet()->getStyle('H'.$rowControlHist)
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
		
					$objPHPExcel->setActiveSheetIndex(0);
					$filename  = "Viaje_".$dataInfo['ID_VIAJE']."_".date("His_Ymd").".xlsx";
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
			    		
			if(@$this->dataIn['option']=='getReport' && isset($this->dataIn['option'])){	
				$viajes 	 = new My_Model_Viajes();
				$transportista= new My_Model_Transportistas();
				$sucursales	 = new My_Model_Sucursales();				
				$functions   = new My_Controller_Functions();
				
				$fechaInicio = $this->dataIn['inputFechaIn'];
				$fechaFin	 = $this->dataIn['inputFechaFin'];
				$option		 = $this->dataIn['inputOption'];
				$optionValue = $this->dataIn['inputNoTravel']; 
				if($option=='0'){
					$aDataTable  = $viajes->getReportViajes($fechaInicio,$fechaFin);	
				}elseif($option=='1'){
					$aDataTable = $viajes->getDataReport($optionValue);
				}elseif($option=='2'){
					$aClientes  = $sucursales->getFilterSucursales($optionValue,$this->view->dataUser['ID_EMPRESA']);
					$stringData = $functions->arrayToStringDb($aClientes);	
					if($stringData!=""){
						$aDataTable = $viajes->getDataFromCliente($stringData);	
					}
				}elseif($option=='3'){
					$operadores = new My_Model_Operadores();
					$aOperadores= $operadores->getFilterOperadores($optionValue,$this->view->dataUser['ID_EMPRESA']);
					$stringData = $functions->arrayToStringDb($aOperadores);	
					if($stringData!=""){
						$aDataTable = $viajes->getDataFromSucursal($stringData);	
					}
				}

	
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
				
				/**
				 * Detalle del Viaje
				 * */
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A5', 'Id Viaje');
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B5', 'Cliente');
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C5', 'Transportista');
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D5', 'Unidad');
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E5', 'Operador');
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('F5', 'Fecha Inicio');
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G5', 'Fecha Fin');
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('H5', 'Incidencias');
				$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($styleHeader, 'A5:H5');
				$objPHPExcel->setActiveSheetIndex(0)->getStyle("A5:H5")->getFont()->setSize(12);
				$objPHPExcel->setActiveSheetIndex(0)->getStyle("A5:H5")->getFont()->setBold(true);

				$rowControlHist=6;
				$zebraControl=0;				
				foreach($aDataTable as $reporte){
					$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0,  ($rowControlHist), $reporte['CLAVE']);
					$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,  ($rowControlHist), $reporte['CLIENTE']);
					$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2,  ($rowControlHist), $reporte['TRANSPORTISTA']);
					$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(3,  ($rowControlHist), $reporte['ECONOMICO']);
					$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(4,  ($rowControlHist), $reporte['NOMBRE']);
					$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(5,  ($rowControlHist), $reporte['INICIO']);
					$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(6,  ($rowControlHist), $reporte['FIN']);
					$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(7,  ($rowControlHist), $reporte['INCIDENCIAS']);
	
					if($zebraControl++%2==1){
						$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($stylezebraTable, 'A'.$rowControlHist.':H'.$rowControlHist);			
					}				
					$objPHPExcel->getActiveSheet()->getStyle('A'.$rowControlHist.':C'.$rowControlHist)
							->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);				
					$objPHPExcel->getActiveSheet()->getStyle('H'.$rowControlHist)
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
								
				$filename  = "Viajes_".date("HisYmd").".xlsx";
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
}