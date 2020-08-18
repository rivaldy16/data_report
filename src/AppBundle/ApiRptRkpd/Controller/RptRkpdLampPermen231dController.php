<?php
namespace AppBundle\ApiRptRkpd\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Lib\LivedRestClient;

class RptRkpdLampPermen231dController extends Controller
{
    private $uriRestRkpd;
    private $uriRestSikd;
    private $restClient;
    private $kdTenant;
    private $uriRestRenja;
    private $sikd_sub_skpd_nama = 'SKPD INDUK';
    private $uriRestPpas;
    
    static private $pathRkpdReport = "rkpdreports";
    
    public function __construct($request_stack, $rest_client, $uri_rest_rkpd, $uri_rest_renja, $uri_rest_ppas ,$uri_rest_setup)
    {
        $this->request = $request_stack->getCurrentRequest();
        $this->restClient = $rest_client;
        $this->kdTenant = $this->request->headers->get("tenant");
        $this->uriRestRkpd = $uri_rest_rkpd;
        $this->uriRestRenja = $uri_rest_renja;
        $this->uriRestPpas = $uri_rest_ppas;
        $this->uriRestSikd = $uri_rest_setup;
    }
    
    public function getDataReport()
    {
        $jnsRpt = $this->request->query->get('jns_report');
        $idRkpd = $this->request->query->get("id_rkpd");
        $tahun = $this->request->query->get("tahun");
        
        $param = [
            'jns_report' => $jnsRpt,
            'id_rkpd' => $idRkpd
        ];
         
        $this->restClient->setBaseUri($this->uriRestRkpd);
        $rkpdReports = $this->restClient->getCollection("$tahun/rkpdreports", $param);
        return $rkpdReports;

        /*$this->restClient->setBaseUri($this->uriRestRkpd);
        $rkpdReports = $this->restClient->getCollection("$tahun/rkpdreports", $param);
        //return $rkpdReports;

        $rkpdRepHandler = $rkpdReports;

        //CONTAINS RENJA ANGGARAN / KGTN ID
        $mapRkpdRenjaAnggId = [];
        $i = 0;
        foreach ($rkpdRepHandler as $rkpdRepId) {
            $mapRkpdRenjaAnggId[$i] =  $rkpdRepId->renja_kegiatan_id_renja_kegiatan;
            $i++;
        }
         //SET BASE URI TO RENJA DB
        $this->restClient->setBaseUri($this->uriRestRenja);
        //print_r($mapRkpdRenjaAnggId);exit;
        $ids4 = implode(',',  array_unique($mapRkpdRenjaAnggId));
        $paramIn4 = ['ids' => $ids4];
        $renjaRkpdInfo = $this->restClient->getCollection("$tahun/renjaanggarans", $paramIn4);
        //print_r($renjaRkpdInfo);exit;

        $mapRkpdRenjaRenjaId = [];
        $i = 0;
        foreach ($renjaRkpdInfo as $rkpdRepId2) {
            $mapRkpdRenjaRenjaId[$i] =  $rkpdRepId2->renja_renja_id;
            $i++;
        }
        $ids5 = implode(',',  array_unique($mapRkpdRenjaRenjaId));
        $renjaRkpdInfo2 = $this->restClient->getCollection("$tahun/renjarenjas/$ids5/renjablnjlangsungs");
        //print_r($renjaRkpdInfo2);exit;
        $mapRkpdRenjaRkpdPrioritasKab = [];
        $i = 0;
        foreach ($renjaRkpdInfo2 as $rkpdRepId4) {
            $mapRkpdRenjaRkpdPrioritasKab[$i] =  $rkpdRepId4->rkpd_prioritas_kab_id;
            $i++;
        }
        //print_r($mapRkpdRenjaRkpdPrioritasKab);exit;
        $this->restClient->setBaseUri($this->uriRestRkpd);
        $ids6 = implode(',',  array_unique($mapRkpdRenjaRkpdPrioritasKab));
        $paramIn6 = ['ids' => $ids6];
        $renjaRkpdInfo3 = $this->restClient->getCollection("$tahun/rkpdprioritaskabs", $paramIn6);

        $mapRkpdRenjaRkpdSasaran = [];
        $i = 0;
        foreach ($renjaRkpdInfo2 as $rkpdRepId6) {
            $mapRkpdRenjaRkpdSasaran[$i] =  $rkpdRepId4->rkpd_sasaran_id;
            $i++;
        }
        //print_r($mapRkpdRenjaRkpdSasaran);exit;
        $ids7 = implode(',',  array_unique($mapRkpdRenjaRkpdSasaran));
        $paramIn7 = ['ids' => $ids7];
        $renjaRkpdInfo4 = $this->restClient->getCollection("$tahun/rkpdsasarans", $paramIn7);

        foreach($rkpdReports as &$value1) {
            foreach($renjaRkpdInfo2 as $rkpdRepId3){
                $value1->tgt_anggaran_thn_ini =  doubleval($rkpdRepId3->tgt_anggaran_thn_ini);
                $value1->tgt_anggaran_thn_dpn =  doubleval($rkpdRepId3->tgt_anggaran_thn_dpn);
                $value1->tgt_anggaran_renstra =  $rkpdRepId3->tgt_anggaran_renstra;
                $value1->rls_anggaran_sd_thn_lalu =  $rkpdRepId3->rls_anggaran_sd_thn_lalu;
                $value1->jml_anggaran_rkpd =  $rkpdRepId3->jml_anggaran_rkpd;
                $value1->jns_kgtn =  $rkpdRepId3->jns_kgtn;
                $value1->lokasi_kgtn =  $rkpdRepId3->lokasi_kgtn;
            }
            
            if ($renjaRkpdInfo3 != null){
                foreach($renjaRkpdInfo3 as $rkpdRepId5){
                    $value1->rkpd_prioritas_kab_id_rkpd_prioritas_kab = $rkpdRepId5->id_rkpd_prioritas_kab;
                    $value1->rkpd_prioritas_no_prioritas = $rkpdRepId5->no_prioritas;
                    $value1->rkpd_prioritas_nm_program = $rkpdRepId5->nm_program;
                }
            }
            else{
                $value1->rkpd_prioritas_kab_id_rkpd_prioritas_kab = '';
                $value1->rkpd_prioritas_no_prioritas = '';
                $value1->rkpd_prioritas_nm_program = '';
            }

            if ($renjaRkpdInfo4 != null){
                foreach($renjaRkpdInfo4 as $rkpdRepId7){
                    $value1->rkpd_sasaran_id_rkpd_sasaran = $rkpdRepId7->id_rkpd_sasaran;
                    $value1->rkpd_sasaran_no_sasaran = $rkpdRepId7->no_sasaran;
                    $value1->rkpd_sasaran_uraian_sasaran = $rkpdRepId7->uraian_sasaran;
                }
            }
            else{
                $value1->rkpd_sasaran_id_rkpd_sasaran = '';
                $value1->rkpd_sasaran_no_sasaran = '';
                $value1->rkpd_sasaran_uraian_sasaran = '';
            }
        }

        return $rkpdReports;*/
    }
}