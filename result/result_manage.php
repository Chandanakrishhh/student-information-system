<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$conn = mysqli_connect("localhost", "root", "", "student");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

/* ------------------ DELETE ------------------ */
if (isset($_GET['delete'])) {
    $result_id = (int)$_GET['delete'];

    try {
        $stmt = mysqli_prepare($conn, "DELETE FROM result WHERE result_id=?");
        mysqli_stmt_bind_param($stmt, "i", $result_id);
        mysqli_stmt_execute($stmt);

        header("Location: result_manage.php");
        exit();
    } catch (mysqli_sql_exception $e) {
        echo "<p style='color:red;'>Delete Error: " . $e->getMessage() . "</p>";
    }
}

/* ------------------ ADD ------------------ */
if (isset($_POST['add'])) {

    $result_id  = (int)$_POST['result_id'];
    $student_id = (int)$_POST['student_id'];
    $exam_id    = (int)$_POST['exam_id'];
    $marks      = (int)$_POST['marks'];
    $grade      = $_POST['grade'];

    try {
        $stmt = mysqli_prepare($conn,
            "INSERT INTO result (result_id, student_id, exam_id, marks, grade)
             VALUES (?, ?, ?, ?, ?)"
        );
        mysqli_stmt_bind_param($stmt, "iiiis",
            $result_id, $student_id, $exam_id, $marks, $grade
        );
        mysqli_stmt_execute($stmt);

        header("Location: result_manage.php");
        exit();
    } catch (mysqli_sql_exception $e) {
        echo "<p style='color:red;'>Insert Error: " . $e->getMessage() . "</p>";
    }
}

/* ------------------ UPDATE ------------------ */
if (isset($_POST['update'])) {

    $result_id  = (int)$_POST['result_id'];
    $student_id = (int)$_POST['student_id'];
    $exam_id    = (int)$_POST['exam_id'];
    $marks      = (int)$_POST['marks'];
    $grade      = $_POST['grade'];

    try {
        $stmt = mysqli_prepare($conn,
            "UPDATE result
             SET student_id=?, exam_id=?, marks=?, grade=?
             WHERE result_id=?"
        );
        mysqli_stmt_bind_param($stmt, "iiisi",
            $student_id, $exam_id, $marks, $grade, $result_id
        );
        mysqli_stmt_execute($stmt);

        header("Location: result_manage.php");
        exit();
    } catch (mysqli_sql_exception $e) {
        echo "<p style='color:red;'>Update Error: " . $e->getMessage() . "</p>";
    }
}

/* ------------------ EDIT FETCH ------------------ */
$editData = null;
if (isset($_GET['edit'])) {
    $result_id = (int)$_GET['edit'];

    $stmt = mysqli_prepare($conn, "SELECT * FROM result WHERE result_id=?");
    mysqli_stmt_bind_param($stmt, "i", $result_id);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $editData = mysqli_fetch_assoc($result);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Result</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div style="margin-bottom:15px;">
    <a href="../dashboard/dashboard.php">⬅ Back to Dashboard</a>
</div>
<h2>Manage Result</h2>

<form method="POST">

    Result ID:
    <input type="number" name="result_id" required
        value="<?php echo isset($editData['result_id']) ? htmlspecialchars($editData['result_id']) : ''; ?>"
        <?php echo $editData ? "readonly" : ""; ?>>
    <br><br>

    Student ID:
    <input type="number" name="student_id" required
        value="<?php echo isset($editData['student_id']) ? htmlspecialchars($editData['student_id']) : ''; ?>">
    <br><br>

    Exam ID:
    <input type="number" name="exam_id" required
        value="<?php echo isset($editData['exam_id']) ? htmlspecialchars($editData['exam_id']) : ''; ?>">
    <br><br>

    Marks:
    <input type="number" name="marks" required
        value="<?php echo isset($editData['marks']) ? htmlspecialchars($editData['marks']) : ''; ?>">
    <br><br>

    Grade:
    <input type="text" name="grade" required
        value="<?php echo isset($editData['grade']) ? htmlspecialchars($editData['grade']) : ''; ?>">
    <br><br>

    <?php if ($editData) { ?>
        <button type="submit" name="update">Update</button>
        <a href="result_manage.php">Cancel</a>
    <?php } else { ?>
        <button type="submit" name="add">Add</button>
    <?php } ?>

</form>

<hr>

<h3>Result List</h3>

<table border="1" cellpadding="8">
<tr>
    <th>Result ID</th>
    <th>Student ID</th>
    <th>Exam ID</th>
    <th>Marks</th>
    <th>Grade</th>
    <th>Actions</th>
</tr>

<?php
$data = mysqli_query($conn, "SELECT * FROM result");

while ($row = mysqli_fetch_assoc($data)) {
?>
<tr>
    <td><?php echo htmlspecialchars($row['result_id']); ?></td>
    <td><?php echo htmlspecialchars($row['student_id']); ?></td>
    <td><?php echo htmlspecialchars($row['exam_id']); ?></td>
    <td><?php echo htmlspecialchars($row['marks']); ?></td>
    <td><?php echo htmlspecialchars($row['grade']); ?></td>
    <td>
        <a href="result_manage.php?edit=<?php echo htmlspecialchars($row['result_id']); ?>">Edit</a> |
        <a href="result_manage.php?delete=<?php echo htmlspecialchars($row['result_id']); ?>"
           onclick="return confirm('Are you sure?')">Delete</a>
    </td>
</tr>
<?php } ?>

</table>

</body>
</html>