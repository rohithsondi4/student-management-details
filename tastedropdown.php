<?php
require_once "config/config.php";
?>
<form method="POST">
    FRUIT Taste:<select name="fruit_taste" id="fruit_taste">
        <option value="">Select Type</option>
        <?php
        $result=mysqli_query($conn, "select * from fruit_taste") or die(mysqli_error($conn));
        while ($row = mysqli_fetch_array($result)) {
        ?>
        <option value="<?php echo $row['ta_id']; ?>"> <?php echo $row['Taste']; ?></option>
        <?php
        }
        ?>
</form>