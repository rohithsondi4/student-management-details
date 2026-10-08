<?php

require_once "config/config.php";

if (isset($_POST['save'])) {

    $p_id = $_POST['p_id'];
    $fruit = $_POST['fruits'];
    $color = $_POST['fruit_color'];
    $size = $_POST['fruit_size'];
    $taste = $_POST['fruit_taste'];
    $type = $_POST['fruit_type'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];
    $total = $price * $quantity;
    $img_url = $_POST['img_url'];

    if ($p_id != "" && $quantity != "") {
        $qryl = mysqli_query(
            $conn,
            "INSERT INTO `transaction`
            (p_id, quantity, price, total)
            VALUES
            ('$p_id', '$quantity', '$price', '$total')"
        ) or die(mysqli_error($conn));

        echo "The transaction has been successfully Saved.";

    } else {

        echo "Please select all fruit properties and enter quantity.";

    }
}

?>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script>

function getPriceAndImage() {

    var fruit = $('#fruits').val();
    var color = $('#fruit_color').val();
    var size = $('#fruit_size').val();
    var taste = $('#fruit_taste').val();
    var type = $('#fruit_type').val();

    if (!fruit || !color || !size || !taste || !type) {

        $('#p_id').val('');
        $('#price').val('');
        $('#img_url').val('');
        $('#quantity').val('');
        $('#total').val('');

        $('#fruit_image_preview')
            .hide()
            .attr('src', '');

        return;
    }

    $.ajax({

        url: 'ajax/get_price_image.php',

        type: 'POST',

        dataType: 'json',

        data: {

            f_id: fruit,
            c_id: color,
            s_id: size,
            ta_id: taste,
            t_id: type

        },

        success: function(response) {

            console.log(response);

            if (response.status == 1) {

                $('#p_id').val(response.p_id);

                $('#price').val(response.price);

                $('#img_url').val(response.img_url);

                if (response.img_url != '') {

                    $('#fruit_image_preview')
                        .attr('src', response.img_url)
                        .show();

                } else {

                    $('#fruit_image_preview')
                        .hide()
                        .attr('src', '');

                }

                $('#quantity').val('');

                $('#total').val('');

            } else {

                $('#p_id').val('');
                $('#price').val('');
                $('#img_url').val('');
                $('#quantity').val('');
                $('#total').val('');

                $('#fruit_image_preview')
                    .hide()
                    .attr('src', '');

                alert('Price and image not found.');

            }

        },

        error: function(xhr) {

            console.log(xhr.responseText);

        }

    });

}

function gettotalprice() {

    var price = parseFloat($('#price').val());
    var quantity = parseFloat($('#quantity').val());

    if (!isNaN(price) && !isNaN(quantity)) {

        $('#total').val(price * quantity);

    } else {

        $('#total').val('');

    }

}

function priceupdate(
    p_id,
    f_id,
    c_id,
    s_id,
    ta_id,
    t_id,
    price
) {

    $('#p_id').val(p_id);
    $('#fruits').val(f_id);
    $('#fruit_color').val(c_id);
    $('#fruit_size').val(s_id);
    $('#fruit_taste').val(ta_id);
    $('#fruit_type').val(t_id);
    $('#price').val(price);
    $('#quantity').val('');
    $('#total').val('');

    getPriceAndImage();

}

</script>

<form method="POST">

FRUIT Name:

<select
    name="fruits"
    id="fruits"
    onchange="getPriceAndImage()"
>

    <option value="">Select Fruits</option>

    <?php

    $result = mysqli_query($conn, "SELECT * FROM fruits") or die(mysqli_error($conn));

    while ($row = mysqli_fetch_array($result)) {

    ?>

        <option value="<?php echo $row['f_id']; ?>">
            <?php echo $row['f_name']; ?>
        </option>

    <?php

    }

    ?>

</select>

<br>

FRUIT Color:

<select
    name="fruit_color"
    id="fruit_color"
    onchange="getPriceAndImage()"
>

    <option value="">Select Color</option>

    <?php

    $result1 = mysqli_query($conn, "SELECT * FROM fruit_color") or die(mysqli_error($conn));

    while ($row = mysqli_fetch_array($result1)) {

    ?>

        <option value="<?php echo $row['c_id']; ?>">
            <?php echo $row['color']; ?>
        </option>

    <?php

    }

    ?>

</select>

<br>

FRUIT Size:

<select
    name="fruit_size"
    id="fruit_size"
    onchange="getPriceAndImage()"
>

    <option value="">Select Size</option>

    <?php

    $result2 = mysqli_query($conn, "SELECT * FROM fruit_size") or die(mysqli_error($conn));

    while ($row = mysqli_fetch_array($result2)) {

    ?>

        <option value="<?php echo $row['s_id']; ?>">
            <?php echo $row['size']; ?>
        </option>

    <?php

    }

    ?>

</select>

<br>

FRUIT Taste:

<select
    name="fruit_taste"
    id="fruit_taste"
    onchange="getPriceAndImage()"
>

    <option value="">Select Taste</option>

    <?php

    $result3 = mysqli_query($conn, "SELECT * FROM fruit_taste") or die(mysqli_error($conn));

    while ($row = mysqli_fetch_array($result3)) {

    ?>

        <option value="<?php echo $row['ta_id']; ?>">
            <?php echo $row['Taste']; ?>
        </option>

    <?php

    }

    ?>

</select>

<br>

FRUIT Type:

<select
    name="fruit_type"
    id="fruit_type"
    onchange="getPriceAndImage()"
>

    <option value="">Select Type</option>

    <?php

    $result4 = mysqli_query($conn, "SELECT * FROM fruit_type") or die(mysqli_error($conn));
    while ($row = mysqli_fetch_array($result4)) {

    ?>

        <option value="<?php echo $row['t_id']; ?>">
            <?php echo $row['type']; ?>
        </option>

    <?php

    }

    ?>

</select>

<br>

Price:

<input
    type="number"
    name="price"
    id="price"
    value=""
    readonly
>

<br>

<img
    id="fruit_image_preview"
    src=""
    width="100"
    height="100"
    style="display:none;"
>

<br>

<input
    type="hidden"
    name="img_url"
    id="img_url"
>

Quantity:

<input
    type="number"
    name="quantity"
    id="quantity"
    value=""
    min="1"
    oninput="gettotalprice()"
>

<br>

Total:

<input
    type="number"
    name="total"
    id="total"
    value=""
    readonly
>

<br>

ID:

<input
    type="text"
    name="p_id"
    id="p_id"
    value=""
    readonly
>

<br>

<input
    type="submit"
    name="save"
    id="save"
    value="SAVE"
>

<br>

<input
    type="submit"
    name="get"
    id="get"
    value="GET"
>

<br>

<table border="1">

<tr>

    <th>S.no</th>
    <th>Fruit</th>
    <th>Color</th>
    <th>Size</th>
    <th>Taste</th>
    <th>Type</th>
    <th>Price</th>
    <th>Image</th>
    <th>Status</th>
    <th>Action</th>

</tr>

<?php

if (isset($_POST['get'])) {

    $result = mysqli_query($conn, "SELECT * FROM pricing") or die(mysqli_error($conn));
    while ($row = mysqli_fetch_array($result)) {

        $f_id = $row['f_id'];
        $c_id = $row['c_id'];
        $s_id = $row['s_id'];
        $ta_id = $row['ta_id'];
        $t_id = $row['t_id'];

        $fruit_name_result = mysqli_query($conn, "SELECT f_name
             FROM fruits
             WHERE f_id='$f_id'"
        ) or die(mysqli_error($conn));

        $color_name_result = mysqli_query($conn, "SELECT color
             FROM fruit_color
             WHERE c_id='$c_id'"
        ) or die(mysqli_error($conn));

        $size_name_result = mysqli_query($conn, "SELECT size
             FROM fruit_size
             WHERE s_id='$s_id'"
        ) or die(mysqli_error($conn));

        $taste_name_result = mysqli_query($conn, "SELECT Taste
             FROM fruit_taste
             WHERE ta_id='$ta_id'"
        ) or die(mysqli_error($conn));

        $type_name_result = mysqli_query($conn, "SELECT type
             FROM fruit_type
             WHERE t_id='$t_id'"
        ) or die(mysqli_error($conn));

        $fruit_name = mysqli_fetch_assoc($fruit_name_result);
        $color_name = mysqli_fetch_assoc($color_name_result);
        $size_name = mysqli_fetch_assoc($size_name_result);
        $taste_name = mysqli_fetch_assoc($taste_name_result);
        $type_name = mysqli_fetch_assoc($type_name_result);

?>

<tr>

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
        <?php echo $row['price']; ?>
    </td>

    <td>

        <?php

        if (!empty($row['img_url'])) {

        ?>

            <img
                src="<?php echo htmlspecialchars($row['img_url']); ?>"
                width="100"
                height="100"
            >

        <?php

        } else {

            echo "No image";

        }

        ?>

    </td>

    <td>

        <?php

        if (isset($row['status']) && $row['status'] == '1') {

            echo "Available";

        } else {

            echo "Unavailable";

        }

        ?>

    </td>

    <td>

        <input
            type="button"
            name="edit"
            value="Edit"
            onclick="priceupdate(
                '<?php echo $row['p_id']; ?>',
                '<?php echo $row['f_id']; ?>',
                '<?php echo $row['c_id']; ?>',
                '<?php echo $row['s_id']; ?>',
                '<?php echo $row['ta_id']; ?>',
                '<?php echo $row['t_id']; ?>',
                '<?php echo $row['price']; ?>'
            )"
        >

    </td>

</tr>

<?php

    }

}

?>

</table>

</form>