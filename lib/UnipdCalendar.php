<?php
/**
 * Description of UnipdCalendar
 *
 * @author Fabio, Chiara
 */
class UnipdCalendar {

    static $array_aule = array(
        7 => "LUM250",
        8 => "LUF1",
	13 => "LAB P140",
        14 => "LAB P036",
        30 => "LAB TA",
	9 => "LU3", // numero inventato
	10 => "LU4", // numero inventato
	11 => "P300", // numero inventato		
	//0 => "P100", // numero inventato --> su ingegneria
	6 => "P200",
	1 => "P1", // numero inventato
        2 => "P2", // numero inventato
        3 => "P3", // numero inventato
        4 => "P4", // numero inventato
	5 => "P5", // numero inventato 
	12 => "P6", // numero inventato 
        20 => "1BC45",
        21 => "1BC50",
        22 => "1A150",
        23 => "1C150",
        33 => "1AD100",
        24 => "2BC60",
        26 => "2AB40",
        28 => "2AB45",
        29 => "2BC30",
        31 => "SRVII",
        32 => "SRVI"
    );
    
    private $arrayAuleLibere=array();

    function __construct() {
           $this->includes();
           //setto il timezone a Roma perché altrimenti si setta a Greenwich ed è indietro di 2 ore
           //quindi se uno avese cercato da mezzanotte alle 2 avrebbe ricevuto gli orari del giorno sbagliato
           date_default_timezone_set("Europe/Rome");
    }

    private function includes() {
        include "simple_html_dom.php";
    }

	// ESEMPIO EXPLODE: $pieces = explode(" ", $pizza);
	
    /**
     * Funzione che restituisce un array con tutte le prenotazioni presenti in db1.
     * (utilizza solo "http://db1.math.unipd.it/booking/lezioni_tutte_oggi.php" perchè contiene tutte le prentazioni)
     *     
     */
    private function parseDB($data) {
        
        if (self::sanitizeDate($data)) {
           $Db3 = file_get_contents("http://db1.math.unipd.it/booking/lezioni_tutte_oggi.php?oggi=$data");
           
            $prenotazioni = $this->parse($Db3);

            return $prenotazioni;
        } else {
            echo "<p>Errore sull'input</p>";
            return array();
        }
    }

    /**
     * Funzione che controlla che la data sia consistente.
     *
     */
    private static function sanitizeDate($date){
        if (isset($date) && strlen(''.$date)==10 && preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$date)){
           return TRUE;
        }  else {
            return FALSE;
        }
    }

    /**
     * Funzione che fa il parsing di db1 e restituisce un array con tutte le prenotazioni in esso presenti.
     * (utilizza solo "http://db1.math.unipd.it/booking/lezioni_tutte_oggi.php" perchè contiene tutte le prentazioni)
     * E' privata perchè non deve essere utilizzata all'esterno.
     *     
     */
    private function parse($Db) {
        $countPrenotazioni = 0;
        $countTokens = 0;
        $prenotazioni = array();

        $righe = explode("\n", $Db);
        foreach ($righe as $riga) {
            if (isset($riga) && $riga != '') {
                $items = explode("|", $riga);
                foreach ($items as $item) {
                    if (isset($item)) {
                        if (($countTokens) % 8 == 1) // $item contiene il codice dell'aula
                            $prenotazioni[$countPrenotazioni][(($countTokens) % 8) - 1] = self::$array_aule[$item];
                        else if (($countTokens) % 8 != 0) // $item contiene gli altri campi, escluso il primo (codice prenotazione)
                            $prenotazioni[$countPrenotazioni][(($countTokens) % 8) - 1] = $item;
                        $countTokens++;
                        if ($countTokens % 8 == 0) {
                            $countPrenotazioni++;
                        }
                    }
                }
            }
        }
        return $prenotazioni;
    }

    /**
     * Funzione che restituisce un array con le prenotazioni presenti nel sito:
     * https://aulario.dmsa.unipd.it/mrbs/day.php
     *
     */
    private function getArrayFromAulario($date) {
        if (self::sanitizeDate($date)) {
            $orario = explode("-", $date);
            $year = $orario[0];
            $mounth = $orario[1];
            $day = $orario[2];
            $html = file_get_contents("https://aulario.dmsa.unipd.it/mrbs/day.php?year=" . $year . "&month=" . $mounth . "&day=" . $day);
            //serializzo
            $parsifiedHTML = str_get_html($html);
            $prenotazione = array();
            $aule = array("LU3", "LU4", "P1", "P2", "P3", "P300", "P4", "P5");
            $cont = 0;
            foreach ($parsifiedHTML->find('table') as $tables) {
                $cont++;
                if ($cont == 8) {  //ci sono 2 tabelle, la prima è il calendarietto e la seconda la vista agenda 
                    $indice = 0;
                    for ($i = 1; $i < 9; $i++) {
                        $thSaver = 0;
                        foreach ($tables->find('tr') as $riga) {
                            if ($thSaver == 0) {
                                $thSaver++;
                            } else {
                                $celle = $riga->find('td');
                                $ore = $celle[0]->find('a');
                                $ora = $ore[0]->text();
                                $script = $celle[$i]->find('script');
                                if ($script == null) { // se l'aula è prenotata (se la cella è vuota, dentro a td c'è una table con uno script)
                                    $link = $celle[$i]->find('a');
                                    if (!isset($link) || $link == null || $ora=="19:00") { // continuazione della prenotazione
                                        $prenotazione[$indice - 1][3] = $ora; // aggiorno ora fine e non incremento $indice
                                    } else { // nuova prenotazione
                                        $prenotazione[$indice][0] = $aule[$i - 1]; // AULA
                                        $prenotazione[$indice][1] = $date;    // DATA
                                        $prenotazione[$indice][2] = $ora;     // ORA INIZIO
                                        // ORA FINE viene aggiornata al ciclo successivo
                                        $prenotazione[$indice][4] = $link[0]->text(); // CORSO
                                        $prenotazione[$indice][5] = substr($link[0]->title, 14); // COGNOME PROF
                                        $prenotazione[$indice][6] = 0;    // NOME PROF
                                        // non è la prima prenotazione dell'aula -> aggiorno ora fine prenotazione prec
                                        if ($indice > 0 && $prenotazione[$indice - 1][0]==$prenotazione[$indice][0] ) { 
                                           $prenotazione[$indice - 1][3] = $ora;
                                         }
                                        $indice++;
                                    }
                                }
                            }
                        }
                    }
                    $prenotazione[$indice][0] = "P6"; // AULA
                    $prenotazione[$indice][1] = $date;    // DATA
                    $prenotazione[$indice][2] = "07:00";     // ORA INIZIO
                    $prenotazione[$indice][3] = "19:00"; // ORA FINE 
                    $prenotazione[$indice][4] = "Servizio Organizzazione/Formazione"; // CORSO
                    $prenotazione[$indice][5] = "Aula gestita dalla Segreteria Delegato Spazi Didattici"; // COGNOME PROF
                    $prenotazione[$indice][6] = 0; // NOME PROF
                    return $prenotazione;
                }
            }
        } else {
            echo "<p>Errore sull'input</p>";
            return;
        }
    }


    /**
     * Funzione che converte l'ora in un indice che servirà per indicizzare l'array delle prenotazioni.
     *
     */
    private function convertiOra($ora) {
        $lower_bound = 8;  //ora inizio
        $upper_bound = 19; //ora fine
        $slice=0;

        if (isset($ora)) {
			$pieces = explode(":", $ora);
            /* sono 22 time slices da mezzora compresi tra le 8.00 e le 19 
              0 :=	08:00->08:30
              21:=	18:30->19:00
             */
            if ($pieces[0]!=7) {
            	$slice = 2 * ($pieces[0] - $lower_bound);
              	if (isset($pieces[1])) {
                	if ($pieces[1] == 30) { $slice++;}
        		}
       			else {
            		$slice = 25;    //se non è indicata un'ora di fine aggiungo un'ora a quella d'inizio.
        		}
        	}
        	return $slice;
        }
     }

    /**
     * Funzione che converte un indice di un array in un'ora.
     *
     */
    private function convertiInOra($indice) {
        $lower_bound = 8;  //ora inizio
        $upper_bound = 19; //ora fine
        
          if($indice%2){
            $mezzora="30"; 
            $ora=$lower_bound+(($indice-1)/2);
          }
          else{
            $mezzora="00";
            $ora=$lower_bound+($indice/2);
          }
 
        if($ora<10)
            $ora="0".$ora;
        $stringa=$ora.':'.$mezzora;
        return $stringa;
    }

   /**
     * Funzione che fa il merge di due array.
     *
     */
    public function mergeArrays($a1, $a2) {
        $risultato = $a1;

        foreach ($a2 as $riga) {
            array_push($risultato, $riga);
        }
        return $risultato;
    }

    /**
     * Funzione che restituisce l'array finale, contenente tutte le prenotazioni.
     *
     */
    private function getFinalArray($date) {
       if(self::sanitizeDate($date)){
           $db1 = $this->parseDB($date);
            $aulario = $this->getArrayFromAulario($date);
            $merge = $this->mergeArrays($aulario, $db1); // aggiunge il secondo array in coda al primo
            // var_dump($merge);//die();
            $risultato;
            foreach ($merge as $row) {
                if (isset($row)){
                    $aula = $row[0];
                    $oraIn = $this->convertiOra($row[2]);
                    if (isset($row[3]))
                        $oraFine = $this->convertiOra($row[3]);
                    else
                        $oraFine = $this->convertiOra(null);
                    $risultato[$oraIn][$aula] = $row;
                    $diffOre = $oraFine - $oraIn;
                    for ($i = 1; $i < $diffOre; $i++) {
                        $risultato[$oraIn + $i][$aula] = "\"";
                    }
                }
            }
            return $risultato;
        }
       return array();
    }

    /**
     * Funzione che genera la tabella di prenotazioni a partire da un array.
     *
     */
    public function createTable($prenotazioni) {
        echo "<table id='tblPrenotazioni'  summary='Tabella che riporta le prenotazioni delle aule di interesse per gli studenti di Matematica e Informatica.' >";
        echo "<thead>";
        echo "<tr> <th> ORARIO: </th>";
        // NUMERO DI AULE:
        $numAule = 0;
        foreach (self::$array_aule as $aula) {
            echo "<th> $aula </th>";
            $numAule++;
        }
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";
        for ($i = 0; $i <= 22; $i++) {
            echo "<tr> <td class='indice_ora'> ".$this->convertiInOra($i)." </td>";
            if (isset($prenotazioni[$i])) {
                foreach (self::$array_aule as $aula) {
                   if (isset($prenotazioni[$i][$aula])) {
                        if ($prenotazioni[$i][$aula]=="\"")
                            echo "<td class='apici'> <span > \" </span> </td>";
                        else
                            echo "<td class='corso'> <span>" . $prenotazioni[$i][$aula][4] . "</span> </td>";
                    } else {
                        echo "<td> </td>";
                        $this->arrayAuleLibere[$i][$aula]="libera";
                    }
                }
            } else {
                for ($j = 0; $j < $numAule; $j++)
                    echo "<td> </td>";
                $this->arrayAuleLibere[$i]="tutto libero";
                echo $i;
            }
            echo "</tr>";
        }        
        echo "</tbody>";
        echo "</table>";
        var_dump($this->arrayAuleLibere);
    }
    
    /**
     * Funzione che crea l'array delle prenotazioni nel formato corretto per trasformarlo in lista,
     * a partire dall'array delle prenotazioni.
     *
     */
    private function createListedArray($prenotazioni)
    {
       $arrayPerLista = array(); //creo gli array per ogni aula;
        foreach (self::$array_aule as $key => $value) {
            $arrayPerLista[$value] = array();
        }

        foreach (self::$array_aule as $aula) {
            $numPrenotazione=0;
            for ($i = 0; $i <= 24; $i++) {
                if (isset($prenotazioni[$i][$aula])) {
                    if ($prenotazioni[$i][$aula] != "\"") {
                        $arrayPerLista[$aula][$numPrenotazione] = $prenotazioni[$i][$aula];
                        $numPrenotazione++;
                    }
                }
            }
        }
        return $arrayPerLista; 
    }
    /**
     * Funzione che stampa la lista delle prenotazioni a partire dall'array delle prenotazioni. Questo formato serve come
     * alternativa alla tabella per gli screenreader e come layout di stampa.
     *
     */
    public function printListedContents($prenotazioni) {
        //ordinare l'array secondo le aule, e per ogni aula ordinare secondo l'orario
        /* impostare lista: GIORNO
                            AULA
         *                        prenotaz. 1 (orario iniz-fine, materia, docente)
         *                        prenotaz. 2
         * 
         * 
         */
        // contenuto da nascondere tramite css -> aggiungere le opportune classi
       $arrayPerLista=  $this->createListedArray($prenotazioni);
       echo "<ul class='txt_prenotazioni_container'>";
       foreach ($arrayPerLista as $aula => $val) {
            echo "<li><span class='nome_aula'>".$aula."</span>";
            if (isset($val)) {
                ?><ul class="txt_prenotazioni"><?php
                foreach ($val as $prenotazione) {
                    echo "<li><ul>";
                    echo "<li> <strong> ora d'inizio: </strong>{$prenotazione[2]}</li>";
                    echo "<li> <strong> ora di fine: </strong>{$prenotazione[3]}</li>";
                    echo "<li> <strong> Materia: </strong>{$prenotazione[4]}</li>";
                    echo "<li> <strong> Docente: </strong>{$prenotazione[5]} {$prenotazione[6]}</li>";
                    echo "</ul></li>";
                    
                    //var_dump($prenotazione) ;
                }
                ?></ul></li><?php
            }
        }
        echo "</ul>";
        //var_dump($arrayPerLista);
    }

    public function printHead(){
        ?><head>
	<meta charset='UTF-8'>
        <title>Stato Aule Informatica - UNIPD </title>
        <meta name='author' content='Fabio Ros, Chiara Bigarella' >
        <meta name='description' content='Sito per visualizzare la prenotazione delle aule di interesse per gli studenti di Informatica e Matematica'>
	<meta name='viewport' content='width=device-width, initial-scale=1.0, maximum-scale=1.0' />
	<link rel='icon' type='image/x-icon' href='lib/css/img/favicon.ico' sizes='16x16'>
		
		<!-- <meta name='viewport' content='width=device-width, initial-scale=1.0' >-->

        <link rel='stylesheet' href='lib/css/style.css' >
        <link rel='stylesheet' type='text/css' href='//fonts.googleapis.com/css?family=Cuprum'  >
		
	<script src='//code.jquery.com/jquery-1.11.0.min.js'></script>
	<script src='lib/JS/script.js'></script>
	<!-- <meta http-equiv='X-UA-Compatible' content='IE=edge,chrome=1'> -->

	
        <link rel='stylesheet' href='lib/toggle/dist/toggle-switch.css' >

     </head><?php
    }


    public function render() {
       ?>
                <div id="skip-link"><p><a class="visuallyhidden" tabindex="2" href="#maincontent">Vai al contenuto</a></p></div>
                <!-- <div id="page-wrapper"><div id="page">-->
                    <header id="header">
                    <div class="clearfix">
                        <div role="banner" id="logo">
                            <a href="/" id="logo" tabindex="1">
                                <img itemprop="image" src="lib/css/img/logo.png" alt="Università degli Studi di Padova" />
                            </a>
                            <h1 itemprop="name" id="titolo_sito">STATO AULE INFORMATICA</h1>
                        </div>

                        <nav role="navigation" class="lista_collegamenti">
                            <ul class="menu users">
                                <li class="menu_ateneo" ><a tabindex="8" href="http://www.unipd.it/webmail">Webmail</a></li>
                                <li class="menu_ateneo"><a tabindex="9" href="https://uniweb.unipd.it">Uniweb</a></li>
                                <li class="menu_ateneo"><a tabindex="10" href="http://www.unipd.it">Unipd.it</a></li>
                                <li class="menu_ateneo"><a tabindex="11" href="http://informatica.math.unipd.it/">Sito del CdL in informatica</a></li>
                            </ul>
                        </nav>
                </div></header><!-- /.section, /#header -->

                <div id="skip-link-primary"><p><a class="visuallyhidden" tabindex="121" href="#maincontent">Vai al contenuto</a></p></div>

                <nav id="mainmenu" role="navigation">

                    <ul class="clearfix">
                        <li class="fl selected" id="menuHomePage">
                            <a class="fl" tabindex="2" href="">Home</a>
                        </li>
                        <li class="fl" id="menuTutte" >
                            <a class="fl" id="tutte_ref" tabindex="3" href="">Tutte le aule</a>
                        </li>
                        <li class="fl stripe" id="menuLab">
                            <a class="fl" id="lab_ref" tabindex="4" href="">Laboratori</a>
                        </li>
                        <li class="fl" id="menuPaolotti">
                            <a class="fl" id="paolotti_ref" tabindex="5" href="">Paolotti</a>
                        </li>
                        <li class="fl" id="menuLuzzatti">
                            <a class="fl" id="luzzatti_ref" tabindex="6" href="">Luzzatti</a>
                        </li>

                        <li id="menuWifi" class="fl">
                            <a class="fl" id="torre_ref" tabindex="7" href="">Torre di Archimede</a>
                        </li>

                    </ul>
                    <!-- <h1 class="title"></h1>-->
                    <!--h1 class="out-of-layout">Universit&agrave; degli studi di padova</h1-->


                </nav>

		<section class="descrizione">
			<p>
				Questo sito è nato dall'esigenza di poter visualizzare in un'unica pagina lo stato di prenotazione 
				delle aule che possono interessare agli studenti di Informatica e Matematica.
				Per comodità, è rimasta la possibilità di scegliere di visualizzare solo alcuni gruppi di aule. 
				La suddivisione è stata fatta in base al luogo di appartenenza (Paolotti, Via Luzzatti, Torre di Archimede). 
				E' stata data particolare importanza ai laboratori informatici, anch'essi visualizzabili separatamente.
                                
			</p>

		</section>

                <section id="time_controller">
                    <?php 
                        if(isset($_POST['data'])&&$_POST['data']!='')
                            $data=$_POST['data'];
                        else
                            $data=date('Y-m-d');
                    ?>
                    <form method="post" id='formDatePicker'>
<!--                        <label for="input_date"> Seleziona un giorno per il quale vuoi conoscere lo stato d'occupazione delle aule</label>-->
                        <input id="input_date" type="date" name="data" <?php  echo "value='".$data."'"; ?>/>
                        <input type="submit" name="invia_data" value="invia" class="btn">
                    </form>
                    
                    <h2>Data Corrente: <?php echo $data;?> </h2>
                </section>
                
                <section id="toogleWrapper" class="switch-toggle switch-candy">
                	  <input id="tabella" name="view" type="radio" checked>
                	  <label for="tabella">TABELLA</label>
                	  <input id="lista" name="view" type="radio" >
                	  <label for="lista">LISTA</label>
                	  
                	  <a></a>
                </section>
                
                <section id='tblWrapper'>
                        <?php 
                                $arrayPrenotazioni=$this->getFinalArray($data);
                                $this->createTable($arrayPrenotazioni);
                        ?>
                 </section>
          
                <section id='listWrapper'>
                    <?php
                        $this->printListedContents($arrayPrenotazioni);
                    ?>
                </section>
        <?php
    }   

    public function printFooter(){
    ?><footer>
    	<p class="disclaimer"> I dati sono prelevati, al momento del caricamento della pagina,
        in modo automatico dai siti di prenotazione dell'ateneo; potrebbero, quindi, essere affetti da errore.</p>
        <p class="autori" >
        	A cura di: <span itemprop="author" itemscope itemtype="http://schema.org/Person">
                      <span itemprop="name">Chiara Bigarella</span></span>
                    e <span itemprop="author" itemscope itemtype="http://schema.org/Person">
         	          <span itemprop="name">Fabio Ros</span></span>
        </p>
    </footer><?php
    }

}
