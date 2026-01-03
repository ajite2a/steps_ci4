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
    <h2>Items / Products List</h2>
    <a href="<?= route_to('item.create') ?>" class="btn btn-gradient">+ Add Item</a>
</div>

<?php if (empty($items)): ?>
    <div class="card text-center">
        <div class="card-body py-5">
            <p class="card-text mb-3">No items found</p>
            <a href="<?= route_to('item.create') ?>" class="btn btn-gradient">Create First Item</a>
        </div>
    </div>
<?php else: ?>
    <div class="card ">
        <div class="card-body">
            <div class="table-responsive">
                <table id="itemsTable" class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Product Code</th>
                            <th>Product Name</th>
                            <th>Supplier</th>
                            <th>Color</th>
                            <th>Category</th>
                            <th>Purchase Rate</th>
                            <th>MRP</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                            <tr>
                                <td><strong><?= esc($item['product_code']) ?></strong></td>
                                <td><?= esc($item['product_name']) ?></td>
                                <td><?= esc($item['supplier_name'] ?? '-') ?></td>
                                <td><?= esc($item['color_name'] ?? '-') ?></td>
                                <td><?= esc($item['category'] ?? '-') ?></td>
                                <td><?= esc($item['purchase_rate'] ?? '-') ?></td>
                                <td><?= esc($item['mrp'] ?? '-') ?></td>
                                <td>
                                    <a href="<?= route_to('item.edit', $item['id']) ?>" class="btn btn-sm btn-info">Edit</a>
                                    <button type="button" class="btn btn-sm btn-danger delete-item" data-id="<?= $item['id'] ?>" data-name="<?= esc($item['product_name']) ?>">Delete</button>
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
            $('#itemsTable').DataTable({
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

            // Handle delete button
            $(document).on('click', '.delete-item', function() {
                const itemId = $(this).data('id');
                const itemName = $(this).data('name');

                Swal.fire({
                    title: 'Are you sure?',
                    text: `You are about to delete "${itemName}". This action cannot be undone!`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, Delete',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Create and submit the delete form
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = '<?= base_url('items') ?>/' + itemId + '/delete';
                        
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
