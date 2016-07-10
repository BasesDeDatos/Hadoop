<?php
	$mysqli = mysqli_connect("localhost", "prueba", "prueba",  "test");
	if ($mysqli->connect_errno) {
        printf("Falló la conexión: %s\n", $mysqli->connect_error);
        exit();
    }
?>

<?php 
	$resultados = array();
	$words = preg_split('/\W/', $_GET['q'], 0, PREG_SPLIT_NO_EMPTY);
    foreach($words as $word){
	    // $query = "SELECT * FROM addressXword INNER JOIN word on addressXword.word_id = word.word_id WHERE word.word = \"{$_GET['q']}\""; 
        $query = "SELECT * FROM pageCount"
		    . "	WHERE word LIKE \"%".$word."%\"" ;
		$result = $mysqli->query($query);
		$num = 1;
		while ($row = $result->fetch_assoc()){
			$resultados[$row["address"]]["info"] = array("URL" => $row["address"], "title" => $row["title"]);
			$resultados[$row["address"]]["count"] += intval ($row["count"]);
			$resultados[$row["address"]]["words"][$word] = intval ($row["count"]);;
		}
		$words = preg_split('/\W/', limpiarTexto($_GET['q']), 0, PREG_SPLIT_NO_EMPTY);
	     $mysqli->next_result(); 
    }
    //var_dump($resultados)
?>

<!DOCTYPE html>
<html lang="en" class="no-js">
	<head>
	    <?php require_once("header.php"); ?>

        <meta name="viewport" content="width=device-width, initial-scale=1"> 
        <link rel="stylesheet" type="text/css" href="css/normalize.css" />
        <link rel="stylesheet" type="text/css" href="css/demo.css" />
        <link rel="stylesheet" type="text/css" href="css/component.css" />
        <link href='//fonts.googleapis.com/css?family=Raleway:200,400,800|Londrina+Outline' rel='stylesheet' type='text/css'>
        <!--[if IE]>
        	<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->
	</head>
<body class="mdl-demo mdl-color--grey-100 mdl-color-text--grey-700 mdl-base">
  <div class="mdl-layout mdl-js-layout">
    <header class="mdl-layout__header mdl-layout__header--scroll mdl-color--primary">
      <div class="mdl-l/yout--large-screen-only mdl-layout__header-row">
        <h3>Search-APP</h3>
      </div>
      <div class="mdl-layout__tab-bar mdl-js-ripple-effect mdl-color--primary-dark">
        <a href="#overview" class="mdl-layout__tab is-active">RESULTADOS DE: <?php echo $_GET["q"] ?></a>
        <a href="#info" class="mdl-layout__tab">DETALLES DE BUSQUEDA</a>
      </div>
    </header>
    <section class="section--footer mdl-color--white mdl-grid">
            <div class="section__text mdl-cell mdl-cell--4-col-desktop mdl-cell--6-col-tablet mdl-cell--3-col-phone">
              <h5><?php echo count($resultados) ?> Enlaces Encontrados </h5>
            </div>
            
          </section>
    <main class="mdl-layout__content">
      <div class="mdl-layout__tab-panel is-active" id="overview">
      	<pre>
      		
      	
      	</pre>

        <?php 
			// Obtener una lista de columnas
			$count = array();
			foreach ($resultados as $clave => $fila) {
			    $count[$clave] = $fila['count'];
			}
			//var_dump($count);
			
			array_multisort($count, SORT_DESC, $resultados);
			foreach ($resultados as $url  => $data) { ?>
				
		 		<section class="section--center mdl-grid mdl-grid--no-spacing mdl-shadow--2dp">
			          <div class="mdl-card mdl-cell mdl-cell--12-col-desktop">
			            <div class="mdl-card__supporting-text">
			              <h4><?php echo $data["info"]["title"] ?></h4>
			              <p>Encontrada la palabra 
			              	<?php 			
			              		foreach ($data["words"] as $word => $count) { ?>
									<br><?php echo $word?> <b><?php echo $count ?></b> veces.
			              		<?php } ?>
			              </p>
			            </div>
			            <div class="mdl-card__actions">
			            	<?php 
								if (strpos($url, 'http') !== false) {
			            			$url = $url;
								} else {
			            			$url = "//" . $url;
								}
			            	?>
			              <a href="<?php echo $url ?>" class="mdl-button">Ir a la pagina</a>
			            </div>
			          </div>
			          
			        </section>
				<?php $num++;

			} $mysqli->next_result(); 
		?>
		
      </div>
      
      <div class="mdl-layout__tab-panel" id="info">
      	<pre>
			<section class="section--center mdl-grid mdl-grid--no-spacing mdl-shadow--2dp">
	            <div class="mdl-card mdl-cell mdl-cell--12-col-desktop">
	              <div class="mdl-card__supporting-text mdl-grid mdl-grid--no-spacing">
	                <h2 class="mdl-cell mdl-cell--12-col"> <?php echo $_GET["q"] ?></h2>
	                <div class="section__text mdl-cell mdl-cell--10-col-desktop mdl-cell--6-col-tablet mdl-cell--3-col-phone">
						<?php 
						
						$words = preg_split('/\W/', $_GET['q'], 0, PREG_SPLIT_NO_EMPTY);
						$resultados = array();
			    	    foreach($words as $word){
						    // $query = "SELECT * FROM addressXword INNER JOIN word on addressXword.word_id = word.word_id WHERE word.word = \"{$_GET['q']}\""; 
					        $query = "SELECT * FROM totalCount"
							    . "	WHERE word LIKE \"%".$word."%\"";
							$result = $mysqli->query($query);
							while ($row = $result->fetch_assoc()){
								$resultados[$word] += intval($row["count"]);
							}
				    	    $mysqli->next_result(); 
			    	    }
    	    
						?>
	                  	<h4>Ocurrencias totales:<?php
		                  		foreach($resultados as $word => $count){
		                  			echo "<br>" . $word . " " . "({$count})";
		                  		} 
	                  		?>
	                  	</h4>
	                  	<h4>Lista de sitios: </h4>
	                  	<?php 
						
						$words = preg_split('/\W/', $_GET['q'], 0, PREG_SPLIT_NO_EMPTY);
						$resultados = array();
			    	    foreach($words as $word){
						    // $query = "SELECT * FROM addressXword INNER JOIN word on addressXword.word_id = word.word_id WHERE word.word = \"{$_GET['q']}\""; 
					        $query = "SELECT addressXword.address FROM addressXword"
							. " INNER JOIN word ON addressXword.word_id = word.word_id"
						    . "	WHERE word.word LIKE \"%".$word."%\"";
							$result = $mysqli->query($query);
							while ($row = $result->fetch_assoc()){
								$resultados[] = $row["address"];
							}
				    	    $mysqli->next_result(); 
			    	    }
    	    
						?>
	                  	<?php 
						foreach ($resultados as $url){ ?>
							<?php 
								if (strpos($url, 'http') !== false  || strpos($url, 'HTTP') !== false) {
			            			$url = $url;
								} else {
			            			$url = "//" . $url;
								}

			            	?>
							<a href="<?php echo $url ?>" class="mdl-button"><?php echo $url ?></a>
						<?php }
						$mysqli->next_result(); 
						?>
	                </div>
	              </div>
	           </div>
	        </section>
        </pre>
	  </div>
      
      <footer class="mdl-mega-footer">  
        <div class="mdl-mega-footer--bottom-section">
          <div class="mdl-logo">©<?php echo date("Y")?>
            Tecnologico de Costa Rica
          </div>
          <ul class="mdl-mega-footer--link-list">
            <li><a href="https://github.com/BasesDeDatos">Desarrollador</a></li>
          </ul>
        </div>
      </footer>
    </main>
  </div>
  <a href="index.php" id="view-source" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-color--accent mdl-color-text--accent-contrast">Nueva Busqueda</a>
  <script src="js/material.min.js"></script>
  <script src="js/TweenLite.min.js"></script>
  <script src="js/EasePack.min.js"></script>
  <script src="js/rAF.js"></script>
  <script src="js/demo-3.js"></script>
</body>
</html>

<?php $mysqli->close(); ?>

<?php 
    /** 
     * Borra las palabras comunes en ingles
     * @param string $input 
     * @return string 
     */  
    function removeCommonWords($input){
	 
	 	// EEEEEEK Stop words
		$commonWords = array('a','able','about','above','abroad','according','accordingly','across','actually','adj','after','afterwards','again','against','ago','ahead','ain\'t','all','allow','allows','almost','alone','along','alongside','already','also','although','always','am','amid','amidst','among','amongst','an','and','another','any','anybody','anyhow','anyone','anything','anyway','anyways','anywhere','apart','appear','appreciate','appropriate','are','aren\'t','around','as','a\'s','aside','ask','asking','associated','at','available','away','awfully','b','back','backward','backwards','be','became','because','become','becomes','becoming','been','before','beforehand','begin','behind','being','believe','below','beside','besides','best','better','between','beyond','both','brief','but','by','c','came','can','cannot','cant','can\'t','caption','cause','causes','certain','certainly','changes','clearly','c\'mon','co','co.','com','come','comes','concerning','consequently','consider','considering','contain','containing','contains','corresponding','could','couldn\'t','course','c\'s','currently','d','dare','daren\'t','definitely','described','despite','did','didn\'t','different','directly','do','does','doesn\'t','doing','done','don\'t','down','downwards','during','e','each','edu','eg','eight','eighty','either','else','elsewhere','end','ending','enough','entirely','especially','et','etc','even','ever','evermore','every','everybody','everyone','everything','everywhere','ex','exactly','example','except','f','fairly','far','farther','few','fewer','fifth','first','five','followed','following','follows','for','forever','former','formerly','forth','forward','found','four','from','further','furthermore','g','get','gets','getting','given','gives','go','goes','going','gone','got','gotten','greetings','h','had','hadn\'t','half','happens','hardly','has','hasn\'t','have','haven\'t','having','he','he\'d','he\'ll','hello','help','hence','her','here','hereafter','hereby','herein','here\'s','hereupon','hers','herself','he\'s','hi','him','himself','his','hither','hopefully','how','howbeit','however','hundred','i','i\'d','ie','if','ignored','i\'ll','i\'m','immediate','in','inasmuch','inc','inc.','indeed','indicate','indicated','indicates','inner','inside','insofar','instead','into','inward','is','isn\'t','it','it\'d','it\'ll','its','it\'s','itself','i\'ve','j','just','k','keep','keeps','kept','know','known','knows','l','last','lately','later','latter','latterly','least','less','lest','let','let\'s','like','liked','likely','likewise','little','look','looking','looks','low','lower','ltd','m','made','mainly','make','makes','many','may','maybe','mayn\'t','me','mean','meantime','meanwhile','merely','might','mightn\'t','mine','minus','miss','more','moreover','most','mostly','mr','mrs','much','must','mustn\'t','my','myself','n','name','namely','nd','near','nearly','necessary','need','needn\'t','needs','neither','never','neverf','neverless','nevertheless','new','next','nine','ninety','no','nobody','non','none','nonetheless','noone','no-one','nor','normally','not','nothing','notwithstanding','novel','now','nowhere','o','obviously','of','off','often','oh','ok','okay','old','on','once','one','ones','one\'s','only','onto','opposite','or','other','others','otherwise','ought','oughtn\'t','our','ours','ourselves','out','outside','over','overall','own','p','particular','particularly','past','per','perhaps','placed','please','plus','possible','presumably','probably','provided','provides','q','que','quite','qv','r','rather','rd','re','really','reasonably','recent','recently','regarding','regardless','regards','relatively','respectively','right','round','s','said','same','saw','say','saying','says','second','secondly','see','seeing','seem','seemed','seeming','seems','seen','self','selves','sensible','sent','serious','seriously','seven','several','shall','shan\'t','she','she\'d','she\'ll','she\'s','should','shouldn\'t','since','six','so','some','somebody','someday','somehow','someone','something','sometime','sometimes','somewhat','somewhere','soon','sorry','specified','specify','specifying','still','sub','such','sup','sure','t','take','taken','taking','tell','tends','th','than','thank','thanks','thanx','that','that\'ll','thats','that\'s','that\'ve','the','their','theirs','them','themselves','then','thence','there','thereafter','thereby','there\'d','therefore','therein','there\'ll','there\'re','theres','there\'s','thereupon','there\'ve','these','they','they\'d','they\'ll','they\'re','they\'ve','thing','things','think','third','thirty','this','thorough','thoroughly','those','though','three','through','throughout','thru','thus','till','to','together','too','took','toward','towards','tried','tries','truly','try','trying','t\'s','twice','two','u','un','under','underneath','undoing','unfortunately','unless','unlike','unlikely','until','unto','up','upon','upwards','us','use','used','useful','uses','using','usually','v','value','various','versus','very','via','viz','vs','w','want','wants','was','wasn\'t','way','we','we\'d','welcome','well','we\'ll','went','were','we\'re','weren\'t','we\'ve','what','whatever','what\'ll','what\'s','what\'ve','when','whence','whenever','where','whereafter','whereas','whereby','wherein','where\'s','whereupon','wherever','whether','which','whichever','while','whilst','whither','who','who\'d','whoever','whole','who\'ll','whom','whomever','who\'s','whose','why','will','willing','wish','with','within','without','wonder','won\'t','would','wouldn\'t','x','y','yes','yet','you','you\'d','you\'ll','your','you\'re','yours','yourself','yourselves','you\'ve','z','zero');
	 
		return preg_replace('/\b('. implode('|', $commonWords) .')\b/','',$input);
	}
	
	function elimina_acentos($text)
    {
        $text = htmlentities($text, ENT_QUOTES, 'UTF-8');
        $text = strtolower($text);
        $patron = array (
            //puntos y comas por guion
            // '/[\.,:]+/' => '',

            // Vocales
            '/\+/' => '',

            '/\&nbsp;/' => ' ',

            '/&agrave;/' => 'a',
            '/&agrave;/' => 'a',

            '/&egrave;/' => 'e',
            '/&igrave;/' => 'i',
            '/&ograve;/' => 'o',
            '/&ugrave;/' => 'u',
 
            '/&aacute;/' => 'a',
            '/&eacute;/' => 'e',
            '/&iacute;/' => 'i',
            '/&oacute;/' => 'o',
            '/&uacute;/' => 'u',
 
            '/&acirc;/' => 'a',
            '/&ecirc;/' => 'e',
            '/&icirc;/' => 'i',
            '/&ocirc;/' => 'o',
            '/&ucirc;/' => 'u',
 
            '/&atilde;/' => 'a',
            '/&etilde;/' => 'e',
            '/&itilde;/' => 'i',
            '/&otilde;/' => 'o',
            '/&utilde;/' => 'u',
 
            '/&auml;/' => 'a',
            '/&euml;/' => 'e',
            '/&iuml;/' => 'i',
            '/&ouml;/' => 'o',
            '/&uuml;/' => 'u',
 
            '/&auml;/' => 'a',
            '/&euml;/' => 'e',
            '/&iuml;/' => 'i',
            '/&ouml;/' => 'o',
            '/&uuml;/' => 'u',
 
            // Otras letras y caracteres especiales
            '/&aring;/' => 'a',
            '/&ntilde;/' => 'n',
 
            // Agregar aqui mas caracteres si es necesario
 
        );
 
        $text = preg_replace(array_keys($patron),array_values($patron),$text);
        return $text;
    }
    
    function limpiarTexto($input){
    	$input = elimina_acentos($input);
    	$input = removeCommonWords($input);
    	return $input;
    }
?>
