<script>
function fruitupdate(f_id,f_name){
    document.getElementById("f_id").value = f_id;
    document.getElementById("fruits").value =f_name;
}
</script>
<table border="1px">
<?php
require_once "config/config.php";
if (isset($_POST['save_fruit'])) {
    $id=$_POST['f_id'];
    $fruit = $_POST['fruits'];
    if($id!=""){
        $qryl=mysqli_query($conn, "UPDATE fruits SET f_name='$fruit' WHERE f_id='$id'") or die(mysqli_error($conn));
        echo "The fruit " . $fruit . " has been successfully Updated.";
    }
   else {
    $qryl=mysqli_query($conn, "INSERT INTO fruits(f_name) VALUES ('$fruit')") or die(mysqli_error($conn));
    echo " The fruit " . $fruit . " has been successfully Saved.";
}
}
if (isset($_POST['get_fruit'])) {
    $result=mysqli_query($conn, "select * from fruits") or die(mysqli_error($conn));
    while ($row = mysqli_fetch_array($result)){
?>
    <tr>
        <td> <?php echo $row['f_id']; ?></td>
        <td><?php echo $row['f_name']; ?></td>
        <td><?php if ($row['status'] == '1'){
            echo "Available";
        } else{
            echo "Unavailable";
        }?>
        </td>
        <td><input type="submit" onclick="fruitupdate('<?php echo $row['f_id']; ?>','<?php echo $row['f_name']; ?>')" name="edit" id="edit" value="Edit"></td>
        </td>
    </tr>
<?php
    }
}
?>
</table>
<form method="POST">
    FRUIT:
    <input type="text" name="fruits" id="fruits" value="" placeholder="Fruit_Name"></br>
    <input type="submit" name="save_fruit" id="save_fruit" value="Add fruit"></br>
    <input type="submit" name="get_fruit" id="get_fruit" value="Get Fruits">
    <input type="number" name="f_id" id="f_id" placeholder="Fruit_ID">
</form>