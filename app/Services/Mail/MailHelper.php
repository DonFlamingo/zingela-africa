<?php namespace App\Services\Mail;

use Illuminate\Support\Facades\Mail;
use Swift_MailTransport as MailTransport;
use GuzzleHttp\Exception\ClientException;

class MailHelper
{
    public function send($to, $body, $subject, $lang = NULL, $fallback = TRUE, $attaches = [], $view = NULL)
    {
        if (empty($view))
            $view = 'front::Emails.template';

        if (!is_array($to))
            $to = [$to];

        if (!empty($attaches) && is_string($attaches))
            $attaches = [$attaches];

        $data = [
            'to'       => array_map('trim', $to),
            'subject'  => $subject,
            'body'     => $body,
            'lang'     => $lang,
            'attaches' => $attaches
        ];

        try
        {
            Mail::send($view, $data, function ($message) use ($data) {
                $message
                    ->to($data['to'])
                    ->subject($data['subject']);

                if (!empty($data['attaches'])) {
                    foreach ($data['attaches'] as $attach) {
                        $message->attach($attach);
                    }
                }
            });

        }
        catch (ClientException $e) {
            $error = $e->getMessage();

            $response = $e->getResponse();

            if ( $response && $response->getStatusCode() == 422 )
                $fallback = FALSE;
        }
        catch (\Exception $e) {
            $error = $e->getMessage();
        }

        if (!empty($error) && $fallback) {
            $backupMailer = Mail::getSwiftMailer();

            Mail::setSwiftMailer( new \Swift_Mailer(MailTransport::newInstance()) );

            Mail::send($view, $data, function ($message) use ($data) {
                $message
                    ->to($data['to'])
                    ->subject($data['subject']);

                if (!empty($data['attaches'])) {
                    $message->attach($data['attaches']);
                }
            });

            Mail::setSwiftMailer( $backupMailer );
        }

        return [
            'status' => empty($error),
            'error'  => empty($error) ? NULL : $error
        ];
    }

}