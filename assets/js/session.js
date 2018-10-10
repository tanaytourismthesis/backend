$(function(){
  // SESSION TIMEOUT 11/28/2016
	var IDLE_TIMEOUT = 900; // 60; //seconds
	var WARNING_TIMEOUT = IDLE_TIMEOUT - 300; // 60; //seconds
	var _idleSecondsTimer = null;
	var _idleSecondsCounter = 0;
	// var _movementCounter = 0;

	$(document).ready(function(e){

		$("input").on("focus",function(){
			_idleSecondsCounter = 0;
			call_dummy();
		});

		$("body").on("click",function(evt){
			_idleSecondsCounter = 0;
			call_dummy();
		});

		_idleSecondsTimer = window.setInterval(CheckIdleTime, 1000);

  });

	function CheckIdleTime() {
		_idleSecondsCounter++;

		if (_idleSecondsCounter == WARNING_TIMEOUT) {
			showSessionModal();
		}

		if (_idleSecondsCounter >= IDLE_TIMEOUT) {
			window.clearInterval(_idleSecondsTimer);
      showSessionModal(true);
		}

	}

	function call_dummy(){
		$.ajax({
			url: baseurl + 'session/session_check',
			type:'POST',
			data: null,
			context: document.body
		});
	}

	function showSessionModal(close = false) {
		$('#modalSession')
			.on('hidden.bs.modal', function(){
				if ($('.modal.fade.in').length) {
					$('body').addClass('modal-open');
				}
			})
			.modal('show');

		$('#modalSession #btnOK').on('click', function(){
			if (close) {
				alert('You have been idle for more than 15 minutes now. You will be logged out of the system.');
				window.location = baseurl + 'logout';
			} else {
				$('#modalSession').modal('hide');
			}
		});
	}
	// END SESSION TIMEOUT 11/28/2016
});
