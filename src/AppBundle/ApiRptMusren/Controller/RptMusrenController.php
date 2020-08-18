<?php
namespace AppBundle\ApiRptMusren\Controller;

//use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use GuzzleHttp\Client;
use AppBundle\Lib\LivedRestClient;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class RptMusrenController extends Controller
{
    /**
     * @Route("/rpt_musren")
     */
    public function indexAction(Request $request)
    {
        $dataCfg = Yaml::parseFile(__DIR__.'/../Resources/config/data-report.yml');
        $rpt = $request->query->get('jns_report');
        
        try {
            $ctrl = $this->container->get($dataCfg[$rpt]['data-controller']);
            $result = $ctrl->getDataReport();
        } catch (ServiceNotFoundException $ex) {
            throw new BadRequestHttpException("Undefined report");
        }
        
        return new JsonResponse($result);                 
    }
}
