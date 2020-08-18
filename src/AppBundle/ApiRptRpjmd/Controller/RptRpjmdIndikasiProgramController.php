<?php
namespace AppBundle\ApiRptRpjmd\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Lib\LivedRestClient;

class RptRpjmdIndikasiProgramController extends Controller
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
        $idRpjmd = $this->request->query->get("id_rpjmd");
        $tahun = $this->request->query->get("tahun");
        $param = [
            'jns_report' => $jnsRpt,
            'id_rpjmd' => $idRpjmd,
            'tahun' => $tahun
        ];

        $this->restClient->setBaseUri($this->uriRestRpjmd);
        $rpjmdReports = $this->restClient->getCollection("rpjmdreports", $param);
        return $rpjmdReports;
        
        /*$this->restClient->setBaseUri($this->uriRestRpjmd);
        $rpjmdReports = $this->restClient->getCollection("rpjmdreports", $param);

       $this->restClient->setBaseUri($this->uriRestSikd);
        //print_r($rpjmdReports);exit;
        $rpjmdRepHandler = $rpjmdReports;

        //CONTAINS SIKD BIDANG ID
        $mapRpjmdSikdBidangId = [];
        $i = 0;
        foreach ($rpjmdRepHandler as $rpjmdRepId) {
            $mapRpjmdSikdBidangId[$i] =  $rpjmdRepId->rpjmd_program_sikd_prog_id;
            $i++;
        }
        
        $ids2 = implode(',',  array_unique($mapRpjmdSikdBidangId));
        $paramIn2 = ['ids' => $ids2]; $paramIn2 ['parents']=4;
        $sikdRpjmdBidMstr = $this->restClient->getCollection("$tahun/sikdprogs", $paramIn2);
        //print_r($sikdRpjmdBidMstr);exit;
        $mapSikdMstr = $this->populateSikdMstrInfo($sikdRpjmdBidMstr);

        $mapRpjmdSikdSatkerId = [];
        $i = 0;
        foreach ($rpjmdRepHandler as $rpjmdSatkerId) {
            $mapRpjmdSikdSatkerId[$i] =  $rpjmdSatkerId->sikd_satker_id;
            $i++;
        }
        //print_r($mapRpjmdSikdSatkerId);exit;
        $ids2 = implode(',',  array_unique($mapRpjmdSikdSatkerId));
        $paramIn2 = ['ids' => $ids2]; $paramIn2 ['parents']=1;
        $sikdRpjmdSatker = $this->restClient->getCollection("$tahun/sikdsatkers", $paramIn2);
        //print_r($sikdRpjmdSatker);exit;
        $mapSikdSatker = $this->populateSikdSatker($sikdRpjmdSatker);
        foreach ($rpjmdReports as &$value1) {
            $value1->sikd_urusan_id_sikd_urusan = $mapSikdMstr['bidang'][$value1->rpjmd_program_sikd_prog_id]['id_sikd_urusan'];
            $value1->sikd_urusan_kd_sikd_urusan = $mapSikdMstr['bidang'][$value1->rpjmd_program_sikd_prog_id]['kd_urusan'];
            $value1->sikd_urusan_nm_sikd_urusan = $mapSikdMstr['bidang'][$value1->rpjmd_program_sikd_prog_id]['nm_urusan'];
            $value1->sikd_urusan_kd_urusan = $mapSikdMstr['bidang'][$value1->rpjmd_program_sikd_prog_id]['kd_urusan'];
            $value1->sikd_urusan_nm_urusan = $mapSikdMstr['bidang'][$value1->rpjmd_program_sikd_prog_id]['nm_urusan'];
            $value1->sikd_bidang_id_sikd_bidang = $mapSikdMstr['bidang'][$value1->rpjmd_program_sikd_prog_id]['id_sikd_bidang'];
            $value1->sikd_bidang_kd_bidang = $mapSikdMstr['bidang'][$value1->rpjmd_program_sikd_prog_id]['kd_bidang'];
            $value1->sikd_bidang_nm_bidang = $mapSikdMstr['bidang'][$value1->rpjmd_program_sikd_prog_id]['nm_bidang'];
            $value1->sikd_prog_kd_prog = $mapSikdMstr['bidang'][$value1->rpjmd_program_sikd_prog_id]['kd_prog'];
            if ($value1->sikd_satker_id != null){
                $value1->nm_opd_pelaksana = $mapSikdSatker['satker'][$value1->sikd_satker_id]['nama'];
            }else{
                $value1->nm_opd_pelaksana = null;
            }
        }

        return $rpjmdReports;*/
    }

    private function populateSikdMstrInfo($sikdInfoList){
        $mapSikdInfo = []; $bidangList=[]; $urusanList=[];
        foreach ($sikdInfoList as $sikdInfoBlock) {
            //BIDANG
            $infoListBidang['id_sikd_prog'] = $sikdInfoBlock->id_sikd_prog;
            $infoListBidang['kd_prog'] = $sikdInfoBlock->kd_prog;
            $infoListBidang['nm_prog'] = $sikdInfoBlock->nm_prog;
            $infoListBidang['id_sikd_bidang'] = $sikdInfoBlock->sikd_bidang_id;
            $infoListBidang['kd_bidang'] = $sikdInfoBlock->sikd_bidang_kd_bidang;
            $infoListBidang['nm_bidang'] = $sikdInfoBlock->sikd_bidang_nm_bidang;
            $infoListBidang['id_sikd_urusan'] = $sikdInfoBlock->sikd_urusan_id;
            $infoListBidang['nm_urusan'] = $sikdInfoBlock->sikd_urusan_nm_urusan;
            $infoListBidang['kd_urusan'] = $sikdInfoBlock->sikd_urusan_kd_urusan;
            $bidangList[strtoupper($sikdInfoBlock->id_sikd_prog)] = $infoListBidang;
        }
        $mapSikdInfo['bidang'] = $bidangList;
        return $mapSikdInfo;
    }
    private function populateSikdSatker($sikdInfoList){
        $mapSikdInfo = []; $satkerList=[];
        foreach ($sikdInfoList as $sikdInfoBlock) {
            //BIDANG
            $infoListSatker['id_sikd_satker'] = $sikdInfoBlock->id_sikd_satker;
            $infoListSatker['nama'] = $sikdInfoBlock->nama;
            $satkerList[$sikdInfoBlock->id_sikd_satker] = $infoListSatker;
        }
        $mapSikdInfo['satker'] = $satkerList;
        return $mapSikdInfo;
    }
}