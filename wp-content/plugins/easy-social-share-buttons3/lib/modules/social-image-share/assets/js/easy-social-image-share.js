(function($) {
	"use strict";
	
	if (!String.format) {
		String.format = function(format) {
			var args = Array.prototype.slice.call(arguments, 1);
			return format.replace(/{(\d+)}/g, function(match, number) {
				return typeof args[number] != 'undefined'
						? args[number]
						: match
						;
			});
		};
	}

	var jQueryExtensions = {
		transferDataAttributes: function( $source, $destination ) {
			var attributes = $source.data();
			for(var key in attributes){
				if (attributes.hasOwnProperty(key))
					$destination.attr('data-' + key, attributes[key]);
			}
		}
	};

	function BaseElement() {
		this.$element = null;
	}

	BaseElement.prototype.addAttr = function (attrName, attrValue) {
		this.$element.attr(attrName, attrValue);
	};

	BaseElement.prototype.addClass = function( className ) {
		this.$element.addClass(className);
	};

	window.essbis = (function () {

		/* Internal classes */

		/* Icon class - represents a single icon */
		function Icon(width, height, type){
			BaseElement.call(this);
			this.width = width;
			this.height = height;
			this.$element = $('<a/>');
			this.addAttr(essbis.attr.type, type);
			this.addAttr('href', '#');
			this.$iconElement = $('<div/>').addClass( essbis.cssClass.iconPrefix + type);
			this.$iconElement.html( $('<div/>').addClass( essbis.cssClass.prefix + 'inner') );
		}

		Icon.prototype = new BaseElement();

		Icon.prototype.createElement = function() {
			return this.$element.html( this.$iconElement );
		};

		/* Container class - holds icons */
		function BaseContainer(type) {
			this.icons = [];
			this.width = 0;
			this.height = 0;
			this.$element = $('<div/>');

			switch (type)	{
				case 'vertical':
					this.updateContainerSize = this.updateSizeVertical;
					break;
				case 'horizontal':
					this.updateContainerSize = this.updateSizeHorizontal;
					break;
			}

			this.addClass( essbis.cssClass.container );
		}

		BaseContainer.prototype = new BaseElement();

		BaseContainer.prototype.updateSizeHorizontal = function (width, height) {
			this.width += width;
			this.height = height > this.height ? height : this.height;
		};

		BaseContainer.prototype.updateSizeVertical = function (width, height) {
			this.height += height;
			this.width = width > this.width ? width : this.width;
		};

		BaseContainer.prototype.createContainer = function() {
			var $elements = [];

			for(var i = 0; i < this.icons.length; i++)
				$elements.push(this.icons[i].createElement());

			return this.$element
					.css('min-height', this.height + 'px')
					.css('min-width', this.width + 'px')
					.html( $elements );
		};

		BaseContainer.prototype.addIcon = function( icon ) {
			this.icons.push( icon );
			this.updateContainerSize(icon.width, icon.height);
			return this;
		};


		function ClickHandlerArg() {
			this.url = '';
			this.imageUrl = '';
			this.description = '';
		}

		var _functions = {
			openWindow: function(url, name, width, height) {
				var topOffset = Math.round(screen.height/2 - height/ 2),
						leftOffset = Math.round(screen.width/2 - width/2);
				window.open(url, name,
						String.format('width={0},height={1},status=0,toolbar=0,menubar=0,location=1,scrollbars=1,top={2},left={3}', width, height, topOffset, leftOffset ));

			}
		}

		var essbis = {};

		/* Container for all attributes */
		essbis.attr = {};
		essbis.attr.prefix = 'data-essbis';
		essbis.attr.type  = essbis.attr.prefix + 'Type';
		essbis.attr.postTitle  = essbis.attr.prefix + 'PostTitle';
		essbis.attr.postUrl  = essbis.attr.prefix + 'PostUrl';

		/* Containers for CSS classes */
		essbis.cssClass = {};
		essbis.cssClass.prefix = 'essbis-';
		essbis.cssClass.container = essbis.cssClass.prefix + 'container';
		essbis.cssClass.iconPrefix = essbis.cssClass.prefix + 'icon-';

		essbis.buttonTypes = {
			pinterestShare: 'pinterest',
			twitterShare: 'twitter',
			facebookShare: 'facebook'
		}

		essbis.clickHandlers = {};
		essbis.clickHandlers['pinterest'] = function( clickHandlerArg ) {

			if (clickHandlerArg.description == '') {
				clickHandlerArg.description = clickHandlerArg.postTitle;
			}
			
			var url = String.format('http://pinterest.com/pin/create/bookmarklet/?is_video=false&url={0}&media={1}&description={2}',
					encodeURIComponent( clickHandlerArg.url ),
					encodeURIComponent( clickHandlerArg.imageUrl ),
					encodeURIComponent( clickHandlerArg.description )
			);
			_functions.openWindow(url, 'Pinterest', 632, 453);
			return false;
		}

		essbis.clickHandlers['twitter'] = function( clickHandlerArg ) {
			/* https://dev.twitter.com/docs/intents */
			var via = '';
			if (essbis.buttonSettings.settings.twitterHandle)
				via = String.format('&via={0}', essbis.buttonSettings.settings.twitterHandle);
			var url = String.format('https://twitter.com/intent/tweet?text={0}&url={1}{2}',
					encodeURIComponent( clickHandlerArg.postTitle ),
					encodeURIComponent( clickHandlerArg.url ),
					via
			);
			_functions.openWindow(url, 'Twitter', 550, 470);
			return false;
		}

		essbis.clickHandlers['facebook'] = function( clickHandlerArg ){
			var url = '';
						
			if (essbis.buttonSettings.settings.facebookAppID) {
				url = String.format('https://www.facebook.com/dialog/feed?app_id={0}&display=popup&name={1}&link={2}&picture={3}&description={4}',
						essbis.buttonSettings.settings.facebookAppID,
						encodeURIComponent( clickHandlerArg.postTitle ),
						encodeURIComponent( clickHandlerArg.url ),
						encodeURIComponent( clickHandlerArg.imageUrl ),
						encodeURIComponent( clickHandlerArg.description )
				);
			}
			else {
				url = String.format('https://www.facebook.com/sharer/sharer.php?u={0}&display=popup',
						encodeURIComponent( clickHandlerArg.url )
				);
			}

			_functions.openWindow(url, 'Facebook', 550, 420);
			return false;
		}
		
		essbis.clickHandlers['google'] = function( clickHandlerArg ){
			var url = String.format('https://plus.google.com/share?url={0}',
					encodeURIComponent( clickHandlerArg.url )
			);

			_functions.openWindow(url, 'Google', 550, 420);
			return false;
		}

		essbis.clickHandlers['linkedin'] = function( clickHandlerArg ){
			var url = String.format('http://www.linkedin.com/shareArticle?mini=true&url={0}',
					encodeURIComponent( clickHandlerArg.url )
			);

			_functions.openWindow(url, 'LinkedIn', 550, 420);
			return false;
		}
		
		essbis.clickHandlers['vkontakte'] = function( clickHandlerArg ) {

			var url = String.format('http://vk.com/share.php?noparse=true&url={0}&image={1}&description={2}&title={3}',
					encodeURIComponent( clickHandlerArg.url ),
					encodeURIComponent( clickHandlerArg.imageUrl ),
					encodeURIComponent( clickHandlerArg.description ),
					encodeURIComponent( clickHandlerArg.postTitle)
			);
			_functions.openWindow(url, 'VKontakte', 632, 453);
			return false;
		}
		
		essbis.clickHandlers['odnoklassniki'] = function( clickHandlerArg ) {

			var url = String.format('http://www.odnoklassniki.ru/dk?st.cmd=addShare&st.s=1&st._surl={0}&st.comments={2}',
					encodeURIComponent( clickHandlerArg.url ),
					encodeURIComponent( clickHandlerArg.postTitle)
			);
			_functions.openWindow(url, 'Odnoklassniki', 632, 453);
			return false;
		}
		
		essbis.clickHandlers['tumblr'] = function( clickHandlerArg ) {

			var url = String.format('http://www.tumblr.com/share/photo?click_thru={0}&source={1}&caption={2}',
					encodeURIComponent( clickHandlerArg.url ),
					encodeURIComponent( clickHandlerArg.imageUrl ),
					encodeURIComponent( clickHandlerArg.description )
			);
			_functions.openWindow(url, 'Tumblr', 632, 453);
			return false;
		}

		essbis.clickHandlers['reddit'] = function( clickHandlerArg ) {

			var url = String.format('http://reddit.com/submit?url={0}&title={1}&text={2}',
					encodeURIComponent( clickHandlerArg.url ),
					encodeURIComponent( clickHandlerArg.pageTitle ),
					encodeURIComponent( clickHandlerArg.description )
			);
			_functions.openWindow(url, 'reddit', 632, 453);
			return false;
		}

		essbis.clickHandlers['digg'] = function( clickHandlerArg ) {

			var url = String.format('http://digg.com/submit?phase=2&url={0}&title={1}&bodytext={2}',
					encodeURIComponent( clickHandlerArg.url ),
					encodeURIComponent( clickHandlerArg.pageTitle ),
					encodeURIComponent( clickHandlerArg.description )
			);
			_functions.openWindow(url, 'digg', 632, 453);
			return false;
		}

		essbis.clickHandlers['delicious'] = function( clickHandlerArg ) {

			var url = String.format('http://delicious.com/post?url={0}&title={1}&bodytext={2}',
					encodeURIComponent( clickHandlerArg.url ),
					encodeURIComponent( clickHandlerArg.pageTitle )
			);
			_functions.openWindow(url, 'delicious', 632, 453);
			return false;
		}
		
		essbis.handleStats = function(network) {
			if (typeof(essb_settings) != "undefined") {
				if (essb_settings.essb3_stats) {
					var post_id = essb_settings["post_id"] || '';
					
					var instance_mobile = false;
					var instance_template = "onmedia";
					var instance_postion = "onmedia"
					
					if( (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i).test(navigator.userAgent) ) {
						instance_mobile = true;
					}
					var instance_counters = false;
					var instance_button = "icon";
					
					if (typeof(essb_settings) != "undefined") {
						jQuery.post(essb_settings.ajax_url, {
							'action': 'essb_stat_log',
							'post_id': post_id,
							'service': network,
							'template': instance_template,
							'mobile': instance_mobile,
							'position': instance_postion,
							'button': instance_button,
							'counter': instance_counters,
							'nonce': essb_settings.essb3_nonce
						}, function (data) { if (data) {
							
						}}, 'json');
					}
				}
			}
		}

		essbis.debug = function(args) {
			/*if ( 0 || essbis.main.settings.debug ) {
				console.log('ESSBIS.log');
				console.log(args);
			}*/
		};

		essbis.init = function() {
			//remove console.log errors
			var console = (window.console = window.console || {});
			if (!console['log']) console['log'] = function() {};
			//check if in debug mode
		  essbis.main.settings.debug = window.location.search.indexOf('essbisdebug') != -1;
		}

		/* Container for themes */
		essbis.themes = {};

		essbis.getTheme = function( themeName ){
			if (essbis.themes.hasOwnProperty( themeName ) )
				return essbis.themes[ themeName ];
			return essbis.themes[ 'flat' ];
		};

		essbis.addTheme = function( themeName, theme) {
			essbis.themes[ themeName ] = theme;
		}

		var activeUserNetworks = essbis_settings.modules.buttons.networks.split(",");
		
		essbis.debug(essbis_settings.modules.buttons.networks);
		
		var default48Theme = { buttons: {} };
		/*default48Theme.buttons[essbis.buttonTypes.pinterestShare] = { height: 48, width: 48 };
		default48Theme.buttons[essbis.buttonTypes.twitterShare] = { height: 48, width: 48 };
		default48Theme.buttons[essbis.buttonTypes.facebookShare] = { height: 48, width: 48 };*/
		for (var i=0; i < activeUserNetworks.length; i++) {
			var socialNetwork = activeUserNetworks[i];
			default48Theme.buttons[socialNetwork] = { height: 48, width: 48 };
		}
		essbis.addTheme( 'flat', default48Theme );

		var default36Theme = { buttons: {} };
		for (var i=0; i < activeUserNetworks.length; i++) {
			var socialNetwork = activeUserNetworks[i];
			default36Theme.buttons[socialNetwork] = { height: 36, width: 36 };
		}
		essbis.addTheme( 'flat-small', default36Theme );
		
		var default36RoundTheme = { buttons: {} };
		for (var i=0; i < activeUserNetworks.length; i++) {
			var socialNetwork = activeUserNetworks[i];
			default36RoundTheme.buttons[socialNetwork] = { height: 36, width: 36 };
		}
		essbis.addTheme( 'round', default36RoundTheme );
		
		var default24Theme = { buttons: {} };
		for (var i=0; i < activeUserNetworks.length; i++) {
			var socialNetwork = activeUserNetworks[i];
			default24Theme.buttons[socialNetwork] = { height: 24, width: 24 };
		}
		essbis.addTheme( 'tiny', default24Theme );

		/* Container for button sets */
		essbis.buttonSets = {};
		essbis.getButtonSet = function( buttonSetName ) {
			if (essbis.buttonSets.hasOwnProperty( buttonSetName ) )
				return essbis.buttonSets[ buttonSetName ];
			return essbis.buttonSets[ 'default' ];
		}

		essbis.addButtonSet = function( name, buttonSet ) {
			essbis.buttonSets[ name ] = buttonSet;
		}

		//essbis.addButtonSet( 'default', [ essbis.buttonTypes.pinterestShare, essbis.buttonTypes.facebookShare, essbis.buttonTypes.twitterShare]);
		essbis.addButtonSet( 'default', activeUserNetworks);

		/* Module names */
		essbis.moduleNames = {
			main: 'settings',
			hover: 'hover',
			buttonSettings: 'buttons'
		};

		/* Container for modules */
		essbis.module = {};

		/* ======================================================================================== */
		/* MAIN MODULE */
		/*==========================================================================================*/

		essbis.main = essbis.module[essbis.moduleNames.main] = (function (){
			var _settings = {
				debug: 1
			};

			var module = {};
			module.settings = _settings;
			module.setSettings = function( settings) {
				_settings = $.extend( _settings, settings );
			};

			return module;
		})();

		/* ======================================================================================== */
		/* END MAIN MODULE */
		/*==========================================================================================*/

		/* ======================================================================================== */
		/* BUTTON SETTINGS MODULE */
		/*==========================================================================================*/

		essbis.buttonSettings = essbis.module[essbis.moduleNames.buttonSettings] = (function (){
			var _settings = {
				'pinterestImageDescription': ['titleAttribute', 'altAttribute'],
				'twitterHandle': '',
				'customURL' : '',
				'customText': '',
				'customDescription': ''
			};

			var module = {};
			module.settings = _settings;
			module.setSettings = function( settings) {
				_settings = $.extend( _settings, settings );
			};
			
			// include description hover
			module.settings['pinterestImageDescription'] = essbis_settings['pinterestImageDescription'];
			module.settings['twitterHandle'] = essbis_settings['twitteruser'] || '';
			module.settings['facebookAppID'] = essbis_settings['fbapp'] || '';
			module.settings['dontShowOn'] = essbis_settings['dontshow'] || '';

			module.settings['customURL'] = essbis_settings['custom_url'] || '';
			module.settings['customText'] = essbis_settings['custom_text'] || '';
			module.settings['customDescription'] = essbis_settings['custom_description'] || '';
			return module;
		})();

		/* ======================================================================================== */
		/* END BUTTON SETTINGS MODULE */
		/*==========================================================================================*/

		/* ======================================================================================== */
		/* HOVER MODULE */
		/*==========================================================================================*/
		essbis.module[essbis.moduleNames.hover] = (function (){

			/* Private vars */
			var imageIndex = 0;
			var attr = {
				ignore: essbis.attr.prefix + 'Ignore',
				imageDescription: essbis.attr.prefix + 'ImageDescription',
				index : essbis.attr.prefix + 'Index',
				postContainer: essbis.attr.prefix + 'HoverContainer',
				timeoutId: essbis.attr.prefix + 'TimeoutId',
				timeoutId2: essbis.attr.prefix + 'TimeoutId2'
			};
			var classes = {
				visible: 'visible',
				overlay: essbis.cssClass.prefix + 'hover-overlay',
				container: essbis.cssClass.prefix + 'hover-container'
			};
			var _settings = {
				theme: 'flat',
				buttonSet: 'default',
				orientation: 'horizontal',
				hoverPanelPosition: 'middle-middle',
				imageSelector: String.format('.[0] img', classes.container),
				minImageHeight: 100,
				minImageWidth: 100,
				descriptionSource: ['titleAttribute', 'altAttribute'],
				disabledClasses: '.wp-smiley' + (essbis.buttonSettings.settings['dontShowOn'] != '' ? ','+essbis.buttonSettings.settings['dontShowOn']: ''),
				showOnLightbox: '1',
				enabledClasses: '*',
				parentContainerSelector: '',
				parentContainerLevel: 2
			}
			
			/* Private classes */
			/* IconContainer - holds icons */
			function HoverContainer(type, index) {
				BaseContainer.call(this, type);
				this.addClass(classes.overlay);
				this.addClass('essbis-orientation-' + type);
				this.addAttr(module.attr.index, index);
			}

			HoverContainer.prototype = new BaseContainer();

			/* Private functions */
			function getNextImageIndex(){	return ++imageIndex;}

			function validateImage( $image ) {
				
				var isMobileView = false;
				if( (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i).test(navigator.userAgent) ) {
					isMobileView = true;
				}
				
				if (isMobileView) {
					if (_settings.minImageWidthMobile && Number(_settings.minImageWidthMobile))
						_settings.minImageWidth = _settings.minImageWidthMobile;
					if (_settings.minImageHeightMobile && Number(_settings.minImageHeightMobile))
						_settings.minImageWidth = _settings.minImageHeightMobile;
				}
				
				return $image[0].clientHeight >= _settings.minImageHeight
						&& $image[0].clientWidth >= _settings.minImageWidth
						&& $image.not( _settings.disabledClasses).length > 0
						&& $image.filter( _settings.enabledClasses).length > 0;
			}
			
			function validateImageMobile( $image ) {
				
				return $image[0].clientHeight >= _settings.minImageHeight
						&& $image[0].clientWidth >= _settings.minImageWidth
						&& $image.not( _settings.disabledClasses).length > 0
						&& $image.filter( _settings.enabledClasses).length > 0;
			}

			function getDescriptionValue( $image, settingName ) {
				switch( settingName ) {
					case 'titleAttribute':
						return $image.attr('title');
					case 'altAttribute':
						return $image.attr('alt');
					default:
						return '';
				}
			}

			function getDescription( $image ) {
				var result = '',
						descriptionSource = essbis.buttonSettings.settings.pinterestImageDescription;

				for(var i = 0; i < descriptionSource.length && !result; i++) {
					result = getDescriptionValue( $image, descriptionSource[i] );
				}
				return result;
			}

			function getAttrFromContainerOrDefault( $image, attrName, defaultVal ) {
				var $parent = $image.parents( String.format('[{0}]', attr.postContainer));
				return $parent.attr( attrName ) || defaultVal;
			}

			function getUrl( $image ){
				return getAttrFromContainerOrDefault( $image, essbis.attr.postUrl, document.URL );
			}

			function getPostTitle( $image ) {
				return getAttrFromContainerOrDefault( $image, essbis.attr.postTitle, document.title );
			}

			function getContainerOffset(	imageSize, containerSize) {
				var top = 0,
						left = 0;
				var getVerticalMiddle = function() { return Math.round(imageSize.height/2 - containerSize.height/2);},
						getVerticalBottom = function() { return Math.round(imageSize.height - containerSize.height);},
						getHorizontalMiddle = function() { return Math.round(imageSize.width/2 - containerSize.width/2)},
						getHorizontalRight = function() { return Math.round(imageSize.width - containerSize.width)};

				switch ( _settings.hoverPanelPosition ){
					case 'top-left':
						top = 0;
						left = 0;
						break;
					case 'top-middle':
						top = 0;
						left =  getHorizontalMiddle();
						break;
					case 'top-right':
						top = 0;
						left = getHorizontalRight();
						break;
					case 'middle-left':
						top = getVerticalMiddle();
						left = 0;
						break;
					case 'middle-middle':
						top = getVerticalMiddle();
						left = getHorizontalMiddle();
						break;
					case 'middle-right':
						top = getVerticalMiddle();
						left = getHorizontalRight();
						break;
					case 'bottom-left':
						top = getVerticalBottom();
						left = 0;
						break;
					case 'bottom-middle':
						top = getVerticalBottom();
						left = getHorizontalMiddle();
						break;
					case 'bottom-right':
						top = getVerticalBottom();
						left = getHorizontalRight();
						break;
				}
				return { top: top, left: left };
			}

			function getContainerSelector(index) {
				if (index !== undefined)
					return String.format('div.{0}[{1}="{2}"]', classes.overlay, attr.index, index);
				else
					return String.format('div.{0}', classes.overlay);
			}

			function getImageSelector(){
				var selectors = [];

				if (_settings['showOnLightbox'] == '1')
					selectors.push( 'img.cboxPhoto' );

				if (_settings.imageSelector != '')
					selectors.push( String.format('{0}:not([{1}])', _settings.imageSelector, attr.ignore) );

				return selectors.join(',');
			}
			
			function getMobileImageSelector() {
				var selectors = '';
				if (_settings.imageSelector != '')
					selectors = ( String.format('{0}:not([{1}])', _settings.imageSelector, attr.ignore) );
				
				return selectors;
			}

			function getImageSource( $imageElement ) {
				var tagName = $imageElement.prop('tagName')
				switch (tagName.toLowerCase()){
					case 'div':
						return $imageElement.css('background-image').replace(/^url\(["']?/, '').replace(/["']?\)$/, '');
					case 'img':
						return $imageElement.attr('src');
					default:
						return '';
				}
			}

			function getIconContainer(index) {
				var themeName = _settings.theme;
				
				var isMobileView = false;
				if( (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i).test(navigator.userAgent) ) {
					isMobileView = true;
				}
				
				if (isMobileView && _settings.theme_mobile)
					themeName = _settings.theme_mobile;
				
				var buttonSet = essbis.getButtonSet( _settings.buttonSet );
				var currentTheme = essbis.getTheme( themeName );
				
				//essbis.debug(themeName);
				//essbis.debug(buttonSet);
				//essbis.debug(currentTheme);
				essbis.debug(_settings.orientation);
				
				var container = new HoverContainer(_settings.orientation, index);
				container.addClass( themeName );

				for(var i = 0; i < buttonSet.length; i++) {
					container.addIcon(new Icon(currentTheme.buttons[ buttonSet[i] ].width, currentTheme.buttons[ buttonSet[i] ].height, buttonSet[i]) );
				}
				return container;
			}

			function onHover() {
				var $image = $(this);

				if ( !$image.attr(attr.index) && validateImage( $image ) ) {
					$image.attr(attr.index, getNextImageIndex());
				}
				var index = $image.attr(attr.index);
				

				if (!index) {
					$image.attr( attr.ignore, '' );
					return;
				}

				var $container = $( getContainerSelector(index) );

				if ( $container.length == 0 ) {
					//no container - we have to create it
					var container = getIconContainer( index );
					var $containerElement = container.createContainer();

					var containerOffset = getContainerOffset(
							{ height: $image[0].clientHeight, width: $image[0].clientWidth },
							{ height: container.height, width: container.width }
					);
					var imageOffset = $image.offset();
					var finalOffset = {
						top: imageOffset.top + containerOffset.top,
						left: imageOffset.left + containerOffset.left
					};

					$image.after($containerElement);
					$containerElement.offset(finalOffset).addClass( classes.visible );
				} else {
					//container exists, we need to cancel its hiding
					cancelHide( $container );
				}
			}
			
			function onResizeMobile(sender) {
				var $image = sender;				
				
				if ( !$image.attr(attr.index) && validateImageMobile( $image ) ) {
					$image.attr(attr.index, getNextImageIndex());
				}
				var index = $image.attr(attr.index);
				
				if (!index) {
					$image.attr( attr.ignore, '' );
					return;
				}
				
				var $container = $( getContainerSelector(index) );

				if ( $container.length == 0 ) {
					
				} else {
					//container exists, we need to cancel its hiding
					cancelHide( $container );
					var container = getIconContainer( index );
					var containerOffset = getContainerOffset(
							{ height: $image[0].clientHeight, width: $image[0].clientWidth },
							{ height: container.height, width: container.width });
							
					var imageOffset = $image.offset();
					var finalOffset = {
							top: imageOffset.top + containerOffset.top,
							left: imageOffset.left + containerOffset.left
					};

							//$image.after($containerElement);
					$container.offset(finalOffset).addClass( classes.visible );
				}

			}

			function onHoverMobile(sender) {
				var $image = sender;

				
				if ( !$image.attr(attr.index) && validateImageMobile( $image ) ) {
					$image.attr(attr.index, getNextImageIndex());
				}
				var index = $image.attr(attr.index);
				
				if (!index) {
					$image.attr( attr.ignore, '' );
					return;
				}
				
				var $container = $( getContainerSelector(index) );

				if ( $container.length == 0 ) {
					//no container - we have to create it
					var container = getIconContainer( index );
					var $containerElement = container.createContainer();

					var containerOffset = getContainerOffset(
							{ height: $image[0].clientHeight, width: $image[0].clientWidth },
							{ height: container.height, width: container.width }
					);
					var imageOffset = $image.offset();
					var finalOffset = {
						top: imageOffset.top + containerOffset.top,
						left: imageOffset.left + containerOffset.left
					};

					$image.after($containerElement);
					$containerElement.offset(finalOffset).addClass( classes.visible );
				} else {
					//container exists, we need to cancel its hiding
					cancelHide( $container );
				}
			}

			/* Hides the icon container */
			function asyncHide( $container ) {
				var timeoutId = setTimeout(function(){
					$container.removeClass( classes.visible );
					$container.attr(attr.timeoutId2, setTimeout(function() { $container.remove();	}, 600));
				}, 100 );
				$container.attr(attr.timeoutId, timeoutId);
			}

			/* Cancel hiding the overlay */
			function cancelHide( $container ) {
				clearTimeout( $container.attr(attr.timeoutId2) );
				clearTimeout( $container.attr(attr.timeoutId) );
				$container.addClass( classes.visible );
			}

			/* Handle clicking on a link */
			function onClick( ) {
				var $link = $(this);

				var type = $link.attr(essbis.attr.type);
				if ( essbis.clickHandlers[ type ] === undefined)
					return false;

				var index = $link.parent( String.format("div.{0}", essbis.cssClass.container)).attr(attr.index);
				var $image = $( String.format('[{0}="{1}"]', attr.index, index));
				

				var clickHandlerArg = new ClickHandlerArg();
				clickHandlerArg.url = getUrl( $image );
				clickHandlerArg.imageUrl = getImageSource( $image );
				clickHandlerArg.description = getDescription( $image );
				clickHandlerArg.postTitle = getPostTitle( $image );
				
				// since 5.0 support for custom share details added inside plugin settings for custom share URL
				
				if (essbis.buttonSettings.settings.customURL)
					clickHandlerArg.url = essbis.buttonSettings.settings.customURL;
				if (essbis.buttonSettings.settings.customText)
					clickHandlerArg.postTitle = essbis.buttonSettings.settings.customText;
				if (essbis.buttonSettings.settings.customDescription)
					clickHandlerArg.description = essbis.buttonSettings.settings.customDescription;
				
				essbis.clickHandlers[ type ].call($link, clickHandlerArg);
				essbis.handleStats(type);
				return false;
			}

			function createPostContainer() {
				var $this = $(this);
				//empty jQuery element
				var $parent = $();
				//find the post container
				if( _settings.parentContainerSelector )
					$parent = $this.parents( _settings.parentContainerSelector).first();
				if ( $parent.length == 0 ) {
					$parent = $this;
					for(var i = 0; i < _settings.parentContainerLevel; i++)
						$parent = $parent.parent();
				}
				//transfer data attributes
				if ($parent.length > 0){
					jQueryExtensions.transferDataAttributes($this, $parent);
					$parent.addClass(classes.container);
				}
			}

			/* Public stuff */
			var module = {};

			module.attr = attr;

			module.onReady = function() {
				$( String.format('input[{0}]', attr.postContainer )). each( createPostContainer );

				var isMobileView = false;
				if( (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i).test(navigator.userAgent) ) {
					isMobileView = true;
				}
				
				var alwaysVisible = _settings.alwaysVisible || false;
				var mobileOnClick = _settings.mobileOnClick || false;
				
				if (isMobileView && mobileOnClick) {
					alwaysVisible = false;
					isMobileView = false;
				}
				
				
				if (!isMobileView && !alwaysVisible) {
					$( document ).delegate( getImageSelector(), 'mouseenter', onHover);
	
					$( document ).delegate( getImageSelector(), 'mouseleave', function() {
						var index = $(this).attr(attr.index);
						var $container = $( getContainerSelector(index) );
						asyncHide( $container );
					});
				}

				$( document).delegate( String.format("div.{0} a", classes.overlay), 'click', onClick);

				$( document ).delegate( getContainerSelector(), 'mouseenter', function() {
					cancelHide( $( this ) );
				});


				if (!isMobileView && !alwaysVisible) {
					$( document ).delegate( getContainerSelector(), 'mouseleave', function() {
						asyncHide( $( this ) );
					});
				}
				
				if (isMobileView || alwaysVisible) {
					$(document).find(getMobileImageSelector()).each(function() {
						onHoverMobile($(this));
					});
				}
			};

			module.onResize = function() {
				var isMobileView = false;
				if( (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i).test(navigator.userAgent) ) {
					isMobileView = true;
				}
				var alwaysVisible = _settings.alwaysVisible || false;

				
				if (!isMobileView && !alwaysVisible) {
					$( String.format('[{0}]', module.attr.ignore)).each( function() {	$(this).removeAttr( module.attr.ignore );	});
					$( String.format('[{0}]', module.attr.index)).each( function() {	$(this).removeAttr( module.attr.index );	});
				}
				
				if (isMobileView || alwaysVisible) {
					$(document).find(getMobileImageSelector()).each(function() {
						onResizeMobile($(this));
					});
				}
			}

			module.setSettings = function( settings) {
				_settings = $.extend( _settings, settings );
			};

			return module;
		})();

		/* ======================================================================================== */
		/* END HOVER MODULE */
		/*==========================================================================================*/


		essbis.setSettings = function( settings ) {
			essbis.debug(settings);

			/* Add all button sets */
			for(var buttonSetName in settings.buttonSets) {
				if ( settings.buttonSets.hasOwnProperty( buttonSetName ) )
					essbis.addButtonSet( buttonSetName, settings.buttonSets[ buttonSetName ] );
			}

			/* Add all themes */
			for(var themeName in settings.themes) {
				if ( settings.themes.hasOwnProperty( themeName ) )
					essbis.addTheme( settings.themes[ themeName ] );
			}

			/* Distribute module settings across modules */
			for(var moduleName in settings.modules){
				if ( settings.modules.hasOwnProperty( moduleName ) && essbis.module.hasOwnProperty( moduleName ) )
					essbis.module[ moduleName ].setSettings( settings.modules[ moduleName ] );
			}
		}

		essbis.triggerActiveModules = function( functionName ) {
			for(var moduleName in essbis.module) {
				if (essbis.module.hasOwnProperty(moduleName)){
					var moduleNameWithUpperCase = moduleName.charAt(0).toUpperCase() + moduleName.slice(1);
					var settingName = 'module' + moduleNameWithUpperCase + 'Active';
					if (essbis.module.hasOwnProperty( moduleName )
							&& typeof essbis.module[moduleName][functionName] === 'function'
							&& essbis.main.settings[ settingName ] == '1')	{
							essbis.module[moduleName][functionName]();							
					}
				}
			}
		};

		essbis.onReady = function() {
			essbis.debug('onReady');
			essbis.triggerActiveModules('onReady');
		}

		essbis.onResize = function() {
			essbis.debug('onResize');
			essbis.triggerActiveModules('onResize');
		}

		return essbis;
	}());


	$(function() {

		essbis.init();
		essbis.setSettings( essbis_settings );
		$( document ).ready(function() {
			setTimeout(function() {
				essbis.onReady();
			}, 1)
		});

		$(window).resize( essbis.onResize );
	});

})(jQuery);