<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>WherEx - Ventas</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/prism-okaidia.css">
    <link rel="stylesheet" href="css/custom.min.css">
    <!-- Global Site Tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-23019901-1"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());

      gtag('config', 'UA-23019901-1');
    </script>
  </head>
  <body>
    <div class="navbar navbar-expand-lg fixed-top navbar-dark bg-primary">
      <div class="container">
        <a href="../" class="navbar-brand">WherEx - Ventas</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarResponsive">
          <ul class="navbar-nav">
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" id="ventas">Ventas <span class="caret"></span></a>
              <div class="dropdown-menu" aria-labelledby="ventas">
                <a class="dropdown-item" href="{{ url('/') }}">Registro de venta</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="{{ url('/ventaReporte') }}">Reporte</a>
              </div>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="{{ url('/clienteReporte') }}">Clientes</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="{{ url('/productoReporte') }}">Productos</a>
            </li>
          </ul>
          <ul class="navbar-nav ms-md-auto">
            <li class="nav-item">
              <a target="_blank" rel="noopener" class="nav-link" href="https://wherex.cl/"><i class="fa fa-twitter"></i> WherEx</a>
            </li>
          </ul>
        </div>
      </div>
    </div>
