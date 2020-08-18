<?php
namespace AppBundle\ApiRptRenstra\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Lib\LivedRestClient;

class RptRenstraVisiMisiController extends Controller
{
    private $uriRestRenstra;
    private $uriRestSikd;
    private $uriRestRpjmd;
    private $restClient;
    private $kdTenant;
    
    static private $pathRenstraReport = "renstrareports";
    
    public function __construct($request_stack, $rest_client, $uri_rest_renstra)
    {
        $this->request = $request_stack->getCurrentRequest();
        $this->restClient = $rest_client;
        $this->kdTenant = $this->request->headers->get("tenant");
        $this->uriRestRenstra = $uri_rest_renstra;
    }
    
    public function getDataReport()
    {
        $jnsRpt = $this->request->query->get('jns_report');
        $idRenstra = $this->request->query->get("id_renstra");
        $param = [
            'jns_report' => $jnsRpt,
            'id_renstra' => $idRenstra,
        ];
        
        $this->restClient->setBaseUri($this->uriRestRenstra);
        $renstraReports = $this->restClient->getCollection("renstrareports", $param);
        return $renstraReports;
    }
}