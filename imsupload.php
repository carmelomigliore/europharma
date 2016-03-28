<?php
include('db.php');
$action=$_GET['action'];
if($action=='upload'){
	
	$query = $db->prepare("SELECT id FROM prodotti WHERE nome = :nome");
	$query2 = $db->prepare("INSERT INTO ims(annomese, idprodotto, numeropezzi, idarea) VALUES(:annomese, :idprodotto, :numeropezzi, :idarea)");
	$userfile_tmp = $_FILES['userfile']['tmp_name'];
	$file = file_get_contents($userfile_tmp);
	$lines = explode("\r\n",$file);
	foreach($lines as $line){
		try{
			//echo($line.'<br>');
			$values = explode(";",$line);
			if(count($values) < 5)
				continue;
			//echo($values[2].'<br>');
			$query->execute(array(':nome' => $values[2]));
			$idprodotto = $query->fetch();
			$idprodotto = $idprodotto[0];
			if(!is_numeric($idprodotto)){
				echo('Il prodotto '.$values[2].' è stato skippato perché non presente nel database');
				continue;
			}
			$query2->execute(array(':annomese' => $values[0], ':idarea' => $values[1], ':idprodotto' => $idprodotto, ':numeropezzi' => $values[3]));
		}catch(Exception $pdoe){
			echo('Errore: '.$pdoe->getMessage());
		}
	}
	
} else{
	echo('<form enctype="multipart/form-data" action="index.php?section=caricodati&action=upload" method="POST">
  <input type="hidden" name="MAX_FILE_SIZE" value="30000">
  Invia questo file: <input name="userfile" type="file"></br>
  <input type="submit" value="Invia File">
</form>');
}

?>
