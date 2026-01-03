<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>

<h2 class="mb-4">Welcome to Your Dashboard</h2>

<div class="row mb-4">
    <div class="col-md-12">
        <div class="card card-custom">
            <div class="card-body">
                <p class="card-text">You have successfully logged in using the Access Database authentication system.</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card card-custom h-100">
            <div class="card-body">
                <h5 class="card-title">👤 User Information</h5>
                <p class="card-text"><strong>Name:</strong> <?= esc($user_name) ?></p>
                <p class="card-text"><strong>Email:</strong> <?= esc($user_email) ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card card-custom h-100">
            <div class="card-body">
                <h5 class="card-title">🔒 Security Status</h5>
                <p class="card-text"><strong>Status:</strong> Authenticated</p>
                <p class="card-text"><strong>Session:</strong> Active</p>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card card-custom h-100">
            <div class="card-body">
                <h5 class="card-title">💾 Database</h5>
                <p class="card-text"><strong>Type:</strong> Microsoft Access</p>
                <p class="card-text"><strong>Connection:</strong> Active</p>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
