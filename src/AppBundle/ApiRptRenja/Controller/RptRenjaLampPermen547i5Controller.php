<?php
namespace AppBundle\ApiRptRenja\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Lib\LivedRestClient;

class RptRenjaLampPermen547i5Controller extends Controller
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

       	//print_r($jnsRpt);exit;

        $param = [
            'jns_report' => $jnsRpt,
            'id_renja' => $idRenja,
            'tahun' => $tahun ,
        ];

        $tahun = $param['tahun'];
        
        //print_r(); exit;

        $this->restClient->setBaseUri($this->uriRestRenja);
        $renjaReports = $this->restClient->getCollection("$tahun/renjareports", $param);
        return $renjaReports;

        /*$this->restClient->setBaseUri($this->uriRestRenja);
        $renjaReports = $this->restClient->getCollection("$tahun/renjareports", $param);
        
        //return $renjaReports;

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
            if ($subSkpd && sizeof($subSkpd) > 0){
                $nmSubSkpd = $subSkpd->nama;
                $kdSubSkpd = $subSkpd->kode;
            }
        }

        //print_r("ok");exit;
        $renjaRepHandler = $renjaReports;

        //CONTAINS SIKD BIDANG ID
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

        //CONTAINS SIKD PROG ID
        $mapRenjaProgSikdProgId = [];
        $i = 0;
        foreach ($renjaRepHandler as $renjaProgIdHandler) {
            $mapRenjaProgSikdProgId[$i] =  $renjaProgIdHandler->renja_program_sikd_prog_id;
            $i++;
        }

        //CONTAINS SIKD'S INFO PROG
        $param2 ['sikd_prog_id'] = $mapRenjaProgSikdProgId;
        $ids = implode(',', $param2["sikd_prog_id"]);
        $paramIn = ['ids' => $ids];
        $sikdRenjaProg = $this->restClient->getCollection("$tahun/sikdprogs", $paramIn);
        $mapSikdProg = $this->populateSikdMstrProgInfo($sikdRenjaProg);

        $satkerType = 'SikdSkpd';

        $RenjaPermen546c6 = array();
        foreach ($renjaReports as &$value1) {
                    //$value1->id_renja_renja = $value1->id_renja_renja;
                    $value1->sikd_satker_kode = $kdSatker;
                    $value1->sikd_satker_nama = $nmSatker;
                    $value1->sikd_sub_skpd_nama = $nmSubSkpd;
                    $value1->sikd_sub_skpd_kode = $kdSubSkpd;
                    $value1->satker_type = $satkerType;
                    $value1->sikd_urusan_id_sikd_urusan = $mapSikdBidang['bidang'][$value1->renja_program_sikd_bidang_id]['sikd_urusan_id'];
                    $value1->sikd_urusan_kd_urusan = $mapSikdBidang['bidang'][$value1->renja_program_sikd_bidang_id]['sikd_urusan_kd_urusan'];
                    $value1->sikd_urusan_nm_urusan = $mapSikdBidang['bidang'][$value1->renja_program_sikd_bidang_id]['sikd_urusan_nm_urusan'];
                     $value1->sikd_bidang_id_sikd_bidang = $mapSikdBidang['bidang'][$value1->renja_program_sikd_bidang_id]['id_sikd_bidang'];
                    $value1->sikd_bidang_kd_bidang =$mapSikdBidang['bidang'][$value1->renja_program_sikd_bidang_id]['kd_bidang'];
                    $value1->sikd_bidang_nm_bidang = $mapSikdBidang['bidang'][$value1->renja_program_sikd_bidang_id]['nm_bidang'];
                    $value1->sikd_prog_id = $mapSikdProg['prog'][$value1->renja_program_sikd_prog_id]['id_sikd_prog'];
                    $value1->sikd_prog_kd_prog = $mapSikdProg['prog'][$value1->renja_program_sikd_prog_id]['kd_prog'];
                    $value1->sikd_prog_nm_prog = $mapSikdProg['prog'][$value1->renja_program_sikd_prog_id]['nm_prog'];
        }

        return $renjaReports;*/
    }

   /* private function populateSikdMstrBidangInfo($sikdInfoList){
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
    }

    private function populateSikdMstrProgInfo($sikdInfoList){
        $mapSikdInfo = []; $progList=[];
        foreach ($sikdInfoList as $sikdInfoBlock) {
            //PROGRAM
            $infoListProg['id_sikd_prog'] = $sikdInfoBlock->id_sikd_prog;
            $infoListProg['kd_prog'] = $sikdInfoBlock->kd_prog;
            $infoListProg['nm_prog'] = $sikdInfoBlock->nm_prog;
            $progList[$sikdInfoBlock->id_sikd_prog] = $infoListProg;
        }
        $mapSikdInfo['prog'] = $progList;
        return $mapSikdInfo;
    }*/
}