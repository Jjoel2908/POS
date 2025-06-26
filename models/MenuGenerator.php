<?php
require_once '../config/Connection.php';
class MenuGenerator extends Connection
{
   public function __construct()
   {
   }

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
      $permissions = self::selectAll("permisos");
      $submodules  = '';

      $modules = $this->generateInitialMenu();

      foreach ($permissions as $permission) {

         if (!in_array($permission['id'], $_SESSION['permisos'])) continue;

         if ($permission['icono'] != 'submodulo') {
            $modules .= '<li>
                           <a href="' . $permission['archivo'] . '">
                              <div class="parent-icon"><i class="' . $permission['icono'] . '"></i>
                              </div>
                              <div class="menu-title">' . $permission['nombre'] . '</div>
                           </a>
                        </li>';
         } else {
            $submodules .= '<li> <a href="' . $permission['archivo'] . '"><i class="fa-solid fa-chevron-right me-3"></i>' . $permission['nombre'] . '</a></li>';
         }
      }

      if (count(array_intersect([11, 12, 13], $_SESSION['permisos'])) > 0) {
         $modules .= '<li>
                        <a class="has-arrow" href="javascript:;">
                           <div class="parent-icon"><i class="fa-solid fa-chart-column me-1"></i>
                           </div>
                           <div class="menu-title">Reportes</div>
                        </a>
                        <ul>
                        ' . $submodules . '
                        </ul>
                     </li>';
      }

      return $modules;
   }
}
