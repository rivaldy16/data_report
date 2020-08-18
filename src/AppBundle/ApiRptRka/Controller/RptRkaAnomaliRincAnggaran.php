<?php
namespace AppBundle\ApiRptRka\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Lib\LivedRestClient;

class RptRkaAnomaliRincAnggaran extends Controller
{
    private $uriRestRenja;
    private $uriRestRka;
    private $uriRestSikd;
    private $restClient;
    private $kdTenant;
    
    static private $pathRkaReport = "rkareport";
    
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
        $rapbd_rapbd_id = $this->request->query->get("rapbd_rapbd_id");
        $sikd_satker_id = $this->request->query->get("sikd_satker_id");

        $param = [
            'tahun' => $tahun,
            'jns_report' => $jns_report,
            'rapbd_rapbd_id' => $rapbd_rapbd_id,
            'sikd_satker_id' => $sikd_satker_id
        ];
        //print_r($this->uriRestRka);exit;
        $this->restClient->setBaseUri($this->uriRestRka);
        $rkaReports = $this->restClient->getCollection("$tahun/rkareports", $param);

        return $rkaReports;
    }
    
}