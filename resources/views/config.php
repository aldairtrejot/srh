<?php
//ARRAY DE ROLES DE USUARIO
$userRole = session('SESSION_ROLE_USER', []);

//ARRAY DE ROLES SEGUN NECESIDAD
//ARRAY DE ADMINISTRACION
$adminRole = [
    config('custom_config.ADM_TOTAL'),
];

//ARRAY DE ADMINISTRACION DE ROLES DE CORRESPONDENCIA
$letterRoleAdmin = [
    config('custom_config.ADM_TOTAL'),
    config('custom_config.COR_TOTAL'),
];

//ARRAY DE CRH CORRESPONDENCIA
$letterRoleCrh = [
    config('custom_config.ADM_TOTAL'),
    config('custom_config.COR_CRH'),
];

//ARRAY GENERAL DE CORRESPONDENCIA DE ROLES
$letterRole = [
    config('custom_config.ADM_TOTAL'),
    config('custom_config.COR_TOTAL'),
    config('custom_config.COR_USUARIO'),
    config('custom_config.COR_ENLACE')
];

// ARRAY DE ROLES DE TITULARES POR CORRESPONDENCIA
$letterUsuario = [
    config('custom_config.ADM_TOTAL'),
    config('custom_config.COR_TOTAL'),
    config('custom_config.COR_USUARIO')
];

$coursesRole = [
    config('custom_config.ADM_TOTAL'),
    config('custom_config.COR_TOTAL'),
    config('custom_config.COR_USUARIO'),
    config('custom_config.COR_ENLACE')
];

//VALORES RESULTANTES
$adminMatch = !empty(array_intersect($userRole, $adminRole));
$letterMatch = !empty(array_intersect($userRole, $letterRole));
$coursesMatch = !empty(array_intersect($userRole, $coursesRole));
$letterAdminMatch = !empty(array_intersect($userRole, $letterRoleAdmin));
$letterCRH = !empty(array_intersect($userRole, $letterRoleCrh));
$letterUSERS = !empty(array_intersect($userRole, $letterUsuario));