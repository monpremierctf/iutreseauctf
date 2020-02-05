<?php

    function isFlagValidationAllowed() {
        $isFlagValidationAllowed = file_get_contents("isFlagValidationAllowed.cfg");
        return ($isFlagValidationAllowed==="true");
    }

?>