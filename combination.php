<?php
require_once "config/config.php";
if (isset($_POST['save'])) {
    $p_id = $_POST['p_id'];
    $fruit = $_POST['fruits'];
    $color = $_POST['fruit_color'];
    $size = $_POST['fruit_size'];
    $taste = $_POST['fruit_taste'];
    $type = $_POST['fruit_type'];
    $cost = $_POST['cost'];
    $price = $_POST['price'];
    $imagePath = "";
    if (
        isset($_FILES['fruit_image']) &&$_FILES['fruit_image']['error'] == UPLOAD_ERR_OK) {
   $imageInfo = getimagesize($_FILES['fruit_image']['tmp_name']);

        if ($imageInfo === false) {
            die("Please upload a valid image.");
        }
    $allowedTypes = array(IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF);

        if (!in_array($imageInfo[2], $allowedTypes)) {
            die("Only JPG, PNG, and GIF images are allowed.");
        }

        $extension = image_type_to_extension($imageInfo[2], false);
        $fileName = uniqid("fruit_", true) . "." . $extension;

        $uploadDirectory = __DIR__ . "/images/";

        if (!is_dir($uploadDirectory)) {
            mkdir($uploadDirectory, 0755, true);
        }

        if (!move_uploaded_file(
            $_FILES['fruit_image']['tmp_name'],
            $uploadDirectory . $fileName
        )) {
            die("Image upload failed.");
        }

        // This value is stored in the database.
        $imagePath = "images/" . $fileName;
    }
    if ($p_id != "") {
        if ($imagePath != "") {
            $updateQuery = "UPDATE pricing SET
            f_id='$fruit',
            c_id='$color',
            s_id='$size',
            ta_id='$taste',
            t_id='$type',
            cost='$cost',
            price='$price',
            img_url='$imagePath'
            WHERE p_id='$p_id'";
        } else {
            $updateQuery = "UPDATE pricing SET
            f_id='$fruit',
            c_id='$color',
            s_id='$size',
            ta_id='$taste',
            t_id='$type',
            cost='$cost',
            price='$price'
            WHERE p_id='$p_id'";
        }
        mysqli_query($conn, $updateQuery) or die(mysqli_error($conn));
    } else {
        $qryl = mysqli_query($conn, "INSERT INTO pricing(f_id,c_id,s_id,ta_id,t_id,cost,price,img_url) VALUES ('$fruit','$color','$size','$taste','$type','$cost','$price','$imagePath')") or die(mysqli_error($conn));
        echo " The fruit " . $fruit . " has been successfully Saved.";
    }
}
?>
<script>
    function priceupdate(p_id, f_id, c_id, s_id, ta_id, t_id, cost, price) {
        document.getElementById('p_id').value = p_id;
        document.getElementById('fruits').value = f_id;
        document.getElementById('fruit_color').value = c_id;
        document.getElementById('fruit_size').value = s_id;
        document.getElementById('fruit_taste').value = ta_id;
        document.getElementById('fruit_type').value = t_id;
        document.getElementById('cost').value = cost;
        document.getElementById('price').value = price;
    }
</script>
<form method="POST" enctype="multipart/form-data">

    FRUIT Name:<select name="fruits" id="fruits">
        <option value="">Select Fruits</option>
        <?php
        $result=mysqli_query($conn, "select * from fruits") or die(mysqli_error($conn));
        while ($row=mysqli_fetch_array($result)){
        ?>
            <option value="<?php echo $row['f_id']; ?>"> <?php echo $row['f_name']; ?></option>
        <?php
        }
        ?>
    </select>
    <br>
    FRUIT Color:<select name="fruit_color" id="fruit_color">
        <option value="">Select Color</option>
        <?php
        $result1 = mysqli_query($conn, "select * from fruit_color") or die(mysqli_error($conn));
        while ($row = mysqli_fetch_array($result1)) {
        ?>
            <option value="<?php echo $row['c_id']; ?>"> <?php echo $row['color']; ?></option>
        <?php
        }
        ?>
    </select>
    <br>
    FRUIT size:<select name="fruit_size" id="fruit_size">
        <option value="">Select Size</option>
        <?php
        $result2 = mysqli_query($conn, "select * from fruit_size") or die(mysqli_error($conn));
        while ($row = mysqli_fetch_array($result2)) {
        ?>
            <option value="<?php echo $row['s_id']; ?>"> <?php echo $row['size']; ?></option>
        <?php
        }
        ?>
    </select>
    <br>
    FRUIT Taste:<select name="fruit_taste" id="fruit_taste">
        <option value="">Select Type</option>
        <?php
        $result3 = mysqli_query($conn, "select * from fruit_taste") or die(mysqli_error($conn));
        while ($row = mysqli_fetch_array($result3)) {
        ?>
            <option value="<?php echo $row['ta_id']; ?>"> <?php echo $row['Taste']; ?></option>
        <?php
        }
        ?>
    </select>
    <br>
    FRUIT Type:<select name="fruit_type" id="fruit_type">
        <option value="">Select Type</option>
        <?php
        $result4 = mysqli_query($conn, "select * from fruit_type") or die(mysqli_error($conn));
        while ($row = mysqli_fetch_array($result4)) {
        ?>
            <option value="<?php echo $row['t_id']; ?>"> <?php echo $row['type']; ?></option>
        <?php
        }
        ?>
    </select>
    <br>
    Cost: <input type="number" name="cost" id="cost" value="">
    <br>

    Price:<input type="number" name="price" id="price" value=""><br>
    Image:
    <input type="file" name="fruit_image" id="fruit_image"
        accept="image/jpeg,image/png,image/gif">
    ID:<input type="text" name="p_id" id="p_id" value=""><br>
    <input type="submit" name="save" id="save" value="SAVE"><br>
    <input type="submit" name="get" id="get" value="GET"><br>
    <table border="1">
        <tr>
            <th>S.no</th>
            <th>Fruit</th>
            <th>color</th>
            <th>Size</th>
            <th>Taste</th>
            <th>Type</th>
            <th>Cost</th>
            <th>Price</th>
            <th>Image</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        <?php
        if (isset($_POST['get'])) {
            $result = mysqli_query($conn, "select * from pricing") or die(mysqli_error($conn));
            while ($row = mysqli_fetch_array($result)) {
                $f_id = $row['f_id'];
                $c_id = $row['c_id'];
                $s_id = $row['s_id'];
                $ta_id = $row['ta_id'];
                $t_id = $row['t_id'];
                $fruit_name_result = mysqli_query($conn,
                    "SELECT f_name FROM fruits WHERE f_id='$f_id'"
                ) or die(mysqli_error($conn));

                $color_name_result = mysqli_query($conn,
                    "SELECT color FROM fruit_color WHERE c_id='$c_id'"
                ) or die(mysqli_error($conn));

                $size_name_result = mysqli_query($conn,
                    "SELECT size FROM fruit_size WHERE s_id='$s_id'"
                ) or die(mysqli_error($conn));

                $taste_name_result = mysqli_query($conn,    
                    "SELECT Taste FROM fruit_taste WHERE ta_id='$ta_id'"
                ) or die(mysqli_error($conn));

                $type_name_result = mysqli_query($conn,
                    "SELECT type FROM fruit_type WHERE t_id='$t_id'"
                ) or die(mysqli_error($conn));

                $fruit_name = mysqli_fetch_assoc($fruit_name_result);
                $color_name = mysqli_fetch_assoc($color_name_result);
                $size_name = mysqli_fetch_assoc($size_name_result);
                $taste_name = mysqli_fetch_assoc($taste_name_result);
                $type_name = mysqli_fetch_assoc($type_name_result);
        ?>
                <tr>
                    <td> <?php echo $row['p_id']; ?></td>
                    <td><?php echo $fruit_name['f_name']; ?></td>
                    <td><?php echo $color_name['color']; ?></td>
                    <td><?php echo $size_name['size']; ?></td>
                    <td><?php echo $taste_name['Taste']; ?></td>
                    <td><?php echo $type_name['type']; ?></td>
                    <td><?php echo $row['cost']; ?></td>
                    <td><?php echo $row['price']; ?></td>
                    <td>
                        <?php if (!empty($row['img_url'])) { ?>
                            <img src="<?php echo htmlspecialchars($row['img_url']); ?>"
                                alt="Fruit image"
                                width="100"
                                height="100">
                        <?php } else { ?>
                            No image
                        <?php } ?>
                    </td>
                    <td><?php if ($row['status'] == '1') {
                            echo "Available";
                        } else {
                            echo "Unavailable";
                        } ?>
                    </td>
                    <td><input type="button" onclick="priceupdate('<?php echo $row['p_id']; ?>','<?php echo $row['f_id']; ?>','<?php echo $row['c_id']; ?>','<?php echo $row['s_id']; ?>','<?php echo $row['ta_id']; ?>','<?php echo $row['t_id']; ?>','<?php echo $row['cost']; ?>','<?php echo $row['price']; ?>')" name="edit" id="edit" value="Edit"></td>
                    </td>
                </tr>
        <?php
            }
        }
        ?>

    </table>
</form>