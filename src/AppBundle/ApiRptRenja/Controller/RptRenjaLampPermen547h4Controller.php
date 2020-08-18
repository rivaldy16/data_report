<?php
namespace AppBundle\ApiRptRenja\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Lib\LivedRestClient;

class RptRenjaLampPermen547h4Controller extends Controller
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
        
        $tahun = $param['tahun'];
        $idSatker = $param['sikd_satker_id'];
        $idSubUnit = $param['sikd_sub_skpd_id'];

        $this->restClient->setBaseUri($this->uriRestRenja);
        $renjaReports = $this->restClient->getCollection("$tahun/renjareports", $param);
        return $renjaReports;
        
        /*$this->restClient->setBaseUri($this->uriRestRenja);
        $renjaReports = $this->restClient->getCollection("$tahun/renjareports", $param);

        //print_r($renjaReports);exit;
        
        $nmSatker = '';
        $kdSatker = '';
        $idBidang = '';
        $this->restClient->setBaseUri($this->uriRestSikd);
        $satker = $this->restClient->getCollection("$tahun/sikdsatkers/$idSatker");

        if ( $satker){
            $nmSatker = $satker->nama;
            $kdSatker = $satker->kode;
        }

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
        
        $renjaRepHandler = $renjaReports;
        $i = 0;
        foreach ($renjaRepHandler as $renjaSikdProg) {
            $mapRenjaSikdProgId[$i] =  $renjaSikdProg->sikd_prog_id_sikd_prog;
            $i++;
        }
        
        $param2 ['sikd_prog_id'] = $mapRenjaSikdProgId;
        $ids = implode(',', $param2["sikd_prog_id"]);
        $paramIn = ['ids' => $ids];
        $sikdProgs = $this->restClient->getCollection("$tahun/sikdprogs", $paramIn);
        $mapSikdProgs = $this->populateSikdInfoProgs($sikdProgs);

        $i = 0;
        $mapRenjaSikdSmbrAnggaranId = [];
        foreach ($renjaRepHandler as $renjaSikdProg) {
            if ($renjaSikdProg->renja_mata_anggaran_sikd_sumber_anggaran_id != NULL){
                $mapRenjaSikdSmbrAnggaranId[$i] =  $renjaSikdProg->renja_mata_anggaran_sikd_sumber_anggaran_id;
            }
            $i++;
        }

        $param2 ['renja_mata_anggaran_sikd_sumber_anggaran_id'] = $mapRenjaSikdSmbrAnggaranId;
        $ids = implode(',', $param2["renja_mata_anggaran_sikd_sumber_anggaran_id"]);
        $paramIn = ['ids' => $ids];
        $sikdSmbrAnggarans = $this->restClient->getCollection("$tahun/sikdsumberanggarans", $paramIn);
        $mapSikdSmbrAnggarans = $this->populateSikdInfoSmbrAnggarans($sikdSmbrAnggarans);

        //SIKD BIDANG
        $mapRenjaSikdBidangId = [];
        $i = 0;
        foreach ($renjaRepHandler as $renjaBidangIdHandler) {
            $mapRenjaSikdBidangId[$i] =  $renjaBidangIdHandler->renja_program_sikd_bidang_id;
            $i++;
        }
        
        //CONTAINS SIKD'S INFO BIDANG
        $param2 ['sikd_bidang_id'] = $mapRenjaSikdBidangId;
        $ids = implode(',', $param2["sikd_bidang_id"]);
        $paramIn = ['ids' => $ids];
        $sikdRenjaBidang = $this->restClient->getCollection("$tahun/sikdbidangs", $paramIn);
        $mapSikdBidang = $this->populateSikdMstrBidangInfo($sikdRenjaBidang);

        $renjaRekapProyeksi = array();
        foreach ($renjaReports as &$value1) {
            $value1->sikd_satker_kode = $kdSatker;
            $value1->sikd_satker_nama = $nmSatker;
            $value1->sikd_sub_skpd_kode = $kdSubSkpd;
            $value1->sikd_sub_skpd_nama = $nmSubSkpd;
            $value1->sikd_urusan_id_sikd_urusan = $mapSikdBidang['bidang'][$value1->renja_program_sikd_bidang_id]['sikd_urusan_id'];
            $value1->sikd_urusan_kd_urusan = $mapSikdBidang['bidang'][$value1->renja_program_sikd_bidang_id]['sikd_urusan_kd_urusan'];
            $value1->sikd_urusan_nm_urusan = $mapSikdBidang['bidang'][$value1->renja_program_sikd_bidang_id]['sikd_urusan_nm_urusan'];
            $value1->sikd_bidang_id_sikd_bidang = $mapSikdBidang['bidang'][$value1->renja_program_sikd_bidang_id]['id_sikd_bidang'];
            $value1->sikd_bidang_kd_bidang =$mapSikdBidang['bidang'][$value1->renja_program_sikd_bidang_id]['kd_bidang'];
            $value1->sikd_bidang_nm_bidang = $mapSikdBidang['bidang'][$value1->renja_program_sikd_bidang_id]['nm_bidang'];
            $value1->sikd_prog_kd_prog = $mapSikdProgs['sikd_progs'][$value1->sikd_prog_id_sikd_prog]['kd_prog'];
            $value1->sikd_prog_nm_prog = $mapSikdProgs['sikd_progs'][$value1->sikd_prog_id_sikd_prog]['nm_prog'];
            if ($value1->renja_mata_anggaran_sikd_sumber_anggaran_id != ''){
                $value1->nm_sumber_anggaran = $mapSikdSmbrAnggarans['sikd_sumber_anggaran'][$value1->renja_mata_anggaran_sikd_sumber_anggaran_id]['nm_sumber_anggaran'];    
            }else{
                $value1->nm_sumber_anggaran = '';    
            }
            
        }

        return $renjaReports;*/
    }

    /*private function populateSikdInfoProgs($sikdInfoList){
        $mapSikdInfo = []; $sikdProgList=[];
        foreach ($sikdInfoList as $sikdInfoBlock) {
            $infoList['id_sikd_prog'] = $sikdInfoBlock->id_sikd_prog;
            $infoList['kd_bidang'] = $sikdInfoBlock->kd_bidang;
            $infoList['kd_prog'] = $sikdInfoBlock->kd_prog;
            $infoList['nm_prog'] = $sikdInfoBlock->nm_prog;
            $sikdProgList[$sikdInfoBlock->id_sikd_prog] = $infoList;
        }
        $mapSikdInfo['sikd_progs'] = $sikdProgList;
        return $mapSikdInfo;
    }

    private function populateSikdInfoSmbrAnggarans($sikdInfoList){
        $mapSikdInfo = []; $sikdSmbrAnggaranList=[];
        foreach ($sikdInfoList as $sikdInfoBlock) {
            $infoList['id_sikd_sumber_anggaran'] = $sikdInfoBlock->id_sikd_sumber_anggaran;
            $infoList['kd_sumber_anggaran'] = $sikdInfoBlock->kd_sumber_anggaran;
            $infoList['nm_sumber_anggaran'] = $sikdInfoBlock->nm_sumber_anggaran;
            $infoList['singkatan'] = $sikdInfoBlock->singkatan;
            $infoList['tipe_anggaran'] = $sikdInfoBlock->tipe_anggaran;
            $infoList['sikd_sumber_anggaran_id'] = $sikdInfoBlock->sikd_sumber_anggaran_id;
            $infoList['sikd_sumber_anggaran_kd_sumber_anggaran'] = $sikdInfoBlock->sikd_sumber_anggaran_kd_sumber_anggaran;
            $infoList['sikd_sumber_anggaran_nm_sumber_anggaran'] = $sikdInfoBlock->sikd_sumber_anggaran_nm_sumber_anggaran;
            $infoList['sikd_sumber_anggaran_singkatan'] = $sikdInfoBlock->sikd_sumber_anggaran_singkatan;
            $infoList['sikd_sumber_anggaran_tipe_anggaran'] = $sikdInfoBlock->sikd_sumber_anggaran_tipe_anggaran;
            $sikdSmbrAnggaranList[$sikdInfoBlock->id_sikd_sumber_anggaran] = $infoList;
        }
        $mapSikdInfo['sikd_sumber_anggaran'] = $sikdSmbrAnggaranList;
        return $mapSikdInfo;
    }

     private function populateSikdMstrBidangInfo($sikdInfoList){
        $mapSikdInfo = []; $bidangList=[]; $urusanList=[];
        foreach ($sikdInfoList as $sikdInfoBlock) {
            //BIDANG
            $infoListBidang['id_sikd_bidang'] = $sikdInfoBlock->id_sikd_bidang;
            $infoListBidang['kd_bidang'] = $sikdInfoBlock->kd_bidang;
            $infoListBidang['nm_bidang'] = $sikdInfoBlock->nm_bidang;
            $infoListBidang['sikd_urusan_id'] = $sikdInfoBlock->sikd_urusan_id;
            $infoListBidang['sikd_urusan_kd_urusan'] = $sikdInfoBlock->sikd_urusan_kd_urusan;
            $infoListBidang['sikd_urusan_nm_urusan'] = $sikdInfoBlock->sikd_urusan_nm_urusan;
            $bidangList[$sikdInfoBlock->id_sikd_bidang] = $infoListBidang;
        }
        $mapSikdInfo['bidang'] = $bidangList;
        return $mapSikdInfo;
    }*/
    
}