<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>

<div class="container-sm">
    <div class="card">
        <div class="card-body">
            <h2 class="card-title mb-4">
                <?php if (isset($color) && !empty($color)): ?>
                    Edit Color
                <?php else: ?>
                    Create New Color
                <?php endif; ?>
            </h2>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= session()->getFlashdata('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <form action="<?php if (isset($color) && !empty($color)): ?><?= route_to('color.update', $color['id']) ?><?php else: ?><?= route_to('color.store') ?><?php endif; ?>" method="post">
                <?= csrf_field() ?>

                <div class="mb-3">
                    <label for="color_name" class="form-label">Color Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="color_name" name="color_name" value="<?= isset($color) ? esc($color['color_name']) : '' ?>" placeholder="Enter color name" autofocus required>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-gradient flex-grow-1">
                        <?php if (isset($color) && !empty($color)): ?>
                            Update Color
                        <?php else: ?>
                            Create Color
                        <?php endif; ?>
                    </button>
                    <a href="<?= route_to('color.index') ?>" class="btn btn-outline-secondary flex-grow-1 text-dark text-decoration-none">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
