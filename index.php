<?php

require_once __DIR__ . "/modelos/BasedeDatos.php";

//limpiar parametros de la URL para evitar inyecciones de código
$_GET['c'] = isset($_GET['c']) ? strtolower(trim($_GET['c'])) : 'inicio';
$_GET['a'] = isset($_GET['a']) ? trim($_GET['a']) : 'Inicio';

//Seguridad contra inclusion de Archivos locales
$controladorInput = (isset($_GET['c']) && $_GET['c'] !== null) ? strtolower(trim($_GET['c'])) : 'inicio';
$controladorInput = preg_replace('/[^a-z0-9_]/', '', $controladorInput);

$accionInput = (isset($_GET['a']) && $_GET['a'] !== null) ? trim($_GET['a']) : 'Inicio';


$rutaRaiz     = __DIR__ . "/controladores/{$controladorInput}.php";
$rutaUsuarios = __DIR__ . "/controladores/usuarios/{$controladorInput}.php";
$rutaAdmin = __DIR__ . "/controladores/administrador/{$controladorInput}.php";

if (file_exists($rutaRaiz)) {
    $archivoControlador = $rutaRaiz;
} elseif (file_exists($rutaUsuarios)) {
    $archivoControlador = $rutaUsuarios;
} elseif (file_exists($rutaAdmin)) {
    $archivoControlador = $rutaAdmin;
} else {
    $archivoControlador = null;
}

if ($archivoControlador !== null) {
    require_once $archivoControlador;
    $nombreClase = ucwords($controladorInput) . "Controlador";

    if (class_exists($nombreClase)) {
        $controlador = new $nombreClase();
    }
    //comprueba que el método exista en la clase
    if (method_exists($controlador, $accionInput)) {
        call_user_func(array($controlador, $accionInput));
    } else {
        //si no existe el método, se carga la acción por defecto
        call_user_func(array($controlador, "Inicio"));
    }
} else {
    //si no existe el archivo o 'c' está vacío, se envía a inicio
    require_once "controladores/inicio.php";
    $controlador = new InicioControlador();
    call_user_func(array($controlador, "Inicio"));
}




