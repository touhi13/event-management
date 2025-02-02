<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div class="card shadow">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h2 class="h4 mb-0"><i class="bi bi-calendar-event me-2"></i>Events</h2>
        <a href="/events/create" class="btn btn-primary">
            <i class="bi bi-plus-lg me-2"></i>Create New Event
        </a>
    </div>
    <div class="card-body">
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-3">
                        <input type="text" class="form-control" name="search"
                            value="<?=htmlspecialchars($filters['search'])?>" placeholder="Search events...">
                    </div>
                    <div class="col-md-2">
                        <input type="date" class="form-control" name="date_from"
                            value="<?=htmlspecialchars($filters['date_from'])?>">
                    </div>
                    <div class="col-md-2">
                        <input type="date" class="form-control" name="date_to"
                            value="<?=htmlspecialchars($filters['date_to'])?>">
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" name="location">
                            <option value="">All Locations</option>
                            <?php foreach ($locations as $location): ?>
                            <option value="<?=htmlspecialchars($location)?>"
                                <?=$filters['location'] === $location ? 'selected' : ''?>>
                                <?=htmlspecialchars($location)?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary">Filter</button>
                        <a href="/events" class="btn btn-secondary">Reset</a>
                    </div>
                </form>
            </div>
        </div>
        <?php if (empty($events)): ?>
        <p class="text-center text-muted">No events found.</p>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>
                            <a href="?<?=http_build_query(array_merge($_GET, [
    'sort'  => 'name',
    'order' => ($sortBy === 'name' && $sortOrder === 'ASC') ? 'DESC' : 'ASC',
]))?>">
                                Name <?=$sortBy === 'name' ? ($sortOrder === 'ASC' ? '↑' : '↓') : ''?>
                            </a>
                        </th>
                        <th>
                            <a href="?<?=http_build_query(array_merge($_GET, [
    'sort'  => 'event_date',
    'order' => ($sortBy === 'event_date' && $sortOrder === 'ASC') ? 'DESC' : 'ASC',
]))?>">
                                Date <?=$sortBy === 'event_date' ? ($sortOrder === 'ASC' ? '↑' : '↓') : ''?>
                            </a>
                        </th>
                        <th>
                            <a href="?<?=http_build_query(array_merge($_GET, [
    'sort'  => 'location',
    'order' => ($sortBy === 'location' && $sortOrder === 'ASC') ? 'DESC' : 'ASC',
]))?>">
                                Location <?=$sortBy === 'location' ? ($sortOrder === 'ASC' ? '↑' : '↓') : ''?>
                            </a>
                        </th>
                        <th>
                            <a href="?<?=http_build_query(array_merge($_GET, [
    'sort'  => 'max_capacity',
    'order' => ($sortBy === 'max_capacity' && $sortOrder === 'ASC') ? 'DESC' : 'ASC',
]))?>">
                                Capacity <?=$sortBy === 'max_capacity' ? ($sortOrder === 'ASC' ? '↑' : '↓') : ''?>
                            </a>
                        </th>
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
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?=$i === $currentPage ? 'active' : ''?>">
                    <a class="page-link" href="?<?=http_build_query(array_merge($_GET, ['page' => $i]))?>">
                        <?=$i?>
                    </a>
                </li>
                <?php endfor; ?>
            </ul>
        </nav>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>