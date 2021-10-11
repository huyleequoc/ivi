jQuery(window).load(function(){

    if( typeof YoastSEO != undefined ){

        (function(){ // Isolate script in function to avoid conflicts
            ZombifyYoastPlugin = function() {
                YoastSEO.app.registerPlugin( 'zombifyYoastPlugin', {status: 'ready'} );

                /**
                 * @param modification 	{string} 	The name of the filter
                 * @param callable 		{function} 	The callable
                 * @param pluginName 	{string} 	The plugin that is registering the modification.
                 * @param priority 		{number} 	(optional) Used to specify the order in which the callables
                 * 						associated with a particular filter are called. Lower numbers
                 * 						correspond with earlier execution.
                 */
                YoastSEO.app.registerModification( 'content', this.modifyContent, 'zombifyYoastPlugin', 5 );
            }

            ZombifyYoastPlugin.prototype.modifyContent = function( content ) {
                return content + ' ' + zf_yoast_plugin.content;
            };

            /** if Yoast SEO is already loaded, instantiate the plugin */
            if ( typeof YoastSEO !== 'undefined' && typeof YoastSEO.app !== 'undefined' ) {
                new ZombifyYoastPlugin();
            }
            /** Yoast SEO will trigger a ready event when initialized. Load the plugin right after. */
            else {
                jQuery( window ).on(
                    'YoastSEO:ready',
                    function() {
                        new ZombifyYoastPlugin();
                    }
                );
            }
        }());

    }

});