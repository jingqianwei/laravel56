<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/4 0004
 * Time: 11:12
 */

namespace App\Console\Commands;

use App\Services\RequestClient;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Console\Command;

class GuzzleDemo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'guzzle:demo';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Guzzle demo';

    /**
     * Create a new command instance.
     *
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     * @return mixed
     */
    public function handle()
    {
        $this->line(date('Y-m-d H:i:s').' ok...');

        $requestClient = new RequestClient('',9);
        $imageUrl = 'https://cdn.shopify.com/s/files/1/2472/1662/products/6_1024x1024_7aebf745-068d-4359-aa21-b55957b78ce5.jpg?v=1521167684';
        $response = $requestClient->get($imageUrl);

        $code = $response->getStatusCode();

        $this->line(date('Y-m-d H:i:s') . ' 请求状态码为 ' . $code);
        try{
            $image = $response->getBody();
            $imageSavePath = storage_path('tmp/b55957b78ce5.jpg');
            $saved = file_put_contents($imageSavePath,$image );

            $this->line(date('Y-m-d H:i:s') . ' 文件大小: ' . var_export($saved,true));
            $this->line(date('Y-m-d H:i:s').' ok done');
        }catch(ConnectException  $e){
            $this->line('网络问题,需要重新尝试： '. $imageUrl );
        }catch(ClientException $e){
            $this->line('4xx 错误,需要重新尝试：'. $imageUrl );
        }catch(ServerException $e){
            $this->line('5xx 错误,需要重新尝试： '. $imageUrl );
        }


        $this->line(date('Y-m-d H:i:s').' fake...');

        $requestClient = new RequestClient('',10);

        $imageUrl = 'https://cdn.shopify.com/s/files/1/2472/1662/products/6_1024x1024_7aebf745-068d-4359-aa21-b55957b78ce6.jpg?v=1521167686';

        $response = $requestClient->get($imageUrl);

        $code = $response->getStatusCode();

        $this->line(date('Y-m-d H:i:s') . ' 请求状态码为 ' . $code);
        try{
            $image = $response->getBody();

            $imageSavePath = storage_path('tmp/b55957b78ce4.jpg');
            $saved = file_put_contents($imageSavePath,$image );

            $this->line(date('Y-m-d H:i:s'). var_export($saved,true));
            $this->line(date('Y-m-d H:i:s').' fake done');

        }catch(ConnectException  $e){
            $this->line('网络问题,需要重新尝试： '. $imageUrl );
        }catch(ClientException $e){
            $this->line('4xx 错误,需要重新尝试：'. $imageUrl );
        }catch(ServerException $e){
            $this->line('5xx 错误,需要重新尝试： '. $imageUrl );
        }
    }
}