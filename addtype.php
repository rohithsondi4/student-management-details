<script>
function typeupdate(t_id,type){
    document.getElementById("t_id").value = t_id;
    document.getElementById("fruit_type").value =type;
}
</script>
<table border="1px">
<?php
require_once "config/config.php";
if (isset($_POST['save_type'])){
    $id=$_POST['t_id'];
    $type1 = $_POST['fruit_type'];
    if($id!=""){
        $qryl=mysqli_query($conn, "UPDATE fruit_type SET type='$type1' WHERE t_id='$id'") or die(mysqli_error($conn));
        echo "The fruit " . $type1 . " has been successfully Updated.";
    }
   else{
    $qryl=mysqli_query($conn, "INSERT INTO fruit_type(type) VALUES ('$type1')") or die(mysqli_error($conn));
    echo " The fruit " . $type1 . " has been successfully Saved.";
}
}
if (isset($_POST['get_type'])){
    $result=mysqli_query($conn, "select * from fruit_type") or die(mysqli_error($conn));
    while ($row = mysqli_fetch_array($result)){
?>
    <tr>
        <td> <?php echo $row['t_id']; ?></td>
        <td style="padding:10px;"><?php echo $row['type']; ?></td>
        <td><?php if ($row['status']=='1'){
            echo "Available";
        } else{
            echo "Unavailable";
        }?>
        </td>
        <td><input type="submit" onclick="typeupdate('<?php echo $row['t_id']; ?>','<?php echo $row['type']; ?>')" name="edit" id="edit" value="Edit"></td>
    </tr>
<?php
    }
}
?>
</table>
<form method="POST">
    FRUIT:
    <input type="text" name="fruit_type" id="fruit_type" value="" placeholder="Fruit_type"></br>
    <input type="submit" name="save_type" id="save_type" value="Add type"></br>
    <input type="submit" name="get_type" id="get_type" value="Get type">
    <input type="number" name="t_id" id="t_id" placeholder="Type_ID">
</form>