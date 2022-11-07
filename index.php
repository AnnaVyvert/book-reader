<?php
if ($_COOKIE["page"] == "" && $_GET["page"] == "") {
  setcookie("page", "1");
  for($i=1; $i<6; $i++)
    setcookie("SearchHistory".$i, 'mockup');
  header('Location: index.php?page=1');
  exit;
} else if ($_GET["page"] == "") {

  header('Location: index.php?page=' . $_COOKIE["page"]);
  exit;
} else if ($_GET["page"] != "") {
  setcookie("page", $_GET["page"]);
}
$fullText = file_get_contents("pale.fire.txt");
//$fullText = strip_tags($fullText);
// $textArray = str_split($fullText, 2000);
$textArray = [];
$point = 0;

for ($i = 0; $i < 100; $i++) {
  $to_point = strpos($fullText, '. ', $point + 500);
  $textArray[] = substr($fullText, $point, $to_point - $point + 1);
  // print(substr($fullText, $point, $to_point - $point + 1));
  $point = $to_point + 2;
  if (strlen($fullText) < $point + 500)
    break;
}

$SearchHistory = [];
for($i=1; $i<6; $i++){
  $SearchHistory[$i] = $_COOKIE["SearchHistory".$i] ?? "";
}

if (!empty($_GET["search"]) != "" && empty($_GET["option"])) {
  for($i=5; $i>1; $i--){
    setcookie("SearchHistory".$i, $_COOKIE["SearchHistory".$i], time() - 1);
    setcookie("SearchHistory".$i, $_COOKIE["SearchHistory".$i-1]);
  }
  setcookie("SearchHistory1", $_COOKIE["SearchHistory1"], time() - 1);
  setcookie("SearchHistory1", $_GET["search"]);
  header('Location: index.php?page=' . $_COOKIE["page"] . '&search=' . $_GET["search"] . '&option=1');
}

$matches = array();
$matchesCount = 0;

if (!empty($_GET["search"]) != "" && !empty($_GET["option"])) {
  preg_match_all("/(" . $_GET["search"] . ")/ui", $fullText, $matches, PREG_OFFSET_CAPTURE);

  if (!empty($matches)) {
    $matchesCount = sizeof($matches[0]);
  }


  $currentSentence = getCurrentSentence($fullText,$_GET["option"], $matches);

}


//function test($matches)
//{
//print($matches[0][0][1]);
//print("<pre>");
//print_r($matches[0]);
//	print("</pre>");

//}


function getPreviousSentence($option)
{
  return "...Test1" . $option;
}

function getCurrentSentence($txt, $option, $matches)
{
  $matchPos = $matches[0][$option - 1][1];
  // print('pos'.$matchPos);

  return substr($txt, $matchPos, 10);
}

function getFutureSentence($option)
{
  return "...Test3" . $option;
}



?>
<html lang="ru">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css"
  integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous" />

<head>
  <title>Book-reader</title>
  <style>
    .header {
      /* background-color: #bdb; */
      width: 90%;
      margin-left: 5%;
      margin-top: 1%;
      height: 8%;
      text-align: center;
      display: table;
    }

    .header h1 {
      display: table-cell;
      vertical-align: middle;
    }

    .blank-h {
      height: 2%;
    }

    .blank-w {
      width: 2%;
    }

    .main-search {
      width: 90%;
      margin-left: 5%;
      height: 30%;
      display: table;
    }

    .cmd-input {
      display: table-cell;
      /* background-color: #bdb; */
      width: 40%;
      border-right: 1px solid rgba(0, 0, 0, 0.175);
    }

    .cmd-input>* {
      margin-left: 10%;
    }

    .cmd-input .int-text {
      width: 90%;
      margin-top: 5px;
      margin-bottom: 5px;
      padding: 5px;
    }

    .cmd-input .int-submit {
      width: 90%;
      margin-top: 5px;
      margin-bottom: 5px;
      align-items: center;
      border: 1px solid;
      /* background-color: #afa; */
    }

    .cmd-input ol {
      width: 70%;
      margin-top: 5px;
      margin-bottom: 5px;
      /* background-color: #fff; */
    }

    .highlight {
      /* background-color: FF0#; */
    }

    .search-output {
      display: table-cell;
      /* background-color: #bdb; */
      width: 58%;
    }

    .search-output>* {
      margin-left: 5%;
    }

    .search-output .selector {
      width: 95%;
      margin-top: 5px;
      margin-bottom: 5px;
      padding: 6px;
    }

    .search-output .int-submit {
      width: 95%;
      margin-top: 5px;
      margin-bottom: 5px;
      align-items: center;
      border: 1px solid;
      /* background-color: #afa; */
    }

    .search-output ul {
      width: 86%;
      /* background-color: #fff; */
    }


    .main-reader {
      /* background-color: #bdb; */
      width: 90%;
      margin-left: 5%;
      height: 42%;
      text-align: center;
      display: table;
    }

    .main-reader .reader {
      /* background-color: #fff; */
      width: 90%;
      margin-left: 5%;
      margin-top: 2%;
      height: 80%;
      white-space: pre-wrap;
      overflow: scroll;
      overflow-x: hidden;
    }

    ::-webkit-scrollbar {
      width: 10px;
      background-color: whitesmoke;
    }

    ::-webkit-scrollbar-thumb {
      background-color: whitesmoke;
      border: 1px solid grey;
    }


    .main-reader .cmds {
      display: table;
      width: 90%;
      margin-left: 5%;
      margin-top: 10px;
      margin-bottom: 10px;
    }

    .main-reader .cmds .switch-page {
      display: table-cell;
      /* background-color: #fff; */
      cursor: pointer;
      text-decoration: none;
      color: black;
    }

    .footer {
      /* background-color: #bdb; */
      width: 90%;
      margin-left: 5%;
      text-align: center;
    }

    .page-text {
      margin-left: 25%;
      text-align: left;
    }
  </style>
</head>

<body>
  <div class='card header'>
    <h1>Bookreader | Страница:
      <?php print($_GET['page']+1) ?>
      /
      <?php print(count($textArray)) ?>
    </h1>
  </div>
  <div class="blank-h"></div>

  <div class="card main-search">
    <div class="cmd-input">
      <br>
      <span><b>Поиск в произведении:</b></span><br>
      <form style="margin-bottom: 0px;">
        <input type="hidden" name="page" value="<?php print($_GET["page"]); ?>" />
        <input class="int-text" type="text" name="search" /><br>
        <input class="card int-submit" type="submit" value="Искать в произведении" />
      </form>
      <ol>
        <?php
        for($i=1; $i<6; $i++){
            print('<li>');
            print(mb_substr($SearchHistory[$i], 0, 50));
            print('</li>');
        }
        ?>
      </ol>
    </div>
    <div class="blank-w"></div>

    <div class="search-output">
      <div class="selector">
        <span><br><b>Количество совпадений:
            <?php print($matchesCount); ?>
          </b></span>
        <form style="margin-bottom: 0px;">
          <input type="hidden" name="page" value="<?php print($_GET["page"]); ?>"></input>
          <input type="hidden" name="search" value="<?php print($_GET["search"] ?? "-1"); ?>"></input>
          <?php
          $select = '<select class="selector" name="option">';
          if ($matchesCount == 0)
            $select .= '<option value="1">Совпадений нет</option>';
          for ($i = 0; $i < $matchesCount; $i++) {
            $select .= '<option value="';
            $select .= $i + 1;
            $select .= '">';
            $select .= $i + 1;
            $select .= '-ое совпадение';
            $select .= '</option>';
          }
          $select .= '</select>';
          print($select);
          ?>
          <input class="card int-submit" type="submit" value="Вывести совпадение"></input>
        </form>
        <ul>
          <li>
            <?php print($currentSentence ?? "..."); ?>
          </li>
        </ul>
      </div>
    </div>
  </div>

  <div class="blank-h"></div>

  <div class="card main-reader">
    <div class="reader">
      <div class="page-text">
        <?php
        // print(strpos($fullText, '.', $_GET['page'] * 1000 + 1000) + 1);
        // print(substr($fullText, strpos($fullText, '.', $_GET['page'] * 1000), strpos($fullText, '.', $_GET['page'] * 1000)+1));
        $page_len = 1000;
        $len = $_GET["page"] * $page_len;
        // print($len);
        print($textArray[$_GET['page']]); ?>
      </div>
    </div>
    <div class="cmds">
      <a class='card switch-page' href="?page=<?php if ($_GET["page"] - 1 >= 0) {
        print($_GET["page"] - 1);
      } else {
        print("0");
      } ?>">Предыдущая страница</a>
      <div class="blank-w" style="display: table-cell;"></div>
      <a class='card switch-page' href="?page=<?php if ($_GET["page"] + 1 < sizeof($textArray) - 1) {
        print($_GET["page"] +
          1);
      } else {
        print(sizeof($textArray) - 1);
      } ?>">Следующая страница</a>
    </div>
  </div>

  <div class="blank-h"></div>
  <div class="card footer">
    <h4>based on Vladislav Kostuynin's dev</h4>
  </div>
</body>

</html>