<?php
require_once "config/config.php";
?>
<form method="POST">
    FRUIT Type:<select name="fruit_type" id="fruit_type">
        <option value="">Select Type</option>
        <?php
        $result=mysqli_query($conn, "select * from fruit_type") or die(mysqli_error($conn));
        while ($row = mysqli_fetch_array($result)) {
        ?>
        <option value="<?php echo $row['t_id']; ?>"> <?php echo $row['type']; ?></option>
        <?php
        }
        ?>
</form>