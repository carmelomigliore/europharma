<?php
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
	public $note;

	public function Agent($record, $myid = NULL, $mynome = NULL, $mycognome = NULL, $mycodicefiscale = NULL, $mypartitaiva = NULL, $myemail = NULL, $myiva = NULL, $myenasarco = NULL, $myritacconto = NULL, $mycontributoinps = NULL, $myrivalsainps = NULL, $mytipocontratto = NULL, $mytipoattivita = NULL, $mydatainizio = NULL, $mydatafine = NULL, $mydataperiodoprova = NULL, $mytel = NULL, $myindirizzo = NULL, $mynote = NULL){
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
			$this->note = $record['note'];
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
			$this->note = $mynote;
		}
	}
	
	public function insertInDB($db){
		$query = $db->prepare("INSERT INTO agenti(nome, cognome, codicefiscale, partitaiva, email, iva, enasarco, ritacconto, contributoinps, rivalsainps, indirizzo, telefono, tipocontratto, datainiziocontratto, datafinecontratto, dataperiodoprova, tipoattivita, note) VALUES (:nome, :cognome, :codicefiscale, :partitaiva, :email, :iva, :enasarco, :ritacconto, :contributoinps, :rivalsainps, :indirizzo, :telefono, :tipocontratto, :datainiziocontratto, :datafinecontratto, :dataperiodoprova, :tipoattivita, :note) RETURNING id");
		$query->execute(array(':nome' => $this->nome, ':cognome' => $this->cognome, ':codicefiscale' => $this->codicefiscale, ':partitaiva' => $this->partitaiva, ':email' => $this->email, ':iva' => $this->iva, 
		':enasarco' => $this->enasarco, ':ritacconto' => $this->ritacconto, ':contributoinps' => $this->contributoinps, ':rivalsainps' => $this->rivalsainps, ':indirizzo' => $this->indirizzo, ':telefono' => $this->tel, ':tipocontratto' => $this->tipocontratto, ':datainiziocontratto' => $this->datainizio, ':datafinecontratto' => $this->datafine, ':dataperiodoprova' => $this->dataperiodoprova, ':tipoattivita' => $this->tipoattivita, ':note' => $this->note));
		$result = $query->fetch(PDO::FETCH_ASSOC);
		$this->id = $result['id'];
	}
	
	public function updateInDB($db){
	$query=$db->prepare('UPDATE agenti SET nome = :nome, cognome = :cognome, codicefiscale = :codicefiscale, partitaiva = :partitaiva, email = :email, iva = :iva, enasarco = :enasarco, ritacconto = :ritacconto, contributoinps = :contributoinps, rivalsainps = :rivalsainps, indirizzo = :indirizzo, telefono = :telefono, tipocontratto = :tipocontratto, datainiziocontratto = :datainiziocontratto, datafinecontratto = :datafinecontratto, dataperiodoprova = :dataperiodoprova, tipoattivita = :tipoattivita, note = :note WHERE id = :id');
	$query->execute(array(':nome' => $this->nome, ':cognome' => $this->cognome, ':codicefiscale' => $this->codicefiscale, ':partitaiva' => $this->partitaiva, ':email' => $this->email, ':iva' => $this->iva, ':ritacconto' => $this->ritacconto, ':rivalsainps' => $this->rivalsainps, ':enasarco' => $this->enasarco, ':contributoinps' => $this->contributoinps, ':indirizzo' => $this->indirizzo, ':telefono' => $this->tel, ':tipocontratto' => $this->tipocontratto, ':datainiziocontratto' => $this->datainizio, ':datafinecontratto' => $this->datafine, ':dataperiodoprova' => $this->dataperiodoprova, ':tipoattivita' => $this->tipoattivita, ':note' => $this->note, ':id' => $this->id));
	}
	
	public function assignProduct($db, $idproduct, $provvigione){
		$query = $db->prepare('INSERT INTO "agente-prodotto"(idagente, codprodotto, provvigione) VALUES (:idagente, :codprodotto, :provvigione)');
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
		echo('dio'.$idagenteprodottoarea);
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
	
	public function calculateSalary($db, $annomese, &$calciva, &$calcenasarco, &$calcritacconto, &$calccontributoinps, &$calcrivalsainps, &$totaledovuto, &$imponibile){
		$query = $db->prepare('SELECT * FROM "monthly-results-agente-importolordo" WHERE annomese = :annomese AND idagente = :idagente');
		$query->execute(array(':annomese' => $annomese, ':idagente' => $this->id));
		$result = $query->fetch(PDO::FETCH_ASSOC);
		$imponibile = $result['importolordo'];
		$calciva  = 0;
		$calcenasarco = 0;
		$calcritacconto = 0;
		$calccontributoinps = 0;
		$calcrivalsainps = 0;
		
		if($this->rivalsainps>0){
			$calcrivalsainps = $imponibile*$this->rivalsainps/100;
		}
		
		if($this->contributoinps>0){
			$calccontributoinps = $imponibile*$this->contributoinps/100;
		}
		
		if($this->iva>0){
			if($this->rivalsainps>0){
				$calciva = ($imponibile+$calcrivalsainps)*$this->iva/100;  //se c'è rivalsainps, iva si calcola sulla somma imponibile + rivalsa
			}else if($this->contributoinps>0){
				$calciva = ($imponibile+$calccontributoinps)*$this->iva/100;  //se c'è contributoinps, iva si calcola sulla somma imponibile + contributo
			}else{
				$calciva = $imponibile*$this->iva/100;
			}
		}
		
		if($this->ritacconto>0){
			if($this->rivalsainps>0){
				$calcritacconto = - (($imponibile+$calcrivalsainps)*$this->ritacconto/100); //se c'è rivalsainps, la ritenuta d'acconto si calcola sulla somma imponibile + rivalsa
			}else{
				$calcritacconto = - ($imponibile*$this->ritacconto/100);
			}
		}
		
		if($this->enasarco>0){
			$calcenasarco = - ($imponibile*$this->enasarco/100);
		}
		$totaledovuto = $imponibile+$calciva+$calcenasarco+$calcritacconto+$calccontributoinps+$calcrivalsainps;
	}
	
	public static function getAgentFromDB($myid, $db){
		$query = $db->prepare('SELECT * FROM agenti WHERE id = :id');
		$query->execute(array(':id' => $myid));
		$resultagent = $query->fetch(PDO::FETCH_ASSOC);
		return new Agent($resultagent);
	}
	
}

?>
