--
-- PostgreSQL database dump
--

SET statement_timeout = 0;
SET lock_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

--
-- Name: plpgsql; Type: EXTENSION; Schema: -; Owner: 
--

CREATE EXTENSION IF NOT EXISTS plpgsql WITH SCHEMA pg_catalog;


--
-- Name: EXTENSION plpgsql; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION plpgsql IS 'PL/pgSQL procedural language';


SET search_path = public, pg_catalog;

--
-- Name: calculateprovvigione(character varying, bigint, bigint); Type: FUNCTION; Schema: public; Owner: myuser
--

CREATE FUNCTION calculateprovvigione(myannomese character varying, myidagenteprodotto bigint, myidagente bigint) RETURNS real
    LANGUAGE plpgsql
    AS $$DECLARE

myprovvigione real;

mynumpezzi integer;

r RECORD;

sql text := '';

BEGIN

   SELECT provvigione INTO myprovvigione FROM "agente-prodotto" ap WHERE ap.id = myidagenteprodotto;

    sql = 'SELECT * FROM "agente-prodotto-target" WHERE idagprodotti @> ARRAY[' || CAST (myidagenteprodotto as text) || ']::bigint[]';

    FOR r IN EXECUTE(sql) LOOP

        SELECT sum(numeropezzi) INTO mynumpezzi FROM "monthly-results-agente-prodotto" WHERE idagente = myidagente AND idagenteprodotto = ANY(r.idagprodotti) AND annomese = myannomese GROUP BY idagente;

        IF (mynumpezzi >= r.target) THEN

            myprovvigione := r.percentuale;

        END IF;

    END LOOP;

   RETURN myprovvigione;

END$$;


ALTER FUNCTION public.calculateprovvigione(myannomese character varying, myidagenteprodotto bigint, myidagente bigint) OWNER TO myuser;

--
-- Name: insertagenteprodottoarea(integer, integer, integer, character varying); Type: FUNCTION; Schema: public; Owner: myuser
--

CREATE FUNCTION insertagenteprodottoarea(newidagenteprodotto integer, newidagentearea integer, mycodprodotto integer, myarea character varying) RETURNS void
    LANGUAGE plpgsql
    AS $$DECLARE

   num integer;

BEGIN

   SELECT count(*) INTO num FROM "agente-prodotto-area" AS apa, "agente-aree" AS aa, "agente-prodotto" as ap

          WHERE  apa.idagenteprodotto = ap.id AND apa.idagentearea = aa.id AND aa.area = myarea

          AND ap.codprodotto = mycodprodotto;

   IF num > 0 THEN 

      RAISE EXCEPTION 'Il prodotto è assegnato ad un altro agente per la stessa area';

   END IF;

   INSERT INTO "agente-prodotto-area"(idagentearea, idagenteprodotto) VALUES (newidagentearea, newidagenteprodotto);

END$$;


ALTER FUNCTION public.insertagenteprodottoarea(newidagenteprodotto integer, newidagentearea integer, mycodprodotto integer, myarea character varying) OWNER TO myuser;

--
-- Name: insertarget(text, integer, real); Type: FUNCTION; Schema: public; Owner: myuser
--

CREATE FUNCTION insertarget(idagprod text, newtarget integer, newpercentuale real) RETURNS void
    LANGUAGE plpgsql
    AS $$DECLARE

    searchsql text := '';

    r RECORD;

BEGIN

    searchsql := 'SELECT * FROM "agente-prodotto-target" WHERE idagprodotti @> ARRAY[' || idagprod || ']::bigint[] OR idagprodotti <@ ARRAY[' || idagprod || ']::bigint[]';

    FOR r IN EXECUTE(searchsql) LOOP
        IF NOT (ARRAY[idagprod]::bigint[] @> r.idagprodotti AND ARRAY[idagprod]::bigint[] <@ r.idagprodotti) THEN
            RAISE EXCEPTION 'Non puoi inserire più di un target somma se i prodotti sono diversi tra loro';

        END IF;

        IF newtarget >= r.target AND newpercentuale <= r.percentuale THEN

            RAISE EXCEPTION 'Il target è maggiore o uguale, ma la percentuale è minore o uguale';

        ELSEIF newtarget <= r.target AND newpercentuale >= r.percentuale THEN

            RAISE EXCEPTION 'Il target è minore o uguale, ma la percentuale è maggiore o uguale';

        END IF;

    END LOOP;

    INSERT INTO "public"."agente-prodotto-target" VALUES (newtarget, newpercentuale, ARRAY[idagprod]::bigint[]);
END$$;


ALTER FUNCTION public.insertarget(idagprod text, newtarget integer, newpercentuale real) OWNER TO myuser;

--
-- Name: sumimsfarmacie(character varying, bigint, bigint); Type: FUNCTION; Schema: public; Owner: myuser
--

CREATE FUNCTION sumimsfarmacie(myannomese character varying, myidprodotto bigint, myidagente bigint) RETURNS integer
    LANGUAGE plpgsql
    AS $$DECLARE

farma integer;

BEGIN

SELECT numeropezzi INTO farma FROM farmacie WHERE idprodotto = myidprodotto AND idagente = myidagente AND annomese = myannomese;

IF (farma IS NULL) THEN

    farma := 0;

END IF;

RETURN farma;

END$$;


ALTER FUNCTION public.sumimsfarmacie(myannomese character varying, myidprodotto bigint, myidagente bigint) OWNER TO myuser;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: agente-aree; Type: TABLE; Schema: public; Owner: myuser; Tablespace: 
--

CREATE TABLE "agente-aree" (
    area character varying(5) NOT NULL,
    id integer NOT NULL,
    idagente integer NOT NULL
);


ALTER TABLE "agente-aree" OWNER TO myuser;

--
-- Name: agente-aree_id_seq; Type: SEQUENCE; Schema: public; Owner: myuser
--

CREATE SEQUENCE "agente-aree_id_seq"
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE "agente-aree_id_seq" OWNER TO myuser;

--
-- Name: agente-aree_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: myuser
--

ALTER SEQUENCE "agente-aree_id_seq" OWNED BY "agente-aree".id;


--
-- Name: agente-prodotto; Type: TABLE; Schema: public; Owner: myuser; Tablespace: 
--

CREATE TABLE "agente-prodotto" (
    id integer NOT NULL,
    codprodotto integer NOT NULL,
    provvigione real,
    idagente integer NOT NULL
);


ALTER TABLE "agente-prodotto" OWNER TO myuser;

--
-- Name: agente-prodotto-area; Type: TABLE; Schema: public; Owner: myuser; Tablespace: 
--

CREATE TABLE "agente-prodotto-area" (
    idagentearea integer NOT NULL,
    idagenteprodotto integer NOT NULL,
    id integer NOT NULL
);


ALTER TABLE "agente-prodotto-area" OWNER TO myuser;

--
-- Name: agente-prodotto-area_id_seq; Type: SEQUENCE; Schema: public; Owner: myuser
--

CREATE SEQUENCE "agente-prodotto-area_id_seq"
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE "agente-prodotto-area_id_seq" OWNER TO myuser;

--
-- Name: agente-prodotto-area_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: myuser
--

ALTER SEQUENCE "agente-prodotto-area_id_seq" OWNED BY "agente-prodotto-area".id;


--
-- Name: agente-prodotto-target; Type: TABLE; Schema: public; Owner: myuser; Tablespace: 
--

CREATE TABLE "agente-prodotto-target" (
    target integer NOT NULL,
    percentuale real NOT NULL,
    idagprodotti bigint[],
    id integer NOT NULL
);


ALTER TABLE "agente-prodotto-target" OWNER TO myuser;

--
-- Name: agente-prodotto-target_id_seq; Type: SEQUENCE; Schema: public; Owner: myuser
--

CREATE SEQUENCE "agente-prodotto-target_id_seq"
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE "agente-prodotto-target_id_seq" OWNER TO myuser;

--
-- Name: agente-prodotto-target_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: myuser
--

ALTER SEQUENCE "agente-prodotto-target_id_seq" OWNED BY "agente-prodotto-target".id;


--
-- Name: agente-prodotto_id_seq; Type: SEQUENCE; Schema: public; Owner: myuser
--

CREATE SEQUENCE "agente-prodotto_id_seq"
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE "agente-prodotto_id_seq" OWNER TO myuser;

--
-- Name: agente-prodotto_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: myuser
--

ALTER SEQUENCE "agente-prodotto_id_seq" OWNED BY "agente-prodotto".id;


--
-- Name: agenti; Type: TABLE; Schema: public; Owner: myuser; Tablespace: 
--

CREATE TABLE agenti (
    nome text NOT NULL,
    cognome text NOT NULL,
    codicefiscale character varying(16) NOT NULL,
    partitaiva character varying(11),
    email text,
    iva real,
    enasarco real,
    ritacconto real,
    contributoinps real,
    rivalsainps real,
    id integer NOT NULL,
    indirizzo text,
    telefono character varying(30),
    tipocontratto text,
    datainiziocontratto date,
    datafinecontratto date,
    dataperiodoprova date,
    tipoattivita text,
    note text
);


ALTER TABLE agenti OWNER TO myuser;

--
-- Name: agenti_id_seq; Type: SEQUENCE; Schema: public; Owner: myuser
--

CREATE SEQUENCE agenti_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE agenti_id_seq OWNER TO myuser;

--
-- Name: agenti_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: myuser
--

ALTER SEQUENCE agenti_id_seq OWNED BY agenti.id;


--
-- Name: aree; Type: TABLE; Schema: public; Owner: myuser; Tablespace: 
--

CREATE TABLE aree (
    codice character varying(5) NOT NULL,
    nome text NOT NULL
);


ALTER TABLE aree OWNER TO myuser;

--
-- Name: farmacie; Type: TABLE; Schema: public; Owner: myuser; Tablespace: 
--

CREATE TABLE farmacie (
    annomese character varying(6),
    idprodotto bigint,
    numeropezzi bigint,
    idagente bigint,
    id integer NOT NULL
);


ALTER TABLE farmacie OWNER TO myuser;

--
-- Name: farmacie_id_seq; Type: SEQUENCE; Schema: public; Owner: myuser
--

CREATE SEQUENCE farmacie_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE farmacie_id_seq OWNER TO myuser;

--
-- Name: farmacie_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: myuser
--

ALTER SEQUENCE farmacie_id_seq OWNED BY farmacie.id;


--
-- Name: ims; Type: TABLE; Schema: public; Owner: myuser; Tablespace: 
--

CREATE TABLE ims (
    annomese character varying(6),
    idprodotto bigint,
    numeropezzi bigint,
    idarea character varying,
    id integer NOT NULL
);


ALTER TABLE ims OWNER TO myuser;

--
-- Name: ims_id_seq; Type: SEQUENCE; Schema: public; Owner: myuser
--

CREATE SEQUENCE ims_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE ims_id_seq OWNER TO myuser;

--
-- Name: ims_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: myuser
--

ALTER SEQUENCE ims_id_seq OWNED BY ims.id;


--
-- Name: prodotti; Type: TABLE; Schema: public; Owner: myuser; Tablespace: 
--

CREATE TABLE prodotti (
    id integer NOT NULL,
    nome text NOT NULL,
    sconto real,
    prezzo real,
    provvigionedefault real
);


ALTER TABLE prodotti OWNER TO myuser;

--
-- Name: monthly-results-agente-prodotto-microarea; Type: VIEW; Schema: public; Owner: myuser
--

CREATE VIEW "monthly-results-agente-prodotto-microarea" AS
 SELECT ims.annomese,
    aa.idagente,
    aree.codice,
    aree.nome AS area,
    ap.codprodotto,
    ims.numeropezzi,
    agenti.nome,
    agenti.cognome,
    prodotti.nome AS prodotto
   FROM "agente-aree" aa,
    "agente-prodotto" ap,
    "agente-prodotto-area" apa,
    ims,
    agenti,
    prodotti,
    aree
  WHERE (((((((((aa.area)::text = (ims.idarea)::text) AND (ims.idprodotto = ap.codprodotto)) AND (ap.id = apa.idagenteprodotto)) AND (aa.id = apa.idagentearea)) AND (aa.idagente = ap.idagente)) AND (aa.idagente = agenti.id)) AND (ims.idprodotto = prodotti.id)) AND ((ims.idarea)::text = (aree.codice)::text));


ALTER TABLE "monthly-results-agente-prodotto-microarea" OWNER TO myuser;

--
-- Name: monthly-results-agente-prodotto-area; Type: VIEW; Schema: public; Owner: myuser
--

CREATE VIEW "monthly-results-agente-prodotto-area" AS
 SELECT "monthly-results-agente-prodotto-microarea".annomese,
    "monthly-results-agente-prodotto-microarea".idagente,
    "monthly-results-agente-prodotto-microarea".area,
    "monthly-results-agente-prodotto-microarea".codprodotto,
    sum("monthly-results-agente-prodotto-microarea".numeropezzi) AS numeropezzi,
    "monthly-results-agente-prodotto-microarea".nome,
    "monthly-results-agente-prodotto-microarea".cognome,
    "monthly-results-agente-prodotto-microarea".prodotto
   FROM "monthly-results-agente-prodotto-microarea"
  GROUP BY "monthly-results-agente-prodotto-microarea".area, "monthly-results-agente-prodotto-microarea".annomese, "monthly-results-agente-prodotto-microarea".codprodotto, "monthly-results-agente-prodotto-microarea".idagente, "monthly-results-agente-prodotto-microarea".nome, "monthly-results-agente-prodotto-microarea".cognome, "monthly-results-agente-prodotto-microarea".prodotto;


ALTER TABLE "monthly-results-agente-prodotto-area" OWNER TO myuser;

--
-- Name: monthly-results-agente-prodotto; Type: VIEW; Schema: public; Owner: myuser
--

CREATE VIEW "monthly-results-agente-prodotto" AS
 SELECT "monthly-results-agente-prodotto-area".annomese,
    "monthly-results-agente-prodotto-area".idagente,
    "monthly-results-agente-prodotto-area".codprodotto,
    (sum("monthly-results-agente-prodotto-area".numeropezzi) + (sumimsfarmacie("monthly-results-agente-prodotto-area".annomese, ("monthly-results-agente-prodotto-area".codprodotto)::bigint, ("monthly-results-agente-prodotto-area".idagente)::bigint))::numeric) AS numeropezzi,
    "monthly-results-agente-prodotto-area".nome,
    "monthly-results-agente-prodotto-area".cognome,
    "monthly-results-agente-prodotto-area".prodotto,
    "agente-prodotto".id AS idagenteprodotto
   FROM "monthly-results-agente-prodotto-area",
    "agente-prodotto"
  WHERE (("monthly-results-agente-prodotto-area".codprodotto = "agente-prodotto".codprodotto) AND ("monthly-results-agente-prodotto-area".idagente = "agente-prodotto".idagente))
  GROUP BY "monthly-results-agente-prodotto-area".codprodotto, "monthly-results-agente-prodotto-area".annomese, "monthly-results-agente-prodotto-area".idagente, "monthly-results-agente-prodotto-area".nome, "monthly-results-agente-prodotto-area".cognome, "monthly-results-agente-prodotto-area".prodotto, "agente-prodotto".id;


ALTER TABLE "monthly-results-agente-prodotto" OWNER TO myuser;

--
-- Name: monthly-results-agente-prodotto-provvigione; Type: VIEW; Schema: public; Owner: myuser
--

CREATE VIEW "monthly-results-agente-prodotto-provvigione" AS
 SELECT "monthly-results-agente-prodotto".annomese,
    "monthly-results-agente-prodotto".idagente,
    "monthly-results-agente-prodotto".codprodotto,
    "monthly-results-agente-prodotto".nome,
    "monthly-results-agente-prodotto".cognome,
    "monthly-results-agente-prodotto".prodotto,
    calculateprovvigione("monthly-results-agente-prodotto".annomese, ("monthly-results-agente-prodotto".idagenteprodotto)::bigint, ("monthly-results-agente-prodotto".idagente)::bigint) AS provvigione,
    "monthly-results-agente-prodotto".numeropezzi
   FROM "monthly-results-agente-prodotto",
    prodotti
  WHERE (prodotti.id = "monthly-results-agente-prodotto".codprodotto);


ALTER TABLE "monthly-results-agente-prodotto-provvigione" OWNER TO myuser;

--
-- Name: monthly-results-agente-prodotto-importolordo; Type: VIEW; Schema: public; Owner: myuser
--

CREATE VIEW "monthly-results-agente-prodotto-importolordo" AS
 SELECT "monthly-results-agente-prodotto-provvigione".annomese,
    "monthly-results-agente-prodotto-provvigione".idagente,
    "monthly-results-agente-prodotto-provvigione".codprodotto,
    "monthly-results-agente-prodotto-provvigione".nome,
    "monthly-results-agente-prodotto-provvigione".cognome,
    "monthly-results-agente-prodotto-provvigione".prodotto,
    (((prodotti.prezzo - ((prodotti.prezzo * prodotti.sconto) / (100)::double precision)) * ("monthly-results-agente-prodotto-provvigione".provvigione / (100)::double precision)) * ("monthly-results-agente-prodotto-provvigione".numeropezzi)::double precision) AS importolordo
   FROM "monthly-results-agente-prodotto-provvigione",
    prodotti
  WHERE (prodotti.id = "monthly-results-agente-prodotto-provvigione".codprodotto);


ALTER TABLE "monthly-results-agente-prodotto-importolordo" OWNER TO myuser;

--
-- Name: monthly-results-agente-importolordo; Type: VIEW; Schema: public; Owner: myuser
--

CREATE VIEW "monthly-results-agente-importolordo" AS
 SELECT "monthly-results-agente-prodotto-importolordo".annomese,
    "monthly-results-agente-prodotto-importolordo".idagente,
    "monthly-results-agente-prodotto-importolordo".nome,
    "monthly-results-agente-prodotto-importolordo".cognome,
    round((sum("monthly-results-agente-prodotto-importolordo".importolordo))::numeric, 2) AS importolordo
   FROM "monthly-results-agente-prodotto-importolordo"
  GROUP BY "monthly-results-agente-prodotto-importolordo".annomese, "monthly-results-agente-prodotto-importolordo".idagente, "monthly-results-agente-prodotto-importolordo".nome, "monthly-results-agente-prodotto-importolordo".cognome;


ALTER TABLE "monthly-results-agente-importolordo" OWNER TO myuser;

--
-- Name: prodotti_id_seq; Type: SEQUENCE; Schema: public; Owner: myuser
--

CREATE SEQUENCE prodotti_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE prodotti_id_seq OWNER TO myuser;

--
-- Name: prodotti_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: myuser
--

ALTER SEQUENCE prodotti_id_seq OWNED BY prodotti.id;


--
-- Name: storico; Type: TABLE; Schema: public; Owner: myuser; Tablespace: 
--

CREATE TABLE storico (
    idagente bigint,
    meseanno character varying(6),
    importolordo real,
    calcenasarco real,
    calcritacconto real,
    calciva real,
    calccontributoinps real,
    calcrivalsainps real
);


ALTER TABLE storico OWNER TO myuser;

--
-- Name: id; Type: DEFAULT; Schema: public; Owner: myuser
--

ALTER TABLE ONLY "agente-aree" ALTER COLUMN id SET DEFAULT nextval('"agente-aree_id_seq"'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: myuser
--

ALTER TABLE ONLY "agente-prodotto" ALTER COLUMN id SET DEFAULT nextval('"agente-prodotto_id_seq"'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: myuser
--

ALTER TABLE ONLY "agente-prodotto-area" ALTER COLUMN id SET DEFAULT nextval('"agente-prodotto-area_id_seq"'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: myuser
--

ALTER TABLE ONLY "agente-prodotto-target" ALTER COLUMN id SET DEFAULT nextval('"agente-prodotto-target_id_seq"'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: myuser
--

ALTER TABLE ONLY agenti ALTER COLUMN id SET DEFAULT nextval('agenti_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: myuser
--

ALTER TABLE ONLY farmacie ALTER COLUMN id SET DEFAULT nextval('farmacie_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: myuser
--


ALTER TABLE ONLY ims ALTER COLUMN id SET DEFAULT nextval('ims_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: myuser
--

ALTER TABLE ONLY prodotti ALTER COLUMN id SET DEFAULT nextval('prodotti_id_seq'::regclass);


--
-- Name: agente-aree_area_idagente_key; Type: CONSTRAINT; Schema: public; Owner: myuser; Tablespace: 
--

ALTER TABLE ONLY "agente-aree"
    ADD CONSTRAINT "agente-aree_area_idagente_key" UNIQUE (area, idagente);


--
-- Name: agente-aree_pkey; Type: CONSTRAINT; Schema: public; Owner: myuser; Tablespace: 
--

ALTER TABLE ONLY "agente-aree"
    ADD CONSTRAINT "agente-aree_pkey" PRIMARY KEY (id);


--
-- Name: agente-prodotto-area_idagentearea_idagenteprodotto_key; Type: CONSTRAINT; Schema: public; Owner: myuser; Tablespace: 
--

ALTER TABLE ONLY "agente-prodotto-area"
    ADD CONSTRAINT "agente-prodotto-area_idagentearea_idagenteprodotto_key" UNIQUE (idagentearea, idagenteprodotto);


--
-- Name: agente-prodotto-area_pkey; Type: CONSTRAINT; Schema: public; Owner: myuser; Tablespace: 
--

ALTER TABLE ONLY "agente-prodotto-area"
    ADD CONSTRAINT "agente-prodotto-area_pkey" PRIMARY KEY (id);


--
-- Name: agente-prodotto-target_pkey; Type: CONSTRAINT; Schema: public; Owner: myuser; Tablespace: 
--

ALTER TABLE ONLY "agente-prodotto-target"
    ADD CONSTRAINT "agente-prodotto-target_pkey" PRIMARY KEY (id);


--
-- Name: agente-prodotto_codprodotto_idagente_key; Type: CONSTRAINT; Schema: public; Owner: myuser; Tablespace: 
--

ALTER TABLE ONLY "agente-prodotto"
    ADD CONSTRAINT "agente-prodotto_codprodotto_idagente_key" UNIQUE (codprodotto, idagente);


--
-- Name: agente-prodotto_pkey; Type: CONSTRAINT; Schema: public; Owner: myuser; Tablespace: 
--

ALTER TABLE ONLY "agente-prodotto"
    ADD CONSTRAINT "agente-prodotto_pkey" PRIMARY KEY (id);


--
-- Name: agenti_codicefiscale_key; Type: CONSTRAINT; Schema: public; Owner: myuser; Tablespace: 
--

ALTER TABLE ONLY agenti
    ADD CONSTRAINT agenti_codicefiscale_key UNIQUE (codicefiscale);


--
-- Name: agenti_pkey; Type: CONSTRAINT; Schema: public; Owner: myuser; Tablespace: 
--

ALTER TABLE ONLY agenti
    ADD CONSTRAINT agenti_pkey PRIMARY KEY (id);


--
-- Name: aree_pkey; Type: CONSTRAINT; Schema: public; Owner: myuser; Tablespace: 
--

ALTER TABLE ONLY aree
    ADD CONSTRAINT aree_pkey PRIMARY KEY (codice);


--
-- Name: farmacie_pkey; Type: CONSTRAINT; Schema: public; Owner: myuser; Tablespace: 
--

ALTER TABLE ONLY farmacie
    ADD CONSTRAINT farmacie_pkey PRIMARY KEY (id);


--
-- Name: ims_pkey; Type: CONSTRAINT; Schema: public; Owner: myuser; Tablespace: 
--

ALTER TABLE ONLY ims
    ADD CONSTRAINT ims_pkey PRIMARY KEY (id);


--
-- Name: prodotti_nome_key; Type: CONSTRAINT; Schema: public; Owner: myuser; Tablespace: 
--

ALTER TABLE ONLY prodotti
    ADD CONSTRAINT prodotti_nome_key UNIQUE (nome);


--
-- Name: prodotti_pkey; Type: CONSTRAINT; Schema: public; Owner: myuser; Tablespace: 
--

ALTER TABLE ONLY prodotti
    ADD CONSTRAINT prodotti_pkey PRIMARY KEY (id);


--
-- Name: storico_idagente_meseanno_key; Type: CONSTRAINT; Schema: public; Owner: myuser; Tablespace: 
--

ALTER TABLE ONLY storico
    ADD CONSTRAINT storico_idagente_meseanno_key UNIQUE (idagente, meseanno);


--
-- Name: agente-aree_idagente_fkey; Type: FK CONSTRAINT; Schema: public; Owner: myuser
--

ALTER TABLE ONLY "agente-aree"
    ADD CONSTRAINT "agente-aree_idagente_fkey" FOREIGN KEY (idagente) REFERENCES agenti(id);


--
-- Name: agente-prodotto-area_idagentearea_fkey; Type: FK CONSTRAINT; Schema: public; Owner: myuser
--

ALTER TABLE ONLY "agente-prodotto-area"
    ADD CONSTRAINT "agente-prodotto-area_idagentearea_fkey" FOREIGN KEY (idagentearea) REFERENCES "agente-aree"(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: agente-prodotto-area_idagenteprodotto_fkey; Type: FK CONSTRAINT; Schema: public; Owner: myuser
--

ALTER TABLE ONLY "agente-prodotto-area"
    ADD CONSTRAINT "agente-prodotto-area_idagenteprodotto_fkey" FOREIGN KEY (idagenteprodotto) REFERENCES "agente-prodotto"(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: agente-prodotto_codprodotto_fkey; Type: FK CONSTRAINT; Schema: public; Owner: myuser
--

ALTER TABLE ONLY "agente-prodotto"
    ADD CONSTRAINT "agente-prodotto_codprodotto_fkey" FOREIGN KEY (codprodotto) REFERENCES prodotti(id);


--
-- Name: agente-prodotto_idagente_fkey; Type: FK CONSTRAINT; Schema: public; Owner: myuser
--

ALTER TABLE ONLY "agente-prodotto"
    ADD CONSTRAINT "agente-prodotto_idagente_fkey" FOREIGN KEY (idagente) REFERENCES agenti(id);


--
-- Name: farmacie_idagente_fkey; Type: FK CONSTRAINT; Schema: public; Owner: myuser
--

ALTER TABLE ONLY farmacie
    ADD CONSTRAINT farmacie_idagente_fkey FOREIGN KEY (idagente) REFERENCES agenti(id);


--
-- Name: farmacie_idprodotto_fkey; Type: FK CONSTRAINT; Schema: public; Owner: myuser
--

ALTER TABLE ONLY farmacie
    ADD CONSTRAINT farmacie_idprodotto_fkey FOREIGN KEY (idprodotto) REFERENCES prodotti(id);


--
-- Name: idarea; Type: FK CONSTRAINT; Schema: public; Owner: myuser
--

ALTER TABLE ONLY "agente-aree"
    ADD CONSTRAINT idarea FOREIGN KEY (area) REFERENCES aree(codice);


--
-- Name: ims_idarea_fkey; Type: FK CONSTRAINT; Schema: public; Owner: myuser
--

ALTER TABLE ONLY ims
    ADD CONSTRAINT ims_idarea_fkey FOREIGN KEY (idarea) REFERENCES aree(codice);


--
-- Name: ims_idprodotto_fkey; Type: FK CONSTRAINT; Schema: public; Owner: myuser
--

ALTER TABLE ONLY ims
    ADD CONSTRAINT ims_idprodotto_fkey FOREIGN KEY (idprodotto) REFERENCES prodotti(id);


--
-- Name: storico_idagente_fkey; Type: FK CONSTRAINT; Schema: public; Owner: myuser
--

ALTER TABLE ONLY storico
    ADD CONSTRAINT storico_idagente_fkey FOREIGN KEY (idagente) REFERENCES agenti(id);


--
-- Name: public; Type: ACL; Schema: -; Owner: postgres
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;


--
-- PostgreSQL database dump complete
--

