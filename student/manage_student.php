<?php
include("../config/db_connect.php");

$message = "";

/* ------------------ INSERT ------------------ */
if (isset($_POST['add'])) {

    $student_id = (int)$_POST['student_id'];
    $name  = mysqli_real_escape_string($conn, $_POST['name']);
    $dob   = mysqli_real_escape_string($conn, $_POST['dob']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $department = mysqli_real_escape_string($conn, $_POST['department']);

    try {

        $check = mysqli_query($conn,
            "SELECT * FROM student_info WHERE student_id = $student_id");

        if (mysqli_num_rows($check) > 0) {
            $message = "Error: Student ID already exists (Primary Key violation).";
        } else {

            $insert = "INSERT INTO student_info 
                       (student_id, name, dob, email, phone, department)
                       VALUES 
                       ($student_id, '$name', '$dob', '$email', '$phone', '$department')";

            mysqli_query($conn, $insert);

            header("Location: manage_student.php");
            exit();
        }

    } catch (mysqli_sql_exception $e) {
        $message = "Database Error: " . $e->getMessage();
    }
}


/* ------------------ DELETE ------------------ */
if (isset($_GET['delete'])) {

    $student_id = (int)$_GET['delete'];

    try {

        mysqli_query($conn,
            "DELETE FROM student_info WHERE student_id=$student_id");

        header("Location: manage_student.php");
        exit();

    } catch (mysqli_sql_exception $e) {

        // Foreign key constraint error
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
    $name  = mysqli_real_escape_string($conn, $_POST['name']);
    $dob   = mysqli_real_escape_string($conn, $_POST['dob']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $department = mysqli_real_escape_string($conn, $_POST['department']);

    try {

        $update = "UPDATE student_info SET
                   name='$name',
                   dob='$dob',
                   email='$email',
                   phone='$phone',
                   department='$department'
                   WHERE student_id=$student_id";

        mysqli_query($conn, $update);

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
    $result = mysqli_query($conn,
        "SELECT * FROM student_info WHERE student_id=$student_id");
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
       value="<?php echo $editData['name'] ?? ''; ?>">

<label>DOB:</label>
<input type="date" name="dob" required
       value="<?php echo $editData['dob'] ?? ''; ?>">

<label>Email:</label>
<input type="email" name="email" required
       value="<?php echo $editData['email'] ?? ''; ?>">

<label>Phone:</label>
<input type="text" name="phone" required
       value="<?php echo $editData['phone'] ?? ''; ?>">

<label>Department:</label>
<input type="text" name="department" required
       value="<?php echo $editData['department'] ?? ''; ?>">

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
    <td><?php echo $row['name']; ?></td>
    <td><?php echo $row['dob']; ?></td>
    <td><?php echo $row['email']; ?></td>
    <td><?php echo $row['phone']; ?></td>
    <td><?php echo $row['department']; ?></td>
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