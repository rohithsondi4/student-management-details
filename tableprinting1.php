<form method="POST">
    Table number:
    <input type="number" name="num1" id="num1" value="">
    <input type="submit" name="submit" id="submit" value="Calculate">
</form>
<table border="1px">
<?php
function tableprinting($a){
    $i=1;
    while($i<=20){
        echo"<tr><td>". "$a X $i" . "=" .($a*$i)."</td></tr>";
        $i++;
    }
}
if(isset($_POST['submit'])){
    $a=$_POST['num1'];
    while($a<=15){
       return tableprinting($a);
        $a++;   
    }
}
?>
</table>