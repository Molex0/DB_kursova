<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <style>
    body {
      display: flex;
      justify-content: center;
      flex-direction: column;
    }

    form {
      margin: 120px auto;
      width: 300px;
      display: flex;
      flex-direction: column;
    }

    table {
      border-collapse: collapse;
      width: 100%;
      margin: 20px 0;
    }

    th,
    td {
      text-align: left;
      padding: 8px;
    }

    th {
      background-color: #ccc;
    }

    tr:nth-child(even) {
      background-color: #f2f2f2;
    }
  </style>
</head>

<body>
  <h1><a href="index.php"><-- To main</a></h1>
  <form method="get">
    <label for="type">Тип підрозділу:</label>
    <select name="type" id="type">
      <option value="rota">Рота</option>
      <option value="chastina">Частина</option>
      <option value="brigada">Бригада</option>
      <option value="army">Армія</option>
    </select>
    <br>
    <label for="name">Назва підрозділу:</label>
    <input type="text" name="name" id="name">
    <br>
    <label for="rank">Ранг:</label>
    <select name="rank" id="rank">
      <option value="">Всі</option>
      <option value="Генерал">Генерал</option>
      <option value="Полковник">Полковник</option>
      <option value="Підполковник">Підполковник</option>
      <option value="Майор">Майор</option>
      <option value="Капітан">Капітан</option>
      <option value="Лейтенант">Лейтенант</option>
    </select>
    <br>
    <input type="submit" name="submit" value="Відправити">
  </form>

  <?php
  // Отримання даних з форми
  require_once 'connection.php';
  $link = mysqli_connect($host, $user, $password, $database)
    or die("Помилка" . mysqli_error($link));

  if (isset($_GET['submit'])) {


    // Отримання даних з форми
    $type = htmlentities(mysqli_real_escape_string($link, $_GET['type']));
    $name = htmlentities(mysqli_real_escape_string($link, $_GET['name']));
    $rank = htmlentities(mysqli_real_escape_string($link, $_GET['rank']));

    if (empty($rank)) {
      $rank_condition = "s.soldier_rank IN ('Генерал', 'Полковник', 'Підполковник', 'Майор', 'Капітан', 'Лейтенант')";
    } else {
      $rank_condition = "s.soldier_rank = '$rank'";
    }
    if ($type == 'army') {
      $sql = "SELECT s.soldier_id, s.name, s.soldier_rank, s.department_id 
        FROM soldier s 
        INNER JOIN department ON s.department_id = department.department_id
        INNER JOIN vzvod ON department.vzvod_id = vzvod.vzvod_id
        INNER JOIN rota ON vzvod.rota_id = rota.rota_id
        INNER JOIN chastina ON rota.chastina_id = chastina.chastina_id
        INNER JOIN brigada ON chastina.brigada_id = brigada.brigada_id
        INNER JOIN army ON brigada.army_id = army.army_id AND army.name = '$name'
        WHERE $rank_condition";
    } elseif ($type == 'brigada') {
      $sql = "SELECT s.soldier_id, s.name, s.soldier_rank, s.department_id 
          FROM soldier s 
          INNER JOIN department ON s.department_id = department.department_id
          INNER JOIN vzvod ON department.vzvod_id = vzvod.vzvod_id
          INNER JOIN rota ON vzvod.rota_id = rota.rota_id
          INNER JOIN chastina ON rota.chastina_id = chastina.chastina_id
          INNER JOIN brigada ON chastina.brigada_id = brigada.brigada_id AND brigada.name = '$name'
          WHERE $rank_condition";
    } elseif ($type == 'chastina') {
      $sql = "SELECT s.soldier_id, s.name, s.soldier_rank, s.department_id 
            FROM soldier s 
            INNER JOIN department ON s.department_id = department.department_id
            INNER JOIN vzvod ON department.vzvod_id = vzvod.vzvod_id
            INNER JOIN rota ON vzvod.rota_id = rota.rota_id
            INNER JOIN chastina ON rota.chastina_id = chastina.chastina_id AND chastina.name = '$name'
            WHERE $rank_condition";
    } elseif ($type == 'rota') {
      $sql = "SELECT s.soldier_id, s.name, s.soldier_rank, s.department_id 
            FROM soldier s 
            INNER JOIN department ON s.department_id = department.department_id
            INNER JOIN vzvod ON department.vzvod_id = vzvod.vzvod_id
            INNER JOIN rota ON vzvod.rota_id = rota.rota_id AND rota.name = '$name'
            WHERE $rank_condition";
    }

    // Виконання запиту
    $result = mysqli_query($link, $sql);
    $rows = mysqli_num_rows($result);

    echo '<h2>Результати пошуку:</h2>';
    echo "Знайдено записів: " . $rows;
    if ($rows > 0) {
      echo "<table>";
      echo "<th>ID</th><th>ПІБ</th><th>Звання</th><th>Відділення</th>";
      for ($i = 0; $i < $rows; ++$i) {
        $row = mysqli_fetch_row($result);
        echo "<tr>";

        for ($j = 0; $j < 4; ++$j) echo "<td>$row[$j]</td>";
        echo "</tr>";
      }
      echo "</table>";
      mysqli_free_result($result);
    } else {
      echo "<h3>No results found.</h3>";
    }

    // закриваємо з'єднання з базою даних
    mysqli_close($link);
  }

  ?>
</body>

</html>