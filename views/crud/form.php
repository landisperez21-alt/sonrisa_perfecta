<form method="post" class="form-grid" autocomplete="off">
    <input type="hidden" name="csrf" value="<?= e(csrf()) ?>">
    <input type="hidden" name="id" value="<?= e($edit['id'] ?? 0) ?>">

    <?php foreach ($controller->campos as $campo => $tipo): ?>
        <?php
        $obligatorio = in_array($campo, $controller->obligatorios, true);
        $ancho = $tipo === 'textarea' ? 'full' : '';
        ?>
        <label class="field <?= $ancho ?>">
            <span class="field-label">
                <?= e(etiqueta($campo)) ?><?= $obligatorio ? ' <em>*</em>' : '' ?>
            </span>

            <?php if (in_array($tipo, tiposReferencia(), true)): ?>
                <?php $opciones = opcionesReferencia($tipo); ?>
                <select name="<?= e($campo) ?>" <?= $obligatorio ? 'required' : '' ?>>
                    <option value="">Selecciona…</option>
                    <?php foreach ($opciones as $opcion): ?>
                        <option value="<?= e($opcion['id']) ?>"
                            <?= (string) ($edit[$campo] ?? '') === (string) $opcion['id'] ? 'selected' : '' ?>>
                            <?= e($opcion['etiqueta']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (!$opciones): ?>
                    <small class="hint">Aún no hay registros. Primero crea uno en
                        <a href="<?= e($tipo) ?>.php"><?= e(ucfirst($tipo)) ?></a>.</small>
                <?php endif; ?>

            <?php elseif (opcionesFijas($tipo)): ?>
                <?php $actual = $edit[$campo] ?? opcionesFijas($tipo)[0]; ?>
                <select name="<?= e($campo) ?>">
                    <?php foreach (opcionesFijas($tipo) as $opcion): ?>
                        <option value="<?= e($opcion) ?>" <?= $actual === $opcion ? 'selected' : '' ?>>
                            <?= e($tipo === 'metodo_pago' ? nombreMetodo($opcion) : ucfirst($opcion)) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

            <?php elseif ($tipo === 'textarea'): ?>
                <textarea name="<?= e($campo) ?>" rows="3" maxlength="2000"
                    <?= $obligatorio ? 'required' : '' ?>><?= e($edit[$campo] ?? '') ?></textarea>

            <?php else: ?>
                <?php
                $valor = $edit[$campo] ?? '';
                $tipoHtml = 'text';
                $extra = '';
                if ($tipo === 'email') { $tipoHtml = 'email'; }
                if ($tipo === 'tel') { $tipoHtml = 'tel'; $extra = 'maxlength="25" inputmode="tel"'; }
                if ($tipo === 'doc') { $extra = 'maxlength="20" pattern="[A-Za-z0-9\-]{6,20}" title="De 6 a 20 letras o números, sin espacios"'; }
                if ($tipo === 'number') { $tipoHtml = 'number'; $extra = 'step="0.01" min="0" inputmode="decimal"'; }
                if ($tipo === 'minutos') { $tipoHtml = 'number'; $extra = 'step="1" min="1" max="600" inputmode="numeric"'; }
                if ($tipo === 'date') { $tipoHtml = 'date'; }
                if ($tipo === 'time') { $tipoHtml = 'time'; $valor = substr((string) $valor, 0, 5); }
                if ($tipo === 'datetime-local') {
                    $tipoHtml = 'datetime-local';
                    $valor = $valor ? str_replace(' ', 'T', substr((string) $valor, 0, 16)) : '';
                }
                if ($tipoHtml === 'text' && $extra === '') { $extra = 'maxlength="255"'; }
                ?>
                <input name="<?= e($campo) ?>" type="<?= e($tipoHtml) ?>" value="<?= e($valor) ?>"
                    <?= $extra ?> <?= $obligatorio ? 'required' : '' ?>>
            <?php endif; ?>
        </label>
    <?php endforeach; ?>

    <div class="actions full">
        <button type="submit" name="accion" value="guardar"><?= $edit ? 'Guardar cambios' : 'Guardar registro' ?></button>
        <a class="btn secondary" href="<?= e($controller->url) ?>">Cancelar</a>
        <button type="reset" class="secondary">Limpiar</button>
    </div>
</form>
