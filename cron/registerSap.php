<?php
  error_reporting(E_ALL);
  $conexion = new mysqli('192.168.6.23','dba','t3cnod8A!','SIMA') or die("Some error occurred during connection " . mysqli_error($conexion));

  $pathReporte = "/var/www/reporte_sima/reportes/";
  $sql = "SELECT *
      FROM PROD_CITAS
      WHERE ENVIO_SAP = 2"; 
  $query = mysqli_query($conexion, $sql);
  while($result = mysqli_fetch_array($query)){
    $nameFile = $result['FOLIO'].".pdf";
    file_put_contents($pathReporte.$nameFile, file_get_contents("http://192.168.6.23/reportes/".$nameFile));

    if(file_exists($pathReporte.$nameFile)){
      $bValidOp = setReporteSap($result['FOLIO'],$nameFile);
      if($bValidOp){
        setToReportar($result['ID_CITA']);
        unlink($pathReporte.$nameFile);
      } 
    }
  } 

  function setMarkReporte($idOject){
    global $conexion;
    $result = false;
      $sql ="UPDATE PROD_CITAS 
          SET ENVIO_SAP = 3
        WHERE ID_CITA = $idOject";
    $query  = mysqli_query($conexion, $sql);
    if($query){
      $result= true;
    }
    return $result;   
  }  


function setReporteSap($idFolio,$nameFile){
  	$conexion = odbc_connect("MSSQL", "Siames", "TSMuda1+");
    if ($conexion){
      $fecha = Date("Y-m-d H:i:s");
      $sql = "insert INTO ATC1 (AbsEntry, Line, srcPath, trgtPath, [FileName], FileExt, [Date], UsrID, Copied, [Override]) select MAX (AbsEntry)+1, '1', '\\192.168.6.203\f$\DOCUMENTOS SAP\TSM\ANEXOS', '\\192.168.6.203\f$\DOCUMENTOS SAP\TSM\ANEXOS', '".$idFolio."', 'pdf', '".$fecha.".000', '1', 'Y', 'Y' from OATC;";
      if($qry = odbc_exec($conexion, $sql)){
        $sqlUpdate = "UPDATE OSLT set attachment = '\\192.168.6.203\f$\DOCUMENTOS SAP\TSM\ANEXOS\$nameFile' where SltCode = (select SltCode from OSCL T1 inner join SCL1 T2 on T1.callID = T2.srvcCallID inner join OSLT T3 on T2.solutionID = T3.SltCode where T1.DocNum = '".$idFolio."' )";
        if($queryUpdate = odbc_exec($conexion, $sqlUpdate)){
          $sqlUpdate2 = "update OSLT set AtcEntry = (SELECT MAX(AbsEntry) FROM OATC) where SltCode = (select SltCode from OSCL T1 inner join SCL1 T2 on T1.callID = T2.srvcCallID inner join OSLT T3 on T2.solutionID = T3.SltCode where T1.DocNum = '".$idFolio."')";
          if($queryUpdate2 = odbc_exec($conexion, $sqlUpdate2)){

          }
        }
        odbc_close($conexion);
      }else{
        echo "Fallo el qry: ".$sql;	
      }
    } else {
      echo "No fue posible conectarse a SAP";
    }  
  }

