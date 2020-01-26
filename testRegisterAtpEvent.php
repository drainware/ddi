<html>
<body>
<form action="http://192.168.23.250/ddi/index.php?module=api&action=registerAtpEvents" method="post" enctype="multipart/form-data">
<label for="datetime">Date:</label>
<input type="text" name="datetime" id="datetime" /> 
<br />
<label for="ip">IP:</label>
<input type="text" name="ip" id="ip" /> 
<br />
<label for="user">User:</label>
<input type="text" name="user" id="user" /> 
<br />
<label for="variables">Variables:</label>
<input type="text" name="variables" id="variables" /> 
<br />
<label for="addfile">File:</label>
<input type="file" name="addfile" id="addfile" /> 
<br />
<input type="submit" name="submit" value="Submit" />
</form>

</body>
</html>

<?
//phpinfo();
?>
