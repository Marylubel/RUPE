<title>Perfil Administrador</title>

<link rel="canonical" href="https://getbootstrap.com/docs/5.0/examples/checkout/">



<!-- Bootstrap core CSS -->
<link href="/RUPE/assets/css/bootstrap.min.css" rel="stylesheet">

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

<!-- Estilos de la plantilla -->
<link href="/RUPE/assets/css/form-validation.css" rel="stylesheet">

<body class="bg-light">

  <div class="container">
    <main>
      <form action="/RUPE/controladores/usuarios/personalusu.php" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
        <section class="py-5">
          <div class="col-md-8 mx-auto bg-white p-3 rounded border shadow-sm">

            <!-- Espacio de la Foto de Perfil e información personal -->
            <div class="row align-items-center justify-content-center g-3">
              <div class="col-auto text-center">
                <div class="mb-2 position-relative d-inline-block">
                  <img id="img-visualizacion" class="d-block mx-auto rounded-circle border shadow-sm"
                    src="<?= htmlspecialchars(!empty($perfil['foto']) ? $perfil['foto'] : ($_SESSION['usuario_foto'] ?? '/RUPE/assets/fotos/avatar-de-perfil.png')); ?>"
                    alt="Foto de perfil"
                    width="120" height="120" style="object-fit: cover;">
                </div>
              </div>

              <div class="col-md-7">
                <h4 class="mb-2 text-md-start text-center">Información Personal</h4>
                <p class="text-muted fs-6 mb-2 text-md-start text-center">Ingrese su información personal y seleccione una fotografía para su perfil.</p>

                <!-- Botón para seleccionar foto -->
                <div>
                  <label for="foto" class="form-label fw-bold mb-1 small">Seleccione su Foto de Perfil</label>
                  <input class="form-control form-control-sm" type="file" id="foto" name="foto"
                    accept="image/*" onchange="visualizarFoto(this)">
                  <div class="form-text" style="font-size: 0.75rem;">Se permiten formatos: JPG, PNG. Máximo 2MB.</div>
                </div>
              </div>
            </div>
          </div>
        </section>
        <!-- Encabezado datos personales -->
        <div class="col-12 mx-auto">
          <h4 class="mb-2">Datos Personales</h4>

          <!-- Primera línea del formulario -->
          <div class="row g-4">
            <div class="col-md-4">
              <label for="nombre" class="form-label">Nombre Completo</label>
              <input type="text" class="form-control" id="nombre" name="nombre" value="<?= htmlspecialchars(
                                                                                          $perfil['nombre_usuario'] ?? ''
                                                                                        ); ?>" placeholder="Ejemplo: Juan Antonio">
            </div>
            <div class="col-md-3">
              <label for="appaterno" class="form-label">Apellido Paterno</label>
              <input type="text" class="form-control" id="appaterno" name="appaterno" value="<?= htmlspecialchars(
                                                                                                $perfil['ap_paterno'] ?? ''
                                                                                              ); ?>" placeholder="">
            </div>
            <div class="col-md-3">
              <label for="apmaterno" class="form-label">Apellido Materno</label>
              <input type="text" class="form-control" id="apmaterno" name="apmaterno" value="<?= htmlspecialchars(
                                                                                                $perfil['ap_materno'] ?? ''
                                                                                              ); ?>" placeholder="">
            </div>
            <div class="col-md-2">
              <label for="edad" class="form-label">Edad</label>
              <input type="number" class="form-control" id="edad" name="edad" min="18" max="100" step="1" value="<?= htmlspecialchars(
                                                                                        $perfil['edad'] ?? ''
                                                                                      ); ?>">
            </div>
          </div>

          <!-- Segunda línea del formulario -->
          <div class="row g-4 my-1">
            <div class="col-md-3">
              <label for="curp" class="form-label">CURP</label>
              <input type="text" class="form-control" id="curp" name="curp" maxlength="18" value="<?= htmlspecialchars(
                                                                                      $perfil['curp'] ?? ''
                                                                                    ); ?>" placeholder="">
            </div>
            <div class="col-md-2">
              <label for="telefono" class="form-label">Teléfono</label>
              <input type="text" class="form-control" id="telefono" maxlength="10" name="telefono" value="<?= htmlspecialchars(
                                                                                              $perfil['num_telefono'] ?? ''
                                                                                            ); ?>" placeholder="">
            </div>
            <div class="col-md-5">
              <label for="correo" class="form-label">Correo electrónico</label>
              <input type="email" class="form-control bg-light" id="correo" name="correo" value="<?= htmlspecialchars(
                                                                                          $perfil['correo_electronico'] ?? $_SESSION['correo'] ?? ''); ?>" reandonly>
                                                                                          
            </div>
            <div class="col-md-2">
              <label class="form-label fw-semibold">Sexo</label>
              <select id="sexo" name="sexo" class="form-select">
                <option value="" <?= empty($perfil['genero']) ? 'selected' : ''; ?>
                  disabled>Seleccione...</option>
                <option value="Femenino" <?= ($perfil['genero'] ?? '') === 'Femenino' ? 'selected' : ''; ?>>Femenino</option>
                <option value="Masculino" <?= ($perfil['genero'] ?? '') === 'Masculino' ? 'selected' : ''; ?>>Masculino</option>
              </select>
            </div>
          </div>

          <!-- Tercera línea del formulario -->
          <div class="row g-4 my-1">
            <div class="col-md-4">
              <label class="form-label fw-semibold">Ocupación</label>
              <select id="selocupacion" name="ocupacion" class="form-select">
                <option value="" <?= empty($perfil['ocupacion']) ? 'selected' : ''; ?>
                  disabled>Seleccione una ocupación</option>
                <option value="Estudiante" <?= (($perfil['ocupacion'] ?? '') === 'Estudiante')
                                              ? 'selected'
                                              : ''; ?>>Estudiante</option>
                <option value="Empleado" <?= (($perfil['ocupacion'] ?? '') === 'Empleado')
                                            ? 'selected'
                                            : ''; ?>>Empleado/a</option>
                <option value="Emprendedor" <?= (($perfil['ocupacion'] ?? '') === 'Emprendedor')
                                              ? 'selected'
                                              : ''; ?>>Emprendedor/a / Comerciante</option>
                <option value="AmaCasa" <?= (($perfil['ocupacion'] ?? '') === 'AmaCasa')
                                          ? 'selected'
                                          : ''; ?>>Ama de Casa</option>
                <option value="Jubilado" <?= (($perfil['ocupacion'] ?? '') === 'Jubilado')
                                            ? 'selected'
                                            : ''; ?>>Jubilado/a / Pensionado/a</option>
              </select>
            </div>
            <div class="col-md-6">
              <label for="calle" class="form-label">Dirección (nombre de la calle)</label>
              <input type="text" class="form-control" id="calle" name="calle" value="<?= htmlspecialchars($perfil['calle'] ?? ''); ?>" placeholder="">
            </div>
            <div class="col-md-1">
              <label for="numext" class="form-label">No.ext</label>
              <input type="text" class="form-control" id="numext" name="numext" value="<?= htmlspecialchars($perfil['num_exterior'] ?? ''); ?>" placeholder="">
            </div>
            <div class="col-md-1">
              <label for="numint" class="form-label">No.int</label>
              <input type="text" class="form-control" id="numint" name="numint" value="<?= htmlspecialchars($perfil['num_interior'] ?? ''); ?>" placeholder="">
            </div>
          </div>

          <!-- Cuarta línea del formulario -->
          <div class="row g-3 my-1">
            <div class="col-md-2">
              <label for="codigo_postal" class="form-label">Código Postal</label>
              <input type="text" class="form-control" id="codigo_postal" name="codigo_postal" value="<?= htmlspecialchars($perfil['codigo_postal'] ?? ''); ?>"
                maxlength="5">
              <div id="codigoError" class="form-text text-danger" style="display: none;">Ingrese código postal válido.</div>
            </div>

            <div class="col-md-5">
              <label for="colonia_nombre" class="form-label">Colonia</label>
              <select id="colonia_nombre" name="colonia_nombre" class="form-select" data-colonia="<?= htmlspecialchars($perfil['nombre_col'] ?? '') ?>" disabled onchange="autocompletarCP">
                <option value="" selected disabled>Codigo Postal primero o Alcaldia...</option>
              </select>
            </div>

            <div class="col-md-5">
              <label for="alcaldia" class="form-label">Alcaldía/Mpio</label>
              <select id="alcaldia" name="alcaldia_id" class="form-select" disabled onchange="cargarColoniasPorAlcaldia()">
                <option value="" selected disabled>Seleccione...</option>
              </select>
            </div>
          </div>

          <!-- Quinta línea del formulario -->
          <div class="row g-3 my-1">
            <div class="col-md-4">
              <label class="form-label fw-semibold">Estado</label>
              <select id="estado" name="estado_id" class="form-select" onchange="cargarMunicipios()">
                <option value="" selected disabled>Seleccione...</option>
                <?php if (!empty($listaEstados)): ?>
                  <?php foreach ($listaEstados as $e): ?>
                    <option value="<?= $e['id'] ?>" <?= (isset($perfil['id_estado']) && $perfil['id_estado'] == $e['id']) ? 'selecte' : '' ?>>
                      <?= htmlspecialchars($e['nombre']) ?></option>
                  <?php endforeach; ?>
                <?php endif; ?>
              </select>
            </div>

          </div>

          <div class="row g-2 my-1">
            <div class="col-md-6">
              <button id="btnGuardar" class="w-100 btn btn-lg fw-bold shadow-sm" style="background-color: #75b0d7;" type="submit">Guardar</button>
            </div>
            <div class="col-md-6">
              <a href="/RUPE/index.php?c=perfilusuario&a=Inicio" class="btn btn-lg w-100 py-2 fw-bold shadow-sm" style="background-color: #75b0d7;" >Regresar</a>
            </div>

      </form>
    </main>
  </div>
  <script src="../assets/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/js/form-validation.js"></script>

</body>


<!-- JAVASCRIPT DE CONTROL  -->
<script>
  //se intenta con esta variable ingresar a la ruta correcta
  const urlControlador = '/RUPE/controladores/usuarios/personalusu.php';

  //Visualizar la foto al seleccionarla
  function visualizarFoto(input){
    if(input.files && input.files){
      const reader = new FileReader();
      reader.onload = function(e){
        document.getElementById('img-visualizacion').src = e.target.result;
      };
      reader.readAsDataURL(input.files);
    }
  }

  //bloquear en edad los numeros negativos o caracteres alfabeticos
  const inputEdad = document.getElementById('edad');
  if(inputEdad){
    inputEdad.addEventListener('keydown', function(e){
      if (['-', '+', 'e', 'E', '.', ','].includes(e.key)){
        e.preventDefault();
      }
    });
    inputEdad.addEventListener('input', function(){
      this.value = this.value.replace(/[^0-9]/g, '');
      if (this.value !== '' && parseInt(this.value, 10) <= 0){
        this.value = '';
      }
    });
  }

  //CURP en mayusculas
  const inputCurp = document.getElementById('curp');
  if (inputCurp){
    inputCurp.style.textTransform = 'uppercase';
    inputCurp.addEventListener('input', function(){
      this.value = this.value.toUpperCase();
    });
  }

  //Llenado manual de alcaldias/municipios ya que el usuario tendra la opción de elegir que campos llenar
  function cargarMunicipios() {
    const estadoId = document.getElementById('estado').value;
    const selectAlcaldia = document.getElementById('alcaldia');
    const selectColonia = document.getElementById('colonia_nombre');

    selectAlcaldia.innerHTML = '<option value="" selected disabled>Cargando...</option>';
    selectAlcaldia.disabled = true;
    selectColonia.innerHTML = '<option value="" selected disable>Código Postal primero o Alcaldía...</option>';
    selectColonia.disabled = true;

    if (!estadoId) return;

    fetch(`${urlControlador}?action=getMunicipios&estado_id=${estadoId}`)
      .then(res => res.json())
      .then(municipios => {
        //.then(data => {
        selectAlcaldia.innerHTML = '<option value="" selected disabled>Seleccione...</option>';
        if (Array.isArray(municipios) && municipios.length > 0) {
          municipios.forEach(m => {
            const option = document.createElement('option');
            option.value = m.id;
            option.textContent = m.nombre;
            selectAlcaldia.appendChild(option);
            //if (
            //m.nombre.normalize("NFD")
            //.replace(/[\u0300-\u036f]/g, "")
            //.trim()
            //.toUpperCase() ===
            //alcaldiaNombreTarget.normalize("NFD")
            //.replace(/[\u0300-\u036f]/g, "")
            //.trim()
            //.toUpperCase()
            //) {
            //option.selected = true;
            //}
          });
          selectAlcaldia.disabled = false;
        }
      })
      .catch(err => console.error('Error al cargar alcaldías:', err));
  }
  //En caso de requerir llenar Colonias al elegir Alcaldía y no codigo postal
  function cargarColoniasPorAlcaldia() {
    const selectAlcaldia = document.getElementById('alcaldia');
    const selectColonia = document.getElementById('colonia_nombre');
    const alcaldiaNombre = selectAlcaldia.options[selectAlcaldia.selectedIndex].text;

    selectColonia.innerHTML = '<option value="" selected disabled>Cargando colonias...</option>';
    selectColonia.disabled = true;

    if (!alcaldiaNombre || alcaldiaNombre === 'Seleccione...') return;

    fetch(`${urlControlador}?action=getColoniasPorAlcaldia&alcaldia_nombre=${encodeURIComponent(alcaldiaNombre)}`)
      .then(res => res.json())
      .then(data => {
        selectColonia.innerHTML = '<option value="" selected disabled>Seleccione una colonia...</option>';
        if (Array.isArray(data) && data.length > 0) {
          data.forEach(registro => {
            const option = document.createElement('option');
            option.value = registro.nombre_col;
            option.textContent = registro.nombre_col;
            option.dataset.cp = registro.codigo_postal; // Guarda el CP oculto
            selectColonia.appendChild(option);
          });
          selectColonia.disabled = false;
        } else {
          selectColonia.innerHTML = '<option value="" selected disabled>Sin colonias registradas</option>';
        }
      })
      .catch(err => console.error('Error al cargar colonias:', err));
  }

//Para obtener el CP al elegir la Colonia
  function autocompletarCP() {
    const selectColonia = document.getElementById('colonia_nombre');
    const inputCodigo = document.getElementById('codigo_postal');
    const errorDiv = document.getElementById('codigoError');
    const opcion = selectColonia.options[selectColonia.selectedIndex];

    if (opcion && opcion.dataset.cp) {
      inputCodigo.value = opcion.dataset.cp;
      if (errorDiv) errorDiv.style.display = 'none';
      //inputCodigo.dispatchEvent(new Event('change'));
    }
  }

  //Validar y/o seleccionar al ingresar codigo postal
  document.getElementById('codigo_postal').addEventListener('input', function(e) {
      const cp = e.target.value.trim();
      const selectColonia = document.getElementById('colonia_nombre');
      const selectEstado = document.getElementById('estado');
      const selectAlcaldia = document.getElementById('alcaldia');
      const errorDiv = document.getElementById('codigoError');

      selectColonia.innerHTML = '<option value="">Cargando...</option>';
      selectColonia.disabled = true;
      if (errorDiv) {
        errorDiv.style.display = 'none';
        errorDiv.textContent = '';
      }
      if (cp.length === 5 && /^\d+$/.test(cp)) {
        fetch(`${urlControlador}?action=getColoniasPorCP&cp=${cp}`)
          .then(response => response.json())
          .then(data => {
              if (Array.isArray(data) && data.length > 0) {
                const primerRegistro = data[0];
                const nombreEstado = primerRegistro.nombre_edo ? primerRegistro.nombre_edo.trim() : "";
                const alcaldiaNombreTarget = primerRegistro.alcaldia_mpio ? primerRegistro.alcaldia_mpio.trim() : "";

                //Restricción para Ciudad de México y Estado de México
                if (nombreEstado !== "Ciudad de México" && nombreEstado !== "México") {
                  mostrarError('RUPE solo opera en Ciudad de México y Estado de México.');
                  bloquearControles();
                  return;
                }
                //Para la seleccion de Estado
                selectEstado.value = primerRegistro.id_estado;
                selectEstado.style.pointerEvents = "none";
                selectEstado.style.backgroundColor = "#e9ecef";

                //Seleccion de alcaldia por nombre
                fetch(`${urlControlador}?action=getMunicipios&estado_id=${primerRegistro.id_estado}`)
                  .then(res => res.json())
                  .then(municipios => {
                      selectAlcaldia.innerHTML = '<option value="" selected disabled>Seleccione...</option>';
                      if (Array.isArray(municipios) && municipios.length > 0) {
                        municipios.forEach(m =>{
                        const option = document.createElement('option');
                        option.value = m.id;
                        option.textContent = m.nombre;
                        if (m.nombre.trim().toUpperCase() === alcaldiaNombreTarget.toUpperCase()) {
                          option.selected = true;
                        }
                        selectAlcaldia.appendChild(option);
                      }); 
                      selectAlcaldia.disabled = false; 
                      selectAlcaldia.style.pointerEvents = "none"; 
                      selectAlcaldia.style.backgroundColor = "#e9ecef";
                  }
              });
            //Mostrar y habilitar colonias
            const coloniaGuardada = selectColonia.dataset.colonia || ""; 
            selectColonia.innerHTML = '<option value="" selected disabled>Seleccione una colonia...</option>'; 
            data.forEach(registro => {
              const option = document.createElement('option');
              option.value = registro.nombre_col;
              option.textContent = registro.nombre_col;
              if (coloniaGuardada && registro.nombre_col.trim().toUpperCase() === coloniaGuardada.trim().toUpperCase()) {
                option.selected = true;
              }
              selectColonia.appendChild(option);
            }); selectColonia.disabled = false;
          }
        else {
          mostrarError('Código postal no encontrado para la zona');
          bloquearControles();
        }
      })
    .catch(err => {
      mostrarError('Error al conectar a la base de datos');
      console.error(err);
    });
  }
  else {
    //restaurar si se borran digitos
    selectEstado.value = "";
    selectEstado.style.pointerEvents = "";
    selectEstado.style.backgroundColor = "";
    selectAlcaldia.innerHTML = '<option value="" selected disabled>Seleccione...</option>';
    selectAlcaldia.disabled = true;
    selectAlcaldia.style.pointerEvents = "";
    selectAlcaldia.style.backgroundColor = "";
    selectColonia.innerHTML = '<option value="" selected disabled>Código Postal primero o Alcaldía...</option>';
    selectColonia.disabled = true;
  }

  function mostrarError(mensaje) {
    if (errorDiv) {
      errorDiv.textContent = mensaje;
      errorDiv.style.display = 'block';
    }
    selectColonia.innerHTML = '<option value="" selected disabled>CP no permitido</option>';
    selectColonia.disabled = true;
  }

  function bloquearControles() {
    selectEstado.value = "";
    selectEstado.style.pointerEvents = "none";
    selectEstado.style.backgroundColor = "#e9ecef";
    selectAlcaldia.innerHTML = '<option value="" selected disabled>No permitido</option>';
    selectAlcaldia.disabled = true;
    selectAlcaldia.style.backgroundColor = "#e9ecef";
  }
  });

  //Cargar ubicación al ingresar al formulario si existe un codigo postal
  window.addEventListener('load', function(){
    const inputCP = document.getElementById('codigo_postal');
    if (inputCP && inputCP.value.trim().length === 5) {
      inputCP.dispatchEvent(new Event('input'));
    }
  });

  //Confirmar que los select se envien por POST al guardar
  document.querySelector('form.needs-validation').addEventListener('submit', function(){
    document.getElementById('colonia_nombre').disabled = false;
    document.getElementById('alcaldia').disabled = false;
    document.getElementById('estado').disabled = false;
  });
</script>
<!-- 

  //El código postal se consulta de la tabla sepomex
  //document.getElementById('codigo').addEventListener('input', function(e) {
  //  const cp = e.target.value;
  //  const selectColonia = document.getElementById('colonia_nombre');
  //  const selectEstado = document.getElementById('estado');
  //  const selectAlcaldia = document.getElementById('alcaldia');
  //  const errorDiv = document.getElementById('codigoError');

  //selectColonia.innerHTML = '<option value="">Cargando...</option>';
  //selectColonia.disabled = true;
  //if (errorDiv) {
  //  errorDiv.style.display = 'none';
  //  errorDiv.textContent = '';
  //}

  //  if (cp.length === 5 && /^\d+$/.test(cp)) {
  //  selectColonia.innerHTML = '<option value='
  // fetch(`/RUPE/controladores/personalusu.php?action=getColoniasPorCP&cp=${cp}`)
  //  .then(response => response.json())
  // .then(data => {
  //  if (Array.isArray(data) && data.length > 0) {
  //  const primerRegistro = data[0]; // Tomamos el primer resultado
  // const nombreEstado = primerRegistro.nombre_edo ? primerRegistro.nombre_edo.trim() : "";

  // Restricción estricta: solo CDMX y Estado de México
  // if (nombreEstado !== "Ciudad de México" && nombreEstado !== "México") {
  //   mostrarError('El proyecto RUPE solo opera en CDMX y Estado de México.');
  //   bloquearControles();
  //   return;
  // }

  // Autoseleccionar y fijar Estado (sin disabled para que se envíe por POST)
  // selectEstado.value = primerRegistro.id_estado;
  // selectEstado.disabled = false;
  // selectEstado.style.pointerEvents = "none";
  // selectEstado.style.backgroundColor = "#e9ecef";

  // Cargar y fijar Alcaldía correspondiente
  // const estadoId = primerRegistro.id_estado;
  // const alcaldiaIdTarget = primerRegistro.id_alcaldia;

  // fetch(`/RUPE/controladores/personalusu.php?action=getMunicipios&estado_id=${estadoId}`)
  //  .then(res => res.json())
  // .then(municipios => {
  //   selectAlcaldia.innerHTML = '<option value="" selected disabled>Seleccione...</option>';
  //   if (Array.isArray(municipios) && municipios.length > 0) {
  //    municipios.forEach(m => {
  //     const option = document.createElement('option');
  //    option.value = m.id;
  //   option.textContent = m.nombre;
  //  selectAlcaldia.appendChild(option);
  //});
  //selectAlcaldia.value = alcaldiaIdTarget;
  //selectAlcaldia.disabled = false;
  //selectAlcaldia.style.pointerEvents = "none";
  //selectAlcaldia.style.backgroundColor = "#e9ecef";
  //}
  //});

  // Llenar las colonias
  //selectColonia.innerHTML = '<option value="" selected disabled>Seleccione una colonia...</option>';
  //data.forEach(registro => {
  //const option = document.createElement('option');
  //option.value = registro.nombre_col;
  //option.textContent = registro.nombre_col;
  //selectColonia.appendChild(option);
  //});
  //selectColonia.disabled = false;

  //} else {
  //mostrarError('Código Postal no encontrado en la zona de cobertura.');
  //bloquearControles();
  //}
  //})
  //.catch(err => {
  // mostrarError('Error al conectar con la base de datos.');
  //console.error(err);
  //});
  //} else {
  /// Restaurar controles si borra el CP
  //selectEstado.disabled = false;
  //selectEstado.style.pointerEvents = "";
  //selectEstado.style.backgroundColor = "";
  //selectEstado.value = "";
  //selectAlcaldia.innerHTML = '<option value="" selected disabled>Seleccione...</option>';
  //selectAlcaldia.disabled = true;
  //selectAlcaldia.style.pointerEvents = "";
  //selectAlcaldia.style.backgroundColor = "";
  //selectColonia.innerHTML = '<option value="" selected disabled>Primero Codigo Postal...</option>';
  //}
 
  //Para controlar el boton guardar, no se active si no se detecta llenado de campos
  //document.addEventListener('DOMContentLoaded', function() {
  //  const formulario = document.querySelector('form.needs-validation');
  //  const btnGuardar = document.getElementById('btnGuardar');
  //  const campos = formulario.querySelectorAll('input, select, textarea');

  // const valoresIniciales = {};
  // campos.forEach((campo, index) => {
  //  if (campo.type !== 'submit' && campo.type !== 'button' && campo.type !== 'file') {
  //    const clave = campo.name || campo.id || `campo_${index}`;
  //    valoresIniciales[clave] = campo.value.trim();
  //  }
  //});

  //function huboCambiosReales() {
  //for (let i = 0; i < campos.length; i++) {
  // const campo = campos[i];
  //if (campo.type === 'submit' || campo.type === 'button' || campo.type === 'hidden') continue;
  //if (campo.type === 'file' && campo.files.length > 0) return true;
  //if (campo.type !== 'file') {
  //  const clave = campo.name || campo.id || `campo_${i}`;
  //  if (campo.value.trim() !== valoresIniciales[clave]) return true;
  // }
  //}
  //return false;
  //}

  //function actualizarEstadoBoton() {
  // if (btnGuardar) btnGuardar.disabled = !huboCambiosReales();
  //}

  //formulario.addEventListener('input', actualizarEstadoBoton);
  //formulario.addEventListener('change', actualizarEstadoBoton);
  //formulario.addEventListener('submit', function(e) {
  //if (!huboCambiosReales()) {
  //  e.preventDefault();
  // e.stopPropagation();
  // }
  //});

  //actualizarEstadoBoton()




 const urlBase = '/RUPE/controladores/personaladmin.php';

  function cargarMunicipios() {
    const estadoId = document.getElementById('estado').value;
    const alcaldiaSelect = document.getElementById('alcaldia');

    resetSelect(alcaldiaSelect, '-- Selecciona Alcaldía/Mpio --');
    resetSelect(document.getElementById('codigo'), '-- Selecciona Mpio --');
    resetSelect(document.getElementById('colonia'), '-- Selecciona CP --');

    if (!estadoId) return;

    fetch(`${urlBase}?action=getMunicipios&estado_id=${estadoId}`)
      .then(res => res.json())
      .then(data => {
        data.forEach(m => fillOption(alcaldiaSelect, m.id, m.nombre));
        alcaldiaSelect.disabled = false;
      });
    .catch(error => console.error('Error al cargar municipios:', error));
  }

  function cargarCP() {
    const municipioId = document.getElementById('alcaldia').value;
    const cpSelect = document.getElementById('codigo');

    resetSelect(cpSelect, '-- Selecciona Código Postal --');
    resetSelect(document.getElementById('colonia'), '-- Selecciona CP --');

    if (!municipioId) return;

    fetch(`${urlBase}?action=getCP&municipio_id=${municipioId}`)
      .then(res => res.json())
      .then(data => {
        data.forEach(cp => fillOption(cpSelect, cp.id, cp.codigo));
        cpSelect.disabled = false;
      });
    .catch(error => console.error('Error al cargar códigos postales:', error));
  }

  function cargarColonias() {
    const cpId = document.getElementById('codigo').value;
    const coloniaSelect = document.getElementById('colonia');

    resetSelect(coloniaSelect, '-- Selecciona Colonia --');

    if (!cpId) return;

    fetch(`${urlBase}?action=getColonias&codigo_id=${cpId}`)
      .then(res => res.json())
      .then(data => {
        data.forEach(col => fillOption(coloniaSelect, col.id, col.nombre));
        coloniaSelect.disabled = false;
      });
    .catch(error => console.error('Error al cargar colonias:', error));
  }

  function resetSelect(element, defaultText) {
    element.innerHTML = `<option value="" selected disabled>${defaultText}</option>`;
    element.disabled = true;
  }

  function fillOption(element, id, text) {
    const opt = document.createElement('option');
    opt.value = id;
    opt.textContent = text;
    element.appendChild(opt);
  }

  function visualizarFoto(input) {
    if (input.files && input.files[0]) {
      var reader = new FileReader();
      reader.onload = function(e) {
        document.getElementById('img-visualizacion').src = e.target.result;
      }
      reader.readAsDataURL(input.files[0]);
    }
  } -->