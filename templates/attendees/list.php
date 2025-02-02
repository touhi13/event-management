<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div class="card shadow">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h2 class="h4 mb-0"><i class="bi bi-people me-2"></i>Attendees for <?=htmlspecialchars($event['name'])?></h2>
        <a href="/events/attendees/export?id=<?=$event['id']?>" class="btn btn-success">
            <i class="bi bi-download me-2"></i>Export to CSV
        </a>
    </div>

    <div class="card-body">
        <!-- Search and Filter Form -->
        <form method="GET" class="mb-4">
            <input type="hidden" name="id" value="<?=$event['id']?>">
            <div class="row g-3">
                <div class="col-md-4">
                    <input type="text" class="form-control" name="search" placeholder="Search attendees..."
                        value="<?=htmlspecialchars($filters['search'] ?? '')?>">
                </div>
                <div class="col-md-3">
                    <input type="date" class="form-control" name="date_from"
                        value="<?=htmlspecialchars($filters['date_from'] ?? '')?>">
                </div>
                <div class="col-md-3">
                    <input type="date" class="form-control" name="date_to"
                        value="<?=htmlspecialchars($filters['date_to'] ?? '')?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>
                            <a href="?id=<?=$event['id']?>&sort=name&order=<?=$sortBy === 'name' && $sortOrder === 'ASC' ? 'DESC' : 'ASC'?>"
                                class="text-decoration-none text-dark">
                                Name
                                <?php if ($sortBy === 'name'): ?>
                                <i class="bi bi-arrow-<?=$sortOrder === 'ASC' ? 'up' : 'down'?>"></i>
                                <?php endif; ?>
                            </a>
                        </th>
                        <th>
                            <a href="?id=<?=$event['id']?>&sort=email&order=<?=$sortBy === 'email' && $sortOrder === 'ASC' ? 'DESC' : 'ASC'?>"
                                class="text-decoration-none text-dark">
                                Email
                                <?php if ($sortBy === 'email'): ?>
                                <i class="bi bi-arrow-<?=$sortOrder === 'ASC' ? 'up' : 'down'?>"></i>
                                <?php endif; ?>
                            </a>
                        </th>
                        <th>Phone</th>
                        <th>
                            <a href="?id=<?=$event['id']?>&sort=registration_date&order=<?=$sortBy === 'registration_date' && $sortOrder === 'ASC' ? 'DESC' : 'ASC'?>"
                                class="text-decoration-none text-dark">
                                Registration Date
                                <?php if ($sortBy === 'registration_date'): ?>
                                <i class="bi bi-arrow-<?=$sortOrder === 'ASC' ? 'up' : 'down'?>"></i>
                                <?php endif; ?>
                            </a>
                        </th>
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

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <nav aria-label="Page navigation" class="mt-4">
            <ul class="pagination justify-content-center">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?=$i === $currentPage ? 'active' : ''?>">
                    <a class="page-link"
                        href="?id=<?=$event['id']?>&page=<?=$i?>&sort=<?=$sortBy?>&order=<?=$sortOrder?>">
                        <?=$i?>
                    </a>
                </li>
                <?php endfor; ?>
            </ul>
        </nav>
        <?php endif; ?>
    </div>

    <div class="card-footer">
        <a href="/events" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back to Events
        </a>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>