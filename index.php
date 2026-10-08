<table border="1px">
<?php
require_once "config/config.php";
if (isset($_POST['save_fruit'])) {
    $fruit = $_POST['fruits'];
    echo "$fruit";
    $qryl = mysqli_query($conn, "INSERT INTO fruits(f_name) VALUES ('$fruit')") or die(mysqli_error($conn));
    echo " The fruit " . $fruit . " has been successfully Saved.";
}
if (isset($_POST['get_fruit'])){
    $result = mysqli_query($conn, "select * from fruits") or die(mysqli_error($conn));
    while ($row = mysqli_fetch_array($result)){
        echo "<tr>",
        "<td>", $row['f_id'],"</td>",
        "<td>" , $row['f_name'],"</td>";
        echo "<td>";
        if($row['status']=='1'){ 
            echo "Available"; } 
        else{ 
            echo "Unavailable"; }
        echo "</td>";
         echo "<td>",
        "<input type='submit' name='edit' value='Edit'>",
        "</td>";
        echo "</tr>";
        echo "<br>";
    }
}

if(isset($_POST['save_color'])){
    $Fruit_color=$_POST['fruit_color'];
    echo "$Fruit_color";
    $qryl = mysqli_query($conn, "INSERT INTO fruit_color(color) VALUES ('$Fruit_color')") or die(mysqli_error($conn));
    echo " The fruit color is " . $Fruit_color . " has been successfully Saved.";
}
if (isset($_POST['get_color'])){
$result_1=mysqli_query($conn, "select * from fruit_color") or die(mysqli_error($conn));
while ($row = mysqli_fetch_array($result_1)){
    echo "<tr><td>" ,$row['c_id'],"<td>",$row['color'],"</td>","</td>","</tr>";
}
}

if(isset($_POST['save_size'])){
    $Fruit_size=$_POST['fruit_size'];
    echo "$Fruit_size";
    $qryl = mysqli_query($conn, "INSERT INTO fruit_size(size) VALUES ('$Fruit_size')") or die(mysqli_error($conn));
    echo " The fruit size is " . $Fruit_size . " has been successfully Saved.";
}
if (isset($_POST['get_size'])){
$result_2=mysqli_query($conn, "select * from fruit_size") or die(mysqli_error($conn));
while ($row = mysqli_fetch_array($result_2)){
    echo $row['s_id'];
    echo "<br>";
    echo $row['size'];
    echo "<br>";
}
}

if(isset($_POST['save_type'])){
    $Fruit_type=$_POST['fruit_type'];
    echo "$Fruit_type";
    $qryl = mysqli_query($conn, "INSERT INTO fruit_type(type) VALUES ('$Fruit_type')") or die(mysqli_error($conn));
    echo " The fruit type is " . $Fruit_type . " has been successfully Saved.";
}
if (isset($_POST['get_type'])){
$result_3=mysqli_query($conn, "select * from fruit_type") or die(mysqli_error($conn));
while ($row = mysqli_fetch_array($result_3)){
    echo $row['t_id'];
    echo $row['type'];
    echo "<br>";
}
}

if(isset($_POST['save_taste'])){
    $Fruit_taste=$_POST['fruit_taste'];
    echo "$Fruit_taste";
    $qryl = mysqli_query($conn, "INSERT INTO fruit_taste(Taste) VALUES ('$Fruit_taste')") or die(mysqli_error($conn));
    echo " The fruit taste is " . $Fruit_taste . " has been successfully Saved.";
}
if (isset($_POST['get_taste'])){
$result_4=mysqli_query($conn, "select * from fruit_taste") or die(mysqli_error($conn));
while ($row = mysqli_fetch_array($result_4)){
    echo $row['ta_id'];
    echo "<br>";
    echo $row['Taste'];
    echo "<br>";
}
}
?>
</table>
<form method="POST">
    FRUIT:
    <input type="text" name="fruits" id="fruits" value="" placeholder="Fruit_Name"></br>
    <input type="submit" name="save_fruit" id="save_fruit" value="Add fruit"></br>
    <input type="submit" name="get_fruit" id="get_fruit" value="Get Fruits">
</br>
    FRUIT COLOR:
    <input type="text" name="fruit_color" id="fruit_color" value="" placeholder="Fruit_Color"></br>
    <input type="submit" name="save_color" id="save_color" value="Add Color"></br>
    <input type="submit" name="get_color" id="get_color" value="Get Color">
</br>
    FRUIT SIZE:
    <input type="text" name="fruit_size" id="fruit_size" value="" placeholder="Fruit_size"></br>
    <input type="submit" name="save_size" id="save_size" value="Add size"></br>
    <input type="submit" name="get_size" id="get_size" value="Get size">
</br>
   FRUIT Type:
    <input type="text" name="fruit_type" id="fruit_type" value="" placeholder="Fruit_type"></br>
    <input type="submit" name="save_type" id="save_type" value="Add type"></br>
    <input type="submit" name="get_type" id="get_type" value="Get type">
</br>
    FRUIT Taste:
    <input type="text" name="fruit_taste" id="fruit_taste" value="" placeholder="Fruit_taste"></br>
    <input type="submit" name="save_taste" id="save_taste" value="Add taste"></br>
    <input type="submit" name="get_taste" id="get_taste" value="Get taste">
</br>
</form>