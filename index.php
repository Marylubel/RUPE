<?php

require_once "modelos/BasedeDatos.php";

//limpiar parametros de la URL para evitar inyecciones de código
$_GET['c'] = isset($_GET['c']) ? strtolower(trim($_GET['c'])) : 'inicio';
$_GET['a'] = isset($_GET['a']) ? trim($_GET['a']) : 'Inicio';

//Seguridad contra inclusion de Archivos locales
$controladorInput = (isset($_GET['c']) && $_GET['c'] !== null) ? strtolower(trim($_GET['c'])) : 'inicio';
$controladorInput = preg_replace('/[^a-z0-9_]/', '', $controladorInput);

$accionInput = (isset($_GET['a']) && $_GET['a'] !== null) ? trim($_GET['a']) : 'Inicio';

//Interpretar el controlador y la acción
//$archivoControlador = "controladores/{$controladorInput}.php";

//if (file_exists($archivoControlador)) {
  //  require_once $archivoControlador;
  //  $nombreClase = ucwords($controladorInput) . "Controlador";

    //if (class_exists($nombreClase)) {
      //  $controlador = new $nombreClase();
    //}
    $rutaRaiz     = "controladores/{$controladorInput}.php";
$rutaUsuarios = "controladores/usuarios/{$controladorInput}.php";

if (file_exists($rutaRaiz)) {
    $archivoControlador = $rutaRaiz;
} elseif (file_exists($rutaUsuarios)) {
    $archivoControlador = $rutaUsuarios;
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




//require_once "modelos/BasedeDatos.php";
//require_once "modelos/registro.php";

//obtener y limpiar los valores de las variables de entrada
//$controladorInput = isset($_GET['c']) ? strtolower(trim($_GET['c'])) : '';
//$accionInput      = isset($_GET['a']) ? trim($_GET['a']) : 'Inicio';

//el controlador y la acción se limpian para evitar inyecciones de código
//$controladorInput = preg_replace('/[^a-z0-9_]/', '', $controladorInput);
//$accionInput = preg_replace('/[^a-z0-9_]/', '', $accionInput);
//$archivoControlador = "controladores/{$controladorInput}.php";

// Valida que el archivo del controlador exista antes de requerirlo
//if (!empty($controladorInput) && file_exists($archivoControlador)) {
    //require_once $archivoControlador;
    
    //$nombreClase = ucwords($controladorInput) . "Controlador";
    //$controlador = new $nombreClase();
    
    // Validar que el método exista en la clase
    //if (method_exists($controlador, $accionInput)) {
        //call_user_func(array($controlador, $accionInput));
    //}else {
        //No existe, se carga la acción por defecto
       // call_user_func(array($controlador, "Inicio"));
    //}
//} else {
    //si no existe el archivo o 'c' está vacío, se envía a inicio
    //require_once "controladores/inicio.php";
    //$controlador = new InicioControlador();
    //call_user_func(array($controlador, "Inicio"));
//}

//if (!isset($_GET['c']) || empty($_GET['c'])) {  //lee la variable "c" contenga o no valores
  //  require_once "controladores/inicio.php";
    //$controlador = new InicioControlador();
    //call_user_func(array($controlador, "Inicio")); //llama al metodo inicio de la clase InicioControlador
//} else {
  //  $controlador = strtolower($_GET['c']);
   // if (file_exists("controladores/$controlador.php")) {  // Validar el archivo del controlador exista antes de requerirlo
    //require_once "controladores/$controlador.php";
    //$controlador = ucwords($controlador) . "Controlador";
    //$controlador = new $controlador;
    //$accion = isset($_GET['a']) ? $_GET['a'] : "Inicio";
    //call_user_func(array($controlador, $accion));
//}else {
       // require_once "controladores/inicio.php"; //si no existe el controlador, se envia al inicio
       // $controlador = new InicioControlador(); 
       // call_user_func(array($controlador, "Inicio"));

//}
//}