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

	public $tipocontratto;
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

	public function Agent($record, $myid = NULL, $mynome = NULL, $mycognome = NULL, $mycodicefiscale = NULL, $mypartitaiva = NULL, $myemail = NULL, $myiva = NULL, $myenasarco = NULL, $myritacconto = NULL, $mycontributoinps = NULL, $myrivalsainps = NULL, $mytipocontratto = NULL, $mytipoattivita = NULL, $mydatainizio = NULL, $mydatafine = NULL, $mydataperiodoprova = NULL, $mytel = NULL, $myindirizzo = NULL, $mynote = NULL, $mycap = NULL, $mycitta = NULL, $myprovincia = NULL, $myattivo = NULL){
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
		}
	}
	
	public function insertInDB($db){
		$query = $db->prepare("INSERT INTO agenti(nome, cognome, codicefiscale, partitaiva, email, iva, enasarco, ritacconto, contributoinps, rivalsainps, indirizzo, telefono, tipocontratto, datainiziocontratto, datafinecontratto, dataperiodoprova, tipoattivita, note, citta, cap, provincia) VALUES (:nome, :cognome, :codicefiscale, :partitaiva, :email, :iva, :enasarco, :ritacconto, :contributoinps, :rivalsainps, :indirizzo, :telefono, :tipocontratto, :datainiziocontratto, :datafinecontratto, :dataperiodoprova, :tipoattivita, :note, :citta, :cap, :provincia, :attivo) RETURNING id");
		$query->execute(array(':nome' => $this->nome, ':cognome' => $this->cognome, ':codicefiscale' => $this->codicefiscale, ':partitaiva' => $this->partitaiva, ':email' => $this->email, ':iva' => $this->iva, ':enasarco' => $this->enasarco, ':ritacconto' => $this->ritacconto, ':contributoinps' => $this->contributoinps, ':rivalsainps' => $this->rivalsainps, ':indirizzo' => $this->indirizzo, ':telefono' => $this->tel, ':tipocontratto' => $this->tipocontratto, ':datainiziocontratto' => $this->datainizio, ':datafinecontratto' => strlen($this->datafine) > 0 ? $this->datafine : null, ':dataperiodoprova' => strlen($this->dataperiodoprova) > 0 ? $this->dataperiodoprova : null, ':tipoattivita' => $this->tipoattivita, ':note' => $this->note, ':citta' => $this->citta, ':cap' => $this->cap, ':provincia' => $this->provincia, ':attivo' => $this->attivo));
		$result = $query->fetch(PDO::FETCH_ASSOC);
		$this->id = $result['id'];
	}
	
	public function updateInDB($db){
	$query=$db->prepare('UPDATE agenti SET nome = :nome, cognome = :cognome, codicefiscale = :codicefiscale, partitaiva = :partitaiva, email = :email, iva = :iva, enasarco = :enasarco, ritacconto = :ritacconto, contributoinps = :contributoinps, rivalsainps = :rivalsainps, indirizzo = :indirizzo, telefono = :telefono, tipocontratto = :tipocontratto, datainiziocontratto = :datainiziocontratto, datafinecontratto = :datafinecontratto, dataperiodoprova = :dataperiodoprova, tipoattivita = :tipoattivita, note = :note, citta = :citta, cap = :cap, provincia = :provincia, attivo = :attivo WHERE id = :id');
	$query->execute(array(':nome' => $this->nome, ':cognome' => $this->cognome, ':codicefiscale' => $this->codicefiscale, ':partitaiva' => $this->partitaiva, ':email' => $this->email, ':iva' => $this->iva, ':ritacconto' => $this->ritacconto, ':rivalsainps' => $this->rivalsainps, ':enasarco' => $this->enasarco, ':contributoinps' => $this->contributoinps, ':indirizzo' => $this->indirizzo, ':telefono' => $this->tel, ':tipocontratto' => $this->tipocontratto, ':datainiziocontratto' => $this->datainizio, ':datafinecontratto' => strlen($this->datafine) > 0 ? $this->datafine : null, ':dataperiodoprova' => strlen($this->dataperiodoprova) > 0 ? $this->dataperiodoprova : null, ':tipoattivita' => $this->tipoattivita, ':note' => $this->note, ':id' => $this->id, ':citta' => $this->citta, ':cap' => $this->cap, ':provincia' => $this->provincia, ':attivo' => $this->attivo));
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
	
	public function assignProductTarget($db, $idproduct, $target, $percentuale){
		//TODO Fabrizio
	}
	
	public function deleteProductTarget($db, $idproduct, $target){
		//TODO Fabrizio
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
	
	public function calculateIMS($db, $annomese){
		$query = $db->prepare('SELECT importolordo FROM "monthly-results-agente-importolordo" WHERE annomese = :annomese AND idagente = :idagente');
		$query->execute(array(':annomese' => $annomese, ':idagente' => $this->id));
		$result = $query->fetch(PDO::FETCH_ASSOC);
		$imponibile = $result['importolordo'];
		$this->calculateNettoPrintFattura($imponibile, 'ims', $annomese);
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
		$this->calculateNettoPrintFattura($imponibile, 'farmacia'.$numerofattura, $annomese);
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
		$query = $db->prepare('SELECT prodotti.nome, aree.nome || "substring"(aree.codice::text, 4, 2) as area, numeropezzi, to_char(provvigione, \'FM999999999.00\'), to_char(prezzonetto, \'FM999999999.00\') as prezzonetto, to_char(prezzonetto*(provvigione/100)*numeropezzi, \'FM999999999.00\') as spettanza FROM storico, aree, prodotti WHERE idagente = :idagente AND annomese = :annomese AND storico.idprodotto = prodotti.id AND storico.codarea = aree.codice ORDER BY prodotti.nome,area,codice');
		$query->execute(array(':idagente' => $this->id, ':annomese' => $annomese));
		return $query->fetchAll(PDO::FETCH_ASSOC);
	}
	
	public function generateCSV($array, $headers, $tipo, $annomese){
		$fp = fopen($annomese.$tipo.$this->cognome.$this->nome.'.csv', 'w');
		
		fputcsv($fp, $headers, ';');
		foreach ($array as $fields) {
		    fputcsv($fp, $fields, ';');
		}

		fclose($fp);
	}
		
	public function calculateNettoPrintFattura($imponibile, $tipofattura, $annomese){
		$calciva  = 0;
		$calcenasarco = 0;
		$calcritacconto = 0;
		$calccontributoinps = 0;
		$calcrivalsainps = 0;
		
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
			$calcenasarco = - round(($imponibile*$this->enasarco/100),2);
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
		if($this->tipoattivita == 'I.S.F.')
			$tipocompensi = 'COMPENSI RELATIVI A';
		else if($this->tipoattivita == 'Agente')
			$tipocompensi = 'PROVVIGIONI RELATIVE A';
		else if($this->tipoattivita == 'Consulente')
			$tipocompensi = 'CONSULENZE RELATIVE A';

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
		$paragraph->addText("\n\n\n<w:br/><w:br/><w:br/>IMPONIBILE                    "."€ ".number_format($imponibile,2,',','.')."\n<w:br/>");



		if($calciva != 0)
		{

			$paragraph->addText("IVA ".$this->iva." %                    "."€ ".number_format($calciva,2,',','.')."\n<w:br/>");		
		}

		if($calcenasarco != 0)
		{
			$paragraph->addText("ENASARCO ".$this->enasarco." %                    "."€  ".number_format($calcenasarco,2,',','.')."\n<w:br/>");	
		}

		if($calcritacconto != 0)
		{
			$paragraph->addText("RIT. ACC. ".$this->ritacconto." %                    "."€  ".number_format($calcritacconto,2,',','.')."\n<w:br/>");
		}

		if($calccontributoinps != 0)
		{
			$paragraph->addText("CASSA DI PREVIDENZA ".$this->contributoinps." %                    "."€  ".number_format($calccontributoinps,2,',','.')."\n<w:br/>");
		}

		if($calcrivalsainps != 0)
		{
			$paragraph->addText("RIVALSA INPS ".$this->rivalsainps." %                    "."€  ".number_format($calcrivalsainps,2,',','.')."\n<w:br/>");
		}


		$paragraph->addText("\n\n\n<w:br/><w:br/><w:br/>TOTALE FATTURA "."                    "."€  ".number_format($totaledovuto,2,',','.'));


		$doc->getDocument()->getBody()->addNode( $paragraph );

		// inserimento dei dati nel corpo del documento
		//echo '<pre>'.($doc->getDocument()->getBody()->look()).'</pre>';
		// salvataggio in formato DOCX
		$doc->saveAs($this->cognome.$this->nome.$annomese.$tipofattura.'.docx');
	}
	
	public static function getAgentFromDB($myid, $db){
		$query = $db->prepare('SELECT * FROM agenti WHERE id = :id');
		$query->execute(array(':id' => $myid));
		$resultagent = $query->fetch(PDO::FETCH_ASSOC);
		return new Agent($resultagent);
	}
	
}

?>
