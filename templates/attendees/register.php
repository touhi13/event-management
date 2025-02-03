<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div class="card shadow">
    <div class="card-header">
        <h2 class="h4 mb-0">Register for <?=htmlspecialchars($event['name'])?></h2>
    </div>

    <div class="card-body">
        <form id="registrationForm" method="POST" class="needs-validation" novalidate>
            <!-- Alert for form-level errors -->
            <div id="formAlert" class="alert alert-danger d-none"></div>

            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" id="name" name="name" required minlength="3"
                    value="<?=htmlspecialchars($_POST['name'] ?? '')?>">
                <div class="invalid-feedback" data-field="name"></div>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required
                    value="<?=htmlspecialchars($_POST['email'] ?? '')?>">
                <div class="invalid-feedback" data-field="email"></div>
            </div>

            <div class="mb-3">
                <label for="phone" class="form-label">Phone</label>
                <input type="tel" class="form-control" id="phone" name="phone" required
                    value="<?=htmlspecialchars($_POST['phone'] ?? '')?>">
                <div class="invalid-feedback" data-field="phone"></div>
            </div>

            <div class="d-flex justify-content-between">
                <a href="/events/view?id=<?=$event['id']?>" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Back
                </a>
                <button type="submit" class="btn btn-primary" id="submitBtn">
                    <span class="spinner-border spinner-border-sm d-none me-2" role="status" aria-hidden="true"></span>
                    Register
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('registrationForm');
    const submitBtn = document.getElementById('submitBtn');
    const spinner = submitBtn.querySelector('.spinner-border');
    const formAlert = document.getElementById('formAlert');

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        // Reset previous errors
        formAlert.classList.add('d-none');
        form.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

        // Show loading state
        submitBtn.disabled = true;
        spinner.classList.remove('d-none');

        // Collect form data
        const formData = new FormData(form);

        // Send AJAX request
        fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message and redirect
                    window.location.href = data.redirect;
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