<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <style>
    body{
      display: flex;
      justify-content: center;
      flex-direction: column;
    }
    form{
      margin:120px auto;
      width: 300px;
      display: flex;
      flex-direction: column;
    }
    input{
      margin-bottom: 30px;
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
  <form method="get" action="">
    <label for="army-name">Назва армії:</label>
    <input type="text" id="army-name" name="army-name">

    <label for="brigade-name">Назва бригади:</label>
    <input type="text" id="brigade-name" name="brigade-name">

    <button type="submit" name="submit">Пошук</button>
  </form>
  <?php
      require_once 'connection.php';
      $link = mysqli_connect($host, $user, $password, $database)
        or die("Помилка" . mysqli_error($link));
  if (isset($_GET['submit'])) {
    
    // отримуємо значення полів форми
    $armyName = htmlentities(mysqli_real_escape_string($link, $_GET['army-name']));
    $brigadeName = htmlentities(mysqli_real_escape_string($link, $_GET['brigade-name']));


    // виконуємо запит до бази даних, щоб знайти частини, які належать вказаній армії та бригаді
    $sql = "SELECT chastina.name AS chastina_name, soldier.name AS soldier_name
    FROM chastina 
    INNER JOIN brigada ON chastina.brigada_id = brigada.brigada_id 
    INNER JOIN army ON brigada.army_id = army.army_id 
    INNER JOIN soldier ON chastina.commander_id = soldier.soldier_id
    WHERE army.name LIKE '%$armyName%' AND brigada.name LIKE '%$brigadeName%'";

    $result = mysqli_query($link, $sql);

    // виводимо результати запиту
    echo '<h2>Результати пошуку:</h2>';
    $rows = mysqli_num_rows($result);
    if (mysqli_num_rows($result) > 0) {
      echo "<table>";
      echo "<tr><th>Частина</th><th>Командир відділення</th>";
      for ($i = 0; $i < $rows; ++$i) {
        $row = mysqli_fetch_row($result);
        echo "<tr>";

        for ($j = 0; $j < 2; ++$j) echo "<td>$row[$j]</td>";
        echo "</tr>";
      }
      echo "</table>";
      mysqli_free_result($result);
    } else {
      echo "No results found.";
    }

    // закриваємо з'єднання з базою даних
    mysqli_close($link);
  }
  ?>

</body>

</html>