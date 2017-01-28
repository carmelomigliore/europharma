<?php
include_once('util.php');
require_once('vsword/VsWord.php');
class Agent {
	public $id;
	public $nome;
	public $cognome;
	public $codicefiscale;
	public $partitaiva;
	public $email;
	public $iva;
	public $enasarco;
	public $ritacconto;
	public $contributoinps;
	public $rivalsainps;

	public $tipocontratto;  //in realtà è il tipo di regime, il nome resta tipocontratto per compatibilità
	public $tipoattivita ;
	public $datainizio ;
	public $datafine;
	public $dataperiodoprova;
	public $tel;
	public $indirizzo;
	public $cap;
	public $citta;
	public $provincia;
	public $note;
	public $attivo;
	public $email2;
	public static $path='E:\Shares\Pubblica\Agenti\Provvigioni\AAAACOMPENSI_NUOVO_2016'; //da cambiare quando si installa sulla macchina europharma

	public function Agent($record, $myid = NULL, $mynome = NULL, $mycognome = NULL, $mycodicefiscale = NULL, $mypartitaiva = NULL, $myemail = NULL, $myiva = NULL, $myenasarco = NULL, $myritacconto = NULL, $mycontributoinps = NULL, $myrivalsainps = NULL, $mytipocontratto = NULL, $mytipoattivita = NULL, $mydatainizio = NULL, $mydatafine = NULL, $mydataperiodoprova = NULL, $mytel = NULL, $myindirizzo = NULL, $mynote = NULL, $mycap = NULL, $mycitta = NULL, $myprovincia = NULL, $myattivo = NULL, $myemail2 = NULL){
		if( $record != NULL){
			$this->nome = $record['nome'];
			$this->cognome = $record['cognome'];
			$this->codicefiscale = $record['codicefiscale'];
			$this->partitaiva = $record['partitaiva'];
			$this->email = $record['email'];
			$this->iva = $record['iva'];
			$this->enasarco = $record['enasarco'];
			$this->ritacconto = $record['ritacconto'];
			$this->contributoinps = $record['contributoinps'];
			$this->rivalsainps = $record['rivalsainps'];
			$this->id = $record['id'];

			$this->tipocontratto = $record['tipocontratto'];
			$this->tipoattivita = $record['tipoattivita'];
			$this->datainizio = $record['datainiziocontratto'];
			$this->datafine = $record['datafinecontratto'];
			$this->dataperiodoprova = $record['dataperiodoprova'];
			$this->tel = $record['telefono'];
			$this->indirizzo = $record['indirizzo'];
			$this->cap = $record['cap'];
			$this->citta = $record['citta'];
			$this->provincia = $record['provincia'];
			$this->note = $record['note'];
			$this->attivo = $record['attivo'];
			$this->email2 = $record['email2'];
		}else{
			$this->nome = $mynome;
			$this->cognome = $mycognome;
			$this->codicefiscale = $mycodicefiscale;
			$this->partitaiva = $mypartitaiva;
			$this->email = $myemail;
			$this->iva = $myiva;
			$this->enasarco = $myenasarco;
			$this->ritacconto = $myritacconto;
			$this->contributoinps = $mycontributoinps;
			$this->rivalsainps = $myrivalsainps;
			$this->id = $myid;

			$this->tipocontratto = $mytipocontratto;
			$this->tipoattivita = $mytipoattivita;
			$this->datainizio = $mydatainizio;
			$this->datafine = $mydatafine;
			$this->dataperiodoprova = $mydataperiodoprova;
			$this->tel = $mytel;
			$this->indirizzo = $myindirizzo;
			$this->cap = $mycap;
			$this->citta = $mycitta;
			$this->provincia = $myprovincia;
			$this->note = $mynote;
			$this->attivo = $myattivo;
			$this->email2 = $myemail2;
		}
	}
	
	public function insertInDB($db){
		$query = $db->prepare("INSERT INTO agenti(nome, cognome, codicefiscale, partitaiva, email, iva, enasarco, ritacconto, contributoinps, rivalsainps, indirizzo, telefono, tipocontratto, datainiziocontratto, datafinecontratto, dataperiodoprova, tipoattivita, note, citta, cap, provincia, attivo, email2) VALUES (:nome, :cognome, :codicefiscale, :partitaiva, :email, :iva, :enasarco, :ritacconto, :contributoinps, :rivalsainps, :indirizzo, :telefono, :tipocontratto, :datainiziocontratto, :datafinecontratto, :dataperiodoprova, :tipoattivita, :note, :citta, :cap, :provincia, :attivo, :email2) RETURNING id");
		$query->execute(array(':nome' => $this->nome, ':cognome' => $this->cognome, ':codicefiscale' => $this->codicefiscale, ':partitaiva' => $this->partitaiva, ':email' => $this->email, ':iva' => $this->iva, ':enasarco' => $this->enasarco, ':ritacconto' => $this->ritacconto, ':contributoinps' => $this->contributoinps, ':rivalsainps' => $this->rivalsainps, ':indirizzo' => $this->indirizzo, ':telefono' => $this->tel, ':tipocontratto' => $this->tipocontratto, ':datainiziocontratto' => $this->datainizio, ':datafinecontratto' => strlen($this->datafine) > 0 ? $this->datafine : null, ':dataperiodoprova' => strlen($this->dataperiodoprova) > 0 ? $this->dataperiodoprova : null, ':tipoattivita' => $this->tipoattivita, ':note' => $this->note, ':citta' => $this->citta, ':cap' => $this->cap, ':provincia' => $this->provincia, ':attivo' => $this->attivo, ':email2' => $this->email2));
		$result = $query->fetch(PDO::FETCH_ASSOC);
		$this->id = $result['id'];
	}
	
	public function updateInDB($db){
	$query=$db->prepare('UPDATE agenti SET nome = :nome, cognome = :cognome, codicefiscale = :codicefiscale, partitaiva = :partitaiva, email = :email, iva = :iva, enasarco = :enasarco, ritacconto = :ritacconto, contributoinps = :contributoinps, rivalsainps = :rivalsainps, indirizzo = :indirizzo, telefono = :telefono, tipocontratto = :tipocontratto, datainiziocontratto = :datainiziocontratto, datafinecontratto = :datafinecontratto, dataperiodoprova = :dataperiodoprova, tipoattivita = :tipoattivita, note = :note, citta = :citta, cap = :cap, provincia = :provincia, attivo = :attivo, email2= :email2 WHERE id = :id');
	$query->execute(array(':nome' => $this->nome, ':cognome' => $this->cognome, ':codicefiscale' => $this->codicefiscale, ':partitaiva' => $this->partitaiva, ':email' => $this->email, ':iva' => $this->iva, ':ritacconto' => $this->ritacconto, ':rivalsainps' => $this->rivalsainps, ':enasarco' => $this->enasarco, ':contributoinps' => $this->contributoinps, ':indirizzo' => $this->indirizzo, ':telefono' => $this->tel, ':tipocontratto' => $this->tipocontratto, ':datainiziocontratto' => $this->datainizio, ':datafinecontratto' => strlen($this->datafine) > 0 ? $this->datafine : null, ':dataperiodoprova' => strlen($this->dataperiodoprova) > 0 ? $this->dataperiodoprova : null, ':tipoattivita' => $this->tipoattivita, ':note' => $this->note, ':id' => $this->id, ':citta' => $this->citta, ':cap' => $this->cap, ':provincia' => $this->provincia, ':attivo' => $this->attivo, ':email2' => $this->email2));
	}
	
	public function assignProduct($db, $idproduct, $provvigione){
		$db->beginTransaction();
		$query = $db->prepare('INSERT INTO "agente-prodotto"(idagente, codprodotto, provvigione) VALUES (:idagente, :codprodotto, :provvigione) RETURNING id');
		$query->execute(array(':idagente' => $this->id, ':codprodotto' => $idproduct, ':provvigione' => $provvigione));
		$idagenteprodotto = $query->fetch();
		$idagenteprodotto = $idagenteprodotto[0];
		
		$query = $db->prepare('SELECT target1, target2, target3, percentuale1, percentuale2, percentuale3 FROM prodotti WHERE id = :idprodotto');
		$query->execute(array(':idprodotto' => $idproduct));
		$result = $query->fetch(PDO::FETCH_ASSOC);
		
		/*$query= $db->prepare('SELECT id FROM "agente-prodotto" WHERE codprodotto = :idprodotto AND idagente = :idagente');
		$query->execute(array(':idprodotto' => $idproduct, ':idagente' => $this->id));
		$idagenteprodotto*/
		
		//echo($idagenteprodotto);
		
		if($result['target1'] != 0){
			$query = $db->prepare('SELECT insertarget(:products, :target, :percentuale)');	
			$query->execute(array(':products' => php_to_postgres_array(array($idagenteprodotto)), ':target' => $result['target1'], ':percentuale' => $result['percentuale1']));
		}
		
		if($result['target2'] != 0){
			$query = $db->prepare('SELECT insertarget(:products, :target, :percentuale)');	
			$query->execute(array(':products' => php_to_postgres_array(array($idagenteprodotto)), ':target' => $result['target2'], ':percentuale' => $result['percentuale2']));
		}
		
		if($result['target3'] != 0){
			$query = $db->prepare('SELECT insertarget(:products, :target, :percentuale)');	
			$query->execute(array(':products' => php_to_postgres_array(array($idagenteprodotto)), ':target' => $result['target3'], ':percentuale' => $result['percentuale3']));
		}
		$db->commit();
	}

	public function updateProvvigioneProduct($db, $idproduct, $provvigione){
	$query = $db->prepare('UPDATE "agente-prodotto" SET provvigione = :provvigione WHERE idagente = :idagente AND codprodotto = :codprodotto ');
		$query->execute(array(':idagente' => $this->id, ':codprodotto' => $idproduct, ':provvigione' => $provvigione));
	}
	
	
	public function deleteProduct($db, $idproduct){
		$query = $db->prepare('DELETE from "agente-prodotto" WHERE idagente = :idagente AND codprodotto = :idprodotto');
		$query->execute(array(':idprodotto' => $idproduct, ':idagente' => $this->id));
	}
	
	
	public function assignArea($db, $codarea){
		$query = $db->prepare('INSERT INTO "agente-aree"(area, idagente) VALUES (:codarea, :idagente)');
		$query->execute(array(':codarea' => $codarea, ':idagente' => $this->id));
	}

	public function deleteProductArea($db, $idagenteprodottoarea){
		$query = $db->prepare('DELETE from "agente-prodotto-area" WHERE id = :idagenteprodottoarea');
		$query->execute(array(':idagenteprodottoarea' => $idagenteprodottoarea));
	}
	
	public function deleteArea($db, $codarea){
		$query = $db->prepare('DELETE from "agente-aree" WHERE area = :idarea AND idagente = :idagente');
		$query->execute(array(':idarea' => $codarea, ':idagente' => $this->id));
	}
	
	public function assignProductArea($db, $codprodotto, $codarea){
		$query = $db->prepare('SELECT id FROM "agente-aree" WHERE area = :codarea AND idagente = :idagente');
		$query->execute(array(':codarea' => $codarea, ':idagente' => $this->id));
		$resultagarea = $query->fetch(PDO::FETCH_ASSOC);
		$query = $db->prepare('SELECT id FROM "agente-prodotto" WHERE codprodotto = :codprodotto AND idagente = :idagente');
		$query->execute(array(':codprodotto' => $codprodotto, ':idagente' => $this->id));
		$resultagprod = $query->fetch(PDO::FETCH_ASSOC);
		$query = $db->prepare('SELECT insertagenteprodottoarea(:idagprod, :idagarea, :codprodotto, :codarea)');
		$query->execute(array(':idagprod' => $resultagprod['id'], ':idagarea' => $resultagarea['id'], ':codprodotto' => $codprodotto, ':codarea' => $codarea));
	}
	
	public function calculateIMS($db, $annomese, $textpositivo=null, $valuepositivo=null, $textnegativo=null, $valuenegativo=null){
		$query = $db->prepare('SELECT importolordo FROM "monthly-results-agente-importolordo" WHERE annomese = :annomese AND idagente = :idagente');
		$query->execute(array(':annomese' => $annomese, ':idagente' => $this->id));
		$result = $query->fetch(PDO::FETCH_ASSOC);
		$imponibile = $result['importolordo'];
		$this->calculateNettoPrintFattura($db, $imponibile, 'ims', $annomese, $textpositivo, $valuepositivo, $textnegativo, $valuenegativo);
	}
	
	public function calculateCompensoCapo($db, $annomese, $textpositivo=null, $valuepositivo=null, $textnegativo=null, $valuenegativo=null){
		$query = $db->prepare('SELECT spettanza AS importolordo FROM "capiarea-spettanza" WHERE annomese = :annomese AND idagente = :idagente');
		$query->execute(array(':annomese' => $annomese, ':idagente' => $this->id));
		$result = $query->fetch(PDO::FETCH_ASSOC);
		$imponibile = $result['importolordo'];
		$this->calculateNettoPrintFattura($db, $imponibile, 'capoarea', $annomese , $textpositivo, $valuepositivo, $textnegativo, $valuenegativo);
	}
	
	public function calculateCompensoDirettoreItalia($db, $annomese, $textpositivo=null, $valuepositivo=null, $textnegativo=null, $valuenegativo=null){
		$query = $db->prepare('SELECT sum(spettanza) AS fatturato FROM "direttore-ims-spettanza" WHERE annomese = :annomese');
		$query->execute(array(':annomese' => $annomese));
		$result = $query->fetch(PDO::FETCH_ASSOC);
		$fatturato = $result['fatturato'];
		$query = $db->prepare('SELECT sum(spettanza) AS fatturato FROM "direttore-farmacie-spettanza" WHERE annomese = :annomese');
		$query->execute(array(':annomese' => $annomese));
		$result = $query->fetch(PDO::FETCH_ASSOC);
		$fatturato += $result['fatturato'];
		//$percentualedirettore = 0.03;
		$imponibile = $fatturato ;  //* $percentualedirettore;
		$this->calculateNettoPrintFattura($db, $imponibile, 'direttorereteitalia', $annomese, $textpositivo, $valuepositivo, $textnegativo, $valuenegativo);
	}
	
	public function calculateFarmacia($db, $annomese, $numerofattura){
		$query = $db->prepare('SELECT idprodotto, numeropezzi, provvigione, prezzonetto FROM "compensi-farmacie" WHERE annomese = :annomese AND idagente = :idagente AND numerofattura = :numerofattura');
		$query->execute(array(':annomese' => $annomese, ':idagente' => $this->id, ':numerofattura' => $numerofattura));
		$result = $query->fetchAll(PDO::FETCH_ASSOC);
		$imponibile = 0;
		foreach ($result as $row){
			$provvigione = $row['provvigione'];
			$prezzonetto = $row['prezzonetto'];
			if(is_null($provvigione)){
				$query = $db->prepare('SELECT provvigione FROM "agente-prodotto" WHERE idagente = :idagente AND codprodotto = :idprodotto');
				$query->execute(array(':idprodotto' => $row['idprodotto'], ':idagente' => $this->id));
				$temp = $query->fetch();
				$provvigione = $temp[0];
				
				$query = $db->prepare('SELECT prezzo - prezzo*sconto/100 AS prezzonetto FROM prodotti WHERE id = :idprodotto');
				$query->execute(array(':idprodotto' => $row['idprodotto']));
				$temp = $query->fetch();
				$prezzonetto = $temp[0];
			}
			$imponibile+= ($prezzonetto * $provvigione / 100)*$row['numeropezzi'];
		}
		//$this->calculateNettoPrintFattura($imponibile, 'farmacia'.$numerofattura, $annomese);
		return $imponibile;
	}
	
	public function calculateFarmaciaCapo($db, $annomese, $numerofattura){
		$query = $db->prepare('SELECT idprodotto, numeropezzi, percentualecapo, prezzonetto FROM "compensi-farmacie", prodotti WHERE annomese = :annomese AND numerofattura = :numerofattura AND "compensi-farmacie".idprodotto = prodotti.id');
		$query->execute(array(':annomese' => $annomese, ':numerofattura' => $numerofattura));
		$result = $query->fetchAll(PDO::FETCH_ASSOC);
		
		
		
		$imponibile = 0;
		foreach ($result as $row){
			$provvigione = $row['percentualecapo'];
			$prezzonetto = $row['prezzonetto'];
			if(is_null($prezzonetto)){
				$query = $db->prepare('SELECT prezzo - prezzo*sconto/100 AS prezzonetto FROM prodotti WHERE id = :idprodotto');
				$query->execute(array(':idprodotto' => $row['idprodotto']));
				$temp = $query->fetch();
				$prezzonetto = $temp[0];
			}
			$imponibile+= ($prezzonetto * $provvigione / 100)*$row['numeropezzi'];
			
		}
		return $imponibile;
	}
	
	public function saveFarmaciaCapo($db, $annomese, $numerofattura){
		$querystoricocapoarea = $db->prepare('INSERT INTO "storico-capiarea-farmacie" VALUES(:mesecorrente, :idagente, :numerofattura, :annomesefattura, :percentuale, :idprodotto, :prezzonetto)');
		$query = $db->prepare('SELECT idprodotto, numeropezzi, percentualecapo, prezzonetto FROM "compensi-farmacie", prodotti WHERE annomese = :annomese AND numerofattura = :numerofattura AND "compensi-farmacie".idprodotto = prodotti.id');
		$query->execute(array(':annomese' => $annomese, ':numerofattura' => $numerofattura));
		$result = $query->fetchAll(PDO::FETCH_ASSOC);	
		//$imponibile = 0;
		foreach ($result as $row){
			$provvigione = $row['percentualecapo'];
			$prezzonetto = $row['prezzonetto'];
			if(is_null($prezzonetto)){
				$query = $db->prepare('SELECT prezzo - prezzo*sconto/100 AS prezzonetto FROM prodotti WHERE id = :idprodotto');
				$query->execute(array(':idprodotto' => $row['idprodotto']));
				$temp = $query->fetch();
				$prezzonetto = $temp[0];
			}
			//$imponibile+= ($prezzonetto * $provvigione / 100)*$row['numeropezzi'];
			$querystoricocapoarea->execute(array(':mesecorrente' => date('Y').date('m'), ':idagente' => $this->id, ':numerofattura' => $numerofattura, ':annomesefattura' => $annomese, ':percentuale'=> $provvigione, ':idprodotto' => $row['idprodotto'], ':prezzonetto' => $prezzonetto));
		}
	}
	
	public function calculateImponibileFarmacieMeseCapo($db, $annomese){
		$query=$db->prepare('SELECT SUM(prezzonetto*numeropezzi*percentuale/100) as imponibile FROM "storico-capiarea-farmacie" AS scf, farmacie as f WHERE scf.annomesefattura = f.annomese AND scf.numerofattura = f.numerofattura AND scf.idprodotto = f.idprodotto AND scf.annomese = :annomese AND scf.idagente = :idagente');
		$query->execute(array(':annomese' => $annomese, ':idagente' => $this->id));
		$temp = $query->fetch();
		$imponibile = $temp[0];
		return $imponibile;
	}
	
	public function statsPivot($db, $annomese, &$columns){
		$query = $db->prepare("select pivotcode('vista_crosstab','microarea','nome','max(numeropezzi)','integer', :idagente, :annomese)");
		$query->execute(array(':idagente' => $this->id, ':annomese' => $annomese));
		$qresults = $query->fetch();
		$query = $db->prepare($qresults[0]);
		$query->execute();
		$results = $query->fetchAll(PDO::FETCH_ASSOC);
		
		$rs = $db->query('WITH myquery AS ('.str_replace(';', '', $qresults[0]).') SELECT * FROM myquery LIMIT 0');
		for ($i = 0; $i < $rs->columnCount(); $i++) {
		    $col = $rs->getColumnMeta($i);
		    $columns[] = $col['name'];
		}
		$func = function($value) use ( &$func ){
			if(is_array($value))
				return array_map($func, $value);
			else
				return is_null($value)?"-":$value;
		};
		return array_map($func, $results);
	}
	
	public function statsNormal($db, $annomese){
		$query = $db->prepare('SELECT prodotti.nome, aree.nome || "substring"(aree.codice::text, 4, 2) as area, numeropezzi, replace(to_char(provvigione, \'FM999999999.00\'),\'.\',\',\'), replace(to_char(storico.prezzonetto*numeropezzi, \'FM999999999.00\'),\'.\',\',\') as prezzonetto, replace(to_char(prezzonetto*(provvigione/100)*numeropezzi, \'FM999999999.00\'),\'.\',\',\') as spettanza FROM storico, aree, prodotti WHERE idagente = :idagente AND annomese = :annomese AND storico.idprodotto = prodotti.id AND storico.codarea = aree.codice ORDER BY prodotti.nome,area,codice');
		$query->execute(array(':idagente' => $this->id, ':annomese' => $annomese));
		return $query->fetchAll(PDO::FETCH_ASSOC);
	}
	
	public function statsNormalSum($db, $annomese){
		$query = $db->prepare('SELECT prodotti.nome, sum(numeropezzi), replace(to_char(sum(prezzonetto*(provvigione/100)*numeropezzi), \'FM999999999.00\'),\'.\',\',\') as spettanza FROM storico, aree, prodotti WHERE idagente = :idagente AND annomese = :annomese AND storico.idprodotto = prodotti.id AND storico.codarea = aree.codice GROUP BY prodotti.nome ORDER BY prodotti.nome');
		$query->execute(array(':idagente' => $this->id, ':annomese' => $annomese));
		return $query->fetchAll(PDO::FETCH_ASSOC);
	}
	
	public function statsNormalCapo($db, $annomese){
		$query = $db->prepare('SELECT prodotti.nome, aree.nome || "substring"(aree.codice::text, 4, 2) as area, numeropezzi, replace(to_char(provvigione, \'FM999999999.00\'),\'.\',\',\'), replace(to_char(storico.prezzonetto*numeropezzi, \'FM999999999.00\'),\'.\',\',\') as prezzonetto, replace(to_char(prezzonetto*(provvigione/100)*numeropezzi, \'FM999999999.00\'),\'.\',\',\') as spettanza FROM "storico-capiarea" AS storico, aree, prodotti WHERE idagente = :idagente AND annomese = :annomese AND storico.idprodotto = prodotti.id AND storico.codarea = aree.codice ORDER BY prodotti.nome,area,codice');
		$query->execute(array(':idagente' => $this->id, ':annomese' => $annomese));
		return $query->fetchAll(PDO::FETCH_ASSOC);
	}
	
	public function statsNormalCapoSum($db, $annomese){
		$query = $db->prepare('SELECT prodotti.nome, sum(numeropezzi), replace(to_char(sum(prezzonetto*(provvigione/100)*numeropezzi), \'FM999999999.00\'),\'.\',\',\') as spettanza FROM "storico-capiarea" AS storico, aree, prodotti WHERE idagente = :idagente AND annomese = :annomese AND storico.idprodotto = prodotti.id AND storico.codarea = aree.codice GROUP BY prodotti.nome ORDER BY prodotti.nome');
		$query->execute(array(':idagente' => $this->id, ':annomese' => $annomese));
		return $query->fetchAll(PDO::FETCH_ASSOC);
	}
	
	public function generateCSV($array, $headers, $tipo, $annomese){
		if (!file_exists(self::$path.'/'.$this->cognome.'_'.$this->nome.'_'.$this->codicefiscale)) {
		    mkdir(self::$path.'/'.$this->cognome.'_'.$this->nome.'_'.$this->codicefiscale, 0777, true);
		}
		$fp = fopen(self::$path.'/'.$this->cognome.'_'.$this->nome.'_'.$this->codicefiscale.'/'.$annomese.$tipo.$this->cognome.$this->nome.'.csv', 'w');
		
		fputcsv($fp, $headers, ';');
		foreach ($array as $fields) {
		    fputcsv($fp, $fields, ';');
		}

		fclose($fp);
	}
	
	public function calculateBonusSeiMesi($db, $annomese, $nettofatturatolimite, &$nettofatturatoreturn){
		$annomeseend = $annomese;
		if($annomese[4] == '0' && $annomese[5] == '1'){ //Intervallo gennaio-giugno
			$annomeseend[5] = '6';
		}else if($annomese[4] == '0' && $annomese[5] == '7'){ //luglio-dicembre
			$annomeseend[4] = '1';
			$annomeseend[5] = '2';
		}
		
		$arraymesi = getMesiIntervallo($annomese, $annomeseend);
		
		
		try{
			$query = $db->prepare('SELECT SUM(prezzonetto*numeropezzi) FROM "storico" WHERE idagente = :idagente AND annomese = ANY (:arraymesi ::varchar[]) GROUP BY idagente');
			$query->execute(array(':idagente' => $this->id, ':arraymesi' => '{'.php_to_postgres_array($arraymesi).'}'));
			$nettofatturatoims = $query->fetch();
			
			$query = $db->prepare('SELECT SUM(prezzonetto*numeropezzi) FROM "compensi-farmacie" WHERE idagente = :idagente AND annomese = ANY (:arraymesi ::varchar[]) GROUP BY idagente'); //TODO liquidato o annomese?
			$query->execute(array(':idagente' => $this->id, ':arraymesi' => '{'.php_to_postgres_array($arraymesi).'}'));
			$nettofatturatofarmacie = $query->fetch();
		
		//print_r($arraymesi);
		//echo("nettofatturato: \n");
		//echo($nettofatturatoims[0] + $nettofatturatofarmacie[0]);
		$nettofatturatoreturn = $nettofatturatoims[0] + $nettofatturatofarmacie[0];
		if(($nettofatturatoims[0] + $nettofatturatofarmacie[0])/6 >= $nettofatturatolimite){

			$queryprodotti = $db->prepare('SELECT codprodotto FROM "agente-prodotto" WHERE idagente = :idagente');
			$queryprodotti->execute(array(':idagente' => $this->id));
			$prodotti = $queryprodotti->fetchAll(PDO::FETCH_ASSOC);
			$bonus = 0;
			foreach($prodotti as $prodotto){
				$querybonus = $db->prepare('SELECT provvigionebonus FROM prodotti WHERE id = :idprodotto');
				$querybonus->execute(array(':idprodotto' => $prodotto['codprodotto']));
				$provvigionebonus = $querybonus->fetch();
				$provvigionebonus = $provvigionebonus[0];
				//echo('provvigionebonus: '.$provvigionebonus);
				foreach($arraymesi as $myannomese){
					$queryprovvigione =  $querybonus = $db->prepare('SELECT provvigione FROM "storico" WHERE annomese = :annomese AND idagente = :idagente AND idprodotto = :idprodotto');
					$queryprovvigione->execute(array(':idagente' => $this->id, ':annomese' => $myannomese, ':idprodotto' => $prodotto['codprodotto']));
					$provvigioneold = $queryprovvigione->fetch();
					$provvigioneold = $provvigioneold[0];
					//echo('provvigioneold: '.$provvigioneold);
					if($provvigionebonus > $provvigioneold){ //se la provvigione "vecchia" supera quella bonus (e.g. perché quel mese era stato raggiunto un target) allora non fare nulla
						$querynettofatturatomeseprodotto = $db->prepare('SELECT SUM(prezzonetto*numeropezzi) FROM "storico" WHERE idagente = :idagente AND annomese = :annomese AND idprodotto= :idprodotto GROUP BY idagente');
						$querynettofatturatomeseprodotto->execute(array(':idagente' => $this->id, ':annomese' => $myannomese, ':idprodotto' => $prodotto['codprodotto']));
						$nettofatturatomeseprodotto = $querynettofatturatomeseprodotto->fetch();
						$nettofatturatomeseprodotto = $nettofatturatomeseprodotto[0];
						
						$querynettofatturatomeseprodottofarmacie = $db->prepare('SELECT SUM(prezzonetto*numeropezzi) FROM "compensi-farmacie" WHERE idagente = :idagente AND annomese = :annomese AND idprodotto = :idprodotto GROUP BY idagente'); //TODO annomese o liquidato?
						$querynettofatturatomeseprodottofarmacie->execute(array(':idagente' => $this->id, ':annomese' => $myannomese, ':idprodotto' => $prodotto['codprodotto']));
						$nettofatturatomeseprodottofarmacie = $querynettofatturatomeseprodottofarmacie->fetch();
						$nettofatturatomeseprodottofarmacie = $nettofatturatomeseprodottofarmacie[0];
						
						$bonus+= ($nettofatturatomeseprodotto + $nettofatturatomeseprodottofarmacie)*$provvigionebonus/100 -  ($nettofatturatomeseprodotto + $nettofatturatomeseprodottofarmacie)*$provvigioneold/100;  //differenza tra spettanza calcolata con la provvigione bonus e la spettanza calcolata con la provvigione "normale" 
					}
				}
			}
			//echo('bonus: '.$bonus);
			return $bonus;
		} else {   //nel caso in cui la media mensile di nettofatturato è inferiore a quella limite, niente bonus
			//echo('vuoto\n');
			return 0;
		}
		} catch(Exception $e){
			echo $e->getMessage().' Line: '.$e->getLine();
		}
	}
	
	public function getImponibilePassatoEnasarco($db, $annomese){
		$newannomese = $annomese;
		$newannomese[4] = '0';
		$newannomese[5] = '1';
		
		$codicefiscaledacercare = $this->codicefiscale.'-';
		if($this->tipoattivita == 'CapoArea'){
			$codicefiscaledacercare = str_replace('-','',$codicefiscaledacercare);
		}
	
		$arraymesi = getMesiIntervallo($newannomese, $annomese);
		array_pop($arraymesi);

		$query = $db->prepare('SELECT SUM(prezzonetto*numeropezzi*(provvigione/100)) from storico, agenti WHERE agenti.id = storico.idagente AND annomese = ANY (:arraymesi ::varchar[]) and (idagente = :idagente OR codicefiscale = :codicefiscale)  GROUP BY agenti.cognome');
		$query->execute(array(':idagente' => $this->id, ':arraymesi' => '{'.php_to_postgres_array($arraymesi).'}', ':codicefiscale' => $codicefiscaledacercare));
		$sumstorico = $query->fetch();

		$query = $db->prepare('SELECT SUM(prezzonetto*numeropezzi*(provvigione/100)) from "storico-capiarea", agenti WHERE agenti.id = "storico-capiarea".idagente AND annomese = ANY (:arraymesi ::varchar[]) and (idagente = :idagente OR codicefiscale = :codicefiscale)  GROUP BY agenti.cognome');
		$query->execute(array(':idagente' => $this->id, ':arraymesi' => '{'.php_to_postgres_array($arraymesi).'}', ':codicefiscale' => $codicefiscaledacercare));
		$sumstoricocapiarea = $query->fetch();
		
		$query = $db->prepare('SELECT SUM(scf.prezzonetto*farmacie.numeropezzi*(scf.percentuale/100)) from "storico-capiarea-farmacie" as scf, agenti, farmacie WHERE scf.idagente = agenti.id AND scf.annomesefattura = farmacie.annomese AND scf.numerofattura = farmacie.numerofattura AND scf.idprodotto = farmacie.idprodotto AND scf.annomese = ANY (:arraymesi ::varchar[]) and (scf.idagente = :idagente OR codicefiscale = :codicefiscale)  GROUP BY agenti.cognome');
		$query->execute(array(':idagente' => $this->id, ':arraymesi' => '{'.php_to_postgres_array($arraymesi).'}', ':codicefiscale' => $codicefiscaledacercare));
		$sumstoricocapiareafarmacie = $query->fetch();
		
		$query = $db->prepare('SELECT SUM(prezzonetto*numeropezzi*(provvigione/100)) from "compensi-farmacie", agenti WHERE agenti.id = "compensi-farmacie".idagente AND liquidato = ANY (:arraymesi ::varchar[]) and (idagente = :idagente OR codicefiscale = :codicefiscale)  GROUP BY agenti.cognome');
		$query->execute(array(':idagente' => $this->id, ':arraymesi' => '{'.php_to_postgres_array($arraymesi).'}', ':codicefiscale' => $codicefiscaledacercare));
		$sumcompensifarmacie = $query->fetch();
		
		$query = $db->prepare('SELECT SUM(imponibile) from storicoftlibere, agenti WHERE agenti.id = storicoftlibere.idagente AND annomese = ANY (:arraymesi ::varchar[]) and (idagente = :idagente OR codicefiscale = :codicefiscale)  GROUP BY agenti.cognome');
		$query->execute(array(':idagente' => $this->id, ':arraymesi' => '{'.php_to_postgres_array($arraymesi).'}', ':codicefiscale' => $codicefiscaledacercare));
		$sumftlibere = $query->fetch();
		
		return $sumstorico[0] + $sumcompensifarmacie[0] + $sumftlibere[0]+ $sumstoricocapiarea[0] + $sumstoricocapiareafarmacie[0];
	}
		
	public function calculateNettoPrintFattura($db, $imponibile, $tipofattura, $annomese, $textpositivo=null, $valuepositivo=null, $textnegativo=null, $valuenegativo=null, $fatturalibera=null){
		$calciva  = 0;
		$calcenasarco = 0;
		$calcritacconto = 0;
		$calccontributoinps = 0;
		$calcrivalsainps = 0;
		
		if(!is_null($valuepositivo) && !is_null($textpositivo))
			$imponibile+=$valuepositivo;
		
		if(!is_null($valuenegativo) && !is_null($textnegativo))
			$imponibile+=$valuenegativo;
		
		if($this->tipoattivita == 'Consulente'){
			$imponibile = round($imponibile/5)*5;
		}
		
		
		if($this->rivalsainps>0){
			$calcrivalsainps = round($imponibile*$this->rivalsainps/100,2);
		}
		
		if($this->contributoinps>0){
			$calccontributoinps = round($imponibile*$this->contributoinps/100,2);
		}
		
		if($this->iva>0){
			if($this->rivalsainps>0){
				$calciva = round(($imponibile+$calcrivalsainps)*$this->iva/100,2);  //se c'è rivalsainps, iva si calcola sulla somma imponibile + rivalsa
			}else if($this->contributoinps>0){
				$calciva = round(($imponibile+$calccontributoinps)*$this->iva/100,2);  //se c'è contributoinps, iva si calcola sulla somma imponibile + contributo
			}else{
				$calciva = round($imponibile*$this->iva/100,2);
			}
		}
		
		if($this->ritacconto>0){
			if($this->rivalsainps>0){
				$calcritacconto = - round((($imponibile+$calcrivalsainps)*$this->ritacconto/100),2); //se c'è rivalsainps, la ritenuta d'acconto si calcola sulla somma imponibile + rivalsa
			}else{
				$calcritacconto = - round(($imponibile*$this->ritacconto/100),2);
			}
		}
		
		if($this->enasarco>0){
		
			$query = $db->prepare('SELECT massimale FROM enasarco');
			$query->execute();
			$arraymassimale = $query->fetch();

			$sumimponibile = $this->getImponibilePassatoEnasarco($db, $annomese);
			$tempcalcenasarco = - round(($sumimponibile*$this->enasarco/100),2);
			if(-$tempcalcenasarco > $arraymassimale[0])
				$calcenasarco = 0;

			else
			{
				$calcenasarco = - round(($imponibile*$this->enasarco/100),2);
				if((- $calcenasarco -  $tempcalcenasarco) > $arraymassimale[0])
					$calcenasarco =  $arraymassimale[0] - (-$calcenasarco -  $tempcalcenasarco);
			}
				//mi prendo l'anno e metto gennaio in annomese (nuovo annomese) 
				//funzione mi ritorna tutti i mesi e tolgo ultimo elemento (mese corrente)
			// select massimale from enasarco
				// select sum(storico.prezzonetto*storico.numeropezzi*(storico.provvigione/100)) from storico where annomese = any :arraymesi and idagente = :id group by idagente  -> mi ritorna l'imponibile fino al mese precedente
		// // select sum(storico.prezzonetto*storico.numeropezzi*(storico.provvigione/100)) from "compensi-farmacie" where annomese = any :arraymesi and idagente = :id group by idagente
		// sommo i due valori delle precedenti query
				// calcolo ensararco $NEWcalcenasarco = - round(($NEWimponibile*$this->enasarco/100),2);
				// if NEWcalcoloenasarco > massimale (da prendere nel db) -> $calcenasarco = 0
				// else calcolo come sotto l'enasarco, e se la somma di NEWcalcoloenasarco e calcoloenasarco < massimale allora l'enasarco rimane quello che ho calcolato ($calcenasarco classico)
	// altrimenti $calcenasarco = massimale - (la somma tra newcalcenasarco e enasarco)
				//$calcenasarco = - round(($imponibile*$this->enasarco/100),2);
		}
		$totaledovuto = round($imponibile+$calciva+$calcenasarco+$calcritacconto+$calccontributoinps+$calcrivalsainps,2);
		
		$anno = substr($annomese, 0, -2);
		$mese = substr($annomese, 4); 

		$partitaiva = "";
		if($this->partitaiva != NULL && strlen($this->partitaiva)>0){
		$fat="FATTURA Nr";
		$partitaiva = "P.IVA ".$this->partitaiva;
		}
		else
		$fat= "RICEVUTA Nr";


		$tipocompensi = '';
		if(!is_null($fatturalibera)){
			$tipocompensi = $fatturalibera;
		}
		else if($this->tipoattivita == 'I.S.F.')
			$tipocompensi = 'COMPENSI RELATIVI A';
		else if($this->tipoattivita == 'Agente')
			$tipocompensi = 'PROVVIGIONI RELATIVE A';
		else if($this->tipoattivita == 'Consulente')
			$tipocompensi = 'CONSULENZE RELATIVE A';
		else if($this->tipoattivita == 'CapoArea')
			$tipocompensi = 'COMPENSI COORDINAMENTO RELATIVI A';
		else if($this->tipoattivita == 'DirettoreItalia')
			$tipocompensi = 'CONSULENZA GESTIONALE E SCIENTIFICA RETE ITALIA RISORSE UMANE';

		VsWord::autoLoad();
		// istanza
		$doc = new VsWord();
	
		// primo paragrafo
		$paragraph = new PCompositeNode(); 
		$paragraph->addPNodeStyle( new AlignNode(AlignNode::TYPE_LEFT) );
		$paragraph->addText($this->cognome." ".$this->nome."\n<w:br/>".$this->indirizzo."\n<w:br/>".$this->cap." ".$this->citta." ".$this->provincia."\n<w:br/>"."C.F. ".strtoupper($this->codicefiscale). "\n<w:br/>".$partitaiva);

		$doc->getDocument()->getBody()->addNode( $paragraph );


		// secondo paragrafo
		$paragraph = new PCompositeNode(); 
		$paragraph->addPNodeStyle( new AlignNode(AlignNode::TYPE_RIGHT) );
		$paragraph->addText("");
		$paragraph->addText("EURO-PHARMA SRL\n<w:br/>Via Beinette 8/d\n<w:br/>10127 Torino TO\n<w:br/>P.IVA e C.F. 06328630014");
		$doc->getDocument()->getBody()->addNode( $paragraph );

		// terzo paragrafo
	

		$paragraph = new PCompositeNode(); 
		$paragraph->addPNodeStyle( new AlignNode(AlignNode::TYPE_LEFT) );
		$paragraph->addText("\n\n\n<w:br/><w:br/><w:br/>".$fat."______________ "." DEL ______________");
		$paragraph->addText("\n\n\n\n<w:br/><w:br/><w:br/><w:br/>".$tipocompensi."\n<w:br/>".$mese.'/'.$anno);
		$doc->getDocument()->getBody()->addNode( $paragraph );

		// quarto paragrafo

		$paragraph = new PCompositeNode(); 
		$paragraph->addPNodeStyle( new AlignNode(AlignNode::TYPE_RIGHT) );
		$paragraph->addText("\n\n\n<w:br/><w:br/><w:br/>");
		$integrazione = 0;
		if(!is_null($valuepositivo) && !is_null($textpositivo)){
			$integrazione = $imponibile - $valuepositivo; 
		}
		
		if(!is_null($valuenegativo) && !is_null($textnegativo)){
			$integrazione = $imponibile - $valuenegativo; 
		}
		
		if((!is_null($valuenegativo) && !is_null($textnegativo)) || (!is_null($valuepositivo) && !is_null($textpositivo))){
			$paragraph->addText($tipocompensi." ".$mese.'/'.$anno."                       "."€ ".number_format($integrazione,2,',','.')."\n<w:br/>");
		}else{
			$paragraph->addText($tipocompensi." ".$mese.'/'.$anno."                       "."€ ".number_format($imponibile,2,',','.')."\n<w:br/>");
		}
		
		if(!is_null($valuepositivo) && !is_null($textpositivo)){
			$paragraph->addText($textpositivo."                       "."€ ".number_format($valuepositivo,2,',','.')."\n<w:br/>");
		}
		
		if(!is_null($valuenegativo) && !is_null($textnegativo)){
			$paragraph->addText($textnegativo."                       "."€ ".number_format($valuenegativo,2,',','.')."\n<w:br/>");
		}
		
		if($calccontributoinps != 0)
		{
			$paragraph->addText("CASSA DI PREVIDENZA ".$this->contributoinps." %                    "."€  ".number_format($calccontributoinps,2,',','.')."\n<w:br/>");
		}

		if($calcrivalsainps != 0)
		{
			$paragraph->addText("RIVALSA INPS ".$this->rivalsainps." %                    "."€  ".number_format($calcrivalsainps,2,',','.')."\n<w:br/>");
		}
		
		$paragraph->addText("IMPONIBILE                    "."€ ".number_format($imponibile+$calcrivalsainps+$calccontributoinps,2,',','.')."\n<w:br/>");


		if($calciva != 0)
		{

			$paragraph->addText("\n\n<w:br/><w:br/>IVA ".$this->iva." %                    "."€ ".number_format($calciva,2,',','.'));		
		}
		
		
		if($this->partitaiva != NULL && strlen($this->partitaiva)>0){
			$paragraph->addText("\n<w:br/>TOTALE FATTURA "."                    "."€  ".number_format($imponibile+$calcrivalsainps+$calccontributoinps+$calciva,2,',','.')."\n\n<w:br/><w:br/>");
		}
		else{
			$paragraph->addText("\n<w:br/>TOTALE RICEVUTA "."                    "."€  ".number_format($imponibile+$calcrivalsainps+$calccontributoinps+$calciva,2,',','.')."\n\n<w:br/><w:br/>");
		}


		if($calcenasarco != 0)
		{
			$paragraph->addText("ENASARCO ".$this->enasarco." %                    "."€  ".number_format($calcenasarco,2,',','.')."\n<w:br/>");	
		}

		if($calcritacconto != 0)
		{
			$paragraph->addText("RIT. ACC. ".$this->ritacconto." %                    "."€  ".number_format($calcritacconto,2,',','.')."\n<w:br/>");
		}


		$paragraph->addText("\n\n<w:br/><w:br/>TOTALE A CREDITO "."                    "."€  ".number_format($totaledovuto,2,',','.'));

		$doc->getDocument()->getBody()->addNode( $paragraph );
		
		if($this->tipocontratto == 'MINIMI'){
			$paragraph = new PCompositeNode(); 
			$paragraph->addPNodeStyle( new AlignNode(AlignNode::TYPE_LEFT) );
			$paragraph->addText("\n\n<w:br/><w:br/>Prestazione svolta ai sensi dell’art. 1, commi 96-117, legge 244/2007 come modificata dall’articolo 27, D.L. 06/07/2011 n. 98 convertito con modificazioni dalla Legge 15/07/2011 n. 111 e pertanto non soggetta ad Iva né a ritenuta ai sensi del provvedimento Direttore Agenzia Entrate n. 185820 del 22/12/2011");
			$doc->getDocument()->getBody()->addNode( $paragraph );
		}
		
		if($this->tipocontratto == 'FORFETTARIO'){
			$paragraph = new PCompositeNode(); 
			$paragraph->addPNodeStyle( new AlignNode(AlignNode::TYPE_LEFT) );
			$paragraph->addText("\n\n<w:br/><w:br/>Operazione effettuata ai sensi dell'art. 1, commi da 54 a 89 della Legge n. 190/2014 REGIME FORFETTARIO");
			$doc->getDocument()->getBody()->addNode( $paragraph );
		}
		

		// inserimento dei dati nel corpo del documento
		//echo '<pre>'.($doc->getDocument()->getBody()->look()).'</pre>';
		// salvataggio in formato DOCX
		if (!file_exists(self::$path.'/'.$this->cognome.'_'.$this->nome.'_'.$this->codicefiscale)) {
		    mkdir(self::$path.'/'.$this->cognome.'_'.$this->nome.'_'.$this->codicefiscale, 0777, true);
		}
		
		$doc->saveAs(self::$path.'/'.$this->cognome.'_'.$this->nome.'_'.$this->codicefiscale.'/'.$this->cognome.$this->nome.$annomese.$tipofattura.'.docx');
	}
	
	public static function getAgentFromDB($myid, $db){
		$query = $db->prepare('SELECT * FROM agenti WHERE id = :id');
		$query->execute(array(':id' => $myid));
		$resultagent = $query->fetch(PDO::FETCH_ASSOC);
		return new Agent($resultagent);
	}
	
}

?>
