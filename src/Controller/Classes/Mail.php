<?php

namespace App\Controller\Classes;

use Mailjet\Client;
use Mailjet\Resources;

class Mail
{
    private $api_key = 'e210312f6fc56c5cfbec15a7ed2df2a7';
    private $api_key_secret = '65594412db14a1f648af7d3d24e1d391';

    public function send($to_email,$to_name,$subject,$content)
    {   
        $mj = new Client($this->api_key,$this->api_key_secret,true,['version' => 'v3.1']);
        
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "justin.dissaux@laposte.net",
                        'Name' => "La Boutique Française"
                    ],
                    'To' => [
                        [
                            'Email' => $to_email,
                            'Name' => $to_name
                        ]
                    ],
                    'TemplateID' => 4501150,
                    'TemplateLanguage' => true,
                    'Subject' => $subject,
                    'Variables' => [
                        'content' => $content,                        
                    ]
                ]
            ]
        ];
$response = $mj->post(Resources::$Email, ['body' => $body]);
$response->success();
    }
}