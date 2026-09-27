<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$conn = mysqli_connect("localhost", "root", "", "student");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

/* ------------------ DELETE ------------------ */
if (isset($_GET['delete'])) {
    $enrollment_id = (int)$_GET['delete'];

    try {
        mysqli_query($conn, "DELETE FROM enrollment WHERE enrollment_id=$enrollment_id");
        // Relative redirect
        header("Location: manage_enrollment.php");
        exit();
    } catch (mysqli_sql_exception $e) {
        echo "<p style='color:red;'>Delete Error: " . $e->getMessage() . "</p>";
    }
}

/* ------------------ ADD ------------------ */
if (isset($_POST['add'])) {
    $enrollment_id = (int)$_POST['enrollment_id'];
    $student_id = (int)$_POST['student_id'];
    $course_id = (int)$_POST['course_id'];
    $attendance_percentage = (float)$_POST['attendance_percentage'];

    try {
        $insert = "INSERT INTO enrollment (enrollment_id, student_id, course_id, attendance_percentage)
                   VALUES ($enrollment_id, $student_id, $course_id, $attendance_percentage)";
        mysqli_query($conn, $insert);
        header("Location: manage_enrollment.php");
        exit();
    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() == 1452) {
            echo "<p style='color:red;'>Invalid Student ID or Course ID! Make sure they exist.</p>";
        } else if ($e->getCode() == 1062) {
            echo "<p style='color:red;'>Enrollment ID already exists!</p>";
        } else {
            echo "<p style='color:red;'>Insert Error: " . $e->getMessage() . "</p>";
        }
    }
}

/* ------------------ UPDATE ------------------ */
if (isset($_POST['update'])) {
    $enrollment_id = (int)$_POST['enrollment_id'];
    $student_id = (int)$_POST['student_id'];
    $course_id = (int)$_POST['course_id'];
    $attendance_percentage = (float)$_POST['attendance_percentage'];

    try {
        $update = "UPDATE enrollment
                   SET student_id=$student_id, course_id=$course_id, attendance_percentage=$attendance_percentage
                   WHERE enrollment_id=$enrollment_id";
        mysqli_query($conn, $update);
        header("Location: manage_enrollment.php");
        exit();
    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() == 1452) {
            echo "<p style='color:red;'>Invalid Student ID or Course ID! Make sure they exist.</p>";
        } else {
            echo "<p style='color:red;'>Update Error: " . $e->getMessage() . "</p>";
        }
    }
}

/* ------------------ FETCH DATA FOR EDIT ------------------ */
$editData = null;
if (isset($_GET['edit'])) {
    $enrollment_id = (int)$_GET['edit'];
    $result = mysqli_query($conn, "SELECT * FROM enrollment WHERE enrollment_id=$enrollment_id");
    $editData = mysqli_fetch_assoc($result);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Enrollment</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<!-- Dashboard link -->
<div style="margin-bottom:15px;">
    <a href="../dashboard/dashboard.php">⬅ Back to Dashboard</a>
</div>

<h2>Manage Enrollment</h2>

<?php if ($editData) { ?>
    <h3>Editing Enrollment ID: <?php echo $editData['enrollment_id']; ?></h3>
<?php } ?>

<form method="POST">

    <label>Enrollment ID:</label>
    <input type="number" name="enrollment_id" required
           value="<?php echo $editData['enrollment_id'] ?? ''; ?>"
           <?php if ($editData) echo "readonly"; ?>>
    <br><br>

    <label>Student ID:</label>
    <input type="number" name="student_id" required
           value="<?php echo $editData['student_id'] ?? ''; ?>">
    <br><br>

    <label>Course ID:</label>
    <input type="number" name="course_id" required
           value="<?php echo $editData['course_id'] ?? ''; ?>">
    <br><br>

    <label>Attendance Percentage:</label>
    <input type="number" step="0.01" name="attendance_percentage" required
           value="<?php echo $editData['attendance_percentage'] ?? ''; ?>">
    <br><br>

    <?php if ($editData) { ?>
        <button type="submit" name="update">Update Enrollment</button>
        <a href="enrollment_manage.php">Cancel</a>
    <?php } else { ?>
        <button type="submit" name="add">Add Enrollment</button>
    <?php } ?>

</form>

<hr>

<h3>Enrollment List</h3>

<table border="1" cellpadding="10">
<tr>
    <th>Enrollment ID</th>
    <th>Student ID</th>
    <th>Course ID</th>
    <th>Attendance (%)</th>
    <th>Actions</th>
</tr>

<?php
$result = mysqli_query($conn, "SELECT * FROM enrollment ORDER BY enrollment_id");

while ($row = mysqli_fetch_assoc($result)) {
?>
<tr>
    <td><?php echo $row['enrollment_id']; ?></td>
    <td><?php echo $row['student_id']; ?></td>
    <td><?php echo $row['course_id']; ?></td>
    <td><?php echo $row['attendance_percentage']; ?></td>
    <td>
        <!-- RELATIVE LINKS: No leading slashes -->
        <a href="manage_enrollment.php?edit=<?php echo $row['enrollment_id']; ?>">Edit</a> |
        <a href="manage_enrollment.php?delete=<?php echo $row['enrollment_id']; ?>" 
           onclick="return confirm('Are you sure?')">Delete</a>
    </td>
</tr>
<?php } ?>

</table>

</body>
</html>