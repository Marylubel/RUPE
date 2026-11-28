<main class="container py-3 mt-2">
    <section class="pt-1">
        <div class="col-lg-10 mx-auto">
            <div class="p-3 mb-4 text-black rounded-3 shadow-sm" style="background-color: #6394b3;">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div>
                        <h3 class="fw-bold mb-1">Reportar Mascota Extraviada (Sin Cuenta)</h3>
                        <p class="mb-0 small">Completa los datos de tu mascota y el lugar donde se extravió para generar tu folio oficial y cartel con código QR.</p>
                    </div>
                    <a href="/RUPE/index.php" class="btn btn-light btn-sm fw-bold">Regresar al Inicio</a>
                </div>
            </div>

            <form action="/RUPE/controladores/reporteinvitado.php" method="POST" enctype="multipart/form-data">

                <!-- BLOQUE 1: DATOS DE LA MASCOTA -->
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white border-bottom py-2">
                        <h5 class="fw-bold mb-0" style="color: #065688;">1. Datos de la Mascota</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label for="nombre_mascota" class="form-label fw-semibold">Nombre de la mascota *</label>
                                <input type="text" class="form-control" id="nombre_mascota" name="nombre_mascota" required>
                            </div>

                            <div class="col-md-4">
                                <label for="especie_id" class="form-label fw-semibold">Especie *</label>
                                <select id="especie_id" class="form-select" onchange="cargarRazas()" required>
                                    <option value="" selected disabled>Seleccione...</option>
                                    <?php foreach ($listaEspecies as $esp): ?>
                                        <option value="<?= $esp['id_especie'] ?>"><?= htmlspecialchars($esp['nom_especie']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label for="id_catalogos" class="form-label fw-semibold">Raza *</label>
                                <select id="id_catalogos" name="id_catalogos" class="form-select" disabled required>
                                    <option value="" selected disabled>Seleccione especie primero...</option>
                                </select>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-3">
                                <label for="id_sexos" class="form-label fw-semibold">Sexo *</label>
                                <select id="id_sexos" name="id_sexos" class="form-select" required>
                                    <option value="" selected disabled>Seleccione...</option>
                                    <?php foreach ($listaSexos as $sex): ?>
                                        <option value="<?= $sex['id_sexo_masc'] ?>"><?= htmlspecialchars($sex['sexos']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label for="edad" class="form-label fw-semibold">Edad aprox. (años)</label>
                                <input type="number" class="form-control" id="edad" name="edad" min="0" max="30" step="0.5" placeholder="Ej. 2">
                            </div>

                            <div class="col-md-3">
                                <label for="color" class="form-label fw-semibold">Color *</label>
                                <input type="text" class="form-control" id="color" name="color" placeholder="Ej. Café con blanco" required>
                            </div>

                            <div class="col-md-3">
                                <label for="tamano" class="form-label fw-semibold">Tamaño *</label>
                                <select id="tamano" name="tamano" class="form-select" required>
                                    <option value="" selected disabled>Seleccione...</option>
                                    <option value="Chico">Chico</option>
                                    <option value="Mediano">Mediano</option>
                                    <option value="Grande">Grande</option>
                                    
                                </select>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="sena_particular" class="form-label fw-semibold">Señas particulares *</label>
                                <input type="text" class="form-control" id="sena_particular" name="sena_particular" 
                                       placeholder="Ej. Collar rojo, mancha en la oreja derecha, cicatriz..." required>
                            </div>

                            <div class="col-md-6">
                                <label for="foto" class="form-label fw-semibold">Fotografía de la mascota *</label>
                                <input type="file" class="form-control" id="foto" name="foto" accept="image/*" required>
                                <div class="form-text small">Esta fotografía aparecerá en el cartel de búsqueda.</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- BLOQUE 2: LUGAR Y FECHA DEL EXTRAVÍO -->
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white border-bottom py-2">
                        <h5 class="fw-bold mb-0 text-primary">2. Lugar y Fecha del Extravío</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="fecha" class="form-label fw-semibold">Fecha en que se extravió *</label>
                                <input type="date" class="form-control" id="fecha" name="fecha" max="<?= date('Y-m-d') ?>" value="<?= date('Y-m-d') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="hora" class="form-label fw-semibold">Hora aproximada *</label>
                                <input type="time" class="form-control" id="hora" name="hora" value="<?= date('H:i') ?>" required>
                            </div>
                        </div>

                        <!-- Ubicación SEPOMEX -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-3">
                                <label for="codigo_postal" class="form-label fw-semibold">Código Postal</label>
                                <input type="text" class="form-control" id="codigo_postal" name="codigo_postal" maxlength="5" 
                                       placeholder="Ej. 06700" onkeyup="buscarPorCP()">
                                <div id="msg-cp" class="form-text text-muted small">5 dígitos para autocompletar</div>
                            </div>

                            <div class="col-md-3">
                                <label for="estado_id" class="form-label fw-semibold">Estado *</label>
                                <select id="estado_id" name="estado_id" class="form-select" onchange="cargarMunicipios()" required>
                                    <option value="" selected disabled>Seleccione...</option>
                                    <?php foreach ($listaEstados as $est): ?>
                                        <option value="<?= $est['id'] ?>"><?= htmlspecialchars($est['nombre']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label for="municipio_id" class="form-label fw-semibold">Municipio / Alcaldía *</label>
                                <select id="municipio_id" name="municipio_id" class="form-select" onchange="cargarColoniasPorMunicipio()" disabled required>
                                    <option value="" selected disabled>Seleccione estado...</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label for="colonia_nombre" class="form-label fw-semibold">Colonia *</label>
                                <select id="colonia_nombre" name="colonia_nombre" class="form-select" onchange="completarCPDesdeColonia()" disabled required>
                                    <option value="" selected disabled>Seleccione municipio...</option>
                                </select>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label for="calle" class="form-label fw-semibold">Calle donde se extravió *</label>
                                <input type="text" class="form-control" id="calle" name="calle" required>
                            </div>
                            <div class="col-md-4">
                                <label for="entre_calle" class="form-label fw-semibold">Entre calle</label>
                                <input type="text" class="form-control" id="entre_calle" name="entre_calle">
                            </div>
                            <div class="col-md-4">
                                <label for="y_calle" class="form-label fw-semibold">Y calle</label>
                                <input type="text" class="form-control" id="y_calle" name="y_calle">
                            </div>
                        </div>

                        <div class="mb-0">
                            <label for="comentarios" class="form-label fw-semibold">Detalles adicionales del extravío</label>
                            <textarea class="form-control" id="comentarios" name="comentarios" rows="2" 
                                      placeholder="Ej. Se asustó con cohetes y corrió rumbo al parque..."></textarea>
                        </div>
                    </div>
                </div>

                <!-- BLOQUE 3: DATOS DE CONTACTO DEL RESPONSABLE -->
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white border-bottom py-2">
                        <h5 class="fw-bold mb-0 text-primary">3. Datos de Contacto del Responsable</h5>
                    </div>
                    <div class="card-body p-4">
                        <p class="small text-muted mb-3">
                            Por seguridad y privacidad, tu teléfono <strong>no</strong> aparecerá impreso en el cartel público; se resguarda en el sistema para el seguimiento de tu folio.
                        </p>
                        <div class="row g-3">
                            <div class="col-md-7">
                                <label for="nombre_invitado" class="form-label fw-semibold">Nombre completo *</label>
                                <input type="text" class="form-control" id="nombre_invitado" name="nombre_invitado" required>
                            </div>
                            <div class="col-md-5">
                                <label for="telefono_invitado" class="form-label fw-semibold">Teléfono de contacto (10 dígitos) *</label>
                                <input type="tel" class="form-control" id="telefono_invitado" name="telefono_invitado" 
                                       maxlength="10" pattern="[0-9]{10}" placeholder="Ej. 5512345678"
                                       oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- BOTONES DE ACCIÓN -->
                <div class="row g-3 mb-5">
                    <div class="col-md-6">
                        <button type="submit" class="btn btn-danger btn-lg w-100 fw-bold shadow-sm">
                            Generar Reporte y Cartel QR
                        </button>
                    </div>
                    <div class="col-md-6">
                        <a href="/RUPE/index.php" class="btn btn-outline-secondary btn-lg w-100 fw-bold shadow-sm">
                            Cancelar y Regresar
                        </a>
                    </div>
                </div>

            </form>
        </div>
    </section>
</main>

<script>
    const urlInvitadoCtrl = '/RUPE/controladores/reporteinvitado.php';

    function cargarRazas() {
        const espId = document.getElementById('especie_id').value;
        const selectRaza = document.getElementById('id_catalogos');
        selectRaza.innerHTML = '<option value="" selected disabled>Cargando razas...</option>';
        selectRaza.disabled = true;

        fetch(`${urlInvitadoCtrl}?action=getRazas&especie_id=${espId}`)
            .then(res => res.json())
            .then(datos => {
                selectRaza.innerHTML = '<option value="" selected disabled>Seleccione raza...</option>';
                datos.forEach(r => {
                    const opt = document.createElement('option');
                    opt.value = r.id_catalogo;
                    opt.textContent = r.nom_raza;
                    selectRaza.appendChild(opt);
                });
                selectRaza.disabled = false;
            });
    }

    function buscarPorCP() {
        const cp = document.getElementById('codigo_postal').value.replace(/\D/g, '');
        const msg = document.getElementById('msg-cp');
        if (cp.length !== 5) return;

        msg.textContent = 'Buscando...';
        fetch(`${urlInvitadoCtrl}?action=getColoniasPorCP&cp=${cp}`)
            .then(res => res.json())
            .then(datos => {
                if (Array.isArray(datos) && datos.length > 0) {
                    const primero = datos[0];
                    document.getElementById('estado_id').value = primero.id_estado;

                    const selectMun = document.getElementById('municipio_id');
                    selectMun.innerHTML = `<option value="${primero.id_alcaldia}" data-nombre="${primero.alcaldia_mpio}" selected>${primero.alcaldia_mpio}</option>`;
                    selectMun.disabled = false;

                    const selectCol = document.getElementById('colonia_nombre');
                    selectCol.innerHTML = '<option value="" selected disabled>Seleccione colonia...</option>';
                    datos.forEach(c => {
                        const opt = document.createElement('option');
                        opt.value = c.nombre_col;
                        opt.textContent = c.nombre_col;
                        opt.dataset.cp = c.codigo_postal;
                        selectCol.appendChild(opt);
                    });
                    selectCol.disabled = false;
                    msg.textContent = 'Ubicación encontrada';
                    msg.className = 'form-text text-success small fw-bold';
                } else {
                    msg.textContent = 'CP no encontrado';
                    msg.className = 'form-text text-danger small';
                }
            });
    }

    function cargarMunicipios() {
        const estadoId = document.getElementById('estado_id').value;
        const selectMun = document.getElementById('municipio_id');
        const selectCol = document.getElementById('colonia_nombre');

        selectMun.innerHTML = '<option value="" selected disabled>Cargando...</option>';
        selectMun.disabled = true;
        selectCol.innerHTML = '<option value="" selected disabled>Seleccione municipio...</option>';
        selectCol.disabled = true;

        fetch(`${urlInvitadoCtrl}?action=getMunicipios&estado_id=${estadoId}`)
            .then(res => res.json())
            .then(datos => {
                selectMun.innerHTML = '<option value="" selected disabled>Seleccione municipio...</option>';
                datos.forEach(m => {
                    const opt = document.createElement('option');
                    opt.value = m.id;
                    opt.textContent = m.nombre;
                    opt.dataset.nombre = m.nombre;
                    selectMun.appendChild(opt);
                });
                selectMun.disabled = false;
            });
    }

    function cargarColoniasPorMunicipio() {
        const selectMun = document.getElementById('municipio_id');
        const nombreAlcaldia = selectMun.options[selectMun.selectedIndex]?.dataset?.nombre || '';
        const selectCol = document.getElementById('colonia_nombre');

        selectCol.innerHTML = '<option value="" selected disabled>Cargando colonias...</option>';
        selectCol.disabled = true;

        fetch(`${urlInvitadoCtrl}?action=getColoniasPorMunicipio&alcaldia_nombre=${encodeURIComponent(nombreAlcaldia)}`)
            .then(res => res.json())
            .then(datos => {
                selectCol.innerHTML = '<option value="" selected disabled>Seleccione colonia...</option>';
                datos.forEach(c => {
                    const opt = document.createElement('option');
                    opt.value = c.nombre_col;
                    opt.textContent = c.nombre_col;
                    opt.dataset.cp = c.codigo_postal;
                    selectCol.appendChild(opt);
                });
                selectCol.disabled = false;
            });
    }

    function completarCPDesdeColonia() {
        const selectCol = document.getElementById('colonia_nombre');
        const cp = selectCol.options[selectCol.selectedIndex]?.dataset?.cp || '';
        if (cp) {
            document.getElementById('codigo_postal').value = String(cp).padStart(5, '0');
        }
    }
</script>
