<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$conn = mysqli_connect("localhost", "root", "", "student");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$error = "";

/* ------------------ DELETE ------------------ */
if (isset($_GET['delete'])) {

    $exam_id = (int)$_GET['delete'];

    try {

        // Check if record exists
        $check = mysqli_query($conn,
            "SELECT * FROM exam WHERE exam_id=$exam_id");

        if (mysqli_num_rows($check) == 0) {
            $error = "Exam record not found.";
        } else {

            mysqli_query($conn,
                "DELETE FROM exam WHERE exam_id=$exam_id");

            header("Location: exam_manage.php");
            exit();
        }

    } catch (mysqli_sql_exception $e) {

        // Foreign key constraint
        if ($e->getCode() == 1451) {
            $error = "Cannot delete this exam. It is referenced in another table.";
        } else {
            $error = "Delete Error: " . $e->getMessage();
        }
    }
}


/* ------------------ ADD ------------------ */
if (isset($_POST['add'])) {

    $exam_id   = (int)$_POST['exam_id'];
    $exam_type = mysqli_real_escape_string($conn, $_POST['exam_type']);
    $exam_date = $_POST['exam_date'];
    $course_id = (int)$_POST['course_id'];

    try {

        mysqli_query($conn,
            "INSERT INTO exam (exam_id, exam_type, exam_date, course_id)
             VALUES ($exam_id, '$exam_type', '$exam_date', $course_id)");

        header("Location: exam_manage.php");
        exit();

    } catch (mysqli_sql_exception $e) {

        if ($e->getCode() == 1062) {
            $error = "Exam ID already exists (Primary Key violation).";
        } elseif ($e->getCode() == 1452) {
            $error = "Invalid Course ID! Please enter an existing Course ID.";
        } else {
            $error = "Insert Error: " . $e->getMessage();
        }
    }
}


/* ------------------ UPDATE ------------------ */
if (isset($_POST['update'])) {

    $exam_id   = (int)$_POST['exam_id'];
    $exam_type = mysqli_real_escape_string($conn, $_POST['exam_type']);
    $exam_date = $_POST['exam_date'];
    $course_id = (int)$_POST['course_id'];

    try {

        mysqli_query($conn,
            "UPDATE exam SET
                exam_type='$exam_type',
                exam_date='$exam_date',
                course_id=$course_id
             WHERE exam_id=$exam_id");

        header("Location: exam_manage.php");
        exit();

    } catch (mysqli_sql_exception $e) {

        if ($e->getCode() == 1452) {
            $error = "Invalid Course ID!";
        } else {
            $error = "Update Error: " . $e->getMessage();
        }
    }
}


/* ------------------ EDIT FETCH ------------------ */
$editData = null;

if (isset($_GET['edit'])) {

    $exam_id = (int)$_GET['edit'];

    $result = mysqli_query($conn,
        "SELECT * FROM exam WHERE exam_id=$exam_id");

    if (mysqli_num_rows($result) > 0) {
        $editData = mysqli_fetch_assoc($result);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Exam</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div style="margin-bottom:15px;">
    <a href="../dashboard/dashboard.php">⬅ Back to Dashboard</a>
</div>

<h2>Exam Management</h2>

<?php if ($error): ?>
    <p class="error"><?= $error ?></p>
<?php endif; ?>

<form method="POST">

    <label>Exam ID:</label>
    <input type="number" name="exam_id"
           value="<?= $editData['exam_id'] ?? '' ?>"
           required
           <?= $editData ? 'readonly' : '' ?>>

    <label>Exam Type:</label>
    <input type="text" name="exam_type"
           value="<?= $editData['exam_type'] ?? '' ?>" required>

    <label>Exam Date:</label>
    <input type="date" name="exam_date"
           value="<?= $editData['exam_date'] ?? '' ?>" required>

    <label>Course ID:</label>
    <input type="number" name="course_id"
           value="<?= $editData['course_id'] ?? '' ?>" required>

    <br>

    <?php if ($editData): ?>
        <button type="submit" name="update">Update</button>
        <button type="button" onclick="window.location='exam_manage.php'">Cancel</button>
    <?php else: ?>
        <button type="submit" name="add">Add</button>
    <?php endif; ?>

</form>

<hr>

<h3>Exam Records</h3>

<table>
<tr>
    <th>ID</th>
    <th>Type</th>
    <th>Date</th>
    <th>Course ID</th>
    <th>Actions</th>
</tr>

<?php
$result = mysqli_query($conn, "SELECT * FROM exam ORDER BY exam_id");

while ($row = mysqli_fetch_assoc($result)) {
?>
<tr>
    <td><?= $row['exam_id']; ?></td>
    <td><?= $row['exam_type']; ?></td>
    <td><?= $row['exam_date']; ?></td>
    <td><?= $row['course_id']; ?></td>
    <td>
        <a href="?edit=<?= $row['exam_id']; ?>">Edit</a> |
        <a href="?delete=<?= $row['exam_id']; ?>"
           onclick="return confirm('Are you sure you want to delete this exam?')">
           Delete
        </a>
    </td>
</tr>
<?php } ?>

</table>

</body>
</html>