<?php
    echo "
        <center class='noprint'>
            <b class='attention'>Поделитесь невероятным \"совпадением\" астропрогноза с реальностью жизни:</b><br>
            <span class='info'><span>Будет сохранен только <?php echo $gorotitle[$id_gorogroup]; ?> на указанную дату.</span></span><br>";
?>
<script type="text/javascript">(function() {
  if (window.pluso) {return};
   var d = document, s = d.createElement('script'), g = 'getElementsByTagName';
   s.type = 'text/javascript'; s.charset='UTF-8'; s.async = true;
   s.src = ('https:' == window.location.protocol ? 'https' : 'http')  + '://x.pluso.ru/pluso-x.js';
   var h=d[g]('body')[0];
   h.appendChild(s);
})();
</script>
<div class="pluso-engine" pluso-sharer={"buttons":"vkontakte,odnoklassniki,facebook,twitter,google,moimir,email,print,more","style":{"size":"big","shape":"round","theme":"theme05","css":"background:transparent"},"orientation":"horizontal","multiline":false}  data-title="%D0%9C%D0%BE%D0%B9%20%D0%BB%D0%B8%D1%87%D0%BD%D1%8B%D0%B9%20%D0%B0%D1%81%D1%82%D1%80%D0%BE%D0%BF%D1%80%D0%BE%D0%B3%D0%BD%D0%BE%D0%B7" data-description="<?php echo $gorotitle[$id_gorogroup]; ?>. Посмотрите, что звезды уготовили мне на <?php echo $cntdate; ?>." data-image="http%3A%2F%2F46.105.39.143%2Fimages%2Fnostradamus.png"></div> 