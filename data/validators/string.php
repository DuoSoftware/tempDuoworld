<?php
	
	class stringValidator{
		public function Validate($field, $value,$validator){
			
			if (isset($validator->maxLen))
			if (strlen($value) > $validator->maxLen)
				return "$field exceeds the maximum string length, expected : " . $validator->maxLen;

			if (isset($validator->minLen))
			if (strlen($value) < $validator->minLen)
				return "$field exceeds the minimum string length, expected : " . $validator->minLen;

			return NULL;
		}
	}
?>