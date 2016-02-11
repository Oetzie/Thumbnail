<?php

	return array(
		array(
	        'name' 		=> 'cache',
	        'desc' 		=> 'thumbnail_snippet_cache_desc',
	        'type' 		=> 'combo-boolean',
	        'options' 	=> '',
	        'value'		=> true,
	        'area'		=> PKG_NAME_LOWER,
	        'lexicon' 	=> PKG_NAME_LOWER.':default'
	    ),
	    array(
	        'name' 		=> 'position',
	        'desc' 		=> 'thumbnail_snippet_method_desc',
	        'type' 		=> 'textfield',
	        'options' 	=> '',
	        'value'		=> 'cover',
	        'area'		=> PKG_NAME_LOWER,
	        'lexicon' 	=> PKG_NAME_LOWER.':default'
	    ),
	    array(
	        'name' 		=> 'quality',
	        'desc' 		=> 'thumbnail_snippet_quality_desc',
	        'type' 		=> 'numberfield',
	        'options' 	=> '',
	        'value'		=> '75',
	        'area'		=> PKG_NAME_LOWER,
	        'lexicon' 	=> PKG_NAME_LOWER.':default'
	    )
	);

?>