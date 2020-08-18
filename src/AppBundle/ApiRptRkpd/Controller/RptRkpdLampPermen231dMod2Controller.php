<?php
namespace AppBundle\ApiRptRkpd\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Lib\LivedRestClient;

class RptRkpdLampPermen231dMod2Controller extends Controller
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
        $format = $this->request->query->get("format");
        $idRkpd = $this->request->query->get("id_rkpd");
        $tahun = $this->request->query->get("tahun");
        $jnsRkpd = $this->request->query->get("jns_rkpd");
        $param = [
            'jns_report' => $jnsRpt,
            'id_rkpd' => $idRkpd,
            'tahun' => $tahun,
            'format' => $format,
            'jns_rkpd' => $jnsRkpd
        ];
        
        $tahun = $param['tahun'];
        
        $this->restClient->setBaseUri($this->uriRestRkpd);
        $rkpdReports = $this->restClient->getCollection("$tahun/rkpdreports", $param);
        return $rkpdReports;
        
        /*$this->restClient->setBaseUri($this->uriRestRkpd);
        $rkpdReports = $this->restClient->getCollection("$tahun/rkpdreports", $param);

        $rkpdRepHandler = $rkpdReports;

        $mapRkpdRenjaAnggId = [];
        $i = 0;
        foreach ($rkpdRepHandler as $rkpdRepId) {
            $mapRkpdRenjaAnggId[$i] =  $rkpdRepId->renja_kegiatan_id_renja_kegiatan;
            $i++;
        }
        $this->restClient->setBaseUri($this->uriRestRenja);
        
        $ids4 = implode(',',  array_unique($mapRkpdRenjaAnggId));
        $paramIn4 = ['ids' => $ids4];
        $renjaRkpdInfo = $this->restClient->getCollection("$tahun/renjaanggarans", $paramIn4);

        $mapRkpdRenjaRenjaId = [];
        $i = 0;
        foreach ($renjaRkpdInfo as $rkpdRepId2) {
            $mapRkpdRenjaRenjaId[$i] =  $rkpdRepId2->renja_renja_id;
            $i++;
        }
        $ids5 = implode(',',  array_unique($mapRkpdRenjaRenjaId));
        $renjaRkpdInfo2 = $this->restClient->getCollection("$tahun/renjarenjas/$ids5/renjablnjlangsungs");

        foreach ($rkpdReports as &$value1) {
            foreach ($renjaRkpdInfo2 as $rkpdRepId3) {
                $value1->tgt_anggaran_thn_ini =  doubleval($rkpdRepId3->tgt_anggaran_thn_ini);
                $value1->tgt_anggaran_thn_dpn =  doubleval($rkpdRepId3->tgt_anggaran_thn_dpn);
                $value1->tgt_anggaran_renstra =  $rkpdRepId3->tgt_anggaran_renstra;
                $value1->rls_anggaran_sd_thn_lalu =  $rkpdRepId3->rls_anggaran_sd_thn_lalu;
                $value1->jml_anggaran_rkpd =  $rkpdRepId3->jml_anggaran_rkpd;
                $value1->jns_kgtn =  $rkpdRepId3->jns_kgtn;
                $value1->lokasi_kgtn =  $rkpdRepId3->lokasi_kgtn;
            }
        }

        return $rkpdReports;*/
    }
}