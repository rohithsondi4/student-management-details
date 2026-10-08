<?php
require_once "config/config.php";

if (!isset($_GET['st_id'])) {
    die("Student ID not found.");
}

$st_id = $_GET['st_id'];

$student = mysqli_fetch_assoc(mysqli_query($conn, "
SELECT s.*, c.course_name, c.course_duration, c.course_fees,
       b.batch_name, b.start_date, b.end_date, b.start_time, b.end_time,
       s.email_ID AS email,
       s.Total_payment AS total_payment
FROM student s
INNER JOIN courses c ON s.c_id = c.c_id
INNER JOIN batch b ON s.b_id = b.b_id
WHERE s.st_id = '$st_id'
"));

if (!$student) {
    die("Student not found.");
}

$transactions = mysqli_query($conn, "
    SELECT t.*, pm.payment_mode, pt.payment_term
    FROM student_transaction t
    LEFT JOIN payment_mode pm ON t.pm_id = pm.pm_id
    LEFT JOIN payment_term pt ON t.pt_id = pt.pt_id
    WHERE t.st_id='$st_id'
    ORDER BY t.tr_id
");
$paid = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(paid_amount),0) AS total_paid FROM student_transaction WHERE st_id='$st_id'"));
$due = (float) $student['total_payment'] - (float) $paid['total_paid'];
?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Student Details</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #f3f7ff;
            --surface: #ffffff;
            --surface-soft: #f8fbff;
            --line: #dfeaf7;
            --text: #1f2937;
            --muted: #60708d;
            --primary: #3b82f6;
            --primary-strong: #1d4ed8;
            --success: #10b981;
            --shadow: 0 18px 38px rgba(59, 130, 246, 0.12);
        }
        * { box-sizing: border-box; }
        body {
            margin: 0; font-family: 'Inter', Arial, sans-serif; color: var(--text);
            background: radial-gradient(circle at top left, rgba(59,130,246,0.08), transparent 22%), linear-gradient(180deg, #f4f8ff 0%, #edf4ff 100%);
        }
        .page-shell { max-width: 1220px; margin: 0 auto; padding: 24px 18px 40px; }
        .topbar {
            background: linear-gradient(135deg, #eef6ff 0%, #dbeafe 45%, #ffffff 100%);
            border: 1px solid var(--line); border-radius: 18px; padding: 18px 20px; box-shadow: var(--shadow); margin-bottom: 18px;
        }
        .topbar h1 { margin: 0; color: var(--text); font-size: clamp(1.7rem, 2vw, 2.4rem); }
        .nav { display: flex; flex-wrap: wrap; gap: 10px; margin: 18px 0 24px; padding: 12px 14px; background: rgba(255,255,255,0.9); border: 1px solid var(--line); border-radius: 14px; box-shadow: 0 10px 16px rgba(148,163,184,0.08); }
        .nav a { color: var(--text); text-decoration: none; padding: 10px 14px; border-radius: 10px; font-weight: 700; background: var(--surface-soft); border: 1px solid var(--line); }
        .nav a:hover { background: linear-gradient(135deg, rgba(59,130,246,0.08), rgba(139,92,246,0.08)); color: var(--primary-strong); }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px; }
        .card {
            background: rgba(255,255,255,0.96); border: 1px solid var(--line); border-radius: 20px; box-shadow: var(--shadow); overflow: hidden;
        }
        .card-head { padding: 18px 20px; border-bottom: 1px solid var(--line); background: linear-gradient(90deg, #f7fbff, #eef4ff); }
        .card-head h2 { margin: 0; color: var(--text); font-size: clamp(1.1rem, 2vw, 1.45rem); }
        .card-body { padding: 18px 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px 10px; text-align: left; border-bottom: 1px solid var(--line); }
        th { color: var(--muted); font-size: 0.72rem; letter-spacing: 0.12em; text-transform: uppercase; font-weight: 800; }
        td { color: var(--text); }
        .amount { font-weight: 800; color: var(--primary-strong); }
        .good { color: var(--success); font-weight: 700; }
        .summary-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px; }
        .stat { background: linear-gradient(135deg, #eff6ff, #f8fbff); border: 1px solid var(--line); border-radius: 16px; padding: 18px; }
        .stat-label { margin: 0 0 8px; color: var(--muted); font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.12em; font-weight: 800; }
        .stat-value { margin: 0; font-size: 1.4rem; font-weight: 800; color: var(--text); }
        @media (max-width: 768px) { .page-shell { padding: 18px 12px 30px; } .nav { padding: 10px; } }
    </style>
</head>
<body>
    <div class="page-shell">
        <div class="topbar">
            <h1>Complete Student Details</h1>
        </div>

        <div class="nav">
            <a href="dashboard.php">Dashboard</a>
            <a href="student.php">Students</a>
            <a href="transreport.php">Transactions</a>
        </div>

        <div class="summary-grid" style="margin-bottom:20px;">
            <div class="stat">
                <p class="stat-label">Course Fee</p>
                <p class="stat-value amount">₹<?php echo number_format($student['course_fees'], 2); ?></p>
            </div>
            <div class="stat">
                <p class="stat-label">Total Payment</p>
                <p class="stat-value amount">₹<?php echo number_format($student['total_payment'], 2); ?></p>
            </div>
            <div class="stat">
                <p class="stat-label">Total Paid</p>
                <p class="stat-value good">₹<?php echo number_format($paid['total_paid'], 2); ?></p>
            </div>
            <div class="stat">
                <p class="stat-label">Due</p>
                <p class="stat-value amount">₹<?php echo number_format($due, 2); ?></p>
            </div>
        </div>

        <div class="grid">
            <div class="card">
                <div class="card-head"><h2>Student Information</h2></div>
                <div class="card-body">
                    <table>
                        <tr><th>Student ID</th><td><?php echo $student['st_id']; ?></td></tr>
                        <tr><th>Name</th><td><?php echo htmlspecialchars($student['st_name']); ?></td></tr>
                        <tr><th>Gender</th><td><?php echo $student['gender']; ?></td></tr>
                        <tr><th>Mobile</th><td><?php echo htmlspecialchars($student['mobile_no']); ?></td></tr>
                        <tr><th>Email</th><td><?php echo htmlspecialchars($student['email']); ?></td></tr>
                        <tr><th>Address</th><td><?php echo htmlspecialchars($student['address']); ?></td></tr>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-head"><h2>Course &amp; Batch</h2></div>
                <div class="card-body">
                    <table>
                        <tr><th>Course</th><td><?php echo htmlspecialchars($student['course_name']); ?></td></tr>
                        <tr><th>Duration</th><td><?php echo htmlspecialchars($student['course_duration']); ?></td></tr>
                        <tr><th>Course Fee</th><td class="amount">₹<?php echo number_format($student['course_fees'],2); ?></td></tr>
                        <tr><th>Batch</th><td><?php echo htmlspecialchars($student['batch_name']); ?></td></tr>
                        <tr><th>Batch Dates</th><td><?php echo $student['start_date'] . " to " . $student['end_date']; ?></td></tr>
                        <tr><th>Batch Time</th><td><?php echo $student['start_time'] . " - " . $student['end_time']; ?></td></tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="card" style="margin-top:20px;">
            <div class="card-head"><h2>Payment History</h2></div>
            <div class="card-body" style="padding:0; overflow:auto;">
                <table>
                    <thead>
                        <tr><th>Transaction ID</th><th>Paid Amount</th><th>Payment Mode</th><th>Payment Term</th><th>Date</th></tr>
                    </thead>
                    <tbody>
                        <?php while ($t = mysqli_fetch_assoc($transactions)) { ?>
                        <tr>
                            <td><?php echo $t['tr_id']; ?></td>
                            <td class="amount">₹<?php echo number_format($t['paid_amount'],2); ?></td>
                            <td><?php echo isset($t['payment_mode']) ? htmlspecialchars($t['payment_mode']) : 'N/A'; ?></td>
                            <td><?php echo isset($t['payment_term']) ? htmlspecialchars($t['payment_term']) : 'N/A'; ?></td>
                            <td><?php echo $t['payment_date']; ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>