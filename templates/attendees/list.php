<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div class="card shadow">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h2 class="h4 mb-0">Attendees for <?=htmlspecialchars($event['name'])?></h2>
        <a href="/events/attendees/export?id=<?=$event['id']?>" class="btn btn-success">
            <i class="bi bi-download"></i> Export to CSV
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Registration Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($attendees as $attendee): ?>
                    <tr>
                        <td><?=htmlspecialchars($attendee['name'])?></td>
                        <td><?=htmlspecialchars($attendee['email'])?></td>
                        <td><?=htmlspecialchars($attendee['phone'])?></td>
                        <td><?=htmlspecialchars($attendee['registration_date'])?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer">
        <a href="/events" class="btn btn-secondary">Back to Events</a>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>