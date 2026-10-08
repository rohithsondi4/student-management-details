<!DOCTYPE html>
<html>
<head>
    <title>Arithmetic Operations</title>
</head>
<body>
<form method="POST">
    First number: 
    <input type="number" name="num1" id="num1" value="">
    Second number:
    <input type="number" name="num2" id="num2" value="">
    operation:
    <select name="num3" id="num3">
        <option value="Add"> add</option>
        <option value="Sub"> sub</option>
        <option value="Multi"> multi</option>
        <option value="Division"> div</option>
    </select>
    <input type="submit" name="submit" value="calculate">
</form>
<?php
 if(isset($_POST['submit'])){
    $a=$_POST['num1'];
    $b=$_POST['num2'];
    $operation=$_POST['num3'];
    if($operation=='Add'){
        $c=$a+$b;
        echo $c;
    }
    elseif($operation=='Sub'){
        $x=$a-$b;
        echo $x;
    }
    elseif($operation=='Multi'){
        $y=$a*$b;
        echo $y;
    }
    elseif($operation=='Division'){
        $z=$a/$b;
        echo $z;
    }
    else{
        echo "not possible";
    }
 }
 ?>
 </body>
 </html>