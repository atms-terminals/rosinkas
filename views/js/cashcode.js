/*jshint unused:false*/
/*global doAction*/
var ws;
// const DISPATCHER_URL = 'ws://192.168.3.216:8011'; 
const DISPATCHER_URL = 'ws://localhost:8011'; 

/**
 * Блокировка/разблокировка купюрника
 */
function setCashmachineEnabled(flag) {
    'use strict';
    if (ws.readyState === ws.OPEN) {
        if (flag === true) {
            ws.send('{"object": "cashmachine", "cmd": "enabled", "enable": true}');
        } else if (flag === false) {
            ws.send('{"object": "cashmachine", "cmd": "enabled", "enable": false}');
        }
    }
}

/**
 * Запрос проверки состояния ФР
 * В ответ придут флаги - работает или нет, наличие бумаги
 */
function frGetState() {
    'use strict';
    if (ws.readyState === ws.OPEN) {
        ws.send('{"object": "fr", "cmd": "getstate"}');
    }
}

/**
 * Печать Z-отчета
 */
function frPrintZReport() {
    'use strict';
    if (ws.readyState === ws.OPEN) {
        ws.send('{"object": "fr", "cmd": "printzreports"}');
    }
}

/**
 * обработчик событий купюрника
 */
function handleCashmachineEvent(eventType, eventValue) {
    'use strict';
    var event;
    
    if (eventType === 'banknote') {
        var currAmount = $('.amount').val();
        currAmount += eventValue;

        $('.amount').val(currAmount);
        $('.amountScreen').html(currAmount);

        console.log('Купюроприемник: банкнота принята ' + eventValue);
    } else if (eventType === 'started') {
        console.log('Купюроприемник: запущен');
        event = {
            type: 'cash',
            message: 'Купюроприемник: запущен (связь восстановилась)'
        };
        doAction('writeLog', 1, event);
    } else if (eventType === 'stopped') {
        console.log('Купюроприемник: остановлен');
        event = {
            type: 'cash',
            message: 'Купюроприемник: остановлен (пропала до него связь)'
        };
        doAction('writeLog', 8, event);
    } else if (eventType === 'DropCassetteOutOfPosition') {
        // инкассация
        frPrintZReport();
        console.log('Купюроприемник: Кассета изъята');
        doAction('collection', 9);
    } else if (eventType === 'cassetInserted') {
        console.log('Купюроприемник: Кассета вставлена');
        // конец инкассации
        event = {
            type: 'cash',
            message: 'Купюроприемник: Кассета вставлена'
        };
        doAction('writeLog', 1, event);
    } else if (eventType === 'writeLog') {
        console.log('Купюроприемник: ошибка ' + eventValue);
        event = {
            type: 'cash',
            message: 'Купюроприемник: ошибка ' + eventValue
        };
        doAction('writeLog', 8, event);
    } else {
        console.log('Купюроприемник: неизвестная ошибка ' + eventType);
        event = {
            type: 'cash',
            message: 'Купюроприемник: неизвестная ошибка ' + eventType
        };
        doAction('writeLog', 8, event);
    }
}

/**
 * Обработчик событий принтера
 */
function handleFRResponse(result, obj) {
    'use strict';
    if (result !== 'ok') {
        var event = {
            type: 'fr',
            message: 'ФР: ' + result + '\n' + obj
        };
        doAction('writeLog', 8, event);
    }
    console.log('ФР: ' + result + '\n' + obj);
}

/**
 * Инициализация купюрника
 */
function DispatcherWebSocket() {
    'use strict';
    if ('WebSocket' in window) {
        // Let us open a web socket
        ws = new WebSocket(DISPATCHER_URL);
        
        ws.onerror = function function_name(argument) {
            var event = {
                type: 'webSocket',
                message: 'ошибка'
            };
            doAction('writeLog', 8, event);
        };

        ws.onopen = function() {
            var msg = '{"object": "common", "cmd": "connect"}';
            ws.send(msg);
        };
        
        ws.onmessage = function (evt) { 
            var eventObj;
            /* jshint ignore:start */
            eventObj = eval('(' + evt.data + ')');
            /* jshint ignore:end */
          
            if (eventObj.object === 'common' && eventObj.result === 'connected') {
                // init();
            }
          
            if (eventObj.object === 'cashmachine') {
                if (eventObj.event != null) {
                    handleCashmachineEvent(eventObj.event, eventObj.eventValue);
                }
            } else if (eventObj.object === 'fr') {
                handleFRResponse(eventObj.result, evt.data);
            }
        };
       
       ws.onclose = function() {};
    } else {
        var event = {
            type: 'webSocket',
            message: 'WebSocket NOT supported by your Browser!'
        };
        doAction('writeLog', 8, event);
       // The browser doesn't support WebSocket
        // window.alert('WebSocket NOT supported by your Browser!');
    }
}

/**
 * Полный функционал по созданию фискального чека
 *
 * Позиции задаются списком "название";"цена";"количество";, разделенных между собой символом '|'
 * Пример: товар 1;100;2;|товар2;300;1;
 */
// function frPrintCheck(positions, summ) {
//     'use strict';
//     if (ws.readyState === ws.OPEN) {
//         ws.send('{"object": "fr", "cmd": "printcheck", "text": "' + positions + '", "summ": ' + summ + '}');
//     }
// }

// /**
//  * Печать X-отчета
//  */
// function frPrintXReport() {
//     'use strict';
//     ws.send('{"object": "fr", "cmd": "printxreport"}');
// }

// // печать позиции
// function printCheck(name, price, cnt, summ) {
//     'use strict';
//     frPrintCheck(name + ';' + price + ';' + cnt + ';', summ);
// }

$(document).ready(function() {
    'use strict';
    $(document).contextmenu(function function_name() {
        // return false;
    });

    DispatcherWebSocket();
});
