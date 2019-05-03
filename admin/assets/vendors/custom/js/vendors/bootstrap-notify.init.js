//== Set defaults

$.notifyDefaults({
	template: '' +
	'<div data-notify="container" class="alert alert-{0} m-alert" role="alert">' +
	'<button type="button" aria-hidden="true" class="close" data-notify="dismiss"></button>' +
	'<span data-notify="icon"></span>' +
	'<span data-notify="title">{1}</span>' +
	'<span data-notify="message">{2}</span>' +
	'<div class="progress" data-notify="progressbar">' +
	'<div class="progress-bar progress-bar-animated bg-{0}" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div>' +
	'</div>' +
	'<a href="{3}" target="{4}" data-notify="url"></a>' +
	'</div>'
});