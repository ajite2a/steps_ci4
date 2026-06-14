<?= $this->extend('layouts/app') ?>

<?= $this->section('css') ?>
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.10/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        .badge-insert  { background-color: #198754; }
        .badge-update  { background-color: #0d6efd; }
        .badge-delete  { background-color: #dc3545; }
        .badge-synced  { background-color: #6c757d; }
        .badge-pending { background-color: #fd7e14; }
    </style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>
        Pending Sync
        <?php if ($filter === 'pending' && !empty($logs)): ?>
            <span class="badge bg-danger ms-2"><?= count($logs) ?></span>
        <?php endif; ?>
    </h2>
    <div class="d-flex gap-2">
        <a href="<?= route_to('sync.index') ?>" class="btn btn-sm <?= $filter === 'pending' ? 'btn-gradient' : 'btn-outline-secondary' ?>">Pending Only</a>
        <a href="<?= route_to('sync.index') ?>?filter=all" class="btn btn-sm <?= $filter === 'all' ? 'btn-gradient' : 'btn-outline-secondary' ?>">All Logs</a>
        <?php if ($filter === 'pending' && !empty($logs)): ?>
            <button id="markAllBtn" class="btn btn-sm btn-success">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-check-all me-1" viewBox="0 0 16 16">
                    <path d="M8.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L2.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093L8.95 4.992a.252.252 0 0 1 .02-.022zm-4.3 1.984L2.324 8.384a.75.75 0 1 1 1.06-1.06l1.94 1.946 3.95-4.934a.75.75 0 1 1 1.072 1.05l-4.79 5.99a.75.75 0 0 1-1.08.02l-3.34-3.375a.75.75 0 1 1 1.06-1.06z"/>
                </svg>
                Mark All Synced
            </button>
        <?php endif; ?>
    </div>
</div>

<?php if (empty($logs)): ?>
    <div class="card text-center">
        <div class="card-body py-5">
            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="#198754" class="bi bi-check-circle mb-3" viewBox="0 0 16 16">
                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                <path d="m10.97 4.97-.02.022-3.473 4.425-2.093-2.094a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05"/>
            </svg>
            <p class="card-text text-muted">No pending sync records. Everything is up to date.</p>
        </div>
    </div>
<?php else: ?>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="syncTable" class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Table</th>
                            <th>Record ID</th>
                            <th>Change Type</th>
                            <th>Changed At</th>
                            <th>Status</th>
                            <?php if ($filter === 'pending'): ?>
                                <th>Action</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($logs as $log): ?>
                            <tr id="row-<?= $log['id'] ?>">
                                <td><?= esc($log['id']) ?></td>
                                <td><code><?= esc($log['table_name']) ?></code></td>
                                <td><?= esc($log['record_id']) ?></td>
                                <td>
                                    <?php
                                        $type = strtoupper($log['change_type'] ?? '');
                                        $badgeClass = match($type) {
                                            'INSERT' => 'badge-insert',
                                            'UPDATE' => 'badge-update',
                                            'DELETE' => 'badge-delete',
                                            default  => 'bg-secondary',
                                        };
                                    ?>
                                    <span class="badge <?= $badgeClass ?>"><?= esc($type) ?></span>
                                </td>
                                <td><?= esc($log['changed_at'] ?? '—') ?></td>
                                <td>
                                    <?php if ((int)$log['synced'] === 1): ?>
                                        <span class="badge badge-synced">Synced</span>
                                    <?php else: ?>
                                        <span class="badge badge-pending">Pending</span>
                                    <?php endif; ?>
                                </td>
                                <?php if ($filter === 'pending'): ?>
                                    <td>
                                        <button class="btn btn-sm btn-success mark-synced-btn"
                                                data-id="<?= $log['id'] ?>"
                                                title="Mark as synced">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-check-lg" viewBox="0 0 16 16">
                                                <path d="M12.736 3.97a.733.733 0 0 1 1.047 0c.286.289.29.756.01 1.05L7.88 12.01a.733.733 0 0 1-1.065.02L3.217 8.384a.757.757 0 0 1 0-1.06.733.733 0 0 1 1.047 0l3.052 3.093 5.4-6.425z"/>
                                            </svg>
                                        </button>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.10/dist/sweetalert2.all.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#syncTable').DataTable({
                responsive: true,
                pageLength: 25,
                lengthMenu: [10, 25, 50, 100],
                order: [[4, 'desc']],
                columnDefs: [
                    { targets: [0], width: '60px' },
                    { targets: -1, orderable: false, searchable: false }
                ]
            });

            // Mark single record synced
            $(document).on('click', '.mark-synced-btn', function () {
                const id  = $(this).data('id');
                const btn = $(this);

                $.ajax({
                    url: '<?= base_url('sync') ?>/' + id + '/mark-synced',
                    type: 'POST',
                    data: { '<?= csrf_token() ?>': '<?= csrf_hash() ?>' },
                    success: function (res) {
                        if (res.success) {
                            const row = $('#row-' + id);
                            row.find('.badge-pending').removeClass('badge-pending').addClass('badge-synced').text('Synced');
                            btn.closest('td').html('<span class="text-muted small">Done</span>');
                            updatePendingBadge(-1);
                        } else {
                            Swal.fire('Error', 'Could not mark record as synced.', 'error');
                        }
                    },
                    error: function () {
                        Swal.fire('Error', 'Request failed.', 'error');
                    }
                });
            });

            // Mark all synced
            $('#markAllBtn').on('click', function () {
                Swal.fire({
                    title: 'Mark All as Synced?',
                    text: 'This will mark all pending records as synced.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#198754',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, Mark All',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '<?= base_url('sync/mark-all-synced') ?>',
                            type: 'POST',
                            data: { '<?= csrf_token() ?>': '<?= csrf_hash() ?>' },
                            success: function (res) {
                                if (res.success) {
                                    Swal.fire({
                                        title: 'Done!',
                                        text: 'All records marked as synced.',
                                        icon: 'success',
                                        timer: 1500,
                                        showConfirmButton: false
                                    }).then(() => location.reload());
                                } else {
                                    Swal.fire('Error', 'Could not mark all records as synced.', 'error');
                                }
                            },
                            error: function () {
                                Swal.fire('Error', 'Request failed.', 'error');
                            }
                        });
                    }
                });
            });

            function updatePendingBadge(delta) {
                const badge = $('h2 .badge.bg-danger');
                if (badge.length) {
                    const current = parseInt(badge.text()) + delta;
                    if (current <= 0) {
                        badge.remove();
                        $('#markAllBtn').hide();
                    } else {
                        badge.text(current);
                    }
                }
            }
        });
    </script>
<?= $this->endSection() ?>
