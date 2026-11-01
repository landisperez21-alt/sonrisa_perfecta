<?php
// Este modelo NO es un CRUD: solo hace consultas de lectura para los reportes y el panel.
class Reporte
{
    public function __construct(private PDO $db)
    {
    }

    private function filas(string $sql, array $parametros = []): array
    {
        $consulta = $this->db->prepare($sql);
        $consulta->execute($parametros);
        return $consulta->fetchAll();
    }

    // Atenciones realizadas (citas en estado "atendida") dentro del período.
    public function atenciones(string $desde, string $hasta): array
    {
        return $this->filas(
            "SELECT c.fecha, TIME_FORMAT(c.hora, '%H:%i') AS hora,
                    p.nombre AS paciente, o.nombre AS odontologo, s.nombre AS servicio, s.precio
             FROM citas c
             JOIN pacientes p   ON p.id = c.paciente_id
             JOIN odontologos o ON o.id = c.odontologo_id
             JOIN servicios s   ON s.id = c.servicio_id
             WHERE c.estado = 'atendida' AND c.fecha BETWEEN ? AND ?
             ORDER BY c.fecha DESC, c.hora DESC",
            [$desde, $hasta]
        );
    }

    // Ingresos: solo pagos con estado "pagado", agrupados por método.
    public function ingresosPorMetodo(string $desde, string $hasta): array
    {
        return $this->filas(
            "SELECT metodo, COUNT(*) AS cantidad, SUM(monto) AS total
             FROM pagos
             WHERE estado = 'pagado' AND DATE(fecha_pago) BETWEEN ? AND ?
             GROUP BY metodo
             ORDER BY total DESC",
            [$desde, $hasta]
        );
    }

    // Citas pendientes dentro del período.
    public function pendientes(string $desde, string $hasta): array
    {
        return $this->filas(
            "SELECT c.fecha, TIME_FORMAT(c.hora, '%H:%i') AS hora,
                    p.nombre AS paciente, p.telefono, o.nombre AS odontologo, s.nombre AS servicio
             FROM citas c
             JOIN pacientes p   ON p.id = c.paciente_id
             JOIN odontologos o ON o.id = c.odontologo_id
             JOIN servicios s   ON s.id = c.servicio_id
             WHERE c.estado = 'pendiente' AND c.fecha BETWEEN ? AND ?
             ORDER BY c.fecha ASC, c.hora ASC",
            [$desde, $hasta]
        );
    }

    // Cuántas citas hay en cada estado dentro del período.
    public function citasPorEstado(string $desde, string $hasta): array
    {
        $filas = $this->filas(
            "SELECT estado, COUNT(*) AS cantidad
             FROM citas
             WHERE fecha BETWEEN ? AND ?
             GROUP BY estado",
            [$desde, $hasta]
        );
        $resultado = ['pendiente' => 0, 'confirmada' => 0, 'atendida' => 0, 'cancelada' => 0];
        foreach ($filas as $fila) {
            $resultado[$fila['estado']] = (int) $fila['cantidad'];
        }
        return $resultado;
    }

    // Ranking de servicios (sin contar citas canceladas).
    public function serviciosMasSolicitados(string $desde, string $hasta): array
    {
        return $this->filas(
            "SELECT s.nombre, COUNT(*) AS cantidad
             FROM citas c
             JOIN servicios s ON s.id = c.servicio_id
             WHERE c.estado <> 'cancelada' AND c.fecha BETWEEN ? AND ?
             GROUP BY s.id, s.nombre
             ORDER BY cantidad DESC, s.nombre ASC
             LIMIT 5",
            [$desde, $hasta]
        );
    }

    // Números rápidos del panel principal.
    public function resumenHome(): array
    {
        $uno = fn(string $sql) => (float) $this->db->query($sql)->fetchColumn();

        return [
            'pacientes' => (int) $uno('SELECT COUNT(*) FROM pacientes'),
            'citas_hoy' => (int) $uno("SELECT COUNT(*) FROM citas WHERE fecha = CURDATE() AND estado <> 'cancelada'"),
            'pendientes' => (int) $uno("SELECT COUNT(*) FROM citas WHERE estado = 'pendiente' AND fecha >= CURDATE()"),
            'ingresos_mes' => $uno(
                "SELECT COALESCE(SUM(monto), 0) FROM pagos
                 WHERE estado = 'pagado'
                   AND YEAR(fecha_pago) = YEAR(CURDATE()) AND MONTH(fecha_pago) = MONTH(CURDATE())"
            ),
        ];
    }

    public function proximasCitas(int $limite = 5): array
    {
        $consulta = $this->db->prepare(
            "SELECT c.fecha, TIME_FORMAT(c.hora, '%H:%i') AS hora, c.estado,
                    p.nombre AS paciente, s.nombre AS servicio, o.nombre AS odontologo
             FROM citas c
             JOIN pacientes p   ON p.id = c.paciente_id
             JOIN odontologos o ON o.id = c.odontologo_id
             JOIN servicios s   ON s.id = c.servicio_id
             WHERE c.estado IN ('pendiente', 'confirmada')
               AND (c.fecha > CURDATE() OR (c.fecha = CURDATE() AND c.hora >= CURTIME()))
             ORDER BY c.fecha ASC, c.hora ASC
             LIMIT ?"
        );
        $consulta->bindValue(1, $limite, PDO::PARAM_INT);
        $consulta->execute();
        return $consulta->fetchAll();
    }
}
