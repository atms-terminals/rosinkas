/**
* запрос чего-то с сервера
**/
function get(action, $area, values) {
    'use strict';
    values = values || false;

    var sid = $('#sid').val();
    $.get(sid + '/admin/' + action, values, function(data) {
        $area.find('.resultArea').html(data);
    });
}

$(document).ready(function() {
    'use strict';

    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        switch (e.target.hash) {
            case '#hws': 
                get('getHwsState', $('#hws'));
                break;
            case '#collections': 
                get('getCollections', $('#collections'));
                break;
            case '#priceGroup': 
                get('getPriceGroup', $('#priceGroup'), {active: $('#priceStatus').prop('checked') ? 1 : 0});
                break;
            case '#admin': 
                get('getTerminals', $('#terminals'));
                get('getUsers', $('#users'));
                break;
        }
    });

    // разрешение/запрещение услуги
    $(document).on('click', '.serviceItem', function() {
        var sid = $('#sid').val(),
            req = {
                id: $(this).attr('id'), 
                status: $(this).prop('checked') ? 1 : 0
            };

        $.post(sid + '/admin/setPriceGroupStatus', req, function() {

        }, 'json')
            .fail(function(){
                get('getPriceGroup', $('#priceGroup'));
            });
    });

    // показ услуг
    $(document).on('click', '#priceStatus', function() {
        get('getPriceGroup', $('#priceGroup'), {active: $(this).prop('checked') ? 1 : 0});
    });

    // запрос инкассаций
    $('#refreshCollections').click(function(event) {
        event.preventDefault();
        get('getCollections', $('#collections'));
    });

    // запрос статусов оборудования
    $('#refreshHwsStatus').click(function(event) {
        event.preventDefault();
        get('getHwsState', $('#hws'));
    });

    // запрос авансов по карте
    $('#getPrepaid').click(function(event) {
        event.preventDefault();
        get('getPrepaidStatus', $('#prepaid'), {searchStr: $('#searchStr').val()});
    });

    // изменение авансов по карте
    $(document).on('click', '#changePrepaid', function(event) {
        event.preventDefault();
        var sid = $('#sid').val(),
            req = {
                card: $('#changePrepaymentDialog .card').val(), 
                amount: $('#changePrepaymentDialog .amount').val()
            };

        $.post(sid + '/admin/changePrepaid', req, function() {
            $('#getPrepaid').trigger('click');
        }, 'json')
            .fail(function(){
                get('getPrepaidStatus', $('#prepaid'), {searchStr: $('#searchStr').val()});
            });
    });

    // показ диалога изменения аванса
    $(document).on('click', '.changePrepayment', function(event) {
        event.preventDefault();
        var $tr = $(this).closest('tr');
        $('#changePrepaymentDialog .amount').val($tr.find('span.amount').text());
        $('#changePrepaymentDialog .card').val($tr.find('span.card').text());
    });

    // изменение статуса терминала
    $(document).on('click', '.changeStatus', function(event) {
        event.preventDefault();
        var sid = $('#sid').val(),
            $this = $(this),
            $tr = $this.closest('tr'),
            req = {
                id: $tr.find('.id').val(), 
                status: $this.hasClass('enable') ? 1 : 0
            };

        $.post(sid + '/admin/changeStatus', req, function() {
            if ($this.hasClass('user')) {
                get('getUsers', $('#users'));
            } else {
                get('getTerminals', $('#terminals'));
            }
        }, 'json')
            .fail(function(){
                if ($this.hasClass('user')) {
                    get('getUsers', $('#users'));
                } else {
                    get('getTerminals', $('#terminals'));
                }
            });
    });

    // окно добавление/редактирования пользователя
    $(document).on('click', '.changeUser', function(event) {
        event.preventDefault();
        var $this = $(this),
            $tr = $this.closest('tr'),
            action = '';

        if ($this.hasClass('user')) {
            $('#changeUserDialog .user').show();
            $('#changeUserDialog .terminal').hide();
            action = 'User';
        } else {
            $('#changeUserDialog .user').hide();
            $('#changeUserDialog .terminal').show();
            action = 'Terminal';
        }

        $('#changeUserDialog input[type=text]').val('');
        $('#changeUserDialog .id').val(0);
        $('#changeUserDialog .confirm').show();

        if ($this.hasClass('add')) {
            action = 'add' + action;
            $('#changeUserDialog .edit').hide();
        } else {
            action = 'edit' + action;
            $('#changeUserDialog .add').hide();
            $('#changeUserDialog .id').val($tr.find('.id').val());

            if ($this.hasClass('user')) {
                $('#changeUserDialog .login').val($tr.find('.login').text());
            } else {
                $('#changeUserDialog .address').val($tr.find('.address').text());
                $('#changeUserDialog .ip').val($tr.find('.ip').text());
            }
        }

        $('#changeUserDialog .action').val(action);

    });

    $('#changeUserDialog .confirm').click(function(event) {
        event.preventDefault();
        var sid = $('#sid').val(),
            action = $('#changeUserDialog .action').val(), 
            req = {
                id: $('#changeUserDialog .id').val(), 
                ip: $('#changeUserDialog .ip').val(), 
                address: $('#changeUserDialog .address').val(), 
                login: $('#changeUserDialog .login').val(), 
            };

        $.post(sid + '/admin/' + action, req, function() {
            if (action === 'addUser' || action === 'editUser') {
                get('getUsers', $('#users'));
            } else {
                get('getTerminals', $('#terminals'));
            }
        }, 'json')
            .fail(function(){
            if (action === 'addUser' || action === 'editUser') {
                    get('getUsers', $('#users'));
                } else {
                    get('getTerminals', $('#terminals'));
                }
            });

    });

    // окно ввода нового пароля
    $(document).on('click', '.changeUserPassword', function(event) {
        event.preventDefault();
        var $tr = $(this).closest('tr');
        $('#changePasswordDialog .password').val('');
        $('#changePasswordDialog .modal-body .id').val($tr.find('.id').val());
    });

    // сохранение нового пароля
    $(document).on('click', '#changePassword', function(event) {
        event.preventDefault();
        var sid = $('#sid').val(),
            action = 'changePassword', 
            req = {
                id: $('#changePasswordDialog .id').val(), 
                new: $('#changePasswordDialog .password').val(), 
            };

        $.post(sid + '/admin/' + action, req, function() {
        }, 'json')
            .fail(function(){
            });

    });

    // окно подтверждения удаления
    $(document).on('click', '.confirmDelete', function(event) {
        event.preventDefault();
        var $this = $(this);
        if ($this.hasClass('user')) {
            $('#confirmDeleteDialog .modal-body span').html('пользователя');
            $('#confirmDeleteDialog .modal-body .id').val($this.siblings('.id').val());
            $('#confirmDeleteDialog .modal-body .action').val('deleteUser');
        } else if ($this.hasClass('terminal')) {
            $('#confirmDeleteDialog .modal-body span').html('терминал');
            $('#confirmDeleteDialog .modal-body .id').val($this.siblings('.id').val());
            $('#confirmDeleteDialog .modal-body .action').val('deleteTerminal');
        }
    });

    // удаление
    $(document).on('click', '#deleteThis', function(event) {
        event.preventDefault();
        var sid = $('#sid').val(),
            action = $('#confirmDeleteDialog .action').val(), 
            req = {
                id: $('#confirmDeleteDialog .id').val(), 
            };

        $.post(sid + '/admin/' + action, req, function() {
            if (action === 'deleteUser') {
                get('getUsers', $('#users'));
            } else {
                get('getTerminals', $('#terminals'));
            }
        }, 'json')
            .fail(function(){
                if (action === 'deleteUser') {
                    get('getUsers', $('#users'));
                } else {
                    get('getTerminals', $('#terminals'));
                }
            });

    });
});