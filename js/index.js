$(".messagearea").on("input", function () {
  var input1 = $(textarea).val(); //#textarea に入力された文字を取得
  var input2 = $(username).val(); //#username に入力された文字を取得

  if (input1 && input2) {
    //もし文字が入っていれば

    $("#sendbtn").prop("disabled", false); //disabled を無効にする＝ボタンが押せる
  } else {
    $("#sendbtn").prop("disabled", true); //disabled を有効にする＝ボタンが押せない
  }
});
