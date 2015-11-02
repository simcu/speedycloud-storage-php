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
        $http_uri = '/' . $this->bucket;
        $headers = $this->createHeaders($http_method, '', '', '', '', $http_uri);
        $data = $this->query($http_method, $http_uri, $headers);
        if (substr($data['header'], 9, 3) == '200') {
            return [
                'max' => $data['body']['MaxKeys'],
                'total' => count($data['body']['Contents']),
                'list' => $data['body']['Contents']
            ];
        } else {
            return false;
        }
    }

    /**
     * create new object
     * @param $remote remote file name
     * @param $local local file path
     * @return \SimpleXMLElement
     */
    public function newObject($remote, $local)
    {
        $http_uri = '/' . $this->bucket . $remote;
        $http_method = 'PUT';
        $headers = $this->createHeaders($http_method, '', 'application/x-www-form-urlencoded', '', '', $http_uri);
        $data = $this->query($http_method, $http_uri, $headers, file_get_contents($local));
        if (substr($data['header'], 9, 3) == '200') {
            return true;
        } else {
            return false;
        }
    }

    /**
     * delete the object
     * @param $name object name
     * @return bool
     */
    public function delObject($name)
    {
        $http_uri = '/' . $this->bucket . $name;
        $http_method = 'DELETE';
        $headers = $this->createHeaders($http_method, '', '', '', '', $http_uri);
        $data = $this->query($http_method, $http_uri, $headers);
        return true;
    }

    /**
     * get the object
     * @param $name object name
     * @return bool|string file content
     */
    public function getObject($name)
    {
        $http_uri = '/' . $this->bucket . $name;
        $http_method = 'GET';
        $headers = $this->createHeaders($http_method, '', '', '', '', $http_uri);
        $data = $this->query($http_method, $http_uri, $headers, false);
        if (substr($data['header'], 9, 3) == '200') {
            return $data['body'];
        } else {
            return false;
        }
    }

    /**
     * curl query action
     * @param $method
     * @param $url
     * @param $headers
     * @param string $data
     * @return \SimpleXMLElement
     */
    private function query($method, $url, $headers, $xml = true, $data = '')
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->gate_url . $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        if (!empty($data)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            $headers[] = "Content-Length: " . strlen($data);
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        $return = curl_exec($ch);
        curl_close($ch);
        $return = explode("\r\n\r\n", $return, 2);
        if ($xml) {
            return [
                'header' => $return[0],
                'body' => isset($return[1]) ? json_decode(json_encode(simplexml_load_string($return[1])), true) : []
            ];
        } else {
            return [
                'header' => $return[0],
                'body' => $return[1]
            ];
        }
    }
}
