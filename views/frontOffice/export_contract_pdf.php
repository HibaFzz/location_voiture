<?php
ob_start();

include '../../controllers/ContractController.php';
require_once('../../vendor/tecnickcom/tcpdf/tcpdf.php');

$contractController = new ContractController();
$contractId = $_GET['id'] ?? 0;

$contract = $contractController->getContractById($contractId);
$user = $contractController->getUserById($contract['user_id']);
$car = $contractController->getCarById($contract['car_id']);

if (!$contract || !$user || !$car) {
    die("Contract, User, or Car not found.");
}

$pdf = new TCPDF();
$pdf->AddPage();

$pdf->SetFont('helvetica', 'B', 20);
$pdf->Cell(0, 10, 'Rental Car Contract', 0, 1, 'C');
$pdf->Ln(5); 

$pdf->SetFont('helvetica', 'B', 14);
$pdf->SetFillColor(220, 220, 220);
$pdf->Cell(0, 10, 'Contract Information', 0, 1, 'L', 1);
$pdf->Ln(3); 

$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(90, 10, 'Contract ID:', 0, 0);
$pdf->Cell(0, 10, $contract['id'], 0, 1);
$pdf->Cell(90, 10, 'Contractor CIN:', 0, 0);
$pdf->Cell(0, 10, $user['cin'], 0, 1);
$pdf->Cell(90, 10, 'Contractor Name:', 0, 0);
$pdf->Cell(0, 10, $user['prenom'] . ' ' . $user['nom'], 0, 1);
$pdf->Cell(90, 10, 'Email:', 0, 0);
$pdf->Cell(0, 10, $user['email'], 0, 1);
$pdf->Cell(90, 10, 'Phone:', 0, 0);
$pdf->Cell(0, 10, $user['numtelephone'], 0, 1);
$pdf->Ln(3); 

$pdf->SetFont('helvetica', 'B', 14);
$pdf->SetFillColor(220, 220, 220);
$pdf->Cell(0, 10, 'Car Information', 0, 1, 'L', 1);
$pdf->Ln(3); 

$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(90, 10, 'Car Title:', 0, 0);
$pdf->Cell(0, 10, $car['vehicletitle'], 0, 1);
$pdf->Cell(90, 10, 'Matricule:', 0, 0);
$pdf->Cell(0, 10, $car['matricule'], 0, 1);
$pdf->Cell(90, 10, 'Brand:', 0, 0);
$pdf->Cell(0, 10, $car['brand'], 0, 1);
$pdf->Cell(90, 10, 'Fuel Type:', 0, 0);
$pdf->Cell(0, 10, $car['fueltype'], 0, 1);
$pdf->Cell(90, 10, 'Model Year:', 0, 0);
$pdf->Cell(0, 10, $car['modelyear'], 0, 1);
$pdf->Cell(90, 10, 'Price per Day:', 0, 0);
$pdf->Cell(0, 10, number_format($car['priceperday'], 2) . ' TND', 0, 1);
$pdf->Ln(3); 


$pdf->SetFont('helvetica', 'B', 14);
$pdf->SetFillColor(220, 220, 220);
$pdf->Cell(0, 10, 'Rental Period & Payment', 0, 1, 'L', 1);
$pdf->Ln(3); 

$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(90, 10, 'Start Date:', 0, 0);
$pdf->Cell(0, 10, $contract['start_date'], 0, 1);
$pdf->Cell(90, 10, 'End Date:', 0, 0);
$pdf->Cell(0, 10, $contract['end_date'], 0, 1);
$pdf->Cell(90, 10, 'Total Payment:', 0, 0);
$pdf->Cell(0, 10, number_format($contract['total_payment'], 2) . ' TND', 0, 1);
$pdf->Ln(10); 

$pdf->SetY(-30);
$pdf->SetFont('helvetica', 'I', 8);
$pdf->Cell(0, 10, 'This document is generated by Our Car Rental System.', 0, 0, 'C');


ob_end_clean();

$pdf->Output('contract_' . $contract['id'] . '.pdf', 'D');
?>
