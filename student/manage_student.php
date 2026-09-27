<?php
require("../config/auth_check.php");
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

include("../config/db_connect.php");

$message = "";

/* ------------------ INSERT ------------------ */
if (isset($_POST['add'])) {

    $student_id = (int)$_POST['student_id'];
    $name  = $_POST['name'];
    $dob   = $_POST['dob'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $department = $_POST['department'];

    try {
        $stmt = mysqli_prepare($conn,
            "INSERT INTO student_info (student_id, name, dob, email, phone, department) VALUES (?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "isssss", $student_id, $name, $dob, $email, $phone, $department);
        mysqli_stmt_execute($stmt);

        header("Location: manage_student.php");
        exit();

    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() == 1062) {
            $message = "Error: Student ID already exists (Primary Key violation).";
        } else {
            $message = "Database Error: " . $e->getMessage();
        }
    }
}


/* ------------------ DELETE ------------------ */
if (isset($_GET['delete'])) {

    $student_id = (int)$_GET['delete'];

    try {
        $stmt = mysqli_prepare($conn, "DELETE FROM student_info WHERE student_id=?");
        mysqli_stmt_bind_param($stmt, "i", $student_id);
        mysqli_stmt_execute($stmt);

        header("Location: manage_student.php");
        exit();

    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() == 1451) {
            $message = "Error: Cannot delete this student. 
                        This record is referenced in another table (Foreign Key constraint).";
        } else {
            $message = "Database Error: " . $e->getMessage();
        }
    }
}


/* ------------------ UPDATE ------------------ */
if (isset($_POST['update'])) {

    $student_id = (int)$_POST['student_id'];
    $name  = $_POST['name'];
    $dob   = $_POST['dob'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $department = $_POST['department'];

    try {
        $stmt = mysqli_prepare($conn,
            "UPDATE student_info SET name=?, dob=?, email=?, phone=?, department=? WHERE student_id=?");
        mysqli_stmt_bind_param($stmt, "sssssi", $name, $dob, $email, $phone, $department, $student_id);
        mysqli_stmt_execute($stmt);

        header("Location: manage_student.php");
        exit();

    } catch (mysqli_sql_exception $e) {
        $message = "Database Error: " . $e->getMessage();
    }
}


/* ------------------ EDIT FETCH ------------------ */
$editData = null;

if (isset($_GET['edit'])) {
    $student_id = (int)$_GET['edit'];

    $stmt = mysqli_prepare($conn, "SELECT * FROM student_info WHERE student_id=?");
    mysqli_stmt_bind_param($stmt, "i", $student_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $editData = mysqli_fetch_assoc($result);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Students</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div style="margin-bottom:15px;">
    <a href="../dashboard/dashboard.php">⬅ Back to Dashboard</a>
</div>

<h2>Manage Students</h2>

<?php if($message != ""): ?>
    <p class="error"><?php echo $message; ?></p>
<?php endif; ?>

<!-- ================= FORM ================= -->

<form method="POST">

<?php if (!$editData) { ?>
    <label>Student ID:</label>
    <input type="number" name="student_id" required>
<?php } else { ?>
    <input type="hidden" name="student_id"
           value="<?php echo $editData['student_id']; ?>">
    <p><strong>Editing Student ID:
        <?php echo $editData['student_id']; ?></strong></p>
<?php } ?>

<label>Name:</label>
<input type="text" name="name" required
       value="<?php echo htmlspecialchars($editData['name'] ?? ''); ?>">

<label>DOB:</label>
<input type="date" name="dob" required
       value="<?php echo $editData['dob'] ?? ''; ?>">

<label>Email:</label>
<input type="email" name="email" required
       value="<?php echo htmlspecialchars($editData['email'] ?? ''); ?>">

<label>Phone:</label>
<input type="text" name="phone" required
       value="<?php echo htmlspecialchars($editData['phone'] ?? ''); ?>">

<label>Department:</label>
<input type="text" name="department" required
       value="<?php echo htmlspecialchars($editData['department'] ?? ''); ?>">

<br>

<?php if ($editData) { ?>
    <input type="submit" name="update" value="Update Student">
    <button type="button" onclick="window.location='manage_student.php'">
        Cancel
    </button>
<?php } else { ?>
    <input type="submit" name="add" value="Add Student">
<?php } ?>

</form>

<br><br>

<!-- ================= TABLE ================= -->

<table>
<tr>
    <th>ID</th>
    <th>Name</th>
    <th>DOB</th>
    <th>Email</th>
    <th>Phone</th>
    <th>Department</th>
    <th>Actions</th>
</tr>

<?php
$data = mysqli_query($conn,
    "SELECT * FROM student_info ORDER BY student_id");

while ($row = mysqli_fetch_assoc($data)) {
?>
<tr>
    <td><?php echo $row['student_id']; ?></td>
    <td><?php echo htmlspecialchars($row['name']); ?></td>
    <td><?php echo $row['dob']; ?></td>
    <td><?php echo htmlspecialchars($row['email']); ?></td>
    <td><?php echo htmlspecialchars($row['phone']); ?></td>
    <td><?php echo htmlspecialchars($row['department']); ?></td>
    <td>
        <a href="?edit=<?php echo $row['student_id']; ?>">Edit</a> |
        <a href="?delete=<?php echo $row['student_id']; ?>"
           onclick="return confirm('Delete this student?')">
           Delete
        </a>
    </td>
</tr>
<?php } ?>

</table>

</body>
</html>