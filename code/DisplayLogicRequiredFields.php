<?php

class DisplayLogicRequiredFields extends RequiredFields
{
	/**
	 * Allows validation of fields via specification of a php function for
	 * validation which is executed after the form is submitted.
	 *
	 * @param array $data
	 *
	 * @return boolean
	 */
	public function php($data) {
		$valid = true;
		$fields = $this->form->Fields();

		foreach($fields as $field) {
            if ($field->isVisible())
    			$valid = ($field->validate($this) && $valid);
		}

		if($this->required) {
			foreach($this->required as $fieldName) {
				if(!$fieldName) {
					continue;
				}

				if($fieldName instanceof FormField) {
					$formField = $fieldName;
					$fieldName = $fieldName->getName();
				}
				else {
					$formField = $fields->dataFieldByName($fieldName);
				}

				$error = true;

				// submitted data for file upload fields come back as an array
				$value = isset($data[$fieldName]) ? $data[$fieldName] : null;

				if(is_array($value)) {
					if($formField instanceof FileField && isset($value['error']) && $value['error']) {
						$error = true;
					} else {
						$error = (count($value)) ? false : true;
					}
				} else {
					// assume a string or integer
					$error = (strlen($value)) ? false : true;
				}

				if($formField && $error) {
					$errorMessage = _t(
						'Form.FIELDISREQUIRED',
						'{name} is required',
						array(
							'name' => strip_tags(
								'"' . ($formField->Title() ? $formField->Title() : $fieldName) . '"'
							)
						)
					);

					if($msg = $formField->getCustomValidationMessage()) {
						$errorMessage = $msg;
					}

					$this->validationError(
						$fieldName,
						$errorMessage,
						"required"
					);

					$valid = false;
				}
			}
		}

		return $valid;
	}
}

class DisplayLogicEvaluator {
    protected $form;

    public function __construct($form) {

    }



    public function evaluateEqualTo($val) {
        return $this->getFieldValue() === $val;
    }

    evaluateNotEqualTo: function(val) {
        return this.getFieldValue() !== val;
    },

    evaluateLessThan: function(val) {
        var num = parseFloat(val);

        return this.getFieldValue() < num;
    },

    evaluateGreaterThan: function(val) {
        var num = parseFloat(val);

        return parseFloat(this.getFieldValue()) > num;
    },

    evaluateLessThan: function(val) {
        num = parseFloat(val);
        return parseFloat(this.getFieldValue()) < num;
    },

    evaluateContains: function(val) {
        return this.getFieldValue().match(val) !== null;
    },

    evaluateStartsWith: function(val) {
        return this.getFieldValue().match(new RegExp('^'+val)) !== null;
    },

    evaluateEndsWith: function(val) {
        return this.getFieldValue().match(new RegExp(val+'$')) !== null;
    },

    evaluateEmpty: function() {
        return $.trim(this.getFieldValue()).length === 0;
    },

    evaluateNotEmpty: function() {
        return !this.evaluateEmpty();
    },

    evaluateBetween: function(minmax) {
        v = parseFloat(this.getFieldValue());
        parts = minmax.split("-");
        if(parts.length === 2) {
            return v > parseFloat(parts[0]) && v < parseFloat(parts[1]);
        }
        return false;
    },

    evaluateChecked: function() {
        return this.getFormField().is(":checked");
    },

    evaluateNotChecked: function() {
        return !this.getFormField().is(":checked");
    },
}
