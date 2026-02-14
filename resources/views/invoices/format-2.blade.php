<?php
$request_type = '';
if(@$_REQUEST['print_type']) {
    $request_type = $_REQUEST['print_type'];
}
$print_type = ($request_type != 'print') ? 'pdf' : 'print';

if($print_type == 'pdf') {
    require('../../../lib/settings.php');
}
ini_set('memory_limit', '2048M');
header("Access-Control-Allow-Origin: *");

$units = get_option("erp_units");
$units[0]= '';
$settings = $_SESSION['ERP_SETTINGS'];
	$item_settings = (array)unserialize($settings['sales_print']);
	$header_repeat = (int)$item_settings['header_repeat'];
	$border_ref =  0;
if($print_type == 'pdf'){
    header("content-type:application/json");
  
    require('../../../classes/upload.php');
    require '../../../classes/phpoffice/vendor/autoload.php';
    require(root.'site/auth/validate.php');

    $vendorDirPath = realpath("../../../classes/mpdf" . '/vendor');
    if (file_exists($vendorDirPath . '/autoload.php')) {
        require $vendorDirPath . '/autoload.php';
    } else {
        throw new Exception(
            die("somthing went wrong")
        );
    }

	$pdf_on_letterhead = (int)$_REQUEST['letterhead'];

	$settings = $_SESSION['ERP_SETTINGS'];
	
	$item_settings = (array)unserialize($settings['sales_print']);
	$header_repeat = (int)$item_settings['header_repeat'];
	$custom_format = (int)$item_settings['custom_format'];
	$custom_format_width = (int)$item_settings['custom_format_width'];
	$custom_format_length = (int)$item_settings['custom_format_length'];
	$custom_format_margin = (int)$item_settings['custom_format_margin'];
	$hide_address_on_lh = (int)$item_settings['hide_address_on_lh'];
	$format = 'A4';
	$margin_left_right ='';
	$margin_top_on_pdf = (float)$item_settings['margin_top_on_pdf'];
	$margin_bottom_on_pdf = (float)$item_settings['margin_bottom_on_pdf'];
	$margin_footer_on_pdf = (float)$item_settings['margin_footer_on_pdf'];

	if($pdf_on_letterhead){
		$margin_top_on_pdf = (float)$item_settings['margin_top_on_pdf_lh'];
		$margin_bottom_on_pdf = (float)$item_settings['margin_bottom_on_pdf_lh'];
		$margin_footer_on_pdf = (float)$item_settings['margin_footer_on_pdf_lh'];
	}

	if($custom_format) $margin_left_right = '';//' <style> @page{ magrin-left: '.$custom_format_margin.'mm; margin-right:'.$custom_format_margin.'mm;} </style> ' ; // mm
// if($header_repeat) {
	$border_ref = 1;
	
//     $mpdf = new \Mpdf\Mpdf([
//         'mode' => 'utf-8',
//         'format' => ($custom_format)  ? [$custom_format_width , $custom_format_length ] : 'A4',
//         'margin_top' => 0,
//         'margin_header' => 0,
// 		'margin_left' => 8,
// 		'margin_right' => 8,
// 		'margin_header' => 8,
// 		'margin_footer' => 8,
// 		'orientation'=> 'P',
// 		'setAutoTopMargin' => 'stretch',
// 		'setAutoBottomMargin' => 'stretch',
// 		'autoMarginPadding' => 0,
//         'pagenumPrefix' => 'Page :  ',
//         'pagenumSuffix' => ' - ',
//         'nbpgPrefix' => ' out of ',
//         'nbpgSuffix' => ' pages',
//         'default_font' => 'arial',
// 		'margin_bottom' => ($margin_bottom_on_pdf ) ? $margin_bottom_on_pdf  : 5,
//         // 'default_font_size'=>15
//     ]);
// }else{
// 	$border_ref = 1;
//     $mpdf = new \Mpdf\Mpdf([
//         'mode' => 'utf-8',
//         'format' => ($custom_format)  ? [$custom_format_width , $custom_format_length ] : 'A4',
//         'margin_top' => 0,
//         'margin_header' => 0,
// 		'margin_left' => 8,
// 		'margin_right' => 8,
// 		'margin_header' => 8,
// 		'margin_footer' => 8,
// 		'orientation'=> 'P',
// 		'setAutoTopMargin' => 'stretch',
// 		'setAutoBottomMargin' => 'stretch',
// 		'autoMarginPadding' => 0,
//         'pagenumPrefix' => 'Page :  ',
//         'pagenumSuffix' => ' - ',
//         'nbpgPrefix' => ' out of ',
//         'nbpgSuffix' => ' pages',
//         'default_font' => 'arial',
// 		'orientation'=> 'P',
// 		'margin_bottom' => ($margin_bottom_on_pdf ) ? $margin_bottom_on_pdf  : 5,
        // 'default_font_size'=>15
    // ]);
// }

$mpdf = new \Mpdf\Mpdf([
	'mode' => 'utf-8',
	'format' => ($custom_format)  ? [$custom_format_width , $custom_format_length ] : 'A4',
	'setAutoTopMargin' => 'stretch',
	'setAutoBottomMargin' => 'stretch',
	'autoMarginPadding' => 0,
	'margin_top' => ($margin_top_on_pdf ) ? $margin_top_on_pdf  : 0,
	'margin_bottom' => ($margin_bottom_on_pdf ) ? $margin_bottom_on_pdf  : 0,
	'margin_left' => 8,
	'margin_right' => 8,
	'margin_header' => 8,
	'margin_footer' => ($margin_footer_on_pdf ) ? $margin_footer_on_pdf  : 8,
	'pagenumPrefix' => 'Page :  ',
        'pagenumSuffix' => ' - ',
        'nbpgPrefix' => ' out of ',
        'nbpgSuffix' => ' pages',
]);
$mpdf->SetDefaultFontSize(9);
$mpdf->SetDefaultFont('Calibri');
// $filter_param = json_decode(base64_decode($_REQUEST['jsondata']),true);
$guid = $_REQUEST['id'];
$profiles_only= (int)$_REQUEST['profiles_only'];

// $typo = $filter_param['typo'];
}else{
	// $typo = (int)$_REQUEST['typo'];
	$guid = (int)$_REQUEST['id'];
	?>
	<script type="text/javascript">
		$(document).ready(function(){
			setTimeout( ()=>  window.print(), 2000);
		})
	</script>
	<?php 
}
// $pdf_on_letterhead = 0;
// if($typo == 1){
// 	$pdf_on_letterhead = 1;	
// }

$transaction_ID = (int)$guid;

$data = findQuery("SELECT 
						  r.* 
						  from 
						  erp_sales_sales r 
						  where r.id = $guid
					");

$items = findQuery("SELECT 
						  i.*,
						  i.id as itemID,
						  p.name, p.code  , p.product_guid 
						  from 
						  erp_sales_sales_items i 
						  INNER JOIN 
						  erp_compound_items p 
						  ON p.id = i.compound_guid
						  where i.order_guid = $guid
					");

if(!$data){
	$json['errormsg'] = 'Invalid Data .';
	echo json_encode($json);
}

$vat_cols="display:table-cell!important;";
$vat_rows="display:revert;";
$colspan1=10;
$colspan2=4;
if($data[0]['is_customer_taxable']==1 && $data[0]['purchase_type']==1)
{ //$vat_cols="display:table-cell!important;";
}
else{$vat_cols="display:none;";$vat_rows="display:none;"; $colspan1=3;$colspan2=4;}
if($print_type == 'print'){
	$colspan1=10;
	$colspan2=4;
}


function showDepartmentRow($items){
	$show_department = false;
	foreach($items as $item){
		if($item['department']){
			$show_department =true;
			break;
		}
	}
	return $show_department;
}
$show_department_row = showDepartmentRow($items);
$department_TH = '';

$settlements  = findQuery("SELECT * from erp_sales_settlement where sales_guid = $guid");

$termsArr = array();
if($data[0]['terms']){
	$termsArr = json_decode(base64_decode($data[0]['terms']),true);
	// exit;
}
if($_SESSION['COMPANY_SETTINGS']['e_invoice'] == 1){
	include "../../../zatca/vendor/autoload.php";
}
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
if($_SESSION['COMPANY_SETTINGS']['e_invoice'] == 1){
	$writer = new PngWriter();
	if($data[0]['qr_code'] != null){
		$qrCode = new QrCode(
			data: $data[0]['qr_code'],
			encoding: new Encoding('UTF-8'),
			errorCorrectionLevel: ErrorCorrectionLevel::Low,
			size: 300,
			margin: 10,
			roundBlockSizeMode: RoundBlockSizeMode::Margin,
			foregroundColor: new Color(0, 0, 0),
			backgroundColor: new Color(255, 255, 255)
		);


		$result = $writer->write($qrCode);

		$qr_code = $result->getDataUri();
	}
}

$ref_id = $data[0]['sales_ref'];
$ref_no = '';
$voucher_guid = 0;
$invHeading = 'TAX INVOICE';
if($ref_id) {
    $ref_no = rowvalue($ref_id, 'erp_sales_sales_master', 'result');
    $voucher_guid = rowvalue($ref_id, 'erp_sales_sales_master', 'voucher_id');
}
$user_ref = $data[0]['user_ref'];
$is_branch = (int)$data[0]['is_branch'];
$sales_date = date('d.m.Y',strtotime($data[0]['date_added']));
$payment_terms = $data[0]['payment_terms'];
$customer_ref = $data[0]['user_ref'];
$salesman_guid = $data[0]['salesman_guid'];
$salesman = '';
if($salesman_guid) $salesman = rowvalue($salesman_guid,'erp_clinic_employee','name');
$currency = rowvalue(1,'erp_company','symbol');

if($data[0]['salesorder_ref_manual'] != ''){
	$SO_Ref = $data[0]['salesorder_ref_manual'];
} else {
	$SO_Ref = array();
	$SO_Ref_Arr = explode(',',$data[0]['salesorder_ref']);
	foreach($SO_Ref_Arr as $SOs){
		if($SOs)
			$SO_Ref[] = rowvalue($SOs,'erp_salesorder_master','result'); 
	}
	$SO_Ref = implode(', ',$SO_Ref);
}

if($data[0]['dn_ref_manual'] != ''){
	$DN_Ref = $data[0]['dn_ref_manual'];
} else {
	$DN_Ref = array();
	$DN_Ref_Arr = explode(',',$data[0]['dn_ref']);
	foreach($DN_Ref_Arr as $DNs){
		if($DNs)
			$DN_Ref[] = rowvalue($DNs,'erp_deliverynote_master','result'); 
	}
	$DN_Ref = implode(', ',$DN_Ref);
}

/*$SO_Ref = array();
$DN_Ref = array();
$SO_Ref_Arr = explode(',',$data[0]['salesorder_ref']);
$DN_Ref_Arr = explode(',',$data[0]['dn_ref']);
foreach($SO_Ref_Arr as $SOs){
	if($SOs)
		$SO_Ref[] = rowvalue($SOs,'erp_salesorder_master','result'); 
}
foreach($DN_Ref_Arr as $DNs){
	if($DNs)
		$DN_Ref[] = rowvalue($DNs,'erp_deliverynote_master','result'); 
}
$SO_Ref = implode(', ',$SO_Ref);
$DN_Ref = implode(', ',$DN_Ref);*/
// $vendor_guid = $data[0]['vendor_guid'];
// $vendorData = findQuery("SELECT * from erp_customer where id=$vendor_guid");
// $name = $vendorData[0]['name'];
// $code = $vendorData[0]['code'];
// $address = $vendorData[0]['address'];
// $country = $vendorData[0]['country'];
// $phone = $vendorData[0]['phone'];

$compData = findQuery("SELECT * from erp_company  order by id desc LIMIT 1");
$comp_name = $compData[0]['name'];
$comp_address = $compData[0]['address'];
$comp_country = $compData[0]['countryName'];
$comp_email = $compData[0]['email'];
$comp_phone = $compData[0]['phone'];
$comp_mobile = $compData[0]['mobile'];
$comp_location = $compData[0]['location'];
$comp_vat = $compData[0]['vat_no'];

$customer_id = $data[0]['customer_id'];
$customerData = findQuery("SELECT * from erp_customer where id=$customer_id");
$cust_name = $customerData[0]['name'];
$cust_code = $customerData[0]['code'];
$cust_address = $customerData[0]['address1'];
if($customerData[0]['address2']) $cust_address .= '<br>'.$customerData[0]['address2'];
if($customerData[0]['address3']) $cust_address .= '<br>'.$customerData[0]['address3'];
$cust_city = $customerData[0]['city'];
$cust_phone = $customerData[0]['phone'];
$cust_trn = $customerData[0]['vat_no'];
$cust_country_guid = $customerData[0]['country_guid'];
$cust_country = '';

if($cust_country_guid)
	$cust_country = rowvalue($cust_country_guid,'erp_country','name');

	$cust_phone_tr = '<tr><td>&nbsp;</td><td>&nbsp;</td></tr>';
	$cust_trn_tr = '<tr><td>&nbsp;</td><td>&nbsp;</td></tr>';
if($cust_phone) {
    $cust_phone_tr = '<tr >
					<td style="border:none;padding-top: 0;
					padding-bottom: 0;" class="font-size-11">
						Phone No. :  '.$cust_phone.'
					</td>
				</tr>';
}
if($cust_trn) {
    $cust_trn_tr = '<tr >
				<td style="border:none;padding-top: 0;
				padding-bottom: 0;" class="font-size-11">
					VAT No. :  '.$cust_trn.'
				</td>
			</tr>';
}

function concat($values = array())
{
    $newString = '';
    for ($i = 0; $i < count($values); $i++) {
        //If current value is not null or empty, display it
        if (!empty($values[$i]))
            $newString .= $values[$i];
        //If current value is not null AND if it is not the last value
        if (!empty($values[$i]) && $i < count($values) - 1)
            $newString .=  ', ';
    }
    return $newString;
}

$cust_full_address = concat(array($cust_address, $cust_city,$cust_country));
if(empty($cust_full_address)) $cust_full_address .= '';
else if(empty($cust_address)) $cust_full_address .= '<p style="margin:0"></p>';

if($data[0]['customer_branch']){
    $branchData = findOne("SELECT * from contact_details where id=".$data[0]['customer_branch']);
	$cust_name = $branchData['name'];

	$cust_full_address = concat(array($branchData['address1'], $branchData['address2'], $branchData['address3']));
	$cust_phone = $branchData['telephone'];
	if($cust_phone) {
		$cust_phone_tr = '<tr >
						<td style="border:none;padding-top: 0;
						padding-bottom: 0;" class="font-size-11">
							Phone No. :  '.$cust_phone.'
						</td>
					</tr>';
	}
}

$deliver_to = (int)$data[0]['deliver_to'];
$deliver_cust_name = $deliver_cust_phone = $deliver_cust_address = '';
$show_customer_delivery_box = false;
$deliver_TO = '';
$deliver_TO_PHONE ='';
if ($deliver_to > 0) {
    $DeliverCustomerData = findQuery("SELECT * from contact_details where id=$deliver_to");
	if ($DeliverCustomerData) {
		$show_customer_delivery_box = true;
		$deliver_cust_name = $DeliverCustomerData[0]['name'];
		// $deliver_cust_code = $DeliverCustomerData[0]['code'];
		$deliver_cust_address1 = $DeliverCustomerData[0]['address1'];
		// if ($DeliverCustomerData[0]['address2']) {
			$deliver_cust_address2 = $DeliverCustomerData[0]['address2'];
		// }
		// if ($DeliverCustomerData[0]['address3']) {
			$deliver_cust_address3  = $DeliverCustomerData[0]['address3'];
		// }
		$deliver_cust_address = concat(array($deliver_cust_address1,$deliver_cust_address2,$deliver_cust_address3));
		if($deliver_cust_address == ''){
			$deliver_TO = '';
		} 
		else{
			$deliver_TO = '<tr >
							<td style="border:none;padding-top: 0;
							" class="font-size-11">
								'.$deliver_cust_address.'
							</td>
						</tr>';
		}
		$deliver_cust_phone = $DeliverCustomerData[0]['mobile'];
		if($deliver_cust_phone == ''){
			$deliver_TO_PHONE ='';
		}	else{
			$deliver_TO_PHONE ='<tr >
										<td style="border:none;padding-top: 0;
										" class="font-size-11">
											Phone No. :  '.$deliver_cust_phone.'
										</td>
									</tr>
									';
		}
	}
}
$pterms = array();
$pterms[1] = 'Payment in advance';
$pterms[2] = 'Payment seven days after invoice date';
$pterms[3] = 'Payment ten days after invoice date';
$pterms[4] = 'Payment 30 days after invoice date';
$pterms[5] = 'End of month';
$pterms[6] = 'Cash on delivery';

$logourl = 'pdf-head-logo.png';
if(file_exists(upload_folder.'logo/company/logo.png')){
	$logourl = siteurl.'upload/logo/company/logo.png';
}

$signature = '';
if(file_exists(upload_folder.'signature/company/signature.png')){
	$signatureurl = siteurl.'upload/signature/company/signature.png';

	$print_signature_width = $compData[0]['print_signature_width'] != '' ? $compData[0]['print_signature_width'].'px' : '100%';
	$print_signature_height = $compData[0]['print_signature_height'] != '' ? $compData[0]['print_signature_height'].'px' : '80px';
	$print_signature_position = $compData[0]['print_signature_position'] != '' ? $compData[0]['print_signature_position'] : 'center';

	$signature = "<img src='$signatureurl' style='width:$print_signature_width; height:$print_signature_height' />";
}
$print_logo_width = $compData[0]['print_logo_width'] != '' ? $compData[0]['print_logo_width'].'px' : '100%';
$print_logo_height = $compData[0]['print_logo_height'] != '' ? $compData[0]['print_logo_height'].'px' : '80px';
$print_logo_position = $compData[0]['print_logo_position'] != '' ? $compData[0]['print_logo_position'] : 'center';

$logo_on_condition = '<div class="col-xs-3" style="text-align:center;vertical-align:middle">
						<table width="100%" style="height:80px">
							<tr>
								<td style="text-align:'.$print_logo_position.';">
									<img src="'.$logourl.'" style="width:'.$print_logo_width.'; height:'.$print_logo_height.'" />
								</td>
							</tr>
						</table>
					</div>';
if($pdf_on_letterhead == 1){
	$height_letter = 'height:110px';
	if($print_type == 'print') $height_letter ='';
	$logo_on_condition = '<table><tr><td style="'.$height_letter.'"></td></tr></table>';

}
$transaction_setting_guid = (int)$voucher_guid;
if ($transaction_setting_guid) {
    $invHeading = rowvalue($transaction_setting_guid, 'erp_transaction_settings', 'print_name');
	$is_coating_voucher = rowvalue($transaction_setting_guid, 'erp_transaction_settings', 'coating_voucher');
}
$hide_vat = '';
if($is_branch){
	$hide_vat = ' .no-vat-colom{ display:none;} ';
}



$styles = '
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin> 
	<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500&display=swap" rel="stylesheet"> 
	<style>
		body { 
				font-family: "Roboto", sans-serif;
				font-weight:400
		}
	.row {
		margin: 10px 0px 0px 0px !important;
		padding: 0px !important;
	}

	.col-xs-1, .col-sm-1, .col-md-1, .col-lg-1, .col-xs-2, .col-sm-2, .col-md-2, .col-lg-2, .col-xs-3, .col-sm-3, .col-md-3, .col-lg-3, .col-xs-4, .col-sm-4, .col-md-4, .col-lg-4, .col-xs-5, .col-sm-5, .col-md-5, .col-lg-5, .col-xs-6, .col-sm-6, .col-md-6, .col-lg-6, .col-xs-7, .col-sm-7, .col-md-7, .col-lg-7, .col-xs-8, .col-sm-8, .col-md-8, .col-lg-8, .col-xs-9, .col-sm-9, .col-md-9, .col-lg-9, .col-xs-10, .col-sm-10, .col-md-10, .col-lg-10, .col-xs-11, .col-sm-11, .col-md-11, .col-lg-11, .col-xs-12, .col-sm-12, .col-md-12, .col-lg-12 
	{
		border:0;
		padding:0;
		margin-left:-0.00001;
	}

	.col-xs-1, .col-xs-2, .col-xs-3, .col-xs-4, .col-xs-5, .col-xs-6, .col-xs-7, .col-xs-8, .col-xs-9, .col-xs-10, .col-xs-11, .col-xs-12 
	{
		float: left;
	}

	.col-xs-12 {
		width: 100%;
	}

	.col-xs-11 {
		width: 91.66666667%;
	}

	.col-xs-10 {
		width: 83.33333333%;
	}

	.col-xs-9 {
		width: 75%;
	}

	.col-xs-8 {
		width: 66.66666667%;
	}

	.col-xs-7 {
		width: 58.33333333%;
	}

	.col-xs-6 {
		width: 50%;
	}

	.col-xs-5 {
		width: 41.66666667%;
	}

	.col-xs-4 {
		width: 33.33333333%;
	}

	.col-xs-3 {
		width: 25%;
	}

	.col-xs-2 {
		width: 16.66666667%;
	}
	.col-xs-1 {
		width: 8.33333333%;
	}

	.col-xs-pull-12 {
		right: 100%;
	}

	.col-xs-pull-11 {
		right: 91.66666667%;
	}

	.col-xs-pull-10 {
		right: 83.33333333%;
	}

	.col-xs-pull-9 {
		right: 75%;
	}

	.col-xs-pull-8 {
		right: 66.66666667%;
	}

	.col-xs-pull-7 {
		right: 58.33333333%;
	}

	.col-xs-pull-6 {
		right: 50%;
	}

	.col-xs-pull-5 {
		right: 41.66666667%;
	}

	.col-xs-pull-4 {
		right: 33.33333333%;
	}

	.col-xs-pull-3 {
		right: 25%;
	}

	.col-xs-pull-2 {
		right: 16.66666667%;
	}

	.col-xs-pull-1 {
		right: 8.33333333%;
	}

	.border-bottom{
		border-left:none;
		border-right:none;
		border-top:none;
		
	}
	.left{
		text-align:left;
	}
	.font-size-11{
		font-size:11px;
	}
	.font-size-13{
		font-size:11px;
	}
	.font-size-15{
		font-size:11px;
	}
	#item-table th{
		padding:3px;
		font-size:10px;
		text-align:center;
		font-weight:500;
	}
	#item-table {
		margin-top:5px;
	}
	#item-table td{

			border-bottom:1px solid darkgray;
		
		font-size: 10px;
		font-weight: 500;
		padding: 6px;
		border-top: none;
		vertical-align: top;
	}
	#item-table th{

		border-bottom:1px solid black;
		padding: 6px 12px;
	}
	#inner-item-table td,#settlement td{
		font-size: 10px;
		padding: 5px;
		border-bottom: 1px solid black;
	}
	.settle-box{
		width:100%;;height:120px;text-align:right;
	}
	#settlement{
		
	}
	#word-amt-table td{
		font-size:10px!important;
	}
	h5{
		font-size:10px;
	}

	'.$hide_vat.'
</style>';
//width: 300px;border-collapse:collapse;margin-left: 55%;
if($print_type =='print'){
	$vh_height = "26vh";
	$settlement_set = (int)$item_settings['settlement_set'];
	if($settlement_set) {
		// if (count($settlements) > 0) $vh_height = "25vh";
		if(count($settlements) == 1) $vh_height = "24vh";
		if(count($settlements)== 2) $vh_height = "22vh";
		if(count($settlements)== 3) $vh_height = "20vh";
	} 
	$styles .= '<style>
	h5{
		font-size:18px;
		font-weight:bold;
		margin:2px 0;
	}
	#word-amt-table td{
		font-size:16px;
		padding-bottom:4px;
	}
	#item-table td{
		border-bottom:none!important;
		font-size: 15px!important;
		padding:5px;
		
	}
	#reff-table-cs{
		border: 1px solid;
	}
	#print-padding{
		padding-left:2%;
	}
	#refrence-top-table{
		margin-bottom: 10px;
	}
	#inner-item-table td,#settlement td{
		font-size: 15px;
		padding: 5px;
		
	}
	#item-table {
		height:'.$vh_height.';
	}
	.font-size-13{
		font-size:16px;
	}
	.font-size-15{
		font-size:18px;
	}
	.font-size-11 {
		font-size: 15px;
	}
	#settlement{
		
	}
	</style>';
}else{
	$style .= $margin_left_right;
}
//width: 34%;border-collapse:collapse;margin-left: 56%;
$logo = $_SESSION['ERP_SETTINGS']['logo'];
// if($_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['HTTP_HOST'] == 'localhost:8012'){
// 	$logo = $_SERVER['DOCUMENT_ROOT'].'\\'.site_folder.'assets\\images\\'.$_SESSION['ERP_SETTINGS']['logo'];
// }
// echo $logo;
// exit;

$COMP_NAME_TR = '<tr>
					<td class="font-size-15" style="padding-top: 14px; text-align:right">
						<b>'.$comp_name.'<b>
					</td>
				</tr>';
$COMP_ADDR_TR = '<tr>
					<td class="font-size-13" style="  text-align:right">
						'.$comp_address.'
						'.($compData[0]['cr_no'] ? '<br>C.R. No. : '.$compData[0]['cr_no'] : '').'
					</td>
				</tr>';
$COMP_EMAIL_TR = '<tr>
					<td class="font-size-13" style=" margin:0px; text-align:right">
					E-Mail : '.$comp_email.'
					</td>
				</tr>';
$comp_location_set = (int)$item_settings['comp_location'];
$comp_country_set = (int)$item_settings['comp_country'];

if($comp_location_set == 0 ) $comp_location ='';
if($comp_country_set == 0 ) $comp_country ='';
if(strtolower($comp_country) == 'bahrain') $comp_country = 'Kingdom of Bahrain';
$locationArr =array($comp_location,$comp_country);
$locationStr =  array_filter($locationArr, 'strlen');
$locationStr = implode(', ',$locationStr);
$COMP_LOCATION_TR = '<tr>
						<td class="font-size-13" style=" margin:0px; text-align:right">
							'.$locationStr.'
						</td>
					</tr>';

$COMP_PHONE_TR = '<tr>
						<td class="font-size-13" style=" margin:0px; text-align:right">
							Phone No. : '.$comp_phone.' 
						</td>
					</tr>';
$COMP_MOBILE_TR = '<tr>
	<td class="font-size-13" style=" margin:0px; text-align:right">
	Mobile No. : '.$comp_mobile.'
	</td>
</tr>';
$COMP_VAT_TR = '<tr>
						<td class="font-size-13" style=" padding-bottom:1pc; text-align:right">
							<b>VAT No. : '.$comp_vat.'</b> 
						</td>
					</tr>' ;

// print_r($settings);

$comp_name_set = (int)$item_settings['comp_name'];
$comp_address_set = (int)$item_settings['comp_address'];
$comp_email_set = (int)$item_settings['comp_email'];
$comp_phone_set = (int)$item_settings['comp_phone'];
$comp_mobile_set = (int)$item_settings['comp_mobile'];
$comp_vat_set = (int)$item_settings['comp_vat'];
$page_number_position = (int)$item_settings['page_number_position'];
$header_repeat = (int)$item_settings['header_repeat'];

$comp_vat_set = (int)$item_settings['comp_vat'];
$comp_vat_set = (int)$item_settings['comp_vat'];
$comp_vat_set = (int)$item_settings['comp_vat'];

$approved_by_set = (int)$item_settings['approved_by'];
$prepared_by_set = (int)$item_settings['prepared_by'];
$received_by_set = (int)$item_settings['received_by'];
$received_by2_set = (int)$item_settings['received_by2'];
$received_by2_sign_set = (int)$item_settings['received_by2_sign'];
$received_by2_date_set = (int)$item_settings['received_by2_date'];

/*********************** Coloms Disabled ***************************/

$department_set = (int)$item_settings['department_set'];
$amt_excl_vat_set = (int)$item_settings['amt_excl_vat_set'];
$vat_set = (int)$item_settings['vat_set'];
$vat_amt_set = (int)$item_settings['vat_amt_set'];
$amt_incl_vat_set = (int)$item_settings['amt_incl_vat_set'];
$unit_set = (int)$item_settings['unit_set'];
$settlement_set = (int)$item_settings['settlement_set'];
$terms_set = (int)$item_settings['terms_set'];
$show_on_letterhead_set = (int)$item_settings['show_on_letterhead_set'];

/*********************** Coloms Disabled ***************************/

/*********************** Coloms Name Dynamic ***************************/

$item_code_col_name = $item_settings['item_code_col_name'];
$department_col_name = $item_settings['department_col_name'];
$item_name_col_name = $item_settings['item_name_col_name'];
$qty_col_name = $item_settings['qty_col_name'];
$unit_col_name = $item_settings['unit_col_name'];
$unit_price_col_name = $item_settings['unit_price_col_name'];
$amt_excl_vat_col_name = $item_settings['amt_excl_vat_col_name'];
$vat_col_name = $item_settings['vat_col_name'];
$vat_amt_col_name = $item_settings['vat_amt_col_name'];
$amt_incl_vat_col_name = $item_settings['amt_incl_vat_col_name'];

$item_code_col_width = (float)$item_settings['item_code_col_width'];
$department_col_width = (float)$item_settings['department_col_width'];
$item_name_col_width = (float)$item_settings['item_name_col_width'];
$qty_col_width =(float)$item_settings['qty_col_width'];
$unit_col_width =(float)$item_settings['unit_col_width'];
$unit_price_col_width = (float)$item_settings['unit_price_col_width'];
$amt_excl_vat_col_width = (float)$item_settings['amt_excl_vat_col_width'];
$vat_col_width = (float)$item_settings['vat_col_width'];
$vat_amt_col_width = (float)$item_settings['vat_amt_col_width'];
$amt_incl_vat_col_width = (float)$item_settings['amt_incl_vat_col_width'];

/*********************** Coloms Name Dynamic ***************************/

/************************Functions *************************/

if(!$department_set) {
	$show_department_row = false;
}
/************************Functions *************************/
$approved_by_td = '<td class="center" style="height: 40px;">Approved by: .........................</td>';
$prepared_by_td = '<td class="center" style="height: 40px;">Prepared by: ..........................</td>';
$received_by_td = '<td class="center" style="height: 40px;">Received by: ..........................</td>';

$received_by2_td = '<td class="center" style="height: 40px;">Received by: ..........................</td>';
$received_by2_sign_td = '<td class="center" style="height: 40px;">Signature: ..............................</td>';
$received_by2_date_td = '<td class="center" style="height: 40px;">Date: ..............................</td>';

$prepared_by_sign_td = $approved_by_sign_td = $received_by_sign_td = '<td class="center">Signature: ..............................</td>';
if($comp_name_set == 0) $COMP_NAME_TR = '';
if($comp_address_set == 0) $COMP_ADDR_TR = '';
if($comp_email_set == 0) $COMP_EMAIL_TR = '';
if($comp_phone_set == 0) $COMP_PHONE_TR = '';
if($comp_mobile_set == 0) $COMP_MOBILE_TR = '';
if($comp_vat_set == 0) $COMP_VAT_TR = '';
if($comp_location_set == 0) $COMP_LOCATION_TR = '';

if($approved_by_set == 0){
    $approved_by_td = '';
    $approved_by_sign_td = '';
}
if($prepared_by_set == 0){
    $prepared_by_td = '';
    $prepared_by_sign_td = '';
}
if($received_by_set == 0){
    $received_by_td = '';
    $received_by_sign_td = '';
}
if($received_by2_set == 0){
    $received_by2_td = '';
}

if($received_by2_date_set == 0){
    $received_by2_date_td = '';
}

if($received_by2_sign_set == 0){
    $received_by2_sign_td = '';
}


$topcontent = '
				<div class="row" >
					'.$logo_on_condition.'

					<div class="col-xs-3">
						'.(isset($qr_code) ? '<td style="width: 30%;vertical-align: middle; border: none;">
												<img src="'.$qr_code.'" alt="QR Code" style="width: 180px; height: auto;">
											</td>' : '&nbsp;').'
					</div>
					<div class="col-xs-6" >
						<table width="100%">
								'.$COMP_NAME_TR.'
								'.$COMP_ADDR_TR.'
								'.$COMP_LOCATION_TR.'
								'.$COMP_EMAIL_TR.'
								'.$COMP_PHONE_TR.'
								'.$COMP_MOBILE_TR.'
								'.$COMP_VAT_TR.'
						</table>
					</div>
					<div class="col-xs-4" >

					</div>
					
				</div>';

if($pdf_on_letterhead && $hide_address_on_lh){
	$topcontent = '';
}

$tax_heading = '<table autosize="1" width="100%" border="1">
					<tr >
						<td class="font-size-15" style="text-align:center;border:none;padding-top:5px;padding-bottom:5px"><b>'.$invHeading.'</b></td>
						
					</tr>
				</table>';

				if ($show_customer_delivery_box) {
					$delivered_to_box = '
					<tr >
						<td style="border:none;
						padding-bottom: 2px;" class="font-size-11">
							<b>Deliver To:</b>
						</td>
					</tr>
					<tr >
						<td style="border:none;padding-top: 0;
						padding-bottom: 2px;" class="font-size-11">
							<b>'.$deliver_cust_name.'</b>
						</td>
					</tr>
					
					'.$deliver_TO.'
					
					'.$deliver_TO_PHONE.'
				';
}else{
	$delivered_to_box = '';
}
$customer_box = '
		<table id="refrence-top-table" autosize="1" width="100%" border="0" cellpadding="0" cellspacing="2" style="margin-top:10px;">
			<tr>
			<td style="width:45%;padding-right:2%;vertical-align:top;border:1px solid;" >
				<table autosize="1" width="100%" border="0" cellpadding="4" cellspacing="2">
					<tr >
						<td style="border:none;padding-bottom: 0;" class="font-size-11">
							<b>'.$cust_name.'</b>
						</td>
					</tr>
					<tr >
						<td style="border:none;padding-top: 0;
						padding-bottom: 0;" class="font-size-11">
							Customer Code : '.$cust_code.'
						</td>
					</tr>
					<tr >
						<td style="border:none;padding-top: 0;
						padding-bottom: 0;" class="font-size-11">
							'.$cust_full_address.'
						</td>
					</tr>
					'   .$cust_full_address2.   '
					
					'	.	$cust_phone_tr		.	'
					'	.	$cust_trn_tr		.	'
					'	.	$delivered_to_box	.	'
				</table>
				</td>
				';
				$salesmanTR  ='';
				if($salesman){
					$salesmanTR = '
					<tr>
						<td class="border-bottom font-size-11" >Salesman </td>
						<td class="border-bottom font-size-11" colspan="3">'.$salesman.' </td>						
					</tr>
				';
				}
$due_date = '<td class="border-bottom font-size-11" colspan="2"></td>';
if($data[0]['credit_sales_only'] && $data[0]['due_date'] != ''){
	$due_date = '<td class="border-bottom font-size-11"  >
					Due Date
				 </td>
				 <td class="border-bottom font-size-11">
					<b>'.date('d.m.Y', strtotime($data[0]['due_date'])).'</b>
				 </td>';
}
$reference_box = '
				
				<td style="vertical-align:top;border:none!important;" id="print-padding">
				<table autosize="1" width="100%" border="'.$border_ref.'"  id="reff-table-cs" cellpadding="4" cellspacing="0"  >
					<tr >
						<td class="border-bottom font-size-11"  >
							Invoice No.
						</td>
						<td class="border-bottom font-size-13" >
							<b>'.$ref_no.'</b>
						</td>
						<td class="border-bottom font-size-11"  >
							Date
						</td>
						<td class="border-bottom font-size-11" >
							<b>'.$sales_date.'</b>
						</td>
					</tr>
					<tr >
						<td class="border-bottom font-size-11" >
							Order No.
						</td>
						<td class="border-bottom font-size-11">
							<b>'.$SO_Ref.'</b>
						</td>
						'.$due_date.'
					</tr>
					<tr >
						<td class="border-bottom font-size-11" >
							Del Note No.
						</td>
						<td class="border-bottom font-size-11" colspan="3">
							<b>'.$DN_Ref.'</b>
						</td>
						
					</tr>
					<tr >
						<td class="border-bottom font-size-11" >
							Payment Terms
						</td>
						<td class="border-bottom font-size-11" colspan="3" >
							<b>'.$payment_terms.'</b>
						</td>
						
					</tr>
					'.$salesmanTR.'
					<tr>
						<td  style="border:none!important" class="font-size-11"  >
								Customer Ref
						</td>
						<td  style="border:none!important" class="font-size-11" colspan="3"  >
							'.$customer_ref.'
						</td>
					</tr>
				</table>
				</td>
				</tr>
				</table>';


$colom_name1 = 'SL No.';
$colom_name2 = 'Item Code';
$department_colom = 'Department';
$colom_name3 = 'Item Name';
$colom_name4 = 'Quantity';
$colom_name4_1 = 'Unit';
$colom_name5 = 'Unit Price';
$colom_name6 = 'Total Excl.VAT';
$colom_name7 = 'VAT (%)';
$colom_name8 = 'VAT Amount';
$colom_name9 = 'Total Incl.VAT';

if($item_code_col_name)  $colom_name2 = $item_code_col_name;
if($item_name_col_name)  $colom_name3 = $item_name_col_name;
if($qty_col_name)  $colom_name4 = $qty_col_name;
if($department_col_name) $department_colom = $department_col_name;
if($unit_col_name)  $colom_name4_1 = $unit_col_name;
if($unit_price_col_name)  $colom_name5 = $unit_price_col_name;
if($amt_excl_vat_col_name)  $colom_name6 = $amt_excl_vat_col_name;
if($vat_col_name)  $colom_name7 = $vat_col_name;
if($vat_amt_col_name)  $colom_name8 = $vat_amt_col_name;
if($amt_incl_vat_col_name)  $colom_name9 = $amt_incl_vat_col_name;

$background = '';
$font_bold = 'font-weight:bold';
$border = 0;
$with_background = (int)$item_settings['with_background'];
if($with_background == 1){
	$background = 'style="background-color:#c6eaff"';
	$font_bold = '';
	$border = 1;
}
if($show_department_row){
    $department_TH = '<th style="'.$font_bold.';width:'.$department_col_width.'%">'.$department_col_name.'</th>';
    $colspan1 = $colspan +1;
}
if($vat_set){
    $VAT_TH = '<th class="no-vat-colom" style="'.$font_bold.';width:'.$vat_col_width.'%;'.$vat_cols.'">'.$colom_name7.' </th>';
    $colspan1 = $colspan +1;
}
if($vat_amt_set){
    $VAT_AMT_TH = '<th class="no-vat-colom" style="'.$font_bold.';width:'.$vat_amt_col_width.'%;'.$vat_cols.'">'.$colom_name8.' <br> ( '.$currency.' )</th>';
    $colspan1 = $colspan +1;
}
if($amt_incl_vat_set){
    $TOT_INCL_VAT_TH = '<th class="no-vat-colom" style="'.$font_bold.';width:'.$amt_incl_vat_col_width.'%;'.$vat_cols.'">'.$colom_name9.' <br> ( '.$currency.' )</th>';
    $colspan1 = $colspan +1;
}
if($unit_set){
    $UNIT_TH = '<th style="'.$font_bold.';min-width:'.$unit_col_width.'%">'.$colom_name4_1.'</th>';
    $colspan1 = $colspan +1;
}


$section2 = '
				<table autosize="1" width="100%" border="'.$border.'" id="item-table"  style="border-collapse:collapse;">
				<thead style="border-bottom:1px solid black">
					<tr '.$background.'>
						<th style="'.$font_bold.';min-width:5%">'.$colom_name1.'</th>
						<th style="'.$font_bold.';min-width:'.$item_code_col_width.'%">'.$colom_name2.'</th>
						'.$department_TH.'
						<th style="'.$font_bold.';min-width:'.$item_name_col_width.'%">'.$colom_name3.'</th>
						<th style="'.$font_bold.';min-width:'.$qty_col_width.'%">'.$colom_name4.'</th>
						'.$UNIT_TH.'
						<th style="'.$font_bold.';min-width:'.$unit_price_col_width.'%">'.$colom_name5.'</th>
						<th style="'.$font_bold.';min-width:'.$amt_excl_vat_col_width.'%">'.$colom_name6.'<br> ( '.$currency.' )</th>
						<div style="'.$vat_cols.'">
							'.$VAT_TH.'
							'.$VAT_AMT_TH.'
							'.$TOT_INCL_VAT_TH.'
							
						</div>
					</tr>			
				</thead>
				<tbody >
				';

				$returnData = findQuery("SELECT ri.unit_price * -1 as unit_price, 
				ri.qty * -1 as qty,
				ri.net_vat  * -1 as net_vat,
				ri.net_discount  * -1 as net_discount,
				ri.net_amount_excl_vat  * -1 as net_total,
				ri.gross_net_total_incl_vat  * -1 as gross_net_total_incl_vat,
				ri.net_amount_excl_vat * -1 as net_amount_excl_vat,
				ri.total_discount * -1 as total_discount,
				ri.total_before_discount * -1 as total_before_discount,
				ri.disc_prcnt * 1 as disc_prcnt,
				ri.compound_item_guid,
				ri.product_guid,
				ri.tax_guid,
				ri.tax,
				ri.id,
				i.name,i.code,i.packing_unit as unit_guid  
				from erp_pos_items_return ri , 
				erp_compound_items i 
				where ri.compound_item_guid=i.id 
				and 
				ri.credit_note_id = $transaction_ID 
				and 
				ri.type = 3 ");
$x = 0;
$total_qty = 0;
$total_excl_vat = 0;
$total_vat = 0;
foreach ($returnData as $rdata) {
    $guid = $rdata['id'];
    $id = (int)$rdata['id'];
    $product_guid = (int)$rdata['product_guid'];
    $compound_item_guid = (int)$rdata['compound_item_guid'];
    $unitName = '';
    if ($rdata['unit_guid']) {
        $unitName = rowvalue($rdata['unit_guid'], 'erp_units', 'name');
    }
    $subTotal[] = (float)$rdata['net_total'];
    $rqty = (float)$rdata['qty'];
    $total_before_discount = (float)$rdata['total_before_discount'];
    $total_discount = (float)$rdata['total_discount'];
    $net_amount_excl_vat = (float)$rdata['net_amount_excl_vat'];
    $net_vat = (float)$rdata['net_vat'];
    $gross_net_total_incl_vat = (float)$rdata['gross_net_total_incl_vat'];
    $net_vat = (float)$rdata['net_vat'];
    $discount_per_unit = 0;
    if((float)$total_discount) {
        if((float)$rqty) {
            $discount_per_unit = (float)$total_discount/(float)$rqty;
        }
    }
    // echo '<pre>';
    // print_r($rdata);
    $tax_guid = (int)$rdata['tax_guid'];
    $tax = (float)$rdata['tax'];

	$x++;
	$total_qty += $rqty;
	$total_excl_vat += $net_amount_excl_vat;
	$total_vat += $net_vat;

	// $unit_name = rowvalue($rdata['unit_guid'],'erp_units','name');
	
	if($show_department_row){
		$department_TD  = '<td ></td>		';
		// $department_TD_blank = '<td></td>';
		// $department_TD_blank_total = '<td  style="border-top:1px solid;border-bottom:1px solid;"></td>';
	}
	if($vat_set){
		$VAT_TD = '<td class="no-vat-colom"  style="text-align:right;'.$vat_cols.'">'.cnv_Float($tax,2).'</td>';
		$vat_TD_blank = '<td class="no-vat-colom" style="'.$vat_cols.'">&nbsp;</td>';
		$vat_TD_blank_total = '<td class="no-vat-colom" style="border-top:1px solid;border-bottom:1px solid;'.$vat_cols.'"></td>';
	}
	if($vat_amt_set){
		$VAT_AMT_TD = '<td class="no-vat-colom" style="text-align:right;'.$vat_cols.'">'.toComma(cnv_Float($net_vat)).'</td>';
		$vat_amt_TD_blank = '<td class="no-vat-colom" style="'.$vat_cols.'">&nbsp;</td>';
	}
	if($amt_incl_vat_set){
		$TOT_INCL_VAT_TD = '<td class="no-vat-colom" style="text-align:right;'.$vat_cols.'">'.toComma(cnv_Float((float)$gross_net_total_incl_vat)).'</td>';
		$tot_incl_vat_TD_blank = '<td class="no-vat-colom" style="'.$vat_cols.'">&nbsp;</td>';
	}
	if($unit_set){
		$UNIT_TD = '<td style="text-align:center">'.$unitName.'</td>';
		$UNIT_TD_blank = '<td  >&nbsp;</td>';
		$UNIT_TD_blank_total = '<td  ></td>';
	}

	$item_name  = $rdata['name'];
	$section2 .=	
					'<tr >
						<td style="text-align:center">'.$x.'</th>
						<td >'.$rdata['code'].'</td>
						'.$department_TD.'
						<td >'.$item_name.'</td>
						
						<td style="text-align:center">'.$rqty.'</td>
						'.$UNIT_TD.'
						<td style="text-align:right">'.toComma(cnv_Float(abs((float)$rdata['unit_price']) - abs((float)$discount_per_unit))).'</th>
						<td style="text-align:right">'.toComma(cnv_Float($net_amount_excl_vat)).'</td>
						<div style="'.$vat_cols.'">
						'.$VAT_TD.'
						'.$VAT_AMT_TD.'
						'.$TOT_INCL_VAT_TD.'
						
						
						</div>
						
					</tr>';
}
$y = 1;
foreach($items as $item){
	$x++;
	$total_qty += $item['qty'];
	$total_excl_vat += $item['grosstotal'];
	$total_vat += $item['vat_amount'];
	$unit_name = rowvalue($item['unit_guid'],'erp_units','name');
	$serial_numbers_exists = false;
	$is_coating = $item['is_coating'];
	if (is_null(json_decode(base64_decode($item['batch_numbers_arr']))) == 1) 
	{ }else{
		$serial_numbers_exists = true;
		$serial_numbers = json_decode(base64_decode($item['batch_numbers_arr']),true);
		$serial_no_string = array();
		foreach($serial_numbers as $sn_arr){
			$serial_no_string[] = $sn_arr['sn'];
		}
		$serial_numbers = implode(',  ',$serial_no_string);
	}
	if($show_department_row){
		$department_TD  = '<td >'.$item['department'].'</td>		';
		$department_TD_blank = '<td></td>';
		$department_TD_blank_total = '<td  style="border-top:1px solid;border-bottom:1px solid;"></td>';
	}
	if($vat_set){
		$VAT_TD = '<td class="no-vat-colom"  style="text-align:right;'.$vat_cols.'">'.cnv_Float($item['vat'],2).'</td>';
		$vat_TD_blank = '<td class="no-vat-colom" style="'.$vat_cols.'">&nbsp;</td>';
		$vat_TD_blank_total = '<td class="no-vat-colom" style="border-top:1px solid;border-bottom:1px solid;'.$vat_cols.'"></td>';
	}
	if($vat_amt_set){
		$VAT_AMT_TD = '<td class="no-vat-colom" style="text-align:right;'.$vat_cols.'">'.toComma(cnv_Float($item['vat_amount'])).'</td>';
		$vat_amt_TD_blank = '<td class="no-vat-colom" style="'.$vat_cols.'">&nbsp;</td>';
	}
	if($amt_incl_vat_set){
		$TOT_INCL_VAT_TD = '<td class="no-vat-colom" style="text-align:right;'.$vat_cols.'">'.toComma(cnv_Float((float)$item['grosstotal'] + (float)$item['vat_amount'])).'</td>';
		$tot_incl_vat_TD_blank = '<td class="no-vat-colom" style="'.$vat_cols.'">&nbsp;</td>';
	}
	if($unit_set){
		$UNIT_TD = '<td style="text-align:center">'.$unit_name.'</td>';
		$UNIT_TD_blank = '<td  >&nbsp;</td>';
		$UNIT_TD_blank_total = '<td  style="border-top:1px solid;border-bottom:1px solid;"></td>';
	}
	$item_name = $item['name'];
	$is_non_inventory_item = (int)$item['is_non_inventory_item'];
	if($is_non_inventory_item == 1 ) {
		if($item['description'] != '' && $item['description'] != NULL) $item_name = $item['description'];
		else $item_name = $item_name;
	}
	else  if($item['description'] != '') $item_name = $item_name . '<br>' . $item['description'];
	$weight = 'lighter';
	if($is_coating == 1) $weight = 'bold';
	$item_name_desc = '';
	 if (!empty($item['actual_batch_arr'])) {
                            $batch_numbers = json_decode(base64_decode($item['actual_batch_arr']), true);
                            if (is_array($batch_numbers)) {
                                $itemID = (int)$item['itemID'];
                                $batchDetails = findQuery("SELECT s.sale_qty, s.return_qty, b.batch_number , b.mfg_date,b.expiry_date FROM `batch_number_sales` s JOIN batch_number b ON b.id = s.batch_number_guid where s.order_item_guid = $itemID and s.type =4");
                                foreach($batchDetails as $batches){
                                    $expiryDate = '  ';
                                   if (!empty($batches['expiry_date']) && 
                                        $batches['expiry_date'] !== '0000-00-00' && 
                                        $batches['expiry_date'] !== '1970-01-01') {
                                        $expiryDate = date('d/m/Y', strtotime($batches['expiry_date']));
                                    }
                                $item_name_desc .= '
								<table style="width: 100%; border:none:!important;margin:0;padding:0" border="1" style="border-collapse:collapse;">
  <tr>
    <td style="width:20%;border:none:!important;padding-bottom:0">BN: '.$batches['batch_number'].'</td>
    <td style="width:40%;border:none:!important;width:20%;padding-bottom:0">Qty: '.$batches['sale_qty'].'</td>
    <td style="width:10%;border:none:!important;width:35%;padding-bottom:0">ED: '.$expiryDate.'</td>
    
  </tr>
</table>';


                                }
                            }
                         }
	if($profiles_only == 0){
			$section2 .=	
					'<tr >
						<td style="text-align:center">'.$x.'</th>
						<td style="font-weight:'.$weight.'" >'.$item['code'].'</td>
						'.$department_TD.'
						<td style="font-weight:'.$weight.'">'.$item_name.' '.$item_name_desc.'</td>
						
						<td style="text-align:center">'.$item['qty'].'</td>
						'.$UNIT_TD.'
						<td style="text-align:right">'.toComma(cnv_Float($item['net_unit_price'])).'</th>
						<td style="text-align:right">'.toComma(cnv_Float($item['grosstotal'])).'</td>
						<div style="'.$vat_cols.'">
						'.$VAT_TD.'
						'.$VAT_AMT_TD.'
						'.$TOT_INCL_VAT_TD.'
						
						
						</div>
						
					</tr>';
	if($serial_numbers_exists){
			$section2 .=	
					'<tr >
						<td ></th>
						<td ></td>
						'.$department_TD_blank.'
						<td >'.$serial_numbers.'</td>
						'.$UNIT_TD_blank.'
						<td style="text-align:right"></td>
						<td style="text-align:right"></td>
						<td style="text-align:right"></td>
						<div style="'.$vat_cols.'">
						'.$vat_TD_blank.'
						'.$vat_amt_TD_blank.'
						'.$tot_incl_vat_TD_blank.'
						</div>
						
					</tr>';
	}
	
		
}
	$is_bundled_item = (int)$item['is_bundled_item'];
	$bundles = array();
	
	if ($is_bundled_item) {
		$sales_item_guid = (int)$item['id'];
		$bundles = findQuery("SELECT b.qty,c.name,c.code,b.product_guid from sale_bundled_item b INNER JOIN erp_products c ON c.id =  b.pack_product_guid  where b.sales_item_guid = $sales_item_guid and b.type ='sales'");
		if(empty($bundles)){
			$dn_ref_guid = (int)$data[0]['dn_ref'];
			$dn_guid_arr = findQuery("SELECT id from erp_deliverynote where dn_ref = '$dn_ref_guid' ");
			if($dn_guid_arr) {

				$dn_guid = $dn_guid_arr[0]['id'];
				$product_guid = (int)$item['product_guid'];
				$bundles = findQuery("SELECT b.qty,c.name,c.code,b.product_guid from sale_bundled_item b INNER JOIN erp_products c ON c.id =  b.pack_product_guid  where b.product_guid = $product_guid and b.master_guid = $dn_guid and b.type ='dn'");
			}
		}
			foreach ($bundles as $bundle_item) {
				$section2 .=	
					'<tr >
						<td ></th>
						<td >'.$bundle_item['code'].'</td>
						'.$department_TD_blank.'
						<td >'.$bundle_item['name'].'</td>
						<td style="text-align:center">'.((float)$bundle_item['qty'] * (float)$item['qty']).'</td>
						'.$UNIT_TD_blank.'
						<td style="text-align:right"></td>
						<td style="text-align:right"></td>
						<div style="'.$vat_cols.'">
						'.$vat_TD_blank.'
						'.$vat_amt_TD_blank.'
						'.$tot_incl_vat_TD_blank.'
						</div>
						
					</tr>';
			}
	}
	if($is_coating == 1){
		$dn_item_guid = $item['id'];
		$coatingItems = findQuery("SELECT i.*, p.name, p.code from coating_item_sale i JOIN erp_products p ON p.id = i.prod_guid  where i.sls_item_guid = $dn_item_guid and i.qty > 0");
		foreach($coatingItems as $c_item){
				$unit_name =($c_item['comp_guid2'] > 0) ?  $units[rowvalue($c_item['comp_guid2'],'erp_compound_items',"packing_unit")] : 'Kg';
				if($profiles_only == 0){
//' - '.($c_item['qty'] *  $c_item['tot_unit2']).' '.$unit_name.
				$section2 .=	
				'<tr >
					<td ></th>
					<td ><i>'.$c_item['code'].'</i></td>
					'.$department_TD_blank.'
					<td ><i>'.$c_item['name'].'<i></td>
					<td style="text-align:center"></td>
					'.$UNIT_TD_blank.'
					<td style="text-align:right"></td>
					<td style="text-align:right"></td>
					<div style="'.$vat_cols.'">
					'.$vat_TD_blank.'
					'.$vat_amt_TD_blank.'
					'.$tot_incl_vat_TD_blank.'
					</div>
					
				</tr>';
			}else{
				$totQty = $c_item['qty'] * $c_item['tot_unit2'];
				$totAMT = ($item['net_unit_price'] * $totQty);
				$vatAMT = ($totAMT *$item['vat'])/100;
				if($vat_set){
					$VAT_TD = '<td class="no-vat-colom"  style="text-align:right;'.$vat_cols.'">'.cnv_Float($item['vat'],2).'</td>';
				}
				if($vat_amt_set){
					
					$VAT_AMT_TD = '<td class="no-vat-colom" style="text-align:right;'.$vat_cols.'">'.toComma(cnv_Float($vatAMT)).'</td>';
				}
				if($amt_incl_vat_set){
					$TOT_INCL_VAT_TD = '<td class="no-vat-colom" style="text-align:right;'.$vat_cols.'">'.toComma(cnv_Float(($totAMT) + (float)$vatAMT)).'</td>';
				}
				if($unit_set){
					$unit_td_new = '<td style="text-align:center">'.$unit_name.'</td>';
				}
				$section2 .=	
								'<tr >
									<td style="tet-align:center">'.$y.'</th>
									<td ><i>'.$c_item['code'].'</i></td>
									'.$department_TD_blank.'
									<td ><i>'.$c_item['name'].'<i></td>
									<td style="text-align:center">'.$totQty.'</td>
									'.$unit_td_new.'
									<td style="text-align:right">'.toComma(cnv_Float($item['net_unit_price'])).'</td>
									<td style="text-align:right">'.toComma(cnv_Float($totAMT)).'</td>
									<div style="'.$vat_cols.'">
									'.$VAT_TD.'
									'.$VAT_AMT_TD.'
									'.$TOT_INCL_VAT_TD.'
									</div>
									
								</tr>';
								$y++;
			}
			
		}
	}
}

$invoice_discount = $item['g_invoice_disc'];
$total_excl_vat_after_discount = cnv_Float($total_excl_vat);
$total_vat_after_discount = cnv_Float($total_vat);
$invoice_discount_TR = '';
if((float)$invoice_discount){
	$total_excl_vat_after_discount =cnv_Float($total_excl_vat  - $invoice_discount);
	$total_vat_after_discount =cnv_Float($item['g_vat']);
	$invoice_discount_TR = '<tr>
										<td><h5>Discount</h5></td>
										<td   style="text-align:right"> 
											<h5>(-)'.toComma(cnv_Float($invoice_discount)).' </h5>
										</td>
									</tr>';
} 

$final_total = $final_total_in_words = cnv_Float($item['g_nettotal']);


$only = '';
$vat_only = '';
$tot_only = '';

$words = explode('.',$total_excl_vat_after_discount);
$fills = '';
$bhd = convertToBD($words[0]);
$words[1] = (floor( $total_excl_vat_after_discount * 1000) - floor( $words[0] * 1000));
if((float)$words[1] > 0){
	$fills =  convertToFills($words[1],$words[0]);
}
if($total_excl_vat_after_discount > 0) $only = ' Only.';

$decimal_zeros = 0;
$decimals = (int)$_SESSION['COMPANY_SETTINGS']['decimals'];
if($decimals){
	 $decimal_zeros = pow(10,$decimals);
}

$vat_words = explode('.',$total_vat_after_discount);
$vat_fills = '';
$vat_bhd = convertToBD($vat_words[0]);
$vat_words[1] = (floor( $total_vat_after_discount * $decimal_zeros) - floor( $vat_words[0] * $decimal_zeros));
if((float)$vat_words[1] > 0){
	$vat_fills =  convertToFills($vat_words[1],$vat_words[0]);
}
if($total_vat_after_discount > 0) $vat_only = ' Only.';

$words_excl_advance = (int)$item_settings['amount_in_words_excl_advance'];

if($words_excl_advance){
	$final_total_in_words = cnv_Float((float)$final_total - (float)$data[0]['adv_received']);
}
$tot_words = explode('.',$final_total_in_words);

$tot_fills = '';
$tot_bhd = convertToBD($tot_words[0]);
$tot_words[1] = (floor( $final_total_in_words * $decimal_zeros) - floor( $tot_words[0] * $decimal_zeros));
if((float)$tot_words[1] > 0){
	$tot_fills =  convertToFills($tot_words[1],$tot_words[0]);
}
if($final_total_in_words > 0) $tot_only = ' Only.';

$round_off_td = '';
if (abs($item['g_roundoff'])) {
    $round_off_td ='<tr>
					<td><h5>Round Off</h5></td>
					<td   style="text-align:right"> 
						<h5>'.cnv_Float($item['g_roundoff']).'</h5>
					</td>
					</tr>';
}
$advance_tr = $vat_on_advance_tr = '';
$total_after_advance_tr = '';
if (($data[0]['adv_received']) != 0) {
    $advance_tr ='<tr>
					<td><h5>Advance Received</h5></td>
					<td   style="text-align:right"> 
						<h5>'.toComma(cnv_Float($data[0]['adv_received'])).'</h5>
					</td>
				  </tr>';
}
if (($data[0]['vat_on_advance']) != 0) {
    $vat_on_advance_tr ='<tr>
					<td><h5>VAT on Advance</h5></td>
					<td   style="text-align:right"> 
						<h5>-'.toComma(cnv_Float($data[0]['vat_on_advance'])).'</h5>
					</td>
				  </tr>
	';
}
if (($data[0]['adv_received']) != 0) {
    $total_after_advance_tr ='<tr>
							<td><h5>Net Total Incl.VAT</h5></td>
							<td   style="text-align:right"> 
								<h5>'.toComma(cnv_Float((float)$final_total - (float)$data[0]['adv_received'])).'</h5>
							</td>
						</tr>';
}
if($vat_amt_set) {
    $vat_amt_TD_blank_total = '<td class="no-vat-colom"  style="border-top:1px solid;border-bottom:1px solid;text-align:right;font-size:11px;'.$vat_cols.'">
								<b> '.toComma(cnv_Float($total_vat)).'</b>
							</td>';
}
if($amt_incl_vat_set) {
    $tot_incl_vat_TD_blank_total = '<td class="no-vat-colom"  style="border-top:1px solid;border-bottom:1px solid;text-align:right;font-size:11px;'.$vat_cols.'">
									<b> '.toComma(cnv_Float((float)$total_vat + (float)$total_excl_vat)).'</b>
								</td>';
}

if($print_type  == 'print'){
	$section2 .= '<tr style="height:100%" >
	 						<td  ></td>
	 						<td >&nbsp;</td>
							<td >&nbsp;</td>
	 						<td >&nbsp;</td>
							'.$department_TD_blank.'
	 						'.$UNIT_TD_blank.'
							<td >&nbsp;</td>
	 						<td >&nbsp;</td>
	 						<div style="'.$vat_cols.'">
	 						'.$vat_TD_blank.'
	 						'.$vat_amt_TD_blank.'
	 						'.$tot_incl_vat_TD_blank.'
	 						</div>
	 				</tr>';
}
$section2 .=	' 
				<tr >
						<td  style="border-top:1px solid;border-bottom:1px solid;"></td>
						<td  style="border-top:1px solid;border-bottom:1px solid;"></td>
						'.$department_TD_blank_total.'
						<td  style="border-top:1px solid;border-bottom:1px solid;font-size:11x">
							<b> Total</b>
						</td>
						<td  style="border-top:1px solid;border-bottom:1px solid;text-align:center; font-size:11px">
							<b> '.$total_qty.' </b>
						</td>
						'.$UNIT_TD_blank_total.'
						<td  style="border-top:1px solid;border-bottom:1px solid;"></td>
						<td  style="border-top:1px solid;border-bottom:1px solid;text-align:right; font-size:11px">
							<b> '.toComma(cnv_Float($total_excl_vat)).'</b>
						</td>
						<div style="'.$vat_cols.'">
						'.$vat_TD_blank_total.'
						'.$vat_amt_TD_blank_total.'
						'.$tot_incl_vat_TD_blank_total.'
						
						
						</div>
						
				</tr>
				<tr >
						<td colspan="'.$colspan1.'" style="border-top:1px solid;border-bottom:1px solid black!important;padding:0">
						
						
							
							
						</td>
						
				</tr>
				</tbody>
				</table><table autosize="1" width="100%" border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td  style="padding:0;width: 60%;border:1px solid black;padding-left: 15px;">
					<table 	autosize="1" 
						width="100%" 
						id="word-amt-table"
						border="0" 
						cellspacing="4">
					<tbody>
						<div style="'.$vat_cols.'">
						<tr class="no-vat-colom" style="'.$vat_rows.'">
							<td  > 
								Amount Excl. VAT (in words)
							</td>
						</tr>
						<tr class="no-vat-colom" style="'.$vat_rows.'">
							<td>
								<h5 >'.$bhd . $fills .  $only .' </h4>
							</td>
						</tr>
						<tr class="no-vat-colom" style="'.$vat_rows.'">
							<td>
								VAT Amount (in words)
							</td>
						</tr>
						<tr class="no-vat-colom" style="'.$vat_rows.'">
							<td>
								<h5>'.$vat_bhd . $vat_fills  . $vat_only .  ' </h4>										
							</td>
						</tr>
						</div>
						<tr>
							<td>
								Net Total Incl. VAT (in words)
							</td>
						</tr>
						<tr>
							<td>
								<h5>'. $tot_bhd . $tot_fills. $tot_only . ' </h4>								
							</td>
						</tr>
					</tbody>
				</table>
					</td>
					<td style="padding:0">
					<table 	autosize="1" 
						id="inner-item-table" 
						width="100%" 
						border="1" 
						cellspacing="0"
						style="border-collapse:collapse;">
					<tbody>
						<tr>
							<td  > 
								<h5>Gross Amount </h5>
							</td>
							<td  style="text-align:right"> 
								<h5>'.toComma(cnv_Float($total_excl_vat)).' </h5>
							</td>
						</tr>
						'.$invoice_discount_TR.'
						<div style="'.$vat_rows.'">
						<tr style="'.$vat_rows.'">
							<td><h5>Total <span class="no-vat-colom">Excl. VAT</span></h5></td>
							<td   style="text-align:right"> 
								<h5>'.toComma(cnv_Float($total_excl_vat_after_discount)).'</h5>
							</td>
						</tr>
						
						<tr class="no-vat-colom" style="'.$vat_rows.'">
							<td><h5>VAT</h5></td>
							<td   style="text-align:right"> 
								<h5>'.toComma(cnv_Float($total_vat_after_discount )).'</h5>
							</td>
						</tr>
						</div>
						'.$round_off_td.'
						
						<tr>
							<td style="padding-bottom:15px"><h5>Total Amount</h5></td>
							<td   style="text-align:right;padding-bottom:15px"> 
								<h5>'.toComma(cnv_Float($final_total )).'</h5>
							</td>
						</tr>
						'.$advance_tr.'
						'.$vat_on_advance_tr.'
						'.$total_after_advance_tr.'
						<tr>
							<td colspan="2" style="text-align:right">
								<h5>For '.$comp_name.' <h5>
								'.($signature ?: '<br><br><br>').'
								Authorised Signatory
								</td>
						
						</tr>
					</tbody>
				</table>
					</td>
				</tr>
			</table>';

$section2_1 .= '  ';
$section6 = '';
//class="settle-box"
if($settlement_set) {
    if (count($settlements) > 0) {
        $section6 .= '<div><table width="100%" autosize="1" cellspacing="0"
	style="border-collapse:collapse;page-break-inside:avoid;" cellpadding="0"  >
				<tr >
				<td style="width:60%" >&nbsp;</td>
				<td  style="width:40%">
				<table   border="1" width="100%"  cellspacing="0"
				style="border-collapse:collapse;" cellpadding="0"  page-break-before="always"  id="settlement">
					<tr>
						<td colspan="2" style="text-align:left;">Settlement</td>

					</tr>';

        foreach ($settlements as $settleData) {
            $ledger_name  = rowvalue($settleData['ledger_guid'], 'erp_ledgers', 'name');


            $section6 .= ' <tr  class="settlement_tr">
							<td  style="text-align:left;">
							'. $ledger_name.' </td>
							<td style="text-align:right;">'.cnv_Float($settleData['amount']).' </td>
						</tr>';

        }

        $section6 .=		' </table>
				<td>
				</tr>
				</table></div>';
    }
}
$section4 = '';
		$term_sl = 1;
if($terms_set) {
    foreach($termsArr as $term) {
        if($term_sl == 1) {
            $section4 .= '
					<div style="page-break-inside:avoid;">	<h5 class="font-size-13">Terms and Conditions :</h5>
						<table autosize="1" width="100%" >';
        }
        $section4 .='<tr>
					<td class="font-size-13" style="width: 5%;text-align:center;">'.$term_sl.'</td>
					<td class="font-size-13"  style="">'.$term.'</td>
				</tr>';
        $term_sl++;
    }
    $section4 .= '</table></div> ';
}
	

	
	$section5 = '';
	//margin-top:15px;margin-bottom:25px 
	if($pdf_on_letterhead == 0 || $show_on_letterhead_set == 1){
		$section5 .= '<table autosize="1" border="0" width="100%" class="font-size-13"  style=" margin-top:15px;margin-bottom:15px;border:none; " cellpadding="7">
						<tr>
							'.$prepared_by_td.'
							'.$approved_by_td.'
							'.$received_by_td.'
						</tr>
						<tr>
							'.$prepared_by_sign_td.'
							'.$approved_by_sign_td.'
							'.$received_by_sign_td.'
							
						</tr>

					 
					  <tr>
						  '.$received_by2_td.'
						  '.$received_by2_sign_td.'
						  '.$received_by2_date_td.'
					  </tr>
					 

					</table>';
	}
$section5 .= '	
				<div class=" font-size-11" style="font-weight:normal;text-align:center;border-bottom:1px solid">
				Thank you for your business.
				</div>
				';

$html =   $styles.$topcontent.$tax_heading.$customer_box.$reference_box.$section1.$section2.$section2_1.$section6.$section4;
if($print_type=='print'){
	echo $html.$section5;
	exit;
}
$mpdf->setAutoBottomMargin = 'stretch';
$mpdf->defaultfooterline = 0;

$header = $topcontent.$tax_heading.$customer_box.$reference_box;
if($header_repeat){
	if($page_number_position){
		$header =  $topcontent.'<p style="text-align: right;font-size:11px;margin:0;"> {PAGENO}{nbpg}</p>'.$tax_heading.$customer_box.$reference_box;
		$mpdf->SetHTMLHeader($header, 'O', false);
	}else{
		$mpdf->SetHTMLHeader($header, 'O', false);
	}
	$html =  $styles.$section1.$section2.$section2_1.$section6.$section4;
}

if(!$page_number_position){
	$mpdf->setFooter($section5.'{PAGENO}{nbpg}');
}else{
	$mpdf->setFooter($section5);
}

$mpdf->autoScriptToLang = true;
$mpdf->autoLangToFont = true;
$mpdf->WriteHTML($html);
$mpdf->simpleTables = true;
$mpdf->SetDisplayMode('fullpage');
$mpdf->list_indent_first_level = 0; 

//call watermark content and image
// $mpdf->SetWatermarkText('etutorialspoint');
// $mpdf->showWatermarkText = true;
// $mpdf->watermarkTextAlpha = 0.1;

//output in browser
ob_clean();

if ($_SERVER['HTTP_HOST'] != 'localhost' && $_SERVER['HTTP_HOST'] != 'localhost:8012'){
    $save_path= $_SERVER['DOCUMENT_ROOT'].'/upload/pdf/';
}else 
{
    $save_path= $_SERVER['DOCUMENT_ROOT'].'\\'.site_folder.'upload\\pdf\\';
}

if(!file_exists($save_path)) {
        mkdir($save_path, 0777, true);
}
$file_name = str_replace('/', '-', $ref_no);

if(isset($_REQUEST['dinline'])){
	$mpdf->Output($file_name . '.pdf','I' ); exit;
}

$mpdf->Output($save_path. $file_name . '.pdf' );
$json['path'] = $save_path;
$json['url']  = siteurl.'upload/pdf/'.$file_name.'.pdf';
echo json_encode($json);
exit;



// '
// 				<tr >
// 						<td >&nbsp;</td>
// 						<td >&nbsp;</td>
// 						<td >&nbsp;</td>
// 						'.$department_TD_blank.'
// 						<td >&nbsp;</td>
// 						<td >&nbsp;</td>
// 						<td >&nbsp;</td>
// 						<td >&nbsp;</td>
// 						<div style="'.$vat_cols.'">
// 						'.$vat_TD_blank.'
// 						'.$vat_amt_TD_blank.'
// 						'.$tot_incl_vat_TD_blank.'
// 						</div>
// 				</tr>
// 				<tr >
// 						<td >&nbsp;</td>
// 						<td >&nbsp;</td>
// 						<td >&nbsp;</td>
// 						'.$department_TD_blank.'
// 						<td >&nbsp;</td>
// 						<td >&nbsp;</td>
// 						<td >&nbsp;</td>
// 						<td >&nbsp;</td>
// 						<div style="'.$vat_cols.'">
// 						'.$vat_TD_blank.'
// 						'.$vat_amt_TD_blank.'
// 						'.$tot_incl_vat_TD_blank.'
// 						</div>
// 				</tr>
// 				<tr style="height:100%" >
// 						<td  ></td>
// 						<td >&nbsp;</td>
// 						<td >&nbsp;</td>
// 						<td >&nbsp;</td>
// 						'.$department_TD_blank.'
// 						<td >&nbsp;</td>
// 						<td >&nbsp;</td>
// 						<td >&nbsp;</td>
// 						<div style="'.$vat_cols.'">
// 						'.$vat_TD_blank.'
// 						'.$vat_amt_TD_blank.'
// 						'.$tot_incl_vat_TD_blank.'
// 						</div>
// 				</tr>
				
				
?>
