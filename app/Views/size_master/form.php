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
    
    .table {
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
    }
    
    .table th {
        padding: 0.5rem;
        font-weight: 600;
        font-size: 0.85rem;
    }
    
    .table td {
        padding: 0.4rem;
        vertical-align: middle;
    }
    
    .table input {
        margin-bottom: 0;
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

<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>

<div class="container-sm">
    <div class="card">
        <div class="card-body">
            <h2 class="card-title mb-4">
                <?php if (isset($size_master) && !empty($size_master)): ?>
                    Edit Size Master
                <?php else: ?>
                    Create Size Master
                <?php endif; ?>
            </h2>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert" style="margin-bottom: 1rem;">
                    <?= session()->getFlashdata('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <form action="<?php if (isset($size_master) && !empty($size_master)): ?><?= route_to('sizemaster.update', $size_master['id']) ?><?php else: ?><?= route_to('sizemaster.store') ?><?php endif; ?>" method="post">
                <?= csrf_field() ?>

                <div class="mb-3">
                    <label for="master_name" class="form-label">Master Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="master_name" name="master_name" value="<?= isset($size_master) ? esc($size_master['master_name']) : '' ?>" placeholder="e.g. sm, lg" autofocus required>
                </div>

                <div class="mb-3">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                        <label class="form-label" style="margin-bottom: 0;">Sizes <span class="text-danger">*</span></label>
                        <button type="button" class="btn btn-sm btn-success" id="addSizeRow" style="padding: 0.35rem 0.75rem;">
                            <i class="fa fa-plus"></i> Add Size
                        </button>
                    </div>
                    <div class="table-responsive" style="margin-bottom: 0.5rem;">
                        <table class="table table-bordered table-sm" id="sizesTable" style="margin-bottom: 0;">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 45%; padding: 0.5rem;">Size Value</th>
                                    <th style="width: 45%; padding: 0.5rem;">New Size</th>
                                    <th style="width: 10%; text-align: center; padding: 0.5rem;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (isset($size_master) && !empty($size_master['sizes'])): ?>
                                    <?php foreach ($size_master['sizes'] as $size): ?>
                                        <tr class="size-row">
                                            <td style="padding: 0.35rem;">
                                                <input type="text" class="form-control form-control-sm size-value" name="size_value[]" value="<?= esc($size['size_value']) ?>" placeholder="e.g. 31" required>
                                            </td>
                                            <td style="padding: 0.35rem;">
                                                <input type="text" class="form-control form-control-sm new-size" name="new_size[]" value="<?= esc($size['new_size'] ?? '') ?>" placeholder="e.g. Small">
                                            </td>
                                            <td style="text-align: center; padding: 0.35rem;">
                                                <button type="button" class="btn btn-sm btn-danger remove-row" title="Remove this row" style="padding: 0.25rem 0.5rem;">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr class="size-row">
                                        <td style="padding: 0.35rem;">
                                            <input type="text" class="form-control form-control-sm size-value" name="size_value[]" placeholder="e.g. 31" required>
                                        </td>
                                        <td style="padding: 0.35rem;">
                                            <input type="text" class="form-control form-control-sm new-size" name="new_size[]" placeholder="e.g. Small">
                                        </td>
                                        <td style="text-align: center; padding: 0.35rem;">
                                            <button type="button" class="btn btn-sm btn-danger remove-row" title="Remove this row" style="padding: 0.25rem 0.5rem;">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="d-flex gap-2" style="margin-top: 1.25rem;">
                    <button type="submit" class="btn btn-gradient" style="padding: 0.4rem 1.25rem; font-size: 0.9rem;">
                        <?php if (isset($size_master) && !empty($size_master)): ?>
                            Update
                        <?php else: ?>
                            Create
                        <?php endif; ?>
                    </button>
                    <a href="<?= route_to('sizemaster.index') ?>" class="btn btn-outline-secondary text-dark text-decoration-none" style="padding: 0.4rem 1.25rem; font-size: 0.9rem;">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sizesTable = document.getElementById('sizesTable');
        const addSizeRowBtn = document.getElementById('addSizeRow');
        const masterNameInput = document.getElementById('master_name');

        // Handle Enter on master name to focus first size input
        masterNameInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const firstSizeInput = document.querySelector('.size-value');
                if (firstSizeInput) {
                    firstSizeInput.focus();
                }
            }
        });

        // Add new row function
        function addNewRow() {
            const tbody = sizesTable.querySelector('tbody');
            const newRow = document.createElement('tr');
            newRow.className = 'size-row';
            newRow.innerHTML = `
                <td>
                    <input type="text" class="form-control size-value" name="size_value[]" placeholder="e.g. 31" required>
                </td>
                <td>
                    <input type="text" class="form-control new-size" name="new_size[]" placeholder="e.g. Small">
                </td>
                <td style="text-align: center;">
                    <button type="button" class="btn btn-sm btn-danger remove-row" title="Remove this row">
                        <i class="fa fa-trash"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(newRow);
            attachRemoveListener(newRow.querySelector('.remove-row'));
            attachEnterKeyListener(newRow.querySelector('.size-value'));
            attachEnterKeyListener(newRow.querySelector('.new-size'));
        }

        // Handle Enter key to focus next input or prompt action on last input
        function attachEnterKeyListener(input) {
            input.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const allInputs = Array.from(document.querySelectorAll('input[type="text"].size-value, input[type="text"].new-size'));
                    const currentIndex = allInputs.indexOf(this);
                    
                    // If there's a next input, focus it
                    if (currentIndex !== -1 && currentIndex < allInputs.length - 1) {
                        allInputs[currentIndex + 1].focus();
                    } 
                    // If this is the last input, ask user what to do
                    else if (currentIndex === allInputs.length - 1) {
                        showActionPrompt();
                    }
                }
            });
        }

        // Show dialog when Enter is pressed on last input
        function showActionPrompt() {
            Swal.fire({
                title: 'Add New Row?',
                text: 'Do you want to add a new row or submit the form?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Add New Row',
                cancelButtonText: 'Submit Form'
            }).then((result) => {
                if (result.isConfirmed) {
                    addNewRow();
                    // Focus the first input of the new row
                    setTimeout(function() {
                        const newInputs = Array.from(document.querySelectorAll('input[type="text"].size-value, input[type="text"].new-size'));
                        if (newInputs.length > 0) {
                            newInputs[newInputs.length - 2].focus();
                        }
                    }, 100);
                } else {
                    // Submit the form
                    document.querySelector('form').submit();
                }
            });
        }

        // Attach remove listener to button
        function attachRemoveListener(button) {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const row = this.closest('tr');
                const tbody = sizesTable.querySelector('tbody');
                
                // Don't remove if it's the last row
                if (tbody.querySelectorAll('tr').length > 1) {
                    row.remove();
                } else {
                    alert('You must have at least one size entry');
                }
            });
        }

        // Attach listeners to existing remove buttons
        document.querySelectorAll('.remove-row').forEach(button => {
            attachRemoveListener(button);
        });

        // Attach Enter key listeners to all size input fields
        document.querySelectorAll('.size-value, .new-size').forEach(input => {
            attachEnterKeyListener(input);
        });

        // Add new row button click
        addSizeRowBtn.addEventListener('click', function(e) {
            e.preventDefault();
            addNewRow();
        });

        // Form submission validation
        const form = document.querySelector('form');
        form.addEventListener('submit', function(e) {
            const rows = sizesTable.querySelectorAll('.size-row');
            const sizeValues = Array.from(rows).map(row => {
                return row.querySelector('.size-value').value.trim();
            });

            // Check if all size values are filled
            if (sizeValues.some(val => val === '')) {
                e.preventDefault();
                alert('Please fill in all Size Value fields');
                return false;
            }

            // Check for duplicate size values
            const uniqueSizes = new Set(sizeValues);
            if (uniqueSizes.size !== sizeValues.length) {
                e.preventDefault();
                alert('Duplicate size values are not allowed');
                return false;
            }
        });
    });
</script>

<?= $this->endSection() ?>
