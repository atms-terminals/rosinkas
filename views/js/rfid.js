/*jshint unused:false*/
/* global doAction, stopAjax : true*/
const RFID_URL = 'http://localhost:8012/index.php'; 

/////////////////////////////////////////////////////////////////////////////////////
// получение содержимого экрана с сервера
function getCard(params) {
    'use strict';

    var req = {
        action: 'read',
        timeout: params.timeout
    };

    var action = params.action || 'move',
        screen = params.screen || '1';

    // $('#loadingMessage').show();
    $.post(RFID_URL, req, function (response) {
        if (response.code === 0) {
            doAction(action, screen, {card: response.key});
        } else {
            doAction('move', 1, {});
        }
    }, 'json')
        .fail(function () {
            // скрываем сообщение "подождите"
            doAction('move', 1, {});
            stopAjax = 0;
        });
}
