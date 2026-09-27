<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$conn = mysqli_connect("localhost", "root", "", "student");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

/* ------------------ DELETE ------------------ */
if (isset($_GET['delete'])) {
    $course_id = (int)$_GET['delete'];

    try {
        $stmt = mysqli_prepare($conn, "DELETE FROM course WHERE course_id=?");
        mysqli_stmt_bind_param($stmt, "i", $course_id);
        mysqli_stmt_execute($stmt);
        header("Location: course_manage.php");
        exit();
    } catch (mysqli_sql_exception $e) {
        echo "<p style='color:red;'>Delete Error: " . $e->getMessage() . "</p>";
    }
}

/* ------------------ ADD ------------------ */
if (isset($_POST['add'])) {
    $course_id   = (int)$_POST['course_id'];
    $course_name = $_POST['course_name'];
    $credits     = (int)$_POST['credits'];
    $faculty_id  = (int)$_POST['faculty_id'];

    try {
        $stmt = mysqli_prepare($conn,
            "INSERT INTO course (course_id, course_name, credits, faculty_id) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "isii", $course_id, $course_name, $credits, $faculty_id);
        mysqli_stmt_execute($stmt);
        header("Location: course_manage.php");
        exit();
    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() == 1452) {
            echo "<p style='color:red;'>Invalid Faculty ID! Make sure the faculty exists.</p>";
        } else if ($e->getCode() == 1062) {
            echo "<p style='color:red;'>Course ID already exists!</p>";
        } else {
            echo "<p style='color:red;'>Insert Error: " . $e->getMessage() . "</p>";
        }
    }
}

/* ------------------ UPDATE ------------------ */
if (isset($_POST['update'])) {
    $course_id   = (int)$_POST['course_id'];
    $course_name = $_POST['course_name'];
    $credits     = (int)$_POST['credits'];
    $faculty_id  = (int)$_POST['faculty_id'];

    try {
        $stmt = mysqli_prepare($conn,
            "UPDATE course SET course_name=?, credits=?, faculty_id=? WHERE course_id=?");
        mysqli_stmt_bind_param($stmt, "siii", $course_name, $credits, $faculty_id, $course_id);
        mysqli_stmt_execute($stmt);
        header("Location: course_manage.php");
        exit();
    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() == 1452) {
            echo "<p style='color:red;'>Invalid Faculty ID! Make sure the faculty exists.</p>";
        } else {
            echo "<p style='color:red;'>Update Error: " . $e->getMessage() . "</p>";
        }
    }
}

/* ------------------ EDIT FETCH ------------------ */
$editData = null;
if (isset($_GET['edit'])) {
    $course_id = (int)$_GET['edit'];
    $stmt = mysqli_prepare($conn, "SELECT * FROM course WHERE course_id=?");
    mysqli_stmt_bind_param($stmt, "i", $course_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $editData = mysqli_fetch_assoc($result);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Course</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div style="margin-bottom:15px;">
    <a href="../dashboard/dashboard.php">⬅ Back to Dashboard</a>
</div>

<h2>Manage Course</h2>

<?php if ($editData) { ?>
    <h3>Editing Course ID: <?php echo $editData['course_id']; ?></h3>
<?php } ?>

<form method="POST">

    <label>Course ID:</label>
    <input type="number" name="course_id" required
           value="<?php echo $editData['course_id'] ?? ''; ?>"
           <?php if ($editData) echo "readonly"; ?>>
    <br><br>

    <label>Course Name:</label>
    <input type="text" name="course_name" required
           value="<?php echo htmlspecialchars($editData['course_name'] ?? ''); ?>">
    <br><br>

    <label>Credits:</label>
    <input type="number" name="credits" required
           value="<?php echo $editData['credits'] ?? ''; ?>">
    <br><br>

    <label>Faculty ID:</label>
    <input type="number" name="faculty_id" required
           value="<?php echo $editData['faculty_id'] ?? ''; ?>">
    <br><br>

    <?php if ($editData) { ?>
        <button type="submit" name="update">Update Course</button>
        <a href="course_manage.php">Cancel</a>
    <?php } else { ?>
        <button type="submit" name="add">Add Course</button>
    <?php } ?>

</form>

<hr>

<h3>Course List</h3>

<table border="1" cellpadding="10">
<tr>
    <th>Course ID</th>
    <th>Course Name</th>
    <th>Credits</th>
    <th>Faculty ID</th>
    <th>Actions</th>
</tr>

<?php
$result = mysqli_query($conn, "SELECT * FROM course ORDER BY course_id");

while ($row = mysqli_fetch_assoc($result)) {
?>
<tr>
    <td><?php echo $row['course_id']; ?></td>
    <td><?php echo htmlspecialchars($row['course_name']); ?></td>
    <td><?php echo $row['credits']; ?></td>
    <td><?php echo $row['faculty_id']; ?></td>
    <td>
        <a href="course_manage.php?edit=<?php echo $row['course_id']; ?>">Edit</a> |
        <a href="course_manage.php?delete=<?php echo $row['course_id']; ?>"
           onclick="return confirm('Are you sure?')">Delete</a>
    </td>
</tr>
<?php } ?>

</table>

</body>
</html>