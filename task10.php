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
    <label for="max">ID підрозділу</label>
    <input type="text" id="id" name="id">
    <br>
    <div>
      <input type="radio" id="count" name="count" value="zero">
      <label for="max">Спеціальності, яких немає в підрозділі</label>
    </div>
    <br>
    <div>
      <input type="radio" id="count" name="count" value="more">
      <label for="min">Спеціальності, кількість яких >5 в підрозділі</label>
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
    $pidrozdil = htmlentities(mysqli_real_escape_string($link, $_GET['type']));
    $id = htmlentities(mysqli_real_escape_string($link, $_GET['id']));

    if ($type == 'zero') {
      if ($pidrozdil == 'army') {
        $sql = "SELECT s.name
        FROM specialities s
        WHERE NOT EXISTS (
            SELECT 1
            FROM soldier
            JOIN department d ON soldier.department_id = d.department_id
            JOIN vzvod v ON d.vzvod_id = v.vzvod_id
            JOIN rota r ON v.rota_id = r.rota_id
            JOIN chastina c ON r.chastina_id = c.chastina_id
            JOIN brigada b ON c.brigada_id = b.brigada_id
            JOIN army a ON b.army_id = a.army_id
            JOIN speciality sp ON soldier.soldier_id = sp.soldier_id
            WHERE a.army_id = $id AND sp.speciality = s.specialities_id
        );";
      } elseif ($pidrozdil == 'brigada') {
        $sql = "SELECT s.name
        FROM specialities s
        WHERE NOT EXISTS (
            SELECT 1
            FROM soldier
            JOIN department d ON soldier.department_id = d.department_id
            JOIN vzvod v ON d.vzvod_id = v.vzvod_id
            JOIN rota r ON v.rota_id = r.rota_id
            JOIN chastina c ON r.chastina_id = c.chastina_id
            JOIN brigada b ON c.brigada_id = b.brigada_id
            JOIN speciality sp ON soldier.soldier_id = sp.soldier_id
            WHERE b.brigada_id = $id AND sp.speciality = s.specialities_id
        );";
      } elseif ($pidrozdil == 'chastina') {
        $sql = "SELECT s.name
        FROM specialities s
        WHERE NOT EXISTS (
            SELECT 1
            FROM soldier
            JOIN department d ON soldier.department_id = d.department_id
            JOIN vzvod v ON d.vzvod_id = v.vzvod_id
            JOIN rota r ON v.rota_id = r.rota_id
            JOIN chastina c ON r.chastina_id = c.chastina_id
            JOIN speciality sp ON soldier.soldier_id = sp.soldier_id
            WHERE c.chastina_id = $id AND sp.speciality = s.specialities_id
        );";
      }
    } elseif ($type == 'more') {
      if ($pidrozdil == 'army') {
        $sql = "SELECT spts.name AS speciality_name, COUNT(*) AS count
        FROM soldier s
        JOIN speciality spt ON s.soldier_id = spt.soldier_id
        JOIN specialities spts ON spt.speciality = spts.specialities_id
        JOIN department d ON s.department_id = d.department_id
        JOIN vzvod v ON d.vzvod_id = v.vzvod_id
        JOIN rota r ON v.rota_id = r.rota_id
        JOIN chastina c ON r.chastina_id = c.chastina_id
        JOIN brigada b ON c.brigada_id = b.brigada_id
        JOIN army a ON b.army_id = a.army_id
        WHERE a.army_id = $id
        GROUP BY spts.name
        HAVING COUNT(*) > 5;";
      } elseif ($pidrozdil == 'brigada') {
        $sql = "SELECT spts.name AS speciality_name, COUNT(*) AS count
        FROM soldier s
        JOIN speciality spt ON s.soldier_id = spt.soldier_id
        JOIN specialities spts ON spt.speciality = spts.specialities_id
        JOIN department d ON s.department_id = d.department_id
        JOIN vzvod v ON d.vzvod_id = v.vzvod_id
        JOIN rota r ON v.rota_id = r.rota_id
        JOIN chastina c ON r.chastina_id = c.chastina_id
        JOIN brigada b ON c.brigada_id = b.brigada_id
        WHERE b.brigada_id = $id
        GROUP BY spts.name
        HAVING COUNT(*) > 5;";
      } elseif ($pidrozdil == 'chastina') {
        $sql = "SELECT spts.name AS speciality_name, COUNT(*) AS count
        FROM soldier s
        JOIN speciality spt ON s.soldier_id = spt.soldier_id
        JOIN specialities spts ON spt.speciality = spts.specialities_id
        JOIN department d ON s.department_id = d.department_id
        JOIN vzvod v ON d.vzvod_id = v.vzvod_id
        JOIN rota r ON v.rota_id = r.rota_id
        JOIN chastina c ON r.chastina_id = c.chastina_id
        WHERE c.chastina_id = $id
        GROUP BY spts.name
        HAVING COUNT(*) > 5;";
      }
    }
    // Виконання запиту
    $result = mysqli_query($link, $sql);
    $rows = mysqli_num_rows($result);

    echo '<h2>Результати пошуку:</h2>';
    echo "Знайдено записів: " . $rows;
    if ($rows > 0) {
      echo "<table>";
      echo "<th>Спеціальність</th><th>К-ть спеціалістів</th>";
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