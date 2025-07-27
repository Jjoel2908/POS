<?php
session_start();

require_once '../config/global.php';
require_once '../models/Invoice.php';
require '../models/MYPDF.php';

/** Zona horaria */
date_default_timezone_set('America/Mexico_City');

/** Lista blanca de módulos válidos */
$validModules = [
    'Compra',
];

/** Limpiamos los datos enviamos por GET */
$id     = filter_var($_GET['id'], FILTER_VALIDATE_INT) ?: 0;
$module = htmlspecialchars($_GET['module'] ?? '', ENT_QUOTES, 'UTF-8');
$ref    = filter_var($_GET['reference'], FILTER_VALIDATE_INT) ?: 0;
$token  = $_GET['token'] ?? '';

/** Verificamos si el módulo es válido o si el usuario tiene permisos por la referencia */
if (
    $id <= 0 ||
    !in_array($module, $validModules) || 
    !isset($_SESSION['id']) ||
    !isset($_SESSION['user']) ||
    !isset($_SESSION['permisos']) || 
    !in_array($ref, $_SESSION['permisos'])
) {
    die('Acceso denegado');
}

/** Recalcular el token esperado */
$tokenData = "$id|$module|$ref";
$token     = hash_hmac('sha256', $tokenData, SECRET_KEY);
if (!hash_equals($token, $_GET['token'])) {
    die('Token inválido o manipulado');
}

try {
    /** Crear un objeto Invoice usando el módulo y el ID */
    $Invoice = new Invoice($module, $id);

    /** Obtener los datos de la factura */
    $data = $Invoice->getData();

    /** Creación del objeto TCPDF */
    $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    /** Seteamos el margen del header */
    $pdf->SetMargins(PDF_MARGIN_LEFT, 20, PDF_MARGIN_RIGHT);

    /** Configuración del documento PDF */
    $pdf->SetTitle('Comprobante de Compra');
    $pdf->SetFont('helvetica', '', 14);

    $pdf->AddPage();
    $pdf->Ln(6);

} catch (Exception $e) {
    die("Ocurrió un error al obtener los datos");
}