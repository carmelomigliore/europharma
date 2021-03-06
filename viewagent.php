<script type="text/javascript">
    function showhide(id) {
    	var mydiv = document.getElementById("product"+id);
	if(mydiv.style.display=="none"){
		mydiv.style.display = "block";
		document.getElementById("showhide"+id).firstChild.data = "Nascondi";
	}else{
		mydiv.style.display = "none";
		document.getElementById("showhide"+id).firstChild.data = "Mostra";
	}
    }
</script>
<?php
include_once('db.php');
include_once('agent.php');
$id=$_GET['id'];
/*$query=$db->prepare('SELECT * FROM agenti WHERE id = :id');
$query->execute(array(':id' => $id));
$row = $query->fetch(PDO::FETCH_ASSOC);*/
$agente = Agent::getAgentFromDB($id,$db);

echo('<div class="caricodati" align="center" style="width:80%;"><div class="title">
		<br>	<h1><p>Collaboratore: '.$agente->nome.' '.$agente->cognome.'</p></h1>
		</div>');

echo('<table width="60%" id="tablevew"><tr>');
echo('<td><p><strong>Codice fiscale:</strong> '.$agente->codicefiscale.'</p></td>');
echo('<td class="celldataview"><p><strong>e-mail:</strong> '.$agente->email.'</p></td>');
echo('</tr><tr>');
echo('<td><p><strong>Partita IVA:</strong> '.$agente->partitaiva.'</p></td>');
echo('<td class="celldataview"><p><strong>e-mail alternativa:</strong> '.$agente->email2.'</p></td>');
echo('</tr><tr>');
echo('<td><p><strong>Indirizzo: </strong>'.$agente->indirizzo.'</p></td>');
echo('<td class="celldataview"><p><strong>CAP: </strong> '.$agente->cap.'</p></td>');
echo('</tr><tr>');
echo('<td><p><strong>Città:</strong> '.$agente->citta.'</p></td>');
echo('<td class="celldataview"><p><strong>Provincia: </strong>'.$agente->provincia.'</p></td>');
echo('</tr><tr>');
echo('<td><p><strong>Telefono:</strong> '.$agente->tel.'</p></td>');
echo('<td class="celldataview"><p><strong>Tipo di contratto:</strong> '.$agente->tipocontratto.'</p></td>');
echo('</tr><tr>');
echo('<td><p><strong>Tipo attività:</strong> '.$agente->tipoattivita.'</p></td>');
echo('<td class="celldataview"><p><strong>Data inizio contratto:</strong> '.$agente->datainizio.'</p></td>');
echo('</tr><tr>');
echo('<td><p><strong>Data fine contratto:</strong> '.$agente->datafine.'</p></td>');
echo('<td class="celldataview"><p><strong>Data periodo prova:</strong> '.$agente->dataperiodoprova.'</p></td>');
echo('</tr><tr>');
echo('<td><p><strong>% IVA:</strong> '.$agente->iva.'</p></td>');
echo('<td class="celldataview"><p><strong>% Enasarco: </strong>'.$agente->enasarco.'</p></td>');
echo('</tr><tr>');
echo('<td><p><strong>% Ritenuta d\'acconto:</strong> '.$agente->ritacconto.'</p></td>');
echo('<td class="celldataview"><p><strong>% Contributo previdenziale: </strong>'.$agente->contributoinps.'</p></td>');
echo('</tr><tr>');
echo('<td><p><strong>% Rivalsa INPS:</strong> '.$agente->rivalsainps.'</p></td>');
echo('<td class="celldataview"><p><strong>Note:</strong> '.$agente->note.'</p></td>');
echo('</tr>');
echo('</table>');
echo('</div>');

echo('<a href="index.php?section=fattura&action=spinner&id='.$id.'">Genera Fattura</a>'.'<br>');
echo('<a href="index.php?section=statistiche&action=spinner&id='.$id.'">Statistiche mensili</a>'.'<br>');

/* SEZIONE AREE ASSEGNATE ALL'AGENTE*/

echo('<p align="center">Aree assegnate all\'agente</p>');
$query=$db->prepare('SELECT DISTINCT nome FROM aree, "agente-aree" AS aa WHERE aree.codice = aa.area AND aa.idagente = :id');
$query->execute(array(':id' => $id));
$result = $query->fetchAll(PDO::FETCH_ASSOC);
echo('<table border="1" align="center"><tr><th>Zona</th><th>Microaree</th></tr>');
foreach($result as $row){
	$query=$db->prepare('SELECT codice FROM aree, "agente-aree" AS aa WHERE aree.codice = aa.area AND aa.idagente = :id AND aree.nome = :nome');
	$query->execute(array(':id' => $id, ':nome' => $row['nome']));
	$subresult= $query->fetchAll(PDO::FETCH_ASSOC);
	echo('<tr><td>'.$row['nome'].'</td>');
	echo('<td>');
	$idx=0;
	foreach($subresult as $microarea){
		echo(substr($microarea['codice'],3).'       <a href="index.php?section=addagentarea&action=deletemicro&id='.$id.'&microarea='.$microarea['codice'].'" onclick="return confirm(\'Vuoi confermare questa operazione?\')">[X]</a>'.(++$idx%10!=0?', ':'<br>'));
	}
	echo('</td></tr>');
}
echo('</table>');
echo('<a href="index.php?section=addagentarea&action=selectprovince&id='.$id.'">Aggiungi area</a>');

/* SEZIONE PRODOTTI ASSEGNATI ALL'AGENTE*/


echo('<p align="center">Prodotti assegnati all\'agente</p>  <a href="index.php?section=addagentproduct&action=selectproduct&id='.$id.'">Assegna nuovo prodotto all\'agente</a>');
$query=$db->prepare('SELECT count(*) as num FROM "agente-prodotto" WHERE idagente = :idagente');
$query->execute(array(':idagente' => $id));
$productcount = $query->fetch(PDO::FETCH_ASSOC);
$productcount = $productcount['num'];
if($productcount == 0){
	echo('<br><br><a href="index.php?section=addagentproduct&action=addallproducts&id='.$id.'" onclick="return confirm(\'Vuoi confermare questa operazione?\')" >Assegna tutti i prodotti all\'agente</a>');
}
$index=0;
$query=$db->prepare('SELECT prodotti.id, prodotti.nome, provvigione, ap.id as idagenteprodotto FROM prodotti, "agente-prodotto" AS ap WHERE idagente = :idagente AND prodotti.id = ap.codprodotto ORDER BY prodotti.nome'); // seleziona i prodotti
$query->execute(array(':idagente' => $id));
$products = $query->fetchAll(PDO::FETCH_ASSOC);

foreach($products as $prod){
	$class = $index%2==0?"producteven":"productodd";
	echo('<div class="'.$class.'"><p align="center">'.$prod['nome'].'        <a nohref id="showhide'.$prod['id'].'" onclick="showhide('.$prod['id'].')">Mostra</a>'.'</p>');			 
	echo('<div class="'.$class.'" id="product'.$prod['id'].'" style="display:none;"><a href="index.php?section=addagentproduct&action=deleteproduct&id='.$id.'&product='.$prod['id'].'" onclick="return confirm(\'Vuoi confermare questa operazione?\')">Elimina prodotto</a>');
echo('<p align="center">Provvigione: '.$prod['provvigione'].'</p> <a href="index.php?section=addagentproduct&action=viewprovvigione&provvigione='.$prod['provvigione'].'&id='.$id.'&product='.$prod['id'].'">Modifica Provvigione</a>');
	$query=$db->prepare('SELECT DISTINCT nome FROM aree, "agente-aree" AS aa, "agente-prodotto" AS ap, "agente-prodotto-area" AS apa WHERE aree.codice = aa.area AND aa.idagente = :idagente AND ap.codprodotto = :codprodotto AND apa.idagentearea = aa.id AND apa.idagenteprodotto = ap.id');   //Seleziona le provincie assegnate all'agente per un determinato prodotto'
	$query->execute(array(':idagente' => $id, ':codprodotto' => $prod['id']));
	$result = $query->fetchAll(PDO::FETCH_ASSOC);
	echo('<table width="100%"><tr>');  //tabella esterna solo per allineamento
	echo('<td class="celldata" ><div class="tabledata"><table border="1" width="40%"><tr>         <a href="index.php?section=addagentproduct&action=addareaproduct&id='.$id.'&product='.$prod['id'].'">Aggiungi Zona</a></tr><tr><th>Zona</th><th>Microaree</th></tr>');  //tabella aree
	foreach($result as $row){
		$query=$db->prepare('SELECT codice, apa.id FROM aree, "agente-aree" AS aa, "agente-prodotto" AS ap, "agente-prodotto-area" AS apa WHERE aree.codice = aa.area AND aa.idagente = :idagente AND ap.codprodotto = :codprodotto AND apa.idagentearea = aa.id AND apa.idagenteprodotto = ap.id AND aree.nome = :nome');  //Seleziona le microaree dentro la provincia
		$query->execute(array(':idagente' => $id, ':nome' => $row['nome'], ':codprodotto' => $prod['id']));
		$subresult= $query->fetchAll(PDO::FETCH_ASSOC);
		echo('<tr><td>'.$row['nome'].'</td>');
		echo('<td>');
		$idx = 0;
		foreach($subresult as $microarea){    
			echo(substr($microarea['codice'],3).'       <a href="index.php?section=addagentproduct&action=deleteareaproduct&id='.$id.'&idareaproduct='.$microarea['id'].'" onclick="return confirm(\'Vuoi confermare questa operazione?\')">[X]</a>'.(++$idx%5!=0?', ':'<br>'));
		}
		echo('</td></div></tr>');
	}
	echo('</table></td>');
	
	$query=$db->prepare('SELECT target, percentuale, array_to_json(apt.idagprodotti) as productlist, apt.id FROM "agente-prodotto" AS ap, "agente-prodotto-target" AS apt WHERE ap.codprodotto = :codprodotto AND ap.idagente = :idagente AND apt.idagprodotti @> ARRAY[ap.id]::bigint[] ORDER BY target');  //Seleziona gli eventuali target/bonus relativi all'agente per un determinato prodotto'
	$query->execute(array(':idagente' => $id, ':codprodotto' => $prod['id']));
	$targets=$query->fetchAll(PDO::FETCH_ASSOC);
	echo('<td class="celldata"><div class="tabledata"><a href="index.php?section=addagentproducttarget&action=settarget&id='.$id.'&idagenteprodotto='.$prod['idagenteprodotto'].'">Aggiungi target per questo prodotto</a><table border="1"><tr><th>Target</th><th>Percentuale</th><th>Note</th><th></th></tr>'); //tabella target
	if(count($targets)>0){
		foreach($targets as $targ){
			$buddyproducts = json_decode($targ['productlist']);
			$note= '';
			if(count($buddyproducts)>1){
				$note= 'Target somma con ';
				$buddyproducts = array_diff($buddyproducts, array($prod['idagenteprodotto']));
				foreach($buddyproducts as $buddyproduct){
					$buddyquery = $db->prepare('SELECT prodotti.nome FROM prodotti, "agente-prodotto" AS ap WHERE ap.id = :idagenteprodotto AND ap.codprodotto = prodotti.id');
					$buddyquery->execute(array(':idagenteprodotto' => $buddyproduct));
					$buddyresult = $buddyquery->fetch();
					$note = $note.$buddyresult[0].' ';
				}
			}
			echo('<tr><td>'.$targ['target'].'</td><td>'.$targ['percentuale'].'</td><td>'.$note.'</td><td><a href="index.php?section=addagentproducttarget&action=deletetarget&id='.$id.'&idtarget='.$targ['id'].'" onclick="return confirm(\'Vuoi confermare questa operazione?\')">[X]</a></td>');
		}
		
	}
	else{
		echo('<tr><td>/</td><td>/</td></tr>');
	}
	echo('</table></div></td>');
	echo('</tr></table></div></div>');
	$index++;
}


?>
