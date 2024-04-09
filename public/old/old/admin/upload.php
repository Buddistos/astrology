<html>
<head>
  <title>Загрузка файлов на сервер</title>
</head>
<body>
    <?php
        if($_FILES["filename"]["size"] > 1024*3*1024)
        {
            echo ("Размер файла превышает три мегабайта");
            exit;
        }
        // Проверяем загружен ли файл
        if(is_uploaded_file($_FILES["filename"]["tmp_name"]))
        {
            // Если файл загружен успешно, перемещаем его
            // из временной директории в конечную
            move_uploaded_file($_FILES["filename"]["tmp_name"], "/home/u34097/tmp/".$_FILES["filename"]["name"]);
            echo "Успешно загружен";
        } else {
            echo("Ошибка загрузки файла");
        }
    ?>
      <h2><p><b> Форма для загрузки файлов </b></p></h2>
      <form action="upload.php" method="post" enctype="multipart/form-data">
      <input type="file" name="filename"><br> 
      <input type="submit" value="Загрузить"><br>
      </form>
</body>
</html>