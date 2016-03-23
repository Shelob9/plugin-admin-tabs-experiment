jQuery( document ).ready(function() {
    (function($) {
        'use strict'

        var AppRouter = $.Router.extend({
            routes: {
                "tab/:id": "loadTab"
            }
        });

        var tab1View = Backbone.View.extend({
            initialize: function(){
                this.render();
            },
            render: function(){

                //get HTML for template
                var tmpl = jQuery( '#tmpl-tab-1' ).html();
                // Compile the template using underscore
                var template = _.template( tmpl, {} );
                // Load the compiled HTML into the Backbone "el"
                this.$el.html( template );
            },
            events: {
                "click input[type=submit]": "save"
            },
            save: function(e){
                e.preventDefault();
            }
        });

        var tab2View = Backbone.View.extend({
            initialize: function(){
                this.render();
            },
            render: function(){

                //get HTML for template
                var tmpl = jQuery( '#tmpl-tab-2' ).html();
                // Compile the template using underscore
                var template = _.template( tmpl , {} );
                // Load the compiled HTML into the Backbone "el"
                this.$el.html( template );
            }
        });



        // Instantiate the router
        var app_router = new AppRouter;

        //callback when "loadTab" route happens
        app_router.on('route:loadTab', function (id) {
            if( 2 == id ){
                var tab_2_view = new tab2View({ el: jQuery("#tab_container") });
            }else{
                var tab_1_view = new tab1View({ el: jQuery("#tab_container") });
            }

        });




        // Start Backbone history
        $.history.start();


    }(Backbone));
});




