    function js_func() {
        console.log(1);
        $.ajax({
            url: "http://localhost/HxS/Forum-A/forum/goodbtn.php",
            success: function(result) {
                console.log(1);
                //$("div").text(result);
                $("div").show();
            }
        })
    }

