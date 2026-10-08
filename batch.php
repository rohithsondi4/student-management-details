<?php
require_once "config/config.php";

if(isset($_POST['save'])){
    $batch_name=mysqli_real_escape_string($conn,$_POST['batch_name']);
    $start_date=$_POST['start_date']; $end_date=$_POST['end_date'];
    $start_time=$_POST['start_time']; $end_time=$_POST['end_time'];
    mysqli_query($conn,"INSERT INTO batch(batch_name,start_date,end_date,start_time,end_time)
    VALUES('$batch_name','$start_date','$end_date','$start_time','$end_time')");
    header("Location: batch.php"); 
    exit;
}

if(isset($_POST['update'])){
    $b_id=$_POST['b_id'];
    $batch_name=mysqli_real_escape_string($conn,$_POST['batch_name']);
    $start_date=$_POST['start_date']; $end_date=$_POST['end_date'];
    $start_time=$_POST['start_time']; $end_time=$_POST['end_time'];
    mysqli_query($conn,"UPDATE batch SET batch_name='$batch_name',start_date='$start_date',
    end_date='$end_date',start_time='$start_time',end_time='$end_time' WHERE b_id='$b_id'");
    header("Location: batch.php");
    exit;
}

if(isset($_GET['delete'])){
    mysqli_query($conn,"DELETE FROM batch WHERE b_id='".$_GET['delete']."'");
    header("Location: batch.php"); 
    exit;
}

$edit=null;
if(isset($_GET['edit'])) $edit=mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM batch WHERE b_id='".$_GET['edit']."'"));
$result=mysqli_query($conn,"SELECT * FROM batch ORDER BY b_id ASC");
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Batches</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
        <style>
            :root {
                --bg: #f3f7ff;
                --bg-2: #ebf4ff;
                --surface: #ffffff;
                --surface-soft: #f8fbff;
                --line: #dfeaf7;
                --text: #1f2937;
                --muted: #60708d;
                --primary: #3b82f6;
                --primary-strong: #1d4ed8;
                --primary-soft: #e0edff;
                --danger: #ef4444;
                --shadow: 0 18px 38px rgba(59, 130, 246, 0.12);
            }
            * { box-sizing: border-box; }
            body {
                margin: 0;
                font-family: 'Inter', Arial, sans-serif;
                background: radial-gradient(circle at top left, rgba(59,130,246,0.08), transparent 22%), linear-gradient(180deg, #f4f8ff 0%, #edf4ff 100%);
                color: var(--text);
            }
            .page-shell { max-width: 1200px; margin: 0 auto; padding: 24px 18px 40px; }
            .topbar {
                background: linear-gradient(135deg, #eef6ff 0%, #dbeafe 45%, #ffffff 100%);
                border: 1px solid var(--line);
                border-radius: 18px; padding: 18px 20px; box-shadow: var(--shadow); margin-bottom: 18px; animation: fadeUp 0.6s ease both;
            }
            .topbar h1 { margin: 0; color: var(--text); font-size: clamp(1.6rem, 2vw, 2.3rem); }
            .nav { display: flex; flex-wrap: wrap; gap: 10px; margin: 18px 0 24px; padding: 12px 14px; background: rgba(255,255,255,0.9); border: 1px solid var(--line); border-radius: 14px; box-shadow: 0 10px 16px rgba(148,163,184,0.08); }
            .nav a { color: var(--text); text-decoration: none; padding: 10px 14px; border-radius: 10px; font-weight: 700; background: var(--surface-soft); border: 1px solid var(--line); }
            .nav a:hover { background: linear-gradient(135deg, var(--primary-soft), #eef4ff); color: var(--primary-strong); box-shadow: 0 8px 16px rgba(59,130,246,0.08); }
            .panel { background: rgba(255,255,255,0.96); border: 1px solid var(--line); border-radius: 20px; box-shadow: var(--shadow); overflow: hidden; margin-bottom: 26px; animation: fadeUp 0.75s ease 0.15s both; }
            .panel-head { padding: 18px 20px; border-bottom: 1px solid var(--line); background: linear-gradient(90deg, #f7fbff, #eef4ff); }
            .panel-head h2 { margin: 0; color: var(--text); font-size: clamp(1.2rem, 2vw, 1.6rem); }
            .panel-body { padding: 20px; }
            form { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 18px; }
            .field { display: flex; flex-direction: column; gap: 8px; }
            label { font-size: 0.8rem; letter-spacing: 0.08em; color: var(--muted); text-transform: uppercase; font-weight: 800; }
            input, button { border-radius: 12px; border: 1px solid var(--line); background: #ffffff; color: var(--text); padding: 12px 14px; font: inherit; outline: none; }
            input:focus { border-color: rgba(59,130,246,0.75); box-shadow: 0 0 0 3px rgba(59,130,246,0.12); }
            .btn { min-width: 140px; border: none; cursor: pointer; font-weight: 800; transition: transform 0.2s ease; }
            .btn-primary { background: linear-gradient(135deg, var(--primary), var(--primary-strong)); color: #fff; box-shadow: 0 12px 18px rgba(59,130,246,0.2); }
            .btn-secondary { background: #f5f8ff; color: var(--text); border: 1px solid var(--line); }
            .btn:hover { transform: translateY(-1px); }
            .actions { display: flex; gap: 12px; align-items: center; margin-top: 8px; }
            table { width: 100%; border-collapse: collapse; background: transparent; }
            th, td { padding: 14px 12px; text-align: left; border-bottom: 1px solid var(--line); color: var(--text); }
            th { background: #f8fbff; color: var(--muted); font-size: 0.72rem; letter-spacing: 0.12em; text-transform: uppercase; font-weight: 800; }
            tbody tr:hover { background: #f9fbff; }
            .action-links a { color: var(--text); text-decoration: none; margin-right: 8px; padding: 6px 10px; border-radius: 8px; }
            .action-links a:nth-child(1) { background: rgba(59,130,246,0.12); }
            .action-links a:nth-child(2) { background: rgba(239,68,68,0.09); }
            @keyframes fadeUp { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: translateY(0); } }
            @media (max-width:768px){ .page-shell{padding:18px 12px 30px;} .nav{padding:10px;} form{grid-template-columns:1fr;} }
        </style>
    </head>
    <body>
        <div class="page-shell">
            <div class="topbar"><h1>Batch Management</h1></div>
            <div class="nav">
                <a href="dashboard.php">Dashboard</a>
                <a href="courses.php">Courses</a>
                <a href="student.php">Students</a>
                <a href="transreport.php">Transactions</a>
            </div>

            <div class="panel">
                <div class="panel-head"><h2><?php echo $edit ? 'Edit Batch' : 'Add New Batch'; ?></h2></div>
                <div class="panel-body">
                    <form method="POST">
                        <?php if($edit){ ?><input type="hidden" name="b_id" value="<?php echo $edit['b_id']; ?>"><?php } ?>
                        <div class="field">
                            <label>Batch Name</label>
                            <input type="text" name="batch_name" required value="<?php echo $edit['batch_name'] ? $edit['batch_name'] : ''; ?>">
                        </div>
                        <div class="field">
                            <label>Start Date</label>
                            <input type="date" name="start_date" required value="<?php echo $edit['start_date'] ? $edit['start_date'] : ''; ?>">
                        </div>
                        <div class="field">
                            <label>End Date</label>
                            <input type="date" name="end_date" required value="<?php echo $edit['end_date'] ? $edit['end_date'] : ''; ?>">
                        </div>
                        <div class="field">
                            <label>Start Time</label>
                            <input type="time" name="start_time" required value="<?php echo $edit['start_time'] ? $edit['start_time'] : ''; ?>">
                        </div>
                        <div class="field">
                            <label>End Time</label>
                            <input type="time" name="end_time" required value="<?php echo $edit['end_time'] ? $edit['end_time'] : ''; ?>">
                        </div>
                        <div class="actions" style="grid-column: 1 / -1;">
                            <?php if($edit){ ?><button class="btn btn-primary" name="update">Update</button><?php } else { ?><button class="btn btn-primary" name="save">Save</button><?php } ?>
                            <?php if($edit){ ?><a class="btn btn-secondary" href="batch.php" style="text-decoration:none;">Cancel</a><?php } ?>
                        </div>
                    </form>
                </div>
            </div>

            <div class="panel">
                <div class="panel-head"><h2>Batches</h2></div>
                <div class="panel-body" style="padding:0; overflow:auto;">
                    <table>
                        <thead>
                            <tr><th>ID</th><th>Name</th><th>Start</th><th>End</th><th>Start Time</th><th>End Time</th><th>Action</th></tr>
                        </thead>
                        <tbody>
                            <?php while($row=mysqli_fetch_assoc($result)){ ?>
                            <tr>
                                <td><?php echo $row['b_id']; ?></td>
                                <td><?php echo htmlspecialchars($row['batch_name']); ?></td>
                                <td><?php echo $row['start_date']; ?></td>
                                <td><?php echo $row['end_date']; ?></td>
                                <td><?php echo $row['start_time']; ?></td>
                                <td><?php echo $row['end_time']; ?></td>
                                <td class="action-links">
                                    <a href="?edit=<?php echo $row['b_id']; ?>">Edit</a>
                                    <a href="?delete=<?php echo $row['b_id']; ?>" onclick="return confirm('Delete this batch?')">Delete</a>
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