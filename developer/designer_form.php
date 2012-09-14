<html>
<head>
    <title>Javascript Append Div Content Dynamically</title>
   
    <style type="text/css">

    .dynamicDiv {
    width:400px;
    background-color:#e1e1e1;
    font-size:11px;
    font-family:verdana;
    color:#000;


    }

    </style>
   
    <script type="text/javascript" language="javascript"> 

fields = 1;

		function createIn()
        {

			
            var inTag = document.createElement("input");
			inTag.type = "text";
			inTag.name = "text_"+fields;
			var br=document.createElement('br');
			document.getElementById("fields_div").appendChild(br);
			var inColor = document.createElement("input");
			inColor.type = "text";
			inColor.name = "color_"+fields;
            document.getElementById("fields_div").appendChild(inTag);
			document.getElementById("fields_div").appendChild(inColor);

			fields += 1;
			document.getElementById('fields_num').value=fields;
        }
 
    </script>

</head>
<body onLoad="javascript:createIn();">
 

<form id="form1" name="form1" method="post" action="designer.php">
<table width="0" cellspacing="0" cellpadding="0">

	<tr>
		<td>Template Name(small) : </td>
		<td><input name="class_name" type="text" /></td>
	</tr>

  <tr>
    <td>Colors: </td>
    <td>
	<div id="fields_div" class="dynamicDiv" align="center">	</div>	</td>
  </tr>
</table>
<br>
<input type="button" onClick="createIn()" name="add" value="Add input field" />
<p>
	

    <input type="hidden"  name="fields_num" id="fields_num" value=""/>
  <br/>
    <input type="submit" name="button" id="button" value="Generate Template" />

</p>
</form>
</body>
</html>