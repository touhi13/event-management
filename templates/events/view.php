<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header">
                <h2 class="h4 mb-0"><?=htmlspecialchars($event['name'])?></h2>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h5>Description</h5>
                        <p class="text-muted"><?=nl2br(htmlspecialchars($event['description']))?></p>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <h5>Date & Time</h5>
                            <p class="text-muted">
                                <?=htmlspecialchars(date('F j, Y g:i A', strtotime($event['event_date'])))?></p>
                        </div>
                        <div class="mb-3">
                            <h5>Location</h5>
                            <p class="text-muted"><?=htmlspecialchars($event['location'])?></p>
                        </div>
                        <div class="mb-3">
                            <h5>Capacity</h5>
                            <p class="text-muted"><?=htmlspecialchars($event['max_capacity'])?> attendees</p>
                        </div>
                        <div class="mb-3">
                            <h5>Available Spots</h5>
                            <p class="text-muted"><?=$event['max_capacity'] - $attendeeCount?></p>
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <?php if ($event['max_capacity'] > $attendeeCount): ?>
                    <a href="/events/register?id=<?=$event['id']?>" class="btn btn-success">Register for Event</a>
                    <?php else: ?>
                    <div class="alert alert-warning text-center">
                        This event is at full capacity.
                    </div>
                    <?php endif; ?>

                    <?php if ($isAdmin || $event['user_id'] === $userId): ?>
                    <a href="/events/edit?id=<?=$event['id']?>" class="btn btn-warning">Edit Event</a>
                    <a href="/events/attendees?id=<?=$event['id']?>" class="btn btn-info">View Attendees</a>
                    <?php endif; ?>

                    <a href="/events" class="btn btn-secondary">Back to Events</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>