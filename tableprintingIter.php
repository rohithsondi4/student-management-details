<form method="POST">
    Table number:
    <input type="number" name="num1" id="num1" value="">
    <input type="submit" name="submit" id="submit" value="Calculate">
</form>
<table border="1px">
<?php
if(isset($_POST['submit'])){
    $a=$_POST['num1'];
    $t=1;
    while($t<=$a){
    $i=0;
    while($i<=20){
        echo"<tr><td>". "$t X $i" . "=" .($t*$i)."</td></tr>";
        $i++;
    }
    $t++;
}
}
?>
</table>