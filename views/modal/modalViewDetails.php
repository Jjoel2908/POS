<!-- # [ O P E N   C A S H B O X ] # -->
<div class="modal fade" id="modalViewDetails" tabindex="-1" role="dialog" aria-labelledby="modalViewDetailsLabel" aria-hidden="true">
   <div class="modal-dialog modal-xl" role="document">
      <div class="modal-content">

         <!-- # [ H E A D E R ] # -->
         <div class="modal-header border-0">
            <h5 class="modal-title my-1" id="modalTitle">
               <span class="badge bg-gradient-cosmic font-16 fw-normal py-2">
                  Detalles de <span id="detalle"> </span>
                  <span class="mx-2">|</span>
                  Total: $<span id="totalDetails"></span>
               </span>
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
         </div>

         <!-- # [ B O D Y ] # -->
         <div class="modal-body pt-0">
            <table class="table table-striped view-table" id="table-view-details"></table>
         </div>
      </div>
   </div>
</div>