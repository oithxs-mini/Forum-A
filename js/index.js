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

$(".liarea").on("input", function () {
  var input1 = $(liuser).val(); //#liuser に入力された文字を取得
  var input2 = $(lipass).val(); //#lipass に入力された文字を取得

  if (input1 && input2) {
    //もし文字が入っていれば

    $("#loginbtn").prop("disabled", false); //disabled を無効にする＝ボタンが押せる
  } else {
    $("#loginbtn").prop("disabled", true); //disabled を有効にする＝ボタンが押せない
  }
});

$(".suarea").on("input", function () {
  var input1 = $(suuser).val(); //#suuser に入力された文字を取得
  var input2 = $(supass).val(); //#supass に入力された文字を取得
  var input3 = $(supass2).val(); //#supass2 に入力された文字を取得

  if (input1 && input2 && input3 && input2 == input3) {
    //もし文字が入っていれば

    $("#signupbtn").prop("disabled", false); //disabled を無効にする＝ボタンが押せる
  } else {
    $("#signupbtn").prop("disabled", true); //disabled を有効にする＝ボタンが押せない
  }
});