(function($) {
    'use strict'

    var AppRouter = $.Router.extend({
        routes: {
            "tab/:id": "loadTab"

        }
    });

    // Instantiate the router
    var app_router = new AppRouter;

    //callback when "loadTab" route happens
    app_router.on('route:loadTab', function (id) {
        alert( "tab is: " + id );
    });

    // Start Backbone history
    $.history.start();

}(Backbone));

