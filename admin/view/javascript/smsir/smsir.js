(function($){
	$.smsir = (function(){
		// Private variables
		var settings = {
			to: '',
			Token: '',
			type: '',
			message: '',
		}
		
		var eventHandlers = {
			complete: [],
			error: []
		}
				
		var fireEvent = function(evt, evtData) {
			if (eventHandlers[evt]) {
				for(x=0; x < eventHandlers[evt].length; x++) {
					eventHandlers[evt][x].apply(document, [evtData]);
				}
			}
			
			if (settings[evt] && typeof settings[evt] == 'function') {
				settings[evt].apply(document, [evtData]);
			}
		}
		
		// Send logic
		var send_bulk = function() {
			//for( x=0; x < settings.to.length; x++ ) {
				send_single(settings.token, settings.to, settings.message, settings.type);
			//}
		}
		
		var send_single = function(Token, toNumber, msg, type) {
			var xhr = $.ajax({
				url: 'index.php?route=extension/module/smsir/sendRequest&token=' + Token,
				data: {
					Messages: msg,
					MobileNumbers: toNumber,
					type: type,
					CanContinueInCaseOfError: 'false',
				},
				type: 'POST',
				dataType: 'json',
				success: function(resp, status, xhr) {//alert(resp.toSource());
					resp['to'] = xhr.MobileNumbers;
					if (resp.status != 'error') {
						fireEvent('success', resp);
					} else {
						fireEvent('error', resp);
					}
				},
				//error: function(xhr, resp) {
				error: function(data) {
					alert(JSON.stringify(data));
					fireEvent('error', resp);
				}
			});
			xhr.MobileNumbers = toNumber;
		}
		
		var send = function(options) {
			settings.success = settings.error = null;
			if (options) {
				settings = $.extend(settings, options);
			}
			
			if (settings.to) {
				if (typeof settings.to == 'object') send_bulk();
				else send_single(settings.apikey, settings.to, settings.message);
			}
		}
		
		// Public methods
		function smsIrObj( options ) {
			if (options) {
				send(options);
			}
		}
		
		smsIrObj.send = send;
				
		smsIrObj.addEventHandler = function(evt, callback) {
			if (eventHandlers[evt] && typeof eventHandlers[evt] == 'object') {
				eventHandlers[evt].push(callback);
				return true;
			}
			return false;
		}
		
		smsIrObj.getLastMessage = function() {
			return settings;
		}
		
		return smsIrObj;
	}())
}(jQuery))