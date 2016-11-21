<?php
	
	class numberValidator{
		public function Validate($field, $value,$validator){
			if (isset($validator->max))
			if ($value > $validator->max)
				return "$field is greater than the maximum value for number, expected less than " . $validator->max;

			if (isset($validator->min))
			if ($value < $validator->min)
				return "$field is less than the minimum value for number, expected less than " . $validator->min;

			return NULL;
		}
	}
?>