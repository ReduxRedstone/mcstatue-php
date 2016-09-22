<html>
<head>
	<title></title>
  <link rel="stylesheet" type="text/css" href="files/css/main.css">
</head>
<body>
	<form id="uploadimage" action="generate.php" method="post" enctype="multipart/form-data">
	    <div id="image_preview"><img id="previewing"></div>
	    <hr id="line">
	    <div id="selectImage">
		    <label>Select Your Image</label><br>
        <input type="file" name="file" id="file" required><br>
        <input placeholder="username" type="username" name="username" id="username" required><br>
		    <input placeholder="password" type="password" name="password" id="password" required><br>
		    <input type="submit" value="Create Statue" class="submit">
	    </div>
  	</form>
  	<div id="img-data"></div>
  	<script src="files/js/jquery-2.1.4.min.js"></script>
    <script src="http://malsup.github.com/jquery.form.js"></script> 
    <script> 
        $(document).ready(function() { 
            $('#uploadimage').ajaxForm(function(data) {
                $('#img-data').html(data); 
            }); 
        }); 
    </script> 
  	<script src="files/js/main.js"></script>
</body>
</html>