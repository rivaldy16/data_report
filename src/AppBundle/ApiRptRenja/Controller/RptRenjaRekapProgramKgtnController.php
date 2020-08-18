<?php
namespace AppBundle\ApiRptRenja\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Lib\LivedRestClient;

class RptRenjaRekapProgramKgtnController extends Controller
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
        //CONTAINS SIKD KGTN ID
        $mapRenjaProgSikdKgtnId = [];
        $i = 0;
        foreach ($renjaRepHandler as $renjaProgSikdKgtnId) {
            $mapRenjaProgSikdKgtnId[$i] =  $renjaProgSikdKgtnId->renja_sikd_kgtn_id;
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
        //CONTAINS SIKD'S INFO
        $param2 ['sikd_kgtn_id'] = $mapRenjaProgSikdKgtnId;
        $ids = implode(',', $param2["sikd_kgtn_id"]); $parentParam = '&parents=3';
        $paramIn = ['ids' => $ids];$paramIn ['parents']=3;
        $sikdRenja = $this->restClient->getCollection("$tahun/sikdkgtns", $paramIn);
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
//                     $value1->renja_program_id_renja_program = $value1->renja_program_id_rkpd_program;
//                     $value1->renja_program_kd_program = $value1->renja_program_kd_program;
            $value1->sikd_prog_id_sikd_prog = $mapSikd['prog'][$value1->renj_prog_sikd_prog_id]['id_sikd_prog'];
            $value1->sikd_prog_kd_prog = $mapSikd['prog'][$value1->renj_prog_sikd_prog_id]['kd_prog'];
            $value1->sikd_prog_nm_prog = $mapSikd['prog'][$value1->renj_prog_sikd_prog_id]['nm_prog'];
            $value1->renja_program_tgt_anggaran_renstra = doubleval($value1->renja_program_tgt_anggaran_renstra);
            $value1->renja_program_rls_anggaran_sd_thn_lalu = doubleval($value1->renja_program_rls_anggaran_sd_thn_lalu);
            $value1->renja_program_pagu_indikatif = doubleval($value1->renja_program_pagu_indikatif);
//                     $value1->renja_program_keterangan = $value1->renja_program_keterangan;
            if(array_key_exists($value1->renj_prog_sikd_bidang_id,$mapSikd['bidang'])){
                $mapSikdNow = $mapSikd;
            } else {
                $mapSikdNow = $mapSikdMstr;
            }
            $value1->sikd_bidang_id_sikd_bidang = $mapSikdNow['bidang'][$value1->renj_prog_sikd_bidang_id]['id_sikd_bidang'];
            $value1->sikd_bidang_kd_bidang =$mapSikdNow['bidang'][$value1->renj_prog_sikd_bidang_id]['kd_bidang'];
            $value1->sikd_bidang_nm_bidang = $mapSikdNow['bidang'][$value1->renj_prog_sikd_bidang_id]['nm_bidang'];
            $value1->sikd_urusan_id_sikd_urusan = $mapSikdNow['bidang'][$value1->renj_prog_sikd_bidang_id]['sikd_urusan_id'];
            $value1->sikd_urusan_kd_urusan = $mapSikdNow['bidang'][$value1->renj_prog_sikd_bidang_id]['sikd_urusan_nm_urusan'];
            $value1->sikd_urusan_nm_urusan = $mapSikdNow['bidang'][$value1->renj_prog_sikd_bidang_id]['sikd_urusan_kd_urusan'];
//                     $value1->renja_kegiatan_id_renja_kegiatan = $value1->renja_kegiatan_id_renja_kegiatan;
//                     $value1->renja_kegiatan_sikd_kgtn_id = $value1->renja_kegiatan_sikd_kgtn_id;
//                     $value1->renja_kegiatan_kd_kegiatan_ =$value1->renja_kegiatan_kd_kegiatan_;
            $value1->renja_kegiatan_kd_kegiatan = $mapSikd['kgtn'][$value1->renja_sikd_kgtn_id]['kd_kgtn'];
//                     $value1->renja_kegiatan_nm_kegiatan = $value1->renja_kegiatan_nm_kegiatan;
//                     $value1->renja_kegiatan_no_subkegiatan = $value1->renja_kegiatan_no_subkegiatan;
//                     $value1->renja_kegiatan_nm_subkegiatan = $value1->renja_kegiatan_nm_subkegiatan;
            $value1->renja_kegiatan_jml_anggaran_rkpd = doubleval($value1->renja_kegiatan_jml_anggaran_rkpd);
            $value1->renja_kegiatan_tgt_anggaran_thn_ini = doubleval($value1->renja_kegiatan_tgt_anggaran_thn_ini);
        }
        return $renjaReports;*/
    }
    
    /*private function populateSikdInfo($sikdInfoList){
        $mapSikdInfo = []; $kgtnList=[]; $progList=[]; $bidangList=[]; $urusanList=[];
        foreach ($sikdInfoList as $sikdInfoBlock) {
            //KEGIATAN
            $infoListKgtn['id_sikd_kgtn'] = $sikdInfoBlock->id_sikd_kgtn;
            $infoListKgtn['nm_kgtn'] = $sikdInfoBlock->nm_kgtn;
            $infoListKgtn['kd_kgtn'] = $sikdInfoBlock->kd_kgtn;
            $kgtnList[$sikdInfoBlock->id_sikd_kgtn] = $infoListKgtn;
            //PROGRAM
            $infoListProg['id_sikd_prog'] = $sikdInfoBlock->sikd_prog_id;
            $infoListProg['nm_prog'] = $sikdInfoBlock->sikd_prog_nm_prog;
            $infoListProg['kd_prog'] = $sikdInfoBlock->sikd_prog_kd_prog;
            $progList[$sikdInfoBlock->sikd_prog_id] = $infoListProg;
            //BIDANG
            $infoListBidang['id_sikd_bidang'] = $sikdInfoBlock->sikd_bidang_id;
            $infoListBidang['nm_bidang'] = $sikdInfoBlock->sikd_bidang_nm_bidang;
            $infoListBidang['kd_bidang'] = $sikdInfoBlock->sikd_bidang_kd_bidang;
            $infoListBidang['sikd_urusan_id'] = $sikdInfoBlock->sikd_urusan_id;
            $infoListBidang['sikd_urusan_nm_urusan'] = $sikdInfoBlock->sikd_urusan_nm_urusan;
            $infoListBidang['sikd_urusan_kd_urusan'] = $sikdInfoBlock->sikd_urusan_kd_urusan;
            $bidangList[$sikdInfoBlock->sikd_bidang_id] = $infoListBidang;
        }
        $mapSikdInfo['kgtn'] = $kgtnList;
        $mapSikdInfo['prog'] = $progList;
        $mapSikdInfo['bidang'] = $bidangList;
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