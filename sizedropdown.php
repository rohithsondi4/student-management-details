<?php
require_once "config/config.php";
?>
<form method="POST">
    FRUIT size:<select name="fruit_size" id="fruit_size">
        <option value="">Select Size</option>
        <?php
        $result=mysqli_query($conn, "select * from fruit_size") or die(mysqli_error($conn));
        while ($row = mysqli_fetch_array($result)) {
        ?>
        <option value="<?php echo $row['s_id']; ?>"> <?php echo $row['size']; ?></option>
        <?php
        }
        ?>
</form>