<?php
if (SUPRA !== 1) die('not way!');
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="cache-control" content="no-cache">
    <meta http-equiv="pragma" content="no-cache">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Конструктор страниц</title>

    <link rel="icon" href="images/favicons/favicon.png" type="image/x-icon"/>
    <link rel="apple-touch-icon" href="images/favicons/apple-touch-icon.png">
    <link rel="apple-touch-icon" sizes="72x72" href="images/favicons/apple-touch-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="114x114" href="images/favicons/apple-touch-icon-114x114.png">

    <link rel="stylesheet" href="css/bootstrap.css"/>
    <link rel="stylesheet" href="css/bootstrap-datepicker.css"/>
    <link rel="stylesheet" href="css/spectrum.css"/>
    <link rel="stylesheet" href="css/owl.carousel.css"/>
    <link rel="stylesheet" href="css/magnific-popup.css"/>
    <link rel="stylesheet" href="css/aos.css"/>
    <link rel="stylesheet" href="css/codemirror.css"/>
    <link rel="stylesheet" href="css/style.css"/>
    <link rel="stylesheet" href="css/main.css"/>
    <link rel="stylesheet" href="css/supra_icons.css"/>
    <link rel="stylesheet" href="css/icons.css"/>
    <link rel="stylesheet" href="css/preloader.css"/>

    <script src="http://ajax.googleapis.com/ajax/libs/webfont/1/webfont.js"></script>
    <script src="js/fonts.js"></script>
</head>
<body>
<script src="js/jquery-2.1.4.min.js"></script>
<div class="supra-preloader">
    <img src="images/logo.png" srcset="images/logo@2x.png 2x" alt="suprapagebuilder"/>
    <div class="progress-bar-s">
        <div class="progress">
            <div class="load"></div>
        </div>
    </div>
    <div class="rights">
        <p>&#169; 2016 <a href="https://versite.ru/" target="_blank">Versite.ru</a></p>
        <p>Version 4.6.3</p>
    </div>
</div>
<aside class="left supra black">
    <nav>
        <ul class="checking">
            <li id="sections" data-name="Блоки" class="item-check"><i class="supra icon-plus-square"></i></li>
            <li id="default-styles" data-name="Стиль по умолчанию" class="item-check"><i class="supra icon-edit-mode"></i></li>
            <li id="typography" data-name="Шрифт по умолчанию" class="item-check"><i class="supra icon-typography"></i></li>
            <li id="project-pages" data-name="Страницы проекта" class="item-check"><i class="supra icon-file-empty"></i></li>
            <li id="viewing-mobile" class="viewing item-check"><i class="supra icon-phone"></i></li>
            <li id="viewing-tablet" class="viewing item-check"><i class="supra icon-tablet"></i></li>
            <li id="viewing-desctop" class="viewing item-check"><i class="supra icon-screen"></i></li>
            <li id="redo" class="redo unactive"><i class="supra icon-redo"></i></li>
            <li id="undo" class="undo unactive"><i class="supra icon-undo"></i></li>
        </ul>
    </nav>
</aside>
<aside class="add-sections-items supra black">
    <ul>
		<?php
		foreach ($this->groups as $group_name => $items) {
			foreach ($items['sections'] as $id => $value) {
				echo '
					<li class="wrap-hover flex-center"
						data-group="' . $group_name . '"
						data-name="' . $value->name . '"
						data-id="' . $id . '"
						style="display: none;">
						<img src="' . $value->preview . '" />
						<i class="supra icon-plus add-section"></i>
					</li>

				';
			}
		}
		?>
    </ul>
</aside>
<aside class="control-panel supra black">
    <div class="title">
        <h3>Блоки</h3>
        <i class="supra bookmark"></i>
    </div>
    <ul class="sections">
		<?php
		foreach ($this->groups as $group_name => $value) {
			echo "<li data-group=\"$group_name\">"
				. $value['name'] .
				"</li>";
		}
		?>
    </ul>
    <div class="default-styles">
        <div class="btn-group" role="group" aria-label="...">
            <button data-id="light" type="button" class="supra-btn btn-default active"><i
                        class="supra icon-sun"></i><span>Light</span></button>
            <button data-id="dark" type="button" class="supra-btn btn-default"><i
                        class="supra icon-moon-small"></i><span>Dark</span></button>
        </div>
        <div class="message">
            <i class="supra icon-cross2"></i>
            <p>
                Lorem ipsum dolor sit amet,
                consectetur adipisicing elit.
                Alias assumenda consectetur
                consequatur cum debitis deserunt
                dolor doloribus eaque error,
                odio perferendis placeat possimus,
                quae sapiente sed sunt suscipit temporibus ullam!
            </p>
        </div>
        <ul id="dark"></ul>
        <ul id="light" class="active"></ul>
        <button type="button" class="supra-btn apply btn-primary">Применить</button>
    </div>
    <ul class="typography">
        <li>
            <button type="button" class="supra-btn btn-primary apply">Применить</button>
        </li>
    </ul>
    <div class="project-pages">
        <ul>
            <li>
                <i class="supra icon-file-add"></i>
                <span>Новая страница</span>
            </li>
        </ul>
        <div class="btn-ex-im-d">
            <div type="button" class="supra-btn new-project">
                <i class="supra icon-folder-plus"></i>
                <span>Новый</span>
            </div>
            <div type="button" class="supra-btn export">
                <i class="supra icon-folder-download"></i>
                <span>Сохранить</span>
            </div>
            <div type="button" class="supra-btn import">
                <i class="supra icon-folder-upload"></i>
                <span>Загрузить</span>
            </div>
            <div type="button" class="supra-btn btn-success download" data-href="http://google.com.ua">
                <i class="supra icon-file-zip"></i>
                <span>Скачать</span>
            </div>
        </div>
    </div>
</aside>

<div class="wrap-iframe flex-center">
    <div class="wrap viewing-desctop">
        <label>
            <span class="width" contenteditable="true"></span> x <span class="height" contenteditable="true"></span>
            <i class="rotate icon-undo2"></i>
        </label>
        <iframe id="main" src="./main.html"></iframe>
    </div>
</div>

<div id="modal-container" class="supra"></div>
<div id="modal-project-container" class="supra"></div>
<div id="modal-form-container" class="supra font-style-supra"></div>
<div id="modal-thumb" class="supra"></div>
<i id="collapse-popup-thumb" class="icon-launch-2 supra icon-size-m"></i>

<script>
	<?php
	echo "sectionsPreview=" . json_encode($this->groups) . ";\n";
	echo "typographyFonts=" . json_encode($this->fontsDropdown) . ";\n";
	?>
</script>
<!-- Latest 3.2.x goodshare.js minify version from jsDelivr CDN -->
<script src="https://cdn.jsdelivr.net/jquery.goodshare.js/3.2.8/goodshare.min.js"></script>
<script src="js/jquery.magnific-popup.min.js"></script>
<script src="js/jquery.nicescroll.min.js"></script>
<script src="js/jquery.mask.js"></script>
<script src="js/jquery.vide_builder.min.js"></script>
<script src="js/aos.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCByts0vn5uAYat3aXEeK0yWL7txqfSMX8"></script>

<script src="js/bootstrap.min.js"></script>
<script src="js/spectrum.js"></script>

<script src="js/codemirror.js"></script>
<script src="js/javascript.js"></script>
<script src="js/css.js"></script>
<script src="js/htmlmixed.js"></script>
<script src="js/xml.js"></script>

<script src="js/constructForm.js"></script>
<script src="js/download.js"></script>
<script src="js/options.js"></script>
<script src="js/editor.text.js"></script>
<script src="js/modal.js"></script>
<script src="js/controls.js"></script>
<script src="js/page.js"></script>
<script src="js/suprabuilder.js"></script>
<script src="js/supra-main.js"></script>
<script src="js/bootstrap-datepicker.min.js"></script>


<!--<script src="js/builder.min.js"></script>-->
</body>
</html>
