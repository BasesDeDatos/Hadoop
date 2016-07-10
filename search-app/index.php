<!DOCTYPE html>
<html lang="en" class="no-js">

<head>
  <?php require_once( "header.php"); ?>

  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" type="text/css" href="css/normalize.css" />
  <link rel="stylesheet" type="text/css" href="css/demo.css" />
  <link rel="stylesheet" type="text/css" href="css/component.css" />
  <link href='//fonts.googleapis.com/css?family=Raleway:200,400,800|Londrina+Outline' rel='stylesheet' type='text/css'>
  <!--[if IE]>
        	<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->
</head>
<body>
  <header class="mdl-layout__header mdl-layout__header--scroll mdl-color--primary" style="position: absolute">
    <div class="mdl-l/yout--large-screen-only mdl-layout__header-row">
      <h3>Search-APP</h3>
    </div>
  </header>
  <div class="container demo-3">
    <div class="content">
      <div id="large-header" class="large-header">
        <canvas id="demo-canvas"></canvas>
        <h1 class="main-title"><span>Search-APP</span>
	        <section class="section--center mdl-grid mdl-grid--no-spacing mdl-shadow--2dp">
	            <form action="result.php" method="GET" class="mdl-cell mdl-cell--12-col-desktop" style="background-color: #F9F1E9;">
	              <div class="mdl-card__supporting-text" style="width: 100%;">
	                <input type="text"
	                    name="q" class="mdl-textfield__input" 
	                    style="color: #040404; font: 13.3333px Arial; text-align: center;" 
	                    placeholder="Buscar">
	              </div>
	     <div class="mdl-card__actions" style="text-align: center;">
	                <button type="submit" href="#" style="" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-color--accent mdl-color-text--accent-contrast" data-upgraded=",MaterialButton,MaterialRipple">Buscar<span class="mdl-button__ripple-container"><span class="mdl-ripple is-animating" style="width: 207.056px; height: 207.056px; transform: translate(-50%, -50%) translate(53px, 16px);"></span></span></button>
	              </div>
	            </form>
	        </section>
	    </h1>
      </div>
    </div>
  </div>
  <!-- /container -->
  <footer class="mdl-mega-footer" style="
            height: 50px;
            padding: 0 15px;
            position: fixed;
            width: 100%;
            bottom: 0;
            z-index: 9;
        ">
    <div class="mdl-mega-footer--bottom-section">
      <div class="mdl-logo">©
        <?php echo date( "Y")?> Tecnologico de Costa Rica
      </div>
      <ul class="mdl-mega-footer--link-list">
        <li><a href="https://github.com/BasesDeDatos">Desarrollador</a></li>
      </ul>
    </div>
  </footer>
  <script src="js/TweenLite.min.js"></script>
  <script src="js/EasePack.min.js"></script>
  <script src="js/rAF.js"></script>
  <script src="js/demo-3.js"></script>
</body>

</html>