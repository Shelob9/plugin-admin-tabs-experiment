jQuery( document ).ready(function() {
    (function($) {
        'use strict'

        var AppRouter = $.Router.extend({
            routes: {
                "tab/:tab": "loadTab",
                '*path':  'defaultRoute'
            }
        });

        //Switch nav active markup
        var mark_active_tab = function( tab ){
            jQuery( '.nav-tab' ).removeClass( 'nav-tab-active' );
            var active = '#' + tab + '-tab';
            jQuery( active ).addClass( 'nav-tab-active' );

        };

        var sync = $.sync;
        $.sync = function(method, model, options) {
            options.cache = false;
            options.beforeSend = function (xhr) {
                xhr.setRequestHeader('X-WP-Nonce', JP_BB_VARS.nonce);
            };


            sync(method, model, options);
        };



        //settings model
        var Settings = $.Model.extend({
            defaults: {
                postType: 'page',
                postID: 5
            },
            url: JP_BB_VARS.root,
            initialize: function(){
            }
        });

        //settings view
        var settingsView = $.View.extend({
            initialize: function(){
                //set model in view
                this.model = new Settings();

                //fetch model from API and render templatw
                var view = this;
                this.model.fetch({
                    success: function() {
                        view.render();
                    }
                });

            },

            render: function() {

                //get HTML for template
                var tmpl = jQuery( '#tmpl-settings' ).html();

                //prepare data for template
                var data = {
                    postType: this.model.get( 'postType' ),
                    postID: parseInt(this.model.get( 'postID' ), 10 )
                };

                // Compile the template using underscore
                var template = _.template( tmpl , data );
                jQuery( '#tab_container' ).html( template );

            },
            events: {
                "click input[type=submit]": "save",
                "change input[name='post-type']": "updatePostType",
                "change input[name='post-id']": "updatePostID"
            },
            updatePostType : function( e ){
                this.model.set( { postType: jQuery( "input[name='post-type']" ).val() });

            },
            updatePostID : function ( e ) {
                this.model.set( { postID: jQuery( "input[name='post-id']" ).val() });
            },
            save: function(e){
                e.preventDefault();
                this.model.save( {
                    postType: this.model.get( 'postType' ),
                    postID: parseInt( this.model.get( 'postID' ), 10 )
                });
            }
        });

        //info view
        var infoView = $.View.extend({
            initialize: function(){
                this.render();
            },
            render: function(){

                //get HTML for template
                var tmpl = jQuery( '#tmpl-info' ).html();
                // Compile the template using underscore
                var template = _.template( tmpl , {} );
                // Load the compiled HTML into the $ "el"
                this.$el.html( template );
            }
        });



        // Instantiate the router
        var app_router = new AppRouter;

        //callback when "loadTab" route happens
        app_router.on('route:loadTab', function (tab ) {
            var view;
            if( 'info' == tab ){
                view = new infoView({ el: jQuery("#tab_container") });
            }else{
                view = new settingsView({ el: jQuery("#tab_container") });
            }

            mark_active_tab( tab );

        });

        //handle default route
        app_router.on('route:defaultRoute', function () {
            mark_active_tab( 'settings' );
            var view = new settingsView({ el: jQuery("#tab_container") });
        });





        // Start $ history
        $.history.start();


    }(Backbone));
});




