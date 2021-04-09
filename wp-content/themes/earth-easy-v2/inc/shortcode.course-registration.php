<?php
// Course Registration Form
function course_registration_form_shortcode($atts) {
	extract(shortcode_atts(array(
		'title' => 'Course Registration Form'
	), $atts));
	return '
		<form>
			<p>
				<label for="CompanyName">Company Name</label><br />
				<input type="text" id="CompanyName" name="CompanyName" />
			</p>
			<p>
				<label for="AttendeeName">Attendee Name</label><br />
				<input type="text" id="AttendeeName" name="AttendeeName" />
			</p>
		</form>
	';
}
add_shortcode('course_registration_form', 'course_registration_form_shortcode');
?>