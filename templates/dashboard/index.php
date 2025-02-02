<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div class="container py-4">
    <div class="row">
        <!-- Welcome Section -->
        <div class="col-12 mb-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h4 mb-0">
                        <i class="bi bi-speedometer2 me-2"></i>Dashboard
                    </h2>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h3 class="h5 mb-3">
                        <i class="bi bi-calendar-event me-2"></i>My Events
                    </h3>
                    <p class="h2 mb-0 text-primary"><?=$eventsCount?></p>
                </div>
                <div class="card-footer bg-light">
                    <a href="<?=BASE_PATH?>/events" class="text-decoration-none">View Events</a>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h3 class="h5 mb-3">
                        <i class="bi bi-people me-2"></i>My Registrations
                    </h3>
                    <p class="h2 mb-0 text-success"><?=$registrationsCount?></p>
                </div>
                <div class="card-footer bg-light">
                    <a href="<?=BASE_PATH?>/events/register" class="text-decoration-none">Register for Event</a>
                </div>
            </div>
        </div>

        <?php if ($isAdmin): ?>
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h3 class="h5 mb-3">
                        <i class="bi bi-person-badge me-2"></i>Total Users
                    </h3>
                    <p class="h2 mb-0 text-info"><?=$usersCount?></p>
                </div>
                <div class="card-footer bg-light">
                    <a href="<?=BASE_PATH?>/admin/users" class="text-decoration-none">Manage Users</a>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Recent Activity -->
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h3 class="h5 mb-0">Recent Activity</h3>
                </div>
                <div class="card-body">
                    <?php if (empty($recentActivity)): ?>
                    <p class="text-muted text-center mb-0">No recent activity.</p>
                    <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($recentActivity as $activity): ?>
                        <div class="list-group-item">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1"><?=htmlspecialchars($activity['event_name'])?></h6>
                                <small class="text-muted">
                                    <?=date('M d, Y', strtotime($activity['registration_date']))?>
                                </small>
                            </div>
                            <p class="mb-1">Event Date: <?=date('M d, Y H:i', strtotime($activity['event_date']))?>
                            </p>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>