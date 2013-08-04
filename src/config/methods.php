<?php return array(

	// Methods aliases
	//
	// Here you can configure a list of methods to alias and the arguments
	// you want them to take
	//////////////////////////////////////////////////////////////////////

	'photosetsGetList'   => array(
		'user_id', 'page', 'per_page'
	),

	'photosetsGetPhotos' => array(
		'photoset_id', 'extras', 'privacy_filter', 'per_page', 'page', 'media'
	),

	'peopleGetPhotos'    => array(
		'user_id', 'safe_search', 'min_upload_date', 'max_upload_date', 'min_taken_date', 'max_taken_date', 'content_type', 'privacy_filter', 'extras', 'per_page', 'page'
	),

	'collectionsGetTree' => array(
		'collection_id', 'user_id'
	),

);
