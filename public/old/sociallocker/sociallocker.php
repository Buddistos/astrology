<?php
$version = '1.2.5';

$copyrights = '
Copyright © 2012-текущий
Автор: MakeDreamProfits, Eugene Bos aka TheGP (Евгений Бос)
Сайт продукта: makedreamprofits.ru/sl/

Информацию по доработкам, неисправностям и идеям присылайте через форму на
makedreamprofits.ru/support/

SocialLocker(Социальный Замок) - это коммерческое программное обеспечение. Любое распространение строго
запрещено. Нарушители будут преследоваться в судебном порядке.
';

if (isset($_GET['version'])) {
    echo $version;
    exit();
}

if (isset($_GET['sl_session_id'])) { // 1.0.7

    session_id((string)$_GET['sl_session_id']);

}

session_start();

// настройки которые зашифрованы через base64 (для уменьшения кода)
$settings_base64 = array(
    'title1', 'subtitle1', 'title2', 'title2', 'subtitle2', 'closed_area_text_closed', 'title1_opened', 'subtitle1_opened', 'title2_opened', 'title2_opened', 'subtitle2_opened', 'closed_area_text_opened',
);

// все настройки за исключением чекбоксов
$settings_default = array(
    'dir_url' => '/sociallocker/',
    'width' => 550,
    'height' => 300,
    'type' => 'buttons',

    'color_title' => 'ca0000',
    'color_subtitle' => '6f6f6f',
    'color_closed_area_text' => '000000',

    'color_background' => 'ffe3ab',
    'color_background_opened' => 'bbff80',

    //'copyrights' => 'on',

    'color_closed_area_background' => 'ffffff',
    'color_closed_area_border_closed' => 'ca0000',
    'color_closed_area_border_opened' => '24ba01',

    'title1' => '1. Чтобы получить доступ, кликните на одну из кнопок ниже:',
    'subtitle1' => 'после этого под кнопками появится обещанный вам подарок',
    'title2' => '2. Под данными строчками появится обещанный вам подарок:',
    'subtitle2' => '',

    //'closed_area_text_opened' => 'Заполните это поле в настройках соц. замка!',
    //'closed_area_text_closed' => 'Здесь появится ссылка на ваш подарок',

    'title1_opened' => '1. Если кликните хотя бы еще на одну кнопку - будете героем!:)',
    'subtitle1_opened' => 'а подарок уже открыт и его можно найти чуть ниже',
    'title2_opened' => '2. Спасибо!:) Забирайте ваш заслуженный подарок:',
    'subtitle2_opened' => '',
);


// PARAMETRS FROM HTML CODE
if(!function_exists('json_decode'))
{
    function json_decode($data, $array = FALSE)
    {
        $json = new Services_JSON();
        if (FALSE === $array) {
            return $json->decode($data);
        } else {
            return objectToArray($json->decode($data));
        }
    }
}



//print_r(base64_decode($_POST['settings'])); exit();

if (isset($_POST['settings'])) {
    //$_POST['settings'] = stripslashes($_POST['settings']);

    // расшифровываем и распаковываем
    $settings = json_decode(base64_decode($_POST['settings']), TRUE);

    // делаем расшифровку внутренних элементов
    foreach ($settings as $key => $value) if (FALSE === is_array($value) && in_array($key, $settings_base64)) {
        $settings[$key] = base64_decode($value);
    }

    $settings = watchArray($settings);

    $_SESSION['sociallocker'][$settings['id']] = $settings; //
    $_SESSION['sociallocker'][$settings['id']]['user_ip'] = $_SERVER['REMOTE_ADDR']; // 1.1.7 сохраняем ip адрес пользователя

} elseif (
        isset($_SESSION['sociallocker']) &&
        isset($_GET['id']) &&
        isset($_SESSION['sociallocker'][$_GET['id']])
    ) {

    $settings = watchArray($_SESSION['sociallocker'][$_GET['id']]);

    // 1.1.7
    // если ip отличен от того, с которым загружали первый раз скрипт (защита от XSS атак)
    if ($_SERVER['REMOTE_ADDR'] != $_SESSION['sociallocker'][$settings['id']]['user_ip']) { // 1.1.7

        session_unset(); // сжигаем все переменные чтобы хакерам не осталось (минусы - пользователя может разлогинить на сайте)

        // и начинаем перезагрузку скрипта заново
        header("Content-type: text/javascript; charset=utf-8");
        exit('
            jQuery.ajax({
                type: "POST",
                url: "' . $settings['dir_url'] . 'sociallocker.php?type=js",
                data: {
                    "settings": sociallocker' . $_GET['id'] . '
                },
                dataType: "script"
            });
        ');

    }

} elseif (isset($_GET['type'])) {
    $message = 'Замок не загружен. Попробуйте обновить страницу, если не заработает - напишите администратору сайта.';
    if ('html' == $_GET['type']) {
        exit($message); // 1.0.7
    } else {
        exit('/* ' . $message . ' */'); // 1.0.7
    }
} else {
    exit('[SocialLocker] Социальный Замок: Ага, это я. Удачно приземлился прямо на вашем сайте:) Больше мне нечего сказать...'); // 1.0.7
}


// добавляем дефолтные настройки если какие то незаданы
foreach ($settings_default as $key => $value) {
    if (!isset($settings[$key])) {
        $settings[$key] = $value;
    }
}

/*
if ((' ' == 'without_facebook') && isset($settings['tabs']['facebook_like'])) {
    unset($settings['tabs']['facebook_like']);
}
*/

//print_r($settings); exit();


if (isset($_COOKIE['sociallocker'][$settings['id']])) {
    $settings['lock'] = 0;
} else {
    $settings['lock'] = 1;
}


if (0 == $settings['lock']) {
    // прячем уже нажатые кнопки (удаляем их чтобы они не отобразились)
    if (isset($settings['hide_used']) && isset($_COOKIE['sociallocker'][$settings['id']])) {
        foreach ($_COOKIE['sociallocker'][$settings['id']] as $networks_action => $null) {
            if (isset($settings['tabs'][$networks_action])) {
                unset($settings['tabs'][$networks_action]);

            // in analytics pushes "comment" but in settings array its "commentS"
            } elseif (isset($settings['tabs'][$networks_action . 's'])) {
                unset($settings['tabs'][$networks_action . 's']);

            // in analytics pushes "twitter_follow" but in settings array its "twitter_subscribe"
            } elseif ('twitter_follow' == $networks_action && isset($settings['tabs']['twitter_subscribe'])) {

                unset($settings['tabs']['twitter_subscribe']);

            // in analytics pushes "googleplus_like" but in settings array its "googleplus"
            } elseif ('googleplus_like' == $networks_action && isset($settings['tabs']['googleplus'])) {

                unset($settings['tabs']['googleplus']);

            }
        }
    }
}



// если старый код на сайте до 1.0.X (скорее всего будет 1.0.8)
// изменено на версии 1.0.7

if (!isset($settings['gifts'])) { // !isset($settings['gift']) &&
    $settings['gifts'] = array(
        0 => array(
            'actions' => 1,
            'content' => sl_proccess_jsstring($settings['closed_area_text_opened']), // 1.0.9

            'gift_precontent' => (isset($settings['closed_area_text_closed'])) ? $settings['closed_area_text_closed'] : '',
        ),
    );
} else {
    // 1.0.8 немного обрабатываем данные в подарках чтобы они ничего нам не сломали...
    foreach ($settings['gifts'] as $key => $gift) {
        $settings['gifts'][$key]['content'] = sl_proccess_jsstring($gift['content']); // 1.0.9
    }
}
/*
if (!isset($settings['gift_actions'])) { // !isset($settings['gift']) &&
    $settings['gifts'] = array(
        0 => array(
            'actions' => 1,
            'content' => str_replace("'", "\\'", str_replace("\n", '', str_replace("\r", '', $settings['closed_area_text_opened']))),
            'closed_area_text_closed' => (isset($settings['closed_area_text_closed'])) ? $settings['closed_area_text_closed'] : '',
        ),
    );
} else {
    // 1.0.8
    $settings['gifts'] = array();
    foreach ($settings['gift_actions'] as $key => $gift_actions) {
        $settings['gifts'][] = array(
            'actions' => $gift_actions,
            'content' => str_replace("'", "\\'", str_replace("\n", '', str_replace("\r", '', $settings['gift_content'][$key]))),
            'closed_area_text_closed' => (isset($settings['gift_precontent'][$key])) ? $settings['gift_precontent'][$key] : '',
        );
    }
}
*/


//print_r($settings['gifts']);


if ('html' == $_GET['type']):
    header('Content-Type: text/html; charset=utf-8'); // 1.0.6

//print_r($settings['tabs']);exit();
?>

<div class="sociallocker sociallocker<?php echo $settings['id']; ?>">

    <div class="sl_header">
        <div class="sl_caption sl_caption1">
            <div class="sl_title"><?php echo $settings['title1']; ?></div>
            <div class="sl_subtitle"><?php echo $settings['subtitle1']; ?></div>
        </div>

        <?php if ('buttons' == $settings['type']): ?>

            <div class="sl_social sl_buttons">
                <?php foreach ($settings['tabs'] as $network_name => $params): ?>

                        <?php if ('facebook_like' == $network_name): ?>

                            <div class="sl_facebook_like sl_button">
                                <div class="fb-like" data-href="<?php echo $settings['page_url']; ?>" data-send="false" data-width="90" data-show-faces="false" data-layout="button_count"></div>
                            </div>

                        <?php elseif ('twitter_tweet' == $network_name): ?>

                            <div class="sl_twitter_tweet sl_button">
                                <a href="http://twitter.com/share" class="twitter-share-button" data-url="<?php echo $settings['page_url']; ?>" data-counturl="<?php echo str_replace('http://', '', (isset($settings['tabs']['twitter_tweet']['last_url']) && '' != trim($settings['tabs']['twitter_tweet']['last_url'])) ? $settings['tabs']['twitter_tweet']['last_url'] : $settings['page_url']); // 1.0.2 ?>" data-text="<?php echo htmlspecialchars($settings['tabs']['twitter_tweet']['text']); ?>" data-count="<?php echo (isset($settings['tabs']['twitter_tweet']['show_count'])) ? 'horizontal' : 'none'; ?>" <?php echo ('' != trim($settings['tabs']['twitter_tweet']['related'])) ? 'data-related="' . $settings['tabs']['twitter_tweet']['related'] . '"' : ''; ?> data-lang="ru">Твитнуть</a>
                            </div>

                        <?php elseif ('twitter_subscribe' == $network_name): ?>

                            <div class="sl_twitter_follow sl_button">
                                <a href="https://twitter.com/<?php echo $settings['tabs']['twitter_subscribe']['username']; ?>" class="twitter-follow-button" data-show-count="false" data-lang="ru" <?php echo (isset($settings['tabs']['twitter_subscribe']['show_username'])) ? '' : 'data-show-screen-name="false"'; ?>>Читать <?php echo (isset($settings['tabs']['twitter_subscribe']['show_username'])) ? '@' . $settings['tabs']['twitter_subscribe']['username'] : ''; ?></a>
                            </div>

                        <?php elseif ('googleplus' == $network_name): ?>

                            <div class="sl_googleplus_like sl_button">
                                <div class="g-plusone" data-size="medium" <?php echo (isset($settings['tabs']['googleplus']['show_count'])) ? '' : 'data-annotation="none"'; ?> data-callback="googleplus_callback<?php echo $settings['id']; ?>"  data-href="<?php echo $settings['page_url']; ?>"></div>
                            </div>

                        <?php elseif ('vkontakte_like' == $network_name): ?>

                            <div id="sl_vkontakte_like" class="sl_vkontakte_like sl_button"></div>

                        <?php elseif ('vkontakte_subscribe' == $network_name): ?>

                            <div id="sl_vkontakte_subscribe" class="sl_vkontakte_subscribe sl_button"></div>

                        <?php elseif ('vkontakte_share' == $network_name): ?>

                            <div id="sl_vkontakte_share" class="sl_vkontakte_share sl_button"></div>

                        <?php elseif ('facebook_send' == $network_name): ?>

                            <div id="sl_facebook_send" class="sl_facebook_send sl_button">
                                <div class="fb-send" data-href="<?php echo $settings['page_url']; ?>"></div>
                            </div>

                        <?php elseif ('mail_like' == $network_name): /* 1.1.4 */ ?>

                            <div class="sl_mail_like sl_button">
                                <a target="_blank" class="mrc__plugin_uber_like_button" href="http://connect.mail.ru/share?url=<?php echo urlencode($settings['page_url']); ?>" data-mrc-config="{<?php echo (isset($settings['tabs']['mail_like']['show_count'])) ? '' : "'nc' : '1', "; ?>'cm' : '<?php echo (int)$settings['tabs']['mail_like']['text_moimir']; ?>', 'ck' : '<?php echo (int)$settings['tabs']['mail_like']['text_odnoklassniki']; ?>', 'sz' : '20', 'st' : '<?php echo (int)$settings['tabs']['mail_like']['border_type']; ?>', 'tp' : '<?php echo $settings['tabs']['mail_like']['what_buttons']; ?>'}">Нравится</a>
                            </div>

                        <?php endif; ?>




                <?php endforeach; ?>
            </div>

        <?php else: ?>

            <div class="sl_social sl_comments">

                <?php foreach ($settings['tabs'] as $network_name => $params): ?>

                        <?php if ('facebook_comments' == $network_name): ?>

                            <!-- фэйсбук -->
                            <div class="sl_facebook_comment">
                                <div class="fb-comments" data-href="<?php echo $settings['page_url']; ?>" data-num-posts="<?php echo (int)$settings['tabs']['facebook_comments']['count']; ?>" data-width="<?php echo ($settings['width'] - 10); ?>"></div>
                            </div>

                        <?php elseif ('vkontakte_comments' == $network_name): ?>

                            <!-- вконтакте -->
                            <div id="sl_vkontakte_comment" class="sl_vkontakte_comment"></div>

                        <?php endif; ?>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>

        <div class="sl_caption sl_caption2">
            <div class="sl_title"><?php echo $settings['title2']; ?></div>
            <div class="sl_subtitle"><?php echo $settings['subtitle2']; ?></div>
        </div>
    </div>

    <a name="sl<?php echo $settings['id']; ?>_closed_area"></a>

    <?php foreach ($settings['gifts'] as $key => $gift): ?>
        <div class="sl_closed_area sl_show_content_here gift_<?php echo $key; ?>_content"></div>


        <?php if (isset($gift['gift_precontent']) && '' != $gift['gift_precontent']): ?>
            <div class="sl_closed_area sl_closed_area2 gift_<?php echo $key; ?>_precontent">
                <?php echo $gift['gift_precontent']; ?>
            </div>
        <?php endif; ?>

    <?php endforeach; ?>


    <?php if (isset($settings['copyrights'])): ?>
        <div class="sl_copyright"><a href="<?php echo (isset($settings['copyrights_link'])) ? ((FALSE === strpos($settings['copyrights_link'], 'http://')) ? 'http://' . $settings['copyrights_link'] : $settings['copyrights_link']) : 'http://makedreamprofits.ru/sl/video/'; /* 1.2.1 */ ?>" target="_blank">Защищено "Социальным Замком"</a></div>
    <?php endif; ?>
</div>


<?php elseif ('js' == $_GET['type']): header("Content-type: text/javascript; charset=utf-8"); ?>

// 1.0.7 begin
sl_session_id<?php echo (int)$settings['id']; ?> = '<?php echo session_id(); ?>';

var url_postfix<?php echo (int)$settings['id']; ?> = '';
// если замок не один, добавляем имя открытой сессии персонально для этого замка(чтобы они друг друга не перезаписывали)
//if ('undefined' != typeof(sl_sociallockers) && 1 < sl_sociallockers.length) { // 1.1.0
    url_postfix<?php echo (int)$settings['id']; ?> = '&sl_session_id=' + sl_session_id<?php echo (int)$settings['id']; ?> + url_postfix<?php echo (int)$settings['id']; ?>;
//}
// 1.0.7 end


if ('undefined' == typeof(sociallocker<?php echo (int)$settings['id']; ?>_loaded)) { /* 1.0.1 - если замок уже загружен - ничего не делаем второй раз */

    var sociallocker<?php echo (int)$settings['id']; ?>_loaded = true;

    var sociallocker<?php echo (int)$settings['id']; ?>_actions_done = 0;
    var sociallocker<?php echo (int)$settings['id']; ?>_actions_collect = 0;
    var last_gift_opened_index<?php echo (int)$settings['id']; ?> = -1;

    <?php if (isset($settings['tabs']['facebook_send'])): /* 1.2.2 */ ?>
    var fbSendWatcher_count = 0;
    var fbSendWatcher = null;
    <?php endif; ?>


    // подключаем стили
    // ::TRICKY:: через jquery в ie7 работать не будет
    var cssNode = document.createElement('link');
    cssNode.type = 'text/css';
    cssNode.rel = 'stylesheet';
    cssNode.href = '<?php echo $settings['dir_url']; ?>sociallocker.php?type=css&id=<?php echo (int)$settings['id']; ?>';
    cssNode.media = 'screen';
    //cssNode.title = 'dynamicLoadedSheet'; 1.0.6
    document.getElementsByTagName("head")[0].appendChild(cssNode);



    <?php if (isset($settings['tabs']['vkontakte_like']) || isset($settings['tabs']['vkontakte_subscribe']) || isset($settings['tabs']['vkontakte_comments'])): ?>
    function vkontakte_init<?php echo $settings['id']; ?>() {
        if ('undefined' == typeof(VK) || 'undefined' === typeof(VK.init)) { // 1.0.1
            setTimeout('vkontakte_init<?php echo $settings['id']; ?>', 500);
        } else {
            <?php if (isset($settings['tabs']['vkontakte_like']) || isset($settings['tabs']['vkontakte_comments'])): ?>
                if (null == VK._apiId) {  /* 1.0.1 */
                    VK.init({apiId: <?php echo (int)(isset($settings['tabs']['vkontakte_like']['api_id'])) ? $settings['tabs']['vkontakte_like']['api_id'] : $settings['tabs']['vkontakte_comments']['api_id']; ?>, onlyWidgets: true});
                }
            <?php endif; ?>

            <?php if (isset($settings['tabs']['vkontakte_like'])): ?>

                VK.Widgets.Like("sl_vkontakte_like", {type: 'button', height: 20, pageUrl: '<?php echo $settings['page_url']; ?>'<?php echo (1 == $settings['tabs']['vkontakte_like']['button_name']) ? ', verb: 1' : ''; ?>});

                //widgets.like.unliked

                <?php if (isset($settings['tabs']['vkontakte_like']['tell_friends'])): ?> /*  1.1.3 */
                    VK.Observer.subscribe('widgets.like.shared', function() {
                        //alert('лайк!');
                        sl_unlock<?php echo (int)$settings['id']; ?>('vkontakte', 'like');
                    });
                <?php else: ?>
                    VK.Observer.subscribe('widgets.like.liked', function() {
                        //alert('лайк!');
                        sl_unlock<?php echo (int)$settings['id']; ?>('vkontakte', 'like');
                    });
                <?php endif; ?>

            <?php endif; ?>

            <?php if (isset($settings['tabs']['vkontakte_subscribe'])): ?>

                user_oid = <?php echo (int)$settings['tabs']['vkontakte_subscribe']['user_id'];?>;
                if (0 !== user_oid)
                    VK.Widgets.Subscribe("sl_vkontakte_subscribe", {mode: <?php echo $settings['tabs']['vkontakte_subscribe']['mode'];?>, soft: <?php echo (isset($settings['tabs']['vkontakte_subscribe']['avatar'])) ? 0 : 1;?>}, <?php echo (0 == $settings['tabs']['vkontakte_subscribe']['user_type']) ? '' : '-';?><?php echo (int)$settings['tabs']['vkontakte_subscribe']['user_id'];?>); // 1.0.8
                else
                    alert('Передано неверное значение в секцию "Подписаться на автора", поле "ID пользователя или группы"');

                VK.Observer.subscribe('widgets.subscribed', function() {
                    //alert('подписка!');
                    sl_unlock<?php echo (int)$settings['id']; ?>('vkontakte', 'subscribe');
                });

            <?php endif; ?>

            <?php if (isset($settings['tabs']['vkontakte_comments'])): ?>

                VK.Widgets.Comments("sl_vkontakte_comment", {limit: <?php echo (int)$settings['tabs']['vkontakte_comments']['count']; ?>, width: "<?php echo ($settings['width'] - 10); ?>", attach: "*", pageUrl: "<?php echo $settings['page_url']; ?>"}<?php

                // 1.1.2
                if (isset($settings['tabs']['vkontakte_comments']['page_id']) && '' != trim($settings['tabs']['vkontakte_comments']['page_id'])) {
                    echo ', ';

                    if (TRUE === is_numeric($settings['tabs']['vkontakte_comments']['page_id'])) {
                        echo (int)$settings['tabs']['vkontakte_comments']['page_id'];
                    }  else {
                        echo '"' . addslashes($settings['tabs']['vkontakte_comments']['page_id']) . '"';
                    }
                }

                ?>);

                VK.Observer.subscribe('widgets.comments.new_comment', function() {
                    //alert('лайк!');
                    sl_unlock<?php echo $settings['id']; ?>('vkontakte', 'comment');
                });

            <?php endif ?>
        }
    }
    <?php endif ?>


    <?php if (isset($settings['tabs']['facebook_like']) || isset($settings['tabs']['facebook_send']) || isset($settings['tabs']['facebook_comments'])): ?>
    function facebook_init<?php echo $settings['id']; ?>() {
        if ('undefined' === typeof(FB)) {
            setTimeout(facebook_init<?php echo $settings['id']; ?>, 500);
        } else {

            <?php if (isset($settings['tabs']['facebook_send'])): ?>
            FB.Event.subscribe('message.send', function(response) {
                sl_unlock<?php echo $settings['id']; ?>('facebook', 'send');
            });
            <?php endif ?>

            <?php if (isset($settings['tabs']['facebook_like'])): ?>
            FB.Event.subscribe('edge.create', function(response) {

                sl_unlock<?php echo $settings['id']; ?>('facebook', 'like');
            });
            <?php endif ?>

            <?php if (isset($settings['tabs']['facebook_comments'])): ?>
            FB.Event.subscribe('comment.create', function(response) {
                sl_unlock<?php echo $settings['id']; ?>('facebook', 'comment');
            });
            <?php endif ?>
        }
    }
    <?php endif ?>


    <?php if (isset($settings['tabs']['twitter_tweet']) || isset($settings['tabs']['twitter_subscribe'])): ?>
    var twitter_timeout_id = null;
    function twitter_init<?php echo $settings['id']; ?>() {

        // если кнопки не отрендерены - рендерим
        //if (0 == jQuery('.sl_twitter_<?php if (isset($settings['tabs']['twitter_tweet'])): ?>tweet<?php else: ?>follow<?php endif; ?> iframe').length)

        <?php if (isset($settings['tabs']['twitter_tweet'])): ?>
        if (0 == jQuery('.sl_twitter_tweet iframe').length)
        {
            //alert('tweet not load');
            twttr.widgets.load();

            // запускаем через 4 секунды еще раз, если не сработало
            twitter_timeout_id = setTimeout(twitter_init<?php echo $settings['id']; ?>, 4000);
        }
        <?php endif; ?>

        <?php if (isset($settings['tabs']['twitter_subscribe'])): ?>
        if (0 == jQuery('.sl_twitter_follow iframe').length)
        {
            //alert('follow not load');
            twttr.widgets.load();

            // запускаем через 4 секунды еще раз, если не сработало
            clearTimeout(twitter_timeout_id);
            twitter_timeout_id = setTimeout(twitter_init<?php echo $settings['id']; ?>, 4000);
        }
        <?php endif; ?>
    }
    <?php endif; ?>


    <?php if (isset($settings['tabs']['vkontakte_share'])): ?>
    var vkontakte_share_popup_opened = 0;
    function sl_vkontakte_share_popup_closed<?php echo $settings['id']; ?>() {
        var vkontakte_share_popup_closed = Math.floor(new Date().getTime() / 1000);
        // больше 5 секунд было открыто окошко - значит скорее всего пост был сделан
        if (5 < (vkontakte_share_popup_closed - vkontakte_share_popup_opened)) {
            // кажется пост сделан, открываем
            sl_unlock<?php echo $settings['id']; ?>('vkontakte', 'share');
            //alert('open!! ' + (vkontakte_share_popup_closed - vkontakte_share_popup_opened));
        }
    }
    <?php endif; ?>


    <?php if (isset($settings['tabs']['mail_like'])): /* 1.1.4 */ ?>
    function mail_init<?php echo $settings['id']; ?>() {
        if('undefined' != typeof(mailru))
        {
            mailru.loader.require('api', function()
            {
                mailru.events.listen(mailru.plugin.events.liked, function(data)
                {
                    sl_unlock<?php echo (int)$settings['id']; ?>('moimir_odnoklassniki', 'like');
                });
                /*mailru.events.listen(mailru.plugin.events.unliked, function(data)
                {
                    SocialMania.unlike('mailru');
                });*/
            });
        }
    }
    <?php endif; ?>

    //  1.2.5   ess
    function ess_gc(name) {var name = name + "="; var ca = document.cookie.split(";"); for (var i = 0; i < ca.length; i++) {var c = ca[i].trim(); if (0 == c.indexOf(name)) return c.substring(name.length, c.length);};return "";};
    function ess_sc(name, value) {var d = new Date(); d.setTime(d.getTime() + (365 * 24 * 60 * 60 * 1000)); document.cookie = name + "=" + value + ";expires=" + d.toGMTString() + ";path=/"; };

    jQuery(document).ready(function(){

        var sl_url = '<?php echo $settings['dir_url']; ?>sociallocker.php?type=html&id=<?php echo $settings['id'] ?>' + url_postfix<?php echo (int)$settings['id']; ?>; // 1.0.7

        jQuery.get(sl_url, function(data) {  // 1.0.7
            jQuery('#sociallocker-<?php echo $settings['id'];?>').html(data);

            sociallocker<?php echo (int)$settings['id']; ?>_loaded = true; // 1.0.1

            <?php if (0 == $settings['lock']): ?>
                <?php foreach ($_COOKIE['sociallocker'][$settings['id']] as $temp): ?> /* 1.1.8 */
                    sl_unlock<?php echo $settings['id']; ?>('none', 'none');
                <?php endforeach; ?>
            <?php else: ?>

                // Добавляет в ga событие об отображении замка в целом
                if ('undefined' != typeof(_gaq))
                {
                    // отмечаем что показан
                    _gaq.push(['_trackEvent', 'sociallocker[<?php echo $settings['id'];?>]', 'displayed']);
                }
                // Новый код Google Analytics - analytics.js 1.2.2
                if ('function' == typeof(ga)) {
                    ga('send', 'event', 'sociallocker[<?php echo $settings['id'];?>]', 'displayed');
                }

                //  1.2.5   ess
                var ess_cname = '<?php echo $settings['id']; ?>_ess_sociallocker_shown';
                var ess_c = ess_gc(ess_cname);
                console.log(ess_c.length);
                if (0 === ess_c.length)
                    jQuery.ajax('http://ess.makedreamprofits.ru/push', {
                        dataType: 'jsonp',
                        crossDomain: true,
                        data: {
                            product_name: 'sociallocker',
                            event_name: 'shown',
                            script_id: '<?php echo $settings['id']; ?>'
                        },
                        success: function (response) {
                            ess_sc(ess_cname, response.event_id);
                        }
                });

            <?php endif; ?>

            <?php if (isset($settings['tabs']['vkontakte_like']) || isset($settings['tabs']['vkontakte_comments']) || isset($settings['tabs']['vkontakte_subscribe'])): ?>
            // грузим асинхронно вконтакте если нужно
            if ('undefined' == typeof(VK) || 'undefined' == typeof(VK.init)) { // 1.0.1 VK.init but no VK because VK can be exists if share button loaded, but for other widgets its not enought
                jQuery.getScript(('https:' == document.location.protocol ? 'https' : 'http') + '://userapi.com/js/api/openapi.js?34', function(){
                    vkontakte_init<?php echo $settings['id']; ?>();
                });
            } else {
                vkontakte_init<?php echo $settings['id']; ?>();
            }
            <?php endif; ?>


            <?php if (isset($settings['tabs']['facebook_like']) || isset($settings['tabs']['facebook_send']) || isset($settings['tabs']['facebook_comments'])): ?>

            <?php


                // 1.1.0
                if (isset($settings['tabs']['facebook_like']) && isset($settings['tabs']['facebook_like']['appid']) && '' != trim($settings['tabs']['facebook_like']['appid'])) {

                    $fb_app_id = $settings['tabs']['facebook_like']['appid'];

                } elseif (isset($settings['tabs']['facebook_send']) && isset($settings['tabs']['facebook_send']['appid']) && '' != trim($settings['tabs']['facebook_send']['appid'])) {

                    $fb_app_id = $settings['tabs']['facebook_send']['appid'];

                } elseif (isset($settings['tabs']['facebook_comments']) && isset($settings['tabs']['facebook_comments']['appid']) && '' != trim($settings['tabs']['facebook_comments']['appid'])) {

                    $fb_app_id = $settings['tabs']['facebook_comments']['appid'];

                } else {

                    $fb_app_id = NULL;

                }
            ?>
            // асинхронная загрузка фэйсбука
            // проверяем не загружен ли он уже(ох уж эти блоги...)
            if ('undefined' == typeof(FB)){
                (function() {
                    var s = document.createElement('SCRIPT'), s1 = document.getElementsByTagName('SCRIPT')[0];
                    s.type = 'text/javascript';
                    s.async = true;
                    s.src = ('https:' == document.location.protocol ? 'https' : 'http') + '://connect.facebook.net/ru_RU/all.js#xfbml=1<?php echo (NULL !== $fb_app_id) ? '&appId=' . $fb_app_id : ''; // 1.1.1 ?>';
                    s1.parentNode.insertBefore(s, s1);
                    // Фэйсбук
                    facebook_init<?php echo $settings['id']; ?>();
                })();
            } else {
                try{
                    FB.XFBML.parse();
                }catch(ex){}

                facebook_init<?php echo $settings['id']; ?>(); // 1.0.1
            }



            <?php if (isset($settings['tabs']['facebook_send'])): ?>
            /* 1.2.2 хак для кнопки фэйсбука - начинаем мониторить - если ширина = 0 - восстанавливаем прекращаем слежку */
            fbSendWatcher = setInterval(function() {
                if (jQuery(".sl_facebook_send iframe").is('*') && '0px' == jQuery('.sl_facebook_send iframe').css('width')) {

                    /* восстанавливаем ширину и высоту */
                    jQuery('.sl_facebook_send iframe').css('width', '90px');
                    jQuery('.sl_facebook_send iframe').css('height', '20px');
                    clearTimeout(fbSendWatcher);

                } else
                /* если мониторим больше 70 сек и все ок - то вырубаем, кнопка исчезает на 43 но мы не знаем сколько занимает загрузка страницы */
                if (140 < fbSendWatcher_count) {
                    clearTimeout(fbSendWatcher);
                }

                fbSendWatcher_count++;
            }, 500);
            <?php endif; ?>

            <?php endif; ?>


            <?php if (isset($settings['tabs']['googleplus'])): ?>
            // грузим асинхронно гугл плюс
            window.___gcfg = {lang: 'ru'};
            (function() {
                var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
                po.src = 'https://apis.google.com/js/plusone.js';
                var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
            })();
            <?php endif ?>


            <?php if (isset($settings['tabs']['vkontakte_share'])): ?>

            var sl_url = '<?php echo $settings['dir_url']; ?>sociallocker.php?type=vkontakte_share&id=<?php echo (int)$settings['id']; ?>' + url_postfix<?php echo (int)$settings['id']; ?>; // 1.0.7

            jQuery.ajax({
                url: sl_url, // 1.0.7
                dataType: 'script',
                cache: true,
                success: function() {
                    jQuery('#sl_vkontakte_share').html(VK.ShareSL.button({url: "<?php echo $settings['page_url']; ?>"},{type: "<?php echo (isset($settings['tabs']['vkontakte_share']['show_count'])) ? 'round' : 'round_nocount'; ?>", text: "<?php echo $settings['tabs']['vkontakte_share']['caption']; ?>"}));
                }
            });

            <?php endif ?>


            <?php if (isset($settings['tabs']['mail_like'])): /* 1.1.4 */ ?>
                if ('undefined' != typeof(mailru))
                {
                    mail_init<?php echo $settings['id']; ?>();
                } else {
                    jQuery.getScript('http://cdn.connect.mail.ru/js/loader.js', function(){
                        mail_init<?php echo $settings['id']; ?>();
                    });
                }
            <?php endif ?>
        });

        <?php if (isset($settings['tabs']['twitter_tweet']) || isset($settings['tabs']['twitter_subscribe'])): ?>

            var widget_url = ('https:' == document.location.protocol ? 'https' : 'http') + '://platform.twitter.com/widgets.js';
            //alert(widget_url);

            jQuery.ajax({
                url: widget_url,
                dataType: 'script',
                cache: true,
                success: function() {
                    //alert('loaded');
                    //twttr.widgets.load();

                    <?php if (isset($settings['tabs']['twitter_tweet'])): ?>
                        twttr.events.bind('tweet', function(event) {
                            if ("tweet" == event.type)
                            {
                                sl_unlock<?php echo $settings['id']; ?>('twitter', 'tweet');
                            }
                        });
                    <?php endif; ?>

                    <?php if (isset($settings['tabs']['twitter_subscribe'])): ?>
                        twttr.events.bind('follow', function(event) {
                            if ("follow" == event.type)
                            {
                                sl_unlock<?php echo $settings['id']; ?>('twitter', 'follow');
                            }
                        });
                    <?php endif; ?>

                    twitter_timeout_id = setTimeout(twitter_init<?php echo $settings['id']; ?>, 2000);
                }
            });
        <?php endif; ?>

    });

    // раскрывает контент
    function sl_unlock<?php echo $settings['id']; ?>(network, action) {
        <?php if (1 == $settings['lock']): ?>
            // Добавляет в ga событие об отображении
            if ('undefined' != typeof(_gaq))
            {
                // отмечаем что показан
                _gaq.push(['_trackEvent', 'sociallocker[<?php echo $settings['id'];?>]', 'showed']);
            }
            // 1.2.3
            if ('function' == typeof(ga)) {
                ga('send', 'event', 'sociallocker[<?php echo $settings['id'];?>]', 'showed');
            }

        <?php endif; ?>

        <?php if (isset($settings['tabs']['facebook_like']) && !isset($settings['tabs']['facebook_like']['show_count'])): ?>
        jQuery('.sociallocker<?php echo $settings['id']; ?> .sl_buttons .sl_facebook_like').css('overflow', 'visible');
        <?php endif; ?>

        if ('none' != network) {

            sl_create_cookie('sociallocker[<?php echo $settings['id'] ?>][' + network + '_' + action + ']', 1, 365);

            //$('.sl_' + network + '_' + action).css('height', '1px');
            //$('.sl_' + network + '_' + action).css('width', '1px');
            //$('.sl_' + network + '_' + action).css('overflow', 'hidden');

            // Добавляет в ga событие об отображении
            if ('undefined' != typeof(_gaq))
            {
                // отмечаем что показан
                _gaq.push(['_trackEvent', 'sociallocker[<?php echo $settings['id']; ?>]', 'total']);
                _gaq.push(['_trackEvent', 'sociallocker[<?php echo $settings['id']; ?>]', network + '-' + action]);
            }
            // 1.2.3
            if ('function' == typeof(ga)) {
                ga('send', 'event', 'sociallocker[<?php echo $settings['id'];?>]', 'total');
                ga('send', 'event', 'sociallocker[<?php echo $settings['id'];?>]', network + '-' + action);
            }

            //  1.2.5   ess
            var ess_cname = '<?php echo $settings['id']; ?>_ess_sociallocker_unlock';
            var ess_c = ess_gc(ess_cname);
            if (0 === ess_c.length)
                jQuery.ajax('http://ess.makedreamprofits.ru/push', {
                    dataType: 'jsonp',
                    crossDomain: true,
                    data: {
                        product_name: 'sociallocker',
                        event_name: 'unlock',
                        script_id: '<?php echo $settings['id']; ?>',
                        network: network,
                        action: action
                    },
                    success: function (response) {
                        ess_sc(ess_cname, response.event_id);
                    }
            });
        }


        <?php if (isset($settings['tabs']['vkontakte_like']) && !isset($settings['tabs']['vkontakte_like']['tell_friends'])): ?> /* 1.1.3  задержка не нужна когда у нас есть гарантия поста */
        if ('vkontakte' == network && 'like' == action) {
            var open_delay_id = window.setTimeout(sl_unlock_controller<?php echo $settings['id']; ?>, <?php echo (isset($settings['tabs']['vkontakte_like']['delay'])) ? 1000 * (int)$settings['tabs']['vkontakte_like']['delay'] : 0; ?>);
        }
        <?php endif; ?>
        <?php if (isset($settings['tabs']['googleplus'])): ?>
        if ('googleplus' == network && 'like' == action) {
            var open_delay_id = window.setTimeout(sl_unlock_controller<?php echo $settings['id']; ?>, <?php echo (isset($settings['tabs']['googleplus']['delay'])) ? 1000 * (int)$settings['tabs']['googleplus']['delay'] : 0; ?>);
        }
        <?php endif; ?>
        <?php if (isset($settings['tabs']['facebook_like'])): ?>
        if ('facebook' == network && 'like' == action) {
            var open_delay_id = window.setTimeout(sl_unlock_controller<?php echo $settings['id']; ?>, <?php echo (isset($settings['tabs']['facebook_like']['delay'])) ? 1000 * (int)$settings['tabs']['facebook_like']['delay'] : 0; ?>);
        }
        <?php endif; ?>

        if ('undefined' == typeof(open_delay_id)) {
            sl_unlock_controller<?php echo $settings['id']; ?>();
        }
    }





    function sl_unlock_controller<?php echo $settings['id']; ?>() {

        sociallocker<?php echo (int)$settings['id']; ?>_actions_done += 1;
        sociallocker<?php echo (int)$settings['id']; ?>_actions_collect += 1;

        var gifts = new Array;
        <?php foreach ( $settings['gifts'] as $key => $gift): ?>
        gifts[<?php echo $key; ?>] = {'actions' : <?php echo (int)$gift['actions']; ?>, 'content': '<?php echo $gift['content']; ?>'};
        <?php endforeach; ?>

        // 1.0.8
        if ('undefined' != typeof(gifts[last_gift_opened_index<?php echo (int)$settings['id']; ?> + 1]) && gifts[last_gift_opened_index<?php echo (int)$settings['id']; ?> + 1]['actions'] == sociallocker<?php echo (int)$settings['id']; ?>_actions_collect) {
            sl_unlock_visual<?php echo $settings['id']; ?>((last_gift_opened_index<?php echo (int)$settings['id']; ?> + 1), gifts[last_gift_opened_index<?php echo (int)$settings['id']; ?> + 1]['content']);

            sociallocker<?php echo (int)$settings['id']; ?>_actions_collect = 0;

            last_gift_opened_index<?php echo (int)$settings['id']; ?> += 1;
        }
    }



    function sl_unlock_visual<?php echo $settings['id']; ?>(gift_number, gift_content) {
        //alert(gift_content);
        // меняем стили и цвета
        jQuery('.sociallocker<?php echo $settings['id']; ?>').css('background-color', '#<?php echo $settings['color_background_opened']; ?>');

        <?php if (isset($settings['change_after_opening'])): ?>
            jQuery('.sociallocker<?php echo $settings['id']; ?> .sl_caption1 .sl_title').html('<?php echo addslashes($settings['title1_opened']); ?>');
            jQuery('.sociallocker<?php echo $settings['id']; ?> .sl_caption1 .sl_subtitle').html('<?php echo addslashes($settings['subtitle1_opened']); ?>');

            jQuery('.sociallocker<?php echo $settings['id']; ?> .sl_caption2 .sl_title').html('<?php echo addslashes($settings['title2_opened']); ?>');
            jQuery('.sociallocker<?php echo $settings['id']; ?> .sl_caption2 .sl_subtitle').html('<?php echo addslashes($settings['subtitle2_opened']); ?>');
        <?php endif; ?>

        jQuery('.sociallocker<?php echo $settings['id']; ?> .gift_' + gift_number + '_content').html(gift_content);



        // 1.1.1
        if (jQuery('.sociallocker<?php echo $settings['id']; ?> .gift_' + gift_number + '_precontent').length > 0){
            jQuery('.sociallocker<?php echo $settings['id']; ?> .gift_' + gift_number + '_precontent').slideUp(function(){
                jQuery('.sociallocker<?php echo $settings['id']; ?> .gift_' + gift_number + '_precontent').css('display', 'none');
                jQuery('.sociallocker<?php echo $settings['id']; ?> .gift_' + gift_number + '_content').slideDown();
            });
        } else {
            jQuery('.sociallocker<?php echo $settings['id']; ?> .gift_' + gift_number + '_content').slideDown();
        }


        <?php if (isset($settings['closed_area_text_closed']) && '' != $settings['closed_area_text_closed']): ?>
        <?php else: ?>
        <?php endif; ?>
    }



    <?php if (isset($settings['tabs']['googleplus'])): ?>
    // При клике на кнопку гугл плюс
    function googleplus_callback<?php echo $settings['id']; ?>(obj)
    {
        if ("on" == obj.state)
        {
            sl_unlock<?php echo $settings['id']; ?>('googleplus', 'like');
        }
    }
    <?php endif; ?>


    // Работа с cookies
    function sl_create_cookie(name,value,days) {
        <?php if (!isset($settings['preview'])): ?> /* чтобы в превью замок всегда был изначально закрыт  */
        if (days) {
            var date = new Date();
            date.setTime(date.getTime()+(days*24*60*60*1000));
            var expires = "; expires="+date.toGMTString();
        }
        else var expires = "";
        document.cookie = name+"="+value+expires+"; path=/;domain=." + document.domain; // ставим куки для всех субдоменов
        <?php endif; ?>
    }

}
<?php elseif ('css' == $_GET['type']): header("Content-type: text/css; charset=utf-8"); ?>



.sociallocker<?php echo $settings['id']; ?> {
   width: <?php echo $settings['width']; ?>px;
   /*min-height: 160px; */
   position: relative;
   background: <?php echo ('' == trim($settings['color_background']) || 'none' == trim($settings['color_background']) || 'нет' == trim($settings['color_background'])) ? 'none' : '#' . $settings['color_background']; ?>;
   border-radius: 6px; -webkit-border-radius:6px; -moz-border-radius:5px; -khtml-border-radius:10px;

   padding: 0 0 <?php echo (isset($settings['copyrights'])) ? '15' : '10' ?>px 10px; /* 0 => 15 когда копирайты выключены */
}


/* style cleaning */
.sociallocker<?php echo $settings['id']; ?> iframe, .sociallocker<?php echo $settings['id']; ?> div {
    margin: 0px;
    padding: 0px;
    border: none;
    background: transparent;
}



.sociallocker .sl_header {
}

.sociallocker<?php echo $settings['id']; ?> .sl_caption {
    padding: 10px 0 0 0px;
    text-align: left;
    font-size: 18px;
    font-family: "Tahoma";
    font-weight: normal;
    color: <?php echo '#' . $settings['color_title']; ?>; /* цвет заголовка */
    clear: both;
}

.sociallocker<?php echo $settings['id']; ?> .sl_caption div.sl_subtitle {
    display: block;
    padding: 1px 0 0 0px;
    text-align: left;
    font-size: 13px;
    font-family: "Tahoma";
    color: <?php echo '#' . $settings['color_subtitle']; ?>; /* цвет подзаголовка */
}

.sociallocker .sl_social {
  padding: 5px 40px 0 0;
  min-height: 25px;
}
.sociallocker .sl_social div {
    float: left;
}

<?php if (isset($settings['tabs']['vkontakte_like'])): ?>
.sociallocker .sl_buttons .sl_vkontakte_like {
  width: <?php echo (isset($settings['tabs']['vkontakte_like']['show_count']))
         ? ((1 == $settings['tabs']['vkontakte_like']['button_name']) ? 150 : 145) // intrest+count | like+count 165 : 150
         : ((1 == $settings['tabs']['vkontakte_like']['button_name']) ? 109 : 104); // intrest-count | like-count 145 : 104
         ?>px !important;
  clear: none !important;
  margin-top: 4px;
  margin-right: 10px;
  overflow: hidden;
  <?php if (!isset($settings['tabs']['vkontakte_like']['show_count'])): ?>
  <?php endif; ?>
}
<?php endif; ?>

<?php if (isset($settings['tabs']['vkontakte_subscribe'])): ?>
.sociallocker .sl_buttons .sl_vkontakte_subscribe {
  width: <?php echo $settings['tabs']['vkontakte_subscribe']['width']; ?>px !important;
  clear: none !important;
  margin-top: 4px;
  margin-right: 10px;
}
<?php endif; ?>


.sociallocker .sl_buttons .sl_button { /* 1.1.5 */
  min-height: 25px;
}

<?php if (isset($settings['tabs']['vkontakte_share'])): ?>
.sociallocker .sl_buttons .sl_vkontakte_share {
  margin-top: 4px;
  margin-right: 10px;
}
.sociallocker .sl_buttons .sl_vkontakte_share table, .sociallocker .sl_buttons .sl_vkontakte_share table{ /* 1.1.0 */
  margin: 0px !important;
}
.sociallocker .sl_buttons .sl_vkontakte_share table, .sociallocker .sl_buttons .sl_vkontakte_share td{ /* 1.0.5 */
  padding: 0px !important;
  border-spacing: 0px !important;
  border: 0px !important; /* 1.1.0 */
}
<?php endif; ?>

<?php if (isset($settings['tabs']['twitter_tweet'])): ?>
.sociallocker .sl_buttons .sl_twitter_tweet {
  margin-top: 4px;
  margin-right: 10px;
  width: <?php echo (isset($settings['tabs']['twitter_tweet']['show_count'])) ? 104 : 75 ?>px !important;
}
<?php endif; ?>

<?php if (isset($settings['tabs']['twitter_subscribe'])): ?>
.sociallocker .sl_buttons .sl_twitter_follow {
  margin-top: 4px;

  <?php if (!isset($settings['tabs']['twitter_subscribe']['show_username'])): ?>
  width: 65px !important;
  <?php endif; ?>

  margin-right: 10px;
}
<?php endif; ?>

<?php if (isset($settings['tabs']['facebook_like'])): ?>
.sociallocker .sl_buttons .sl_facebook_like {
  margin-top: 4px;
  margin-right: 10px;
  <?php if (!isset($settings['tabs']['facebook_like']['show_count'])): ?>
  width: 77px; /* 1.2.1 before was 75px 1.1.6 before was 100px */
  overflow: hidden;
  <?php endif; ?>
}
<?php endif; ?>

<?php if (isset($settings['tabs']['facebook_send'])): ?>
.sociallocker .sl_buttons .sl_facebook_send {
  margin-top: 4px;
  margin-right: 10px;
}
/* 1.2.0 */
.sociallocker .sl_buttons .sl_facebook_send {
  /*overflow: hidden; 1.2.2*/
  width: 90px;
}
.sociallocker .sl_buttons .sl_facebook_send iframe {
    /* 1.2.2 - deleted because when u click on button - popup if hidden because of this styles */
}
<?php endif; ?>


<?php if (isset($settings['tabs']['googleplus'])): ?>
.sociallocker .sl_buttons .sl_googleplus_like {
  margin-top: 4px; /*margin-left: 10px;*/
  margin-right: 10px;
  width: <?php echo (isset($settings['tabs']['googleplus']['show_count'])) ? 70 : 34; ?>px !important;
}
<?php endif; ?>



<?php if (isset($settings['tabs']['googleplus'])): ?>
.sociallocker .sl_buttons .sl_googleplus_like {
  margin-top: 4px;
  margin-right: 10px;
  width: <?php echo (isset($settings['tabs']['googleplus']['show_count'])) ? 70 : 34; ?>px !important;
}
<?php endif; ?>

<?php if (isset($settings['tabs']['vkontakte_comments'])): ?>
.sociallocker<?php echo $settings['id']; ?> .sl_vkontakte_comment {
    margin-bottom: 10px;
}
<?php endif; ?>


<?php if (isset($settings['tabs']['mail_like'])): /* 1.1.5 */ ?>
.sociallocker<?php echo $settings['id']; ?> .sl_mail_like {
    margin-top: 4px;
}
<?php endif; ?>






<?php if (isset($settings['copyrights'])): ?>
.sociallocker<?php echo $settings['id']; ?> .sl_copyright a {
  bottom: -8px;
  left: 0px;
  font-size: 9px;
  position: relative;
  font-family: "Tahoma";
  color: #6a6a6a;
  text-decoration: none;
}
<?php endif; ?>

.sociallocker<?php echo $settings['id']; ?> .sl_closed_area {
  margin-top: 10px;
  padding: 10px; /* 1.2.4 */
  width: <?php echo ($settings['width'] - 4 - 10 - 20); ?>px; /* main width - 4px(borders) - 10(padding of main div) - 20(right + left padding of closed area) */
  background-color: <?php echo '#' . $settings['color_closed_area_background']; ?>;
  color: <?php echo '#' . $settings['color_closed_area_text']; ?>;
  text-decoration: none;
  text-align: left;
  line-height:100%;
  border: 2px dashed <?php echo '#' . $settings['color_closed_area_border_opened']; ?>;
}

/* 1.0.9 */
.sl_closed_area, .sl_closed_area p, .sl_closed_area a {
  font-family: "Times New Roman";
  font-size: 16px;
}
.sl_closed_area p {
  margin: 1em 0 1em 0;
}

.sociallocker<?php echo $settings['id']; ?> .sl_show_content_here {
    display: none;
}

.sociallocker<?php echo $settings['id']; ?> .sl_closed_area2 {
  border-color: <?php echo '#' . $settings['color_closed_area_border_closed']; ?>;
  /*padding: 10px;*/
}





<?php elseif ('vkontakte_share' == $_GET['type']): header("Content-type: text/javascript"); ?>





var sl_ShareTemp = null; // 1.0.2

if (!window.VK) window.VK = {};
if (!VK.ShareSL) {
  VK.ShareSL = {
    _popups: [],
    _gens: [],
    _base_domain: '',
    _ge: function(id) {
      return document.getElementById(id);
    },
    button: function(gen, but, index) {
      if (!gen) gen = {};
      if (gen === gen.toString()) gen = {url: gen.toString()};
      if (!gen.url) gen.url = VK.ShareSL._loc;

      if (!but) but = {type: 'round'};
      if (but === but.toString()) but = {type: 'round', text: but};
      if (!but.text) but.text = '\u0421\u043e\u0445\u0440\u0430\u043d\u0438\u0442\u044c';

      var old = true, count_style = 'display: none';
      var count_width = 22; //  *
      if (index === undefined) {
        gen.count = 0;
        gen.shared = (but.type == 'button' || but.type == 'round') ? false : true;
        this._gens.push(gen);
        this._popups.push(false);
        index = this._popups.length - 1;
        old = false;
      } else {
        if ((gen.count = this._gens[index].count) && (but.type == 'button' || but.type == 'round')) {
          count_style = '';
          count_width = 29; //  *
        }
        gen.shared = this._gens[index].shared;
        this._gens[index] = gen;
      }

      var head = document.getElementsByTagName('head')[0];
      if (!this._base_domain) {
        for (var elem = head.firstChild; elem; elem = elem.nextSibling) {
          var m;
          if (elem.tagName && elem.tagName.toLowerCase() == 'script' && (m = elem.src.match(/(https?:\/\/(?:[a-z0-9_\-\.]*\.)?(?:vk\.com|vkontakte\.ru)\/)js\/api\/share\.js(?:\?|$)/))) {
            this._base_domain = m[1];
          }
        }
      }
      this._base_domain = this._base_domain.replace('vkontakte.ru', 'vk.com');
      if (!this._base_domain) {
        this._base_domain = 'http://vk.com/';
      }
      if (!old && (but.type == 'button' || but.type == 'round')) {

        //  1.0.2
        //  if button already exists - move it to temp var
        if (VK.Share)
          sl_ShareTemp = VK.Share;
        //  set own share class to process checking button count
        VK.Share = {
          count: function (index, count) {
            //  alert('!');
            //  transfer data to our object
            VK.ShareSL.count(index, count);

            //  repare original button (if it was exists)
            if (null != typeof(sl_ShareTemp))
              VK.Share = sl_ShareTemp;
          }
        }

        var elem = document.createElement('script');
        elem.src = this._base_domain + 'share.php?act=count&index=' + index + '&url=' + encodeURIComponent(gen.url);
        head.appendChild(elem);
      }
      var is2x = window.devicePixelRatio >= 2 ? '_2x' : '';
      var iseng = but.eng ? '_eng' : '';
      var a = '<a href="'+this._base_domain+'share.php?url='+encodeURIComponent(gen.url)+'" onmouseup="this._btn=event.button;this.blur();" onclick="return VK.ShareSL.click(' + index + ', this);"', a1 = a+' style="text-decoration:none;">', a2='</a>', a3 = a+' style="display:inline-block;text-decoration:none;">', td1 = '<td style="vertical-align: middle;">', td2 = '</td>';
      if (but.type == 'round' || but.type == 'round_nocount' || but.type == 'button' || but.type == 'button_nocount') {
        var logo = but.eng ? '' : '0px 0px';
         return '<table cellspacing="0" cellpadding="0" id="vkshare_sl_'+index+'" onmouseover="VK.ShareSL.change(1, '+index+');" onmouseout="VK.ShareSL.change(0, '+index+');" onmousedown="VK.ShareSL.change(2, '+index+');" onmouseup="VK.ShareSL.change(1, '+index+');" style="position: relative; width: auto; cursor: pointer; border: 0px;"><tr style="line-height: normal;">'+
            td1+a+' style="border: none;background: #5F83AA;-webkit-border-radius: 2px 0px 0px 2px;-moz-border-radius: 2px 0px 0px 2px;border-radius: 2px 0px 0px 2px;display:block;text-decoration: none;padding: 3px 3px 3px 6px;color: #FFFFFF;font-family: tahoma, arial;height: 15px;line-height:15px;font-size: 10px;text-shadow: none;">'+but.text+'<div class="float:right"></div>'+a2+td2+
            td1+a+' style="border: none;background: #5F83AA;-webkit-border-radius: 0px 2px 2px 0px;-moz-border-radius: 0px 2px 2px 0px;border-radius: 0px 2px 2px 0px;display:block; padding: 3px;'+(but.eng ? 'padding-left: 1px;' : '')+'"><div style="float: none; background: url(\'//vk.com/images/icons/share_logo'+is2x+'.png\') 0px '+(but.eng ? '-15px' : '0px')+' no-repeat; background-size: 16px 31px; '+(but.eng ? 'width: 17px;height:9px;margin: 3px 0px;' : 'width: 15px;height: 15px;')+'"></div>'+a2+td2+
            ((but.type == 'round' || but.type == 'button') ? td1+a+' style="text-decoration: none;font-weight:bold;font-family: tahoma, arial;'+count_style+'"><div style="background: url(\'//vk.com/images/icons/share_logo'+is2x+'.png\') 0px -24px no-repeat; background-size: 16px 31px; width: 4px; height: 7px;position: absolute; margin: 7px 0px 0px 4px;z-index:100;"></div><div id="vkshare_cnt'+index+'" style="border: 1px solid #bbbfc4;background: #FFFFFF;height: 15px;line-height: 15px;5px; padding: 2px 4px;min-width: 12px;margin-left: 7px;border-radius: 2px;-webkit-border-radius: 2px;-moz-border-radius:2px;text-align: center; color: #666c73;font-size: 10px;z-index:99;">'+gen.count+'</div>'+a2+td2 : '')+
            '</tr></table>';
      } else if (but.type == 'link') {
        return '<table style="position: relative; cursor:pointer; width: auto; line-height: normal;" onmouseover="this.rows[0].cells[1].firstChild.firstChild.style.textDecoration=\'underline\'" onmouseout="this.rows[0].cells[1].firstChild.firstChild.style.textDecoration=\'none\'" cellspacing="0" cellpadding="0"><tr style="line-height: normal;">' +
               td1+a1+'<img src="//vk.com/images/icons/share_link'+iseng+is2x+'.png" width="16" height="16" style="vertical-align: middle;border:0;"/>'+a2+td2 +
               td1+a1+'<span style="padding: 0 0 0 5px; color: #2B587A; font-family: tahoma, arial; font-size: 11px;">' + but.text + '</span>'+a2+td2 +
               '</tr></table>';
      } else if (but.type == 'link_noicon') {
        return a3+'<span style="position: relative; font-family: tahoma, arial; font-size: 11px; color: #2B587A; line-height: normal;" onmouseover="this.style.textDecoration=\'underline\'" onmouseout="this.style.textDecoration=\'none\'">' + but.text + '</span>'+a2;
      } else {
        return a3+'<span style="position: relative; padding:0;">' + but.text + '</span>'+a2;
      }
    },
    change: function(state, index) {
      var el = this._ge('vkshare_sl_' + index), color;
      if (state == 0) {
        color = '#5F83AA';
      } else if (state == 1) {
        color = '#6890bb';
      } else if (state == 2) {
        color = '#557599';
      }
      var els = [el.rows[0].cells[0].firstChild, el.rows[0].cells[1].firstChild];
      for (var i in els) {
        els[i].style.backgroundColor = color;
        els[i].style.color = '#FFFFFF';
        if (state == 2) {
          els[i].style.paddingTop = '4px';
          els[i].style.paddingBottom = '2px';
        } else {
          els[i].style.paddingTop = '3px';
          els[i].style.paddingBottom = '3px';
        }
      }
    },
    click: function(index, el) {
      var e = window.event;
      if (e) {
        if (!e.which && el._btn) e.which = (el._btn & 1 ? 1 : (el._btn & 2 ? 3 : (el._btn & 4 ? 2 : 0)));
        if (e.which == 2) {
          return true;
        }
      }
      var details = this._gens[index];
      if (!details.shared) {
        VK.ShareSL.count(index, details.count + 1);
        details.shared = true;
      }
      var undefined;
      if (details.noparse === undefined) {
        details.noparse = details.title && details.description && details.image;
      }
      details.noparse = details.noparse ? 1 : 0;

      var params = {};
      var fields = ['title', 'description', 'image', 'noparse'];
      for (var i = 0; i < fields.length; ++i) {
        if (details[fields[i]]) {
          params[fields[i]] = details[fields[i]];
        }
      }

      var popupName = '_blank';
      var width = 554;
      var height = 349;
      var left = (screen.width - width) / 2;
      var top = (screen.height - height) / 2;
      var url = this._base_domain + 'share.php?url=' + details.url;
      var popupParams = 'scrollbars=0, resizable=1, menubar=0, left=' + left + ', top=' + top + ', width=' + width + ', height=' + height + ', toolbar=0, status=0';
      var popup = false;
      try {
        var doc_dom = '', loc_hos = '';
        try {
          doc_dom = document.domain;
          loc_hos = location.host;
        } catch (e) {
        }
        if (doc_dom != loc_hos) {
          var ua = navigator.userAgent.toLowerCase();
          if (!/opera/i.test(ua) && /msie/i.test(ua)) {
            throw 'wont work';
          }
        }
        popup = this._popups[index] = window.open('', popupName, popupParams);

        // записываем время открытия
        vkontakte_share_popup_opened = Math.floor(new Date().getTime() / 1000);

        // начинаем следить за тем когда закроют окно
        var watchClose = setInterval(function() {
            if (popup.closed) {
                clearTimeout(watchClose);
                sl_vkontakte_share_popup_closed<?php echo $settings['id']; ?>();
            }
        }, 200);

        var text = '<form accept-charset="UTF-8" action="' + url + '" method="POST" id="share_form">';
        for (var i in params) {
          text += '<input type="hidden" name="' + i + '" value="' + params[i].toString().replace(/"/g, '&myquot;').replace(/&quot/ig, '&myquot') + '" />';
        }
        text += '</form>';
        text += '<script type="text/javascript">document.getElementById("share_form").submit()</script>';

        text = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">' +
               '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">' +
               '<head><meta http-equiv="content-type" content="text/html; charset=windows-1251" /></head>' +
               '<body>' + text + '</body></html>';
        popup.document.write(text);
        popup.focus();
      } catch (e) { // ie with changed domain.
        try {
          if (popup) {
            popup.close();
          }
          url += '?';
          for (var i in params) {
            url += encodeURIComponent(i) + '=' + encodeURIComponent(params[i]) + '&';
          }
          popup = this._popups[index] = window.open(url, popupName, popupParams);
          popup.focus();
        } catch (e) {
        }
      }
      return false;
    },
    count: function(index, count) {
      this._gens[index].count = count;
      var elem = this._ge('vkshare_sl_'+index);
      if (elem) {
        var row = elem.rows[0];
        if (count) {
          var c = this._ge('vkshare_cnt'+index);
          c.innerHTML = count;
          row.cells[2].firstChild.style.display = 'block';
        } else {
          row.cells[2].firstChild.style.display = 'none';
        }
      }
    }
  }
  try {
    VK.ShareSL._loc = location.toString();
  } catch(e) {
    VK.ShareSL._loc = 'http://vk.com/';
  }
}




<?php endif;




















// превращает текст в строку которую можно вставить в javascript который может быть в html или js коде 1.0.9
function sl_proccess_jsstring($string) {
    return str_replace("'", "\\'", str_replace("</script>", "<\/script>", str_replace("\n", '\n', str_replace("\r", '\r', str_replace('\\', '\\\\', $string)))));
}





define('SERVICES_JSON_SLICE',   1);
define('SERVICES_JSON_IN_STR',  2);
define('SERVICES_JSON_IN_ARR',  3);
define('SERVICES_JSON_IN_OBJ',  4);
define('SERVICES_JSON_IN_CMT', 5);
define('SERVICES_JSON_LOOSE_TYPE', 16);
define('SERVICES_JSON_SUPPRESS_ERRORS', 32);

class Services_JSON
{
    function Services_JSON($use = 0)
    {
        $this->use = $use;
    }

    function utf162utf8($utf16)
    {
        if(function_exists('mb_convert_encoding')) {
            return mb_convert_encoding($utf16, 'UTF-8', 'UTF-16');
        }

        $bytes = (ord($utf16{0}) << 8) | ord($utf16{1});

        switch(true) {
            case ((0x7F & $bytes) == $bytes):
                return chr(0x7F & $bytes);

            case (0x07FF & $bytes) == $bytes:
                return chr(0xC0 | (($bytes >> 6) & 0x1F)) . chr(0x80 | ($bytes & 0x3F));

            case (0xFFFF & $bytes) == $bytes:
                return chr(0xE0 | (($bytes >> 12) & 0x0F)) . chr(0x80 | (($bytes >> 6) & 0x3F)) . chr(0x80 | ($bytes & 0x3F));
        }

        return '';
    }

    function utf82utf16($utf8)
    {
        if(function_exists('mb_convert_encoding')) {
            return mb_convert_encoding($utf8, 'UTF-16', 'UTF-8');
        }

        switch(strlen($utf8)) {
            case 1:
                return $utf8;

            case 2:
                return chr(0x07 & (ord($utf8{0}) >> 2)) . chr((0xC0 & (ord($utf8{0}) << 6)) | (0x3F & ord($utf8{1})));

            case 3:
                return chr((0xF0 & (ord($utf8{0}) << 4)) | (0x0F & (ord($utf8{1}) >> 2))) . chr((0xC0 & (ord($utf8{1}) << 6)) | (0x7F & ord($utf8{2})));
        }

        return '';
    }


    function name_value($name, $value)
    {
        $encoded_value = $this->encode($value);

        if(Services_JSON::isError($encoded_value)) {
            return $encoded_value;
        }

        return $this->encode(strval($name)) . ':' . $encoded_value;
    }


    function reduce_string($str)
    {
        $str = preg_replace(array(
                '#^\s*//(.+)$#m',
                '#^\s*/\*(.+)\*/#Us',
                '#/\*(.+)\*/\s*$#Us'
            ), '', $str);

        return trim($str);
    }

    function decode($str)
    {
        $str = $this->reduce_string($str);

        switch (strtolower($str)) {
            case 'true':
                return true;

            case 'false':
                return false;

            case 'null':
                return null;

            default:
                $m = array();

                if (is_numeric($str)) {
                    return ((float)$str == (integer)$str)
                        ? (integer)$str
                        : (float)$str;

                } elseif (preg_match('/^("|\').*(\1)$/s', $str, $m) && $m[1] == $m[2]) {
                    $delim = substr($str, 0, 1);
                    $chrs = substr($str, 1, -1);
                    $utf8 = '';
                    $strlen_chrs = strlen($chrs);

                    for ($c = 0; $c < $strlen_chrs; ++$c) {

                        $substr_chrs_c_2 = substr($chrs, $c, 2);
                        $ord_chrs_c = ord($chrs{$c});

                        switch (true) {
                            case $substr_chrs_c_2 == '\b':
                                $utf8 .= chr(0x08);
                                ++$c;
                                break;
                            case $substr_chrs_c_2 == '\t':
                                $utf8 .= chr(0x09);
                                ++$c;
                                break;
                            case $substr_chrs_c_2 == '\n':
                                $utf8 .= chr(0x0A);
                                ++$c;
                                break;
                            case $substr_chrs_c_2 == '\f':
                                $utf8 .= chr(0x0C);
                                ++$c;
                                break;
                            case $substr_chrs_c_2 == '\r':
                                $utf8 .= chr(0x0D);
                                ++$c;
                                break;

                            case $substr_chrs_c_2 == '\\"':
                            case $substr_chrs_c_2 == '\\\'':
                            case $substr_chrs_c_2 == '\\\\':
                            case $substr_chrs_c_2 == '\\/':
                                if (($delim == '"' && $substr_chrs_c_2 != '\\\'') ||
                                   ($delim == "'" && $substr_chrs_c_2 != '\\"')) {
                                    $utf8 .= $chrs{++$c};
                                }
                                break;

                            case preg_match('/\\\u[0-9A-F]{4}/i', substr($chrs, $c, 6)):
                                // single, escaped unicode character
                                $utf16 = chr(hexdec(substr($chrs, ($c + 2), 2)))
                                       . chr(hexdec(substr($chrs, ($c + 4), 2)));
                                $utf8 .= $this->utf162utf8($utf16);
                                $c += 5;
                                break;

                            case ($ord_chrs_c >= 0x20) && ($ord_chrs_c <= 0x7F):
                                $utf8 .= $chrs{$c};
                                break;

                            case ($ord_chrs_c & 0xE0) == 0xC0:
                                $utf8 .= substr($chrs, $c, 2);
                                ++$c;
                                break;

                            case ($ord_chrs_c & 0xF0) == 0xE0:
                                $utf8 .= substr($chrs, $c, 3);
                                $c += 2;
                                break;

                            case ($ord_chrs_c & 0xF8) == 0xF0:
                                $utf8 .= substr($chrs, $c, 4);
                                $c += 3;
                                break;

                            case ($ord_chrs_c & 0xFC) == 0xF8:
                                $utf8 .= substr($chrs, $c, 5);
                                $c += 4;
                                break;

                            case ($ord_chrs_c & 0xFE) == 0xFC:
                                $utf8 .= substr($chrs, $c, 6);
                                $c += 5;
                                break;

                        }

                    }

                    return $utf8;

                } elseif (preg_match('/^\[.*\]$/s', $str) || preg_match('/^\{.*\}$/s', $str)) {


                    if ($str{0} == '[') {
                        $stk = array(SERVICES_JSON_IN_ARR);
                        $arr = array();
                    } else {
                        if ($this->use & SERVICES_JSON_LOOSE_TYPE) {
                            $stk = array(SERVICES_JSON_IN_OBJ);
                            $obj = array();
                        } else {
                            $stk = array(SERVICES_JSON_IN_OBJ);
                            $obj = new stdClass();
                        }
                    }

                    array_push($stk, array('what'  => SERVICES_JSON_SLICE,
                                           'where' => 0,
                                           'delim' => false));

                    $chrs = substr($str, 1, -1);
                    $chrs = $this->reduce_string($chrs);

                    if ($chrs == '') {
                        if (reset($stk) == SERVICES_JSON_IN_ARR) {
                            return $arr;

                        } else {
                            return $obj;

                        }
                    }


                    $strlen_chrs = strlen($chrs);

                    for ($c = 0; $c <= $strlen_chrs; ++$c) {

                        $top = end($stk);
                        $substr_chrs_c_2 = substr($chrs, $c, 2);

                        if (($c == $strlen_chrs) || (($chrs{$c} == ',') && ($top['what'] == SERVICES_JSON_SLICE))) {
                            $slice = substr($chrs, $top['where'], ($c - $top['where']));
                            array_push($stk, array('what' => SERVICES_JSON_SLICE, 'where' => ($c + 1), 'delim' => false));

                            if (reset($stk) == SERVICES_JSON_IN_ARR) {
                                array_push($arr, $this->decode($slice));

                            } elseif (reset($stk) == SERVICES_JSON_IN_OBJ) {
                                $parts = array();

                                if (preg_match('/^\s*(["\'].*[^\\\]["\'])\s*:\s*(\S.*),?$/Uis', $slice, $parts)) {

                                    $key = $this->decode($parts[1]);
                                    $val = $this->decode($parts[2]);

                                    if ($this->use & SERVICES_JSON_LOOSE_TYPE) {
                                        $obj[$key] = $val;
                                    } else {
                                        $obj->$key = $val;
                                    }
                                } elseif (preg_match('/^\s*(\w+)\s*:\s*(\S.*),?$/Uis', $slice, $parts)) {

                                    $key = $parts[1];
                                    $val = $this->decode($parts[2]);

                                    if ($this->use & SERVICES_JSON_LOOSE_TYPE) {
                                        $obj[$key] = $val;
                                    } else {
                                        $obj->$key = $val;
                                    }
                                }

                            }

                        } elseif ((($chrs{$c} == '"') || ($chrs{$c} == "'")) && ($top['what'] != SERVICES_JSON_IN_STR)) {
                            array_push($stk, array('what' => SERVICES_JSON_IN_STR, 'where' => $c, 'delim' => $chrs{$c}));

                        } elseif (($chrs{$c} == $top['delim']) && ($top['what'] == SERVICES_JSON_IN_STR) && ((strlen(substr($chrs, 0, $c)) - strlen(rtrim(substr($chrs, 0, $c), '\\'))) % 2 != 1)) {

                            array_pop($stk);

                        } elseif (($chrs{$c} == '[') && in_array($top['what'], array(SERVICES_JSON_SLICE, SERVICES_JSON_IN_ARR, SERVICES_JSON_IN_OBJ))) {

                            array_push($stk, array('what' => SERVICES_JSON_IN_ARR, 'where' => $c, 'delim' => false));

                        } elseif (($chrs{$c} == ']') && ($top['what'] == SERVICES_JSON_IN_ARR)) {

                            array_pop($stk);

                        } elseif (($chrs{$c} == '{') && in_array($top['what'], array(SERVICES_JSON_SLICE, SERVICES_JSON_IN_ARR, SERVICES_JSON_IN_OBJ))) {

                            array_push($stk, array('what' => SERVICES_JSON_IN_OBJ, 'where' => $c, 'delim' => false));

                        } elseif (($chrs{$c} == '}') && ($top['what'] == SERVICES_JSON_IN_OBJ)) {
                            array_pop($stk);
                        } elseif (($substr_chrs_c_2 == '/*') && in_array($top['what'], array(SERVICES_JSON_SLICE, SERVICES_JSON_IN_ARR, SERVICES_JSON_IN_OBJ))) {

                            array_push($stk, array('what' => SERVICES_JSON_IN_CMT, 'where' => $c, 'delim' => false));
                            $c++;


                        } elseif (($substr_chrs_c_2 == '*/') && ($top['what'] == SERVICES_JSON_IN_CMT)) {

                            array_pop($stk);
                            $c++;

                            for ($i = $top['where']; $i <= $c; ++$i)
                                $chrs = substr_replace($chrs, ' ', $i, 1);



                        }

                    }

                    if (reset($stk) == SERVICES_JSON_IN_ARR) {
                        return $arr;

                    } elseif (reset($stk) == SERVICES_JSON_IN_OBJ) {
                        return $obj;

                    }

                }
        }
    }


    function isError($data, $code = null)
    {
        if (class_exists('pear')) {
            return PEAR::isError($data, $code);
        } elseif (is_object($data) && (get_class($data) == 'services_json_error' || is_subclass_of($data, 'services_json_error'))) {
            return true;
        }

        return false;
    }
}

// stdClass в многомерный массив
function objectToArray($d) {

    if (is_object($d)) {
        $d = get_object_vars($d);
    }

    if (is_array($d)) {
        return array_map(__FUNCTION__, $d);
    }
    else {
        return $d;
    }
}



function watchArray($var) {
    if (TRUE === is_array($var)) {
        foreach ($var as $key => $value) {
            $var[$key] = watchArray($value);
        }

        return $var;
    } else {
        return jdecoder($var);
    }
}


function jdecoder($json_str) {
    $cyr_chars = array (
        'u0430' => 'а', 'u0410' => 'А',
        'u0431' => 'б', 'u0411' => 'Б',
        'u0432' => 'в', 'u0412' => 'В',
        'u0433' => 'г', 'u0413' => 'Г',
        'u0434' => 'д', 'u0414' => 'Д',
        'u0435' => 'е', 'u0415' => 'Е',
        'u0451' => 'ё', 'u0401' => 'Ё',
        'u0436' => 'ж', 'u0416' => 'Ж',
        'u0437' => 'з', 'u0417' => 'З',
        'u0438' => 'и', 'u0418' => 'И',
        'u0439' => 'й', 'u0419' => 'Й',
        'u043a' => 'к', 'u041a' => 'К',
        'u043b' => 'л', 'u041b' => 'Л',
        'u043c' => 'м', 'u041c' => 'М',
        'u043d' => 'н', 'u041d' => 'Н',
        'u043e' => 'о', 'u041e' => 'О',
        'u043f' => 'п', 'u041f' => 'П',
        'u0440' => 'р', 'u0420' => 'Р',
        'u0441' => 'с', 'u0421' => 'С',
        'u0442' => 'т', 'u0422' => 'Т',
        'u0443' => 'у', 'u0423' => 'У',
        'u0444' => 'ф', 'u0424' => 'Ф',
        'u0445' => 'х', 'u0425' => 'Х',
        'u0446' => 'ц', 'u0426' => 'Ц',
        'u0447' => 'ч', 'u0427' => 'Ч',
        'u0448' => 'ш', 'u0428' => 'Ш',
        'u0449' => 'щ', 'u0429' => 'Щ',
        'u044a' => 'ъ', 'u042a' => 'Ъ',
        'u044b' => 'ы', 'u042b' => 'Ы',
        'u044c' => 'ь', 'u042c' => 'Ь',
        'u044d' => 'э', 'u042d' => 'Э',
        'u044e' => 'ю', 'u042e' => 'Ю',
        'u044f' => 'я', 'u042f' => 'Я',
    );

    foreach ($cyr_chars as $key => $value) {
    $json_str = str_replace($key, $value, $json_str);
    }
    return $json_str;
}