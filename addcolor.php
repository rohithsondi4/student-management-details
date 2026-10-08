<script>
function colorupdate(c_id,color){
    document.getElementById("c_id").value = c_id;
    document.getElementById("fruit_color").value =color;
}
</script>
<table border="1px">
<?php
require_once "config/config.php";
if (isset($_POST['save_color'])) {
    $id=$_POST['c_id'];
    $color1 = $_POST['fruit_color'];
    if($id!=""){
        $qryl=mysqli_query($conn, "UPDATE fruit_color SET color='$color1' WHERE c_id='$id'") or die(mysqli_error($conn));
        echo "The fruit " . $color1 . " has been successfully Updated.";
    }
   else {
    $qryl=mysqli_query($conn, "INSERT INTO fruit_color (color) VALUES ('$color1')") or die(mysqli_error($conn));
    echo " The fruit " . $color1 . " has been successfully Saved.";
}
}
if (isset($_POST['get_color'])) {
    $result=mysqli_query($conn, "select * from fruit_color") or die(mysqli_error($conn));
    while ($row = mysqli_fetch_array($result)){
?>
    <tr>
        <td> <?php echo $row['c_id']; ?></td>
        <td><?php echo $row['color']; ?></td>
        <td><?php if ($row['status']=='1'){
            echo "Available";
        } else{
            echo "Unavailable";
        }?>
        </td>
        <td><input type="submit" onclick="colorupdate('<?php echo $row['c_id']; ?>','<?php echo $row['color']; ?>')" name="edit" id="edit" value="Edit"></td>
        </td>
    </tr>
<?php
    }
}
?>
</table>
<form method="POST">
    FRUIT Color:
    <input type="text" name="fruit_color" id="fruit_color" value="" placeholder="Fruit_Color"></br>
    <input type="submit" name="save_color" id="save_color" value="Add Color"></br>
    <input type="submit" name="get_color" id="get_color" value="Get Color">
    <input type="number" name="c_id" id="c_id" placeholder="color_ID">
    <br>
</form>
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