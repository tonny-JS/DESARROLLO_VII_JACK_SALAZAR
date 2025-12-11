<?php
// --------------------------------------------------
// Clase Estudiante
// --------------------------------------------------
class Estudiante {
    // Atributos públicos del estudiante
    public int $id;
    public string $nombre;
    public int $edad;
    public string $carrera;
    public array $materias = []; // Materias con sus calificaciones
    public array $flags = [];    // Indicadores como "honor roll", "en riesgo académico"

    // Constructor que inicializa los datos básicos del estudiante
    public function __construct(int $id, string $nombre, int $edad, string $carrera) {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->edad = $edad;
        $this->carrera = $carrera;
    }

    // Agrega una materia y su calificación al estudiante
    public function agregarMateria(string $materia, float $calificacion): void {
        if ($calificacion < 0 || $calificacion > 100) {
            throw new Exception("Calificación debe estar entre 0 y 100.");
        }
        $this->materias[$materia] = $calificacion;
        $this->actualizarFlags(); // Actualiza los indicadores según el rendimiento
    }

    // Calcula el promedio de todas las materias
    public function obtenerPromedio(): float {
        if (empty($this->materias)) return 0.0;
        return array_sum($this->materias) / count($this->materias);
    }

    // Retorna todos los detalles del estudiante como arreglo asociativo
    public function obtenerDetalles(): array {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'edad' => $this->edad,
            'carrera' => $this->carrera,
            'materias' => $this->materias,
            'promedio' => $this->obtenerPromedio(),
            'flags' => $this->flags
        ];
    }

    // Actualiza los indicadores del estudiante según su rendimiento
    private function actualizarFlags(): void {
        $promedio = $this->obtenerPromedio();
        $this->flags = [];

        if ($promedio >= 90) {
            $this->flags[] = "honor roll";
        }

        // Si el promedio es bajo o hay materias reprobadas (<60)
        if ($promedio < 70 || in_array(false, array_map(fn($cal) => $cal >= 60, $this->materias))) {
            $this->flags[] = "en riesgo académico";
        }
    }

    // Representación en texto del estudiante
    public function __toString(): string {
        $materiasStr = implode(", ", array_map(fn($m, $c) => "$m: $c", array_keys($this->materias), $this->materias));
        $flagsStr = implode(", ", $this->flags);
        return "ID: $this->id | Nombre: $this->nombre | Edad: $this->edad | Carrera: $this->carrera | Promedio: " . number_format($this->obtenerPromedio(),2) . " | Materias: [$materiasStr] | Flags: [$flagsStr]";
    }
}

// --------------------------------------------------
// Clase SistemaGestionEstudiantes
// --------------------------------------------------
class SistemaGestionEstudiantes {
    private array $estudiantes = []; // Estudiantes activos
    private array $graduados = [];   // Estudiantes graduados

    public function agregarEstudiante(Estudiante $estudiante): void {
        $this->estudiantes[$estudiante->id] = $estudiante;
    }

    public function obtenerEstudiante(int $id): ?Estudiante {
        return $this->estudiantes[$id] ?? null;
    }

    public function listarEstudiantes(): array {
        return array_values($this->estudiantes);
    }

    public function calcularPromedioGeneral(): float {
        if (empty($this->estudiantes)) return 0.0;
        $promedios = array_map(fn($e) => $e->obtenerPromedio(), $this->estudiantes);
        return array_sum($promedios) / count($promedios);
    }

    public function obtenerEstudiantesPorCarrera(string $carrera): array {
        return array_filter($this->estudiantes, fn($e) => strcasecmp($e->carrera, $carrera) === 0);
    }

    public function obtenerMejorEstudiante(): ?Estudiante {
        if (empty($this->estudiantes)) return null;
        return array_reduce($this->estudiantes, fn($mejor, $actual) => ($mejor === null || $actual->obtenerPromedio() > $mejor->obtenerPromedio()) ? $actual : $mejor);
    }

    public function generarReporteRendimiento(): array {
        $materiasTotales = [];
        foreach ($this->estudiantes as $e) {
            foreach ($e->materias as $materia => $calificacion) {
                $materiasTotales[$materia][] = $calificacion;
            }
        }

        $reporte = [];
        foreach ($materiasTotales as $materia => $calificaciones) {
            $reporte[$materia] = [
                'promedio' => array_sum($calificaciones)/count($calificaciones),
                'max' => max($calificaciones),
                'min' => min($calificaciones)
            ];
        }
        return $reporte;
    }

    public function graduarEstudiante(int $id): bool {
        if (!isset($this->estudiantes[$id])) return false;
        $this->graduados[$id] = $this->estudiantes[$id];
        unset($this->estudiantes[$id]);
        return true;
    }

    public function generarRanking(): array {
        $ranking = $this->estudiantes;
        usort($ranking, fn($a, $b) => $b->obtenerPromedio() <=> $a->obtenerPromedio());
        return $ranking;
    }

    public function buscarEstudiantes(string $termino): array {
        $termino = strtolower($termino);
        return array_filter($this->estudiantes, fn($e) => str_contains(strtolower($e->nombre), $termino) || str_contains(strtolower($e->carrera), $termino));
    }

    public function generarEstadisticasPorCarrera(): array {
        $estadisticas = [];
        $carreras = array_unique(array_map(fn($e) => $e->carrera, $this->estudiantes));

        foreach ($carreras as $carrera) {
            $estCarrera = $this->obtenerEstudiantesPorCarrera($carrera);
            if (empty($estCarrera)) continue;

            $promedios = array_map(fn($e) => $e->obtenerPromedio(), $estCarrera);
            $mejor = array_reduce($estCarrera, fn($m, $a) => ($m===null||$a->obtenerPromedio()>$m->obtenerPromedio())?$a:$m);

            $estadisticas[$carrera] = [
                'num_estudiantes' => count($estCarrera),
                'promedio_general' => array_sum($promedios)/count($promedios),
                'mejor_estudiante' => $mejor
            ];
        }
        return $estadisticas;
    }
}
?>
