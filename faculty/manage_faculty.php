<?php
require("../config/auth_check.php");
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$conn = mysqli_connect("localhost", "root", "", "student");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$message = "";

/* ------------------ DELETE ------------------ */
if (isset($_GET['delete'])) {
    $faculty_id = (int)$_GET['delete'];

    try {
        $stmt = mysqli_prepare($conn, "DELETE FROM faculty WHERE faculty_id=?");
        mysqli_stmt_bind_param($stmt, "i", $faculty_id);
        mysqli_stmt_execute($stmt);
        header("Location: manage_faculty.php");
        exit();
    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() == 1451) {
            $message = "Cannot delete this faculty. It is referenced in another table (e.g. course or enrollment).";
        } else {
            $message = "Delete Error: " . $e->getMessage();
        }
    }
}

/* ------------------ ADD ------------------ */
if (isset($_POST['add'])) {

    $faculty_id = (int)$_POST['faculty_id'];
    $name  = $_POST['faculty_name'];
    $dept  = $_POST['department'];
    $email = $_POST['email'];

    try {
        $stmt = mysqli_prepare($conn,
            "INSERT INTO faculty (faculty_id, faculty_name, department, email) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "isss", $faculty_id, $name, $dept, $email);
        mysqli_stmt_execute($stmt);
        header("Location: manage_faculty.php");
        exit();
    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() == 1062) {
            $message = "Faculty ID already exists!";
        } else {
            $message = "Insert Error: " . $e->getMessage();
        }
    }
}

/* ------------------ UPDATE ------------------ */
if (isset($_POST['update'])) {

    $faculty_id = (int)$_POST['faculty_id'];
    $name  = $_POST['faculty_name'];
    $dept  = $_POST['department'];
    $email = $_POST['email'];

    try {
        $stmt = mysqli_prepare($conn,
            "UPDATE faculty SET faculty_name=?, department=?, email=? WHERE faculty_id=?");
        mysqli_stmt_bind_param($stmt, "sssi", $name, $dept, $email, $faculty_id);
        mysqli_stmt_execute($stmt);
        header("Location: manage_faculty.php");
        exit();
    } catch (mysqli_sql_exception $e) {
        $message = "Update Error: " . $e->getMessage();
    }
}

/* ------------------ EDIT FETCH ------------------ */
$editData = null;

if (isset($_GET['edit'])) {
    $faculty_id = (int)$_GET['edit'];

    $stmt = mysqli_prepare($conn, "SELECT * FROM faculty WHERE faculty_id=?");
    mysqli_stmt_bind_param($stmt, "i", $faculty_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $editData = mysqli_fetch_assoc($result);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Faculty</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div style="margin-bottom:15px;">
    <a href="../dashboard/dashboard.php">⬅ Back to Dashboard</a>
</div>

<h2>Manage Faculty</h2>

<?php if ($message != "") { ?>
    <p style="color:red;"><?php echo $message; ?></p>
<?php } ?>

<?php if ($editData) { ?>
    <h3>Editing Faculty ID: <?php echo $editData['faculty_id']; ?></h3>
<?php } ?>

<form method="POST">

    <label>Faculty ID:</label>
    <input type="number" name="faculty_id" required
        value="<?php echo $editData['faculty_id'] ?? ''; ?>"
        <?php if ($editData) echo "readonly"; ?>>

    <br><br>

    <label>Faculty Name:</label>
    <input type="text" name="faculty_name" required
        value="<?php echo htmlspecialchars($editData['faculty_name'] ?? ''); ?>">

    <br><br>

    <label>Department:</label>
    <input type="text" name="department" required
        value="<?php echo htmlspecialchars($editData['department'] ?? ''); ?>">

    <br><br>

    <label>Email:</label>
    <input type="email" name="email" required
        value="<?php echo htmlspecialchars($editData['email'] ?? ''); ?>">

    <br><br>

    <?php if ($editData) { ?>
        <button type="submit" name="update">Update Faculty</button>
        <a href="manage_faculty.php">Cancel</a>
    <?php } else { ?>
        <button type="submit" name="add">Add Faculty</button>
    <?php } ?>

</form>

<hr>

<h3>Faculty List</h3>

<table border="1" cellpadding="10">
<tr>
    <th>ID</th>
    <th>Name</th>
    <th>Department</th>
    <th>Email</th>
    <th>Actions</th>
</tr>

<?php
$result = mysqli_query($conn, "SELECT * FROM faculty");

while ($row = mysqli_fetch_assoc($result)) {
?>
<tr>
    <td><?php echo $row['faculty_id']; ?></td>
    <td><?php echo htmlspecialchars($row['faculty_name']); ?></td>
    <td><?php echo htmlspecialchars($row['department']); ?></td>
    <td><?php echo htmlspecialchars($row['email']); ?></td>
    <td>
        <a href="manage_faculty.php?edit=<?php echo $row['faculty_id']; ?>">Edit</a> |
        <a href="manage_faculty.php?delete=<?php echo $row['faculty_id']; ?>"
           onclick="return confirm('Are you sure?')">Delete</a>
    </td>
</tr>
<?php } ?>

</table>

</body>
</html>