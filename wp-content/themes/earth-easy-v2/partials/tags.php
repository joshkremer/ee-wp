<?php
// Tags
$tags_args = array(
	'hide_empty' => 0
);
$tags = get_terms('post_tag', $tags_args);
$context['tags'] = $tags;