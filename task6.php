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
      <option value="chastina">Частина</option>
      <option value="brigada">Бригада</option>
      <option value="army">Армія</option>
    </select>
    <br>
    <label for="name">ID підрозділу:</label>
    <input type="text" name="id" id="id">
    <br>
    <label for="transport">Тип транспорту:</label>
    <select name="transport" id="transport">
      <option value="">Всі</option>
      <option value="Танк">Танк</option>
      <option value="Бронетраспортер">Бронетраспортер</option>
      <option value="Гелікоптер">Гелікоптер</option>
      <option value="Літак">Літак</option>
      <option value="БМП">БМП</option>
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
    $id = htmlentities(mysqli_real_escape_string($link, $_GET['id']));
    $transport = htmlentities(mysqli_real_escape_string($link, $_GET['transport']));

    if(empty($transport)){
      $condition = "";
    }
    else{
      $condition = "AND tt.name = '$transport'";
    }

    if ($type == 'army') {
      $sql = "SELECT tt.name, COALESCE(SUM(t.number), 0) as total_number
      FROM army a
      JOIN brigada b ON a.army_id = b.army_id
      JOIN chastina c ON b.brigada_id = c.brigada_id
      JOIN transport t ON c.chastina_id = t.chastina_id
      JOIN transport_types tt ON t.type = tt.transport_types_id
      WHERE a.army_id = $id $condition
      GROUP BY tt.name;";

    } elseif ($type == 'brigada') {
      $sql = "SELECT tt.name, COALESCE(SUM(t.number), 0) as total_number
      FROM brigada b
      JOIN chastina c ON b.brigada_id = c.brigada_id
      JOIN transport t ON c.chastina_id = t.chastina_id
      JOIN transport_types tt ON t.type = tt.transport_types_id
      WHERE b.brigada_id = $id $condition
      GROUP BY tt.name;";

    } elseif ($type == 'chastina') {
      $sql = "SELECT tt.name, COALESCE(SUM(t.number), 0) as total_number
      FROM chastina c
      JOIN transport t ON c.chastina_id = t.chastina_id
      JOIN transport_types tt ON t.type = tt.transport_types_id
      WHERE c.chastina_id = $id $condition
      GROUP BY tt.name;";
    }

    // Виконання запиту
    $result = mysqli_query($link, $sql);
    $rows = mysqli_num_rows($result);

    echo '<h2>Результати пошуку:</h2>';
    echo "Знайдено записів: " . $rows;
    if ($rows > 0) {
      echo "<table>";
      echo "<th>Тип транспорту</th><th>Кількість у підрозділі</th>";
      for ($i = 0; $i < $rows; ++$i) {
        $row = mysqli_fetch_row($result);
        echo "<tr>";

        for ($j = 0; $j < 2; ++$j) echo "<td>$row[$j]</td>";
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