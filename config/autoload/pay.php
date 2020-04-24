<?php
declare(strict_types=1);

return [
    'alipay' => [
        'app_id' => '2016102200738196',
        'ali_public_key' => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAiT+RowpFw1XlM1qsifbwhSdIjfIDGpXiUIq/Y+poJy+XJ2Z8I0clNN6/TAVjs6rrbNGeF8LlJC/eM+vAfzmfiaVXpdvo9o7o6jz67ZQT5OACa5QUBgwPLEFyUcwgHoSQRwdSOJyIqdDu800bqFe5VgOajsSBw9HLg3hMYb2r4UXnTCIHBauOTp8gL3asIu7Crbqk6XWF1jgg8H6Scw4sAuC2ZENSRlhmlb00MqeJtyihNovDfkkwPQZStSZjAj4h+7VxFrdRvi1KWDIUqA0h1VqC6QwCs4IC7uYnqD4pULuQS0czwTEAta5L7zVHyPJYTPtzHmtBB2eYO/MS9QMEhQIDAQAB',
        'driver' => \App\Handler\Pay\AliPay::class,
        'private_key' => 'MIIEpQIBAAKCAQEAr76xWKj1futBdTWVzMP1jn2B8xq16O6Y8Fr0uEh9N+DLhWcE5wk6eIZAs1aYpeoe2PpD9smQLM7Sa4wRjRb9xExe8q/aZQTFbLXj9PvxzavsJ7FPF2IuTCxv2MIe+EDFlYmdUrHNlpHK5ZmtZC4lHGhK3Dhi1vRE9SYNUOBIM+0Q4eh5cNt5ahnYfgBNkFsSILQSGZBBRQrHqd7H4P2ihs4A342VVnv6/cehCAaK+VaXFKkAKhiiEFBPWVTi7ziwxRFV5nTC0mPlRyVg5YVpfexhTPtJ98wpvcbOpBCgPpiwbIPXxy2NcZQAWMl/wXr9wi0Xco1lKGPK+YUpjpO9BwIDAQABAoIBAQCL4NlC2efbxt9orOe3/Ng7O0Xs+nuDFDUCSUKyUm/nw7H5Uc+jG8NAHorssqX4mGlJBZiGmlN4dn0gQHHPvFbqoGaJ/tFyyeNg7TysxsLkkkv6I7PxxqFW25+CBK+lo9gfd1KSeRZLG2tEZ5aMP/YlsBS1RuPUdsNT45+BmaGeQWBqBYX/0THJmhWk2LdsDgLU7ln1cS4CMhPLqgi71+Gg/EtJ8k3Jkr/OpySKuyDRZgxtcT045xdJlDyekCxhAEwcCpPfPVQ16WWJ1pTbirhuoNYqVAjH/SrFWyvvfkAeXLEtfzn9dgtl22hvGSS6CIb/UeWQxVQUqXLx1CQ36D2hAoGBAP7pa8xD8m5Crxlx4szm+wgjnG8gvds4igLtXrGkYDV1hKO+GQCOKjxjbvfwwI8iFHubmigaDR7R0mxYeJ+tchPMlW3fiz4VSymO89NHZSizPVdy/kaksHRdFkEULC9DBnxoV83aRfzfaESDfuV0VRXrNfDZ4Pz59vshtB4tW3sfAoGBALB+wStgpHHIHkHYWJxT8/cWyyO0SLBWD3yr8TqiLvlWcAuuC74Jt8VKTjN4tFKlr3Ggbg1DLDof5iCNiwtF4BvLx8+EtmbVlqFq4rEDFMxm32zlDy+cxj2V+ovXUcJhWEYgBtIUy3zrsdowtLhDNWO5Cw1qiS87PxpJs4xlO2kZAoGBAKuxsQfeZDXb+HBdAvQcR80Nn1pCZV540Ix0MSnZm2umgfaAHfr/xnbySlX59NjzjXRMNCL6mQe/L8oGNQjoHEQ1shhVT0Y7tWqCfLw8BGjLgW+bZqVSW/+ki06+NZyHuqCk7y8Z67YNC//JyfjmyECBMs3NEvuRqccwxk7lIg7lAoGBAKJYheEbrUfIFyTkF2X8x01CVysJe09QakB4fJU9d838V2Y2+zAcCkFcvyATaHMZWo8/Tdu/LSBuFSVebUa6SJHo2WumHI3s6/igs9K1Dd2SxvOIo3ZdU/B0U5lsPxV4q1Udwohdfmvy9Y7I/IL9t096d5MoqvWaHspUfuame4aJAoGAPZ7nA3LFJ4EAUUFTUAi+PImzP+g0F/1qjvjMtYEyZk6CACogJ3CZJs74sfC/I8HPxa+fWTk7gZuJVkOOibY2nObLGLpAbxUSM//SDbpNb0AtD665MbKPDbG+OJexm+04MAAaVBlrjUZEQ1ZoCgsumzCYyM6b6wsjqxNeYVrhyeI=',
        'log' => [
            'file' => '/hyperf-skeleton/runtime/logs/alipay.log',
        ],
        'mode' => 'dev',
    ],

    'wechat' => [
        'app_id' => '',
        'mch_id' => '',
        'driver' => \App\Handler\Pay\WechatPay::class,
        'key' => '',
        'cert_client' => '',
        'cert_key' => '',
        'log' => [
            'file' => '/hyperf-skeleton/runtime/logs/wechat_pay.log',
        ],
    ],
];