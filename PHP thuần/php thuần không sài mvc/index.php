 <!DOCTYPE html>
 <html lang="en">
 <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" >

    <title>Document</title>
 </head>
 <?php require_once __DIR__ . "/getCourse.php"; ?>
 <body class="container">
    <h1> Course</h1>
    <a href="formCreate.php" class="btn btn-primary">Add Course</a>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>STT</th>
                <th>Course Name</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($courses as $index => $course): ?>
                <tr>
                    <td><?php echo $index + 1 ?></td>
                    <td><?php echo $course[1] ?></td>
                    <td>
                        <a href="formEdit.php?id=<?php echo $course[0] ?>" class="btn btn-warning">Edit</a>
                        <!-- <a href="delete.php?id=<?php echo $course[0] ?>" class="btn btn-danger">Delete</a> -->
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
 </body>
 <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>   

 </html>