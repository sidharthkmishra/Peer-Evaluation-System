<?php

include_once('student_model.php');

class ConfirmationCode
{
    const CONFIRMATION_CODE_SIZE = 10;

    private $studentModel;

    function __construct()
    {
        $this->studentModel = new StudentModel();
    }

    public function generateCode()
    {
        return substr(base_convert(hash('sha256', uniqid(mt_rand())), 16, 36), 0, self::CONFIRMATION_CODE_SIZE);
    }

    public function validateCode($confirmationCode, $emailAddress)
    {
        $confirmationCodeHashAtDB = $this->studentModel->getConfirmationCode($emailAddress);

        if($confirmationCodeHashAtDB === false) {
            echo "Invalid Email Address/Confirmation code";
            return;
        }

        if(!password_verify($confirmationCode, $confirmationCodeHashAtDB)) {
            echo "Invalid Email Address/Confirmation code";
            return;
        }

        $confirmationCodeHashAtDB = $this->studentModel->getActiveConfirmationCode($emailAddress);
        if($confirmationCodeHashAtDB === false) {
            echo "The confirmation code is expired. Please get another code";
            return;
        }

        echo "Validated your details. Soon the evaluation system will be ready for next step. Try Later";
    }
}