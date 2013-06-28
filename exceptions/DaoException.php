<?php
namespace exceptions;
class DaoException extends \Exception {

    private $model;

    public function __construct($message = null, $code = null,
            $previous = null, $model = null) {
        parent::__construct($message,$code,$previous);
        $this->model = $model;
    }

    public function getModel() {
        return $this->model;
    }

}
?>
