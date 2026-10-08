<?php
require_once "config/config.php";
$student_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM student"))['total'];
$course_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM courses"))['total'];
$batch_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM batch"))['total'];
$transaction_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM student_transaction"))['total'];
$total_collected = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(paid_amount),0) AS total FROM student_transaction"))['total'];
$paymentmode_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM payment_mode"))['total'];
$paymentterm_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM payment_term"))['total'];
$pending_students = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COUNT(*) AS total
    FROM (
        SELECT s.st_id, s.Total_payment - COALESCE(SUM(t.paid_amount),0) AS due_amount
        FROM student s
        LEFT JOIN student_transaction t ON s.st_id = t.st_id
        GROUP BY s.st_id, s.Total_payment
        HAVING s.Total_payment - COALESCE(SUM(t.paid_amount),0) > 0
    ) x
"))['total'];
$details = mysqli_query($conn,"
    SELECT
        s.st_id,
        s.st_name,
        c.course_name,
        b.batch_name,
        s.Total_payment AS total_payment,
        COALESCE(SUM(t.paid_amount),0) AS paid_amount,
        s.Total_payment - COALESCE(SUM(t.paid_amount),0) AS due_amount
    FROM student s
    INNER JOIN courses c ON s.c_id = c.c_id
    INNER JOIN batch b ON s.b_id = b.b_id
    LEFT JOIN student_transaction t ON s.st_id = t.st_id
    GROUP BY s.st_id, s.st_name, c.course_name, b.batch_name, s.Total_payment
    ORDER BY s.st_id ASC
");
$recent_students = mysqli_query($conn,"
    SELECT
        s.st_id,
        s.st_name,
        c.course_name,
        COALESCE(SUM(t.paid_amount),0) AS paid_amount,
        s.Total_payment - COALESCE(SUM(t.paid_amount),0) AS due_amount
    FROM student s
    INNER JOIN courses c ON s.c_id = c.c_id
    LEFT JOIN student_transaction t ON s.st_id = t.st_id
    GROUP BY s.st_id, s.st_name, c.course_name, s.Total_payment
    ORDER BY s.st_id DESC
    LIMIT 5
");
?>
<!DOCTYPE html>
<html>
<head>
<title>Coaching Institute Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    :root {
        --bg: #f4f7fb;
        --panel: rgba(255,255,255,0.92);
        --text: #1d2433;
        --muted: #64748b;
        --primary: #4f46e5;
        --primary-dark: #312e81;
        --secondary: #0ea5e9;
        --success: #10b981;
        --warning: #f59e0b;
        --danger: #ef4444;
        --shadow: 0 18px 45px rgba(15, 23, 42, 0.12);
        --radius: 22px;
    }

    * { box-sizing: border-box; }

    body {
        margin: 0;
        font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        background: linear-gradient(135deg, #eef4ff 0%, #f8fafc 35%, #edf6ff 100%);
        color: var(--text);
    }

    a { text-decoration: none; }

    .topbar {
        background: linear-gradient(135deg, #0f172a 0%, #1f2937 52%, #111827 100%);
        padding: 22px 40px 18px;
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.18);
        position: sticky;
        top: 0;
        z-index: 20;
    }

    .topbar-inner {
        max-width: 1400px;
        margin: 0 auto;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        flex-wrap: wrap;
    }

    .brand {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .brand-badge {
        width: 52px;
        height: 52px;
        border-radius: 16px;
        background: linear-gradient(135deg, #60a5fa, #8b5cf6);
        display: grid;
        place-items: center;
        font-weight: 700;
        color: white;
        box-shadow: 0 12px 24px rgba(99, 102, 241, 0.3);
    }

    .brand h1 {
        margin: 0;
        font-size: clamp(1.5rem, 2vw, 2.2rem);
        letter-spacing: -0.04em;
        color: #f8fbff;
        font-weight: 800;
    }

    .topbar-note {
        color: rgba(255,255,255,0.72);
        font-size: 0.76rem;
        letter-spacing: 0.12em;
        text-transform: uppercase;
    }

    .main-nav {
        background: rgba(255,255,255,0.08);
        border: 1px solid rgba(255,255,255,0.15);
        border-radius: 999px;
        padding: 10px 16px;
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
        justify-content: center;
    }

    .main-nav a {
        color: rgba(255,255,255,0.9);
        padding: 9px 14px;
        border-radius: 999px;
        font-size: 0.92rem;
        font-weight: 700;
        transition: 0.2s ease;
    }

    .main-nav a:hover,
    .main-nav a:focus {
        background: rgba(255,255,255,0.11);
        color: white;
    }

    .container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 30px 20px 40px;
    }

    .hero-row {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 22px;
        margin-bottom: 28px;
    }

    .hero-panel,
    .mini-panel,
    .panel,
    .stat-card {
        background: rgba(255,255,255,0.9);
        border: 1px solid rgba(148,163,184,0.18);
        box-shadow: var(--shadow);
        border-radius: var(--radius);
    }

    .hero-panel {
        padding: 28px 28px 22px;
        background: linear-gradient(135deg, rgba(79,70,229,0.97), rgba(14,165,233,0.9));
        color: white;
        position: relative;
        overflow: hidden;
    }

    .hero-panel::after {
        content: "";
        position: absolute;
        width: 220px;
        height: 220px;
        border-radius: 50%;
        right: -30px;
        top: -40px;
        background: rgba(255,255,255,0.08);
    }

    .eyebrow {
        display: inline-block;
        font-size: 0.74rem;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        opacity: 0.8;
        margin-bottom: 12px;
        position: relative;
        z-index: 1;
    }

    .hero-panel h2 {
        margin: 0;
        font-size: clamp(2rem, 3vw, 3rem);
        letter-spacing: -0.05em;
        line-height: 1.05;
        position: relative;
        z-index: 1;
    }

    .hero-panel p {
        margin: 14px 0 0;
        max-width: 620px;
        color: rgba(255,255,255,0.82);
        position: relative;
        z-index: 1;
    }

    .hero-actions {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        margin-top: 22px;
        position: relative;
        z-index: 1;
    }

    .primary-btn,
    .secondary-btn,
    .view-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 12px 18px;
        border-radius: 12px;
        font-weight: 700;
        transition: transform 0.2s ease;
    }

    .primary-btn {
        background: white;
        color: var(--primary-dark);
        box-shadow: 0 12px 25px rgba(15, 23, 42, 0.18);
    }

    .secondary-btn {
        background: rgba(255,255,255,0.12);
        border: 1px solid rgba(255,255,255,0.2);
        color: white;
    }

    .primary-btn:hover,
    .secondary-btn:hover,
    .view-btn:hover {
        transform: translateY(-1px);
    }

    .mini-panel {
        padding: 22px 20px;
        background: linear-gradient(180deg, #ffffff, #f8fbff);
    }

    .mini-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 18px;
        color: var(--muted);
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        font-size: 0.76rem;
    }

    .mini-list {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .mini-list li {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        padding: 14px 14px;
        border-radius: 14px;
        background: #f8fafc;
        border: 1px solid #edf2f7;
    }

    .mini-label {
        color: var(--muted);
        font-size: 0.84rem;
        font-weight: 700;
    }

    .mini-value {
        font-weight: 800;
        font-size: 1.1rem;
        color: var(--text);
    }

    .cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 22px;
        margin-bottom: 28px;
    }

    .stat-card {
        position: relative;
        overflow: hidden;
        padding: 22px 22px 18px;
        transition: transform 0.22s ease, box-shadow 0.22s ease;
    }

    .stat-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 22px 45px rgba(15,23,42,0.16);
    }

    .stat-card::before {
        content: "";
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        height: 5px;
        background: linear-gradient(90deg, var(--card-accent), rgba(255,255,255,0.4));
    }

    .stat-card.blue { --card-accent: #4f46e5; }
    .stat-card.green { --card-accent: #10b981; }
    .stat-card.orange { --card-accent: #f59e0b; }
    .stat-card.cyan { --card-accent: #06b6d4; }
    .stat-card.purple { --card-accent: #8b5cf6; }
    .stat-card.pink { --card-accent: #ec4899; }
    .stat-card.gold { --card-accent: #f97316; }

    .stat-inner {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        margin-bottom: 12px;
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: grid;
        place-items: center;
        font-size: 1.4rem;
        background: rgba(79,70,229,0.12);
    }

    .stat-card.green .stat-icon { background: rgba(16,185,129,0.12); }
    .stat-card.orange .stat-icon { background: rgba(245,158,11,0.12); }
    .stat-card.cyan .stat-icon { background: rgba(6,182,212,0.12); }
    .stat-card.purple .stat-icon { background: rgba(139,92,246,0.12); }
    .stat-card.pink .stat-icon { background: rgba(236,72,153,0.12); }
    .stat-card.gold .stat-icon { background: rgba(249,115,22,0.12); }

    .stat-label {
        color: var(--muted);
        font-size: 0.78rem;
        font-weight: 700;
        letter-spacing: 0.09em;
        text-transform: uppercase;
        margin: 0;
    }

    .stat-value {
        margin: 8px 0 0;
        font-size: clamp(1.8rem, 2.6vw, 2.4rem);
        font-weight: 800;
        line-height: 1;
        letter-spacing: -0.05em;
    }

    .stat-card a {
        display: inline-flex;
        color: var(--primary);
        font-weight: 700;
        margin-top: 12px;
    }

    .dashboard-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 22px;
    }

    .panel {
        padding: 24px 20px 18px;
    }

    .panel-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
        margin-bottom: 18px;
    }

    .panel-title {
        margin: 0;
        font-size: clamp(1.3rem, 2vw, 1.8rem);
        letter-spacing: -0.04em;
    }

    .panel-badge {
        background: #eef2ff;
        color: var(--primary);
        font-size: 0.76rem;
        font-weight: 800;
        letter-spacing: 0.09em;
        text-transform: uppercase;
        padding: 8px 12px;
        border-radius: 999px;
    }

    .table-wrap {
        overflow: auto;
        border-radius: 18px;
        border: 1px solid rgba(148,163,184,0.2);
    }

    table {
        width: 100%;
        min-width: 980px;
        border-collapse: collapse;
        background: white;
    }

    thead th {
        background: linear-gradient(180deg, #f8fafc, #eef2ff);
        color: #334155;
        padding: 15px 14px;
        text-align: left;
        font-size: 0.8rem;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        border-bottom: 1px solid #e2e8f0;
    }

    tbody td {
        padding: 14px;
        border-bottom: 1px solid #edf2f7;
        color: #1f2937;
        vertical-align: middle;
    }

    tbody tr:hover {
        background: #f8fafc;
    }

    .student-name {
        font-weight: 700;
    }

    .money {
        font-weight: 700;
        color: #111827;
    }

    .money.due {
        color: var(--danger);
    }

    .view-btn {
        background: linear-gradient(135deg, #4f46e5, #7c3aed);
        color: white;
        box-shadow: 0 12px 22px rgba(79,70,229,0.18);
    }

    .recent-list {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .recent-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        padding: 14px 12px;
        border-radius: 14px;
        background: #f8fafc;
        border: 1px solid #edf2f7;
    }

    .recent-item strong {
        display: block;
        margin-bottom: 4px;
        color: #111827;
    }

    .recent-item span {
        color: var(--muted);
        font-size: 0.82rem;
    }

    .pill {
        display: inline-block;
        padding: 7px 10px;
        border-radius: 999px;
        font-size: 0.72rem;
        font-weight: 800;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }

    .pill.success { background: rgba(16,185,129,0.12); color: var(--success); }
    .pill.warning { background: rgba(245,158,11,0.12); color: var(--warning); }

    @media (max-width: 960px) {
        .hero-row,
        .dashboard-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .topbar {
            padding: 18px 16px 14px;
        }

        .main-nav {
            width: 100%;
            border-radius: 18px;
            justify-content: flex-start;
        }

        .container {
            padding-left: 14px;
            padding-right: 14px;
        }

        .hero-panel,
        .panel {
            padding: 18px 14px 14px;
        }
    }
</style>
</head>
<body>
<header class="topbar">
    <div class="topbar-inner">
        <div class="brand">
            <div class="brand-badge">LX</div>
            <div>
                <div class="topbar-note">Dashboard</div>
                <h1>LearnX Institute</h1>
            </div>
        </div>

        <nav class="main-nav" aria-label="Main navigation">
            <a href="dashboard.php">Dashboard</a>
            <a href="student.php">Students</a>
            <a href="courses.php">Courses</a>
            <a href="batch.php">Batches</a>
            <a href="transreport.php">Transactions</a>
            <a href="paymentmode.php">Payment Modes</a>
            <a href="paymentterm.php">Payment Terms</a>
        </nav>
    </div>
</header>

<div class="container">
    <section class="hero-row">
        <div class="hero-panel">
            <div class="eyebrow">Overview</div>
            <h2>Welcome back,LearnX</h2>
            <p>EAT. CODE. SLEEP</p>
            <div class="hero-actions">
                <a class="primary-btn" href="student.php">Add new student</a>
                <a class="secondary-btn" href="transreport.php">View Transactions</a>
            </div>
        </div>

        <aside class="mini-panel">
            <div class="mini-header">
                <span>Priority</span>
                <span>Live</span>
            </div>
            <ul class="mini-list">
                <li>
                    <div class="mini-label">Pending dues</div>
                    <div class="mini-value"><?php echo $pending_students; ?></div>
                </li>
                <li>
                    <div class="mini-label">Collection</div>
                    <div class="mini-value">₹<?php echo number_format($total_collected,2); ?></div>
                </li>
                <li>
                    <div class="mini-label">Payments</div>
                    <div class="mini-value"><?php echo $transaction_count; ?></div>
                </li>
            </ul>
        </aside>
    </section>

    <div class="cards">
        <div class="stat-card blue">
            <div class="stat-inner">
                <div>
                    <p class="stat-label">Students</p>
                    <p class="stat-value"><?php echo $student_count; ?></p>
                </div>
                <div class="stat-icon">👨‍🎓</div>
            </div>
            <a href="student.php">View all →</a>
        </div>

        <div class="stat-card green">
            <div class="stat-inner">
                <div>
                    <p class="stat-label">Courses</p>
                    <p class="stat-value"><?php echo $course_count; ?></p>
                </div>
                <div class="stat-icon">📚</div>
            </div>
            <a href="courses.php">View all →</a>
        </div>

        <div class="stat-card orange">
            <div class="stat-inner">
                <div>
                    <p class="stat-label">Batches</p>
                    <p class="stat-value"><?php echo $batch_count; ?></p>
                </div>
                <div class="stat-icon">🧑‍🏫</div>
            </div>
            <a href="batch.php">View all →</a>
        </div>

        <div class="stat-card cyan">
            <div class="stat-inner">
                <div>
                    <p class="stat-label">Transactions</p>
                    <p class="stat-value"><?php echo $transaction_count; ?></p>
                </div>
                <div class="stat-icon">💳</div>
            </div>
            <a href="transreport.php">View all →</a>
        </div>

        <div class="stat-card purple">
            <div class="stat-inner">
                <div>
                    <p class="stat-label">Payment Modes</p>
                    <p class="stat-value"><?php echo $paymentmode_count; ?></p>
                </div>
                <div class="stat-icon">🏦</div>
            </div>
            <a href="paymentmode.php">View all →</a>
        </div>

        <div class="stat-card pink">
            <div class="stat-inner">
                <div>
                    <p class="stat-label">Payment Terms</p>
                    <p class="stat-value"><?php echo $paymentterm_count; ?></p>
                </div>
                <div class="stat-icon">📝</div>
            </div>
            <a href="paymentterm.php">View all →</a>
        </div>

        <div class="stat-card gold">
            <div class="stat-inner">
                <div>
                    <p class="stat-label">Total Collected</p>
                    <p class="stat-value">₹<?php echo number_format($total_collected,2); ?></p>
                </div>
                <div class="stat-icon">💰</div>
            </div>
            <span class="stat-label" style="letter-spacing:0.02em; text-transform:none;">Live collection summary</span>
        </div>
    </div>

    <div class="dashboard-grid">
        <section class="panel">
            <div class="panel-header">
                <h2 class="panel-title">Student Joining & Payment Details</h2>
                <span class="panel-badge">Updated overview</span>
            </div>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Student</th>
                            <th>Course</th>
                            <th>Batch</th>
                            <th>Total Payment</th>
                            <th>Paid</th>
                            <th>Due</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php while($row=mysqli_fetch_assoc($details)){ ?>
                        <tr>
                            <td><?php echo $row['st_id']; ?></td>
                            <td class="student-name"><?php echo htmlspecialchars($row['st_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['course_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['batch_name']); ?></td>
                            <td class="money">₹<?php echo number_format($row['total_payment'],2); ?></td>
                            <td class="money">₹<?php echo number_format($row['paid_amount'],2); ?></td>
                            <td class="money due">₹<?php echo number_format($row['due_amount'],2); ?></td>
                            <td>
                                <a class="view-btn" href="student_view.php?st_id=<?php echo $row['st_id']; ?>">View</a>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        </section>

        <aside class="panel">
            <div class="panel-header">
                <h2 class="panel-title">Recent Students</h2>
                <span class="panel-badge">Latest</span>
            </div>

            <ul class="recent-list">
                <?php while($row=mysqli_fetch_assoc($recent_students)){ ?>
                    <li class="recent-item">
                        <div>
                            <strong><?php echo htmlspecialchars($row['st_name']); ?></strong>
                            <span><?php echo htmlspecialchars($row['course_name']); ?></span>
                        </div>
                        <?php if ($row['due_amount'] > 0) { ?>
                            <span class="pill warning">Due</span>
                        <?php } else { ?>
                            <span class="pill success">Paid</span>
                        <?php } ?>
                    </li>
                <?php } ?>
            </ul>
        </aside>

    </div>
</div>
</body>
</html>