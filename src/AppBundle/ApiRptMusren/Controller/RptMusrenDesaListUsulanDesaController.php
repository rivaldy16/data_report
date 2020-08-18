<?php
namespace AppBundle\ApiRptMusren\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Lib\LivedRestClient;

class RptMusrenDesaListUsulanDesaController extends Controller
{
    private $uriRestRkpd;
    private $uriRestSikd;
    private $restClient;
    private $kdTenant;
    private $uriRestRenja;
    private $uriRestMusren;
    
    static private $pathMusrenReport = "musrenreports";
    
    public function __construct($request_stack, $rest_client, $uri_rest_rkpd, $uri_rest_renja, $uri_rest_musren ,$uri_rest_setup)
    {
        $this->request = $request_stack->getCurrentRequest();
        $this->restClient = $rest_client;
        $this->kdTenant = $this->request->headers->get("tenant");
        $this->uriRestRkpd = $uri_rest_rkpd;
        $this->uriRestRenja = $uri_rest_renja;
        $this->uriRestMusren = $uri_rest_musren;
        $this->uriRestSikd = $uri_rest_setup;
    }
    
    public function getDataReport()
    {        
        //print_r("ok");exit;
        $jnsRpt = $this->request->query->get('jns_report');
        $kec = $this->request->query->get("kec");
        $desa = $this->request->query->get("desa");
        $idBidang = $this->request->query->get("bidang");
        $prioritas = $this->request->query->get("prioritas");
        $quota = $this->request->query->get("quota");
        $tahun = $this->request->query->get("tahun");
        
        $param = [
            'jns_report' => $jnsRpt,
            'kec' => $kec,
            'desa' => $desa,
            'bidang' => $idBidang,
            'prioritas' => $prioritas,
            'quota' => $quota
        ];
        
        $this->restClient->setBaseUri($this->uriRestMusren);
        //print_r($this->uriRestMusren);exit;
        $musrenReports = $this->restClient->getCollection("$tahun/musrenreports", $param);

        return $musrenReports;
    }    

}