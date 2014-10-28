/**
 * Detail
 */
var Detail = {};

Detail._spinner = ' <i class="' + Admin.spinnerClass() + '"></i>';

/**
 * functions to execute when document is ready
 *
 * only for NodesController
 *
 * @return void
 */
Detail.documentReady = function() {
	Detail.addDetail();
	Detail.removeDetail();
}

/**
 * add detail field
 *
 * @return void
 */
Detail.addDetail = function() {
	$('a.add-detail').click(function(e) {
		var aAddDetail = $(this);
		var spinnerClass = Admin.iconClass('spinner', false);
		aAddDetail.after(Detail._spinner);
		$.get(aAddDetail.attr('href'), function(data) {
			aAddDetail.parent().find('.clear:first').before(data);
			$('div.detail a.remove-detail').unbind();
			Detail.removeDetail();
			aAddDetail.siblings('i.' + spinnerClass).remove();
		});
		e.preventDefault();
	});
}

/**
 * remove detail field
 *
 * @return void
 */
Detail.removeDetail = function() {
	$('div.detail a.remove-detail').click(function(e) {
		var aRemoveDetail = $(this);
		var spinnerClass = Admin.iconClass('spinner', false);
		if (aRemoveDetail.attr('rel') != '') {
			if (!confirm('Remove this detail field?')) {
				return false;
			}
			aRemoveDetail.after(Detail._spinner);
			$.getJSON(aRemoveDetail.attr('href') + '.json', function(data) {
				if (data.success) {
					aRemoveDetail.parents('.detail').remove();
				} else {
					// error
				}
				aRemoveDetail.siblings('i.' + spinnerClass).remove();
			});
		} else {
			aRemoveDetail.parents('.detail').remove();
		}
		e.preventDefault();
		return false;
	});
}

/**
 * document ready
 *
 * @return void
 */
$(document).ready(function() {
	Detail.documentReady();
});
