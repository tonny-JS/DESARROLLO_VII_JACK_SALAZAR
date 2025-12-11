# Proyecto Final TALLER 5: Sistema de Gestión de Estudiantes

Este pequeño proyecto implementa un sistema completo en PHP para gestionar estudiantes, sus materias y rendimiento académico. Fue desarrollado como parte del Taller 5 de manejo avanzado de arreglos en PHP.

## Cómo usar

1. primero tener PHP instalado.
2. al ejecutar el archivo `proyecto_final.php` viendolo desde el lagoon
3. El sistema creará 10 estudiantes de prueba con diferentes carreras y calificaciones.
4. Se mostrarán:
   - Lista de estudiantes
   - Promedio general de la clase
   - Mejor estudiante
   - Reporte por materia
   - Ranking de estudiantes
   - Estadísticas por carrera
   - Resultados de búsqueda
   - Graduación de un estudiante

## Funcionalidades implementadas

- **Clase Estudiante**
  - Agregar materias y calificaciones
  - Calcular promedio
  - Generar indicadores ("honor roll", "en riesgo académico")
  - Mostrar detalles y representación en texto

- **Clase SistemaGestionEstudiantes**
  - Agregar, listar y buscar estudiantes
  - Calcular promedio general
  - Filtrar por carrera
  - Generar ranking y estadísticas
  - Reporte de rendimiento por materia
  - Graduar estudiantes

## Requisitos técnicos cumplidos

- Uso de arreglos asociativos y multidimensionales
- Funciones de orden superior (`array_map`, `array_filter`, `array_reduce`)
- Validación de datos
- Programación orientada a objetos
- Método `__toString()` para impresión clara

