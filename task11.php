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
      <option value="department">Відділення</option>
      <option value="vzvod">Взвод</option>
      <option value="rota">Рота</option>
      <option value="chastina">Частина</option>
      <option value="brigada">Бригада</option>
      <option value="army">Армія</option>
    </select>
    <br>
    <label for="id">ID підрозділу</label>
    <input type="text" id="id" name="id">
    <br>
    <label for="spec">Спеціальність:</label>
    <select name="spec" id="spec">
      <option value="Піхотинець/стрілець">Піхотинець/стрілець</option>
      <option value="Артилерист">Артилерист</option>
      <option value="Інженер-сапер">Інженер-сапер</option>
      <option value="Розвідник">Розвідник</option>
      <option value="Командир взводу/компанії/батальйону">Командир взводу/компанії/батальйону</option>
      <option value="Танкіст">Танкіст</option>
      <option value="Спеціаліст зв'язку">Спеціаліст зв'язку</option>
      <option value="Медичний працівник">Медичний працівник</option>
      <option value="Морський піхотинець">Морський піхотинець</option>
      <option value="Логіст">Логіст</option>
    </select>
    <br>
    <br>
    <input type="submit" name="submit" value="Відправити">
  </form>

  <?php
  // Отримання даних з форми
  require_once 'connection.php';
  $link = mysqli_connect($host, $user, $password, $database)
    or die("Помилка" . mysqli_error($link));

  if (isset($_GET['submit'])){
    // Отримання даних з форми
    $type = htmlentities(mysqli_real_escape_string($link, $_GET['type']));
    $id = htmlentities(mysqli_real_escape_string($link, $_GET['id']));
    $spec = htmlentities(mysqli_real_escape_string($link, $_GET['spec']));

    if($type == 'army'){
      $sql = "SELECT s.*
      FROM soldier s
      JOIN speciality sp ON s.soldier_id = sp.soldier_id
      JOIN specialities sps ON sp.speciality = sps.specialities_id
      JOIN department d ON s.department_id = d.department_id
      JOIN vzvod v ON d.vzvod_id = v.vzvod_id
      JOIN rota r ON v.rota_id = r.rota_id
      JOIN chastina c ON r.chastina_id = c.chastina_id
      JOIN brigada b ON c.brigada_id = b.brigada_id
      JOIN army a ON b.army_id = a.army_id
      WHERE sps.name = '$spec' AND a.army_id = $id;";
    }
    elseif ($type == 'brigada') {
      $sql = "SELECT s.*
      FROM soldier s
      JOIN speciality sp ON s.soldier_id = sp.soldier_id
      JOIN specialities sps ON sp.speciality = sps.specialities_id
      JOIN department d ON s.department_id = d.department_id
      JOIN vzvod v ON d.vzvod_id = v.vzvod_id
      JOIN rota r ON v.rota_id = r.rota_id
      JOIN chastina c ON r.chastina_id = c.chastina_id
      JOIN brigada b ON c.brigada_id = b.brigada_id
      WHERE sps.name = '$spec' AND b.brigada_id = $id;";
    } elseif ($type == 'chastina') {
      $sql = "SELECT s.*
      FROM soldier s
      JOIN speciality sp ON s.soldier_id = sp.soldier_id
      JOIN specialities sps ON sp.speciality = sps.specialities_id
      JOIN department d ON s.department_id = d.department_id
      JOIN vzvod v ON d.vzvod_id = v.vzvod_id
      JOIN rota r ON v.rota_id = r.rota_id
      JOIN chastina c ON r.chastina_id = c.chastina_id
      WHERE sps.name = '$spec' AND c.chastina_id = $id;";
    } elseif ($type == 'rota') {
      $sql = "SELECT s.*
      FROM soldier s
      JOIN speciality sp ON s.soldier_id = sp.soldier_id
      JOIN specialities sps ON sp.speciality = sps.specialities_id
      JOIN department d ON s.department_id = d.department_id
      JOIN vzvod v ON d.vzvod_id = v.vzvod_id
      JOIN rota r ON v.rota_id = r.rota_id
      WHERE sps.name = '$spec' AND r.rota_id = $id;";
    }
    elseif ($type == 'vzvod') {
      $sql = "SELECT s.*
      FROM soldier s
      JOIN speciality sp ON s.soldier_id = sp.soldier_id
      JOIN specialities sps ON sp.speciality = sps.specialities_id
      JOIN department d ON s.department_id = d.department_id
      JOIN vzvod v ON d.vzvod_id = v.vzvod_id
      WHERE sps.name = '$spec' AND v.vzvod_id = $id;";
    } elseif ($type == 'department'){
      $sql = "SELECT s.*
      FROM soldier s
      JOIN speciality sp ON s.soldier_id = sp.soldier_id
      JOIN specialities sps ON sp.speciality = sps.specialities_id
      JOIN department d ON s.department_id = d.department_id
      WHERE sps.name = '$spec' AND d.department_id = $id;";
    }
    // Виконання запиту
    $result = mysqli_query($link, $sql);
    $rows = mysqli_num_rows($result);

    echo '<h2>Результати пошуку:</h2>';
    echo "Знайдено записів: " . $rows;
    if ($rows > 0) {
      echo "<table>";
      echo "<th>ID</th><th>ПІБ</th><th>Звання</th><th>Дата випуску з академії</th><th>Дата підвищення</th><th>Номер відділення</th>";
      for ($i = 0; $i < $rows; ++$i) {
        $row = mysqli_fetch_row($result);
        echo "<tr>";

        for ($j = 0; $j < 6; ++$j) echo "<td>$row[$j]</td>";
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