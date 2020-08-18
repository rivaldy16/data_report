<?php
namespace AppBundle\ApiRptRenja\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Lib\LivedRestClient;

class RptRenjaRekapProgramRkpdController extends Controller
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
        
        /*$tahun = $param['tahun'];
        $idSatker = $param['sikd_satker_id'];
        $idSubUnit = $param['sikd_sub_skpd_id'];
        
        $this->restClient->setBaseUri($this->uriRestRenja);
        $renjaReports = $this->restClient->getCollection("$tahun/renjareports", $param);
        
        $renjaRepHandler = $renjaReports;
        //CONTAINS SIKD PROG ID
        $mapRenjaProgSikdProgId = [];
        $i = 0;
        foreach ($renjaRepHandler as $renjaProgSikdProgId) {
            $mapRenjaProgSikdProgId[$i] =  $renjaProgSikdProgId->renj_prog_sikd_prog_id;
            $i++;
        }
        //GET SASTKER'S INFO
        $nmSatker = ''; $kdSatker = '';
        $this->restClient->setBaseUri($this->uriRestSikd);
        $satker = $this->restClient->getCollection("$tahun/sikdsatkers/$idSatker");
        if ($satker){
            $nmSatker = $satker->nama;
            $kdSatker = $satker->kode;
        }
        //GET SUB SKPD'S INFO
        $nmSubSkpd = 'Semua Sub Unit'; $kdSubSkpd = '';
        if($idSubUnit != ''){
            $this->restClient->setBaseUri($this->uriRestSikd);
            $subSkpd = $this->restClient->getCollection("$tahun/sikdskpds/$idSatker/sikdsubskpds/$idSubUnit");
            if ($subSkpd && sizeof($subSkpd) > 1){
                $nmSubSkpd = $subSkpd->nama;
                $kdSubSkpd = $subSkpd->kode;
            }
        }
        //CONTAINS SIKD'S INFO BASED ON CHILD NODE
        $param2 ['sikd_prog_id'] = $mapRenjaProgSikdProgId;
        $ids = implode(',', $param2["sikd_prog_id"]); $parentParam = '&parents=3';
        $paramIn = ['ids' => $ids];$paramIn ['parents']=3;
        $sikdRenja = $this->restClient->getCollection("$tahun/sikdprogs", $paramIn);
        $mapSikd = $this->populateSikdInfo($sikdRenja);
        //CONTAINS SIKD'S INFO BASED ON DIRECT ID FROM RENJA
        $renj_bid_ids = [];
        foreach ($renjaReports as &$value1){
            $renj_bid_ids[]=$value1->renj_prog_sikd_bidang_id;
        }
        $ids2 = implode(',', $renj_bid_ids);
        $paramIn2 = ['ids' => $ids2]; $paramIn2 ['parents']=3;
        $sikdRenjaBidMstr = $this->restClient->getCollection("$tahun/sikdbidangs", $paramIn2); 
        $mapSikdMstr = $this->populateSikdMstrInfo($sikdRenjaBidMstr);
        
        $renjaRekapProgram = array();
        foreach ($renjaReports as &$value1) {
            $value1->sikd_satker_kode = $kdSatker;
            $value1->sikd_satker_nama = $nmSatker;
            $value1->sikd_sub_skpd_kode = $kdSubSkpd;
            $value1->sikd_sub_skpd_nama = $nmSubSkpd;
//                     $value1->renja_renja_id_renja_renja = $value1->renja_renja_id_renja_renja;
//                     $value1->renja_renja_tahun = $value1->renja_renja_tahun;
//                     $value1->renja_program_kd_program = $value1->renja_program_kd_program;
            $value1->sikd_prog_id_sikd_prog = $mapSikd['prog'][$value1->renj_prog_sikd_prog_id]['id_sikd_prog'];
            $value1->sikd_prog_kd_prog = $mapSikd['prog'][$value1->renj_prog_sikd_prog_id]['kd_prog'];
            $value1->sikd_prog_nm_prog = $mapSikd['prog'][$value1->renj_prog_sikd_prog_id]['nm_prog'];
            if(array_key_exists($value1->renj_prog_sikd_bidang_id,$mapSikd['bidang'])){
                $mapSikdNow = $mapSikd;
            } else {
                $mapSikdNow = $mapSikdMstr;
            }
            $value1->sikd_bidang_id_sikd_bidang = $mapSikdNow['bidang'][$value1->renj_prog_sikd_bidang_id]['id_sikd_bidang'];
            $value1->sikd_bidang_kd_bidang =$mapSikdNow['bidang'][$value1->renj_prog_sikd_bidang_id]['kd_bidang'];
            $value1->sikd_bidang_nm_bidang = $mapSikdNow['bidang'][$value1->renj_prog_sikd_bidang_id]['nm_bidang'];
            $value1->sikd_urusan_id_sikd_urusan = $mapSikdNow['bidang'][$value1->renj_prog_sikd_bidang_id]['sikd_urusan_id'];
            $value1->sikd_urusan_kd_urusan = $mapSikdNow['bidang'][$value1->renj_prog_sikd_bidang_id]['sikd_urusan_kd_urusan'];
            $value1->sikd_urusan_nm_urusan = $mapSikdNow['bidang'][$value1->renj_prog_sikd_bidang_id]['sikd_urusan_nm_urusan'];
            $value1->rkpd_program_pagu_indikatif = doubleval($value1->rkpd_program_pagu_indikatif);
            $value1->renja_program_pagu_indikatif = doubleval($value1->renja_program_pagu_indikatif);
            $value1->selisih_renja_rkpd = doubleval($value1->selisih_renja_rkpd);
        }
        return $renjaReports;*/
    }
    
    /*private function populateSikdInfo($sikdInfoList){
        $mapSikdInfo = []; $kgtnList=[]; $progList=[]; $bidangList=[]; $urusanList=[];
        foreach ($sikdInfoList as $sikdInfoBlock) {
            //PROGRAM
            $infoListProg['id_sikd_prog'] = $sikdInfoBlock->id_sikd_prog;
            $infoListProg['kd_prog'] = $sikdInfoBlock->kd_prog;
            $infoListProg['nm_prog'] = $sikdInfoBlock->nm_prog;
            $progList[$sikdInfoBlock->id_sikd_prog] = $infoListProg;
            //BIDANG
            $infoListBidang['id_sikd_bidang'] = $sikdInfoBlock->sikd_bidang_id;
            $infoListBidang['nm_bidang'] = $sikdInfoBlock->sikd_bidang_nm_bidang;
            $infoListBidang['kd_bidang'] = $sikdInfoBlock->sikd_bidang_kd_bidang;
            $infoListBidang['sikd_urusan_id'] = $sikdInfoBlock->sikd_urusan_id;
            $infoListBidang['sikd_urusan_nm_urusan'] = $sikdInfoBlock->sikd_urusan_nm_urusan;
            $infoListBidang['sikd_urusan_kd_urusan'] = $sikdInfoBlock->sikd_urusan_kd_urusan;
            $bidangList[$sikdInfoBlock->sikd_bidang_id] = $infoListBidang;
        }
        $mapSikdInfo['prog'] = $progList;
        $mapSikdInfo['bidang'] = $bidangList;
        $mapSikdInfo['urusan'] = $urusanList;
        return $mapSikdInfo;
    }
    
    private function populateSikdMstrInfo($sikdInfoList){
        $mapSikdInfo = []; $bidangList=[]; $urusanList=[];
        foreach ($sikdInfoList as $sikdInfoBlock) {
            //BIDANG
            $infoListBidang['id_sikd_bidang'] = $sikdInfoBlock->id_sikd_bidang;
            $infoListBidang['kd_bidang'] = $sikdInfoBlock->kd_bidang;
            $infoListBidang['nm_bidang'] = $sikdInfoBlock->nm_bidang;
            $infoListBidang['sikd_urusan_id'] = $sikdInfoBlock->sikd_urusan_id;
            $infoListBidang['sikd_urusan_nm_urusan'] = $sikdInfoBlock->sikd_urusan_nm_urusan;
            $infoListBidang['sikd_urusan_kd_urusan'] = $sikdInfoBlock->sikd_urusan_kd_urusan;
            $bidangList[$sikdInfoBlock->id_sikd_bidang] = $infoListBidang;
        }
        $mapSikdInfo['bidang'] = $bidangList;
        return $mapSikdInfo;
    }*/
}