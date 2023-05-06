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
    <div>
      <input type="radio" id="count" name="count" value="zero">
      <label for="max">Споруди без частин</label>
    </div>
    <br>
    <div>
      <input type="radio" id="count" name="count" value="more">
      <label for="min">Споруди з >1 частин</label>
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
    $type = htmlentities(mysqli_real_escape_string($link, $_GET['count']));


    if ($type == 'zero') {
      $sql = "SELECT s.*, COUNT(c.chastina_id) as num
      FROM sporydi s
      LEFT JOIN chastina c ON s.sporydi_id = c.location
      GROUP BY s.sporydi_id
      HAVING COUNT(c.chastina_id) = 0;";
    } elseif ($type == 'more') {
      $sql = "SELECT s.*, COUNT(c.chastina_id) as num
      FROM sporydi s
      LEFT JOIN chastina c ON s.sporydi_id = c.location
      GROUP BY s.sporydi_id
      HAVING COUNT(c.chastina_id) > 1 OR COUNT(c.chastina_id) IS NULL;";
    }
    // Виконання запиту
    $result = mysqli_query($link, $sql);
    $rows = mysqli_num_rows($result);

    echo '<h2>Результати пошуку:</h2>';
    echo "Знайдено записів: " . $rows;
    if ($rows > 0) {
      echo "<table>";
      echo "<th>ID</th><th>Локація</th><th>К-ть частин</th>";
      for ($i = 0; $i < $rows; ++$i) {
        $row = mysqli_fetch_row($result);
        echo "<tr>";

        for ($j = 0; $j < 3; ++$j) echo "<td>$row[$j]</td>";
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