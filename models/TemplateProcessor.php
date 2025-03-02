<?php
class TemplateProcessor {

   public function getUserPermissions() {
      $html = "";
      $menu = json_decode(file_get_contents(dirname(__FILE__) . '/../public/json/menu.json'), TRUE);

      foreach ($menu as $key_menu => $permissions) {
         foreach ($permissions as $key_permissions => $permission) {
            foreach($permission as $key_permission => $modules) {

               
                   $html .=  '<li class="list-group-item d-flex bg-transparent align-items-center border-0">
                                 <div class="form-check form-switch form-check-success">
                                    <input class="form-check-input me-2" type="checkbox" role="switch" id="flexSwitchCheckSuccess" checked="" value="' . $key_permission . '">
                                    <label class="form-check-label" for="flexSwitchCheckSuccess">' . $modules['title'] . '</label>
                                 </div>
                              </li>';
            
            }
         }
      }

      echo $html;
   }
}

?>