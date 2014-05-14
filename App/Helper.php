<?php

namespace Spacewalk;

class Helper {

	static public function format_encode ( $data, $format="json") {

		if ( ! in_array($format, array("json", "json-pretty")))
			$format = "json";

		switch ($format) {
			case 'json-pretty':
				return 
					'<pre>' . 
					json_encode( $data, 
						JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . 
					'</pre>';
				break;

			default:
				return json_encode( $data );
				break;
			
		}

	}
	
}