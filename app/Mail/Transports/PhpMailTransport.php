<?php

namespace App\Mail\Transports;

use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\MessageConverter;

/**
 * Transport sử dụng hàm PHP mail() thuần làm fallback khi hosting Linux khóa proc_open() hoặc cổng SMTP ngoài.
 */
class PhpMailTransport extends AbstractTransport
{
    protected function doSend(SentMessage $message): void
    {
        if (!function_exists('mail')) {
            throw new TransportException('Hàm PHP mail() không khả dụng trên máy chủ hiện tại.');
        }

        $email = MessageConverter::toEmail($message->getOriginalMessage());
        $envelope = $message->getEnvelope();

        $toList = array_map(
            static fn (Address $addr) => $addr->getAddress(),
            $email->getTo() ?: $envelope->getRecipients()
        );
        $toHeader = implode(', ', array_filter($toList));
        if ($toHeader === '') {
            throw new TransportException('Không có địa chỉ người nhận cho PhpMailTransport.');
        }

        $rawSubject = (string) ($email->getSubject() ?? '');
        $encodedSubject = $rawSubject !== '' ? $this->encodeMimeHeaderValue($rawSubject) : '';

        $htmlBody = $email->getHtmlBody();
        $textBody = $email->getTextBody();
        $isHtml = $htmlBody !== null && $htmlBody !== '';
        $bodyRaw = $isHtml ? (string) $htmlBody : (string) ($textBody ?? '');
        $encodedBody = rtrim(chunk_split(base64_encode($bodyRaw), 76, "\r\n"));

        $headers = [
            'MIME-Version: 1.0',
            'Content-Type: ' . ($isHtml ? 'text/html' : 'text/plain') . '; charset=UTF-8',
            'Content-Transfer-Encoding: base64',
        ];

        $fromAddresses = $email->getFrom();
        if (!empty($fromAddresses)) {
            $headers[] = 'From: ' . $this->formatAddressList($fromAddresses);
        } elseif ($envelope->getSender()->getAddress() !== '') {
            $headers[] = 'From: ' . $envelope->getSender()->getAddress();
        }

        $replyToAddresses = $email->getReplyTo();
        if (!empty($replyToAddresses)) {
            $headers[] = 'Reply-To: ' . $this->formatAddressList($replyToAddresses);
        }

        $ccAddresses = $email->getCc();
        if (!empty($ccAddresses)) {
            $headers[] = 'Cc: ' . $this->formatAddressList($ccAddresses);
        }

        $bccAddresses = $email->getBcc();
        if (!empty($bccAddresses)) {
            $headers[] = 'Bcc: ' . $this->formatAddressList($bccAddresses);
        }

        $headers[] = 'X-Mailer: DATYSO-Lead-Mailer';
        $headersString = implode("\r\n", $headers);

        $senderAddress = $envelope->getSender()->getAddress();
        if ($senderAddress === '' && !empty($fromAddresses)) {
            $senderAddress = $fromAddresses[0]->getAddress();
        }

        $additionalParams = '';
        if (
            $senderAddress !== ''
            && filter_var($senderAddress, FILTER_VALIDATE_EMAIL)
            && preg_match('/^[a-zA-Z0-9._+\-@]+$/', $senderAddress)
        ) {
            $additionalParams = '-f' . $senderAddress;
        }

        if (function_exists('error_clear_last')) {
            error_clear_last();
        }

        $sent = $additionalParams !== ''
            ? @mail($toHeader, $encodedSubject, $encodedBody, $headersString, $additionalParams)
            : @mail($toHeader, $encodedSubject, $encodedBody, $headersString);

        if (!$sent) {
            $lastError = error_get_last();
            $errMsg = is_array($lastError) && !empty($lastError['message'])
                ? $lastError['message']
                : 'PHP mail() trả về false (kiểm tra dịch vụ MTA/Exim trên hosting hoặc cấu hình SMTP).';
            throw new TransportException($errMsg);
        }
    }

    /**
     * @param  array<int, Address>  $addresses
     */
    private function formatAddressList(array $addresses): string
    {
        $formatted = [];
        foreach ($addresses as $addr) {
            $email = $addr->getAddress();
            $name = trim($addr->getName());
            if ($name !== '') {
                $formatted[] = $this->encodeMimeHeaderValue($name) . ' <' . $email . '>';
            } else {
                $formatted[] = $email;
            }
        }
        return implode(', ', $formatted);
    }

    private function encodeMimeHeaderValue(string $value): string
    {
        if (preg_match('/[^\x20-\x7E]/', $value)) {
            return '=?UTF-8?B?' . base64_encode($value) . '?=';
        }
        return str_replace(["\r", "\n"], '', $value);
    }

    public function __toString(): string
    {
        return 'php_mail://default';
    }
}
