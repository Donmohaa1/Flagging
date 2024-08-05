document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('surveyForm');

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(form);

        fetch('/submit-form', {
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
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again later.');
        });
    });

    // File size validation
    const fileInputs = document.querySelectorAll('input[type="file"]');
    fileInputs.forEach(input => {
        input.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file && file.size > 10 * 1024 * 1024) { // 10MB
                alert('File size exceeds 10MB limit. Please choose a smaller file.');
                e.target.value = ''; // Clear the input
            }
        });
    });
});
