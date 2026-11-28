    
<style>
  .fondo-perro {
    background-image: url('/RUPE/assets/imagenes/perro1.jpeg'); 
    background-size: cover;      /* La imagen cubre toda la sección */
    background-position: center;   /* Centrar imagen */
    background-repeat: no-repeat;  /* Evita que se duplique la imagen */
  }     
</style>

<main>
<!-- Bienvenida --> 
<section class="py-5 text-center container-fluid fondo-perro text-white shadow-sm">
    <div class="row py-lg-5">
      <div class="col-lg-6 col-md-8 mx-auto">
        <h1 class="display-1 fw-bold text-shadow">RUPE</h1>
        <p class="lead text-white fw-bold fs-4">REGISTRO ÚNICO DE PERROS EXTRAVIADOS</p>
        <p class="mt-4">
          <a href="index.php?c=iniciarsesion" class="btn btn-lg px-4 me-2 fw-bold shadow" style="background-color: #75B0D7;">Iniciar Sesión</a>
          <a href="index.php?c=registro" class="btn btn-lg px-4 me-2 fw-bold shadow" style="background-color: #75B0D7;">Registrarse</a>
        </p>
      </div>
    </div>
  </section>
  <!-- Presentación y finalidad de RUPE -->
  <section class="py-1 bg-white border-bottom">
    <div class="container text-center">
      <div class="col-lg-9 mx-auto">
        <h2 class="fw-bold mb-2" style="color: #0f4264;">Bienvenido a RUPE</h2>
        <p class="lead mb-0" style="font-size: 1.15rem; color: black">
          Una plataforma diseñada para registrar a tus mascotas, generar carteles de búsqueda con <strong>código QR seguro</strong> 
          (protegiendo tus datos personales) y conectar de forma inmediata a quienes encuentran un perrito con su familia.
        </p>
      </div>
    </div>
  </section>

  <!-- Sección informativa de tarjetas -->
  <div class="album py-2 bg-light">
    <div class="container">
      <div class="row row-cols-1 row-cols-md-2 g-4 justify-content-center">

        <!-- Tarjeta 1 -->
        <div class="col">
          <div class="card shadow-sm h-100 border-0">
            <img src="assets/imagenes/Sebusca.jpg" class="card-img-top mx-auto mt-3 rounded px-3" alt="Mascota extraviada" style="height: 225px; object-fit: cover; width: 100%;">
            <div class="card-body d-flex flex-column justify-content-between p-4">
              <div>
                <p class="text-center fw-bold fs-5 mb-3" style="color: #0f4264;">¿Se extravió tu mascota?</p>    
                <p class="card-text text-muted" style="text-align: justify; line-height: 1.6;">
                  Crea una cuenta en <strong>RUPE</strong> y levanta el reporte de extravío de tu mascota. Obten una ficha de búsqueda 
                  con <strong>código QR</strong> y sin mostrar tus datos personales<br>
                  También puedes probar la aplicación sin crear una cuenta. Ingresa con el botón <strong>"Levantar reporte de extravío sin cuenta"</strong>
                </p>
              </div>
              <div class="mt-4">
                <a href="/RUPE/index.php?c=reporteinvitado&a=Inicio" class="btn w-100 fw-bold py-2 shadow-sm text-white" style="background-color:  #f72020;">Levantar Reporte de Extravío sin cuenta</a>
              </div>
            </div>
          </div>
        </div>

        <!-- Tarjeta 2 -->
        <div class="col">
          <div class="card shadow-sm h-100 border-0">            
            <img src="assets/imagenes/localizados.jpg" class="card-img-top mx-auto mt-3 rounded px-3" alt="Mascota localizada" style="height: 225px; object-fit: cover; width: 100%;">
            <div class="card-body d-flex flex-column justify-content-between p-4">
              <div>
                <p class="text-center fw-bold fs-5 mb-3" style="color: #0f4264;">¿Quieres saber si alguien ha visto a tu mascota?</p>    
                <p class="card-text text-muted" style="text-align: justify; line-height: 1.6;">
                  Entra al catálogo de mascotas localizadas y si la ves, puedes levantar el reporte de extravío para obtener el folio de búsqueda<br> 
                  Ingresa el folio en la ficha de localizado y obtendrás el dato de la persona que tiene a tu mascota.<br>
                  Ingresa con el botón <strong>"Mascotas Localizadas"</strong>
                </p>
              </div>
              <a href="/RUPE/index.php?c=localizacion&a=muro" class="btn w-100 fw-bold py-2 shadow-sm text-white" style="background-color:  #387BA7;">Mascotas Localizadas</a>
            </div>
          </div>
        </div>   
      </div> 
    </div> 
  </div> 
</main>

