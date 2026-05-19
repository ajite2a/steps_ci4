<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>

<div class="container-fluid px-4 py-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 fw-bold">Settings</h4>
            <small class="text-muted">Manage application configuration</small>
        </div>
    </div>

    <!-- Existing Settings -->
    <?php if (!empty($settings)): ?>
    <form action="<?= route_to('setting.update') ?>" method="post">
        <?= csrf_field() ?>
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold">Current Settings</h6>
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fa-solid fa-floppy-disk me-1"></i> Save All
                </button>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width:35%">Key</th>
                            <th>Value</th>
                            <th style="width:80px" class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($settings as $i => $setting): ?>
                        <tr>
                            <td class="align-middle">
                                <input type="hidden" name="setting_key[]" value="<?= esc($setting['setting_key']) ?>">
                                <code class="fs-6"><?= esc($setting['setting_key']) ?></code>
                            </td>
                            <td class="align-middle">
                                <?php if ($setting['setting_key'] === 'item_scan_type'): ?>
                                    <select class="form-select form-select-sm" name="setting_value[]" style="max-width:200px;">
                                        <option value="barcode" <?= ($setting['setting_value'] === 'barcode') ? 'selected' : '' ?>>Barcode</option>
                                        <option value="qrcode"  <?= ($setting['setting_value'] === 'qrcode')  ? 'selected' : '' ?>>QR Code</option>
                                    </select>
                                <?php else: ?>
                                    <input type="text" class="form-control form-control-sm" name="setting_value[]"
                                           value="<?= esc($setting['setting_value']) ?>" style="max-width:300px;">
                                <?php endif; ?>
                            </td>
                            <td class="text-center align-middle">
                                <form action="<?= route_to('setting.delete', $setting['id']) ?>" method="post"
                                      onsubmit="return confirm('Delete this setting?')">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </form>
    <?php endif; ?>

    <!-- Add New Setting -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-light">
            <h6 class="mb-0 fw-bold">Add New Setting</h6>
        </div>
        <div class="card-body">
            <form action="<?= route_to('setting.store') ?>" method="post">
                <?= csrf_field() ?>
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Key</label>
                        <input type="text" class="form-control" name="setting_key"
                               placeholder="e.g. item_scan_type" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Value</label>
                        <input type="text" class="form-control" name="setting_value"
                               placeholder="e.g. barcode">
                    </div>
                    <div class="col-md-auto">
                        <button type="submit" class="btn btn-success">
                            <i class="fa-solid fa-plus me-1"></i> Add Setting
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
