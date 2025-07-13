<?php
session_start();

if (empty($_SESSION['user']) || !in_array(35, $_SESSION['permisos'])) {
    header('Location: index.php');
    exit;
}

require_once 'layout/header.php';
?>

<div id="container-report" class="card bg-transparent shadow-0 radius-10" data-module="ReporteGeneral">
    <div class="card-body pt-2">

        <!-- # [ T I T L E ] # -->
        <div class="row">
            <div class="col-sm-12">
                <h5 class="view-title text-secondary">
                    <i class="fa-solid fa-chart-column me-1"></i>
                    Reporte General
                </h5>
            </div>
        </div>

        <!-- # [ S E A R C H ] # -->
        <form class="validation" id="formReports" method="POST" action="" name="" novalidate="">
            <div class="row justify-content-center">
                <div class="col-sm-12 col-md-4 text-center">
                    <label class="mb-2 font-16" for="date">Selecciona un rango de fechas</label>
                    <div class="d-flex px-lg-4 px-md-0">
                        <div class="flex-grow-1 position-relative input-icon">
                            <input class="form-control font-16" type="text" name="date" id="date" required>
                            <span class="position-absolute top-50 translate-middle-y"><i class="fa-solid fa-calendar-days font-20"></i></span>
                        </div>
                        <button type="submit" id="searchContainer" class="btn btn-success">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </button>
                    </div>
                </div>
                <div class="col-sm-12 text-center mt-4" id="loadingContainer" style="display: none;">
                    <span class="spinner-border spinner-border-sm me-2 text-success" role="status" aria-hidden="true"></span>
                    Procesando información, por favor espere un momento...
                </div>
            </div>
        </form>

        <!-- # [ C O N T E N T ] # -->
        <div id="response" class="card radius-10 bg-transparent shadow-0 mt-2 d-none">
            <div class="card-body px-0">

                <!-- # [  W I D G E T S  ] # -->
                <div id="response-widgets" class="row row-cols-1 row-cols-md-2 row-cols-xl-5 row-cols-xxl-6 mt-2 justify-content-center">
                </div>

                <!-- # [C H A R T   J S ] # -->
                <div class="row justify-content-between">
                    <div id="container-expenses-type" class="col-sm-12 col-lg-6 pe-0">
                        <div class="card radius-10">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-center">
                                    <p class="mb-0 font-16 fw-bold mt-1 text-center">Gastos por Concepto</p>
                                </div>
                                <div id="container-expenses-chart" class="chart-container-0 mt-4 d-none">
                                    <canvas id="chart-expenses-type"></canvas>
                                </div>
                                <div class="text-center font-16 text-danger d-none" id="message-expenses-type"></div>
                            </div>
                        </div>
                    </div>

                    <div id="container-products-type" class="col-sm-12 col-lg-6 pe-0">
                        <div class="card radius-10">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-center">
                                    <p class="mb-0 font-16 fw-bold mt-1 text-center">5 Productos Más Vendidos</p>
                                </div>
                                <div id="container-top-products-chart" class="chart-container-0 mt-4 d-none">
                                    <canvas id="chart-top-products"></canvas>
                                </div>
                                <div class="text-center font-16 text-danger d-none" id="message-top-products"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<?php require_once 'layout/footer.php'; ?>

<!-- # [ S C R I P T ] # -->
<script src="../public/js/modules/moduleReports.js"></script>