<?php
require_once "config/config.php";
?>
<form method="POST">
    FRUIT Name:<select name="fruits" id="fruits">
        <option value="">Select Fruits</option>
        <?php
        $result=mysqli_query($conn, "select * from fruits") or die(mysqli_error($conn));
        while ($row = mysqli_fetch_array($result)) {
        ?>
        <option value="<?php echo $row['f_id']; ?>"> <?php echo $row['f_name']; ?></option>
        <?php
        }
        ?>
</form>