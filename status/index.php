<?php
session_start();
unset($_SESSION['seword']);
// envファイルの読み込み
require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// .envから
$envGrafanaUptime = $_ENV['GRAFANA_UPTIME'];
$envGrafanaAccess = $_ENV['GRAFANA_ACCESS'];
$envGrafanaWorker = $_ENV['GRAFANA_WORKER'];
$envGrafanaDuration = $_ENV['GRAFANA_DURATION'];
$envGrafanaStatus = $_ENV['GRAFANA_STATUS'];

if (isset($_POST['windowSize'])) {
  $_SESSION['windowSize'] = $_POST['windowSize'];
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ひとこと掲示板</title>
  <link rel="stylesheet" href="../css/index.css" />

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous" />

  <!-- Google Font -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Train+One&display=swap" rel="stylesheet" />
</head>

<body>
  <header class="p-3 bg-dark text-white">
    <div class="container">
      <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
        <a href="../" class="d-flex align-items-center mb-2 mb-lg-0 text-white text-decoration-none">
          <span class="title h1">HxS掲示板</span>
        </a>

        <ul class="nav col-12 col-lg-auto me-lg-auto mb-3 justify-content-center mb-md-0">
          <li>
            <a href="../" class="nav-link px-2 text-white">Home</a>
          </li>
          <li>
            <a href="../forum/" class="nav-link px-2 text-white">Forum</a>
          </li>
          <li>
            <a href="./" class="nav-link px-2 text-secondary">Status</a>
          </li>
        </ul>

        <form class="col-12 col-lg-auto mb-3 mb-lg-0 me-lg-3">
          <input type="search" class="form-control form-control-dark" placeholder="Search..." aria-label="Search" />
        </form>
      </div>
    </div>
  </header>

  <div class="mt-3"><iframe src="<?php echo $envGrafanaStatus ?>" width="<?php echo $_SESSION['windowSize'] ?>" height="100" frameborder="0"></iframe></div>
  <hr>
  <div><iframe src="<?php echo $envGrafanaUptime ?>" width="<?php echo $_SESSION['windowSize'] ?>" height="200" frameborder="0"></iframe></div>
  <hr>
  <div><iframe src="<?php echo $envGrafanaAccess ?>" width="<?php echo $_SESSION['windowSize'] ?>" height="450" frameborder="0"></iframe></div>
  <hr>
  <div><iframe src="<?php echo $envGrafanaWorker ?>" width="<?php echo $_SESSION['windowSize'] ?>" height="450" frameborder="0"></iframe></div>
  <hr>
  <div class="mb-3"><iframe src="<?php echo $envGrafanaDuration ?>" width="<?php echo $_SESSION['windowSize'] ?>" height="450" frameborder="0"></iframe></div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
  <!-- JSの読み込み -->
  <script src="../js/index.js"></script>
  <script src="../js/status.js"></script>
</body>

</html>