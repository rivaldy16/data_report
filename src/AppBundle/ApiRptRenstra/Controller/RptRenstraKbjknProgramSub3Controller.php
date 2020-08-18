<?php
namespace AppBundle\ApiRptRenstra\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Lib\LivedRestClient;

class RptRenstraKbjknProgramSub3Controller extends Controller
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
        $tahun = $this->request->query->get("tahun");
        $param = [
            'jns_report' => $jnsRpt,
            'id_sasaran' => $idSasaran,
        ];
        
        /*$this->restClient->setBaseUri($this->uriRestRenstra);
        $renstraReports = $this->restClient->getCollection("renstrareports", $param);
        $renstraRepHandler = $renstraReports;

        $mapSikdBidangId = [];
        $i = 0;
        foreach ($renstraRepHandler as $renstraBidangId) {
            $mapSikdBidangId[$i] =  $renstraBidangId->renstra_program_sikd_bidang_id;
            $i++;
        }

        $param2 ['sikd_bidang_id'] = $mapSikdBidangId;
        //print_r($mapSikdBidangId);exit;
        $ids = implode(',', $param2["sikd_bidang_id"]);
        $paramIn = ['ids' => $ids];$paramIn ['parents']=3;
        $this->restClient->setBaseUri($this->uriRestSikd);
        $sikdBidang = $this->restClient->getCollection("$tahun/sikdbidangs", $paramIn);
        //print_r($sikdBidang);exit;
        $mapSikdRekRincObj = $this->populateSikdInfo($sikdBidang);
        //print_r($mapSikdRekRincObj);exit;

        foreach ($renstraReports as &$value1) {
                    $value1->sikd_bidang_kd_bidang = $mapSikdRekRincObj['renstra_program_sikd_bidang_id'][$value1->renstra_program_sikd_bidang_id]['sikd_bidang_kd_bidang'];
                    $value1->sikd_bidang_nm_bidang = $mapSikdRekRincObj['renstra_program_sikd_bidang_id'][$value1->renstra_program_sikd_bidang_id]['sikd_bidang_nm_bidang'];
        }

        return $renstraReports;
    }

    private function populateSikdInfo($sikdInfoList){
        $mapSikdInfo = []; $rekRincObjList=[];
        foreach ($sikdInfoList as $sikdInfoBlock) {
            //RINCIAN OBJEK - REK OBJEK - REK AKUN - REK JENIS - REK KELOMPOK
            $infoListRincObj['sikd_bidang_kd_bidang'] = $sikdInfoBlock->kd_bidang;
            $infoListRincObj['sikd_bidang_nm_bidang'] = $sikdInfoBlock->nm_bidang;
            $rekRincObjList[$sikdInfoBlock->id_sikd_bidang] = $infoListRincObj;
        }
        $mapSikdInfo['renstra_program_sikd_bidang_id'] = $rekRincObjList;
        return $mapSikdInfo;*/
        $this->restClient->setBaseUri($this->uriRestRenstra);
        $renstraReports = $this->restClient->getCollection("renstrareports", $param);

        return $renstraReports;
    }
}