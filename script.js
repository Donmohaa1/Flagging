document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('surveyForm');

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        // Validation supplémentaire avant l'envoi
        if (!validateForm()) {
            alert('Please fill out all required fields.');
            return;
        }

        const formData = new FormData(form);

        fetch('survey.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Form submitted successfully!');
                form.reset();
            } else {
                alert('Error submitting form. Please try again.');
                console.log(data.message);
                console.log(data.errors);
                
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again later.');
        });
    });

    // Validation de la taille des fichiers
    const fileInputs = document.querySelectorAll('input[type="file"]');
    fileInputs.forEach(input => {
        input.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file && file.size > 10 * 1024 * 1024) { // Limite de 10 Mo
                alert('File size exceeds 10MB limit. Please choose a smaller file.');
                e.target.value = ''; // Réinitialiser le champ
            }
        });
    });

    // Fonction de validation supplémentaire
    function validateForm() {
        const requiredFields = document.querySelectorAll('[required]');
        for (let field of requiredFields) {
            if (!field.value.trim()) {
                field.focus();
                return false;
            }
        }
        return true;
    }
});
