<?= $this->extend('layouts/app') ?>

<?= $this->section('css') ?>
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css" rel="stylesheet">
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.10/dist/sweetalert2.min.css" rel="stylesheet">
<?= $this->endSection() ?>

<?php
/**
 * Helper function to get image URL by checking all supported extensions
 */
$getImageUrl = function($imageCode) {
    if (empty($imageCode)) {
        return null;
    }
    
    $uploadDir = FCPATH . 'uploads/images/';
    $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    
    foreach ($imageExtensions as $ext) {
        $filePath = $uploadDir . $imageCode . '.' . $ext;
        if (file_exists($filePath)) {
            return base_url('uploads/images/' . $imageCode . '.' . $ext);
        }
    }
    
    return null;
};
?>

<?= $this->section('content') ?>

<?php $hasActiveFilters = !empty(array_filter($filters ?? [])); ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Items / Products List</h2>
    <a href="<?= route_to('item.create') ?>" class="btn btn-gradient">+ Add Item</a>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="get" action="<?= route_to('item.index') ?>">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Color</label>
                    <select name="color_id" class="form-select">
                        <option value="">All Colors</option>
                        <?php foreach ($colors as $color): ?>
                            <option value="<?= esc($color['id']) ?>" <?= (($filters['color_id'] ?? '') == $color['id']) ? 'selected' : '' ?>>
                                <?= esc($color['color_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Group</label>
                    <select name="product_group" class="form-select">
                        <option value="">All Groups</option>
                        <?php foreach ($product_groups as $group): ?>
                            <option value="<?= esc($group['id']) ?>" <?= (($filters['product_group'] ?? '') == $group['id']) ? 'selected' : '' ?>>
                                <?= esc($group['group_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Supplier</label>
                    <select name="supplier_id" class="form-select">
                        <option value="">All Suppliers</option>
                        <?php foreach ($suppliers as $supplier): ?>
                            <option value="<?= esc($supplier['id']) ?>" <?= (($filters['supplier_id'] ?? '') == $supplier['id']) ? 'selected' : '' ?>>
                                <?= esc($supplier['supplier_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Tags</label>
                    <select name="tags" class="form-select">
                        <option value="">All Tags</option>
                        <?php foreach ($tags as $tag): ?>
                            <option value="<?= esc($tag['id']) ?>" <?= (($filters['tags'] ?? '') == $tag['id']) ? 'selected' : '' ?>>
                                <?= esc($tag['tag_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Article</label>
                    <select name="article" class="form-select">
                        <option value="">All Articles</option>
                        <?php foreach ($articleOptions as $article): ?>
                            <option value="<?= esc($article) ?>" <?= (($filters['article'] ?? '') === $article) ? 'selected' : '' ?>>
                                <?= esc($article) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Brand</label>
                    <select name="brand" class="form-select">
                        <option value="">All Brands</option>
                        <?php foreach ($brands as $brand): ?>
                            <option value="<?= esc($brand['id']) ?>" <?= (($filters['brand'] ?? '') == $brand['id']) ? 'selected' : '' ?>>
                                <?= esc($brand['brand_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Created Date From</label>
                    <input type="date" name="created_from" class="form-control" value="<?= esc($filters['created_from'] ?? '') ?>">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Created Date To</label>
                    <input type="date" name="created_to" class="form-control" value="<?= esc($filters['created_to'] ?? '') ?>">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Size From</label>
                    <select name="size_from" class="form-select">
                        <option value="">Any</option>
                        <?php foreach ($sizeOptions as $size): ?>
                            <option value="<?= esc($size) ?>" <?= (($filters['size_from'] ?? '') === (string) $size) ? 'selected' : '' ?>>
                                <?= esc($size) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Size To</label>
                    <select name="size_to" class="form-select">
                        <option value="">Any</option>
                        <?php foreach ($sizeOptions as $size): ?>
                            <option value="<?= esc($size) ?>" <?= (($filters['size_to'] ?? '') === (string) $size) ? 'selected' : '' ?>>
                                <?= esc($size) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary">Search</button>
                <a href="<?= route_to('item.index') ?>" class="btn btn-outline-secondary">Clear All</a>
                <span class="btn btn-light disabled">Results: <?= count($items) ?></span>
            </div>
        </form>
    </div>
</div>

<?php if (empty($items)): ?>
    <div class="card text-center">
        <div class="card-body py-5">
            <p class="card-text mb-3"><?= $hasActiveFilters ? 'No items found for the selected filters' : 'No items found' ?></p>
            <?php if ($hasActiveFilters): ?>
                <a href="<?= route_to('item.index') ?>" class="btn btn-outline-secondary">Clear Filters</a>
            <?php else: ?>
                <a href="<?= route_to('item.create') ?>" class="btn btn-gradient">Create First Item</a>
            <?php endif; ?>
        </div>
    </div>
<?php else: ?>
    <div class="card ">
        <div class="card-body">
            <div class="table-responsive">
                <table id="itemsTable" class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Image</th>
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
                            <?php 
                                $imageUrl = $getImageUrl($item['img_code'] ?? '');
                            ?>
                            <tr>
                                <td>
                                    <?php if (!empty($imageUrl)): ?>
                                        <img src="<?= $imageUrl ?>" 
                                             alt="<?= esc($item['product_name']) ?>" 
                                             style="height: 50px; width: 50px; object-fit: cover; border-radius: 4px; cursor: pointer;" 
                                             class="item-image-preview"
                                             data-bs-toggle="modal" 
                                             data-bs-target="#imageModal"
                                             onclick="showImageModal('<?= $imageUrl ?>', '<?= esc($item['product_name']) ?>')">
                                    <?php else: ?>
                                        <div style="height: 50px; width: 50px; background-color: #e9ecef; border-radius: 4px; display: flex; align-items: center; justify-content: center; color: #999; font-size: 12px;">
                                            No Image
                                        </div>
                                    <?php endif; ?>
                                </td>
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

<!-- Image Preview Modal -->
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalTitle">Image Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="imageModalImage" src="" alt="Image Preview" style="max-width: 100%; height: auto; border-radius: 8px;">
            </div>
        </div>
    </div>
</div>

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
        // Function to show image in modal
        window.showImageModal = function(imagePath, productName) {
            $('#imageModalTitle').text(productName);
            $('#imageModalImage').attr('src', imagePath);
            // Modal will be shown automatically via data-bs-toggle
        };

        $(document).ready(function() {
            if ($('#itemsTable').length) {
                $('#itemsTable').DataTable({
                    responsive: true,
                    pageLength: 10,
                    lengthMenu: [5, 10, 25, 50],
                    order: [[1, 'asc']],
                    columnDefs: [
                        {
                            targets: -1,
                            orderable: false,
                            searchable: false
                        },
                        {
                            targets: 0,
                            orderable: false,
                            searchable: false
                        }
                    ]
                });
            }

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
