<form method="POST">
    Table number:
    <input type="number" name="num1" id="num1" value="">
    <input type="number" name="num2" id="num2" value="">
    <input type="submit" name="submit" id="submit" value="Calculate">
</form>
<table border="1px">
<?php
// While loop upto which number should be multiplied
function tableprinting($t){
    $i=1;
    $b=$_POST['num2'];
    while($i<=$b){
        echo"<tr><td>". "$t X $i" . "=" .($t*$i)."</td></tr>";
        $i++;
    }
}
// loop upto given number
if(isset($_POST['submit'])){
    $a=$_POST['num1'];
    $t=1;
    while($t<=$a){
     tableprinting($t);
        $t++;
        
    }
}
?>
</table>