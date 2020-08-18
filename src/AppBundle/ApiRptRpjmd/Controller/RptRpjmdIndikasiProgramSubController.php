<?php
namespace AppBundle\ApiRptRpjmd\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Lib\LivedRestClient;

class RptRpjmdIndikasiProgramSubController extends Controller
{
    private $uriRestRpjmd;
    private $restClient;
    private $kdTenant;
    private $uriRestSikd;
    
    static private $pathRpjmdReport = "rpjmdreports";
    
    public function __construct($request_stack, $rest_client, $uri_rest_rpjmd,  $uri_rest_setup)
    {
        $this->request = $request_stack->getCurrentRequest();
        $this->restClient = $rest_client;
        $this->kdTenant = $this->request->headers->get("tenant");
        $this->uriRestRpjmd = $uri_rest_rpjmd;
        $this->uriRestSikd = $uri_rest_setup;
    }
    
    public function getDataReport()
    {
        $jnsRpt = $this->request->query->get('jns_report');
        $idProgram = $this->request->query->get("id_program");
        //$tahun = $this->request->query->get("tahun");
        $param = [
            'jns_report' => $jnsRpt,
            'id_program' => $idProgram,
        ];
        
        $this->restClient->setBaseUri($this->uriRestRpjmd);
        $rpjmdReports = $this->restClient->getCollection("rpjmdreports", $param);

        return $rpjmdReports;
    }
}