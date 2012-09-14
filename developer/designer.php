<textarea cols='100' rows='100'>
<?php

$fields_num 	= $_REQUEST['fields_num'];
$class_name		= $_REQUEST['class_name'];


$all_names=array();
$all_colors=array();

	for ($i=1;$i<$fields_num;$i++){
	$all_names[]=$_REQUEST['text_'.$i];
	$all_colors[]=$_REQUEST['color_'.$i];
	}

define("APP_PATH",$_SERVER['DOCUMENT_ROOT']."/developer");

generateTemplate();

function generateTemplate(){
	
global $class_name,$all_names,$all_colors;

chdir(APP_PATH);

$file = fopen($class_name.".color.html", "w");
$fo = "<body>\n";

	for($i=0;$i < sizeof($all_names);$i++){
	
	$color=$all_colors[$i];
	
    if($color[0] == '#')
      $color = substr($color, 1);
        
$rgb=html2rgb($color);

$setget=<<<HERE


<b>Color Name :</b> $all_names[$i] <br/><b>html hex:</b> #$color <br/><b>R G B:</b> $rgb[0]-$rgb[1]-$rgb[2]
<div style="background-color:#$color;width:75px;height:50px;" >&nbsp;</div><hr/>


HERE;

	$fo.= $setget;

	}
	// end foreach
$fo.= "\n";

echo $fo."\n <br/></body>";
fwrite($file, $fo);
fclose($file);
}

function html2rgb($color)
{

    if (strlen($color) == 6)
        list($r, $g, $b) = array($color[0].$color[1],
                                 $color[2].$color[3],
                                 $color[4].$color[5]);
    elseif (strlen($color) == 3)
        list($r, $g, $b) = array($color[0].$color[0], $color[1].$color[1], $color[2].$color[2]);
    else
        return false;

    $r = hexdec($r); $g = hexdec($g); $b = hexdec($b);

    return array($r, $g, $b);
}

?>
</textarea>