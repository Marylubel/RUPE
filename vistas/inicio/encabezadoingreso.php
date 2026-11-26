<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Hugo 0.84.0">
    <title>Perfil Usuario</title>

    <link rel="canonical" href="https://getbootstrap.com/docs/5.0/examples/headers/">    

    <!-- Bootstrap core CSS -->
<link href="/RUPE/assets/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="/RUPE/assets/bootstrap-icons/bootstrap-icons.min.css">


    <style>
      .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        user-select: none;
      }

      @media (min-width: 768px) {
        .bd-placeholder-img-lg {
          font-size: 3.5rem;
        }
      }
    </style>

    
    <!-- Custom styles for this template -->
    <link href="/RUPE/assets/css/headers.css" rel="stylesheet">
  </head>
  <body>
    
<main>
  <h1 class="visually-hidden">RUPE</h1>

  <div class="container fixed-top" style="background-color: #387BA7;">
    <header class="d-flex flex-wrap justify-content-center py-2 mb-2 border-bottom">
      <a href="/" class="d-flex align-items-center mb-2 mb-md-0 me-md-auto text-dark text-decoration-none">
        <!--<svg class="bi me-2" width="40" height="32"><use xlink:href="#bootstrap"/></svg>-->
        <span class="fs-4 display-5 fw-bold text-white">RUPE</span>
      </a>

      <ul class="nav nav-pills">        
        <li class="nav-item"><a href="/RUPE/index.php?c=perfilusuario&a=Inicio" class="nav-link text-white">Perfil</a></li>
        <li class="nav-item"><a href="/RUPE/controladores/usuarios/reportesusu.php?accion=listar" class="nav-link text-white">Reportes</a></li>
        <li class="nav-item"><a href="/RUPE/index.php?c=localizacion&a=muro" class="nav-link text-white">Localizadas</a></li>
        <li class="nav-item"><a href="/RUPE/index.php?c=cerrarsesion&a=Inicio" class="nav-link text-white" 
        onclick="return confirm('¿Seguro de cerrar sesión?');">Cerrar Sesión</a></li>
      </ul>
      </ul>
    </header>
  </div>
