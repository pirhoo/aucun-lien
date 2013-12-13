$(window).load(function() {

    var $content = $("#embed .tweet .content"),
           $meta = $("#embed .tweet .meta"),
           $logo = $("#embed .tweet .logo"),
         $window = $(window);
    
    var resize = function(val) {

        $content.css("font-size", val);
        
        var margin =  20,
        height = $content.outerHeight() + $meta.outerHeight() + $logo.outerHeight() + margin;

        if( val > 0 && height >= $window.height() ) resize(val-10)
    };

    var scanShare = function() { 
        // Ajoute des évènements pour que les liens ouvrent des popups
        $("a.fb").share("facebook");
        $("a.tw").share("twitter");
        $("a.pn").share("pinterest");
        $("a.gp").share("google");
    };


    var init = function() {
        resize( $window.height() * 0.50);
    }

    scanShare();
    setTimeout(init, 100);
    $window.resize(init);

});