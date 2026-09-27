<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}
$role = $_SESSION['role'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>SIS Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<h1>Student Information System</h1>
<p>Logged in as: <?php echo htmlspecialchars($_SESSION['username']); ?> (<?php echo htmlspecialchars($role); ?>)</p>
<a href="../logout.php">Logout</a>

<div class="menu">
<?php if ($role === 'admin') { ?>
    <a href="../student/manage_student.php">Manage Students</a>
    <a href="../faculty/manage_faculty.php">Manage Faculty</a>
    <a href="../course/course_manage.php">Manage Courses</a>
    <a href="../enrollment/manage_enrollment.php">Manage Enrollment</a>
<?php } ?>
    <a href="../exam/exam_manage.php">Manage Exams</a>
    <a href="../result/result_manage.php">Manage Results</a>
</div>

</body>
</html>