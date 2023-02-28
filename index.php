<?php
require_once 'encode.php';

session_start();

$input = filter_input(INPUT_POST, 'input');

$display = isset($_SESSION['display']) ? $_SESSION['display'] : null;
$symbol = isset($_SESSION['symbol']) ? $_SESSION['symbol'] : null;
$pre_num = isset($_SESSION['pre_num']) ? $_SESSION['pre_num'] : null;
$stand_by_flag = isset($_SESSION['stand_by_flag']) ? $_SESSION['stand_by_flag'] : 0;
$pre_symbol_flag = isset($_SESSION['pre_symbol_flag']) ? $_SESSION['pre_symbol_flag'] : 0;
$over_flag = isset($_SESSION['over_flag']) ? $_SESSION['over_flag'] : 0;


if (preg_match('/[0-9]/', $input)) {
  if (isset($display) && $pre_symbol_flag === 1) {
    $display = $input;
  } elseif (isset($display) && $over_flag === 1) {
    $display = $input;
    $symbol = null;
    $over_flag = 0;
  } elseif (isset($display)) {
    $display .= $input;
  } elseif (!isset($display) && $input == 0) {
    $display = null;
  } else {
    $display = $input;
  }
  $pre_symbol_flag = 0;
}


if (strlen($display) >= 9) {
  $array = str_split($display, 9);
  $display = $array[0];
}


switch ($input) {
  case '+':
    if ($stand_by_flag === 0 || $pre_symbol_flag === 1) {
      $symbol = '+';
      $pre_num = $display;
    } else {
      $display = $pre_num + $display;
      $pre_num = $display;
    }
    $stand_by_flag = 1;
    $pre_symbol_flag = 1;
    break;

  case '-':
    if ($stand_by_flag === 0 || $pre_symbol_flag === 1) {
      $symbol = '-';
      $pre_num = $display;
    } else {
      $display = $pre_num - $display;
      $pre_num = $display;
    }
    $stand_by_flag = 1;
    $pre_symbol_flag = 1;
    break;

  case '×':
    if ($stand_by_flag === 0 || $pre_symbol_flag === 1) {
      $symbol = '×';
      $pre_num = $display;
    } else {
      $display = $pre_num * $display;
      $pre_num = $display;
    }
    $stand_by_flag = 1;
    $pre_symbol_flag = 1;
    break;

  case '÷':
    if ($stand_by_flag === 0 || $pre_symbol_flag === 1) {
      $symbol = '÷';
      $pre_num = $display;
    } else {
      $display = $pre_num / $display;
      $pre_num = $display;
    }
    $stand_by_flag = 1;
    $pre_symbol_flag = 1;
    break;

  case '%':
    $display = $display / 100;
    break;
  
  case '+/-':
    $display = -$display;
    break;
  
  case '.':
    if (isset($display)) {
      $display .= $input;
    } else {
      $display = '0.';
    }
    break;

  case 'enter':
    if ($symbol === '+') {
      $display = $pre_num + $display;
    } elseif ($symbol === '-') {
      $display = $pre_num - $display;
    } elseif ($symbol === '×') {
      $display = $pre_num * $display;
    } elseif ($symbol === '÷') {
      $display = $pre_num / $display;
    }
    $stand_by_flag = 0;
    break;

  default:
    break;
}


$_SESSION['display'] = $display;
$_SESSION['symbol'] = $symbol;
$_SESSION['pre_num'] = $pre_num;
$_SESSION['stand_by_flag'] = $stand_by_flag;
$_SESSION['pre_symbol_flag'] = $pre_symbol_flag;
$_SESSION['over_flag'] = $over_flag;


if ($input === 'AC') {
  $_SESSION = array();
  session_destroy();
  $display = null;
  $symbol = null;
  $stand_by_flag = 0;
  $pre_symbol_flag = 0;
  $over_flag = 0;
}

?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/reset.css">
  <link rel="stylesheet" href="css/style.css">
  <title>電卓</title>
</head>
<body>
  <table>
    <tr>
      <td class="symbol_td"><p class="symbol_display">
        <?php
          if (isset($symbol)) { 
            echo h($symbol);
          } else {
            echo '　';
          } 
        ?>
        </p></td>
      <td colspan=3 class="display_td"><p class="num_display">
        <?php
          if (!isset($display)) {
            echo 0;
          } elseif (strlen($display) <= 9) {
            echo h($display);
          } elseif (strlen($display) > 9){
            echo h(sprintf("%e", $display));
            $_SESSION['over_flag'] = 1;
          } 
        ?>
      </p></td>
    </tr>
    <form action="" method="POST">
      <tr>
        <td class="calc_td"><input class="button" type="submit" name="input" value="+"></td>
        <td class="calc_td"><input class="button" type="submit" name="input" value="-"></td>
        <td class="calc_td"><input class="button" type="submit" name="input" value="×"></td>
        <td class="calc_td"><input class="button" type="submit" name="input" value="÷"></td>
      </tr>
      <tr>
        <td class="calc_td"><input class="button" type="submit" name="input" value="7"></td>
        <td class="calc_td"><input class="button" type="submit" name="input" value="8"></td>
        <td class="calc_td"><input class="button" type="submit" name="input" value="9"></td>
        <td class="calc_td"><input class="button" type="submit" name="input" value="%"></td>
      </tr>
      <tr>
        <td class="calc_td"><input class="button" type="submit" name="input" value="4"></td>
        <td class="calc_td"><input class="button" type="submit" name="input" value="5"></td>
        <td class="calc_td"><input class="button" type="submit" name="input" value="6"></td>
        <td class="calc_td"><input class="button" type="submit" name="input" value="+/-"></td>
      </tr>
      <tr>
        <td class="calc_td"><input class="button" type="submit" name="input" value="1"></td>
        <td class="calc_td"><input class="button" type="submit" name="input" value="2"></td>
        <td class="calc_td"><input class="button" type="submit" name="input" value="3"></td>
        <td class="calc_td"><input class="button" type="submit" name="input" value="AC"></td>
      </tr>
      <tr>
        <td class="calc_td"><input class="button" type="submit" name="input" value="0"></td>
        <td class="calc_td"><input class="button" type="submit" name="input" value="."></td>
        <td colspan=2 class="calc_td"><input class="enter_button" type="submit" name="input" value="enter"></td>
      </tr>
    </form>
  </table>
</body>
</html>