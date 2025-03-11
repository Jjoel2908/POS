<?php

/** Clase para generar las vistas del sistema dinámicamente */
class TemplateView
{
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
        $this->title = $config['title'] ?? 'Módulo';
        $this->icon = $config['icon'] ?? '';
        $this->permission = $config['permission'] ?? 0;
        $this->showAddButton = $config['showAddButton'] ?? true;
        $this->addAction = $config['addAction'] ?? '';
        $this->addIcon = $config['addIcon'] ?? 'fa-solid fa-plus';
        $this->addLabel = $config['addLabel'] ?? '';
        $this->tableId = $config['tableId'] ?? 'module-table';
        $this->modals = $config['modals'] ?? [];
        $this->moduleScript = $config['moduleScript'] ?? '';
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

        <div class="card radius-10">
            <div class="card-body p-4">

                <!-- # [ T I T L E ] # -->
                <div class="row">
                    <div class="col-sm-12">
                        <h5 class="view-title">
                            <i class="<?= $this->icon ?>"></i>
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
                    <table class="table table-striped view-table" id="<?= $this->tableId ?>">
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
        <script src="../public/js/modules/<?= $this->moduleScript ?>.js"></script>

<?php
    }
}
