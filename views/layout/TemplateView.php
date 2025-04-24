<?php

/** Clase para generar las vistas del sistema dinámicamente */
class TemplateView
{
    /** @var string $module Módulo de la vista que se mostrará en la interfaz */
    private string $module;

    /** @var string $title Título de la vista que se mostrará en la interfaz */
    private string $title;

    /** @var string $icon Icono que se mostrará junto al título */
    private string $icon;

    /** @var int $permission ID del permiso requerido para acceder a la vista */
    private int $permission;

    /** @var bool $showAddButton Indica si se debe mostrar el botón de agregar */
    private bool $showAddButton;

    /** @var string $addAction Función de JavaScript que se ejecutará al hacer clic en el botón de agregar */
    private string $addAction;

    /** @var string $addIcon Icono del botón de agregar */
    private string $addIcon;

    /** @var string $addLabel Texto del botón de agregar */
    private string $addLabel;

    /** @var string $tableId ID de la tabla donde se mostrarán los datos */
    private string $tableId;

    /** @var array $modals Lista de rutas de archivos de modales que deben incluirse en la vista */
    private array $modals;

    /** @var string $moduleScript Nombre del script de JavaScript que maneja la lógica del módulo */
    private string $moduleScript;

    /** Constructor de la clase TemplateView
     * Inicializa los atributos con los valores proporcionados en el array de configuración.
     * 
     * @param array $config Configuración de la vista con valores específicos como título, icono, permisos, etc.
     */
    public function __construct(array $config)
    {
        $this->module        = $config['module'] ?? '';
        $this->title         = $config['title'] ?? 'Módulo';
        $this->icon          = $config['icon'] ?? '';
        $this->permission    = $config['permission'] ?? 0;
        $this->showAddButton = $config['showAddButton'] ?? true;
        $this->addAction     = $config['addAction'] ?? '';
        $this->addIcon       = $config['addIcon'] ?? 'fa-solid fa-plus';
        $this->addLabel      = $config['addLabel'] ?? $config['module'];
        $this->tableId       = $config['tableId'] ?? 'module-table';
        $this->modals        = $config['modals'] ?? [];
        $this->moduleScript  = $config['moduleScript'] ?? '';
    }

    /** Renderiza la vista utilizando los atributos de la clase.
     * 
     * - Verifica si el usuario tiene permiso para acceder a la vista.
     * - Incluye la cabecera (`header.php`).
     * - Muestra el título, los botones de acción (si aplica) y la tabla de datos.
     * - Carga el modal correspondiente (si existe).
     * - Incluye el pie de página (`footer.php`) y los scripts necesarios.
     */
    public function render()
    {
        session_start();

        if (empty($_SESSION['user']) || !in_array($this->permission, $_SESSION['permisos'])) {
            header('Location: index.php');
            exit;
        }

        require_once 'layout/header.php';
?>

        <div class="card radius-10" data-module="<?= $this->module ?>">
            <div class="card-body p-4">

                <!-- # [ T I T L E ] # -->
                <div class="row">
                    <div class="col-sm-12">
                        <h5 class="view-title">
                            <i class="<?= $this->icon ?> me-1"></i>
                            <?= $this->title ?>
                        </h5>
                    </div>
                </div>

                <!-- # [ A C T I O N S ] # -->
                <?php if ($this->showAddButton): ?>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="d-flex justify-content-end">
                                <button class="btn btn-success radius-30 px-4 fs-14" onclick="<?= $this->addAction ?>">
                                    <i class="<?= $this->addIcon ?> me-1"></i>
                                    Agregar <?= $this->addLabel ?>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- # [ C O N T E N T ] # -->
                <div class="row">
                    <table class="table table-striped table-bordered view-table" id="<?= $this->tableId ?>">
                    </table>
                </div>

                <!-- # [ M O D A L S ] # -->
                <?php foreach ($this->modals as $modal): ?>
                    <?php require $modal; ?>
                <?php endforeach; ?>

            </div>
        </div>

        <?php require_once 'layout/footer.php'; ?>

        <!-- # [ S C R I P T ] # -->
        <script src="../public/js/modules/moduleRecord.js"></script>
        <?php if (!empty($this->moduleScript)): ?>
            <script src="../public/js/modules/<?= $this->moduleScript ?>.js"></script>
        <?php endif; ?>


<?php
    }

    /** Renderiza la vista de reporte utilizando los atributos de la clase.
     * 
     * - Verifica si el usuario tiene permiso para acceder a la vista.
     * - Incluye la cabecera (`header.php`).
     * - Muestra el título, los filtros y la tabla de datos.
     * - Carga el modal correspondiente (si existe).
     * - Incluye el pie de página (`footer.php`) y los scripts necesarios.
     */
    public function renderReport()
    {
        session_start();

        if (empty($_SESSION['user']) || !in_array($this->permission, $_SESSION['permisos'])) {
            header('Location: index.php');
            exit;
        }

        require_once 'layout/header.php';
?>

        <div id="container-report" class="card bg-transparent shadow-0 radius-10" data-module="<?= $this->module ?>">
            <div class="card-body mt-2">

                <!-- # [ T I T L E ] # -->
                <div class="row">
                    <div class="col-sm-12">
                        <h5 class="view-title">
                            <i class="fa-solid fa-chart-column me-1"></i>
                            <?= $this->title ?>
                        </h5>
                    </div>
                </div>

                <!-- # [ S E A R C H ] # -->
                <form class="validation" id="formReportSales" method="POST" action="" name="" novalidate="">
                    <div class="row justify-content-center">
                        <div class="col-sm-12 col-md-3 text-center pe-0">
                            <label class="mb-2" for="date">Selecciona un rango de fechas</label>
                            <div class="position-relative input-icon">
                                <input class="form-control" type="text" name="date" id="date" required>
                                <span class="position-absolute top-50 translate-middle-y"><i class="fa-solid fa-calendar-days font-20"></i>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-1 d-flex align-items-end ps-0">
                            <button type="submit" class="btn btn-success"><i class="fa-solid fa-magnifying-glass"></i></button>
                        </div>
                    </div>
                </form>

                <!-- # [ C O N T E N T ] # -->
                <div id="response" class="card radius-10 bg-transparent shadow-0 mt-4 d-none">
                    <div class="card-header py-3 border-3">
                        <div class="d-flex align-items-center justify-content-center">
                            <div>
                                <h6 class="mb-0 font-18">Resumen de Ventas <span class="mx-2">|</span> <span id="day"></span></h6>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- # [  W I D G E T S  ] # -->
                        <div class="row row-cols-1 row-cols-md-2 row-cols-xl-2 row-cols-xxl-4 mt-2 justify-content-center">
                            <!-- # [ W I D G E T   T O T A L ] # -->
                            <div class="col">
                                <div class="card radius-10 view-bg mb-2">
                                    <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="me-auto">
                                            <p class="mb-0 text-white font-16">Total Ventas</p>
                                            <h4 class="my-1 text-white">$<span id="total">0.00</span></h4>
                                        </div>
                                        <div class="font-50 text-white">
                                            <i class="fa-solid fa-sack-dollar"></i>
                                        </div>
                                    </div>
                                    </div>
                                </div>
                            </div>

                            <!-- # [ W I D G E T   W I N S ] # -->
                            <div class="col">
                                <div class="card radius-10 bg-gradient-ohhappiness mb-2">
                                    <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="me-auto">
                                            <p class="mb-0 text-white font-16">Ganancias</p>
                                            <h4 class="my-1 text-white">$<span id="earnings">0.00</span></h4>
                                        </div>
                                        <div class="font-50 text-white">
                                            <i class="fa-solid fa-cash-register"></i>
                                        </div>
                                    </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- # [  T A B L E  ] # -->
                        <div class="row mb-2">
                            <div class="col-sm-12 col-md-12">
                                <div class="table-responsive">
                                    <table id="module-table-report" class="table table-striped view-table"></table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- # [ M O D A L S ] # -->
                <?php foreach ($this->modals as $modal): ?>
                    <?php require $modal; ?>
                <?php endforeach; ?>

            </div>
        </div>

        <?php require_once 'layout/footer.php'; ?>

        <!-- # [ S C R I P T ] # -->
        <script src="../public/js/modules/moduleReports.js"></script>
        <?php if (!empty($this->moduleScript)): ?>
            <script src="../public/js/modules/<?= $this->moduleScript ?>.js"></script>
        <?php endif; ?>


<?php
    }
}