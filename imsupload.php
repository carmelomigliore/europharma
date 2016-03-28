<form enctype="multipart/form-data" action="index.php?section=imsupload&action=upload" method="POST">
  <input type="hidden" name="MAX_FILE_SIZE" value="30000">
  Invia questo file: <input name="userfile" type="file"></br>
  <input type="submit" value="Invia File">
</form>
<?php
include('db.php');
$action=$_GET['action'];
if($action==upload){

	$query = $db->prepare("SELECT id FROM prodotti WHERE nome = :nome");
	$query2 = $db->prepare("INSERT INTO ims VALUES(:annomese, :idarea, :idprodotto, :numeropezzi)");
	$userfile_tmp = $_FILES['userfile']['tmp_name'];
	$file = file_get_contents($userfile_tmp);
	$lines = explode("\r\n",$file);
	foreach($lines as $line){
		$values = explode(";",$line);
		if(count($values) < 5)
			continue;
		$query->execute(array(':nome' => $values[2]));
		$idprodotto = $query->fetch();
		if(!is_numeric($idprodotto))
			continue;
		$query2->execute(array(':annomese' => $values[0], ':idarea' => $values[1], ':idprodotto' => $idprodotto, ':numeropezzi' => $values[3]));
	}

	
}

?>
