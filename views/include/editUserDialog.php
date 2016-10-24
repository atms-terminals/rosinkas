    <div id="changeUserDialog" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title add user">Добавление пользователя</h4>
                    <h4 class="modal-title edit user">Изменение пользователя</h4>
                    <h4 class="modal-title add terminal">Добавление терминала</h4>
                    <h4 class="modal-title edit terminal">Изменение терминала</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" class="form-control id" />
                    <input type="hidden" class="form-control action" />

                    <input type="text" class="form-control ip terminal" value = '' placeholder='ip-адрес терминала' />
                    <input type="text" class="form-control login user" value = '' placeholder='логин пользователя' />
                    <input type="text" class="form-control address terminal" value = '' placeholder='местонахождение терминала' />

                </div>
                <div class="modal-footer">
                    <button data-toggle="modal" data-target="#changeUserDialog" class="btn btn-primary edit confirm">Изменить</button>
                    <button data-toggle="modal" data-target="#changeUserDialog" class="btn btn-primary add confirm">Добавить</button>
                </div>
            </div>
        </div>
    </div>
