<?php 

/** 
 * Clase para gestionar las conexesiones y peticiones a servidores remotos 
 */  
 
// header( 'Content-type: text/html; charset=utf-8' );


set_time_limit(0);
class HttpSpider {
    private $curl;  
    private $cookie;  
    private $cookie_path="/cookies";  
    private $id;  
    public $link_actual;
    public $links_visitados = array();
    public $words_actual = array();
    public $contadorLinks = 0;
    public $cantidadLinks = 0;
    private $doc;
    private $contadorPalabras = 0;
    public $niveles = 0;
    public $contadorNivel = 0;

  
    public function __construct($niveles=1) {  
        $this->id = time();  
        $this->niveles = $niveles;

    }
	
    /** 
     * Inicializa el objeto curl con las opciones por defecto. 
     * Si es null se crea 
     * @param string $cookie a usar para la conexion 
     */  
    public function init($cookie=null) {  
        
        if($cookie)  
            $this->cookie = $cookie;  
        else  
            $this->cookie = $this->cookie_path . $this->id;  
  
        $this->curl=curl_init();  
        curl_setopt($this->curl, CURLOPT_USERAGENT, $this->getRandomUserAgent());  
        curl_setopt($this->curl, CURLOPT_HEADER, false);  
        curl_setopt($this->curl, CURLOPT_COOKIEFILE, $this->cookie);  
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, array("Accept-Language: es-es,en"));  
        curl_setopt($this->curl, CURLOPT_COOKIEJAR, $this->cookie);  
        curl_setopt($this->curl, CURLOPT_COOKIESESSION , true);  
        curl_setopt($this->curl, CURLOPT_COOKIE, true);  
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, false);  
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, false);  
        curl_setopt($this->curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);  
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER,true);  
        curl_setopt($this->curl, CURLOPT_CONNECTTIMEOUT, 5);  
        curl_setopt($this->curl, CURLOPT_TIMEOUT, 60);  
        curl_setopt($this->curl, CURLOPT_AUTOREFERER, TRUE);  
        
        $this->doc = new DOMDocument();
        libxml_use_internal_errors(true); //don't show warnings
    }  

	/** 
     * Genera un User Agent de manera aleatoria
     */  
	private function getRandomUserAgent(){
		$userAgents = array(
			"Mozilla/5.0 (Windows; U; Windows NT 5.1; en-GB; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6",
			"Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1)",
			"Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; .NET CLR 1.1.4322; .NET CLR 2.0.50727; .NET CLR 3.0.04506.30)",
			"Opera/9.20 (Windows NT 6.0; U; en)",
			"Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; en) Opera 8.50",
			"Mozilla/4.0 (compatible; MSIE 6.0; MSIE 5.5; Windows NT 5.1) Opera 7.02 [en]",
			"Mozilla/5.0 (Macintosh; U; PPC Mac OS X Mach-O; fr; rv:1.7) Gecko/20040624 Firefox/0.9",
			"Mozilla/5.0 (Macintosh; U; PPC Mac OS X; en) AppleWebKit/48 (like Gecko) Safari/48"       
		);
		$random = rand(0,count($userAgents)-1);

		return $userAgents[$random];
	}

	/** 
     * Establece en que ruta se guardan las cookies. 
     * Importante: El usuario de apache debe tener acceso de lectura y escritura 
     * @param string $path 
     */  
    public function setCookiePath($path){  
        $this->cookie_path = $path;  
    }

	/** 
     * Envía una peticion GET a la URL especificada 
     * @param string $url 
     * @param bool $header 
     * @param bool $follow 
     * @return string Respuesta generada por el servidor 
     */  
    public function get($url) {  
        $this->init();  
        $this->link_actual = $url;
        $this->links_visitados[] = $url;
        $this->contadorLinks++;
        
        curl_setopt($this->curl, CURLOPT_URL, $url);  
        curl_setopt($this->curl, CURLOPT_POST, false);  
        curl_setopt($this->curl, CURLOPT_HEADER, false);  
        curl_setopt($this->curl, CURLOPT_REFERER, '');  
        curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, true);  
        $result=curl_exec ($this->curl);  
        if($result === false){  
            echo curl_error($this->curl);  
        }  
		
        $this->_close();  
        $this->doc->loadHTML($result);

        return $result;  
    }  
	
	/** 
     * Obtiene los datos de la página por medio de una expresion regular
     * @param string $url 
     * @param string[] $cookies 
     * @return string arreglo con los datos de los formularios 
     */ 
	public function getDataForm($url, $cookies) {  
        $this->init();  
		
		if ($cookies){
			$headers = array();
			foreach ($cookies as $name => $value) {  
				$headers[] = 'Cookie: ' . $name.'='.$value;
			}
			curl_setopt($this->curl, CURLOPT_HTTPHEADER, $headers);
		}

        curl_setopt($this->curl, CURLOPT_URL, $url);  
        curl_setopt($this->curl, CURLOPT_POST,false);  
        curl_setopt($this->curl, CURLOPT_REFERER, '');  
        
		$result = curl_exec ($this->curl);  
        if($result === false){  
            echo curl_error($this->curl);  
        }  
		
		// Se obtienen los datos de los inputs
// 		preg_match_all("()siU", $result, $matches_names);
		// Se obtienen los datos de los selects
		preg_match_all("(selected=\"selected\">(.*)</option>)siU", $result, $matches_selects);

		// se unen los resultados
		$resultados = array_merge ($matches_selects[1], $matches_names[1], array($url));
		
		foreach($resultados as $key => $resultado){
			$new_string = str_replace("&#39;", "'", $new_string);
			$new_string = str_replace('value="', "", $new_string);
			$new_string = str_replace('/>      </p>', "", $new_string);
			$resultados[$key] = htmlspecialchars_decode($new_string);
		}

        $this->_close();  
        return $resultados;  
    }  
	
	/** 
     * Obtiene el texto contenido en todos lo tags pasado por parametro
     * @param DOMDocument $doc 
     * @param string $tag 
     * @return string[] lista con los textos encontrado en la página
     */ 
	public function getWordsAndLinks() { 
	   
		$resultado = $this->getChildsData($this->doc, array()); //Llamada a funcion 
		
		$datos["title"] = $resultado["title"];
		$this->title_actual = $resultado["title"];
		$datos["words"] =  $resultado["words"];
		$this->words_actual = $resultado["words"];
		
		if($this->contadorNivel+1 < $this->niveles){
    		$datos["links"] = $this->syndicationURLs();
    		$this->cantidadLinks += count($datos["links"]);
		} else {
		    $datos["links"] = array();
		}
		return $datos;
    }  
    
    /** 
     * Función recursiva para obtener las etiquetas con texto
     * @param DOMDocument | DOMNode $node 
     * @param string[] $datos 
     * @return string[] lista con los textos encontrado en la página
     */ 
    private function getChildsData($node, $datos){
	    if (!$node->childNodes->length == 0){
    	    foreach ($node->childNodes as $childNode){
                //Se valida si el nodo contiene texto y no sea una tira vacia
    			if ($childNode->nodeName == "#text" && !ctype_space($childNode->nodeValue) ){
    			    $text =  $this->limpiarTexto($childNode->nodeValue);

    			    //El name del nodo actual = "#text" entonces validamos el name del padre
    			    //$datos = array("words")
            	    $words = preg_split('/\W/', $text, 0, PREG_SPLIT_NO_EMPTY);
            	    foreach($words as $word){
    				    $datos["words"][] = $word;
            	    }

    			}else if ($childNode->nodeName == "title"){
    			     $text =  $childNode->nodeValue;
    			    //$datos = array("words")
            	    $words = preg_split('/\W/', $text, 0, PREG_SPLIT_NO_EMPTY);
            	    foreach($words as $word){
    				    $datos["title"] .= $word . " ";
            	    }
    			}
    			
    			//Llamado Recursivo
    			$datos = $this->getChildsData($childNode, $datos);
    		}
	    }
	    
		return $datos;
	}

	/** 
     * Obtiene los datos de la página por medio de una expresion regular
     * @param string $file 
     * @param string $mode 
     * @param string[] $datos 
     */ 
	public function saveWords($file, $mode) {  
		$fp = fopen($file, $mode);

        $titulo = $this->title_actual;
        $titulo = trim($titulo) == "" ? $this->link_actual : $titulo;
        
        foreach ($this->words_actual as $word) {
            $txt .= "{$word}<##>{$this->link_actual}<##>{$titulo}\n";
        	$this->contadorPalabras++;
        }
        
        echo '<p class="progreso">';
        // echo  "palabra #: {$this->contadorPalabras} \t| {$word}\t|\t{$this->link_actual}\t|\t{$titulo}<br>";
        $restantes = $this->cantidadLinks - $this->contadorLinks;
        echo  "enlaces recorridos = {$this->contadorLinks}".
            "\t|\tenlaces restantes = {$restantes}".
            "\t|\tCantidad de palabras = {$this->contadorPalabras}".
            "\t|\tNivel " . ($this->contadorNivel+1) . " de {$this->niveles}";
    	echo "</p>";
    	
    	ob_flush();
        flush();
        usleep(10);
        
        fwrite($fp, $txt);
		fclose($fp);
	}
	
    public function syndicationURLs() {
        // Pro Tip #1: Use the DOMDocument extension
        // http://bit.ly/1d7duDg
        $link_ret = array();
        $SITEURL = $this->link_actual; // Set to https://www.perpetual-beta.org/ in my case
        $base_url = rtrim($SITEURL, '/'); // Remove trailing slash
        $base_url_parts = parse_url($base_url);

        // $link_ret["base_url_parts"] = $base_url_parts;
        // Method #1: getElementsByTagName
        // Find the link tags
        $links = $this->doc->getElementsByTagName('a');
        foreach($links as $link) {
            // Get the value of the href attribute
            $href = $link->getAttribute('href');
            $href = trim($href, '/'); // Remove slash
            // De-construct the UR(L|I)
            $url_parts = parse_url($href);
            // $link_ret[$href] = $url_parts;
            
            // Is it a relative link (URI)?
            if (!isset($url_parts['host']) || ($url_parts['host'] == '')) {
                $base_url_arrray = explode("/", $base_url);
                $path = $url_parts['path'];
                
                if (strpos($path, '../') !== false) {
                    $base_url = implode("/", array_slice($base_url_arrray, 0, -2));
                    $href = substr($path, 3);
                } 
                else if (strpos($path, './') !== false) {
                    $base_url = implode("/", array_slice($base_url_arrray, 0, -1)) . "/" ;
                    $href = substr($path, 2);
                }
                
                $enlace = $base_url . "/" . $href;
                if (!in_array($href, $link_ret) && !in_array($href, $this->links_visitados)){
                    $link_ret[] = $enlace;
                }

                // isset($url_parts['scheme']) && ($url_parts['scheme'] != "mailto")
                // It is, so prepend our base URL
                // $link_ret[] =  $base_url . $href;
                // $link_ret[$href] =  $url_parts;
            }
            else{
                if (!in_array($href, $link_ret) && !in_array($href, $this->links_visitados)){
                    $link_ret[] = $href;
                }
            }
        }
        return $link_ret;
    
    }
    
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
    	$input = $this->elimina_acentos($input);
    	$input = $this->removeCommonWords($input);
    	return $input;
    }

    /**
     * Cierra la conexión 
     */  
    private function _close() {  
        curl_close($this->curl);  
    }  
    public function close(){  
        if(file_exists($this->cookie))  
            unlink($this->cookie);  
    }  
} 

/**
* SE INSTANCIA EL SPIDER
*/
?>
<!-- libreria jquery -->
<!--<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">-->
<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<!--<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script> -->
<script type="text/javascript">
    var callback = function(){
			$("p").not(':last').remove();
		};
	var interval = setInterval(callback, 1000);
</script>
<?php
echo '<style type="text/css">.progreso{background: white; position:absolute; left:0; right:0; text-align:center;}</style>';
ob_flush();
flush();

$spider = new HttpSpider(2);  //Parametro los niveles

$links = explode("\n", file_get_contents('direcciones.txt'));

$spider->cantidadLinks = count($links);
// var_dump($links);
$global_links = array();

    while($spider->contadorNivel < $spider->niveles){
        try{
            $new_links = array();
            foreach($links as $link){
                //echo "debug: " . !filter_var($link, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED );
                // if (!filter_var($link, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED) == false) {
                    $datosHTML = $spider->get($link);  
    
                	$data = $spider->getWordsAndLinks();
                	//$data = array("title" => string, "links" => string[], "words" => string[])
                    $spider->saveWords("pruebaExportar.txt", "a"); //Se guardan los resultados en un archivo
                    
                    $new_links = array_merge($new_links, $data["links"]); //lista de los links del link actual
                // } 
            }
            $links = $new_links;
            $global_links = array_merge($global_links, $links); //Lista de los links visitados
            $spider->contadorNivel++;
            $i++;
            
        }
        catch(Exception $e){
        	echo "\tException <br> {$e} <br>";
        }

    }
    
    echo "<pre>";
	echo var_dump($global_links);
	echo "</pre><br>";

echo "<br>FINALIZADO";
$spider->close();  
?>
<script type="text/javascript">
	var interval = clearInterval(interval);
</script>
