<?php
namespace AppBundle\ApiRptRenstra\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Lib\LivedRestClient;

class RptRenstraProgKgtnPrioritasSub1Controller extends Controller
{
    private $uriRestRenstra;
    private $uriRestSikd;
    private $uriRestRpjmd;
    private $restClient;
    private $kdTenant;
    
    static private $pathRenstraReport = "renstrareports";
    
    public function __construct($request_stack, $rest_client, $uri_rest_renstra, $uri_rest_setup, $uri_rest_rpjmd)
    {
        $this->request = $request_stack->getCurrentRequest();
        $this->restClient = $rest_client;
        $this->kdTenant = $this->request->headers->get("tenant");
        $this->uriRestRenstra = $uri_rest_renstra;
        $this->uriRestSikd = $uri_rest_setup;
        $this->uriRestRpjmd = $uri_rest_rpjmd;
    }
    
    public function getDataReport()
    {
        $jnsRpt = $this->request->query->get('jns_report');
        $idSasaran = $this->request->query->get("id_sasaran");
        $param = [
            'jns_report' => $jnsRpt,
            'id_sasaran' => $idSasaran
        ];

       
        $this->restClient->setBaseUri($this->uriRestRenstra);
        $renstraReports = $this->restClient->getCollection("renstrareports", $param);
        return $renstraReports;
    }
}