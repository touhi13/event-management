function registerForEvent(eventId) {
    const form = document.getElementById('registration-form');
    const formData = new FormData(form);

    fetch('/events/register', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', 'Registration successful!');
            } else {
                showAlert('danger', data.error);
            }
        })
        .catch(error => {
            showAlert('danger', 'An error occurred during registration.');
        });
} 