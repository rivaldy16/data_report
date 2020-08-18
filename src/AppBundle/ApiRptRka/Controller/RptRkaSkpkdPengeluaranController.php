<?php
namespace AppBundle\ApiRptRka\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Lib\LivedRestClient;

class RptRkaSkpkdPengeluaranController extends Controller
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
        
        $jns_report = $this->request->query->get("jns_report");
        $tahun = $this->request->query->get("tahun");
        $id_rka_rka = $this->request->query->get("id_rka_rka");

        $param = [
            'tahun' => $tahun,
            'jns_report' => $jns_report,
            'id_rka_rka' => $id_rka_rka
        ];
        
        $this->restClient->setBaseUri($this->uriRestRka);
        $rkaReports = $this->restClient->getCollection("$tahun/rkareports", $param);

        return $rkaReports;
    }
    
}