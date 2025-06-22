<?php require_once '../config/global.php'; ?>
<?php require_once '../models/Dashboard.php'; ?>
<!DOCTYPE html>
<html lang="es">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <meta name="author" content="Joel Alvarado">

   <!--favicon-->
   <!-- <link rel="shortcut icon" type="image/x-icon" href="../../public/images/favicon.ico"> -->
   <link href="../../public/plugins/simplebar/css/simplebar.css" rel="stylesheet" />
   <link href="../../public/plugins/perfect-scrollbar/css/perfect-scrollbar.css" rel="stylesheet" />
   <link href="../../public/plugins/metismenu/css/metisMenu.min.css" rel="stylesheet" />
   <!-- loader-->
   <link href="../../public/css/pace.min.css" rel="stylesheet" />
   <script src="../../public/js/pace.min.js"></script>
   <link rel="stylesheet" href="../../public/css/jquery-ui.min.css">
   <!-- Bootstrap CSS -->
   <link href="../../public/css/bootstrap.min.css" rel="stylesheet">
   <link href="../../public/css/bootstrap-extended.css" rel="stylesheet">
   <link href="https:/fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
   <link href="../../public/css/app.css" rel="stylesheet">
   <link href="../../public/css/icons.css" rel="stylesheet">
   <!-- Theme Style CSS -->
   <link rel="stylesheet" href="../../public/css/dark-theme.css" />
   <link rel="stylesheet" href="../../public/css/semi-dark.css" />
   <link rel="stylesheet" href="../../public/css/header-colors.css" />
   <link rel="stylesheet" href="../../public/DataTables/datatables.min.css" />
   <link rel="stylesheet" href="../../public/plugins/notifications/css/lobibox.min.css"/>
   <link rel="stylesheet" href="../../public/css/style.css" />
   <link rel="stylesheet" href="../../public/css/notie.min.css"/>
   <link rel="stylesheet" href="../../public/plugins/select2/select2.min.css"/>
   <link rel="stylesheet" href="../../public/plugins/daterangepicker/daterangepicker.css"/>

   <title>Sistema de venta</title>

</head>

<body>
   <div class="wrapper">

      <!-- # [ T O P B A R ] # -->
      <?php require 'topbar.php' ?>

      <!-- # [ S I D E B A R ] # -->
      <?php require 'sidebar.php' ?>

      <div class="page-wrapper">
         <div class="page-content">