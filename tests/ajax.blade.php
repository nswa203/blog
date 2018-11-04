{{-- Ajax file upload example --}}

<script>
	function _(el) {
		return document.getElementById(el);
	}

	function uploadFile() {
		var file=_('file1').files[0];
		alert(file.name+' | '+files.size+' | '+file.type); 
		var formdata=new FormData();
		formdata.append('file1', file);
		var ajax=newXMLHttpRequest();
		ajax.upload.addEventListener('progress', progressHandler, false);	
		ajax.addEventListener('load',  completeHandler, false);	
		ajax.addEventListener('error', errorHandler,    false);	
		ajax.addEventListener('abort', abortHandler,    false);	
		ajax.open('POST', 'file_upload_parser.php');								// URL / Route
		ajax.send(formData);
	}

	function progressHandler(event) {
		_('loaded_n_total').innerHTML='Uploaded '+event.loaded+' bytes of '+event.total;
		var percent=(event.loaded / event.total) * 100;
		_('progressBar').value=Math.round(percent);
		_('status').innerHTML=Math.round(percent)+'% uploaded... please wait.';
	}

	function completeHandler(event) {
		_('status').innerHTML=event.target.responseText;
		_('progressBar').value=0;
	}

	function errorHandler(event) {
		_('status').innerHTML='Upload Failed!';
	}

	function abortHandler(event) {
		_('status').innerHTML='Upload Aborted!';
	}
</script>

<h2>HTML5 File Upload Progress Bar Tutorial</h2>
<form enctype="multipart/form-data" method="POST">
	<input type="file" name="file1" id="file1"><br>
	<input type="button" value="Upload File" onClick="uploadFile()">
	<progress id="progressBar" value="0" max="100" style="width:300px;"></progress>
	<h3 id="status"></h3>
	<p id="loaded_n_total"></p>
</form>

<?php 
// This is the file_upload_parser.php server file referenced in the ajax.open()
$fileName 	  = $_FILES['file1']['name']; 
$fileTmpLoc   = $_FILES['file1']['tmp_file']; 
$fileType 	  = $_FILES['file1']['type']; 
$fileSize 	  = $_FILES['file1']['size']; 
$fileErrorMsg = $_FILES['file1']['error']; 
if (!$fileTmpLoc) {
	echo 'ERROR: Please browse for a file before clicking the upload button.';
	exit();
}
if (move_uploaded_file($fileTmpLoc, "test_uploads/$fileName")) {
	echo "SUCCESS: $fileName upload is complete.";
} else {
	echo 'ERROR: move_uploaded_file function failed!'; 
}
