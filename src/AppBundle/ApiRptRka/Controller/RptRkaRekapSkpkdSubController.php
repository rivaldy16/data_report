<?php
namespace AppBundle\ApiRptRka\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Lib\LivedRestClient;

class RptRkaRekapSkpkdSubController extends Controller
{
    private $uriRestRenja;
    private $uriRestRka;
    private $uriRestSikd;
    private $restClient;
    private $kdTenant;
    
    static private $pathRkaReport = "rkareports";
    
    public function __construct($request_stack, $rest_client, $uri_rest_renja, $uri_rest_setup, $uri_rest_rka)
    {
        $this->request = $request_stack->getCurrentRequest();
        $this->restClient = $rest_client;
        $this->kdTenant = $this->request->headers->get("tenant");
        $this->uriRestRenja = $uri_rest_renja;
        $this->uriRestSikd = $uri_rest_setup;
        $this->uriRestRka = $uri_rest_rka;
    }
    
    public function getDataReport()
    {
        
        $jnsRpt = $this->request->query->get("jns_report");
        $tahun = $this->request->query->get("tahun");
        $status_rka = $this->request->query->get("status_rka");

        $param = [
            'tahun' => $tahun,
            'jns_report' => $jnsRpt,
            'status_rka' => $status_rka
        ];
        
        $this->restClient->setBaseUri($this->uriRestRka);
        $rkaReports = $this->restClient->getCollection("$tahun/rkareports", $param);

        return $rkaReports;
    }
    
}