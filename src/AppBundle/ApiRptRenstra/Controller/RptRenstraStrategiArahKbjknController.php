<?php
namespace AppBundle\ApiRptRenstra\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Lib\LivedRestClient;

class RptRenstraStrategiArahKbjknController extends Controller
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
        $idRenstra = $this->request->query->get("id_renstra");
        $tahun = $this->request->query->get("tahun");
        $param = [
            'jns_report' => $jnsRpt,
            'id_renstra' => $idRenstra,
        ];
        
        /*$this->restClient->setBaseUri($this->uriRestRenstra);
        $renstraReports = $this->restClient->getCollection("renstrareports", $param);
        //print_r($renstraReports);exit;

        $renstra = $this->restClient->get("renstrarenstras", $idRenstra);
        $idSatker = '';
        $idSubUnit = '';

        if ($renstra != ''){
            $idSatker = $renstra->sikd_satker_id;
            $idSubUnit = $renstra->sikd_sub_skpd_id;
        }

        $nmSatker = '';
        $kdSatker = '';
        $this->restClient->setBaseUri($this->uriRestSikd);
        $satker = $this->restClient->getCollection("$tahun/sikdsatkers/$idSatker");
        //print_r($satker);exit;
        if ($satker){
            $nmSatker = $satker->nama;
            $kdSatker = $satker->kode;
        }
        
        //GET SUB SKPD'S INFO
        $nmSubSkpd = 'Semua Sub Unit'; $kdSubSkpd = '';
        if($idSubUnit != ''){
            $this->restClient->setBaseUri($this->uriRestSikd);
            $subSkpd = $this->restClient->getCollection("$tahun/sikdskpds/$idSatker/sikdsubskpds/$idSubUnit");
            if ($subSkpd && sizeof($subSkpd) > 0){
                $nmSubSkpd = $subSkpd->nama;
                $kdSubSkpd = $subSkpd->kode;
            }
        }
        $satkerType = 'SikdSkpd';

        //print_r($Rpjmds);exit;
        $this->restClient->setBaseUri($this->uriRestRpjmd);
        $idRpjmd = '';
        if ($renstraReports){
            $idRpjmd = $renstraReports[0]->rpjmd_rpjmd_id;
        }

        //print_r($idRpjmd);exit;
        $visis = [];
        if ($idRpjmd != '')
            $visis = $this->restClient->getCollection("rpjmdrpjmds/".$idRpjmd . " /rpjmdvisis");
        //print_r($visis);exit;

        $idVisi = '';
        $uraianVisi = '';
        if ($visis){
            $idVisi = $visis[0]->id_rpjmd_visi;
            $uraianVisi = $visis[0]->uraian_visi;
        }
            
        foreach ($renstraReports as &$value1) {
                    $value1->no_misi = doubleval($value1->no_misi);
                    $value1->id_sikd_satker = $idSatker;
                    $value1->id_sikd_sub_skpd = $idSubUnit;
                    $value1->kd_satker = $kdSatker;
                    $value1->nm_satker = $nmSatker;
                    $value1->kd_sub_skpd = $kdSubSkpd;
                    $value1->nm_sub_skpd = $nmSubSkpd;
                    $value1->id_rpjmd_visi = $idVisi;
                    $value1->uraian_visi = $uraianVisi;
        }

        return $renstraReports;*/

        $this->restClient->setBaseUri($this->uriRestRenstra);
        $renstraReports = $this->restClient->getCollection("renstrareports", $param);
        return $renstraReports;
    }
}