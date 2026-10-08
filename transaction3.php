<?php
require_once "config/config.php";
if (isset($_POST['save'])) {
   $p_id = $_POST['p_id'];
    $quantity = $_POST['quantity'];
    $price = $_POST['price'];
    $total = $price * $quantity;
if($p_id != "") {
   $qryl = mysqli_query($conn, "INSERT INTO `transaction`(p_id, quantity, price, total) VALUES ('$p_id', '$quantity', '$price', '$total')") or die(mysqli_error($conn));
echo "Transaction successfully Saved.";
    }
}
?>
<script>
function getPriceAndDetails() {
  var p_id=document.getElementById('p_id').value;
    if(p_id == ""){
        document.getElementById('fruit').value = '';
        document.getElementById('color').value = '';
        document.getElementById('size').value = '';
        document.getElementById('taste').value = '';
        document.getElementById('type').value = '';
        document.getElementById('price').value = '';
        document.getElementById('img_url').value = '';
        document.getElementById('quantity').value = '';
        document.getElementById('total').value = '';
        $('#fruit_image_preview')
            .hide()
            .attr('src', '');

    }
    var selected = document.getElementById('p_id').options[document.getElementById('p_id').selectedIndex];
    document.getElementById('fruit').value = selected.getAttribute('data-fruit');
    document.getElementById('color').value = selected.getAttribute('data-color');
    document.getElementById('size').value = selected.getAttribute('data-size');
    document.getElementById('taste').value = selected.getAttribute('data-taste');
    document.getElementById('type').value = selected.getAttribute('data-type');
    document.getElementById('price').value = selected.getAttribute('data-price');
    document.getElementById('img_url').value = selected.getAttribute('data-img');
    var image = selected.getAttribute('data-img');
    if (image != "") {
        document.getElementById('fruit_image_preview').src = image;
        document.getElementById('fruit_image_preview').style.display = 'block';
    } else {
        document.getElementById('fruit_image_preview').style.display = 'none';
    }
    document.getElementById('quantity').value = '';
    document.getElementById('total').value = '';
}
function gettotalprice() {
    var price = parseFloat(document.getElementById('price').value);
    var quantity = parseFloat(document.getElementById('quantity').value);
    if (!isNaN(price) && !isNaN(quantity)){
        document.getElementById('total').value = price * quantity;
    } else {
        document.getElementById('total').value = '';
    }
}
</script>
<form method="POST">
    P_ID:<select name="p_id" id="p_id" onchange="getPriceAndDetails()">
        <option value="">Select P_ID</option>
        <?php
        $result = mysql_query("SELECT * FROM pricing WHERE status='1'")or die(mysql_error());
        while ($row = mysql_fetch_array($result)){
            $f_id = $row['f_id'];
            $c_id = $row['c_id'];
            $s_id = $row['s_id'];
            $ta_id = $row['ta_id'];
            $t_id = $row['t_id'];
            $fruit_result = mysql_query("SELECT f_name FROM fruits WHERE f_id='$f_id'") or die(mysql_error());
           $fruit_row = mysql_fetch_assoc($fruit_result);
           $color_result = mysql_query("SELECT color FROM fruit_color WHERE c_id='$c_id'") or die(mysql_error());
            $color_row = mysql_fetch_assoc($color_result);
            $size_result = mysql_query("SELECT size FROM fruit_size WHERE s_id='$s_id'") or die(mysql_error());
            $size_row = mysql_fetch_assoc($size_result);
            $taste_result = mysql_query("SELECT Taste FROM fruit_taste WHERE ta_id='$ta_id'" ) or die(mysql_error());
            $taste_row = mysql_fetch_assoc($taste_result);
            $type_result = mysql_query(
                "SELECT type FROM fruit_type WHERE t_id='$t_id'"
            ) or die(mysql_error());
            $type_row = mysql_fetch_assoc($type_result);
        ?>
            <option value="<?php echo $row['p_id']; ?>"
                data-fruit="<?php echo $fruit_row['f_name']; ?>"
                data-color="<?php echo $color_row['color']; ?>"
                data-size="<?php echo $size_row['size']; ?>"
                data-taste="<?php echo $taste_row['Taste']; ?>"
                data-type="<?php echo $type_row['type']; ?>"
                data-price="<?php echo $row['price']; ?>"
                data-img="<?php echo $row['img_url']; ?>"
            >
                <?php echo $row['p_id']; ?>
            </option>
        <?php
        }
        ?>
    </select>
    <br>
 Fruit:
    <input type="text" name="fruit" id="fruit">
 Color:
    <input type="text" name="color" id="color">
Size:
    <input type="text" name="size" id="size">
 Taste:
    <input type="text" name="taste" id="taste">
Type:
    <input type="text" name="type" id="type">
    Price:
    <input type="number" name="price" id="price">
    <img id="fruit_image_preview" src="" width="100" height="100" style="display:none;">
Quantity:
<input type="number" name="quantity" id="quantity" value="" onchange="gettotalprice()"onkeyup="gettotalprice()">
    Total:
<input type="number" name="total" id="total" value="">
    <input type="submit" name="save" id="save" value="SAVE">
    <input type="submit"name="get"id="get"value="GET">
<table border="1">
    <tr>
        <th>S.no</th>
        <th>P_ID</th>
        <th>Fruit</th>
        <th>Color</th>
            <th>Size</th>
            <th>Taste</th>
            <th>Type</th>
            <th>Quantity</th>
            <th>Price</th>
            <th>Total</th>
        </tr>
        <?php
        if (isset($_POST['get'])) {

            $result = mysql_query(
                "SELECT * FROM `transaction`"
            ) or die(mysql_error());

            $sno = 1;

            while ($row = mysql_fetch_array($result)) {

                $p_id = $row['p_id']; $pricing_result = mysql_query(
                    "SELECT * FROM pricing WHERE p_id='$p_id'"
                ) or die(mysql_error());

                $pricing = mysql_fetch_assoc($pricing_result);

                $f_id = $pricing['f_id'];
                $c_id = $pricing['c_id'];
                $s_id = $pricing['s_id'];
                $ta_id = $pricing['ta_id'];
                $t_id = $pricing['t_id'];

                $fruit_result = mysql_query(
                    "SELECT f_name FROM fruits WHERE f_id='$f_id'"
                ) or die(mysql_error());

                $fruit_name = mysql_fetch_assoc($fruit_result);
                
                $color_result = mysql_query(
                    "SELECT color FROM fruit_color WHERE c_id='$c_id'"
                ) or die(mysql_error());

                $color_name = mysql_fetch_assoc($color_result);

                $size_result = mysql_query(
                    "SELECT size FROM fruit_size WHERE s_id='$s_id'"
                ) or die(mysql_error());

                $size_name = mysql_fetch_assoc($size_result);

                $taste_result = mysql_query("SELECT Taste FROM fruit_taste WHERE ta_id='$ta_id'") or die(mysql_error());
                $taste_name = mysql_fetch_assoc($taste_result);
                $type_result = mysql_query("SELECT type FROM fruit_type WHERE t_id='$t_id'") or die(mysql_error());
                $type_name = mysql_fetch_assoc($type_result);
               
        ?>
        <tr>
            <td>
                <?php echo $sno; ?>
            </td>
            <td>
                <?php echo $row['p_id']; ?>
            </td>
            <td>
                <?php echo $fruit_name['f_name']; ?>
            </td>
            <td>
                <?php echo $color_name['color']; ?>
            </td>
            <td>
                <?php echo $size_name['size']; ?>
            </td>
            <td>
                <?php echo $taste_name['Taste']; ?>
            </td>
            <td>
                <?php echo $type_name['type']; ?>
            </td>
            <td>
                <?php echo $row['quantity']; ?>
            </td>
            <td>
                <?php echo $row['price']; ?>
            </td>
            <td>
                <?php echo $row['total']; ?>
            </td>
        </tr>
        <?php
                $sno++;
            }
        }
        ?>
    </table>
</form>