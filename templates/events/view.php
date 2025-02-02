<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header">
                <h2 class="h4 mb-0"><i class="bi bi-calendar-event me-2"></i><?=htmlspecialchars($event['name'])?></h2>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h5>Description</h5>
                        <p class="text-muted"><?=nl2br(htmlspecialchars($event['description']))?></p>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <h5><i class="bi bi-clock me-2"></i>Date & Time</h5>
                            <p class="text-muted">
                                <?=htmlspecialchars(date('F j, Y g:i A', strtotime($event['event_date'])))?></p>
                        </div>
                        <div class="mb-3">
                            <h5><i class="bi bi-geo-alt me-2"></i>Location</h5>
                            <p class="text-muted"><?=htmlspecialchars($event['location'])?></p>
                        </div>
                        <div class="mb-3">
                            <h5><i class="bi bi-people me-2"></i>Capacity</h5>
                            <p class="text-muted"><?=htmlspecialchars($event['max_capacity'])?> attendees</p>
                        </div>
                        <div class="mb-3">
                            <h5><i class="bi bi-person-check me-2"></i>Available Spots</h5>
                            <p class="text-muted"><?=$event['max_capacity'] - $attendeeCount?></p>
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <?php if ($event['max_capacity'] > $attendeeCount): ?>
                    <a href="/events/register?id=<?=$event['id']?>" class="btn btn-success">
                        <i class="bi bi-person-plus me-2"></i>Register for Event
                    </a>
                    <?php else: ?>
                    <div class="alert alert-warning text-center">
                        This event is at full capacity.
                    </div>
                    <?php endif; ?>

                    <?php if ($isAdmin || $event['user_id'] === $userId): ?>
                    <a href="/events/edit?id=<?=$event['id']?>" class="btn btn-warning">
                        <i class="bi bi-pencil me-2"></i>Edit Event
                    </a>
                    <a href="/events/attendees?id=<?=$event['id']?>" class="btn btn-info">
                        <i class="bi bi-people me-2"></i>View Attendees
                    </a>
                    <?php endif; ?>

                    <a href="/events" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Back to Events
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>