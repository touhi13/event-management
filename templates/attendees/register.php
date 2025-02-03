<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div class="card shadow">
    <div class="card-header">
        <h2 class="h4 mb-0">Register for <?=htmlspecialchars($event['name'])?></h2>
    </div>

    <div class="card-body">
        <div id="registrationSuccess" class="alert alert-success d-none">
            Registration successful! You will be redirected shortly...
        </div>

        <form id="registrationForm" method="POST" class="needs-validation" novalidate>
            <div id="formAlert" class="alert alert-danger d-none"></div>

            <div class="mb-3">
                <label for="name" class="form-label">Full Name</label>
                <input type="text" class="form-control" id="name" name="name" required minlength="3">
                <div class="invalid-feedback" data-field="name"></div>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" class="form-control" id="email" name="email" required>
                <div class="invalid-feedback" data-field="email"></div>
            </div>

            <div class="mb-3">
                <label for="phone" class="form-label">Phone Number</label>
                <input type="tel" class="form-control" id="phone" name="phone" required>
                <div class="invalid-feedback" data-field="phone"></div>
            </div>

            <div class="d-flex justify-content-between align-items-center">
                <button type="submit" class="btn btn-primary" id="submitBtn">
                    <span class="spinner-border spinner-border-sm d-none" id="submitSpinner"></span>
                    Register
                </button>
                <a href="/events/view?id=<?=$event['id']?>" class="btn btn-link">Back to Event</a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('registrationForm');
    const submitBtn = document.getElementById('submitBtn');
    const spinner = document.getElementById('submitSpinner');
    const formAlert = document.getElementById('formAlert');
    const successAlert = document.getElementById('registrationSuccess');

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        // Reset previous errors
        formAlert.classList.add('d-none');
        form.querySelectorAll('.is-invalid').forEach(el => {
            el.classList.remove('is-invalid');
            el.nextElementSibling.textContent = '';
        });

        // Show loading state
        submitBtn.disabled = true;
        spinner.classList.remove('d-none');

        // Collect form data
        const formData = new FormData(form);

        // Send AJAX request
        fetch(window.location.href, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    successAlert.classList.remove('d-none');
                    form.classList.add('d-none');

                    // Redirect after delay
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 2000);
                } else if (data.errors) {
                    // Display validation errors
                    Object.entries(data.errors).forEach(([field, message]) => {
                        const input = form.querySelector(`[name="${field}"]`);
                        if (input) {
                            input.classList.add('is-invalid');
                            const feedback = form.querySelector(`[data-field="${field}"]`);
                            if (feedback) {
                                feedback.textContent = message;
                            }
                        } else if (field === 'general' || field === 'capacity') {
                            formAlert.textContent = message;
                            formAlert.classList.remove('d-none');
                        }
                    });
                }
            })
            .catch(error => {
                formAlert.textContent = 'An error occurred. Please try again.';
                formAlert.classList.remove('d-none');
            })
            .finally(() => {
                // Reset loading state
                submitBtn.disabled = false;
                spinner.classList.add('d-none');
            });
    });
});
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>