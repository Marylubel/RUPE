<main class="container py-5 mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
        <div>
            <h2 class="fw-bold mb-0">Mis Mascotas Registradas</h2>
            <p class="text-muted mb-0">Consulta, actualiza los datos o agrega una nueva mascota a tu perfil.</p>
        </div>
        <div>
            <a href="/RUPE/controladores/usuarios/mascotausu.php" class="btn fw-bold shadow-sm me-2"style="background-color: #75b0d7;">
                + Registrar Mascota
            </a>
            <a href="/RUPE/index.php?c=perfilusuario&a=Inicio" class="btn btn-outline-secondary fw-bold">
                Regresar
            </a>
        </div>
    </div>

    <?php if (empty($misMascotas)): ?>
        <div class="alert alert-info text-center py-5 shadow-sm rounded-3">
            <h4 class="fw-bold">Aún no tienes mascotas registradas</h4>
            <p class="mb-3">Registra a tus mascotas para mantener su ficha de identificación lista en caso de extravío.</p>
            <a href="/RUPE/controladores/usuarios/mascotausu.php" class="btn btn-primary btn-lg fw-bold">Registrar mi primera mascota</a>
        </div>
    <?php else: ?>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            <?php foreach ($misMascotas as $m): ?>
                <div class="col">
                    <div class="card h-100 shadow-sm border-0 rounded-3 overflow-hidden">
                        <!-- Fotografía de la mascota -->
                        <img src="<?= htmlspecialchars(!empty($m['foto']) ? $m['foto'] : '/RUPE/assets/fotos/Mascota.jpg'); ?>" 
                             onerror="this.onerror=null; this.src='/RUPE/assets/fotos/Mascota.jpg';"
                             class="card-img-top" 
                             alt="<?= htmlspecialchars($m['nombre'] ?? 'Mascota'); ?>" 
                             style="height: 230px; object-fit: cover;">
                        
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <h4 class="card-title fw-bold mb-0" style="color: #0f4264;">
                                    <?= htmlspecialchars(!empty($m['nombre']) ? $m['nombre'] : 'Sin nombre'); ?>
                                </h4>
                                <span class="badge bg-info text-dark">
                                    <?= htmlspecialchars($m['nom_especie'] ?? 'Mascota'); ?>
                                </span>
                            </div>

                            <ul class="list-unstyled small text-muted mb-3">
                                <li><strong>Raza:</strong> <?= htmlspecialchars($m['nom_raza'] ?? 'No especificada'); ?></li>
                                <li><strong>Sexo:</strong> <?= htmlspecialchars($m['sexos'] ?? 'No especificado'); ?></li>
                                <li><strong>Color:</strong> <?= htmlspecialchars(!empty($m['color']) ? $m['color'] : 'No especificado'); ?></li>
                                <li><strong>Tamaño / Edad:</strong> <?= htmlspecialchars($m['tamano'] ?? '-'); ?> / <?= htmlspecialchars(!empty($m['edad']) ? $m['edad'] . ' años' : '-'); ?></li>
                            </ul>

                            <?php if (!empty($m['sena_particular'])): ?>
                                <p class="card-text small bg-light p-2 rounded border">
                                    <strong>Señas:</strong> <?= htmlspecialchars($m['sena_particular']); ?>
                                </p>
                            <?php endif; ?>
                        </div>

                        <div class="card-footer bg-white border-top-0 pb-3 pt-0 d-flex gap-2">
                            <!-- Abre la misma vista mascota.php precargada con los datos de esta mascota -->
                            <a href="/RUPE/controladores/usuarios/mascotausu.php?id=<?= $m['id_mascota'] ?>" 
                               class="btn btn-outline-primary btn-sm w-50 fw-bold">
                               Editar
                            </a>
                            <a href="/RUPE/controladores/usuarios/mascotausu.php?accion=eliminar&id=<?= $m['id_mascota'] ?>" 
                               class="btn btn-outline-danger btn-sm w-50 fw-bold"
                               onclick="return confirm('¿Estás seguro de que deseas eliminar a <?= htmlspecialchars($m['nombre'] ?? 'esta mascota'); ?>?');">
                               Eliminar
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>
