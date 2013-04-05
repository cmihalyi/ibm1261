Content that appears in the calculator application is kept in the defaults.json file.

The object "assumptions" (line 253 in the defaults.json file) contains the values for the hidden assumptions used in calculations.

If looking to change a default value for the calculator do a search for the field "defaultvalue". 
Above the defaultvalue field there should also be a field for "label" this should indicate which value you are changing as it relates to the calculator.
There are other fields which use the "defaultvalue" value so be sure to check the label field to make sure you are changing the correct value.

If needing to change the IBM email address that requests are forwarded to look for the field "cc_email" (line 455 in defaults.json file).