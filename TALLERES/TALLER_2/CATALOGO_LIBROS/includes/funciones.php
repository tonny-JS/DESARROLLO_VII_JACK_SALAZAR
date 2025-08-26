<?php
// Función para simular una "base de datos" de libros
function obtenerLibros() {
    return [
        [
            'titulo' => 'El Quijote',
            'autor' => 'Miguel de Cervantes',
            'anio_publicacion' => 1605,
            'genero' => 'Novela',
            'descripcion' => 'La historia del ingenioso hidalgo Don Quijote de la Mancha.'
        ],
        [
            'titulo' => 'Cien Años de Soledad',
            'autor' => 'Gabriel García Márquez',
            'anio_publicacion' => 1967,
            'genero' => 'Realismo mágico',
            'descripcion' => 'Saga familiar de la familia Buendía en Macondo.'
        ],
        [
            'titulo' => '1984',
            'autor' => 'George Orwell',
            'anio_publicacion' => 1949,
            'genero' => 'Distopía',
            'descripcion' => 'Un mundo totalitario donde el Gran Hermano lo vigila todo.'
        ],
        [
            'titulo' => 'Hamlet',
            'autor' => 'William Shakespeare',
            'anio_publicacion' => 1603,
            'genero' => 'Tragedia',
            'descripcion' => 'La historia del príncipe danés y su venganza.'
        ],
        [
            'titulo' => 'Orgullo y Prejuicio',
            'autor' => 'Jane Austen',
            'anio_publicacion' => 1813,
            'genero' => 'Romance',
            'descripcion' => 'La vida y amor de Elizabeth Bennet y Mr. Darcy.'
        ]
    ];
}

// Función para mostrar los detalles de un libro en HTML
function mostrarDetallesLibro($libro) {
    return "
    <div class='libro'>
        <h2>{$libro['titulo']}</h2>
        <p><strong>Autor:</strong> {$libro['autor']}</p>
        <p><strong>Año de Publicación:</strong> {$libro['anio_publicacion']}</p>
        <p><strong>Género:</strong> {$libro['genero']}</p>
        <p>{$libro['descripcion']}</p>
    </div>
    ";
}
?>
