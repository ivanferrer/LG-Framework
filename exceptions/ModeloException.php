<?php
namespace exceptions;
class ModeloException extends \Exception {

    public function __construct($message = null, $code = null, $previous = null) {
        parent::__construct($message,$code,$previous);

    }
}
?>
