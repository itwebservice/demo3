<?php
include "../../../../../model/model.php";

/** Error reporting */
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('Europe/London');

if (PHP_SAPI == 'cli')
	die('This example should only be run from a Web Browser');

/** Include PHPExcel */
require_once '../../../../../classes/PHPExcel-1.8/Classes/PHPExcel.php';

//This function generates the background color
function cellColor($cells,$color){
    global $objPHPExcel;

    $objPHPExcel->getActiveSheet()->getStyle($cells)->getFill()->applyFromArray(array(
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'startcolor' => array(
             'rgb' => $color
        )
    ));
}

//This array sets the font atrributes
$header_style_Array = array(
    'font'  => array(
        'bold'  => true,
        'color' => array('rgb' => '000000'),
        'size'  => 12,
        'name'  => 'Verdana'
    ));
$table_header_style_Array = array(
    'font'  => array(
        'bold'  => false,
        'color' => array('rgb' => '000000'),
        'size'  => 11,
        'name'  => 'Verdana'
    ));
$content_style_Array = array(
    'font'  => array(
        'bold'  => false,
        'color' => array('rgb' => '000000'),
        'size'  => 9,
        'name'  => 'Verdana'
    ));

//This is border array
$borderArray = array(
    'borders' => array(
        'allborders' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN
        )
    )
);

// Create new PHPExcel object
$objPHPExcel = new PHPExcel();
// Set document properties
$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
    ->setLastModifiedBy("Maarten Balliauw")
    ->setTitle("Office 2007 XLSX Test Document")
    ->setSubject("Office 2007 XLSX Test Document")
    ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
    ->setKeywords("office 2007 openxml php")
    ->setCategory("Test result file");

//////////////////////////****************Content start**************////////////////////////////////

$ticket_id = $_GET['ticket_id'];
$from_date = $_GET['payment_from_date'];
$to_date = $_GET['payment_to_date'];
if($ticket_id!=""){
    $sq_entry_date = mysqli_fetch_assoc(mysqlQuery("select * from ticket_master where ticket_id='$ticket_id'"));
    $date = $sq_entry_date['created_at'];
    $yr = explode("-", $date);
    $year =$yr[0];
    $invoice_id = get_ticket_booking_id($ticket_id,$year);
}

//$invoice_id = ($ticket_id!="") ? get_ticket_booking_id($ticket_id): "";

if($from_date!="" && $to_date!=""){
    $date_str = $from_date.' to '.$to_date;
}
else{
    $date_str = "";
}

// Add some data
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('B2', 'Report Name')
            ->setCellValue('C2', 'Flight Ticket Booking Refund')
            ->setCellValue('B3', 'Booking ID')
            ->setCellValue('C3', $invoice_id)
            ->setCellValue('B4', 'From-To-Date')
            ->setCellValue('C4', $date_str);
             
$objPHPExcel->getActiveSheet()->getStyle('B2:C2')->applyFromArray($header_style_Array);
$objPHPExcel->getActiveSheet()->getStyle('B2:C2')->applyFromArray($borderArray);    

$objPHPExcel->getActiveSheet()->getStyle('B3:C3')->applyFromArray($header_style_Array);
$objPHPExcel->getActiveSheet()->getStyle('B3:C3')->applyFromArray($borderArray);    

$objPHPExcel->getActiveSheet()->getStyle('B4:C4')->applyFromArray($header_style_Array);
$objPHPExcel->getActiveSheet()->getStyle('B4:C4')->applyFromArray($borderArray);   

$query = "select * from ticket_refund_master where 1 ";
if($ticket_id!=""){
    $query .=" and ticket_id='$ticket_id'";
}
if($from_date!='' && $to_date!=''){
    $from_date = get_date_db($from_date);
    $to_date = get_date_db($to_date);
    $query .=" and refund_date between '$from_date' and '$to_date'";
}
$total_refund = 0;
$count = $total_refund =  $sq_pending_amount = $sq_cancel_amount = 0;
$sq_refund = mysqlQuery($query);
$row_count = 6;
$objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('B'.$row_count, "Sr. No")
        ->setCellValue('C'.$row_count, "Booking ID")
        ->setCellValue('D'.$row_count, "Passenger_name")
        ->setCellValue('E'.$row_count, "Refund_ID")
        ->setCellValue('F'.$row_count, "Refund Date")
        ->setCellValue('G'.$row_count, "Mode")
        ->setCellValue('H'.$row_count, "Bank Name")
        ->setCellValue('I'.$row_count, "Cheque No/ID")
        ->setCellValue('J'.$row_count, "Refund Amount");

$objPHPExcel->getActiveSheet()->getStyle('B'.$row_count.':J'.$row_count)->applyFromArray($header_style_Array);
$objPHPExcel->getActiveSheet()->getStyle('B'.$row_count.':J'.$row_count)->applyFromArray($borderArray);    

$row_count++;
while($row_refund = mysqli_fetch_assoc($sq_refund)){

        $traveler_name = "";
        $sq_refund_entries = mysqlQuery("select * from ticket_refund_entries where refund_id='$row_refund[refund_id]'");
        while($row_refund_entry = mysqli_fetch_assoc($sq_refund_entries)){
            $sq_entry_info = mysqli_fetch_assoc(mysqlQuery("select * from ticket_master_entries where entry_id='$row_refund_entry[entry_id]'"));
            $traveler_name .= $sq_entry_info['first_name'].' '.$sq_entry_info['last_name'].', ';
            $sq_entry_date = mysqli_fetch_assoc(mysqlQuery("select * from ticket_master where ticket_id='$sq_entry_info[ticket_id]'"));
            $date = $sq_entry_date['created_at'];
            $yr = explode("-", $date);
            $year =$yr[0];
        }
        $date = $row_refund['refund_date'];
        $yr1 = explode("-", $date);
        $year1 = $yr1[0];
        $traveler_name = trim($traveler_name, ", ");

        $total_refund = $total_refund+$row_refund['refund_amount'];
        if($row_refund['clearance_status']=="Pending"){ 
            $bg='warning';
            $sq_pending_amount = $sq_pending_amount + $row_refund['refund_amount'];
        }
        else if($row_refund['clearance_status']=="Cancelled"){ 
            $bg='danger';
            $sq_cancel_amount = $sq_cancel_amount + $row_refund['refund_amount'];
        }
        else{
            $bg='';
        }

	$objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('B'.$row_count, ++$count)
        ->setCellValue('C'.$row_count, get_ticket_booking_id($row_refund['ticket_id'],$year))
        ->setCellValue('D'.$row_count, $traveler_name)
        ->setCellValue('E'.$row_count, get_ticket_booking_refund_id($row_refund['refund_id'],$year1))
        ->setCellValue('F'.$row_count, date('d-m-Y', strtotime($row_refund['refund_date'])))
        ->setCellValue('G'.$row_count, $row_refund['refund_mode'])
        ->setCellValue('H'.$row_count, $row_refund['bank_name'])
        ->setCellValue('I'.$row_count, $row_refund['transaction_id'])
        ->setCellValue('J'.$row_count,  $row_refund['refund_amount']);

  $objPHPExcel->getActiveSheet()->getStyle('B'.$row_count.':J'.$row_count)->applyFromArray($content_style_Array);
	$objPHPExcel->getActiveSheet()->getStyle('B'.$row_count.':J'.$row_count)->applyFromArray($borderArray);    

		$row_count++;

        $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('B'.$row_count, "")
        ->setCellValue('C'.$row_count, "")
        ->setCellValue('D'.$row_count, "")
        ->setCellValue('E'.$row_count, "")
        ->setCellValue('F'.$row_count, "")
        ->setCellValue('G'.$row_count, 'Refund : '.$total_refund)
        ->setCellValue('H'.$row_count, 'Pending : '.$sq_pending_amount)
        ->setCellValue('I'.$row_count, 'Cancelled : '.$sq_cancel_amount)
        ->setCellValue('J'.$row_count, 'Total Refund : '.($total_refund - $sq_pending_amount - $sq_cancel_amount));

$objPHPExcel->getActiveSheet()->getStyle('B'.$row_count.':J'.$row_count)->applyFromArray($header_style_Array);
$objPHPExcel->getActiveSheet()->getStyle('B'.$row_count.':J'.$row_count)->applyFromArray($borderArray);
}

//////////////////////////****************Content End**************////////////////////////////////
	

// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Simple');


for($col = 'A'; $col !== 'N'; $col++) {
    $objPHPExcel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
}


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


// Redirect output to a client’s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="Flight Ticket Booking Refund('.date('d-m-Y H:i:s').').xls"');
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
exit;
