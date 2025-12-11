// Esperar a que el DOM esté completamente cargado
document.addEventListener('DOMContentLoaded', function() {
    // --- Eliminar eventos ---
    var deleteEventButtons = document.querySelectorAll('.btn-delete-event');
    deleteEventButtons.forEach(function(button) {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            if (confirm('¿Estás seguro de que quieres eliminar este evento?')) {
                window.location.href = this.href;
            }
        });
    });

    // --- Alternar estado de eventos (publicado/borrador) ---
    var toggleEventButtons = document.querySelectorAll('.btn-toggle-event');
    toggleEventButtons.forEach(function(button) {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            fetch(this.href)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Alternar clase 'published' en el artículo del evento
                        this.closest('article').classList.toggle('published');
                        // Actualizar texto del botón según estado
                        this.textContent = data.newStatus === 'published' ? '✓ Publicado' : '○ Borrador';
                    }
                })
                .catch(error => console.error('Error:', error));
        });
    });

    // --- Eliminar inscripciones ---
    var deleteRegistrationButtons = document.querySelectorAll('.btn-delete-registration');
    deleteRegistrationButtons.forEach(function(button) {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            if (confirm('¿Eliminar esta inscripción?')) {
                window.location.href = this.href;
            }
        });
    });
});
