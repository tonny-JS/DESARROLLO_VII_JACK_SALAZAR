document.addEventListener('DOMContentLoaded', function() {
    var deleteEventButtons = document.querySelectorAll('.btn-delete-event');
    deleteEventButtons.forEach(function(button) {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            if (confirm('¿Estás seguro de que quieres eliminar este evento?')) {
                window.location.href = this.href;
            }
        });
    });

    var toggleEventButtons = document.querySelectorAll('.btn-toggle-event');
    toggleEventButtons.forEach(function(button) {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            fetch(this.href)
                .then(function(response) { return response.json(); })
                .then(function(data) {
                    if (data && data.success) {
                        var article = button.closest('article');
                        if (article) article.classList.toggle('published');
                        button.textContent = data.newStatus === 'published' ? '✓ Publicado' : '○ Borrador';
                    }
                })
                .catch(function(error) { console.error('Error:', error); });
        });
    });

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
