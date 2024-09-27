<?php
$title = "students";
ob_start(); ?>
    <H1>Student</H1>

    <div class="card w-100">
        <div class="card-header">
            <div class="row">
                <div class="col-6">
                    <a class="btn btn-success" href="<?= BASE_URL . "/students/create"?>">Add Students</a>
                </div>
                <div class="col-2"></div>
                <div class="col-4">
                    <div>
                        <form class="row">
                    <span class="col-8">
                        <input name="search" class="form-control" placeholder="search..."
                               value="<?= $_GET["search"] ?? "" ?>">
                    </span>
                            <button class="col-3 btn btn-primary"><i class="fa-solid fa-magnifying-glass"></i></button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Name Student</th>
                    <th>Name Course</th>
                    <th>Age</th>
                </tr>
                </thead>
                <tbody>
                <?php if (!empty($students)) {
                    foreach ($students as $key => $student): ?>
                        <tr>
                            <td><?= $key + 1 + ((($_GET['page'] ?? 1) - 1) * LIMIT) ?></td>
                            <td><?= $student->name ?></td>
                            <td><?= $student->courses->name ?></td>
                            <td><?= $student->age ?></td>
                        </tr>
                    <?php endforeach;
                } ?>
                </tbody>
            </table>
        </div>
        <?php if (isset($row) && $row >= 2): ?>
            <div class="card-footer">
                <?php require __DIR__ . "/../layout/componemt/paginate.php" ?>
            </div>
        <?php endif; ?>
    </div>

<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . "/../layout/default.php"; ?>