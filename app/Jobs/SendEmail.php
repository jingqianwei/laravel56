<?php

namespace App\Jobs;

use Illuminate\Mail\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

/**
 * 发邮件
 * Class SendEmail
 * @package App\Jobs
 */
class SendEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var string|array
     */
    private $address;
    /**
     * @var array
     */
    private $parameter;

    /**
     * Create a new job instance.
     *
     * $address 发送邮件地址，字符串或者数组
     *
     * $parameter 支持的参数有：
     * view 为发送模板，data 为view中的填充数据
     * content 为纯文本邮件
     * subject 发送标题
     * form 邮件发件地址  from_name 发件别名 （可为空）
     * attachment 附件地址 attachment_as 附件显示名称 attachment_mime 附件的文档格式 （可为空）
     *
     * @param string|array $address
     * @param array $parameter
     */
    public function __construct($address, array $parameter = [])
    {
        $this->address = $address;
        $this->parameter = $parameter;
    }

    /**
     * Execute the job.
     *
     * @param Mailer $mailer
     * @return void
     */
    public function handle(Mailer $mailer)
    {
        $parameter = $this->parameter;
        $content = $parameter['content'] ?? '';
        $data = $parameter['data'] ?? '';
        $view = $parameter['view'] ?? '';

        if ($view) {
            $mailer->send($view, $data, function (Message $message) {
                self::sendCallback($message);
            });
        } elseif ($content) {
            $mailer->raw($content, function (Message $message) {
                self::sendCallback($message);
            });
        }

        $rawBody = $this->job->getRawBody();
        $info = json_decode($rawBody, true);
        \Log::info('queue id:' . $info['id']);
    }

    private function sendCallback(Message $message)
    {
        $parameter = $this->parameter;
        $from = $parameter['from'] ?? '';
        $subject = $parameter['subject'] ?? '';
        $from_name = $parameter['from_name'] ?? '';
        $attachment = $parameter['attachment'] ?? '';
        $attachment_as = $parameter['attachment_as'] ?? '';
        $attachment_mime = $parameter['attachment_mime'] ?? '';

        $m = $message
            ->to($this->address)
            ->subject($subject);

        if ($from) {
            $m->from($from, $from_name ?: null);
        }

        if ($attachment) {
            $options = [];

            if ($attachment_as) {
                // 附件的名字如果是中文，可能会乱码
                if (!str_contains($attachment_as, '?UTF-8?B?')) {
                    $attachment_as = "=?UTF-8?B?" . base64_encode($attachment_as) . "?=.";
                }
                $options['as'] = $attachment_as;
            }

            if ($attachment_mime) {
                $options['mime'] = $attachment_mime;
            }
            $m->attach($attachment, $options);
        }
    }
}
