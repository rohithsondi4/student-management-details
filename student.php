<?php
require_once "config/config.php";

if (isset($_POST['save'])) {
    $st_name = mysqli_real_escape_string($conn,$_POST['st_name']);
    $gender = $_POST['gender'];
    $mobile_no = $_POST['mobile_no'];
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $c_id = $_POST['c_id'];
    $b_id = $_POST['b_id'];
    $total_payment=(float)$_POST['total_payment'];
    if ($total_payment < 0) $total_payment = 0;
    mysqli_query($conn, "INSERT INTO student (st_name, gender, mobile_no, email_ID, address, c_id, b_id, Total_payment, status)
    VALUES ('$st_name', '$gender', '$mobile_no', '$email', '$address', '$c_id', '$b_id', '$total_payment', '1')") or die(mysqli_error($conn));
    header("Location: student.php");
    exit;
}

if (isset($_POST['update'])) {
    $st_id = $_POST['st_id'];
    $st_name = mysqli_real_escape_string($conn, $_POST['st_name']);
    $gender = $_POST['gender'];
    $mobile_no = $_POST['mobile_no'];
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $c_id = $_POST['c_id'];
    $b_id = $_POST['b_id'];
    $total_payment = max(0, (float) $_POST['total_payment']);
    mysqli_query($conn, "UPDATE student SET st_name='$st_name', gender='$gender', mobile_no='$mobile_no', email_ID='$email', address='$address', c_id='$c_id', b_id='$b_id', Total_payment='$total_payment', status='1' WHERE st_id='$st_id'") or die(mysqli_error($conn));
    header("Location: student.php");
    exit;
}
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM student_transaction WHERE st_id='$id'") or die(mysqli_error($conn));
    mysqli_query($conn, "DELETE FROM student WHERE st_id='$id'") or die(mysqli_error($conn));
    header("Location: student.php");
    exit;
}
$edit = null;
if (isset($_GET['edit'])) {
    $edit = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM student WHERE st_id='" . $_GET['edit'] . "'"));
}
$coursesResult = mysqli_query($conn, "SELECT * FROM courses ORDER BY course_name");
$courses = [];
while ($c = mysqli_fetch_assoc($coursesResult)) {
    $courses[] = $c;
}
$batchesResult = mysqli_query($conn, "SELECT * FROM batch ORDER BY batch_name");
$batches = [];
while ($b = mysqli_fetch_assoc($batchesResult)) {
    $batches[] = $b;
}

$students = mysqli_query($conn, "SELECT s.*, c.course_name, c.course_fees, b.batch_name, s.Total_payment AS total_payment
FROM student s
INNER JOIN courses c ON s.c_id = c.c_id
INNER JOIN batch b ON s.b_id = b.b_id
ORDER BY s.st_id ASC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Students</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #f3f7ff;
            --bg-2: #eaf2ff;
            --surface: #ffffff;
            --surface-soft: #f9fbff;
            --border: #dfeaf7;
            --text: #1f2937;
            --muted: #5f6f87;
            --primary: #3b82f6;
            --primary-strong: #1d4ed8;
            --primary-soft: #e0edff;
            --purple: #8b5cf6;
            --green: #10b981;
            --red: #ef4444;
            --shadow: 0 18px 38px rgba(59, 130, 246, 0.12);
        }
        * { box-sizing: border-box; }
        body {
            margin: 0; font-family: 'Inter', Arial, sans-serif; color: var(--text);
            background: radial-gradient(circle at top left, rgba(59,130,246,0.08), transparent 22%), linear-gradient(180deg, #f4f8ff 0%, #edf4ff 100%);
        }
        .page-shell { max-width: 1320px; margin: 0 auto; padding: 24px 18px 40px; }
        .topbar { background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 45%, #ffffff 100%); border: 1px solid var(--border); border-radius: 18px; padding: 18px 20px; box-shadow: var(--shadow); margin-bottom: 18px; animation: fadeUp 0.6s ease both; }
        .topbar h1 { margin: 0; color: var(--text); font-size: clamp(1.6rem, 2vw, 2.3rem); }
        .nav { display: flex; flex-wrap: wrap; gap: 10px; margin: 18px 0 24px; padding: 12px 14px; background: rgba(255,255,255,0.88); border: 1px solid var(--border); border-radius: 14px; box-shadow: 0 10px 16px rgba(148,163,184,0.08); }
        .nav a { color: var(--text); text-decoration: none; padding: 10px 14px; border-radius: 10px; font-weight: 700; background: var(--surface-soft); border: 1px solid var(--border); }
        .nav a:hover { background: linear-gradient(135deg, var(--primary-soft), #eef4ff); color: var(--primary-strong); box-shadow: 0 8px 16px rgba(59,130,246,0.08); }
        .panel { background: rgba(255,255,255,0.96); border: 1px solid var(--border); border-radius: 20px; box-shadow: var(--shadow); overflow: hidden; margin-bottom: 26px; animation: fadeUp 0.75s ease 0.15s both; }
        .panel-head { padding: 18px 20px; border-bottom: 1px solid var(--border); background: linear-gradient(90deg, #f7fbff, #eef4ff); }
        .panel-head h2 { margin: 0; color: var(--text); font-size: clamp(1.2rem, 2vw, 1.6rem); }
        .panel-body { padding: 20px; }
        form { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 18px; }
        .field { display: flex; flex-direction: column; gap: 8px; }
        .field.full { grid-column: 1 / -1; }
        label { font-size: 0.8rem; letter-spacing: 0.08em; color: var(--muted); text-transform: uppercase; font-weight: 800; }
        input, select, textarea, button { border-radius: 12px; border: 1px solid var(--border); background: #ffffff; color: var(--text); padding: 12px 14px; font: inherit; outline: none; }
        textarea { min-height: 100px; resize: vertical; }
        input:focus, select:focus, textarea:focus { border-color: rgba(59,130,246,0.75); box-shadow: 0 0 0 3px rgba(59,130,246,0.12); }
        .btn { min-width: 140px; border: none; cursor: pointer; font-weight: 800; transition: transform 0.2s ease; }
        .btn-primary { background: linear-gradient(135deg, var(--primary), var(--primary-strong)); color: #fff; box-shadow: 0 12px 18px rgba(59,130,246,0.2); }
        .btn-secondary { background: #f5f8ff; color: var(--text); border: 1px solid var(--border); text-decoration: none; }
        .btn:hover { transform: translateY(-1px); }
        .actions { display: flex; gap: 12px; align-items: center; margin-top: 8px; }
        table { width: 100%; border-collapse: collapse; background: transparent; }
        th, td { padding: 14px 12px; text-align: left; border-bottom: 1px solid var(--border); color: var(--text); }
        th { background: #f8fbff; color: var(--muted); font-size: 0.72rem; letter-spacing: 0.12em; text-transform: uppercase; font-weight: 800; }
        tbody tr:hover { background: #f9fbff; }
        .action-links a { text-decoration: none; color: var(--text); padding: 6px 10px; border-radius: 8px; margin-right: 8px; }
        .action-links a:nth-child(1) { background: rgba(59,130,246,0.12); }
        .action-links a:nth-child(2) { background: rgba(239,68,68,0.09); }
        .action-links a:nth-child(3) { background: rgba(16,185,129,0.12); }
        @keyframes fadeUp { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: translateY(0); } }
        @media (max-width:768px){ .page-shell{padding:18px 12px 30px;} .nav{padding:10px;} form{grid-template-columns:1fr;} }
    </style>
</head>
<body>
    <div class="page-shell">
        <div class="topbar"><h1>Student Management</h1></div>
        <div class="nav">
            <a href="dashboard.php">Dashboard</a>
            <a href="courses.php">Courses</a>
            <a href="batch.php">Batches</a>
            <a href="transreport.php">Transactions</a>
        </div>

        <div class="panel">
            <div class="panel-head"><h2><?php echo $edit ? 'Edit Student' : 'Add New Student'; ?></h2></div>
            <div class="panel-body">
                <form method="POST">
                    <?php if ($edit) { ?><input type="hidden" name="st_id" value="<?php echo $edit['st_id']; ?>"><?php } ?>
                    <div class="field">
                        <label>Name</label>
                        <input type="text" name="st_name" required value="<?php echo isset($edit['st_name']) ? $edit['st_name'] : ''; ?>">
                    </div>
                    <div class="field">
                        <label>Gender</label>
                        <select name="gender" required>
                            <option value="">Select Gender</option>
                            <option value="Male" <?php if ((isset($edit['gender']) ? $edit['gender'] : '') == 'Male') echo 'selected'; ?>>Male</option>
                            <option value="Female" <?php if ((isset($edit['gender']) ? $edit['gender'] : '') == 'Female') echo 'selected'; ?>>Female</option>
                            <option value="Other" <?php if ((isset($edit['gender']) ? $edit['gender'] : '') == 'Other') echo 'selected'; ?>>Other</option>
                        </select>
                    </div>
                    <div class="field">
                        <label>Mobile</label>
                        <input type="text" name="mobile_no" value="<?php echo isset($edit['mobile_no']) ? $edit['mobile_no'] : ''; ?>">
                    </div>
                    <div class="field">
                        <label>Email</label>
                        <input type="email" name="email" value="<?php echo isset($edit['email_ID']) ? $edit['email_ID'] : ''; ?>">
                    </div>
                    <div class="field full">
                        <label>Address</label>
                        <textarea name="address"><?php echo isset($edit['address']) ? $edit['address'] : ''; ?></textarea>
                    </div>
                    <div class="field">
                        <label>Course</label>
                        <select name="c_id" id="c_id" onchange="setFee()" required>
                            <option value="">Select Course</option>
                            <?php foreach ($courses as $c) { ?>
                                <option value="<?php echo $c['c_id']; ?>" data-fee="<?php echo $c['course_fees']; ?>" <?php if ((isset($edit['c_id']) ? $edit['c_id'] : '') == $c['c_id']) echo 'selected'; ?>>
                                    <?php echo htmlspecialchars($c['course_name']); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="field">
                        <label>Batch</label>
                        <select name="b_id" required>
                            <option value="">Select Batch</option>
                            <?php foreach ($batches as $b) { ?>
                                <option value="<?php echo $b['b_id']; ?>" <?php if ((isset($edit['b_id']) ? $edit['b_id'] : '') == $b['b_id']) echo 'selected'; ?>>
                                    <?php echo htmlspecialchars($b['batch_name']); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="field">
                        <label>Course Fee</label>
                        <input type="number" id="course_fees" readonly value="<?php echo isset($edit['c_id']) ? mysqli_fetch_assoc(mysqli_query($conn, "SELECT course_fees FROM courses WHERE c_id='" . $edit['c_id'] . "'"))['course_fees'] : ''; ?>">
                    </div>
                    <div class="field">
                        <label>Total Payment</label>
                        <input type="number" id="total_payment" name="total_payment" required value="<?php echo isset($edit['Total_payment']) ? $edit['Total_payment'] : ''; ?>">
                    </div>
                    <div class="actions" style="grid-column: 1 / -1;">
                        <?php if ($edit) { ?><button type="submit" name="update" value="Update" class="btn btn-primary">Update</button><?php } else { ?><button type="submit" name="save" value="Register" class="btn btn-primary">Register</button><?php } ?>
                        <?php if ($edit) { ?><a href="student.php" class="btn btn-secondary">Cancel</a><?php } ?>
                    </div>
                    <script>
                        function setFee() {
                            var course = document.getElementById("c_id");
                            var selected = course.options[course.selectedIndex];
                            var fee = selected.getAttribute("data-fee");
                            document.getElementById("course_fees").value = fee || "";
                        }
                    </script>
                </form>
            </div>
        </div>

        <div class="panel">
            <div class="panel-head"><h2>Students</h2></div>
            <div class="panel-body" style="padding:0; overflow:auto;">
                <table>
                    <thead>
                        <tr><th>ID</th><th>Name</th><th>Gender</th><th>Mobile</th><th>Email</th><th>Address</th><th>Course</th><th>Batch</th><th>Fee</th><th>Total Payment</th><th>Action</th></tr>
                    </thead>
                    <tbody>
                        <?php while ($s = mysqli_fetch_assoc($students)) { ?>
                            <tr>
                                <td><?php echo $s['st_id']; ?></td>
                                <td><?php echo htmlspecialchars($s['st_name']); ?></td>
                                <td><?php echo htmlspecialchars($s['gender']); ?></td>
                                <td><?php echo htmlspecialchars($s['mobile_no']); ?></td>
                                <td><?php echo htmlspecialchars($s['email_ID']); ?></td>
                                <td><?php echo htmlspecialchars($s['address']); ?></td>
                                <td><?php echo htmlspecialchars($s['course_name']); ?></td>
                                <td><?php echo htmlspecialchars($s['batch_name']); ?></td>
                                <td>₹<?php echo number_format($s['course_fees'],2); ?></td>
                                <td>₹<?php echo number_format($s['total_payment'],2); ?></td>
                                <td class="action-links">
                                    <a href="?edit=<?php echo $s['st_id']; ?>">Edit</a>
                                    <a href="?delete=<?php echo $s['st_id']; ?>" onclick="return confirm('Delete this student and payments?')">Delete</a>
                                    <a href="student_view.php?st_id=<?php echo $s['st_id']; ?>">View</a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
