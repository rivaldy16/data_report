<?php
namespace AppBundle\ApiRptRpjmd\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Lib\LivedRestClient;

class RptRpjmdKbjknProgramSub3Controller extends Controller
{
    private $uriRestRpjmd;
    private $restClient;
    private $kdTenant;
    private $uriRestSikd;
    
    static private $pathRpjmdReport = "rpjmdreports";
    
    public function __construct($request_stack, $rest_client, $uri_rest_rpjmd, $uri_rest_setup)
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
        $idSasaran = $this->request->query->get("id_sasaran");
        $tahun = $this->request->query->get("tahun");
        $param = [
            'jns_report' => $jnsRpt,
            'id_sasaran' => $idSasaran,
            'tahun' => $tahun
        ];
        
        /*$this->restClient->setBaseUri($this->uriRestRpjmd);
        $rpjmdReports = $this->restClient->getCollection("rpjmdreports", $param);

        $rpjmdRepHandler = $rpjmdReports;

        $this->restClient->setBaseUri($this->uriRestSikd);

        //CONTAINS SIKD BIDANG ID
        $mapRpjmdSikdBidangId = [];
        $i = 0;
        foreach ($rpjmdRepHandler as $rpjmdRepId) {
            $mapRpjmdSikdBidangId[$i] =  $rpjmdRepId->rpjmd_program_sikd_bidang_id;
            $i++;
        }
        
        $ids2 = implode(',',  array_unique($mapRpjmdSikdBidangId));
        $paramIn2 = ['ids' => $ids2]; $paramIn2 ['parents']=3;
        $sikdRpjmdBidMstr = $this->restClient->getCollection("$tahun/sikdbidangs", $paramIn2);
        $mapSikdMstr = $this->populateSikdMstrInfo($sikdRpjmdBidMstr);

        foreach ($rpjmdReports as &$value1) {
             $value1->sikd_bidang_kd_bidang = $mapSikdMstr['bidang'][$value1->rpjmd_program_sikd_bidang_id]['kd_bidang'];
            $value1->sikd_bidang_nm_bidang = $mapSikdMstr['bidang'][$value1->rpjmd_program_sikd_bidang_id]['nm_bidang'];
        }

        return $rpjmdReports;*/
        $this->restClient->setBaseUri($this->uriRestRpjmd);
        $rpjmdReports = $this->restClient->getCollection("rpjmdreports", $param);
        return $rpjmdReports;
    }

    private function populateSikdMstrInfo($sikdInfoList){
        $mapSikdInfo = []; $bidangList=[]; $urusanList=[];
        foreach ($sikdInfoList as $sikdInfoBlock) {
            //BIDANG
            $infoListBidang['id_sikd_bidang'] = $sikdInfoBlock->id_sikd_bidang;
            $infoListBidang['kd_bidang'] = $sikdInfoBlock->kd_bidang;
            $infoListBidang['nm_bidang'] = $sikdInfoBlock->nm_bidang;
            $infoListBidang['id_sikd_urusan'] = $sikdInfoBlock->sikd_urusan_id;
            $infoListBidang['nm_urusan'] = $sikdInfoBlock->sikd_urusan_nm_urusan;
            $infoListBidang['kd_urusan'] = $sikdInfoBlock->sikd_urusan_kd_urusan;
            $bidangList[$sikdInfoBlock->id_sikd_bidang] = $infoListBidang;
        }
        $mapSikdInfo['bidang'] = $bidangList;
        return $mapSikdInfo;
    }
}