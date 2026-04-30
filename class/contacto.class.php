<?php

class class_contact {
    public $name;
    public $email;
    public $subject;
    public $message;

    public function __construct(
        $name = null,
        $email = null,
        $subject = null,
        $message = null
    )
    {
        $this->name = $name;
        $this->email = $email;
        $this->subject = $subject;
        $this->message = $message;
      
    }
}