<?php
$title = "students";
ob_start(); ?>
    <H1>Add Student</H1>

    <form method="post">
        <!-- Student Name -->
        <div class="mb-3">
            <label for="studentName" class="form-label">Student Name</label>
            <input type="text" name="name" class="form-control" id="studentName" placeholder="Enter student name">
        </div>

        <!-- Date of Birth -->
        <div class="mb-3">
            <label for="studentDOB" class="form-label">Date of Birth</label>
            <input type="date" name="birthday" class="form-control" id="studentDOB">
        </div>

        <!-- Class ID -->
        <div class="mb-3">
            <label for="classId" class="form-label">Class ID</label>
            <select class="form-select" id="classId" name="course_id">
                <option selected>Select Class</option>

                <?php if (!empty($courses)){foreach ($courses as $course):?>
                    <option value="<?= $course->id ?>"> <?= $course->name ?></option>
                <?php endforeach;}?>
            </select>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary">Add Student</button>
    </form>

<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . "/../layout/default.php"; ?>