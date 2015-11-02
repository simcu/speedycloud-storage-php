<?php namespace Speedycloud\Storage;
/**
 * Speedycloud Object Storage Auth - http://www.Speedycloud.cn
 * @author xRain <xrain@simcu.com>
 * @package simcu/speedycloud-storage
 */
class Auth
{
    protected $access_key;
    protected $secret_key;
    protected $gate_url;
    protected $domain;
    protected $bucket;

    protected function __construct()
    {
        $this->domain = 'cos.speedycloud.org';
        $this->gate_url = 'http://' . $this->domain;
    }

    /**
     * init the key;
     * @param string $access_key
     * @param string $secret_key
     * @praam string $bucket
     */
    public function init($access_key, $secret_key, $bucket)
    {
        $this->access_key = $access_key;
        $this->secret_key = $secret_key;
        $this->bucket = $bucket;
    }

    /**
     * create the query signature
     * @param string $method
     * @param string $content
     * @param string $type
     * @param string $date
     * @param string $amz_headers
     * @param string $params
     * @param string $uri
     */
    private function createSign($method, $content, $type, $amz_headers, $params, $uri)
    {
        $date = $this->getDate();
        $signstr = $method . PHP_EOL;
        if (empty($content)) {
            $signstr .= PHP_EOL;
        } else {
            $signstr .= $content . PHP_EOL;
        }
        if (empty($type)) {
            $signstr .= PHP_EOL;
        } else {
            $signstr .= $type . PHP_EOL;
        }
        $signstr .= $date . PHP_EOL;
        if (!empty($amz_headers)) {
            $signstr .= $amz_headers . PHP_EOL;
        }
        if (!empty($params)) {
            $signstr .= $params . PHP_EOL;
        }
        $signstr .= $uri;
        return [
            'date' => $date,
            'sign' => base64_encode(hash_hmac("sha1", $signstr, $this->secret_key, true))
        ];
    }

    /**
     * Get the GMT date string
     * @return string
     */
    private function getDate()
    {
        return gmdate("D, d M Y H:i:s") . " GMT";
    }

    /**
     * Create query headers array
     * @param $method
     * @param $content
     * @param $type
     * @param $amz_headers
     * @param $params
     * @param $uri
     * @return array
     */
    protected function createHeaders($method, $content, $type, $amz_headers, $params, $uri)
    {
        $cs = $this->createSign($method, $content, $type, $amz_headers, $params, $uri);
        $headers = [
            'Host: ' . $this->domain,
            'Date: ' . $cs['date'],
            'Authorization: AWS ' . $this->access_key . ":" . $cs['sign']
        ];
        if(!empty($type)){
            $headers[] = "Content-Type: ".$type;
        }
        return $headers;
    }
}
