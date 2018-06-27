<?php
namespace Vanderbilt\AutomaticRetryExternalModule;

class AutomaticRetryExternalModule extends \ExternalModules\AbstractExternalModule
{
	function redcap_survey_page()
	{
		?>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.2.2/jquery.form.min.js" integrity="sha384-FzT3vTVGXqf7wRfy8k4BiyzvbNfeYjK+frTVqZeNDFl8woCbF0CYG6g2fMEFFo/i"
				crossorigin="anonymous"></script>

		<style>
			#automatic-retry-dialog {
				position: absolute;
				top: 0px;
				left: 0px;
				width: 100vw;
				height: 100vh;
				margin: auto;
				background: #00000087;
				z-index: 1;
				text-align: center;
				display: none;
			}

			#automatic-retry-message {
				margin: auto;
				margin-top: 50px;
				background: #f1f1f1;
				padding: 10px;
				box-shadow: 0px 1px 9px 0px #464646;
				border-radius: 10px;
				display: none;
			}
		</style>

		<div id="automatic-retry-dialog">
			<div id="automatic-retry-message"></div>
		</div>

		<script>
			$(function () {
				$('button[name=submit-btn-saverecord]')[0].onclick = function () {
					var loadingDialog = $('#automatic-retry-dialog')
					var loadingMessage = $('#automatic-retry-message')

					loadingDialog.show()

					var TRY_COUNT = 10

					var submit = function (triesLeft) {
						triesLeft--

						$('form').ajaxSubmit({
							success: function (data) {
								$('body').html(data)
							},
							error: function () {
								console.log('submit failed')
								loadingMessage.html('A network issue was detected.  Retrying ' + triesLeft + ' more time(s)...')
								loadingMessage.css('display', 'inline-block')

								if (triesLeft > 0) {
									var delay = 5
									console.log('retrying in ' + delay + ' seconds')
									setTimeout(function () {
										submit(triesLeft)
									}, delay * 1000)
								}
								else {
									console.log('giving up')
									loadingMessage.hide()

									setTimeout(function () {
										alert('The request failed after being retried ' + TRY_COUNT + ' times.  Please check your internet connection and try submitting again.')
										loadingDialog.hide()
									}, 0)
								}
							}
						})
					}

					submit(TRY_COUNT)

					return false
				}
			})
		</script>
		<?php
	}
}