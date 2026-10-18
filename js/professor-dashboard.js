document.addEventListener('DOMContentLoaded', function() {
    // Jeton CSRF fourni par la page (balise meta)
    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

    // Gestion du modal d'upload
    const uploadModal = document.getElementById('uploadModal');
    uploadModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const moduleId = button.getAttribute('data-module');
        const contentType = button.getAttribute('data-type');

        document.getElementById('moduleId').value = moduleId;
        document.getElementById('contentType').value = contentType;

        // Mise à jour du titre du modal
        const modalTitle = this.querySelector('.modal-title');
        const titles = {
            'course': 'Ajouter un cours',
            'exercise': 'Ajouter un TD/TP',
            'exam': 'Ajouter un examen'
        };
        modalTitle.textContent = titles[contentType] || 'Ajouter un contenu';
    });

    // Gestion de la suppression du contenu
    document.querySelectorAll('.delete-content').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            if (confirm('Êtes-vous sûr de vouloir supprimer ce contenu ?')) {
                const contentId = this.getAttribute('data-id');
                fetch('delete_content.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ content_id: contentId, csrf_token: csrfToken })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        this.closest('.resource-item').remove();
                    } else {
                        alert('Erreur lors de la suppression : ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert('Une erreur est survenue lors de la suppression');
                });
            }
        });
    });

    // Gestion du formulaire d'upload
    document.getElementById('uploadForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        fetch('upload_content.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload(); // Recharger la page pour afficher le nouveau contenu
            } else {
                alert('Erreur lors du téléchargement : ' + data.message);
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Une erreur est survenue lors du téléchargement');
        });
    });
});
