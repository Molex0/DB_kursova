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
    <label for="eq">Тип озброєння:</label>
    <select name="eq" id="eq">
      <option value="БПЛА">БПЛА</option>
      <option value="Вогнепальна зброя">Вогнепальна зброя</option>
      <option value="Артилерія">Артилерія </option>
      <option value="Ракетні комплекси">Ракетні комплекси</option>
      <option value="Бронежилети">Бронежилети</option>
      <option value="Підривні пристрої">Підривні пристрої</option>
    </select>
    <br>
    <div>
      <input type="radio" id="count" name="count" value="zero">
      <label for="max">Озброєння, якого немає в частині</label>
    </div>
    <br>
    <div>
      <input type="radio" id="count" name="count" value="more">
      <label for="min">Озброєння, якого >10 одиниць в частині</label>
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
    $eq = htmlentities(mysqli_real_escape_string($link, $_GET['eq']));


    if ($count == 'more') {
      $sql = "SELECT *
      FROM chastina c
      JOIN equipment e ON e.chastina_id = c.chastina_id
      JOIN equipment_types et ON et.equipment_types_id = e.type
      WHERE et.name = '$eq' AND e.number > 10";
    } elseif ($count == 'zero') {
      $sql = "SELECT *
      FROM chastina c
      WHERE NOT EXISTS (
          SELECT 1
          FROM equipment e
          JOIN equipment_types et ON et.equipment_types_id = e.type
          WHERE e.chastina_id = c.chastina_id AND et.name = '$eq'
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