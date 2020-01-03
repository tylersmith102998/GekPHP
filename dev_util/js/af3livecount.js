$(document).ready(function() {

    setInterval(function(){
        $.post("js/ajax/af3livecount.php", {}, function(data) {
            $("#line-count").html(data);
            //$("head title").text(data + " Lines of Code");
        });

        $.post("js/ajax/af3livecount-comments.php", {}, function(data) {
            $("#comments-count").html(data);
            //$("head title").text(data + " Lines of Code");
        });

        $.post("js/ajax/af3livecount-whitespace.php", {}, function(data) {
            $("#whitespace-count").html(data);
            //$("head title").text(data + " Lines of Code");
        });

        $.post("js/ajax/af3livecount-total.php", {}, function(data) {
            $("#total-count").html(data);
            $("head title").text(data + " Lines of Code");
        });
    }, 250);

});
