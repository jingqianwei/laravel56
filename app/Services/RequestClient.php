<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/17 0017
 * Time: 16:13
 */

namespace App\Services;

use GuzzleHttp\Client;

class RequestClient
{
    public $base_uri;

    /**
     * @var Client
     */
    public $client;

    public $http_errors;

    public function __construct($base_uri, $timeout, $http_errors = ['http_errors' => false])
    {
        $this->base_uri = $base_uri;

        $this->http_errors = $http_errors;

        $this->client = new Client([
            'base_uri' => $base_uri,
            'timeout'  => $timeout,
        ]);
    }
    
    public function get($url, array $paramsArray = [])
    {
        if (!empty(http_build_query($paramsArray))) {
            $fullUrl = $this->base_uri . $url . '?' . http_build_query($paramsArray);
        } else {
            $fullUrl = $this->base_uri . $url;
        }

        return $this->client->get($fullUrl, $this->http_errors);
    }

    /**
     * explain
     *
     * demo:
     *
     * // Provide the body as a string.
     *       $r = $client->request('POST', 'http://httpbin.org/post', [
     *           'body' => 'raw data'
     *       ]);
     *
     * @param $url
     * @param array $options
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function post($url, array $options)
    {
        $fullUrl = $this->base_uri . $url;

        return $this->client->post( $fullUrl ,$options);
    }

    public function formPost($url, array $form)
    {
        $fullUrl = $this->base_uri . $url;

        return $this->client->post( $fullUrl , [
            'form_params'   => $form
        ]);
    }

    public function jsonPost($url, array $json)
    {
        $fullUrl = $this->base_uri . $url;

        return $this->client->post( $fullUrl , [
            'json'  => $json
        ]);
    }

    /**
     * $response = $client->request('POST', 'http://httpbin.org/post', [
     *      'multipart' => [
     *          [
     *               'name'     => 'field_name',
     *              'contents' => 'abc'
     *          ],
     *          [
     *              'name'     => 'file_name',
     *              'contents' => fopen('/path/to/file', 'r')
     *           ],
     *          [
     *              'name'     => 'other_file',
     *              'contents' => 'hello',
     *              'filename' => 'filename.txt',
     *              'headers'  => [
     *              'X-Foo' => 'this is an extra header to include'
     *           ]
     *          ]
     *          ]
     *      ]);
     *
     * @param $url
     * @param array $files
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function filePost($url, array $files)
    {
        $fullUrl = $this->base_uri . $url;

        return $this->client->post( $fullUrl , [
            'multipart' => $files
        ]);
    }
}