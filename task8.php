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
    <label for="transport">Тип транспорту:</label>
    <select name="transport" id="transport">
      <option value="Танк">Танк</option>
      <option value="Бронетранспортер">Бронетранспортер</option>
      <option value="Гелікоптер">Гелікоптер</option>
      <option value="Літак">Літак</option>
      <option value="БМП">БМП</option>
    </select>
    <br>
    <div>
      <input type="radio" id="count" name="count" value="zero">
      <label for="max">Транспорт, якого немає в частині</label>
    </div>
    <br>
    <div>
      <input type="radio" id="count" name="count" value="more">
      <label for="min">Транспорт, якого >5 одиниць в частині</label>
    </div>
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
    $count = htmlentities(mysqli_real_escape_string($link, $_GET['count']));
    $transport = htmlentities(mysqli_real_escape_string($link, $_GET['transport']));


    if ($count == 'more') {
      $sql = "SELECT *
      FROM chastina c
      JOIN transport t ON t.chastina_id = c.chastina_id
      JOIN transport_types tt ON tt.transport_types_id = t.type
      WHERE tt.name = '$transport' AND t.number > 5";
    } elseif ($count == 'zero') {
      $sql = "SELECT *
      FROM chastina c
      WHERE NOT EXISTS (
          SELECT 1
          FROM transport t
          JOIN transport_types tt ON tt.transport_types_id = t.type
          WHERE t.chastina_id = c.chastina_id AND tt.name = '$transport'
      );";
    }

    // Виконання запиту
    $result = mysqli_query($link, $sql);
    $rows = mysqli_num_rows($result);

    echo '<h2>Результати пошуку:</h2>';
    echo "Знайдено записів: " . $rows;
    if ($rows > 0) {
      echo "<table>";
      echo "<th>ID</th><th>Назва частини</th><th>Локація</th><th>Бригада</th><th>ID командира</th>";
      for ($i = 0; $i < $rows; ++$i) {
        $row = mysqli_fetch_row($result);
        echo "<tr>";

        for ($j = 0; $j < 5; ++$j) echo "<td>$row[$j]</td>";
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