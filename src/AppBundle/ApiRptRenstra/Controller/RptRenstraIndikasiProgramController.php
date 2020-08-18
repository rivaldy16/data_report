<?php
namespace AppBundle\ApiRptRenstra\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Lib\LivedRestClient;

class RptRenstraIndikasiProgramController extends Controller
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
        $idSatker = $this->request->query->get("id_satker");
        $param = [
            'jns_report' => $jnsRpt,
            'id_renstra' => $idRenstra,
            'id_satker' => $idSatker
        ];
        
        /*$this->restClient->setBaseUri($this->uriRestRenstra);
        $renstraReports = $this->restClient->getCollection("renstrareports", $param);
        //print_r($renstraReports);exit;


        $renjaRepHandler = $renstraReports;
        $idSubUnit = '';
        $nmSatker = '';
        $kdSatker = '';
        $this->restClient->setBaseUri($this->uriRestSikd);
        //SATKER
        $mapRenstraSikdSatkerId = [];
        $i = 0;
        foreach ($renjaRepHandler as $renstraSatkerId) {
            $mapRenstraSikdSatkerId[$i] =  $renstraSatkerId->renstra_renstra_sikd_satker_id;
            $i++;
        }
       //print_r($mapRenstraSikdSatkerId);exit;
        $ids2 = implode(',',  array_unique($mapRenstraSikdSatkerId));
        $paramIn2 = ['ids' => $ids2]; $paramIn2 ['parents']=1;
        $sikdRenstraSatker = $this->restClient->getCollection("$tahun/sikdsatkers", $paramIn2);
        //print_r($sikdRenstraSatker);exit;
        $mapSikdSatker = $this->populateSikdSatker($sikdRenstraSatker);
        //print_r($mapSikdSatker);exit;

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

        $mapRenstraSikdProgId = [];
        $i = 0;
        foreach ($renjaRepHandler as $rentraSikdProgId) {
            $mapRenstraSikdProgId[$i] =  $rentraSikdProgId->renstra_program_sikd_prog_id;
            $i++;
        }
        //print_r($mapRenstraSikdProgId);exit;
        $param2 ['sikd_prog_id'] = $mapRenstraSikdProgId;
        $ids = implode(',', $param2["sikd_prog_id"]);
        $paramIn = ['ids' => $ids];$paramIn ['parents']=4;
        $this->restClient->setBaseUri($this->uriRestSikd);
        $sikdProgs = $this->restClient->getCollection("$tahun/sikdprogs", $paramIn);
        //print_r($sikdProgs);exit;
        $mapSikdProgrs = $this->populateSikdInfo($sikdProgs);
        //print_r($mapSikdProgrs);exit;

        foreach ($renstraReports as &$value1) {
                    $value1->id_sikd_sub_skpd = $idSubUnit;
                    $value1->id_sub_skpd = $idSubUnit;
                    $value1->kd_satker = $mapSikdSatker['satker'][$value1->renstra_renstra_sikd_satker_id]['kode'];
                    $value1->nm_satker = $mapSikdSatker['satker'][$value1->renstra_renstra_sikd_satker_id]['nama'];
                    $value1->kd_sub_skpd = $kdSubSkpd;
                    $value1->nm_sub_skpd = $nmSubSkpd;
                    $value1->sikd_urusan_id_sikd_urusan = $mapSikdProgrs['sikd_progs'][$value1->renstra_program_sikd_prog_id]['sikd_urusan_id_sikd_urusan'];
                    $value1->sikd_urusan_kd_urusan = $mapSikdProgrs['sikd_progs'][$value1->renstra_program_sikd_prog_id]['sikd_urusan_kd_urusan'];
                    $value1->sikd_urusan_nm_urusan = $mapSikdProgrs['sikd_progs'][$value1->renstra_program_sikd_prog_id]['sikd_urusan_nm_urusan'];
                    $value1->sikd_bidang_id_sikd_bidang = $mapSikdProgrs['sikd_progs'][$value1->renstra_program_sikd_prog_id]['sikd_bidang_id_sikd_bidang'];
                    $value1->sikd_bidang_kd_bidang = $mapSikdProgrs['sikd_progs'][$value1->renstra_program_sikd_prog_id]['sikd_bidang_kd_bidang'];
                    $value1->sikd_bidang_nm_bidang = $mapSikdProgrs['sikd_progs'][$value1->renstra_program_sikd_prog_id]['sikd_bidang_nm_bidang'];
                    $value1->sikd_prog_kd_prog = $mapSikdProgrs['sikd_progs'][$value1->renstra_program_sikd_prog_id]['sikd_prog_kd_prog'];
                    $value1->sikd_urusan_kd_urusan = $mapSikdProgrs['sikd_progs'][$value1->renstra_program_sikd_prog_id]['sikd_urusan_kd_urusan'];
                    $value1->sikd_bidang_kd_bidang = $mapSikdProgrs['sikd_progs'][$value1->renstra_program_sikd_prog_id]['sikd_bidang_kd_bidang'];
                    $value1->sikd_prog_kd_prog = $mapSikdProgrs['sikd_progs'][$value1->renstra_program_sikd_prog_id]['sikd_prog_kd_prog'];
                    
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
            $infoListRincObj['sikd_urusan_id_sikd_urusan'] = $sikdInfoBlock->sikd_urusan_id;
            $infoListRincObj['sikd_urusan_kd_urusan'] = $sikdInfoBlock->sikd_urusan_kd_urusan;
            $infoListRincObj['sikd_urusan_nm_urusan'] = $sikdInfoBlock->sikd_urusan_nm_urusan;
            //REKENING OBJEK
            $infoListRincObj['sikd_bidang_id_sikd_bidang'] = $sikdInfoBlock->sikd_bidang_id;
            $infoListRincObj['sikd_bidang_kd_bidang'] = $sikdInfoBlock->sikd_bidang_kd_bidang;
            $infoListRincObj['sikd_bidang_nm_bidang'] = $sikdInfoBlock->sikd_bidang_nm_bidang;
            //REKENING AKUN
            $infoListRincObj['sikd_prog_kd_prog'] = $sikdInfoBlock->kd_prog;
            $SidkProgList[strtoupper($sikdInfoBlock->id_sikd_prog)] = $infoListRincObj;
        }
        $mapSikdInfo['sikd_progs'] = $SidkProgList;
        return $mapSikdInfo;
    }

    private function populateSikdSatker($sikdInfoList){
        $mapSikdInfo = []; $satkerList=[];
        foreach ($sikdInfoList as $sikdInfoBlock) {
            //BIDANG
            $infoListSatker['id_sikd_satker'] = $sikdInfoBlock->id_sikd_satker;
            $infoListSatker['nama'] = $sikdInfoBlock->nama;
            $infoListSatker['kode'] = $sikdInfoBlock->kode;
            $satkerList[$sikdInfoBlock->id_sikd_satker] = $infoListSatker;
        }
        $mapSikdInfo['satker'] = $satkerList;
        return $mapSikdInfo;
    }
}