<main class="container py-5 mt-3">
    <section class="pt-3">
        <div class="col-lg-10 mx-auto">
            <div class="p-3 mb-4 text-black rounded-3 shadow-sm" style="background-color: #75b0d7;">
                <h3 class="fw-bold mb-1">Reporte de Mascota Extraviada</h3>
                <p class="mb-0 small">Seleccione la mascota extraviada e indique los detalles de cuándo y dónde fue vista por última vez para generar su folio y cartel con código QR.</p>
            </div>

            <?php if (empty($misMascotas)): ?>
                <div class="alert alert-warning text-center p-5 shadow-sm rounded-3">
                    <h4 class="fw-bold">No tienes mascotas registradas en tu perfil</h4>
                    <p class="mb-3">Para generar un reporte de extravío primero debes registrar los datos y fotografía de tu mascota.</p>
                    <a href="/RUPE/controladores/usuarios/mascotausu.php" class="btn btn-primary fw-bold me-2">+ Registrar Mascota</a>
                    <a href="/RUPE/index.php?c=perfilusuario&a=Inicio" class="btn btn-outline-secondary fw-bold">Regresar</a>
                </div>
            <?php else: ?>
                <form action="/RUPE/controladores/usuarios/reportesusu.php" method="POST" enctype="multipart/form-data" autocomplete="off" class="needs-validation" novalidate>
                    <?php if (!empty($reporte)): ?>
                        <input type="hidden" name="id_reporte" value="<?= $reporte['id_reporte'] ?>">
                        <input type="hidden" name="foto_actual" value="<?= htmlspecialchars($reporte['foto'] ?? '') ?>">
                    <?php endif; ?>

                    <!-- BLOQUE 1: SELECCIÓN Y DATOS DE LA MASCOTA REGISTRADA -->
                    <div class="card border-0 shadow-sm rounded-3 mb-2">
                        <div class="card-header bg-white border-bottom py-1">
                            <h6 class="fw-bold mb-0" style="color: #063388;">1. Datos de la Mascota Extraviada</h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-5 align-items-center mb-2">
                                <div class="col-md-6">
                                    <label for="id_mascota" class="form-label fw-bold">Seleccione su mascota:</label>
                                    <select id="id_mascota" name="id_mascota" class="form-select form-select-sm" onchange="mostrarFichaMascota()" required>
                                        <option value="" selected disabled>Elija una mascota de su lista...</option>
                                        <?php foreach ($misMascotas as $m): ?>
                                            <option value="<?= $m['id_mascota'] ?>"
                                                <?= (!empty($reporte['id_mascota']) && $reporte['id_mascota'] == $m['id_mascota']) ? 'selected' : '' ?>
                                                data-nombre="<?= htmlspecialchars($m['nombre'] ?? 'Sin nombre') ?>"
                                                data-especie="<?= htmlspecialchars($m['nom_especie'] ?? 'No especificada') ?>"
                                                data-raza="<?= htmlspecialchars($m['nom_raza'] ?? 'No especificada') ?>"
                                                data-sexo="<?= htmlspecialchars($m['sexos'] ?? 'No especificado') ?>"
                                                data-edad="<?= htmlspecialchars(!empty($m['edad']) ? $m['edad'] . ' años' : 'No especificada') ?>"
                                                data-color="<?= htmlspecialchars($m['color'] ?? 'No especificado') ?>"
                                                data-tamano="<?= htmlspecialchars($m['tamano'] ?? 'No especificado') ?>"
                                                data-senas="<?= htmlspecialchars($m['sena_particular'] ?? 'Sin señas registradas') ?>"
                                                data-foto="<?= htmlspecialchars(!empty($m['foto']) ? $m['foto'] : '/RUPE/assets/fotos/Mascota.jpg') ?>">
                                                <?= htmlspecialchars($m['nombre'] ?: 'Mascota #' . $m['id_mascota']) ?> (<?= htmlspecialchars($m['nom_especie'] ?? 'Mascota') ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6 text-md-end">
                                    <span class="text-muted small">¿Falta actualizar algún dato o foto de tu mascota?</span><br>
                                    <a href="/RUPE/controladores/usuarios/mascotausu.php?accion=listar" class="btn btn-outline-secondary btn-sm mt-1">Ir a Mis Mascotas</a>
                                </div>
                            </div>

                            <!-- Tarjeta oculta que aparece al elegir la mascota -->
                            <div id="ficha-mascota-seleccionada" class="bg-light border rounded-3 p-3" style="display: none;">
                                <div class="row align-items-center g-3">
                                    <div class="col-sm-3 text-center">
                                        <img id="ficha-foto" src="/RUPE/assets/fotos/Mascota.jpg" alt="Mascota"
                                            class="img-fluid rounded border shadow-sm" style="height: 150px; width: 150px; object-fit: cover;">
                                    </div>
                                    <div class="col-sm-9">
                                        <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                                            <h4 id="ficha-nombre" class="fw-bold text-dark mb-0"></h4>
                                            <span id="ficha-especie" class="badge bg-info text-dark fs-6"></span>
                                        </div>
                                        <div class="row small">
                                            <div class="col-md-6 mb-1"><strong>Raza:</strong> <span id="ficha-raza"></span></div>
                                            <div class="col-md-6 mb-1"><strong>Sexo:</strong> <span id="ficha-sexo"></span></div>
                                            <div class="col-md-6 mb-1"><strong>Edad:</strong> <span id="ficha-edad"></span></div>
                                            <div class="col-md-6 mb-1"><strong>Color:</strong> <span id="ficha-color"></span></div>
                                            <div class="col-md-6 mb-1"><strong>Tamaño:</strong> <span id="ficha-tamano"></span></div>
                                            <div class="col-12 mt-2 p-2 bg-white border rounded">
                                                <strong>Señas particulares:</strong> <span id="ficha-senas" class="text-muted"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- BLOQUE 2: FECHA, HORA, LUGAR Y COMENTARIOS DEL EXTRAVÍO -->
                    <div class="card border-0 shadow-sm rounded-3 mb-3">
                        <div class="card-header bg-white border-bottom py-2">
                            <h6 class="fw-bold mb-0" style="color: #063388;">2. ¿Cuándo y dónde se extravió?</h6>
                        </div>
                        <div class="card-body">
                            <!-- Fecha y Hora -->
                            <div class="row g-3 mb-3">
                                <div class="col-md-4">
                                    <label for="fecha" class="form-label fw-semibold">Fecha del extravío</label>
                                    <input type="date" class="form-control" id="fecha" name="fecha" max="<?= date('Y-m-d') ?>" value="<?= htmlspecialchars($reporte['fecha'] ?? date('Y-m-d')) ?>" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="hora" class="form-label fw-semibold">Hora aproximada</label>
                                    <input type="time" class="form-control" id="hora" name="hora" value="<?= htmlspecialchars(!empty($reporte['hora']) ? substr($reporte['hora'], 0, 5) : date('H:i')) ?>" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="foto" class="form-label fw-semibold">Foto reciente / del día (opcional)</label>
                                    <input type="file" class="form-control" id="foto" name="foto" accept="image/*">
                                    <div class="form-text small">Si no sube una nueva, se usará la de su perfil.</div>
                                </div>
                            </div>

                            <hr class="my-3">
                            <h6 class="fw-bold text-secondary mb-3">Ubicación exacta donde fue vista por última vez</h6>


                            <!-- Ubicación SEPOMEX -->
                            <div class="row g-3 mb-3">
                                <div class="col-md-3">
                                    <label for="codigo_postal" class="form-label fw-semibold">Código Postal</label>
                                    <input type="text" class="form-control" id="codigo_postal" name="codigo_postal" maxlength="5" autocomplete="new-password"
                                        value="<?= htmlspecialchars($reporte['codigo_postal'] ?? '') ?>" placeholder="Ej. 06700" onkeyup="buscarPorCP()">
                                    <div id="msg-cp" class="form-text text-muted small">Escriba 5 dígitos para autocompletar</div>
                                </div>

                                <div class="col-md-3">
                                    <label for="estado_id" class="form-label fw-semibold">Estado</label>
                                    <select id="estado_id" name="estado_id" class="form-select" onchange="cargarMunicipios()" required>
                                        <option value="" disabled <?= empty($reporte['id_estado']) ? 'selected' : '' ?>>Seleccione...</option>
                                        <?php if (!empty($listaEstados)): ?>
                                            <?php foreach ($listaEstados as $est): ?>
                                                <option value="<?= $est['id'] ?>" <?= (!empty($reporte['id_estado']) && $reporte['id_estado'] == $est['id']) ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($est['nombre']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <label for="municipio_id" class="form-label fw-semibold">Municipio / Alcaldía</label>
                                    <select id="municipio_id" name="municipio_id" class="form-select" onchange="cargarColoniasPorMunicipio()" disabled required>
                                        <option value="" disabled <?= empty($reporte['id_alcaldia']) ? 'selected' : '' ?>>Seleccione estado...</option>
                                        <?php if (!empty($listaMunicipios)): ?>
                                            <?php foreach ($listaMunicipios as $mun): ?>
                                                <option value="<?= $mun['id'] ?>" 
                                                        data-nombre="<?= htmlspecialchars($mun['nombre']) ?>"
                                                        <?= (!empty($reporte['id_alcaldia']) && $reporte['id_alcaldia'] == $mun['id']) ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($mun['nombre']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <label for="colonia_nombre" class="form-label fw-semibold">Colonia</label>
                                    <select id="colonia_nombre" name="colonia_nombre" class="form-select" onchange="completarCPDesdeColonia()" disabled required>
                                        <option value="" disabled <?= empty($reporte['nombre_colonia']) ? 'selected' : '' ?>>Seleccione municipio...</option>
                                        <?php if (!empty($listaColonias)): ?>
                                            <?php foreach ($listaColonias as $col): ?>
                                                <option value="<?= htmlspecialchars($col['nombre_col']) ?>" 
                                                        data-cp="<?= htmlspecialchars($col['codigo_postal']) ?>"
                                                        <?= (!empty($reporte['nombre_colonia']) && trim(mb_strtolower($reporte['nombre_colonia'])) === trim(mb_strtolower($col['nombre_col']))) ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($col['nombre_col']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>


                            <div class="row g-3 mb-3">
                                <div class="col-md-4">
                                    <label for="calle" class="form-label fw-semibold">Calle principal o avenida</label>
                                    <input type="text" class="form-control" id="calle" name="calle" value="<?= htmlspecialchars($reporte['calle'] ?? '') ?>" autocomplete="new-password" placeholder="Ej. Av. Madero Sur" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="entre_calle" class="form-label fw-semibold">Entre calle (opcional)</label>
                                    <input type="text" class="form-control" id="entre_calle" name="entre_calle" value="<?= htmlspecialchars($reporte['entre_calle'] ?? '') ?>" placeholder="Ej. Calle Hidalgo">
                                </div>
                                <div class="col-md-4">
                                    <label for="y_calle" class="form-label fw-semibold">Y calle (opcional)</label>
                                    <input type="text" class="form-control" id="y_calle" name="y_calle" value="<?= htmlspecialchars($reporte['y_calle'] ?? '') ?>" placeholder="Ej. Calle Morelos">
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="comentarios" class="form-label fw-semibold">Comentarios y circunstancias del extravío</label>
                                    <textarea class="form-control" id="comentarios" name="comentarios" rows="3"
                                        placeholder="Indique si llevaba collar o placa, si es asustadizo, hacia dónde corrió o cualquier dato importante para quien lo encuentre..."
                                        required><?= htmlspecialchars($reporte['comentarios'] ?? '') ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-2 mb-5">
                        <div class="col-md-6">
                            <button type="submit" class="btn btn-lg w-100 fw-bold shadow-sm" style="background-color: #75b0d7;">
                                Generar Reporte y Cartel QR
                            </button>
                        </div>
                        <div class="col-md-6">
                            <a href="<?= !empty($reporte) ? '/RUPE/controladores/usuarios/reportesusu.php?accion=listar' : '/RUPE/index.php?c=perfilusuario&a=Inicio' ?>"
                                class="btn btn-outline-secondary btn-lg w-100 fw-bold shadow-sm">
                                Cancelar y Regresar
                            </a>
                        </div>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </section>
</main>

<script>
    const urlReporteControlador = '/RUPE/controladores/usuarios/reportesusu.php';

    // 1. Mostrar los datos completos de la mascota elegida en el desplegable
    function mostrarFichaMascota() {
        const select = document.getElementById('id_mascota');
        const opcion = select.options[select.selectedIndex];
        const tarjeta = document.getElementById('ficha-mascota-seleccionada');

        if (!opcion || !opcion.value) {
            tarjeta.style.display = 'none';
            return;
        }

        document.getElementById('ficha-nombre').textContent = opcion.dataset.nombre;
        document.getElementById('ficha-especie').textContent = opcion.dataset.especie;
        document.getElementById('ficha-raza').textContent = opcion.dataset.raza;
        document.getElementById('ficha-sexo').textContent = opcion.dataset.sexo;
        document.getElementById('ficha-edad').textContent = opcion.dataset.edad;
        document.getElementById('ficha-color').textContent = opcion.dataset.color;
        document.getElementById('ficha-tamano').textContent = opcion.dataset.tamano;
        document.getElementById('ficha-senas').textContent = opcion.dataset.senas;
        document.getElementById('ficha-foto').src = opcion.dataset.foto;

        tarjeta.style.display = 'block';
    }

    // 2. Búsqueda automática por Código Postal (5 dígitos)
    function buscarPorCP() {
        const cpInput = document.getElementById('codigo_postal');
        const cp = cpInput.value.replace(/[^0-9]/g, '');
        cpInput.value = cp;

        if (cp.length !== 5) return;

        const msgCp = document.getElementById('msg-cp');
        msgCp.textContent = 'Buscando CP...';

        fetch(`${urlReporteControlador}?action=getColoniasPorCP&cp=${cp}`)
            .then(res => res.json())
            .then(datos => {
                if (Array.isArray(datos) && datos.length > 0) {
                    const primero = datos[0];
                    const selectEstado = document.getElementById('estado_id');
                    const selectMun = document.getElementById('municipio_id');
                    const selectCol = document.getElementById('colonia_nombre');

                    selectEstado.value = primero.id_estado;


                    selectMun.innerHTML = `<option value="${primero.id_alcaldia}" data-nombre="${primero.alcaldia_mpio}" selected>${primero.alcaldia_mpio}</option>`;
                    selectMun.disabled = false;

                    selectCol.innerHTML = '<option value="" selected disabled>Seleccione colonia...</option>';
                    datos.forEach(c => {
                        const opt = document.createElement('option');
                        opt.value = c.nombre_col;
                        opt.textContent = c.nombre_col;
                        opt.dataset.cp = c.codigo_postal;
                        selectCol.appendChild(opt);
                    });
                    selectCol.disabled = false;
                    msgCp.textContent = 'Ubicación encontrada';
                } else {
                    msgCp.textContent = 'CP no encontrado, seleccione manualmente';
                }
            })
            .catch(err => console.error('Error CP:', err));
    }

    // 3. Cargar Municipios al cambiar Estado manualmente
    function cargarMunicipios() {
        const estadoId = document.getElementById('estado_id').value;
        const selectMun = document.getElementById('municipio_id');
        const selectCol = document.getElementById('colonia_nombre');

        selectMun.innerHTML = '<option value="" selected disabled>Cargando...</option>';
        selectMun.disabled = true;
        selectCol.innerHTML = '<option value="" selected disabled>Seleccione municipio...</option>';
        selectCol.disabled = true;

        fetch(`${urlReporteControlador}?action=getMunicipios&estado_id=${estadoId}`)
            .then(res => res.json())
            .then(municipios => {
                selectMun.innerHTML = '<option value="" selected disabled>Seleccione municipio...</option>';
                municipios.forEach(m => {
                    const opt = document.createElement('option');
                    opt.value = m.id;
                    opt.textContent = m.nombre.trim();
                    opt.dataset.nombre = m.nombre.trim();
                    selectMun.appendChild(opt);
                });
                selectMun.disabled = false;
            });
    }

    // 4. Cargar Colonias al cambiar Municipio manualmente
    function cargarColoniasPorMunicipio() {
        const selectMun = document.getElementById('municipio_id');
        const opcionMun = selectMun.options[selectMun.selectedIndex];
        const nombreAlcaldia = opcionMun ? (opcionMun.dataset.nombre || opcionMun.textContent) : '';
        const selectCol = document.getElementById('colonia_nombre');

        selectCol.innerHTML = '<option value="" selected disabled>Cargando colonias...</option>';
        selectCol.disabled = true;

        fetch(`${urlReporteControlador}?action=getColoniasPorMunicipio&alcaldia_nombre=${encodeURIComponent(nombreAlcaldia)}`)
            .then(res => res.json())
            .then(colonias => {
                selectCol.innerHTML = '<option value="" selected disabled>Seleccione colonia...</option>';
                if (Array.isArray(colonias) && colonias.length > 0) {
                    colonias.forEach(c => {
                        const opt = document.createElement('option');
                        opt.value = c.nombre_col;
                        opt.textContent = c.nombre_col;
                        opt.dataset.cp = c.codigo_postal;
                        selectCol.appendChild(opt);
                    });
                    selectCol.disabled = false;
                } else {
                    selectCol.innerHTML = '<option value="" selected disabled>Sin colonias registradas</option>';
                }
            })
            .catch(err => console.error('Error al cargar colonias:', err));
    }

    //Para seleccionar codigo postal cuando se selecciona colonia
    function completarCPDesdeColonia() {
        const selectCol = document.getElementById('colonia_nombre');
        const opcion = selectCol.options[selectCol.selectedIndex];
        if (opcion && opcion.dataset.cp) {
            document.getElementById('codigo_postal').value = opcion.dataset.cp;
            document.getElementById('msg-cp').textContent = 'CP asignado automaticamente';
        }
        document.addEventListener('DOMContentLoaded', () => {
            const selectMascota = document.getElementById('id_mascota');
            if (selectMascota && selectMascota.value) {
                mostrarFichasMascota();
            }
            // 2. Precargar Estado, Municipio y Colonia si estamos editando un reporte
            <?php if (!empty($reporte)): ?>
                const estadoGuardado = "<?= addslashes($reporte['id_estado'] ?? '') ?>";
                const alcIdGuardada = "<?= addslashes($reporte['id_alcaldia'] ?? '') ?>";
                const alcNomGuardada = "<?= addslashes(trim($reporte['nombre_municipio'] ?? '')) ?>";
                const colGuardada = "<?= addslashes(trim($reporte['nombre_colonia'] ?? '')) ?>";
                const cpRaw = "<?= addslashes(trim($reporte['codigo_postal'] ?? '')) ?>";

                if (cpRaw) {
                    document.getElementById('codigo_postal').value = cpRaw.padStart(5, '0');
                }

                if (estadoGuardado) {
                    const selectMun = document.getElementById('municipio_id');
                    const selectCol = document.getElementById('colonia_nombre');

                    fetch(`${urlReporteControlador}?action=getMunicipios&estado_id=${estadoGuardado}`)
                    .then(res => res.json())
                    .then(municipios => {
                        selectMun.innerHTML = '<option value="" disabled>Seleccione municipio...</option>';
                        municipios.forEach(m => {
                            const opt = document.createElement('option');
                            opt.value = m.id;
                            opt.textContent = m.nombre.trim();
                            opt.dataset.nombre = m.nombre.trim();
                            if (String(m.id) === String(alcIdGuardada) || m.nombre.trim().toLowerCase() === alcNomGuardada.toLowerCase()) {
                                opt.selected = true;
                            }
                            selectMun.appendChild(opt);
                        });
                        selectMun.disabled = false;

                        // Una vez seleccionada la alcaldía, cargar sus colonias y marcar la guardada
                        const opcionActiva = selectMun.options[selectMun.selectedIndex];
                        const nombreBuscar = opcionActiva ? opcionActiva.dataset.nombre : alcNomGuardada;

                        if (nombreBuscar) {
                            fetch(`${urlReporteControlador}?action=getColoniasPorMunicipio&alcaldia_nombre=${encodeURIComponent(nombreBuscar)}`)
                                .then(res => res.json())
                                .then(colonias => {
                                    selectCol.innerHTML = '<option value="" disabled>Seleccione colonia...</option>';
                                    colonias.forEach(c => {
                                        const opt = document.createElement('option');
                                        opt.value = c.nombre_col;
                                        opt.textContent = c.nombre_col;
                                        opt.dataset.cp = c.codigo_postal;
                                        if (c.nombre_col.trim().toLowerCase() === colGuardada.toLowerCase()) {
                                            opt.selected = true;
                                        }
                                        selectCol.appendChild(opt);
                                    });
                                    selectCol.disabled = false;
                                });
                        }
                    });
            }
        <?php endif; ?>
    });
    }
</script>