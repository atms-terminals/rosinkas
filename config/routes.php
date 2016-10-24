<?php
return array(
    '.*?/(terminal.php)' => 'terminal/index',
    '.*?/(admin.php)' => 'admin/index',
    '.*?/ajax/move' => 'ajax/move',
    '.*?/ajax/getBalance' => 'ajax/getBalance',
    '.*?/ajax/getMoneyScreen' => 'ajax/getMoneyScreen',
    '.*?/ajax/pay' => 'ajax/pay',
    '.*?/ajax/writeLog' => 'ajax/writeLog',
    '.*?/ajax/collection' => 'ajax/collection',

    '.*?/admin/getHwsState' => 'admin/getHwsState',

    '.*?/admin/getTerminals' => 'admin/getTerminals',
    '.*?/admin/changeStatus' => 'admin/changeStatus',
    '.*?/admin/deleteTerminal' => 'admin/deleteUser',
    '.*?/admin/addUser' => 'admin/addUser',
    '.*?/admin/editUser' => 'admin/editUser',

    '.*?/admin/getUsers' => 'admin/getUsers',
    '.*?/admin/deleteUser' => 'admin/deleteUser',
    '.*?/admin/addTerminal' => 'admin/addUser',
    '.*?/admin/editTerminal' => 'admin/editUser',

    '.*?/admin/getPrepaidStatus' => 'admin/getPrepaidStatus',
    '.*?/admin/changePrepaid' => 'admin/changePrepaid',
);
