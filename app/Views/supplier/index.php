<?= $this->extend('layouts/app') ?>

<?= $this->section('css') ?>
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css" rel="stylesheet">
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.10/dist/sweetalert2.min.css" rel="stylesheet">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Suppliers List</h2>
    <a href="<?= route_to('supplier.create') ?>" class="btn btn-gradient">+ Add Supplier</a>
</div>

<?php if (empty($suppliers)): ?>
    <div class="card text-center">
        <div class="card-body py-5">
            <p class="card-text mb-3">No suppliers found</p>
            <a href="<?= route_to('supplier.create') ?>" class="btn btn-gradient">Create First Supplier</a>
        </div>
    </div>
<?php else: ?>
    <div class="card ">
        <div class="card-body">
            <div class="table-responsive">
                <table id="suppliersTable" class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Supplier Name</th>
                            <th>Phone</th>
                            <th>GST</th>
                            <th>Address</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($suppliers as $supplier): ?>
                            <tr>
                                <td><?= esc($supplier['supplier_name']) ?></td>
                                <td><?= esc($supplier['phone'] ?? '-') ?></td>
                                <td><?= esc($supplier['gst'] ?? '-') ?></td>
                                <td><?= esc(substr($supplier['address'] ?? '', 0, 50)) . (strlen($supplier['address'] ?? '') > 50 ? '...' : '') ?></td>
                                <td>
                                    <a href="<?= route_to('supplier.edit', $supplier['id']) ?>" class="btn btn-sm btn-info">Edit</a>
                                    <button type="button" class="btn btn-sm btn-danger delete-supplier" data-id="<?= $supplier['id'] ?>" data-name="<?= esc($supplier['supplier_name']) ?>">Delete</button>
                                </td>
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
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.10/dist/sweetalert2.all.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#suppliersTable').DataTable({
                responsive: true,
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50],
                order: [[0, 'asc']],
                columnDefs: [
                    {
                        targets: -1,
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            // SweetAlert delete confirmation
            $('.delete-supplier').on('click', function(e) {
                e.preventDefault();
                const supplierId = $(this).data('id');
                const supplierName = $(this).data('name');

                Swal.fire({
                    title: 'Delete Supplier?',
                    text: `Are you sure you want to delete "${supplierName}"? This action cannot be undone.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, Delete',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Create and submit the delete form
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = '<?= base_url('suppliers') ?>/' + supplierId + '/delete';
                        
                        const csrfToken = document.createElement('input');
                        csrfToken.type = 'hidden';
                        csrfToken.name = '<?= csrf_token() ?>';
                        csrfToken.value = '<?= csrf_hash() ?>';
                        
                        form.appendChild(csrfToken);
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            });
        });
    </script>
<?= $this->endSection() ?>
