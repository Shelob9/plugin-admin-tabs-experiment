jQuery( document ).ready(function() {
    (function($) {
        'use strict'

        var AppRouter = $.Router.extend({
            routes: {
                "tab/:tab": "loadTab"
            }
        });

        //settings model
        var Settings = Backbone.Model.extend({
            defaults: {
                postType: 'post',
                postID: 1
            },
            initialize: function(){

            }
        });

        //settings view
        var settingsView = Backbone.View.extend({
            initialize: function(){
                this.render();
            },

            render: function(){

                //get HTML for template
                var tmpl = jQuery( '#tmpl-settings' ).html();
                // Compile the template using underscore
                var model = new Settings();
                var data = model.toJSON();
                var template = _.template( tmpl, data );
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

        //info view
        var infoView = Backbone.View.extend({
            initialize: function(){
                this.render();
            },
            render: function(){

                //get HTML for template
                var tmpl = jQuery( '#tmpl-info' ).html();
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
            var view;
            if( 'info' == id ){
                view = new infoView({ el: jQuery("#tab_container") });
            }else{
                view = new settingsView({ el: jQuery("#tab_container") });
            }

        });




        // Start Backbone history
        $.history.start();


    }(Backbone));
});




