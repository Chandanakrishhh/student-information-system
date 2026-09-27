<?php
$conn = mysqli_connect("localhost", "root", "", "student");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

/* ------------------ DELETE ------------------ */
if (isset($_GET['delete'])) {
    $faculty_id = (int)$_GET['delete'];

    mysqli_query($conn, "DELETE FROM faculty WHERE faculty_id=$faculty_id");

    header("Location: manage_faculty.php");
    exit();
}

/* ------------------ ADD ------------------ */
if (isset($_POST['add'])) {

    $faculty_id = (int)$_POST['faculty_id'];
    $name  = mysqli_real_escape_string($conn, $_POST['faculty_name']);
    $dept  = mysqli_real_escape_string($conn, $_POST['department']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    // Check duplicate ID
    $check = mysqli_query($conn, 
        "SELECT faculty_id FROM faculty WHERE faculty_id=$faculty_id");

    if (mysqli_num_rows($check) > 0) {
        echo "<p style='color:red;'>Faculty ID already exists!</p>";
    } else {

        $insert = "INSERT INTO faculty 
                   (faculty_id, faculty_name, department, email)
                   VALUES 
                   ($faculty_id, '$name', '$dept', '$email')";

        if (mysqli_query($conn, $insert)) {
            header("Location: manage_faculty.php");
            exit();
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    }
}

/* ------------------ UPDATE ------------------ */
if (isset($_POST['update'])) {

    $faculty_id = (int)$_POST['faculty_id'];
    $name  = mysqli_real_escape_string($conn, $_POST['faculty_name']);
    $dept  = mysqli_real_escape_string($conn, $_POST['department']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    $update = "UPDATE faculty
               SET faculty_name='$name',
                   department='$dept',
                   email='$email'
               WHERE faculty_id=$faculty_id";

    if (mysqli_query($conn, $update)) {
        header("Location: manage_faculty.php");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

/* ------------------ EDIT FETCH ------------------ */
$editData = null;

if (isset($_GET['edit'])) {
    $faculty_id = (int)$_GET['edit'];

    $result = mysqli_query($conn,
        "SELECT * FROM faculty WHERE faculty_id=$faculty_id");

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
        value="<?php echo $editData['faculty_name'] ?? ''; ?>">

    <br><br>

    <label>Department:</label>
    <input type="text" name="department" required
        value="<?php echo $editData['department'] ?? ''; ?>">

    <br><br>

    <label>Email:</label>
    <input type="email" name="email" required
        value="<?php echo $editData['email'] ?? ''; ?>">

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
    <td><?php echo $row['faculty_name']; ?></td>
    <td><?php echo $row['department']; ?></td>
    <td><?php echo $row['email']; ?></td>
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