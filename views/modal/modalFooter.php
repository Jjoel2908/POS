                    </div>
                </div>
                <div class="modal-footer">
                    <div id="loadingSpinner" class="spinner-border text-success me-3" role="status" style="display: none;">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <div class="btn-group" role="group">
                        <button id="btnSave" type="submit" class="btn btn-warning fs-14 border-r1 px-4 radius-30" 
                            <?php echo !empty($onClickEvent) ? 'onclick="' . $onClickEvent . '"' : ''; ?>>
                        </button>
                        <button type="button" class="btn btn-danger fs-14 px-4 radius-30" data-bs-dismiss="modal"><i class="fa-solid fa-xmark me-1"></i> Salir</button>
                    </div>
                </div>
            </form>
        </div>   
    </div>   
</div>