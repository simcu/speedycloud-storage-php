<?php namespace Speedycloud\Storage;
/**
 * Speedycloud Storage Actor - http://www.speedycloud.cn
 */
class Actor extends Auth
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get all files in bucket
     */
    public function getObjects()
    {
        $http_method = 'GET';
        $http_url = $this->gate_url . '/' . $this->bucket;
        $headers = $this->createHeaders($http_method, '', '', '', '', '/' . $this->bucket);
        $data = $this->query($http_method, $http_url, $headers);
        return $data;
    }

    public function newObject($remote, $local)
    {
        $http_method = 'PUT';
        $http_url = $http_url = $this->gate_url . '/' . $this->bucket . $remote;
        $headers = $this->createHeaders($http_method, md5_file($local), 'application/x-www-form-urlencoded', '', '', '/'.$this->bucket . $remote);
        $data = $this->query($http_method,$http_url,$headers,file_get_contents($local));
        return $data;
    }

    private function query($method, $url, $headers, $data='')
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        if(!empty($data)){
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            $headers[] = "Content-Length: ".strlen($data);
        }
        var_dump($headers);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $return = curl_exec($ch);
        curl_close($ch);
        return $return;
    }
}
