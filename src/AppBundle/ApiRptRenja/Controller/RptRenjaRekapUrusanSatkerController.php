<?php
namespace AppBundle\ApiRptRenja\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Lib\LivedRestClient;

class RptRenjaRekapUrusanSatkerController extends Controller
{
    private $uriRestRenja;
    private $uriRestSikd;
    private $restClient;
    private $kdTenant;
    
    static private $pathRenjaReport = "renjareports";
    
    public function __construct($request_stack, $rest_client, $uri_rest_renja, $uri_rest_setup)
    {
        $this->request = $request_stack->getCurrentRequest();
        $this->restClient = $rest_client;
        $this->kdTenant = $this->request->headers->get("tenant");
        $this->uriRestRenja = $uri_rest_renja;
        $this->uriRestSikd = $uri_rest_setup;
    }
    
    public function getDataReport()
    {        
        $jnsRpt = $this->request->query->get('jns_report');
        $jnsRenja = $this->request->query->get("jns_renja");
        $idRenja = $this->request->query->get("id_renja");
        $idSatker = $this->request->query->get("sikd_satker_id");
        $tahun = $this->request->query->get("tahun");
        $idSubUnit = $this->request->query->get("sikd_sub_skpd_id");
        $param = [
            'jns_report' => $jnsRpt,
            'id_renja' => $idRenja,
            'tahun' => $tahun ,
            'sikd_satker_id' => $idSatker,
            'sikd_sub_skpd_id' => $idSubUnit,
            'jns_renja' => $jnsRenja
        ];
        
        $this->restClient->setBaseUri($this->uriRestRenja);
        $renjaReports = $this->restClient->getCollection("$tahun/renjareports", $param);
        return $renjaReports;
        
        }
}