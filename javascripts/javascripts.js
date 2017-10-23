function show_activities() {
	var activities = document.getElementById('activities');

	var type = '';

	if (document.form_activities.checkbox1.checked == true)
		type = type + '&action=true';
	if (typeof (document.form_activities.checkbox2) != 'undefined' && document.form_activities.checkbox2.checked == true)
		type = type + '&email=true';
	if (typeof (document.form_activities.checkbox3) != 'undefined' && document.form_activities.checkbox3.checked == true)
		type = type + '&call=true';

	if (!activities) return false;

	activities.contentWindow.location.href = activityURL + type;

	return true;
}

function submit_filter_form(item, nomonthmsg) {
	var year = document.getElementById('year').value;

	if (year == 0 && item == 'month' && nomonthmsg != '') {
		alert(nomonthmsg);
	}

	var filters = document.getElementById('select-field-form');
	filters.submit();
}

function change_folder(pathUrl) {
	pathUrl = pathUrl + '&ajax';

	jQuery('#document-list').html('<img src="pics/ajax.gif" style="position:absolute;left:48%;top:48%;">');

	jQuery.ajax({
		url: pathUrl,
		type: "GET"
	})
		.done(function (data) {
			jQuery('#document-list').html(data);
			jQuery('.doc-link').on('click', function () {
				return change_folder(jQuery(this).attr('href'));
			});
		})
		.fail(function () {
			jQuery('#document-list').html('Error during AJAX request.');
		});

	return false;
}

function resize_wishlist_frame() {
	var height = jQuery(window).height() - 178;
	jQuery('.wishlist-container #datas').css('height', height + 'px');
}

jQuery.noConflict();
jQuery(document).ready(function () {
	jQuery('.doc-link').click(function () {
		return change_folder(jQuery(this).attr('href'));
	});

	resize_wishlist_frame();

	jQuery(window).resize(function () {
		resize_wishlist_frame();
	});
});