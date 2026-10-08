<script>
function tasteupdate(t_id,type){
    document.getElementById("ta_id").value = t_id;
    document.getElementById("fruit_taste").value =type;
}
</script>
<table border="1px">
<?php
require_once "config/config.php";
if(isset($_POST['save_taste'])){
    $id=$_POST['ta_id'];
    $taste1 = $_POST['fruit_taste'];
    if($id!=""){
        $qryl=mysqli_query($conn, "UPDATE fruit_taste SET Taste='$taste1' WHERE ta_id='$id'") or die(mysqli_error($conn));
        echo "The fruit " . $taste1 . " has been successfully Updated.";
    }
   else{
    $qryl=mysqli_query($conn, "INSERT INTO fruit_taste(Taste) VALUES ('$taste1')") or die(mysqli_error($conn));
    echo " The fruit " . $taste1 . " has been successfully Saved.";
}
}
if (isset($_POST['get_taste'])){
    $result=mysqli_query($conn, "select * from fruit_taste") or die(mysqli_error($conn));
    while ($row = mysqli_fetch_array($result)){
?>
    <tr>
        <td> <?php echo $row['ta_id']; ?></td>
        <td style="padding:10px;"><?php echo $row['Taste']; ?></td>
        <td><?php if ($row['status']=='1'){
            echo "Available";
        } else{
            echo "Unavailable";
        }?>
        </td>
        <td><input type="submit" onclick="tasteupdate('<?php echo $row['ta_id']; ?>','<?php echo $row['Taste']; ?>')" name="edit" id="edit" value="Edit"></td>
    </tr>
<?php
    }
}
?>
</table>
<form method="POST">
    FRUIT Taste:
    <input type="text" name="fruit_taste" id="fruit_taste" value="" placeholder="Fruit_taste"></br>
    <input type="submit" name="save_taste" id="save_taste" value="Add taste"></br>
    <input type="submit" name="get_taste" id="get_taste" value="Get taste">
    <input type="number" name="ta_id" id="ta_id" placeholder="Type_ID">
</br>
</form>