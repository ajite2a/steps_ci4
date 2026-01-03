<?= $this->extend('layouts/app') ?>

<?= $this->section('css') ?>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="container-sm">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Size Masters</h2>
        <a href="<?= route_to('sizemaster.create') ?>" class="btn btn-gradient">Create Size Master</a>
    </div>

    <div class="card">
        <div class="card-body">
            <table id="sizesTable" class="table table-striped" style="width:100%">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Master Name</th>
                        <th>Sizes (New Size)</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($size_masters as $m): ?>
                        <tr>
                            <td><?= esc($m['id']) ?></td>
                            <td><?= esc($m['master_name']) ?></td>
                            <td>
                                <?php 
                                    $sizeDisplay = array_map(function($size) {
                                        $newSize = !empty($size['new_size']) ? ' (' . esc($size['new_size']) . ')' : '';
                                        return esc($size['size_value']) . $newSize;
                                    }, $m['sizes'] ?? []);
                                    echo implode(', ', $sizeDisplay);
                                ?>
                            </td>
                            <td>
                                <a href="<?= route_to('sizemaster.edit', $m['id']) ?>" class="btn btn-sm btn-outline-primary" title="Edit"><i class="fa fa-edit"></i></a>
                                <button data-id="<?= $m['id'] ?>" class="btn btn-sm btn-outline-danger btn-delete" title="Delete"><i class="fa fa-trash"></i></button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            $('#sizesTable').DataTable();

            $('.btn-delete').on('click', function() {
                const id = $(this).data('id');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "This will delete the size master and its sizes.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.createElement('form');
                        form.method = 'post';
                        form.action = '<?= base_url('size-masters') ?>/' + id + '/delete';
                        const csrf = document.createElement('input');
                        csrf.type = 'hidden';
                        csrf.name = '<?= csrf_token() ?>';
                        csrf.value = '<?= csrf_hash() ?>';
                        form.appendChild(csrf);
                        document.body.appendChild(form);
                        form.submit();
                    }
                })
            });
        });
    </script>

<?= $this->endSection() ?>
