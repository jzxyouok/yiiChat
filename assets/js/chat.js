/**
 * jQuery Chat plugin file.
 *
 * @author Palenov Maxim <MAX_ELEK@mail.ru>
 * @copyright Copyright &copy; 2013
 * @version $Id: jquery.chat.js 2013-03-01 15:17:01 $
 */
 
(function($)
{
    /**
     * Settings of the widget
     */
	var chatSettings = [];
	
    /**
     * Function update the contnetnt of the widget
     */
	function updateChat() {
		var settings = chatSettings,
			$chat = $(this);
			
            $.ajax({
				type: "GET",
				url: settings.updateUrl,
				data: {'lastID': settings.lastMessageID},
				dataType: 'json',
				success: function(data) {
					$.each(data, function(i, mes) {
						$chat.chat('addMessage', mes.message, mes.username, new Date(mes.date * 1000 /* ms */), true);
						// Get last id
						var id = parseInt(mes.id);
						if (id >= settings.lastMessageID)
							settings.lastMessageID = id;
					});
				},
                error: function (XHR, textStatus, errorThrown) {
						var err;
                        
						if (XHR.readyState === 0 || XHR.status === 0) {
							return;
						}
                        
						switch (textStatus) {
						case 'timeout':
							err = 'The request timed out!';
							break;
						case 'parsererror':
							err = 'Parser error!';
							break;
						case 'error':
							if (XHR.status && !/^\s*$/.test(XHR.status)) {
								err = 'Error ' + XHR.status;
							} else {
								err = 'Error';
							}
							if (XHR.responseText && !/^\s*$/.test(XHR.responseText)) {
								err = err + ': ' + XHR.responseText;
							}
							break;
						}
                        
						alert(err);
					}
			});
	}
	
	methods = {
		/**
		 * Chat set function.
         * @param options map settings for the chat. Available options are as follows:
         * - updateInterval:    Time after which the updated chat messages
         * - updateUrl:         The url, that response messages
         * - sendMessageUrl:    The url for the savin messages
         * - username:          Name of the user
		 * - openCloseTagID:	id of the open / close div
         * - contentID:       	id of the div container of the chat widget
         * - sendButtonID:      id of the send button
         * - textFieldID:       id of the text fueld
		 * - bOpen				Chat is open?
         * @return object the jQuery object
		 */
        init: function (options) {
            var settings = $.extend({
					username: 		'guest',
					openCloseTagID: 'chat_button',
					contentID: 		'chat_content',
					sendButtonID: 	'chat_send_button',
					textFieldID: 	'chat_text',
					bOpen:			false,
                    updateInterval: 5000
					// lastMessageID last message id in the message window, this is corresponding to the id the database
                    // assetsUrl
				}, options || {}),
                
                $chat = $(this);
					
            // Save settings
            chatSettings = settings;

            // Show the chat
            $("html").css('overflow-x', 'hidden');
            $chat.chat('open');

            // Registering event handlers
            $("#" + settings.openCloseTagID).click(function() {
                $chat.chat('open');
            });

            addMsgFunc = function() {
                if ($("#" + settings.textFieldID).val()) {
					message = $("#" + settings.textFieldID).val();
                    $chat.chat('addMessage', message, null, null, false, true);
					// Clear message
					$("#" + settings.textFieldID).val('');
                }
            };

            $("#" + settings.sendButtonID).click(addMsgFunc);
            $("#" + settings.textFieldID).keypress(function(e) {
                if( e.which == 13 /* Key Enter */ )
                    addMsgFunc();
            });

            // Update message list
            chatSettings.lastMessageID = 0;
            updateChat();
            setInterval(updateChat, settings.updateInterval);
            
			return this;
        },
        
        /**
         *  Send message to the server for saving.
         *  @param message string the sending message
         *  @param date object the date object
         */
        sendMessageToServer: function (message, date) {
			var settings = chatSettings;
			
			$.ajax({
				type: "POST",
				url: settings.sendMessageUrl,
				async: false,
				data: {
					'username': settings.username,
					'message': message,
					'date': Math.floor(date.getTime() / 1000) /* Unix time in seconds */
				}
			}).done(function(lastMesID) {
				settings.lastMessageID = lastMesID;
			});
        },
        
        /**
         *  Add message to the message box
         *  @param message string the adding message. Message string must be html encoded. 
		 *	When it's clear message takes from textFuel with id = settings.textFieldID and encode it.
         *  @param username string name of owner of message. When it's clear username from
         *   chatSettings will be used
         *  @param date object the date object
         *  @param noSendToServer boolean Determines whether to save the message on the server.
		 *	@param encodeMessage boolean Specifies whether message must be encoded. 
		 *	 Always sent to the server are not encoded message.
         *   When it's clear or false the message saved in the server.
         */
		addMessage: function (message, username, date, noSendToServer, encodeMessage) {
			var settings = chatSettings,
				$chat = $(this);

			if (!message) return;
            
			var date = date || new Date();
			if (!noSendToServer)
				$chat.chat('sendMessageToServer', message, date);
				
			if (encodeMessage)
				message = $('<span>').text( message ).html();

			// Create a message for the window
			var sDate = '<span class="date">' +
					date.getDate() + '/ ' +
					(1 + date.getMonth()) + '/' +
					date.getFullYear() + ' ' +               
					date.getHours() + ':' +
					date.getMinutes() + ':' +
					date.getSeconds() +
			'</span> ';
			
			var sUsername = username || settings.username;
			sUsername = '<span class="username">' + sUsername + '</span>:';
			
			message = '<span class="message">' + message + '</span><br/>';
		
			$("#" + settings.contentID).append(sDate + sUsername + message);
			
			// Auto scroll
			$("#" + settings.contentID).scrollTop(9999);
        },
        
        /**
         * Open or close the chat window
         */
        open: function () {
            var settings = chatSettings;
            
			if (chatSettings.bOpen) {
				this.animate({'right': '0px'});
                $("#" + settings.openCloseTagID).css('background-image', 'url(' + settings.assetsUrl + '/img/buttonClose.png)');
			}
			else {
			
				if (this.css('right') == 'auto')
					this.css('right', '-' + this.outerWidth() + 'px');
				else
					this.animate({'right': '-' + this.outerWidth() + 'px'});
                
                $("#" + settings.openCloseTagID).css('background-image', 'url(' + settings.assetsUrl + '/img/buttonOpen.png)');
			}
            
			chatSettings.bOpen = !chatSettings.bOpen;
        }
	};
	
	$.fn.chat = function (method) {
		if (methods[method]) {
			return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
		} else if (typeof method === 'object' || !method) {
			return methods.init.apply(this, arguments);
		} else {
			$.error('Method ' + method + ' does not exist on jQuery.chat');
			return false;
		}
	};

})(jQuery);