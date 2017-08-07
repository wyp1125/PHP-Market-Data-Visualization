<?php
$tick=strtoupper($_POST["tick"]);
if($tick=="")
{
#echo "yes";
header("Location: http://haoboluo.com/new.php");
exit;
}
$days=$_GET[days];
$correct=$_GET[correct];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title>Analysis</title>
</head>
<script type="text/javascript">
function clearDefault(a){if(a.defaultValue==a.value){a.value=""}};
</script>
<body>
<table width="1000" height="800" cellspacing="0" cellpadding="10" align="left">
<tr><td width="1000" height="800">
<?php
echo "<form id='form1' name='form2' method='post' action='new2.php?days=".$days."&correct=".$correct."'>";
echo "<p style='font-size:60px'>Ticker:"; 
echo "<input name='tick' type='text' value='".$tick."' onFocus='clearDefault(this)' style='width:200px; height:60px;font-size:40px' />";
echo "&nbsp;<input type=submit value='Submit' name='submit1' style='font-size:40px'>";
echo  "</p></form><hr />";
echo "<img src='new/nplot2.php?tick=".$tick."&days=".$days."' width=980 height=600/>";
echo "<img src='plot.php?tick=".$tick."&days=".$days."&correct=".$correct."' width=980 height=600/>";
//echo "<img src='https://www.google.com/finance/getchart?q=".$tick."&p=30d&i=900' width=980 height=600/>";
?>
  </td></tr>
</table>  
</body>
</html>
