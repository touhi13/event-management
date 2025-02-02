<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card shadow">
            <div class="card-header">
                <h2 class="h4 mb-0">Create New Event</h2>
            </div>
            <div class="card-body">
                <?php if (isset($errors)): ?>
                <div class="alert alert-danger">
                    <?php foreach ($errors as $error): ?>
                    <p class="mb-0"><?=htmlspecialchars($error)?></p>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <form method="POST" action="/events/create">
                    <div class="mb-3">
                        <label for="name" class="form-label">Event Name</label>
                        <input type="text" class="form-control" id="name" name="name"
                            value="<?=htmlspecialchars($_POST['name'] ?? '')?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="4"
                            required><?=htmlspecialchars($_POST['description'] ?? '')?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="event_date" class="form-label">Event Date</label>
                        <input type="datetime-local" class="form-control" id="event_date" name="event_date"
                            value="<?=htmlspecialchars($_POST['event_date'] ?? '')?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="location" class="form-label">Location</label>
                        <input type="text" class="form-control" id="location" name="location"
                            value="<?=htmlspecialchars($_POST['location'] ?? '')?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="max_capacity" class="form-label">Maximum Capacity</label>
                        <input type="number" class="form-control" id="max_capacity" name="max_capacity"
                            value="<?=htmlspecialchars($_POST['max_capacity'] ?? '')?>" required>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Create Event</button>
                        <a href="/events" class="btn btn-secondary">Back to Events</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>