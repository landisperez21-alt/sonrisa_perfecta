<div class="table-scroll">
    <table id="tabla-datos">
        <thead>
            <tr>
                <?php foreach ($controller->columnasLista as $columna): ?>
                    <th><?= e(etiqueta($columna)) ?></th>
                <?php endforeach; ?>
                <th class="col-acciones">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($filas as $fila): ?>
                <tr>
                    <?php foreach ($controller->columnasLista as $columna): ?>
                        <td data-label="<?= e(etiqueta($columna)) ?>"><?= formatearCelda($columna, $fila[$columna] ?? null) ?></td>
                    <?php endforeach; ?>
                    <td class="col-acciones">
                        <a class="btn secondary small" href="<?= e($controller->url) ?>?editar=<?= e($fila['id']) ?>">Editar</a>
                        <form method="post" data-confirm="¿Seguro que deseas eliminar este registro?">
                            <input type="hidden" name="csrf" value="<?= e(csrf()) ?>">
                            <input type="hidden" name="id" value="<?= e($fila['id']) ?>">
                            <button type="submit" class="danger small" name="accion" value="eliminar">Eliminar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$filas): ?>
                <tr><td class="vacio" colspan="<?= count($controller->columnasLista) + 1 ?>">Todavía no hay registros. Usa el formulario de arriba para crear el primero.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
