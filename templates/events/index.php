<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div class="card shadow">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h2 class="h4 mb-0"><i class="bi bi-calendar-event me-2"></i>Events</h2>
        <a href="/events/create" class="btn btn-primary">
            <i class="bi bi-plus-lg me-2"></i>Create New Event
        </a>
    </div>
    <div class="card-body">
        <?php if (empty($events)): ?>
        <p class="text-center text-muted">No events found.</p>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Date</th>
                        <th>Location</th>
                        <th>Capacity</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($events as $event): ?>
                    <tr>
                        <td><?=htmlspecialchars($event['name'])?></td>
                        <td><?=htmlspecialchars(date('Y-m-d H:i', strtotime($event['event_date'])))?></td>
                        <td><?=htmlspecialchars($event['location'])?></td>
                        <td><?=htmlspecialchars($event['max_capacity'])?></td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="/events/view?id=<?=$event['id']?>" class="btn btn-info">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="/events/edit?id=<?=$event['id']?>" class="btn btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="/events/register?id=<?=$event['id']?>" class="btn btn-success">
                                    <i class="bi bi-person-plus"></i>
                                </a>
                                <?php if ($isAdmin || $event['user_id'] === $userId): ?>
                                <a href="/events/attendees?id=<?=$event['id']?>" class="btn btn-secondary">
                                    <i class="bi bi-people"></i>
                                </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>