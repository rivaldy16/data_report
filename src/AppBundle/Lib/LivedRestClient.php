<?php
namespace AppBundle\Lib;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use GuzzleHttp\Client;

class LivedRestClient
{
    protected $baseUri;
    private $kdTenant;
    private $defaultParams;

    public function __construct(RequestStack $request_stack)
    {
        $this->kdTenant = $request_stack->getCurrentRequest()->headers->get('tenant');
        $this->defaultParams = ['http_errors' => false, 'headers' => ['Connection' => 'close', 'tenant' => $this->kdTenant]];
    }

    public function setBaseUri($baseUri)
    {
        $this->baseUri = rtrim(trim($baseUri), "/")."/";
    }
    
    public function getCollection($path, $params = array())
    {
        $client = new Client(['base_uri' => $this->baseUri]);
        $response = $client->request('GET', $path, array_merge(['headers' => ['tenant' => $this->kdTenant]],['query' => $params]));

        if ($response->getStatusCode() == 200) {  // OK
            if ($response->getBody() != "") {
                $jsons = json_decode($response->getBody());
                return $jsons;
            } else {
                return [];
            }
        }
        return false;
    }

    public function get($path, $id)
    {
        $client = new \GuzzleHttp\Client(['base_uri' => $this->baseUri]);
        $res = $client->request('GET', "$path/$id", $this->defaultParams);
        if ($res->getStatusCode() == 200) {  // OK
            return json_decode($res->getBody());
        }
        return false;
    }
}
