<?php
require_once "config/config.php";
?>
<form method="POST">
    FRUIT Color:<select name="fruit_color" id="fruit_color">
        <option value="">Select Color</option>
        <?php
        $result=mysqli_query($conn, "select * from fruit_color") or die(mysqli_error($conn));
        while ($row = mysqli_fetch_array($result)) {
        ?>
        <option value="<?php echo $row['c_id']; ?>"> <?php echo $row['color']; ?></option>
        <?php
        }
        ?>
</form>