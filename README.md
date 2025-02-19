# PHP Api
![php-badge](https://img.shields.io/badge/php-%3E%3D%207-8892BF.svg)
[![packagist-badge](http://img.higreen.top/phpapi.svg)](https://packagist.org/packages/higreen/phpapi)
[![Total Downloads](https://poser.pugx.org/higreen/phpapi/downloads)](https://packagist.org/packages/higreen/phpapi)  
第三方服务，接口封装。

## Installation

``` bash
composer require higreen/phpapi
```

## Features

* [微信](#微信)
    + [小程序](#weixin-mini)
        - 登录凭证校验
        - 获取接口调用凭证
        - 获取小程序码
        - 下发小程序和公众号统一的服务消息
        - 解密数据
    + [小程序-内容安全](#weixin-mini-security)
        - 校验一张图片
        - 校验一段文本
    + [公众号](#weixin-Offi)
        - 登录凭证校验
        - 获取接口调用凭据
        - 获取微信JS接口的临时票据
        - 获取微信JS接口的权限验证配置
        - 获取用户信息
        - 是否关注公众号
        - 发送模板消息
    + [支付](#weixin-pay)
        - 下单
        - 查询订单
        - 退款
        - 回调报文解密
    + [企业付款](#weixin-transfer)
* [支付宝](#支付宝)
    + [小程序](#alipay-mini)
        - 获取支付宝user_id
    + [支付](#alipay-pay)
        - APP支付
        - 小程序支付
        - 扫码支付
        - 回调数据验证签名
* [短信](#短信)
    + [阿里云](#sms-aliyun)
    + [腾讯云](#sms-tencent)
* [百度云](#百度云)
    + [语音](#baidu-speech)
* [腾讯云](#腾讯云)
    + [地图](#tencent-map)

---

## Usage

### 微信

<details>
  <summary><b id="weixin-mini">小程序</b></summary>

<!-- toc -->

```php
use Higreen\Api\Weixin\Mini;

$Mini = new Mini([
    'app_id' => '小程序ID',
    'app_secret' => '小程序密钥',
]);
```

1. 登录凭证校验
    ```php
    $session = $Mini->code2session($code);
```
2. 获取接口调用凭证
    ```php
    $access_token = $Mini->getAccessToken();
```
3. 获取小程序码
    ```php
    $res = Mini::getWXACodeUnlimit($access_token, [
        'scene'      => '[str] [必填] [最大32个可见字符]',
        'page'       => '[str] [可选] [已经发布的小程序存在的页面]',
        'width'      => '[int] [可选] [二维码的宽度，单位 px，最小 280px，最大 1280px]',
        'auto_color' => '[bol] [可选] [自动配置线条颜色]',
        'line_color' => '[arr] [可选] [auto_color 为 false 时生效，使用 rgb 设置颜色 例如 {"r":"xxx","g":"xxx","b":"xxx"} 十进制表示]',
        'is_hyaline' => '[bol] [可选] [是否需要透明底色]',
    ]);
    ```
4. 下发小程序和公众号统一的服务消息
    ```php
    $res = Mini::sendUniformMessage($access_token, [
        'touser'      => '[必填] [str] [用户openid，可以是小程序的openid，也可以是mp_template_msg.appid对应的公众号的openid]',
        'appid'       => '[必填] [str] [公众号appid，要求与小程序有绑定且同主体]',
        'template_id' => '[必填] [str] [公众号模板id]',
        'url'         => '[必填] [str] [公众号模板消息所要跳转的url]',
        'data'        => '[必填] [arr] [模板内容，格式形如 { "key1": { "value": any }, "key2": { "value": any } }]',
        'pagepath'    => '[可选] [str] [点击模板卡片后的跳转页面，仅限本小程序内的页面。支持带参数,（示例index?foo=bar）。该字段不填则模板无跳转。]',
    ]);
    ```
5. 解密数据
    ```php
    $data = Mini::decryptData($encryptedData, $session_key, $iv);
    ```

<!-- tocstop -->

</details>

<details>
  <summary><b id="weixin-mini-security">小程序-内容安全</b></summary>

<!-- toc -->

```php
use Higreen\Api\Weixin\MiniSecurity;
```

1. 校验一张图片
    ```php
    $bool = $MiniSecurity::checkImg($access_token, '图片链接');
    ```
2. 检查一段文本
    ```php
    $bool = $MiniSecurity::checkText($access_token, '文字内容');
    ```

<!-- tocstop -->

</details>

<details>
  <summary><b id="weixin-offi">公众号</b></summary>

<!-- toc -->

```php
use Higreen\Api\Weixin\Offi;

$Offi = new Offi([
    'app_id' => '公众号ID',
    'app_secret' => '公众号密钥',
]);
```

1. 登录凭证校验
   
    ```php
    $session = $Offi->code2session($code);
    ```

2. 获取接口调用凭据

    ```php
    $access_token = $Offi->getAccessToken();
    ```

3. 获取用户信息
    ```php
    $userinfo = Offi::getUserinfo($access_token, 'openid');
    ```

4. 获取微信JS接口的临时票据
    ```php
    $ticket = Offi::getJsapiTicket($access_token);
    ```

5. 获取微信JS接口的权限配置

    ```php
    $config = Offi::getJsapiConfig($ticket, '网页的URL(可选)')
    ```

6. 发送模板消息

    ```php
    $res = Offi::sendMessage($access_token, [
        'touser'      => '[str] [必填] [接收者openid]',
        'template_id' => '[str] [必填] [模板ID]',
        'data'        => '[arr] [必填] [模板数据]',
    ]);
    ```

7. 是否关注公众号
    ```php
    $bool = Offi::isFollow($access_token, 'openid');
    ```

<!-- tocstop -->

</details>

<details>
  <summary><b id="weixin-pay">支付</b></summary>

<!-- toc -->

```php
use Higreen\Api\Weixin\Pay;

$Pay = new Pay([
    'mch_id'     => '商户ID',
    'mch_key'    => '商户密钥',
    'sslcert'    => '证书路径',
    'sslkey'     => '证书密钥路径',
]);
```

1. 下单
    ```php
    $res = $Pay->order('交易类型(大小写不限)：JSAPI,APP,H5,Native', [
        'appid'        => '[str] [必填] [微信生成的应用ID]',
        'description'  => '[str] [必填] [商品描述]',
        'out_trade_no' => '[str] [必填] [商户系统内部订单号，同一个商户号下唯一]',
        'total'        => '[int] [必填] [订单总金额，单位为分]',
        'notify_url'   => '[int] [必填] [直接可访问的URL，不允许携带查询串，要求必须为https]',
        'attach'       => '[str] [可选] [附加数据，在查询API和支付通知中原样返回]',
        'openid'       => '[str] [可选] [JSAPI交易类型必传，用户在商户appid下的唯一标识]',
        'type'         => '[str] [可选] [H5交易类型必传，场景类型 示例值：iOS, Android, Wap]',
    ]);
    ```
    
2. 查询订单
   
    ```php
    $res = $Pay->orderResult('商户系统内部订单号');
    ```
    
3. 退款

    ```php
    $res = $Pay->refund([
        'out_trade_no'  => '[str] [必填] [原支付交易对应的商户订单号]',
        'out_refund_no' => '[str] [必填] [商户系统内部的退款单号，商户系统内部唯一]',
        'total'         => '[int] [必填] [原支付交易的订单总金额，币种的最小单位]',
        'refund'        => '[int] [必填] [退款金额，币种的最小单位]',
        'reason'        => '[str] [可选] [退款原因]',
        'notify_url'    => '[str] [可选] [异步接收微信支付退款结果通知的回调地址]',
    ]);
    ```
    
4. 回调报文解密

    ```php
    $res = Pay::decryptResource('商户号APIV3密钥', '回调的加密报文');
    ```

<!-- tocstop -->

</details>

<details>
  <summary><b id="weixin-transfer">企业付款</b></summary>

<!-- toc -->

```php
use Higreen\Api\Weixin\Transfer;

$Transfer = new Transfer([
    'mch_id'     => '商户ID',
    'mch_key'    => '商户密钥',
    'app_id'     => '应用ID',
    'notify_url' => '回调地址',
    'sslcert'    => '证书路径',
    'sslkey'     => '证书密钥路径',
]);
```
1. 到零钱
    ```php
    $res = $Transfer->balance([
        'partner_trade_no' => '[str] [必填] [商户订单号]',
        'openid'           => '[str] [必填] [用户openid]',
        'amount'           => '[int] [必填] [企业付款金额，单位为分]',
        'desc'             => '[str] [可选] [企业付款备注]',
        're_user_name'     => '[str] [可选] [收款用户姓名]',
    ]);
    ```
2. 到银行卡

<!-- tocstop -->

</details>

### 支付宝

<details>
  <summary><b id="alipay-mini">小程序</b></summary>

<!-- toc -->

```php
use Higreen\Api\Alipay\Mini;

$Mini = new Mini($init);
```

1. 获取支付宝user_id
    `$user_id = $Mini->getUserId($code);`

<!-- tocstop -->

</details>

<details>
  <summary><b id="alipay-pay">支付</b></summary>

<!-- toc -->

```php
use Higreen\Api\Alipay\Pay;
$Pay = new Pay($init);
```

1. APP支付
    `$res = $Pay->app($params);`
2. 小程序支付
    `$res = $Pay->mini($params);`
3. 扫码支付
    ```php
    $res = $Pay->qrcode([
        'subject'      => '交易标题',
        'out_trade_no' => '商户订单号',
        'total_amount' => '订单总金额，单位为人民币（元）',
    ]);
    ```
4. 回调数据验证签名
    `$res = Pay::checkSignature($params, '支付宝公钥');`

<!-- tocstop -->

</details>

### 短信

<details>
  <summary><b>阿里云</b></summary>

<!-- toc -->

```php
use Higreen\Api\Sms\Aliyun;

$init = [
    'id' => '应用Access Key ID',
    'secret' => '应用Access Key Secret',
    'sign' => '短信签名',
];
$Sms = new Aliyun($init);
$res = $Sms->send($phones, $template, $params);
```

<!-- tocstop -->

</details>

<details>
  <summary><b>腾讯云</b></summary>

<!-- toc -->


```php
use Higreen\Api\Sms\Tencent;

$init = [
    'id' => '应用id，SDK AppID',
    'key' => '应用密钥，App Key',
    'sign' => '短信签名',
];
$Sms = new Tencent($init);
$res = $Sms->send($phone, $tpl_id, $tpl_params);
```

<!-- tocstop -->

</details>

### 百度云

<details>
  <summary><b id="baidu-speech">语音</b></summary>

<!-- toc -->

```php
use Higreen\Api\Baidu\Speech;

$init = [
    'api_key'    => '[str] [必填] [应用公钥]',
    'secret_key' => '[str] [必填] [应用密钥]',
];
$Speech = new Speech($init);
```

1. 获取接口调用凭证
    ```php
    $access_token = $Speech->getAccessToken();
    ```

2. 文字转语音
    ```php
    $res = $Speech->text2audio($access_token, [
        'text' => '[str] [必填] [文字]',
    ]);
    ```

<!-- tocstop -->

</details>

### 腾讯云

<details>
  <summary><b id="tencent-map">地图</b></summary>

<!-- toc -->

```php
use Higreen\Api\Tencent\Map;

$Map = new Map('key');
```

1. 地址定位
    ```php
    $res = $Map->locateByAddress($address);
    ```
2. IP定位
    ```php
    $res = $Map->locateByIp($ip);
    ```
3. 查询行政区域
    ```php
    $res = $Map->getDistrict($id = 0);// 子级行政区划
    ```

<!-- tocstop -->

</details>
