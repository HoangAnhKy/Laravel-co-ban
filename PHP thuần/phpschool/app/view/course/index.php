<?php
$title = "course";
ob_start() ?>
    <div class="row">

        <h1>Course</h1>

        <div class="col-6">
            <a href="<?= BASE_URL . "/course/create"; ?>" class="btn btn-success"> <i class="fa-solid fa-plus"></i> Add
                new</a>
        </div>

        <div class="offset-md-2 col-4">
            <div>
                <form class="row">
                    <span class="col-8">
                        <input name="search" class="form-control" placeholder="search..." value="<?= $_GET["search"] ?? ""?>">
                    </span>
                    <button class="col-3 btn btn-primary"><i class="fa-solid fa-magnifying-glass"></i></button>
                </form>
            </div>
        </div>
    </div>

    <table class="table table-striped">
        <thead>
        <tr>
            <th>#</th>
            <th>Name Course</th>
            <th>Created Day</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php if (!empty($courses)): ?>
            <?php foreach ($courses as $key => $course): ?>
                <tr>
                    <td><?= $key + 1 + ((($_GET['page'] ?? 1) - 1) * LIMIT) ?></td>
                    <td><?= $course->name ?></td>
                    <td><?= $course->getDisplayCreated() ?></td>
                    <td>
                        <a class="btn btn-primary" href="<?= BASE_URL . "/course/edit/" . $course->id; ?>"><i
                                    class="fa-regular fa-pen-to-square"></i></a>
                        <a class="btn btn-danger" href="<?= BASE_URL . "/course/delete/" . $course->id; ?>"><i
                                    class="fa-solid fa-xmark"></i></a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
    <?php require __DIR__ . "/../layout/componemt/paginate.php" ?>


<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . "/../layout/default.php"; ?>