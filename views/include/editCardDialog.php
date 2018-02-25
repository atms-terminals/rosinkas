<div id="changeCardDialog" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title add card">Добавление карточки</h4>
                <h4 class="modal-title edit card">Изменение карточки</h4>
            </div>
            <div class="modal-body">
                <input type="text" class="form-control num card" value = '' placeholder='номер карточки' />
                <input type="text" class="form-control org card" value = '' placeholder='организация' />
                <input type="text" class="form-control address card" value = '' placeholder='адрес организации' />

            </div>
            <div class="modal-footer">
                <button data-toggle="modal" data-target="#changeCardDialog" class="btn btn-primary edit confirm">Изменить</button>
                <button data-toggle="modal" data-target="#changeCardDialog" class="btn btn-primary add confirm">Добавить</button>
            </div>
        </div>
    </div>
</div>
