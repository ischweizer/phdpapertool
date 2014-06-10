<?php

// extend felixkiss/uniquewith-validator's Validator for unique_with filter
class CustomValidator extends Felixkiss\UniqueWithValidator\ValidatorExtension {
	/**
	 * The original date_format validator does not support ',' signs in the format string
	 */
	protected function validateDateFormat2($attribute, $value, $parameters) {
		$this->requireParameterCount(1, $parameters, 'date_format2');

		$parsed = date_parse_from_format(implode(',', $parameters), $value);

		return $parsed['error_count'] === 0 && $parsed['warning_count'] === 0;
    }

	protected function replaceDateFormat2($message, $attribute, $rule, $parameters) {
		return str_replace(':format', implode(',', $parameters), $message);
	}

	protected function validateBeforeOrEqual($attribute, $value, $parameters) {
		$this->requireParameterCount(1, $parameters, 'before_or_equal');

		if (!($date = strtotime($parameters[0]))) {
			return strtotime($value) <= strtotime($this->getValue($parameters[0]));
		} else {
			return strtotime($value) <= $date;
		}
	}

	protected function replaceBefore($message, $attribute, $rule, $parameters) {
		if (!(strtotime($parameters[0]))) {
			return str_replace(':date', $this->getAttribute($parameters[0]), $message);
		} else {
			return str_replace(':date', $parameters[0], $message);
		}
	}
}

Validator::resolver(function($translator, $data, $rules, $messages)
{
	return new CustomValidator($translator, $data, $rules, $messages);
});
