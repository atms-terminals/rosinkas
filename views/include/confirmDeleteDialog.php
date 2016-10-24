    <div id="confirmDeleteDialog" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Подтверждение</h4>
                </div>
                <div class="modal-body bg-danger">
                    <p>
                        Вы действительно хотите удалить <span class="target"></span>
                    </p>
                    <input type="hidden" class="id" />
                    <input type="hidden" class="action" />
                </div>
                <div class="modal-footer">
                    <button data-toggle="modal" data-target="#confirmDeleteDialog" class="btn btn-primary" id='deleteThis'>Удалить</button>
                    <button data-toggle="modal" data-target="#confirmDeleteDialog" class="btn btn-primary">Отмена</button>
                </div>
            </div>
        </div>
    </div>
