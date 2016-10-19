/*jshint unused:false*/
var ws;

function toLog(message) {
    'use strict';
    var pElt = document.getElementById('cm_log');
    pElt.innerHTML = new Date() + '; ' + message + '<br>' + pElt.innerHTML;
}

function handleCashmachineEvent(eventType, eventValue) {
    'use strict';
    var str = '';
    
    if (eventType === 'banknote') {
        str = 'Банкнота принята ' + eventValue;
    } else if (eventType === 'started') {
        str = 'Купюроприемник запущен';
    } else if (eventType === 'stopped') {
        str = 'Купюроприемник остановлен';
    } else if (eventType === 'error') {
        str = 'Ошибка купюроприемника ' + eventValue;
    } else {
        str = eventType;
    }

    toLog('Event: ' + str);
}


function handleFRResponse(result, obj) {
    'use strict';
    window.alert('Result: ' + result + '\n' + obj);
}

function DispatcherWebSocket() {
    'use strict';
    const DISPATCHER_URL = 'ws://192.168.3.216:8011'; 
    // var DISPATCHER_URL = 'ws://localhost:8011'; 
    if ('WebSocket' in window) {
        // Let us open a web socket
        ws = new WebSocket(DISPATCHER_URL);
        
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
       
       ws.onclose = function()
       { 
       };
    } else {
       // The browser doesn't support WebSocket
        window.alert('WebSocket NOT supported by your Browser!');
    }
}

/**
 * 
 * Блокировка/разблокировка купюрника
 *
 */
function setCashmachineEnabled(flag) {
    'use strict';
    if (flag === true) {
        ws.send('{"object": "cashmachine", "cmd": "enabled", "enable": true}');
    } else if (flag === false) {
        ws.send('{"object": "cashmachine", "cmd": "enabled", "enable": false}');
    }
}

/**
 * 
 * Полный функционал по созданию фискального чека
 *
 * Позиции задаются списком "название";"цена";"количество";, разделенных между собой символом '|'
 * Пример: товар 1;100;2;|товар2;300;1;
 */
function frPrintCheck(positions, summ) {
    'use strict';
    ws.send('{"object": "fr", "cmd": "printcheck", "text": "' + positions + '", "summ": ' + summ + '}');
}

/**
 * 
 * Запрос проверки состояния ФР
 * В ответ придут флаги - работает или нет, наличие бумаги
 * 
 */
function frGetState() {
    'use strict';
    ws.send('{"object": "fr", "cmd": "getstate"}');
}

/**
 * 
 * Печать X-отчета
 * 
 */
function frPrintXReport() {
    'use strict';
    ws.send('{"object": "fr", "cmd": "printxreport"}');
}

/**
 * 
 * Печать Z-отчета
 * 
 */
function frPrintZReport() {
    'use strict';
    ws.send('{"object": "fr", "cmd": "printzreports"}');
}


function getReqParams() {
    'use strict';
    var tmp = [];        // два вспомагательных
    var tmp2 = [];        // массива
    var param = [];

    if(location.search !== '') {
        tmp = (location.search.substr(1)).split('&');    // разделяем переменные
        for(var i=0; i < tmp.length; i++) {
            tmp2 = tmp[i].split('=');        // массив param будет содержать
            param[tmp2[0]] = tmp2[1];        // пары ключ(имя переменной)->значение
        }
    }
    
    return param;
}

function printCheck() {
    'use strict';
    frPrintCheck('Пирожок;300;1;|Лимонад;50;2;|Салат;100;1;', 500);
}

$(document).ready(function() {
    'use strict';
    $(document).contextmenu(function function_name() {
        return false;
    });

    DispatcherWebSocket();
});
