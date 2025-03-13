<div class="modal fade" id="modalRegister" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="modalRegisterLabel" aria-hidden="true">
    <div class="modal-dialog <?= $modalClass ?? "" ?>" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle"></h5>
            </div>  

            <form class="validation" id="<?= $formId ?>" method="POST" action="" name="" novalidate=""  data-module="<?= $module ?>">
                <div class="modal-body">
                    <div class="row px-2">