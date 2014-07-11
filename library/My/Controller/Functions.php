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
}
