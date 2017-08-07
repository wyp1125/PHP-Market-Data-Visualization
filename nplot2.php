<?php
header("Content-type: image/png");
############################################
$ticker=$_GET[tick];
$days=$_GET[days];
$n=0;
while($n<10*$days)
{
$content = file_get_contents("http://www.google.com/finance/getprices?q=$ticker&i=1800&p=".$days."d&f=d,c,h,l,o,v");
$line=explode("\n",$content);
$n=count($line);
}
$m=0;
$max_p=0;
$min_p=100000;
$max_v=0;
$show_min_p=100000;
$cur_min_p=100000;
$show_tm=0;
for($i=0;$i<$n;$i++)
{
if(preg_match('/\,\d+/',$line[$i]))
{
$c=explode(",",$line[$i]);
if(strpos($c[0],'a'))
{
$c[0]=0;
}
if($c[2]>$max_p)
{$max_p=$c[2];}
if($c[3]<$min_p)
{$min_p=$c[3];}
//if($c[5]>$max_v)
//{$max_v=$c[5];}
if($c[0]!=$show_tm+1)
{
if($cur_min_p<$show_min_p)
{
$show_min_p=$cur_min_p;
}
$cur_min_p=100000;
}
if($c[3]<$cur_min_p)
{$cur_min_p=$c[3];}
$show_tm=$c[0];
$tm[$m]=$c[0];
$o_price[$m]=$c[4];
$c_price[$m]=$c[1];
$h_price[$m]=$c[2];
$l_price[$m]=$c[3];
//$vol[$m]=$c[5];
$m++;
}
}
$diff=$max_p-$min_p;
$a=array(0.05,0.1,0.5,1,5,10,50,100);
for($i=0;$i<7;$i++)
{
if($diff/$a[$i]<=20)
{
$unit=$a[$i];
break;
}
}
$l_bound=floor($min_p/$unit)*$unit;
$u_bound=ceil($max_p/$unit)*$unit;
$ch=($c_price[$m-1]-$c_price[$m-2])/$c_price[$m-2];
$ch=round(1000*$ch)/10;
#################rsi########################
$period=15;
$gain[0]=0;
$loss[0]=0;
$s_gain[0]=0;
$s_loss[0]=0;
for($i=1;$i<$m;$i++)
{
$ch1=$c_price[$i]-$c_price[$i-1];
if($ch1>=0)
{
$gain[$i]=$ch1;
$loss[$i]=0;
}
else
{
$gain[$i]=0;
$loss[$i]=-$ch1;
}
if($i<$preriod)
{
$s_gain[$i]=$s_gain[$i-1]+$gain[$i];
$s_loss[$i]=$s_loss[$i-1]+$loss[$i];
}
else
{
$s_gain[$i]=$s_gain[$i-1]+$gain[$i]-$gain[$i-$period];
$s_loss[$i]=$s_loss[$i-1]+$loss[$i]-$loss[$i-$period];
}
if($s_loss[$i]==0)
{
$rsi[$i]=100;
}
else
{
$rsi[$i]=100-100/(1+$s_gain[$i]/$s_loss[$i]);
}
}
$rsi_trend="";
for($i=$m-10;$i<$m;$i++)
{
if($rsi[$i-1]<=$rsi[$i])
{
$rsi_trend=$rsi_trend."+";
}
else
{
$rsi_trend=$rsi_trend."-";
}
}
############################################
$width = 600;
$height = 300;
$height2 = 100;
$height3 =0;
$margin = 50;
$im = imagecreatetruecolor($width, $height+$height2+$height3);
$red = imagecolorallocate($im, 255, 0, 0);
$gray = imagecolorallocate($im, 220, 220, 220);
$gray2 = imagecolorallocate($im, 160, 160, 160);
$green = imagecolorallocate($im, 0, 160, 0);
$blue = imagecolorallocate($im, 0, 0, 255);
$black = imagecolorallocate($im, 0, 0, 0);
$white = imagecolorallocate($im, 255, 255, 255);
imagefill($im,0,0,$white);
imageline($im,$margin/2,$margin,$margin/2,$height-$margin+$height2+$height3,$black);
imageline($im,$margin/2,$margin,$width-1.5*$margin,$margin,$black);
imageline($im,$width-1.5*$margin,$margin,$width-1.5*$margin,$height-$margin+$height2+$height3,$black);
imageline($im,$margin/2,$height-$margin,$width-1.5*$margin,$height-$margin,$black);
imageline($im,$margin/2,$height-$margin+$height2,$width-1.5*$margin,$height-$margin+$height2,$black);
#imageline($im,$margin/2,$height-$margin+$height2+$height3,$width-1.5*$margin,$height-$margin+$height2+$height3,$black);

imagestring($im,5,$width/4-50,$margin/2,$c_price[$m-1],$blue);
imagestring($im,5,$width/4+50,$margin/2,$show_min_p,$red);
imagestring($im,5,3*$width/4-100,$margin/2,"RSI: ".round($rsi[$m-1]),$black);
imagestring($im,5,3*$width/4,$margin/2,$rsi_trend,$blue);
#if($ch>=0)
#imagestring($im,5,$width/3+170,$margin/2,"$ch %",$green);
#else
#imagestring($im,5,$width/3+170,$margin/2,"$ch %",$red);
$n_grid=($u_bound-$l_bound)/$unit;
for($i=1;$i<$n_grid;$i++)
{
imageline($im,$margin/2,$margin+$i*($height-2*$margin)/$n_grid,$width-1.5*$margin,$margin+$i*($height-2*$margin)/$n_grid,$gray);
imagestring($im,5,$width-1.5*$margin+5,$margin+$i*($height-2*$margin)/$n_grid-10,$u_bound-$i*$unit,$black);
}
for($i=1;$i<10;$i++)
{
$val=10*(10-$i);
imageline($im,$margin/2,$height-$margin+$i*$height2/10,$width-1.5*$margin,$height-$margin+$i*$height2/10,$gray);
imagestring($im,3,$width-1.5*$margin+5,$height-$margin+$i*$height2/10-10,$val,$gray2);
}
$c_width=($width-2*$margin)/$m;
for($i=1;$i<$m;$i++)
{
if($c_price[$i]<=$o_price[$i])
{
imagefilledrectangle($im,$margin/2+$i*$c_width,$margin+($u_bound-$h_price[$i])*($height-2*$margin)/($u_bound-$l_bound),$margin/2+$i*$c_width,$margin+($u_bound-$o_price[$i])*($height-2*$margin)/($u_bound-$l_bound),$red);
imagefilledrectangle($im,$margin/2+$i*$c_width,$margin+($u_bound-$c_price[$i])*($height-2*$margin)/($u_bound-$l_bound),$margin/2+$i*$c_width,$margin+($u_bound-$l_price[$i])*($height-2*$margin)/($u_bound-$l_bound),$red);
imagefilledrectangle($im,$margin/2+$i*$c_width-1.5,$margin+($u_bound-$o_price[$i])*($height-2*$margin)/($u_bound-$l_bound),$margin/2+$i*$c_width+2,$margin+($u_bound-$c_price[$i])*($height-2*$margin)/($u_bound-$l_bound),$red);
}
else
{
imagefilledrectangle($im,$margin/2+$i*$c_width,$margin+($u_bound-$h_price[$i])*($height-2*$margin)/($u_bound-$l_bound),$margin/2+$i*$c_width,$margin+($u_bound-$c_price[$i])*($height-2*$margin)/($u_bound-$l_bound),$green);
imagefilledrectangle($im,$margin/2+$i*$c_width,$margin+($u_bound-$o_price[$i])*($height-2*$margin)/($u_bound-$l_bound),$margin/2+$i*$c_width,$margin+($u_bound-$l_price[$i])*($height-2*$margin)/($u_bound-$l_bound),$green);
imagefilledrectangle($im,$margin/2+$i*$c_width-2,$margin+($u_bound-$c_price[$i])*($height-2*$margin)/($u_bound-$l_bound),$margin/2+$i*$c_width+2,$margin+($u_bound-$o_price[$i])*($height-2*$margin)/($u_bound-$l_bound),$green);
}
if($i>1&&$tm[$i]!=$tm[$i-1]+1&&$i<$m-1)
{
imageline($im,$margin/2+$i*$c_width-4,$margin,$margin/2+$i*$c_width-4,$height-$margin+$height2+$height3,$gray);
}
if($i>$period)
{
imageline($im,$margin/2+($i-1)*$c_width,$height-$margin+(100-$rsi[$i-1])*$height2/100,$margin/2+$i*$c_width,$height-$margin+(100-$rsi[$i])*$height2/100,$blue);
}
}
imageline($im,$margin/2,$margin+($u_bound-$c_price[$m-1])*($height-2*$margin)/($u_bound-$l_bound),$width-1.5*$margin,$margin+($u_bound-$c_price[$m-1])*($height-2*$margin)/($u_bound-$l_bound),$blue);
imageline($im,$margin/2,$margin+($u_bound-$show_min_p)*($height-2*$margin)/($u_bound-$l_bound),$width-1.5*$margin,$margin+($u_bound-$show_min_p)*($height-2*$margin)/($u_bound-$l_bound),$red);
imagegif($im);
imagedestroy($im);
?>

