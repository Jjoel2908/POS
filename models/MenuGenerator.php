<?php
require_once '../config/Connection.php';
class MenuGenerator extends Connection
{
   public function __construct() {}

   private function generateInitialMenu(): string
   {
      return   '<li>
                  <a href="dashboard.php">
                     <div class="parent-icon"><i class="fa-solid fa-gauge-high me-1"></i></div>
                     <div class="menu-title">Inicio</div>
                  </a>
               </li>';
   }

   public function getMenu(): string
   {
      $permissions = self::selectAll("permisos", false);
      $submodules = [
         'ventas'        => '',
         'producción'    => '',
         'gastos'        => '',
         'personal'      => '',
         'pagos'         => '',
         'configuración' => '',
         'reportes'      => '',
      ];

      $modules = $this->generateInitialMenu();

      foreach ($permissions as $permission) {
         if ($permission['id'] == 1) continue;
         if (!in_array($permission['id'], $_SESSION['permisos'])) continue;

         $icon = $permission['icono'];
         $menuItem = '<li><a href="' . $permission['archivo'] . '">
                     <i class="fa-solid fa-chevron-right me-3"></i>' . $permission['nombre'] . '
                  </a></li>';

         if (isset($submodules[$icon])) {
            $submodules[$icon] .= $menuItem;
         } else {
            $modules .= '<li>
                        <a href="' . $permission['archivo'] . '">
                            <div class="parent-icon"><i class="' . $icon . '"></i></div>
                            <div class="menu-title">' . $permission['nombre'] . '</div>
                        </a>
                     </li>';
         }
      }

      $menuStructure = [
         'ventas'        => 'fa-store',
         'producción'    => 'fa-industry',
         'gastos'        => 'fa-coins',
         'personal'      => 'fa-users-gear',
         'pagos'        => 'fa-file-invoice-dollar',
         'configuración' => 'fa-gear',
         'reportes'       => 'fa-chart-column',
      ];

      foreach ($submodules as $key => $content) {
         if (!empty($content)) {
            $modules .= '<li>
                         <a class="has-arrow" href="javascript:;">
                             <div class="parent-icon"><i class="fa-solid ' . $menuStructure[$key] . ' me-1"></i></div>
                             <div class="menu-title">' . ucfirst($key) . '</div>
                         </a>
                         <ul>' . $content . '</ul>
                      </li>';
         }
      }

      return $modules;
   }
}
