<?php

namespace Higreen\Api\Qiniu;

use Higreen\Api\Http;

/**
 * 七牛云海量存储系统
 * 文档地址：https://developer.qiniu.com/kodo/api/3939/
 */
class Kodo extends Base
{
    /**
     * 获取上传凭证
     *
     * @param  array $config [
     * scope    [str] [必填] [目标资源空间 Bucket]
     * callback [str] [可选] [回调地址]
     * ]
     * @return string
     */
    public function getUploadToken(array $config)
    {
        $data = [
            'scope' => $config['scope'],
            'callbackUrl' => $config['callback'],
            'callbackBodyType' => 'application/json',
            'callbackBody' => '{
                "bucket": "$(bucket)",
                "key": "$(key)",
                "mime": "$(mimeType)",
                "name": "$(fname)",
                "size": "$(fsize)",
                "category": "$(x:category)"
            }',
            'deadline' => time() + 3600,
        ];
        $data = json_encode($data);
        $data = $this->encodeUrl($data);
        $sign = hash_hmac('sha1', $data, $this->secret_key, true);
        $sign = $this->encodeUrl($sign);
        $token = $this->access_key . ':' . $sign . ':' . $data;

        return $token;
    }

    /**
     * 删除资料
     *
     * @param  string $bucket [空间名]
     * @param  string $key    [文件名]
     * @return string
     */
    public function delete(string $bucket, string $key)
    {
        $protocol = 'http://';
        $host = 'rs.qbox.me';
        $path = '/delete/';
        $path .= $this->encodeUrl("{$bucket}:{$key}");
        $authorization = $this->getAuthorization($path, $host);

        // 发送请求
        $res = Http::post([
            'url' => $protocol . $host . $path,
            'header' => ["Authorization: {$authorization}"],
            'content_type' => 'form',
        ]);

        return $res;
    }

    /**
     * 获取管理凭证
     *
     * @param  string $path         [路径]
     * @param  string $host         [主机]
     * @param  string $content_type [内容类型]
     * @return string
     */
    public function getAuthorization(string $path, string $host)
    {
        $sign_str = "POST {$path}";
        $sign_str .= "\nHost: {$host}";
        $sign_str .= "\nContent-Type: application/x-www-form-urlencoded";
        $sign_str .= "\n\n";
        $sign = hash_hmac('sha1', $sign_str, $this->secret_key, true);
        $sign = $this->encodeUrl($sign);

        return "Qiniu {$this->access_key}:{$sign}";
    }

    /**
     * 编码URL
     *
     * @param  str $str [被编码的字符串]
     * @return str
     */
    private function encodeUrl(string $str)
    {
        return str_replace(['+', '/'], ['-', '_'], base64_encode($str));
    }

}
