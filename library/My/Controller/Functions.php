<?php
/**
 * Archivo de definición de clase 
 * 
 * @package library.My.Controller
 * @author andres
 */

/**
 * Definición de clase de controlador genérico
 *
 * @package library.My.Controller
 * @author andres
 */
class My_Controller_Functions
{
    public $aMont=array(
        '',
        'Enero',
        'Febrero',
        'Marzo',
        'Abril',
        'Mayo',
        'Junio',
        'Julio',
        'Agosto',
        'Septiembre',
        'Octubre',
        'Noviembre',
        'Diciembre'
        );
        
    public $optionStatus = Array(
		array("id"=>"1",'name'=>'Activo' ),
		array("id"=>"0",'name'=>'Inactivo' )    
    );    

    public function dateToText($fecha_db){
    	$fecha=explode("-",$fecha_db);
    	$mes_digito= (int) $fecha[1];
    	$fecha_texto=date("d",strtotime($fecha_db))." de $aMont[$mes_digito], ".date("Y ",strtotime($fecha_db))."";
    
    	//Si la fecha tiene horas y minutos
    	if (date("H",strtotime($fecha_db))!="00")
    		$fecha_texto.=" ".date("H:i",strtotime($fecha_db))." hrs.";
    
    	return $fecha_texto;
    }
    
    public function sendMail($data,$config){	
		$headers  = "MIME-Version: 1.0\n";
		$headers .= "Content-type: text/html; charset=iso-8859-1\n";
		$headers .= "X-Priority: 3\n";
		$headers .= "X-Mailer: PHP 5.2\n";
		$headers .= "From: \"".$config->getOption("admin_nombre")."\" <".$config->getOption("admin_email_noreply").">\n";
		$headers .= "Reply-To:".$config->getOption("mailCco")."\n";
		$enviado = mail($data['mail_admin'], $data['subject'], $data['mensaje'], $headers);
		return $enviado;    	
    }
    
    public function cboStatus($option=''){
		$options='';
		for($p=0;$p<count($this->optionStatus);$p++){
			$select='';
			if($this->optionStatus[$p]['id']==@$option){$select='selected';}
			$options .= '<option '.$select.' value="'.$this->optionStatus[$p]['id'].'" >'.$this->optionStatus[$p]['name'].'</option>';
		}
		return $options;
    }
    
	public function cbo_from_array($array,$option=''){
		$options='';
		for($p=0;$p<count($array);$p++){
			$select='';
			if($array[$p][id]==@$option){$select='selected';}
			$options .= '<option '.$select.' value="'.$array[$p]['id'].'" >'.$array[$p]['name'].'</option>';
		}
		return $options;		
	}

	public function cbo_number($n,$option=''){
	  for($i=0; $i<$n; $i++){
		  $h = ($i<=9)?"0".$i:$i;
		  $current = ($h==$option) ? 'selected': '';
		  $select .= '<option '.$current.' value="'.$h.'" >'.$h.'</option>';
		  }
	  return $select;  		    
	}
	
	public function selectDb($dataTable,$option=''){	
		$result='';	
		if(count($dataTable)>0){
			foreach($dataTable as $key => $items){
				$select='';
				if($items['ID'] == @$option){$select='selected';}
				$result .= '<option '.$select.' value="'.$items['ID'].'" >'.$items['NAME'].'</option>';			
			}
		}else{
			$result='no-info';
		}
		return $result;			
	}
	
	public function creationClass($nameClass){
		switch($nameClass) {
		   case "clients":
		       return new My_Model_Clientes();
		   case "units":
		       return new My_Model_Unidades();
		   case "operators":
		       return new My_Model_Operadores();		       
		}		
	}
	
	public function arrayToStringDb($dataTable){
		$result='';
		foreach($dataTable as $key => $items){
			if($items['ID']!="NULL"){
				$result .= ($result!='')? ',':'' ;			
				$result .= $items['ID'];	
			}			
		}
		return $result;
	}
}