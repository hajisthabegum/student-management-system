<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

require 'connection.php';

// Handle form submission for Add or Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $course = $_POST['course'];

    if (isset($_POST['update_id'])) {
        // Update existing student
        $id = $_POST['update_id'];
        $stmt = $conn->prepare("UPDATE students SET name=?, email=?, phone=?, course=? WHERE id=?");
        $stmt->execute([$name, $email, $phone, $course, $id]);
    } else {
        // Add new student
        $stmt = $conn->prepare("INSERT INTO students (name, email, phone, course) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $email, $phone, $course]);
    }

    header("Location: admin_panel.php");
    exit();
}

// Handle deletion
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM students WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: admin_panel.php");
    exit();
}

// Handle edit request
$editing = false;
if (isset($_GET['edit'])) {
    $editing = true;
    $id = $_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
    $stmt->execute([$id]);
    $editStudent = $stmt->fetch();
}

// Fetch all students
$stmt = $conn->prepare("SELECT * FROM students");
$stmt->execute();
$students = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h1 class="mb-4">Student Management</h1>

    <!-- Add or Edit Student Form -->
    <div class="card mb-4">
        <div class="card-header">
            <?= $editing ? "Edit Student" : "Add New Student" ?>
        </div>
        <div class="card-body">
            <form method="POST">
                <?php if ($editing): ?>
                    <input type="hidden" name="update_id" value="<?= $editStudent['id'] ?>">
                <?php endif; ?>
                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" class="form-control" required
                           value="<?= $editing ? $editStudent['name'] : '' ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required
                           value="<?= $editing ? $editStudent['email'] : '' ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" class="form-control" required
                           value="<?= $editing ? $editStudent['phone'] : '' ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Course</label>
                    <input type="text" name="course" class="form-control" required
                           value="<?= $editing ? $editStudent['course'] : '' ?>">
                </div>
                <button type="submit" class="btn btn-primary"><?= $editing ? "Update Student" : "Add Student" ?></button>
                <?php if ($editing): ?>
                    <a href="admin_panel.php" class="btn btn-secondary">Cancel</a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <!-- Student List -->
    <div class="card">
        <div class="card-header">Student List</div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Course</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($students as $student): ?>
                    <tr>
                        <td><?= $student['id'] ?></td>
                        <td><?= $student['name'] ?></td>
                        <td><?= $student['email'] ?></td>
                        <td><?= $student['phone'] ?></td>
                        <td><?= $student['course'] ?></td>
                        <td>
                            <a href="?edit=<?= $student['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                            <a href="?delete=<?= $student['id'] ?>" class="btn btn-danger btn-sm"
                               onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
