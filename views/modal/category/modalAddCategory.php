<!-- # [ A D D   C A T E G O R Y ] # -->
<div class="modal fade" id="modalAddCategory" tabindex="-1" role="dialog" aria-labelledby="modalAddCategoryLabel" aria-hidden="true">
   <div class="modal-dialog" role="document">
      <div class="modal-content">

         <!-- # [ H E A D E R ] # -->
         <div class="modal-header">
            <h5 class="modal-title" id="modalTitle"></h5>
         </div>

         <form class="validation" id="formAddCategory" method="POST" action="" name="" novalidate="">

            <!-- # [ B O D Y ] # -->
            <div class="modal-body">
               <div class="row px-2 gap-3">
                  <div class="col-md-12">
                     <label class="mb-1" for="categoria">Nombre de Categoría</label>
                     <div class="position-relative input-icon">
                        <input class="form-control" type="hidden" name="id" id="id">
                        <input class="form-control" type="text" name="categoria" id="categoria" maxlength="100" placeholder="Ingrese el nombre de categoría" required>
                        <span class="position-absolute top-50 translate-middle-y"><i class="bx bx-category"></i></span>
                     </div>
                  </div>
               </div>
            </div>

            <!-- # [ F O O T E R ] # -->
            <div class="modal-footer">
               <button id="btnSave" type="submit" class="btn btn-primary fs-14 rounded-0"></button>
               <button type="button" class="btn btn-danger fs-14 rounded-0" data-bs-dismiss="modal"><i class="fa-solid fa-xmark me-1"></i> Salir</button>
            </div>
         </form>
      </div>
   </div>
</div>