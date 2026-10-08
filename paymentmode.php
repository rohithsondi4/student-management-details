<?php
require_once "config/config.php";

if(isset($_POST['save'])){
    $payment_mode=mysqli_real_escape_string($conn,$_POST['payment_mode']);
    mysqli_query($conn,"INSERT INTO payment_mode (payment_mode) VALUES('$payment_mode')");
    header("Location: paymentmode.php"); 
    exit;
}

if(isset($_POST['update'])){
    $pm_id=$_POST['pm_id'];
    $payment_mode=mysqli_real_escape_string($conn,$_POST['payment_mode']);
    mysqli_query($conn,"UPDATE payment_mode SET payment_mode='$payment_mode' WHERE pm_id='$pm_id'");
    header("Location: paymentmode.php"); 
    exit;
}

if(isset($_GET['delete'])){
    $id=$_GET['delete'];
    mysqli_query($conn,"DELETE FROM payment_mode WHERE pm_id='$id'");
    header("Location: paymentmode.php"); 
    exit;
}

$edit=null;
if(isset($_GET['edit'])) {
    $edit=mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM payment_mode WHERE pm_id='".$_GET['edit']."'"));
}

$paymentmodes=mysqli_query($conn,"SELECT * FROM payment_mode ORDER BY payment_mode");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Payment Mode</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root { --bg:#f3f7ff; --surface:#ffffff; --surface-soft:#f8fbff; --line:#dfeaf7; --text:#1f2937; --muted:#60708d; --primary:#3b82f6; --primary-strong:#1d4ed8; --danger:#ef4444; --shadow:0 18px 38px rgba(59,130,246,0.12); }
        * { box-sizing: border-box; }
        body { margin:0; font-family:'Inter',Arial,sans-serif; color:var(--text); background:radial-gradient(circle at top left, rgba(59,130,246,0.08), transparent 22%), linear-gradient(180deg, #f4f8ff 0%, #edf4ff 100%); }
        .page-shell { max-width: 1100px; margin: 0 auto; padding: 24px 18px 40px; }
        .topbar { background: linear-gradient(135deg, #eef6ff 0%, #dbeafe 45%, #ffffff 100%); border: 1px solid var(--line); border-radius: 18px; padding: 18px 20px; box-shadow: var(--shadow); margin-bottom: 18px; animation: fadeUp 0.6s ease both; }
        .topbar h1 { margin: 0; color: var(--text); font-size: clamp(1.6rem, 2vw, 2.3rem); }
        .nav { display: flex; flex-wrap: wrap; gap: 10px; margin: 18px 0 24px; padding: 12px 14px; background: rgba(255,255,255,0.9); border: 1px solid var(--line); border-radius: 14px; box-shadow: 0 10px 16px rgba(148,163,184,0.08); }
        .nav a { color: var(--text); text-decoration: none; padding: 10px 14px; border-radius: 10px; font-weight: 700; background: var(--surface-soft); border: 1px solid var(--line); }
        .nav a:hover { background: linear-gradient(135deg, rgba(59,130,246,0.08), rgba(139,92,246,0.08)); color: var(--primary-strong); box-shadow: 0 8px 16px rgba(59,130,246,0.08); }
        .panel { background: rgba(255,255,255,0.96); border: 1px solid var(--line); border-radius: 20px; box-shadow: var(--shadow); overflow: hidden; margin-bottom: 26px; animation: fadeUp 0.75s ease 0.15s both; }
        .panel-head { padding: 18px 20px; border-bottom: 1px solid var(--line); background: linear-gradient(90deg, #f7fbff, #eef4ff); }
        .panel-head h2 { margin: 0; color: var(--text); font-size: clamp(1.2rem, 2vw, 1.6rem); }
        .panel-body { padding: 20px; }
        form { display: flex; flex-wrap: wrap; gap: 18px; align-items: end; }
        .field { display: flex; flex-direction: column; gap: 8px; flex: 1; min-width: 220px; }
        label { font-size: 0.8rem; letter-spacing: 0.08em; color: var(--muted); text-transform: uppercase; font-weight: 800; }
        input, button { border-radius: 12px; border: 1px solid var(--line); background: #ffffff; color: var(--text); padding: 12px 14px; font: inherit; outline: none; }
        input:focus { border-color: rgba(59,130,246,0.75); box-shadow: 0 0 0 3px rgba(59,130,246,0.12); }
        .btn { min-width: 140px; border: none; cursor: pointer; font-weight: 800; transition: transform 0.2s ease; }
        .btn-primary { background: linear-gradient(135deg, var(--primary), var(--primary-strong)); color: #fff; box-shadow: 0 12px 18px rgba(59,130,246,0.2); }
        .btn-secondary { background: #f5f8ff; color: var(--text); border: 1px solid var(--line); text-decoration: none; }
        .btn:hover { transform: translateY(-1px); }
        table { width: 100%; border-collapse: collapse; background: transparent; }
        th, td { padding: 14px 12px; text-align: left; border-bottom: 1px solid var(--line); color: var(--text); }
        th { background: #f8fbff; color: var(--muted); font-size: 0.72rem; letter-spacing: 0.12em; text-transform: uppercase; font-weight: 800; }
        tbody tr:hover { background: #f9fbff; }
        .action-links a { color: var(--text); text-decoration: none; padding: 6px 10px; border-radius: 8px; margin-right: 8px; }
        .action-links a:nth-child(1) { background: rgba(59,130,246,0.12); }
        .action-links a:nth-child(2) { background: rgba(239,68,68,0.09); }
        @keyframes fadeUp { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: translateY(0); } }
        @media (max-width:768px){ .page-shell{padding:18px 12px 30px;} .nav{padding:10px;} form{flex-direction:column; align-items:stretch;} }
    </style>
</head>
<body>
    <div class="page-shell">
        <div class="topbar"><h1>Payment Mode Management</h1></div>
        <div class="nav">
            <a href="dashboard.php">Dashboard</a>
            <a href="paymentterm.php">Payment Terms</a>
        </div>

        <div class="panel">
            <div class="panel-head"><h2><?php echo $edit ? 'Edit Payment Mode' : 'Add New Payment Mode'; ?></h2></div>
            <div class="panel-body">
                <form method="POST">
                    <?php if($edit){ ?><input type="hidden" name="pm_id" value="<?php echo $edit['pm_id']; ?>"><?php } ?>
                    <div class="field">
                        <label>Payment Mode</label>
                        <input type="text" name="payment_mode" required value="<?php echo isset($edit['payment_mode']) ? htmlspecialchars($edit['payment_mode']) : ''; ?>">
                    </div>
                    <div class="actions">
                        <?php if($edit){ ?><button type="submit" name="update" value="Update" class="btn btn-primary">Update</button><?php } else { ?><button type="submit" name="save" value="Save" class="btn btn-primary">Add Payment Mode</button><?php } ?>
                        <?php if($edit){ ?><a href="paymentmode.php" class="btn btn-secondary" style="display:inline-flex; align-items:center; justify-content:center;">Cancel</a><?php } ?>
                    </div>
                </form>
            </div>
        </div>

        <div class="panel">
            <div class="panel-head"><h2>Payment Modes</h2></div>
            <div class="panel-body" style="padding:0; overflow:auto;">
                <table>
                    <thead>
                        <tr><th>ID</th><th>Payment Mode</th><th>Action</th></tr>
                    </thead>
                    <tbody>
                        <?php while($pm=mysqli_fetch_assoc($paymentmodes)){ ?>
                        <tr>
                            <td><?php echo $pm['pm_id']; ?></td>
                            <td><?php echo htmlspecialchars($pm['payment_mode']); ?></td>
                            <td class="action-links">
                                <a href="?edit=<?php echo $pm['pm_id']; ?>">Edit</a>
                                <a href="?delete=<?php echo $pm['pm_id']; ?>" onclick="return confirm('Delete this payment mode?')">Delete</a>
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