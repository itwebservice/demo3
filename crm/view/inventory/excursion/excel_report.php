<?php
include "../../../model/model.php";

/** Error reporting */
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('Europe/London');

if (PHP_SAPI == 'cli')
	die('This example should only be run from a Web Browser');

/** Include PHPExcel */
require_once '../../../classes/PHPExcel-1.8/Classes/PHPExcel.php';

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

$entry_id = $_GET['entry_id'];
$str="select * from excursion_inventory_master where entry_id='$entry_id'";
$query = mysqli_fetch_assoc(mysqlQuery($str));

$sq_city = mysqli_fetch_assoc(mysqlQuery("select city_name from city_master where city_id='$query[city_id]'"));
$sq_exc = mysqli_fetch_assoc(mysqlQuery("select entry_id, excursion_name from excursion_master_tariff where entry_id='$query[exc_id]'"));
            
// Add some data
$objPHPExcel->setActiveSheetIndex(0)
    ->setCellValue('B2', 'Report Name')
    ->setCellValue('C2', 'Activity Inventory')
    ->setCellValue('B3', 'City Name')
    ->setCellValue('C3', $sq_city['city_name'])
    ->setCellValue('B4', 'Activity Name')
    ->setCellValue('C4', $sq_exc['excursion_name']);

$objPHPExcel->getActiveSheet()->getStyle('B2:C2')->applyFromArray($header_style_Array);
$objPHPExcel->getActiveSheet()->getStyle('B2:C2')->applyFromArray($borderArray);    

$objPHPExcel->getActiveSheet()->getStyle('B3:C3')->applyFromArray($header_style_Array);
$objPHPExcel->getActiveSheet()->getStyle('B3:C3')->applyFromArray($borderArray);    

$objPHPExcel->getActiveSheet()->getStyle('B4:C4')->applyFromArray($header_style_Array);
$objPHPExcel->getActiveSheet()->getStyle('B4:C4')->applyFromArray($borderArray);   


$row_count = 6;

$objPHPExcel->setActiveSheetIndex(0)
    ->setCellValue('B'.$row_count, "Sr. No")
    ->setCellValue('C'.$row_count, "Service")
    ->setCellValue('D'.$row_count, "Booking ID")
    ->setCellValue('E'.$row_count, "Activity Datetime")
    ->setCellValue('F'.$row_count, "Customer Name")
    ->setCellValue('G'.$row_count, "Total_tickets");

$objPHPExcel->getActiveSheet()->getStyle('B'.$row_count.':G'.$row_count)->applyFromArray($header_style_Array);
$objPHPExcel->getActiveSheet()->getStyle('B'.$row_count.':G'.$row_count)->applyFromArray($borderArray);    

$row_count++;
$count = 0;
$str="select * from excursion_inventory_master where entry_id='$entry_id'";
$query = mysqlQuery($str);
while($row_ser = mysqli_fetch_assoc($query)){
    $sql_temp=mysqlQuery("select * from excursion_master_entries where city_id='$row_ser[city_id]' and exc_name='$row_ser[exc_id]' and status!='Cancel' and (exc_date between '$row_ser[valid_from_date]' and '$row_ser[valid_to_date]')");
    while($sql = mysqli_fetch_assoc($sql_temp)){
        $check_in=$sql['exc_date'];
        $str1="select * from excursion_master where exc_id=$sql[exc_id]";
        $sql_cust=mysqli_fetch_assoc(mysqlQuery($str1));
        $date = $sql_cust['created_at'];
        $yr = explode("-", $date);
        $year = $yr[0];
        $sql_Cust_details=mysqli_fetch_assoc(mysqlQuery("select * from customer_master where customer_id=$sql_cust[customer_id]"));
        if($sql_Cust_details['type']=='Corporate'||$sql_Cust_details['type'] == 'B2B'){
            $customer_name = $sql_Cust_details['company_name'];
        }else{
            $customer_name = $sql_Cust_details['first_name'].' '.$sql_Cust_details['last_name'];
        }

	$objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('B'.$row_count, ++$count)
        ->setCellValue('C'.$row_count, 'Activity')
        ->setCellValue('D'.$row_count, get_exc_booking_id($sql_cust['exc_id'],$year))
        ->setCellValue('E'.$row_count, date("d-m-Y H:i", strtotime($check_in)))
        ->setCellValue('F'.$row_count, $customer_name)
        ->setCellValue('G'.$row_count, $sql['total_adult']+$sql['total_child']);

    $objPHPExcel->getActiveSheet()->getStyle('B'.$row_count.':G'.$row_count)->applyFromArray($content_style_Array);
    $objPHPExcel->getActiveSheet()->getStyle('B'.$row_count.':G'.$row_count)->applyFromArray($borderArray);    
    $row_count++;
}
    //Package Booking
    $sq_hotel_c1 = mysqlQuery("select * from package_tour_excursion_master where city_id= '$row_ser[city_id]' and exc_id = '$row_ser[exc_id]' and booking_id in(select booking_id from package_tour_booking_master where tour_from_date between '$row_ser[valid_from_date]' and '$row_ser[valid_to_date]')");

    while($row_hotel_c1= mysqli_fetch_assoc($sq_hotel_c1)){
        $check_in=$sql['exc_date'];
        $str1="select * from package_tour_booking_master where booking_id='$row_hotel_c1[booking_id]'";
        $sql_cust=mysqli_fetch_assoc(mysqlQuery($str1));
        $date = $sql_cust['booking_date'];
        $yr = explode("-", $date);
        $year = $yr[0];
        $sql_Cust_details=mysqli_fetch_assoc(mysqlQuery("select * from customer_master where customer_id=$sql_cust[customer_id]"));
		if($sql_Cust_details['type']=='Corporate'||$sql_Cust_details['type'] == 'B2B'){
			$customer_name = $sql_Cust_details['company_name'];
		}else{
			$customer_name = $sql_Cust_details['first_name'].' '.$sql_Cust_details['last_name'];
		}
        $cancel_est = mysqli_num_rows(mysqlQuery("select * from package_refund_traveler_estimate where booking_id='$row_hotel_c1[booking_id]'"));
        $pax = 0;
        if($cancel_est == 0){
            $pax = $row_hotel_c1['adult'] + $row_hotel_c1['chwb'] + $row_hotel_c1['chwob'] + $row_hotel_c1['infant'];;
        }
        if($pax != 0){
            $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('B'.$row_count, ++$count)
            ->setCellValue('C'.$row_count, 'Package')
            ->setCellValue('D'.$row_count, get_package_booking_id($row_hotel_c1['booking_id'],$year))
            ->setCellValue('E'.$row_count, 'NA')
            ->setCellValue('F'.$row_count, $customer_name)
            ->setCellValue('G'.$row_count, $pax);

            $objPHPExcel->getActiveSheet()->getStyle('B'.$row_count.':G'.$row_count)->applyFromArray($content_style_Array);
            $objPHPExcel->getActiveSheet()->getStyle('B'.$row_count.':G'.$row_count)->applyFromArray($borderArray);  
            $row_count++;
        }
    }

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
header('Content-Disposition: attachment;filename="Activity Inventory('.date('d-m-Y H:i').').xls"');
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header ('Last-Modified: '.gmdate('D, d M Y H:i').' GMT'); // always modified
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
exit;
