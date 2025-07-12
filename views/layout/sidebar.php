<div class="sidebar-wrapper" data-simplebar="true">
   <div class="sidebar-header">
      <div>
         <img src="../../public/images/gastalon.png" class="logo-icon" alt="logo icon">
      </div>
      <div>
         <h4 class="logo-text text-helvetica"><?php echo WEB_NAME ?></h4>
      </div>
   </div>

   <!-- # [ M E N Ú   P R I N C I P A L ] # -->
   <ul class="metismenu pt-3" id="menu">

      <?php    
         $MenuGenerator = new MenuGenerator();
         echo $MenuGenerator->getMenu(); 
      ?>
   </ul>
</div>