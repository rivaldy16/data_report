<?php
namespace AppBundle\ApiRptRenstra\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Lib\LivedRestClient;

class RptRenstraProgKgtnPrioritasController extends Controller
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
        //$idSatker = $this->request->query->get("id_satker");
        $param = [
            'jns_report' => $jnsRpt,
            'id_renstra' => $idRenstra/*,
            'id_satker' => $idSatker,*/
        ];
        
        /*$this->restClient->setBaseUri($this->uriRestRenstra);
        $renstraReports = $this->restClient->getCollection("renstrareports", $param);
        //print_r($renstraReports);exit;


        $renjaRepHandler = $renstraReports;

        $renstra = $this->restClient->get("renstrarenstras", $idRenstra);
        $mapRenstraSikdSatker = [];
        $i = 0;
        foreach ($renjaRepHandler as $rentraSikdSatkerId) {
            $mapRenstraSikdSatker[$i] =  $rentraSikdSatkerId->renstra_renstra_sikd_satker_id;
            $i++;
        }

        $param2 ['sikd_satker_id'] = $mapRenstraSikdSatker;
        $ids = implode(',', $param2["sikd_satker_id"]);
        $paramIn = ['ids' => $ids];$paramIn ['parents']=4;
        $this->restClient->setBaseUri($this->uriRestSikd);
        $sikdSatkers = $this->restClient->getCollection("$tahun/sikdsatkers", $paramIn);
        //print_r($sikdSatkers);exit;
        $mapSikdSatkers = $this->populateSikdInfo($sikdSatkers);

        $mapRenstraSikdBidangId = [];
        $i = 0;
        foreach ($renjaRepHandler as $rentraSikdBidangId) {
            $mapRenstraSikdBidangId[$i] =  $rentraSikdBidangId->renstra_program_sikd_bidang_id;
            $i++;
        }

        $param2 ['sikd_bidang_id'] = $mapRenstraSikdBidangId;
        $ids = implode(',', $param2["sikd_bidang_id"]);
        $paramIn = ['ids' => $ids];$paramIn ['parents']=4;
        $this->restClient->setBaseUri($this->uriRestSikd);
        $sikdBidangs = $this->restClient->getCollection("$tahun/sikdbidangs", $paramIn);
        //print_r($sikdBidangs);exit;
        $mapSikdBidangs = $this->populateSikdBidangInfo($sikdBidangs);
       
        foreach ($renstraReports as &$value1) {
             $value1->id_sikd_satker = $mapSikdSatkers['sikd_satker'][$value1->renstra_renstra_sikd_satker_id]['id_sikd_satker'];
             $value1->kd_satker = $mapSikdSatkers['sikd_satker'][$value1->renstra_renstra_sikd_satker_id]['kd_satker'];
             $value1->nm_satker = $mapSikdSatkers['sikd_satker'][$value1->renstra_renstra_sikd_satker_id]['nm_satker'];
             $value1->id_sikd_sub_skpd = '';
             $value1->kd_sub_skpd = '';
             $value1->nm_sub_skpd = '';
             $value1->id_sikd_bidang = $mapSikdBidangs['sikd_bidangs'][$value1->renstra_program_sikd_bidang_id]['id_sikd_bidang'];
             $value1->kd_bidang = $mapSikdBidangs['sikd_bidangs'][$value1->renstra_program_sikd_bidang_id]['kd_bidang'];
             $value1->nm_bidang = $mapSikdBidangs['sikd_bidangs'][$value1->renstra_program_sikd_bidang_id]['nm_bidang'];
        }

        return $renstraReports;*/
        $this->restClient->setBaseUri($this->uriRestRenstra);
        $renstraReports = $this->restClient->getCollection("renstrareports", $param);
        return $renstraReports;
    }

    private function populateSikdInfo($sikdInfoList){
        $mapSikdInfo = []; $SidkProgList=[];
        foreach ($sikdInfoList as $sikdInfoBlock) {
            //RINCIAN OBJEK - REK OBJEK - REK AKUN - REK JENIS - REK KELOMPOK
            $infoListRincObj['id_sikd_satker'] = $sikdInfoBlock->id_sikd_satker;
            $infoListRincObj['kd_satker'] = $sikdInfoBlock->kode;
            $infoListRincObj['nm_satker'] = $sikdInfoBlock->nama;
            $SidkProgList[$sikdInfoBlock->id_sikd_satker] = $infoListRincObj;
        }
        $mapSikdInfo['sikd_satker'] = $SidkProgList;
        return $mapSikdInfo;
    }

    private function populateSikdBidangInfo($sikdInfoList){
        $mapSikdInfo = []; $SidkProgList=[];
        foreach ($sikdInfoList as $sikdInfoBlock) {
            //RINCIAN OBJEK - REK OBJEK - REK AKUN - REK JENIS - REK KELOMPOK
            $infoListRincObj['id_sikd_bidang'] = $sikdInfoBlock->id_sikd_bidang;
            $infoListRincObj['kd_bidang'] = $sikdInfoBlock->kd_bidang;
            $infoListRincObj['nm_bidang'] = $sikdInfoBlock->nm_bidang;
            $SidkProgList[$sikdInfoBlock->id_sikd_bidang] = $infoListRincObj;
        }
        $mapSikdInfo['sikd_bidangs'] = $SidkProgList;
        return $mapSikdInfo;
    }
}