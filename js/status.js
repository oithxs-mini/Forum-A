$(document).ready(function () {
  var setWindowSize = function () {
    $.ajax({
      type: "POST",
      url: "../status/index.php", // 画面サイズを渡すPHPのURL
      data: "windowSize=" + $(window).width(),
    });
  };
  setWindowSize();

  // リサイズした時にsessionを更新する為
  var timer;
  $(window).resize(function () {
    if (timer) clearTimeout(timer);
    timer = setTimeout(setWindowSize, 1000);
  });
});
