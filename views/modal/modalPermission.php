<!-- # [ H E A D E R ] # -->
<?php 
   $formId = "formUserPermissions"; 
   $module = "Usuario";
?>

<?php require 'modalHeader.php'; ?>

<!-- # [ B O D Y ] # -->
<input class="form-control" type="hidden" name="id" id="id">
                  <ul id="permissions" class="list-group list-group-flush border-0">
                  </ul>

<!-- # [ F O O T E R ] # -->
<?php require 'modalFooter.php'; ?>