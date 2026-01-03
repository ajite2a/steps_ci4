<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>

<style>
    .container-sm {
        padding: 1rem 0.5rem;
    }
    
    .card {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
    
    .card-body {
        padding: 1.25rem;
    }
    
    .card-title {
        margin-bottom: 1rem !important;
        font-size: 1.5rem;
    }
    
    .mb-3 {
        margin-bottom: 1rem !important;
    }
    
    .form-label {
        margin-bottom: 0.4rem;
        font-weight: 500;
        font-size: 0.95rem;
    }
    
    .form-control {
        padding: 0.4rem 0.75rem;
        font-size: 0.95rem;
        height: auto;
    }
    
    .form-control:focus {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }
    
    .btn-sm {
        padding: 0.4rem 0.75rem;
        font-size: 0.85rem;
    }
    
    .d-flex {
        gap: 0.75rem !important;
    }
    
    .alert {
        padding: 0.75rem 1rem;
        margin-bottom: 1rem;
        font-size: 0.95rem;
    }
</style>

<div class="container-sm">
    <div class="card">
        <div class="card-body">
            <h2 class="card-title mb-4">
                <?php if (isset($supplier) && !empty($supplier)): ?>
                    Edit Supplier
                <?php else: ?>
                    Create New Supplier
                <?php endif; ?>
            </h2>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert" style="margin-bottom: 1rem;">
                    <?= session()->getFlashdata('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <form action="<?php if (isset($supplier) && !empty($supplier)): ?><?= route_to('supplier.update', $supplier['id']) ?><?php else: ?><?= route_to('supplier.store') ?><?php endif; ?>" method="post" id="supplierForm">
                <?= csrf_field() ?>

                <div class="mb-3">
                    <label for="supplier_name" class="form-label">Supplier Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="supplier_name" name="supplier_name" value="<?= isset($supplier) ? esc($supplier['supplier_name']) : '' ?>" placeholder="Enter supplier name" autofocus required>
                </div>

                <div class="mb-3">
                    <label for="phone" class="form-label">Phone</label>
                    <input type="text" class="form-control" id="phone" name="phone" value="<?= isset($supplier) ? esc($supplier['phone'] ?? '') : '' ?>" placeholder="Enter phone number">
                </div>

                <div class="mb-3">
                    <label for="gst" class="form-label">GST Number</label>
                    <input type="text" class="form-control" id="gst" name="gst" value="<?= isset($supplier) ? esc($supplier['gst'] ?? '') : '' ?>" placeholder="Enter GST number">
                </div>

                <div class="mb-3">
                    <label for="address" class="form-label">Address</label>
                    <textarea class="form-control" id="address" name="address" rows="3" placeholder="Enter address"><?= isset($supplier) ? esc($supplier['address'] ?? '') : '' ?></textarea>
                </div>

                <div class="d-flex gap-2" style="margin-top: 1.25rem;">
                    <button type="submit" class="btn btn-gradient" style="padding: 0.4rem 1.25rem; font-size: 0.9rem;">
                        <?php if (isset($supplier) && !empty($supplier)): ?>
                            Update
                        <?php else: ?>
                            Create
                        <?php endif; ?>
                    </button>
                    <a href="<?= route_to('supplier.index') ?>" class="btn btn-outline-secondary text-dark text-decoration-none" style="padding: 0.4rem 1.25rem; font-size: 0.9rem;">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const supplierNameInput = document.getElementById('supplier_name');
        const phoneInput = document.getElementById('phone');
        const gstInput = document.getElementById('gst');
        const addressInput = document.getElementById('address');
        const supplierForm = document.getElementById('supplierForm');

        // All form inputs in order
        const formInputs = [supplierNameInput, phoneInput, gstInput, addressInput];

        // Handle Enter key to focus next input
        formInputs.forEach((input, index) => {
            input.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    
                    // If there's a next input, focus it
                    if (index < formInputs.length - 1) {
                        formInputs[index + 1].focus();
                    } 
                    // If this is the last input, show submit confirmation
                    else {
                        showSubmitConfirmation();
                    }
                }
            });
        });

        // Show confirmation dialog when pressing Enter on last input
        function showSubmitConfirmation() {
            Swal.fire({
                title: 'Ready to Submit?',
                text: 'Do you want to submit the form now?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#007bff',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Submit',
                cancelButtonText: 'Continue Editing'
            }).then((result) => {
                if (result.isConfirmed) {
                    supplierForm.submit();
                }
            });
        }

        // Form submission validation
        supplierForm.addEventListener('submit', function(e) {
            const supplierName = supplierNameInput.value.trim();

            if (supplierName === '') {
                e.preventDefault();
                Swal.fire({
                    title: 'Error',
                    text: 'Please enter supplier name',
                    icon: 'error',
                    confirmButtonColor: '#dc3545'
                });
                supplierNameInput.focus();
                return false;
            }
        });
    });
</script>

<?= $this->endSection() ?>
