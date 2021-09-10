<?php

$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => array(
        'driver' => 'pdo_sqlsrv',
        'host' => 'ARTHUR\SQLEXPRESS',
        'dbname' => 'UserPanel',
        'user' => 'sa',
        'password' => '78124770',
    ),
));

?>