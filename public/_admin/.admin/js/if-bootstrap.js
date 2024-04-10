if ($("textarea").is(".texteditor")) {
    tinyEditor = function (editor) {
        editor.addButton('colbutton', {
            type: 'menubutton',
            text: 'Вставить объекты',
            icon: false,
            menu: [{
                text: 'Изображение',
                onclick: function () {
                    editor.insertContent('{image:[name], width, height}');
                }
            }, {
                text: 'Две колонки',
                onclick: function () {
                    editor.insertContent('<div class="container-fluid"><div class="custom-col col-md-6"><h3>Заголовок 1</h3><p>КОЛОНКА 1</p></div><div class="custom-col col-md-6"><h3>Заголовок 2</h3><p>КОЛОНКА 2</p></div></div><p>Начало новой строки</p>');
                }
            }, {
                text: 'Три колонки',
                onclick: function () {
                    editor.insertContent('<div class="container-fluid"><div class="custom-col col-md-4"><h3>Заголовок 1</h3><p>КОЛОНКА 1</p></div><div class="custom-col col-md-4"><h3>Заголовок 2</h3><p>КОЛОНКА 2</p></div><div class="custom-col col-md-4"><h3>Заголовок 3</h3><p>КОЛОНКА 3</p></div></div><p>Начало новой строки</p>');
                }
            }, {
                text: 'Четыре колонки',
                onclick: function () {
                    editor.insertContent('<div class="container-fluid"><div class="custom-col col-md-3"><h3>Заголовок 1</h3><p>КОЛОНКА 1</p></div><div class="custom-col col-md-3"><h3>Заголовок 2</h3><p>КОЛОНКА 2</p></div><div class="custom-col col-md-3"><h3>Заголовок 3</h3><p>КОЛОНКА 3</p></div><div class="custom-col col-md-3"><h3>Заголовок 4</h3><p>КОЛОНКА 4</p></div></div><p>Начало новой строки</p>');
                }
            }, {
                text:  'Пять колонок',
                onclick: function () {
                    editor.insertContent('<div class="container-fluid"><div class="custom-col col-md-2 col-md-offset-1"><h3>Заголовок 1</h3><p>КОЛОНКА 1</p></div><div class="custom-col col-md-2"><h3>Заголовок 2</h3><p>КОЛОНКА 2</p></div><div class="custom-col col-md-2"><h3>Заголовок 3</h3><p>КОЛОНКА 3</p></div><div class="custom-col col-md-2"><h3>Заголовок 4</h3><p>КОЛОНКА 4</p></div><div class="custom-col col-md-2"><h3>Заголовок 5</h3><p>КОЛОНКА 5</p></div></div><p>Начало новой строки</p>');
                }
            }, {
                text:  'Шесть колонок',
                onclick: function () {
                    editor.insertContent('<div class="container-fluid"><div class="custom-col col-md-2"><h3>Заголовок 1</h3><p>КОЛОНКА 1</p></div><div class="custom-col col-md-2"><h3>Заголовок 2</h3><p>КОЛОНКА 2</p></div><div class="custom-col col-md-2"><h3>Заголовок 3</h3><p>КОЛОНКА 3</p></div><div class="custom-col col-md-2"><h3>Заголовок 4</h3><p>КОЛОНКА 4</p></div><div class="custom-col col-md-2"><h3>Заголовок 5</h3><p>КОЛОНКА 5</p></div><div class="custom-col col-md-2"><h3>Заголовок 6</h3><p>КОЛОНКА 6</p></div></div><p>Начало новой строки</p>');
                }
            }
            ]
        });
    } // Setup FUnction

}
