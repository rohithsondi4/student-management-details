<script>
   function sizeupdate(s_id,size){
   document.getElementByID("s_id").value=s_id;
   document.getElementByID("fruit_size").values=size;
   }
</script>
<table border="1px">
<?php
require_once "config/config.php";
if(isset($_POST['save_size'])){
   $id=$_POST['s_id'];
   $size1 = $_POST['fruit_size'];
   if($id!=""){
      $qryl=mysqli_query($conn, "UPDATE fruit_size SET size='$size1' WHERE s_id='$id'") or die(mysqli_error($conn));
      echo "The fruit " . $size1 . " has been successfully Updated.";
   }
   else{
   $qryl=mysqli_query($conn, "INSERT INTO fruit_size(size) VALUES ('$size1')") or die(mysqli_error($conn));
   echo " The fruit " . $size1 . " has been successfully Saved.";
}
}
if(isset($_POST['get_size'])){
   $result=mysqli_query($conn, "select * from fruit_size") or die(mysqli_error($conn));
   while ($row = mysqli_fetch_array($result)){
?>
   <tr>
      <td> <?php echo $row['s_id']; ?></td>
      <td ><?php echo $row['size']; ?></td>
      <td><?php if ($row['status']=='1'){
         echo "Available";
      }else{
         echo "Unavailable";
      }?>
      </td>
      <td><input type="submit" onclick="sizeupdate('<?php echo $row['s_id']; ?>','<?php echo $row['size']; ?>')" name="edit" id="edit" value="Edit"></td>
      </td>
   </tr>
<?php
}
}
?>
</table>
<form method="POST">
   FRUIT SIZE:
   <input type="text" name="fruit_size" id="fruit_size" value="" placeholder="Fruit_size"></br>
   <input type="submit" name="save_size" id="save_size" value="Add size"></br>
   <input type="submit" name="get_size" id="get_size" value="Get size">
    <input type="number" name="s_id" id="s_id" placeholder="Type_ID">
</form> 