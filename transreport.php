<?php
require_once "config/config.php";

$error = "";
$success = "";

$paymentModes = mysqli_query($conn, "SELECT * FROM payment_mode ORDER BY payment_mode");
$paymentTerms = mysqli_query($conn, "SELECT * FROM payment_term ORDER BY payment_term");

if (isset($_POST['save'])) {
    $st_id = $_POST['st_id'];
    $pm_id = (int) $_POST['pm_id'];
    $pt_id = (int) $_POST['pt_id'];
    $paid_amount = (float) $_POST['paid_amount'];
    $payment_date = $_POST['payment_date'];

    $student = mysqli_fetch_assoc(
        mysqli_query($conn, "SELECT Total_payment AS total_payment FROM student WHERE st_id='$st_id'")
    );

    $paid = mysqli_fetch_assoc(
        mysqli_query($conn, "SELECT COALESCE(SUM(paid_amount),0) AS paid FROM student_transaction WHERE st_id='$st_id'")
    );

    $due = (float) $student['total_payment'] - (float) $paid['paid'];

    if ($paid_amount > 0 && $paid_amount <= $due) {
        $new_balance = $due - $paid_amount;
        $sql = "INSERT INTO student_transaction
                (st_id, pm_id, pt_id, paid_amount, balance_amount, payment_date)
                VALUES
                ('$st_id', '$pm_id', '$pt_id', '$paid_amount', '$new_balance', '$payment_date')";

        if (mysqli_query($conn, $sql)) {
            $success = "Payment saved successfully.";
        } else {
            $error = "Error: " . mysqli_error($conn);
        }
    } else {
        $error = "Payment must be greater than 0 and not greater than current due amount.";
    }
}

if (isset($_POST['update'])) {
    $tr_id = $_POST['tr_id'];
    $st_id = $_POST['st_id'];
    $pm_id = (int) $_POST['pm_id'];
    $pt_id = (int) $_POST['pt_id'];
    $paid_amount = (float) $_POST['paid_amount'];
    $payment_date = $_POST['payment_date'];

    $student = mysqli_fetch_assoc(
        mysqli_query($conn, "SELECT Total_payment AS total_payment FROM student WHERE st_id='$st_id'")
    );

    $others = mysqli_fetch_assoc(
        mysqli_query($conn, "SELECT COALESCE(SUM(paid_amount),0) AS paid FROM student_transaction WHERE st_id='$st_id' AND tr_id<>'$tr_id'")
    );

    $due = (float) $student['total_payment'] - (float) $others['paid'];

    if ($paid_amount > 0 && $paid_amount <= $due) {
        $new_balance = $due - $paid_amount;
        $sql = "UPDATE student_transaction
                SET pm_id='$pm_id',
                    pt_id='$pt_id',
                    paid_amount='$paid_amount',
                    balance_amount='$new_balance',
                    payment_date='$payment_date'
                WHERE tr_id='$tr_id'";

        if (mysqli_query($conn, $sql)) {
            $success = "Payment updated successfully.";
        } else {
            $error = "Error: " . mysqli_error($conn);
        }
    } else {
        $error = "Payment amount is greater than the available due amount.";
    }
}

if (isset($_GET['delete'])) {
    $tr_id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM student_transaction WHERE tr_id='$tr_id'");
    $success = "Payment deleted successfully.";
}

$edit = null;
if (isset($_GET['edit'])) {
    $edit = mysqli_fetch_assoc(
        mysqli_query($conn, "SELECT * FROM student_transaction WHERE tr_id='" . $_GET['edit'] . "'")
    );
}

$students = mysqli_query($conn, "
    SELECT st_id, st_name, Total_payment AS total_payment
    FROM student
    ORDER BY st_name
");

$transactions = mysqli_query($conn, "
    SELECT t.*, s.st_name, c.course_name, s.Total_payment AS total_payment,
           pm.payment_mode, pt.payment_term,
           (COALESCE((SELECT SUM(x.paid_amount) FROM student_transaction x WHERE x.st_id=t.st_id),0)) AS total_paid
    FROM student_transaction t
    INNER JOIN student s ON t.st_id = s.st_id
    INNER JOIN courses c ON s.c_id = c.c_id
    LEFT JOIN payment_mode pm ON t.pm_id = pm.pm_id
    LEFT JOIN payment_term pt ON t.pt_id = pt.pt_id
    ORDER BY t.tr_id ASC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Transactions</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-1: #f4f8ff;
            --bg-2: #edf3ff;
            --panel: #ffffff;
            --panel-strong: #f8fbff;
            --line: #dfeaf7;
            --text: #1f2937;
            --muted: #60708d;
            --green: #10b981;
            --cyan: #08b2e3;
            --purple: #8b5cf6;
            --amber: #f59e0b;
            --rose: #f87171;
            --primary: #3b82f6;
            --primary-strong: #1d4ed8;
            --shadow: 0 18px 38px rgba(59, 130, 246, 0.12);
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: 'Inter', Arial, sans-serif;
            color: var(--text);
            background:
                radial-gradient(circle at top left, rgba(59,130,246,0.08), transparent 22%),
                radial-gradient(circle at top right, rgba(139,92,246,0.08), transparent 26%),
                linear-gradient(135deg, var(--bg-1) 0%, var(--bg-2) 100%);
        }
        .page-shell { max-width: 1280px; margin: 0 auto; padding: 24px 18px 40px; }
        .topbar {
            background: linear-gradient(135deg, #eef6ff 0%, #dbeafe 45%, #ffffff 100%);
            border: 1px solid var(--line);
            border-radius: 18px;
            padding: 20px 22px;
            box-shadow: var(--shadow);
            animation: fadeUp 0.6s ease both;
        }
        .topbar h1 { margin: 0; color: var(--text); font-size: clamp(1.6rem, 2vw, 2.3rem); }
        .nav { display: flex; flex-wrap: wrap; gap: 10px; margin: 18px 0 24px; padding: 12px 14px; background: rgba(255,255,255,0.9); border: 1px solid var(--line); border-radius: 14px; box-shadow: 0 10px 16px rgba(148,163,184,0.08); }
        .nav a { color: var(--text); text-decoration: none; padding: 10px 14px; border-radius: 10px; font-weight: 700; background: var(--panel-strong); border: 1px solid var(--line); }
        .nav a:hover { background: linear-gradient(135deg, rgba(59,130,246,0.08), rgba(139,92,246,0.08)); color: var(--primary-strong); box-shadow: 0 8px 16px rgba(59,130,246,0.08); }
        .panel {
            background: rgba(255,255,255,0.96);
            border: 1px solid var(--line);
            border-radius: 20px;
            box-shadow: var(--shadow);
            overflow: hidden;
            margin-bottom: 26px;
            animation: fadeUp 0.75s ease 0.15s both;
        }
        .panel-head { padding: 18px 20px; border-bottom: 1px solid var(--line); background: linear-gradient(90deg, #f7fbff, #eef4ff); }
        .panel-head h2 { margin: 0; color: var(--text); font-size: clamp(1.2rem, 2vw, 1.6rem); }
        .panel-body { padding: 20px; }
        .notice { margin: 0 0 20px; padding: 12px 14px; border-radius: 12px; font-weight: 700; }
        .notice.error { background: rgba(248,113,113,0.09); color: #b91c1c; border: 1px solid rgba(248,113,113,0.18); }
        .notice.success { background: rgba(16,185,129,0.09); color: #047857; border: 1px solid rgba(16,185,129,0.18); }
        form { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 18px; }
        .field { display: flex; flex-direction: column; gap: 8px; }
        .field.full { grid-column: 1 / -1; }
        label { font-size: 0.8rem; letter-spacing: 0.08em; color: var(--muted); text-transform: uppercase; font-weight: 800; }
        input, select, button { border-radius: 12px; border: 1px solid var(--line); background: #ffffff; color: var(--text); padding: 12px 14px; font: inherit; outline: none; }
        input:focus, select:focus { border-color: rgba(59,130,246,0.75); box-shadow: 0 0 0 3px rgba(59,130,246,0.12); }
        .btn { min-width: 150px; border: none; cursor: pointer; font-weight: 800; transition: transform 0.2s ease; }
        .btn-primary { background: linear-gradient(135deg, var(--cyan), var(--primary-strong)); color: #fff; box-shadow: 0 12px 18px rgba(8,178,227,0.2); }
        .btn-secondary { background: #f5f8ff; color: var(--text); border: 1px solid var(--line); text-decoration: none; display: inline-flex; align-items: center; justify-content: center; }
        .btn:hover { transform: translateY(-1px); }
        .actions { display: flex; gap: 12px; align-items: center; margin-top: 8px; }
        table { width: 100%; border-collapse: collapse; background: transparent; }
        th, td { padding: 14px 12px; text-align: left; border-bottom: 1px solid var(--line); color: var(--text); }
        th { background: #f8fbff; color: var(--muted); font-size: 0.72rem; letter-spacing: 0.12em; text-transform: uppercase; font-weight: 800; }
        tbody tr:hover { background: #f9fbff; }
        .action-links a { color: var(--text); text-decoration: none; margin-right: 8px; padding: 6px 10px; border-radius: 8px; }
        .action-links a:nth-child(1) { background: rgba(59,130,246,0.12); }
        .action-links a:nth-child(2) { background: rgba(248,113,113,0.08); }
        .money { font-weight: 800; color: var(--primary-strong); }
        @keyframes fadeUp { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: translateY(0); } }
        @media (max-width:768px){ .page-shell{padding:18px 12px 30px;} .nav{padding:10px;} form{grid-template-columns:1fr;} }
    </style>
</head>
<body>
    <div class="page-shell">
        <div class="topbar"><h1>Transaction Management</h1></div>
        <div class="nav">
            <a href="dashboard.php">Dashboard</a>
            <a href="student.php">Students</a>
            <a href="courses.php">Courses</a>
            <a href="paymentmode.php">Payment Modes</a>
            <a href="paymentterm.php">Payment Terms</a>
        </div>

        <div class="panel">
            <div class="panel-head"><h2>Payment Entry</h2></div>
            <div class="panel-body">
                <?php if (!empty($error)) { ?><p class="notice error"><?php echo $error; ?></p><?php } ?>
                <?php if (!empty($success)) { ?><p class="notice success"><?php echo $success; ?></p><?php } ?>

                <form method="POST">
                    <?php if ($edit) { ?><input type="hidden" name="tr_id" value="<?php echo $edit['tr_id']; ?>"><?php } ?>

                    <div class="field">
                        <label>Student</label>
                        <select name="st_id" required>
                            <option value="">Select Student</option>
                            <?php while ($row = mysqli_fetch_assoc($students)) { ?>
                                <option value="<?php echo $row['st_id']; ?>" <?php if ($edit && $edit['st_id'] == $row['st_id']) echo 'selected'; ?>>
                                    <?php echo $row['st_name']; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="field">
                        <label>Paid Amount</label>
                        <input type="number" step="0.01" name="paid_amount" required value="<?php echo ($edit ? $edit['paid_amount'] : ''); ?>">
                    </div>

                    <div class="field">
                        <label>Payment Mode</label>
                        <select name="pm_id" required>
                            <option value="">Select Payment Mode</option>
                            <?php while ($mode = mysqli_fetch_assoc($paymentModes)) { ?>
                                <option value="<?php echo $mode['pm_id']; ?>" <?php if ($edit && $edit['pm_id'] == $mode['pm_id']) echo 'selected'; ?>>
                                    <?php echo htmlspecialchars($mode['payment_mode']); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="field">
                        <label>Payment Term</label>
                        <select name="pt_id" required>
                            <option value="">Select Payment Term</option>
                            <?php while ($term = mysqli_fetch_assoc($paymentTerms)) { ?>
                                <option value="<?php echo $term['pt_id']; ?>" <?php if ($edit && $edit['pt_id'] == $term['pt_id']) echo 'selected'; ?>>
                                    <?php echo htmlspecialchars($term['payment_term']); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="field">
                        <label>Payment Date</label>
                        <input type="date" name="payment_date" required value="<?php echo ($edit ? $edit['payment_date'] : date('Y-m-d')); ?>">
                    </div>

                    <div class="actions" style="grid-column: 1 / -1;">
                        <?php if ($edit) { ?>
                            <button type="submit" name="update" class="btn btn-primary">Update Payment</button>
                            <a href="transreport.php" class="btn btn-secondary">Cancel</a>
                        <?php } else { ?>
                            <button type="submit" name="save" class="btn btn-primary">Save Payment</button>
                        <?php } ?>
                    </div>
                </form>
            </div>
        </div>

        <div class="panel">
            <div class="panel-head"><h2>Transactions</h2></div>
            <div class="panel-body" style="padding:0; overflow:auto;">
                <table>
                    <thead>
                        <tr>
                            <th>TR ID</th>
                            <th>Student</th>
                            <th>Course</th>
                            <th>Total</th>
                            <th>Paid This Time</th>
                            <th>Total Paid</th>
                            <th>Due</th>
                            <th>Mode</th>
                            <th>Term</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($t = mysqli_fetch_assoc($transactions)) {
                            $due = (float) $t['total_payment'] - (float) $t['total_paid'];
                        ?>
                            <tr>
                                <td><?php echo $t['tr_id']; ?></td>
                                <td><?php echo htmlspecialchars($t['st_name']); ?></td>
                                <td><?php echo htmlspecialchars($t['course_name']); ?></td>
                                <td class="money">₹<?php echo number_format($t['total_payment'],2); ?></td>
                                <td class="money">₹<?php echo number_format($t['paid_amount'],2); ?></td>
                                <td class="money">₹<?php echo number_format($t['total_paid'],2); ?></td>
                                <td class="money">₹<?php echo number_format($due,2); ?></td>
                                <td><?php echo htmlspecialchars($t['payment_mode']); ?></td>
                                <td><?php echo htmlspecialchars($t['payment_term']); ?></td>
                                <td><?php echo $t['payment_date']; ?></td>
                                <td class="action-links">
                                    <a href="?edit=<?php echo $t['tr_id']; ?>">Edit</a>
                                    <a href="?delete=<?php echo $t['tr_id']; ?>" onclick="return confirm('Delete this payment?')">Delete</a>
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