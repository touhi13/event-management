<form method="POST" id="eventForm" class="needs-validation" novalidate
    action="<?=isset($event['id']) ? '/events/update' : '/events/store'?>">
    <?php if (isset($event['id'])): ?>
    <input type="hidden" name="id" value="<?=htmlspecialchars($event['id'] ?? '')?>">
    <?php endif; ?>

    <div class="mb-3">
        <label for="name" class="form-label">Event Name</label>
        <input type="text" class="form-control" id="name" name="name"
            value="<?=htmlspecialchars($event['name'] ?? '')?>" required minlength="3" maxlength="100">
        <div class="invalid-feedback">
            Event name must be between 3 and 100 characters
        </div>
    </div>

    <div class="mb-3">
        <label for="description" class="form-label">Description</label>
        <textarea class="form-control" id="description" name="description" required minlength="10" maxlength="1000"
            rows="4"><?=htmlspecialchars($event['description'] ?? '')?></textarea>
        <div class="invalid-feedback">
            Description must be between 10 and 1000 characters
        </div>
    </div>

    <div class="mb-3">
        <label for="event_date" class="form-label">Event Date</label>
        <input type="datetime-local" class="form-control" id="event_date" name="event_date"
            value="<?=htmlspecialchars(isset($event['event_date']) ? date('Y-m-d\TH:i', strtotime($event['event_date'])) : '')?>"
            required>
        <div class="invalid-feedback">
            Please select a valid future date
        </div>
    </div>

    <div class="mb-3">
        <label for="location" class="form-label">Location</label>
        <input type="text" class="form-control" id="location" name="location"
            value="<?=htmlspecialchars($event['location'] ?? '')?>" required minlength="3" maxlength="255">
        <div class="invalid-feedback">
            Location must be between 3 and 255 characters
        </div>
    </div>

    <div class="mb-3">
        <label for="max_capacity" class="form-label">Maximum Capacity</label>
        <input type="number" class="form-control" id="max_capacity" name="max_capacity"
            value="<?=htmlspecialchars($event['max_capacity'] ?? '')?>" required min="1" max="1000">
        <div class="invalid-feedback">
            Capacity must be between 1 and 1000
        </div>
    </div>

    <div class="d-grid gap-2">
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-<?=isset($event['id']) ? 'save' : 'plus-circle'?> me-2"></i>
            <?=isset($event['id']) ? 'Update' : 'Create'?> Event
        </button>
        <a href="/events" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back to Events
        </a>
    </div>
</form>

<script>
document.getElementById('eventForm').addEventListener('submit', function(event) {
    if (!this.checkValidity()) {
        event.preventDefault();
        event.stopPropagation();
    }
    this.classList.add('was-validated');

    // Additional custom validations
    const eventDate = new Date(document.getElementById('event_date').value);
    const now = new Date();

    if (eventDate <= now) {
        event.preventDefault();
        const dateInput = document.getElementById('event_date');
        dateInput.setCustomValidity('Event date must be in the future');
        dateInput.reportValidity();
    }
});

// Real-time validation for max capacity
document.getElementById('max_capacity').addEventListener('input', function() {
    const value = parseInt(this.value);
    if (value < 1) {
        this.setCustomValidity('Capacity must be at least 1');
    } else if (value > 1000) {
        this.setCustomValidity('Capacity cannot exceed 1000');
    } else {
        this.setCustomValidity('');
    }
    this.reportValidity();
});
</script>