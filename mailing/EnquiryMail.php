<?php

    class EnquiryMail{
        function __construct($fullname, $email, $telephone, $message)
        {
            $this->fulname = $fullname;
            $this->email = $email;
            $this->phone = $telephone;
            $this->content = $message;
        }

        function Subject(){
            $subjectContent = "ADEFEMICONSULT - ENQUIRY";
            return $subjectContent;
        }

        function htmlBody(){
            $htmlContent = '
                 <div style="border: 2px solid black; padding: 20px">
                    <p>The following enquiry was issued to Adefemi Consult</p><br/>
                    <ul style="list-style: none; margin: 10px 0; padding: 0">
                        <li style="width: 150px;  float: left">Full Name:</li>
                        <li style="font-weight: bold;">'.$this->fulname.'</li>
                    </ul>
                    <ul style="list-style: none; margin: 10px 0; padding: 0">
                        <li style="width: 150px;  float: left">Email Address:</li>
                        <li style="font-weight: bold">'.$this->email.'</li>
                    </ul>
                    <ul style="list-style: none; margin: 10px 0; padding: 0">
                        <li style="width: 150px;  float: left">Telephone:</li>
                        <li style="font-weight: bold">'.$this->phone.'</li>
                    </ul>
                    <ul style="list-style: none; margin: 10px 0; padding: 0">
                        <li style="width: 150px;  float: left">Message:</li>
                        <li style="font-weight: bold">'. $this->content.'</li>
                    </ul>
                </div>
                ';
            return $htmlContent;
        }
    }

?>