<?php

return [

    'default' => 'labsmobile',

    'sms' => [
        'default' => 'labsmobile',
        'certified' => 'labsmobile'
    ],
    'email' => [
        'default' => 'mail', // Obtiene los parametros del sistema
        'certified' => 'mailcertificado'
    ],

    'brokers' => [
        'labsmobile' => [
            'user' => 'MadRedGas',  // mgallardo@aduxia.com @duxi@2018
            'pwd' => 'mRg2019@',
            'sender' => 'Clavel',
            'host' => 'https://api.labsmobile.com',
            'env' => 'production', // local => No envia, production => Envía
            'email' => 'buzonoficinavirtual@madrilena.es', // Certified SMS. We need an email account
        ],
        'linkmobility' => [
            'sender' => 'Clavel',
            'host' => "",
            'account' => '',
            'user' => '',
            'pwd' => '',
            'env' => 'production', // local => No envia, test => entorno de test, production => Envía
        ],
        'linkmobility-certified' => [
            'sender' => 'Clavel',
            'host' => "",
            'user' => '',
            'pwd' => '',
            'account' => '',
            'env' => 'production', // local => No envia, test => entorno de test, production => Envía
            'email' => '', // Certified SMS. We need an email account
        ],
        'linkmobility-didimo' => [
            'sender' => 'Clavel',
            'host' => "",
            'user' => '',
            'pwd' => '',
            'env' => 'production', // local => No envia, test => entorno de test, production => Envía
        ],
        'mailcertificado' => [
            'host' => "",
            'wsdl' => '',
            'username' => '',
            '' => '',
            'env' => 'produccion', // calidad => Entorno test , produccion => Entorno de produccion
            'bcc' => "",
        ],
        'mail' => [
            'bcc' => "",
            'imap_host' => '{imap.gmail.com:993/imap/ssl/novalidate-cert}INBOX',
            'username' => 'aduxiaconsulting@gmail.com',
            'password' => '4dux14457'
        ],
        'twillio-whatsapp' => [
            'number' => '+14155238886',
            'sid' => "AC61f00d53f63d4dd9d82cf27155906340",
            'token' => "cefc0178b2dfa92f65c8dd0981f8ea81",
            'callback' => "https://***/whatsapp/twilio-whatsapp"
        ]
    ]
];
