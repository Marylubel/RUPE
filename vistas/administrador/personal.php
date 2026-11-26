<title>Perfil Administrador</title>

<link rel="canonical" href="https://getbootstrap.com/docs/5.0/examples/checkout/">

<!-- Bootstrap core CSS -->
<link href="../assets/css/bootstrap.min.css" rel="stylesheet">

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
<link href="../assets/css/form-validation.css" rel="stylesheet">

<body class="bg-light">
  <div class="container">
    <main>
      <form action="/RUPE/controladores/personaladmin.php" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
        <section class="py-5">
          <div class="col-md-8 mx-auto bg-white p-3 rounded border shadow-sm">

            <!-- Espacio de la Foto de Perfil e información personal -->
            <div class="row align-items-center justify-content-center g-3">
              <div class="col-auto text-center">
                <div class="mb-2 position-relative d-inline-block">
                  <img id="img-visualizacion" class="d-block mx-auto rounded-circle border shadow-sm"
                    src="<?php echo (isset($_SESSION['admin_foto']) && !empty($_SESSION['admin_foto'])) ? $_SESSION['admin_foto'] : '../assets/fotos/avatar-de-perfil.png'; ?>"
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
                    accept="image/*" <?= empty($perfil['foto']) ? 'required' : '' ?> onchange="visualizarFoto(this)">
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
                                                                                        ); ?>" placeholder="Ejemplo: Juan Antonio" required>
            </div>
            <div class="col-md-3">
              <label for="appaterno" class="form-label">Apellido Paterno</label>
              <input type="text" class="form-control" id="appaterno" name="appaterno" value="<?= htmlspecialchars(
                                                                                                $perfil['ap_paterno'] ?? ''
                                                                                              ); ?>" placeholder="" required>
            </div>
            <div class="col-md-3">
              <label for="apmaterno" class="form-label">Apellido Materno</label>
              <input type="text" class="form-control" id="apmaterno" name="apmaterno" value="<?= htmlspecialchars(
                                                                                                $perfil['ap_materno'] ?? ''
                                                                                              ); ?>" placeholder="" required>
            </div>
            <div class="col-md-2">
              <label for="edad" class="form-label">Edad</label>
              <input type="number" class="form-control" id="edad" name="edad" value="<?= htmlspecialchars(
                                                                                        $perfil['edad'] ?? ''
                                                                                      ); ?>" required>
            </div>
          </div>

          <!-- Segunda línea del formulario -->
          <div class="row g-4 my-1">
            <div class="col-md-3">
              <label for="curp" class="form-label">CURP</label>
              <input type="text" class="form-control" id="curp" name="curp" value="<?= htmlspecialchars(
                                                                                      $perfil['curp'] ?? ''
                                                                                    ); ?>" placeholder="" required>
            </div>
            <div class="col-md-2">
              <label for="telefono" class="form-label">Teléfono</label>
              <input type="text" class="form-control" id="telefono" name="telefono" value="<?= htmlspecialchars(
                                                                                              $perfil['num_telefono'] ?? ''
                                                                                            ); ?>" placeholder="" required>
            </div>
            <div class="col-md-5">
              <label for="correo" class="form-label">Correo electrónico</label>
              <input type="email" class="form-control" id="correo" name="correo" value="<?= htmlspecialchars(
                                                                                          $perfil['correo_electronico'] ?? ''
                                                                                        ); ?>" placeholder="you@example.com" required>
            </div>
            <div class="col-md-2">
              <label class="form-label fw-semibold">Sexo</label>
              <select id="sexo" name="sexo" class="form-select" required>
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
              <select id="selocupacion" name="ocupacion" class="form-select" required>
                <option value="" <?= empty($perfil['ocupacion']) ? 'selected' : ''; ?>
                  disabled>Seleccione una ocupación</option>
                <option value="Administrador" <?= (($perfil['ocupacion'] ?? '') === 'Administrador')
                                                ? 'selected'
                                                : ''; ?>>Administrador</option>                
              </select>
            </div>

            <div class="col-md-6">
              <label for="calle" class="form-label">Dirección (nombre de la calle)</label>
              <input type="text" class="form-control" id="calle" name="calle" value="<?= htmlspecialchars($perfil['calle'] ?? ''); ?>" placeholder="" required>
            </div>

            <div class="col-md-1">
              <label for="numext" class="form-label">No.ext</label>
              <input type="text" class="form-control" id="numext" name="numext" value="<?= htmlspecialchars($perfil['num_exterior'] ?? ''); ?>" placeholder="" required>
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
              <input type="text" class="form-control" id="codigo_postal" name="codigo_postal"
                value="<?= htmlspecialchars($perfil['codigo_postal'] ?? ''); ?>" placeholder="" maxlength="5" required>
              <!--<select id="codigo" name="codigo_id" class="form-select" disabled onchange="cargarColonias()"> 
                <option value="" selected disabled>Seleccione...</option>
              </select> -->
              <div id="codigoError" class="form-text text-danger" style="display: none;">Ingrese código postal válido.</div>
            </div>

            <div class="col-md-5">
              <label for="colonia_nombre" class="form-label">Colonia</label>
              <select id="colonia_nombre" name="colonia_nombre" class="form-select" 
              data-colonia="<?= htmlspecialchars($perfil['nombre_col'] ?? '') ?>" disabled required>
                  <option value="" selected disabled>Codigo Postal...</option>                
              </select>
            </div>

            <div class="col-md-5">
              <label for="alcaldia" class="form-label">Alcaldía/Mpio</label>
              <select id="alcaldia" name="alcaldia_id" class="form-select" <?= empty($perfil['id_alcaldia']) ? 'disabled' : '' ?> onchange="cargarColoniasPorAlcaldia()">
                <?php if (!empty($perfil['id_alcaldia'])): ?>
                  <option value="<?= $perfil['id_alcaldia'] ?>" selected><?= htmlspecialchars($perfil['alcaldia_mpio']) ?></option>
                <?php else: ?>
                  <option value="" selected disabled>Seleccione...</option>
                <?php endif; ?>
              </select>
            </div>
          </div>

          <div class="row g-3 my-1">
            <div class="col-md-4">
              <label class="form-label fw-semibold">Estado</label>
              <select id="estado" name="estado_id" class="form-select" onchange="cargarMunicipios()" required>
                <option value="" disabled <?= empty($perfil['id_estado']) ? 'selected' : '' ?>>Seleccione...</option>
                <?php if (!empty($listaEstados)): ?>
                  <?php foreach ($listaEstados as $e): ?>
                    <option value="<?= $e['id'] ?>" <?= (isset($perfil['id_estado']) && $perfil['id_estado'] == $e['id']) ? 'selected' : '' ?>>
                      <?= htmlspecialchars($e['nombre']) ?></option>
                  <?php endforeach; ?>
                <?php endif; ?>
              </select>
            </div>
          </div>

          <hr class="my-4">
          <button class="w-100 btn btn-primary btn-lg" type="submit">Guardar</button>
      </form>
    </main>
  </div>
  <script src="../assets/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/js/form-validation.js"></script>

</body>


<!-- JAVASCRIPT DE CONTROL  -->
<script>
  // Para ver alcaldias y estados
  function cargarMunicipios() {
    const estadoId = document.getElementById('estado').value;
    const selectAlcaldia = document.getElementById('alcaldia');

    selectAlcaldia.innerHTML = '<option value="" selected disabled>Cargando...</option>';
    selectAlcaldia.disabled = true;

    if (!estadoId) return;

    fetch(`/RUPE/controladores/personaladmin.php?action=getMunicipios&estado_id=${estadoId}`)
      .then(res => res.json())
      .then(municipios => {

        selectAlcaldia.innerHTML =
          '<option value="" selected disabled>Seleccione...</option>';

        if (Array.isArray(municipios) && municipios.length > 0) {
          municipios.forEach(m => {
            const option = document.createElement('option');
            option.value = m.id;
            option.textContent = m.nombre;
            selectAlcaldia.appendChild(option);
          });
          selectAlcaldia.disabled = false;
        }
        //const alcaldiaNombreTarget = primerRegistro.alcaldia_mpio;
        //municipios.forEach(m => {
        //  municipios.forEach(m => {

      })
      .catch(err => console.error('Error al cargar alcaldías:', err));
  }
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
  //});

  //selectAlcaldia.disabled = true;
  //selectAlcaldia.style.backgroundColor = "#e9ecef";
 // }
  //});
 // }
  // Para obtener el codigo postal restringiendo el estado
  document.getElementById('codigo_postal').addEventListener('input', function(e) {
    const cp = e.target.value;
    const selectColonia = document.getElementById('colonia_nombre');
    const selectEstado = document.getElementById('estado');
    const selectAlcaldia = document.getElementById('alcaldia');
    const errorDiv = document.getElementById('codigoError');

    // Limpiar 
    selectColonia.innerHTML = '<option value="">Cargando...</option>';
    selectColonia.disabled = true;
    errorDiv.style.display = 'none';
    errorDiv.textContent = '';

    // Valida el codigo postal con 5 dígitos numéricos
    if (cp.length === 5 && /^\d+$/.test(cp)) {
      fetch(`/RUPE/controladores/personaladmin.php?action=getColoniasPorCP&cp=${cp}`)
        .then(response => response.json())
        .then(data => {

          // Valida que la BD devuelva registros reales
          if (Array.isArray(data) && data.length > 0) {

            // Acceso al índice [0] de la lista
            const primerRegistro = data[0];
            //console.log("Alcaldía:", primerRegistro.alcaldia_mpio, "Estado:", primerRegistro.id_estado);
            const nombreEstado = primerRegistro.nombre_edo ? primerRegistro.nombre_edo.trim() : "";
            const IdEstado = primerRegistro.id_estado; //agregado para obtener el id del estado
            const alcaldiaNombreTarget = primerRegistro.alcaldia_mpio ? primerRegistro.alcaldia_mpio.trim() : ""; //agregado para obtener el nombre de la alcaldía

            // Restricción de estado
            if (nombreEstado !== "Ciudad de México" && nombreEstado !== "México") {
              mostrarError('RUPE solo opera en CDMX y Estado de México.');
              forzarBloqueoGeografico();
              return;
            }

            //Bloquear el Estado para evitar manipulaciones
            selectEstado.value = primerRegistro.id_estado;
            selectEstado.style.pointerEvents = "none";
            selectEstado.style.backgroundColor = "#e9ecef";

            //Obtener las alcaldías/municipios del estado
            const estadoId = primerRegistro.id_estado;
            const alcaldiaIdTarget = primerRegistro.alcaldia_mpio;

            selectAlcaldia.innerHTML = '<option value="" selected disabled>Cargando...</option>';
            selectAlcaldia.disabled = true;

            fetch(`/RUPE/controladores/personaladmin.php?action=getMunicipios&estado_id=${estadoId}`)
              .then(res => res.json())
              .then(municipios => {
                selectAlcaldia.innerHTML = '<option value="" selected disabled>Seleccione...</option>';
                if (Array.isArray(municipios) && municipios.length > 0) {
                  //console.log(
                  //  "Alcaldía SEPOMEX:",
                  //  alcaldiaNombreTarget
                  //);
                  municipios.forEach(m => {
                    //console.log(
                    //  "Valor final del select:",
                    //  selectAlcaldia.value
                    //);
                    const option = document.createElement('option');
                    option.value = m.id;
                    option.textContent = m.nombre;
                    selectAlcaldia.appendChild(option);
                    if (m.nombre.trim().toUpperCase() ===
                      alcaldiaNombreTarget.trim().toUpperCase()) {
                      //console.log("Alcaldía encontrada y seleccionada:", m.nombre);
                      option.selected = true;
                    }
                  });

                  // Para seleccion de alcaldía/municipio obtenida de la tabla SEPOMEX
                  //selectAlcaldia.value = alcaldiaIdTarget; se elimino porque bloqueaba la selección de la alcaldía correcta por el nombre de la alcaldía que no coincidía con el id
                  selectAlcaldia.disabled = true; // Bloqueado para evitar alteraciones
                  selectAlcaldia.style.backgroundColor = "#e9ecef";
                }
              })
              .catch(err => console.error('Error al sincronizar alcaldía:', err));

            //Para guardar la colonia
            const coloniaguardada = document.getElementById('colonia_nombre').dataset.colonia;

            //Activar el listado de colonias
            selectColonia.innerHTML = '<option value="" selected disabled>Seleccione una colonia...</option>';
            data.forEach(registro => {
              const option = document.createElement('option');
              option.value = registro.nombre_col;
              option.textContent = registro.nombre_col;

              if (coloniaguardada && registro.nombre_col.trim().toUpperCase() === coloniaguardada.trim().toUpperCase()) {
                option.selected = true;
              }
              selectColonia.appendChild(option);
            });
            selectColonia.disabled = false;
            selectColonia.style.backgroundColor = "";

          } else {
            mostrarError('Código Postal inexistente o no registrado en la cobertura.');
            forzarBloqueoGeografico();
          }
        })
        .catch(err => {
          mostrarError('Error al conectar con la base de datos local.');
          console.error(err);
        });
    } else {
      // Si borra dígitos o el CP no está completo, restauramos los selectores al modo manual libre
      selectEstado.value = "";
      selectEstado.style.pointerEvents = "";
      selectEstado.style.backgroundColor = "";
      selectAlcaldia.innerHTML = '<option value="" selected disabled>Seleccione...</option>';
      selectAlcaldia.disabled = true;
      selectAlcaldia.style.backgroundColor = "";
      selectColonia.innerHTML = '<option value="" selected disabled>Codigo Postal...</option>';
    }

    // Muestra los mensajes de error en la interfaz de Bootstrap
    function mostrarError(mensaje) {
      errorDiv.textContent = mensaje;
      errorDiv.style.display = 'block';
      selectColonia.innerHTML = '<option value="" selected disabled>CP no permitido</option>';
      selectColonia.disabled = true;
    }

    // Resetea y bloquea los inputs geográficos si meten un CP ajeno (ej: Jalisco)
    function forzarBloqueoGeografico() {
      selectEstado.value = "";
      selectEstado.style.pointerEvents = "none";
      selectEstado.style.backgroundColor = "#e9ecef";

      selectAlcaldia.innerHTML = '<option value="" selected disabled>No permitido</option>';
      selectAlcaldia.disabled = true;
      selectAlcaldia.style.backgroundColor = "#e9ecef";
    }
  });

  window.addEventListener('load', function() {

    //const cp = document.getElementById('codigo_postal').value;
    const inputCP = document.getElementById('codigo_postal');

    if (inputCP && inputCP.value.trim().length === 5){
      inputCP.dispatchEvent(new Event('input'));
    //if (cp && cp.length === 5) {
    //  document.getElementById('codigo_postal')
    //    .dispatchEvent(new Event('input'));
    }

    //
    document.querySelector('form.needs-validation').addEventListener('submit', function() {
    const col = document.getElementById('colonia_nombre');
    const alc = document.getElementById('alcaldia');
    const est = document.getElementById('estado');
    if (col) col.disabled = false;
    if (alc) alc.disabled = false;
    if (est) est.disabled = false;
});

  });
</script>

<!-- const urlBase = '../controladores/personaladmin.php';

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